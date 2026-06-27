<?php
// app/Services/RappelService.php

namespace App\Services;

use App\Models\Tache;
use App\Models\TacheRappel;
use App\Notifications\TacheRappelNotification;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Service RappelService
 * 
 * Gère la génération et l'envoi des rappels automatiques
 * Conforme au cahier des charges: rappels à 48h, 24h, 1h, 30min
 */
class RappelService
{
    /**
     * Génère tous les rappels pour une tâche
     * 
     * @param Tache $tache
     * @param array $typesRappels Types de rappels à générer (48h,24h,1h,30min,now)
     * @return int Nombre de rappels générés
     */
    public function genererRappels(Tache $tache, array $typesRappels = ['48h', '24h', '1h', '30min']): int
    {
        // Supprimer les anciens rappels
        TacheRappel::where('tache_id', $tache->id)->delete();
        
        $datePlanifiee = Carbon::parse($tache->date_planifiee);
        $rappelsGeneres = 0;
        
        foreach ($typesRappels as $type) {
            $heureEnvoi = $this->calculerHeureEnvoi($datePlanifiee, $type);
            
            // Ne générer que si l'heure d'envoi est dans le futur
            if ($heureEnvoi && $heureEnvoi->isFuture()) {
                TacheRappel::create([
                    'tache_id' => $tache->id,
                    'type_rappel' => $type,
                    'heure_envoi_prevue' => $heureEnvoi,
                    'statut' => 'pending',
                ]);
                $rappelsGeneres++;
            }
        }
        
        Log::info("Rappels générés pour la tâche {$tache->id}", [
            'titre' => $tache->titre,
            'nb_rappels' => $rappelsGeneres
        ]);
        
        return $rappelsGeneres;
    }

    /**
     * Calcule l'heure d'envoi en fonction du type de rappel
     * 
     * @param Carbon $datePlanifiee
     * @param string $type
     * @return Carbon|null
     */
    private function calculerHeureEnvoi(Carbon $datePlanifiee, string $type): ?Carbon
    {
        $delais = Tache::RAPPELS_DELAIS;
        
        if (!isset($delais[$type])) {
            return null;
        }
        
        $heures = $delais[$type];
        
        // Pour 'now', envoyer immédiatement
        if ($heures === 0) {
            $heureEnvoi = $datePlanifiee->copy()->startOfDay();
        } else {
            $heureEnvoi = $datePlanifiee->copy()->subHours($heures);
        }
        
        // Pour les rappels à 48h/24h, envoyer à 8h du matin
        if ($type === '48h' || $type === '24h') {
            $heureEnvoi->hour(8)->minute(0)->second(0);
        }
        
        return $heureEnvoi;
    }

    /**
     * Envoie tous les rappels en attente
     * 
     * @return array Statistiques d'envoi
     */
    public function envoyerRappelsEnAttente(): array
    {
        $rappels = TacheRappel::where('statut', 'pending')
            ->where('heure_envoi_prevue', '<=', Carbon::now())
            ->with('tache.elevage.proprietaire')
            ->get();
        
        $stats = [
            'total' => $rappels->count(),
            'envoyes' => 0,
            'echoues' => 0,
        ];
        
        foreach ($rappels as $rappel) {
            try {
                $this->envoyerRappel($rappel);
                $stats['envoyes']++;
            } catch (\Exception $e) {
                $rappel->marquerEchoue($e->getMessage());
                $stats['echoues']++;
                Log::error('Erreur envoi rappel', [
                    'rappel_id' => $rappel->id,
                    'erreur' => $e->getMessage()
                ]);
            }
        }
        
        return $stats;
    }

    /**
     * Envoie un rappel spécifique
     * 
     * @param TacheRappel $rappel
     * @return bool
     */
    public function envoyerRappel(TacheRappel $rappel): bool
    {
        $tache = $rappel->tache;
        
        // Ne pas envoyer si la tâche est déjà terminée
        if ($tache->terminee) {
            $rappel->marquerEchoue('Tâche déjà terminée');
            return false;
        }
        
        $user = $tache->elevage->proprietaire;
        
        if (!$user) {
            $rappel->marquerEchoue('Utilisateur non trouvé');
            return false;
        }
        
        // Envoi de la notification
        $user->notify(new TacheRappelNotification($tache, $rappel->type_rappel));
        
        $rappel->marquerEnvoye();
        
        Log::info('Rappel envoyé', [
            'tache_id' => $tache->id,
            'type_rappel' => $rappel->type_rappel,
            'user_id' => $user->id
        ]);
        
        return true;
    }

    /**
     * Met à jour les rappels lorsque la date d'une tâche change
     * 
     * @param Tache $tache
     * @return void
     */
    public function mettreAJourRappels(Tache $tache): void
    {
        // Récupérer les types de rappels existants
        $typesExistants = TacheRappel::where('tache_id', $tache->id)
            ->pluck('type_rappel')
            ->toArray();
        
        if (!empty($typesExistants)) {
            $this->genererRappels($tache, $typesExistants);
        }
    }

    /**
     * Régénère les rappels pour toutes les tâches futures
     * 
     * @return array Statistiques
     */
    public function regenererTousLesRappels(): array
    {
        $taches = Tache::where('terminee', false)
            ->where('date_planifiee', '>=', Carbon::today())
            ->get();
        
        $stats = [
            'total_taches' => $taches->count(),
            'rappels_generes' => 0,
        ];
        
        foreach ($taches as $tache) {
            $nb = $this->genererRappels($tache);
            $stats['rappels_generes'] += $nb;
        }
        
        return $stats;
    }
}
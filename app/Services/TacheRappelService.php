<?php
// app/Services/TacheRappelService.php

namespace App\Services;

use App\Models\Tache;
use App\Models\TacheRappel;
use App\Notifications\TacheNotification;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TacheRappelService
{
    /**
     * Vérifie et envoie tous les rappels nécessaires
     */
    public function verifierEtEnvoyerRappels(): void
    {
        Log::info('🔄 Vérification des rappels de tâches...');
        
        // Récupérer les tâches non terminées avec des dates planifiées
        $taches = Tache::where('terminee', false)
            ->whereNotNull('date_planifiee')
            ->where('date_planifiee', '>=', now()->subDays(30)) // Limiter aux 30 derniers jours
            ->with(['animal', 'elevage', 'user'])
            ->get();
        
        Log::info('📋 Tâches à vérifier:', ['count' => $taches->count()]);
        
        foreach ($taches as $tache) {
            $this->verifierRappelsPourTache($tache);
        }
        
        Log::info('✅ Vérification des rappels terminée');
    }

    /**
     * Vérifie les rappels pour une tâche spécifique
     */
    public function verifierRappelsPourTache(Tache $tache): void
    {
        $now = now();
        $datePlanifiee = $tache->date_planifiee;
        
        // Si la tâche est terminée, on arrête
        if ($tache->terminee) {
            return;
        }
        
        // Si la date est passée, gérer les rappels de retard
        if ($datePlanifiee < $now) {
            $this->gererRappelRetard($tache);
            return;
        }
        
        // Calculer les différences
        $diffHeures = $now->diffInHours($datePlanifiee);
        $diffJours = $now->diffInDays($datePlanifiee);
        
        // Liste des rappels à envoyer avec leurs seuils
        $rappels = [
            '72h' => ['seuil' => 72, 'margin' => 2],
            '48h' => ['seuil' => 48, 'margin' => 1],
            '24h' => ['seuil' => 24, 'margin' => 1],
            '1h' => ['seuil' => 1, 'margin' => 0.25],
            '30min' => ['seuil' => 0.5, 'margin' => 0.1],
            'now' => ['seuil' => 0, 'margin' => 0.05],
        ];
        
        foreach ($rappels as $type => $config) {
            $seuil = $config['seuil'];
            $margin = $config['margin'];
            
            // Vérifier si on est dans la fenêtre de rappel
            if ($diffHeures >= $seuil - $margin && $diffHeures <= $seuil + $margin) {
                // Vérifier si le rappel n'a pas déjà été envoyé
                $dejaEnvoye = TacheRappel::where('tache_id', $tache->id)
                    ->where('type_rappel', $type)
                    ->where('statut', 'sent')
                    ->exists();
                
                if (!$dejaEnvoye) {
                    $this->envoyerRappel($tache, $type);
                }
            }
        }
    }

    /**
     * Gère les rappels de retard
     */
    protected function gererRappelRetard(Tache $tache): void
    {
        $retardCount = $tache->retard_reminder_count ?? 0;
        $lastSent = $tache->last_reminder_sent_at;
        
        // Premier rappel de retard : immédiatement après la date
        if ($retardCount === 0) {
            $this->envoyerRappel($tache, 'retard');
            return;
        }
        
        // Rappels suivants : tous les jours à 9h
        if ($lastSent) {
            $prochainRappel = $lastSent->copy()->addDay()->startOfDay()->setTime(9, 0);
            if (now()->gte($prochainRappel) && $retardCount < 7) {
                $this->envoyerRappel($tache, 'retard');
            }
        }
    }

    /**
     * Envoie un rappel pour une tâche
     */
    protected function envoyerRappel(Tache $tache, string $type): void
    {
        try {
            $user = $tache->user;
            
            if (!$user) {
                Log::warning('⚠️ Utilisateur non trouvé pour la tâche', ['tache_id' => $tache->id]);
                return;
            }
            
            $message = $this->getMessageRappel($tache, $type);
            
            // Envoyer la notification
            $user->notify(new TacheNotification($tache, 'reminder', [
                'reminder_type' => $type,
                'message' => $message,
            ]));
            
            // Enregistrer le rappel
            TacheRappel::create([
                'tache_id' => $tache->id,
                'type_rappel' => $type,
                'heure_envoi_prevue' => $tache->date_planifiee,
                'statut' => 'sent',
                'date_envoi' => now(),
            ]);
            
            // Mettre à jour la tâche
            $tache->update([
                'last_reminder_type' => $type,
                'last_reminder_sent_at' => now(),
                'retard_reminder_count' => $type === 'retard' ? ($tache->retard_reminder_count ?? 0) + 1 : ($tache->retard_reminder_count ?? 0),
            ]);
            
            Log::info('✅ Rappel envoyé', [
                'tache_id' => $tache->id,
                'type' => $type,
                'user_id' => $user->id,
                'message' => $message
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Erreur envoi rappel', [
                'tache_id' => $tache->id,
                'type' => $type,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Génère le message de rappel
     */
    protected function getMessageRappel(Tache $tache, string $type): string
    {
        $titre = $tache->titre;
        $typeLabel = $tache->type_label;
        $animalNom = $tache->animal?->nom ?? 'l\'élevage';
        $dateFormatee = $tache->date_planifiee->format('d/m/Y à H:i');
        
        $messages = [
            '72h' => "🚨 RAPPEL IMPORTANT (72h) : La tâche '{$titre}' ({$typeLabel}) pour {$animalNom} est prévue dans 3 jours, le {$dateFormatee}. Préparez-vous !",
            '48h' => "🔔 RAPPEL (48h) : La tâche '{$titre}' ({$typeLabel}) pour {$animalNom} est prévue après-demain, le {$dateFormatee}.",
            '24h' => "🔔 RAPPEL (24h) : La tâche '{$titre}' ({$typeLabel}) pour {$animalNom} est prévue demain, le {$dateFormatee}.",
            '1h' => "🔴 RAPPEL URGENT (1h) : La tâche '{$titre}' ({$typeLabel}) pour {$animalNom} est prévue dans 1 heure, à {$dateFormatee}.",
            '30min' => "🔴 RAPPEL URGENT (30min) : La tâche '{$titre}' ({$typeLabel}) pour {$animalNom} est prévue dans 30 minutes, à {$dateFormatee}.",
            'now' => "⏰ TÂCHE IMMINENTE : La tâche '{$titre}' ({$typeLabel}) pour {$animalNom} est prévue maintenant, à {$dateFormatee}.",
            'retard' => "⚠️ TÂCHE EN RETARD : La tâche '{$titre}' ({$typeLabel}) pour {$animalNom} était prévue le {$dateFormatee} et n'a pas encore été effectuée. Veuillez la marquer comme terminée ou reprogrammer.",
        ];
        
        return $messages[$type] ?? "🔔 Rappel pour la tâche '{$titre}'";
    }

    /**
     * Programme un rappel spécifique
     */
    public function programmerRappel(Tache $tache, string $type, Carbon $dateEnvoi): void
    {
        TacheRappel::create([
            'tache_id' => $tache->id,
            'type_rappel' => $type,
            'heure_envoi_prevue' => $dateEnvoi,
            'statut' => 'pending',
        ]);
        
        Log::info('📅 Rappel programmé', [
            'tache_id' => $tache->id,
            'type' => $type,
            'date_envoi' => $dateEnvoi
        ]);
    }
}
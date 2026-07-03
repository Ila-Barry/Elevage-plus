<?php
// app/Services/TacheService.php

namespace App\Services;

use App\Models\Tache;
use App\Models\TacheRappel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

/**
 * Service TacheService
 * 
 * Contient toute la logique métier pour la gestion des tâches
 */
class TacheService
{
    protected RappelService $rappelService;

    public function __construct(RappelService $rappelService)
    {
        $this->rappelService = $rappelService;
    }

    /**
     * Récupère toutes les tâches avec pagination et filtres
     * 
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = Tache::with(['animal', 'elevage.proprietaire']);
        
        // Filtre par élevage
        if (!empty($filters['elevage_id'])) {
            $query->forElevage($filters['elevage_id']);
        }
        
        // Filtre par animal
        if (!empty($filters['animal_id'])) {
            $query->forAnimal($filters['animal_id']);
        }
        
        // Filtre par type
        if (!empty($filters['type'])) {
            $query->ofType($filters['type']);
        }
        
        // Filtre par statut
        if (!empty($filters['statut'])) {
            if ($filters['statut'] === 'terminee') {
                $query->completed();
            } elseif ($filters['statut'] === 'en_retard') {
                $query->late();
            } elseif ($filters['statut'] === 'a_venir') {
                $query->notCompleted()->where('date_planifiee', '>', Carbon::today());
            } elseif ($filters['statut'] === 'aujourdhui') {
                $query->today();
            }
        }
        
        // Filtre par période
        if (!empty($filters['date_debut']) && !empty($filters['date_fin'])) {
            $query->betweenDates($filters['date_debut'], $filters['date_fin']);
        } elseif (!empty($filters['date_debut'])) {
            $query->where('date_planifiee', '>=', $filters['date_debut']);
        } elseif (!empty($filters['date_fin'])) {
            $query->where('date_planifiee', '<=', $filters['date_fin']);
        }
        
        // Filtre par recherche
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('titre', 'LIKE', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'LIKE', '%' . $filters['search'] . '%');
            });
        }
        
        return $query->orderBy('date_planifiee')->paginate($perPage);
    }

    /**
     * Récupère les tâches pour FullCalendar
     * 
     * @param int $elevageId
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getForCalendar(int $elevageId, string $startDate, string $endDate): array
    {
        $taches = Tache::forElevage($elevageId)
            ->whereBetween('date_planifiee', [$startDate, $endDate])
            ->with(['animal'])
            ->get();
        
        return $taches->map(fn($tache) => $tache->formatForFullCalendar())->toArray();
    }

    /**
     * Crée une nouvelle tâche
     * 
     * @param array $data
     * @param bool $genererRappels
     * @param array $typesRappels
     * @return Tache
     */
    public function createTache(array $data, bool $genererRappels = true, array $typesRappels = ['48h', '24h', '1h', '30min']): Tache
    {
        DB::beginTransaction();
        
        try {
            $tache = Tache::create($data);
            
            // Générer les rappels si demandé
            if ($genererRappels) {
                $this->rappelService->genererRappels($tache, $typesRappels);
            }
            
            DB::commit();
            
            Log::info('Nouvelle tâche créée', [
                'tache_id' => $tache->id,
                'titre' => $tache->titre,
                'elevage_id' => $tache->elevage_id
            ]);
            
            return $tache->load(['animal', 'elevage']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création de la tâche: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Met à jour une tâche
     * 
     * @param Tache $tache
     * @param array $data
     * @return Tache
     */
    public function updateTache(Tache $tache, array $data): Tache
    {
        DB::beginTransaction();
        
        try {
            $oldDate = $tache->date_planifiee;
            $tache->update($data);
            
            // Si la date a changé, mettre à jour les rappels
            if ($oldDate != $tache->date_planifiee) {
                $this->rappelService->mettreAJourRappels($tache);
            }
            
            DB::commit();
            
            Log::info('Tâche mise à jour', [
                'tache_id' => $tache->id,
                'titre' => $tache->titre
            ]);
            
            return $tache->fresh(['animal', 'elevage']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour de la tâche: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Marque une tâche comme terminée
     * 
     * @param Tache $tache
     * @param string|null $dateRealisee
     * @return Tache
     */
    public function completeTache(Tache $tache, ?string $dateRealisee = null): Tache
    {
        DB::beginTransaction();
        
        try {
            $tache->marquerCommeTerminee($dateRealisee);
            
            // Désactiver les rappels en attente
            TacheRappel::where('tache_id', $tache->id)
                ->where('statut', 'pending')
                ->update(['statut' => 'sent', 'date_envoi' => Carbon::now()]);
            
            DB::commit();
            
            Log::info('Tâche marquée comme terminée', [
                'tache_id' => $tache->id,
                'date_realisee' => $tache->date_realisee
            ]);
            
            return $tache;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors du marquage de la tâche: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Supprime une tâche
     * 
     * @param Tache $tache
     * @return bool
     */
    public function deleteTache(Tache $tache): bool
    {
        DB::beginTransaction();
        
        try {
            // Les rappels sont supprimés automatiquement par cascade
            $result = $tache->delete();
            
            DB::commit();
            
            Log::info('Tâche supprimée', [
                'tache_id' => $tache->id,
                'titre' => $tache->titre
            ]);
            
            return $result;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression de la tâche: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Récupère les détails d'une tâche
     * 
     * @param Tache $tache
     * @return Tache
     */
    public function getTacheDetails(Tache $tache): Tache
    {
        return $tache->load([
            'animal',
            'elevage.proprietaire',
            'rappels'
        ]);
    }

    /**
     * Calcule le nombre total de tâches pour un élevage
     * 
     * @param int $elevageId
     * @return array
     */
    public function getTotalTachesCount(int $elevageId): array
    {
        return [
            'total' => Tache::forElevage($elevageId)->count(),
            'terminees' => Tache::forElevage($elevageId)->completed()->count(),
            'en_attente' => Tache::forElevage($elevageId)->notCompleted()->count(),
            'en_retard' => Tache::forElevage($elevageId)->late()->count(),
            'aujourdhui' => Tache::forElevage($elevageId)->today()->notCompleted()->count(),
        ];
    }

    /**
     * Récupère les statistiques par type
     * 
     * @param int $elevageId
     * @return \Illuminate\Support\Collection
     */
    public function getStatsByType(int $elevageId)
    {
        return Tache::forElevage($elevageId)
            ->selectRaw('type, count(*) as total, sum(case when terminee then 1 else 0 end) as terminees')
            ->groupBy('type')
            ->get();
    }

    /**
     * Vérifie si l'utilisateur est propriétaire de la tâche
     * 
     * @param Tache $tache
     * @param int|null $userId
     * @return bool
     */
    public function isOwner(Tache $tache, ?int $userId = null): bool
    {
        $userId = $userId ?? auth()->id();
        return $tache->elevage && $tache->elevage->user_id === $userId;
    }
    // À ajouter dans app/Services/TacheService.php

    public function getTotalTachesCountForUser(int $userId): array
    {
        return [
            'total' => Tache::whereHas('elevage', fn($q) => $q->where('user_id', $userId))->count(),
            'terminees' => Tache::whereHas('elevage', fn($q) => $q->where('user_id', $userId))->completed()->count(),
            'en_attente' => Tache::whereHas('elevage', fn($q) => $q->where('user_id', $userId))->notCompleted()->count(),
            'en_retard' => Tache::whereHas('elevage', fn($q) => $q->where('user_id', $userId))->late()->count(),
            'aujourdhui' => Tache::whereHas('elevage', fn($q) => $q->where('user_id', $userId))->today()->notCompleted()->count(),
        ];
    }
}

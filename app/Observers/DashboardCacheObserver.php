<?php
// app/Observers/DashboardCacheObserver.php

namespace App\Observers;

use App\Models\User;
use App\Models\Animal;
use App\Models\Publication;
use App\Models\Tache;
use App\Models\Produit;
use Illuminate\Support\Facades\Cache;

/**
 * Observateur DashboardCacheObserver
 * 
 * Invalide le cache du dashboard lorsqu'il y a des modifications
 * 
 * @package App\Observers
 */
class DashboardCacheObserver
{
    /**
     * Invalider le cache d'un utilisateur spécifique
     *
     * @param int $userId
     * @return void
     */
    private function invalidateUserCache(int $userId): void
    {
        $patterns = [
            "dashboard:user:{$userId}:main",
            "dashboard:kpis:user:{$userId}",
            "dashboard:evolution:*:user:{$userId}:*",
        ];
        
        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }
    }

    /**
     * Écouter l'événement de création d'animal
     *
     * @param Animal $animal
     * @return void
     */
    public function createdAnimal(Animal $animal): void
    {
        if ($animal->elevage && $animal->elevage->user) {
            $this->invalidateUserCache($animal->elevage->user->id);
        }
    }

    /**
     * Écouter l'événement de mise à jour d'animal
     *
     * @param Animal $animal
     * @return void
     */
    public function updatedAnimal(Animal $animal): void
    {
        if ($animal->elevage && $animal->elevage->user) {
            $this->invalidateUserCache($animal->elevage->user->id);
        }
    }

    /**
     * Écouter l'événement de suppression d'animal
     *
     * @param Animal $animal
     * @return void
     */
    public function deletedAnimal(Animal $animal): void
    {
        if ($animal->elevage && $animal->elevage->user) {
            $this->invalidateUserCache($animal->elevage->user->id);
        }
    }

    /**
     * Écouter l'événement de création de publication
     *
     * @param Publication $publication
     * @return void
     */
    public function createdPublication(Publication $publication): void
    {
        $this->invalidateUserCache($publication->user_id);
    }

    /**
     * Écouter l'événement de création de tâche
     *
     * @param Tache $tache
     * @return void
     */
    public function createdTache(Tache $tache): void
    {
        if ($tache->animal && $tache->animal->elevage && $tache->animal->elevage->user) {
            $this->invalidateUserCache($tache->animal->elevage->user->id);
        }
    }

    /**
     * Écouter l'événement de mise à jour de tâche
     *
     * @param Tache $tache
     * @return void
     */
    public function updatedTache(Tache $tache): void
    {
        if ($tache->animal && $tache->animal->elevage && $tache->animal->elevage->user) {
            $this->invalidateUserCache($tache->animal->elevage->user->id);
        }
    }

    /**
     * Écouter l'événement de création de produit
     *
     * @param Produit $produit
     * @return void
     */
    public function createdProduit(Produit $produit): void
    {
        if ($produit->elevage && $produit->elevage->user) {
            $this->invalidateUserCache($produit->elevage->user->id);
        }
    }
}
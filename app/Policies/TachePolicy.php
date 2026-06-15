<?php
// app/Policies/TachePolicy.php

namespace App\Policies;

use App\Models\Tache;
use App\Models\User;

/**
 * Politique d'autorisation pour les tâches
 */
class TachePolicy
{
    /**
     * Vérifie si l'utilisateur peut voir la liste
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Vérifie si l'utilisateur peut voir une tâche
     */
    public function view(User $user, Tache $tache): bool
    {
        if ($user->id === $tache->elevage?->user_id) {
            return true;
        }
        
        $proprietaire = $tache->elevage?->proprietaire;
        return $proprietaire && $proprietaire->profile_visibility === 'public';
    }

    /**
     * Vérifie si l'utilisateur peut créer une tâche
     */
    public function create(User $user, ?int $elevageId = null): bool
    {
        if ($user->status !== 'active') {
            return false;
        }
        
        if ($elevageId) {
            $elevage = \App\Models\Elevage::find($elevageId);
            return $elevage && $elevage->user_id === $user->id;
        }
        
        return true;
    }

    /**
     * Vérifie si l'utilisateur peut modifier une tâche
     */
    public function update(User $user, Tache $tache): bool
    {
        return $user->id === $tache->elevage?->user_id && $user->status === 'active';
    }

    /**
     * Vérifie si l'utilisateur peut supprimer une tâche
     */
    public function delete(User $user, Tache $tache): bool
    {
        return ($user->id === $tache->elevage?->user_id || $user->role === 'admin')
               && $user->status === 'active';
    }

    /**
     * Vérifie si l'utilisateur peut marquer comme terminée
     */
    public function complete(User $user, Tache $tache): bool
    {
        return $user->id === $tache->elevage?->user_id && $user->status === 'active';
    }
}
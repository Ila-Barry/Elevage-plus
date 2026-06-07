<?php
// app/Policies/ElevagePolicy.php

namespace App\Policies;

use App\Models\User;
use App\Models\Elevage;

/**
 * Policy ElevagePolicy
 * 
 * Gère les autorisations pour les opérations sur les élevages
 */
class ElevagePolicy
{
    /**
     * Vérifie si l'utilisateur peut voir la liste des élevages
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Vérifie si l'utilisateur peut voir un élevage spécifique
     */
    public function view(User $user, Elevage $elevage): bool
    {
        return $user->id === $elevage->user_id || $user->isAdmin();
    }

    /**
     * Vérifie si l'utilisateur peut créer un élevage
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Vérifie si l'utilisateur peut modifier un élevage
     */
    public function update(User $user, Elevage $elevage): bool
    {
        return $user->id === $elevage->user_id || $user->isAdmin();
    }

    /**
     * Vérifie si l'utilisateur peut supprimer un élevage
     */
    public function delete(User $user, Elevage $elevage): bool
    {
        return $user->id === $elevage->user_id || $user->isAdmin();
    }
}
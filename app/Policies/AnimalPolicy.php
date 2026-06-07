<?php
// app/Policies/AnimalPolicy.php

namespace App\Policies;

use App\Models\Animal;
use App\Models\User;

/**
 * Politique d'autorisation pour les animaux
 * 
 * Garantit que seuls les propriétaires de l'élevage peuvent modifier/supprimer
 */
class AnimalPolicy
{
    /**
     * Vérifie si l'utilisateur peut voir la liste des animaux
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Vérifie si l'utilisateur peut voir un animal spécifique
     */
    public function view(User $user, Animal $animal): bool
    {
        // L'utilisateur peut voir l'animal s'il est propriétaire de l'élevage
        if ($user->id === $animal->elevage?->user_id) {
            return true;
        }
        
        // Ou si le propriétaire de l'élevage a un profil public
        $proprietaire = $animal->elevage?->proprietaire;
        return $proprietaire && $proprietaire->profile_visibility === 'public';
    }

    /**
     * Vérifie si l'utilisateur peut créer un animal
     * L'utilisateur doit être propriétaire de l'élevage
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
     * Vérifie si l'utilisateur peut modifier un animal
     * Seul le propriétaire de l'élevage peut modifier
     */
    public function update(User $user, Animal $animal): bool
    {
        return $user->id === $animal->elevage?->user_id && $user->status === 'active';
    }

    /**
     * Vérifie si l'utilisateur peut supprimer un animal
     * Seul le propriétaire de l'élevage ou l'admin peut supprimer
     */
    public function delete(User $user, Animal $animal): bool
    {
        return ($user->id === $animal->elevage?->user_id || $user->role === 'admin')
               && $user->status === 'active';
    }
}
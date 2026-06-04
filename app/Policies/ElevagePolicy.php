<?php
// app/Policies/ElevagePolicy.php

namespace App\Policies;

use App\Models\Elevage;
use App\Models\User;

/**
 * Politique d'autorisation pour les élevages
 * 
 * Garantit que seuls les propriétaires peuvent modifier/supprimer leurs élevages
 * Conforme à la vérification du propriétaire exigée dans le cahier des charges
 */
class ElevagePolicy
{
    /**
     * Vérifie si l'utilisateur peut voir la liste des élevages
     * 
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        // Tous les utilisateurs authentifiés peuvent voir la liste publique
        return true;
    }

    /**
     * Vérifie si l'utilisateur peut voir un élevage spécifique
     * 
     * @param User $user
     * @param Elevage $elevage
     * @return bool
     */
    public function view(User $user, Elevage $elevage): bool
    {
        // Un utilisateur peut voir son propre élevage
        // Les autres utilisateurs voient uniquement si le profil est public
        if ($user->id === $elevage->user_id) {
            return true;
        }
        
        // Vérifier si le propriétaire a un profil public
        $proprietaire = $elevage->proprietaire;
        return $proprietaire && $proprietaire->profile_visibility === 'public';
    }

    /**
     * Vérifie si l'utilisateur peut créer un élevage
     * 
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        // Vérifier que l'utilisateur n'est pas banni
        return $user->status === 'active';
    }

    /**
     * Vérifie si l'utilisateur peut modifier un élevage
     * Seul le propriétaire peut modifier
     * 
     * @param User $user
     * @param Elevage $elevage
     * @return bool
     */
    public function update(User $user, Elevage $elevage): bool
    {
        // Vérification du propriétaire (conforme au cahier des charges)
        return $user->id === $elevage->user_id && $user->status === 'active';
    }

    /**
     * Vérifie si l'utilisateur peut supprimer un élevage
     * Seul le propriétaire ou l'admin peut supprimer
     * 
     * @param User $user
     * @param Elevage $elevage
     * @return bool
     */
    public function delete(User $user, Elevage $elevage): bool
    {
        // Le propriétaire ou l'administrateur peut supprimer
        return ($user->id === $elevage->user_id || $user->role === 'admin') 
               && $user->status === 'active';
    }

    /**
     * Vérifie si l'utilisateur peut restaurer un élevage
     * 
     * @param User $user
     * @param Elevage $elevage
     * @return bool
     */
    public function restore(User $user, Elevage $elevage): bool
    {
        return $user->role === 'admin';
    }
}
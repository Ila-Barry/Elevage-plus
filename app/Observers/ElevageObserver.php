<?php
// app/Observers/ElevageObserver.php

namespace App\Observers;

use App\Models\Elevage;
use Illuminate\Support\Facades\Log;

/**
 * Observer ElevageObserver
 * 
 * Déclenche des actions automatiques lors des événements sur les élevages
 */
class ElevageObserver
{
    /**
     * Avant la création d'un élevage
     */
    public function creating(Elevage $elevage): void
    {
        // Définir la date de création si non définie
        if (!$elevage->date_creation) {
            $elevage->date_creation = now();
        }
    }

    /**
     * Après la création d'un élevage
     */
    public function created(Elevage $elevage): void
    {
        Log::info("Nouvel élevage créé: {$elevage->nom} par l'utilisateur {$elevage->user_id}");
    }

    /**
     * Après la mise à jour d'un élevage
     */
    public function updated(Elevage $elevage): void
    {
        Log::info("Élevage mis à jour: {$elevage->nom}");
    }

    /**
     * Avant la suppression d'un élevage
     */
    public function deleting(Elevage $elevage): void
    {
        Log::warning("Élevage supprimé: {$elevage->nom}");
    }
}
<?php
// app/Models/TacheRappel.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * Modèle TacheRappel
 * 
 * Gère les rappels automatiques pour les tâches
 */
class TacheRappel extends Model
{
    protected $table = 'tache_rappels';

    protected $fillable = [
        'tache_id',
        'type_rappel',
        'heure_envoi_prevue',
        'statut',
        'date_envoi',
        'erreur_message',
    ];

    protected $casts = [
        'heure_envoi_prevue' => 'datetime',
        'date_envoi' => 'datetime',
    ];

    /**
     * Relation avec la tâche
     */
    public function tache(): BelongsTo
    {
        return $this->belongsTo(Tache::class);
    }

    /**
     * Vérifie si le rappel doit être envoyé
     */
    public function devraitEtreEnvoye(): bool
    {
        if ($this->statut !== 'pending') {
            return false;
        }
        
        // Ne pas envoyer si la tâche est déjà terminée
        if ($this->tache->terminee) {
            return false;
        }
        
        return Carbon::now()->gte($this->heure_envoi_prevue);
    }

    /**
     * Marque comme envoyé
     */
    public function marquerEnvoye(): void
    {
        $this->statut = 'sent';
        $this->date_envoi = Carbon::now();
        $this->save();
    }

    /**
     * Marque comme échoué
     */
    public function marquerEchoue(string $erreur): void
    {
        $this->statut = 'failed';
        $this->erreur_message = $erreur;
        $this->save();
    }

    /**
     * Obtient le texte du rappel
     */
    public function getMessageRappel(): string
    {
        $tache = $this->tache;
        
        $quand = match($this->type_rappel) {
            '48h' => 'dans 48 heures',
            '24h' => 'demain',
            '1h' => 'dans 1 heure',
            '30min' => 'dans 30 minutes',
            'now' => "aujourd'hui",
            default => "le {$tache->date_planifiee->format('d/m/Y')}",
        };
        
        $entite = $tache->estPourElevage 
            ? "l'élevage {$tache->elevage->nom}"
            : "l'animal {$tache->animal->nom}";
        
        return "🔔 RAPPEL: {$tache->titre} pour {$entite} {$quand}.";
    }
}
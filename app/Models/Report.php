<?php
// Modèle Report (Signalement)

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle Report
 * 
 * Représente un signalement d'une publication par un utilisateur
 */
class Report extends Model
{
    use HasFactory;

    /**
     * Les attributs assignables en masse.
     */
    protected $fillable = [
        'publication_id',
        'user_id',
        'motif',
        'commentaire',
        'statut',
    ];

    /**
     * Les motifs de signalement disponibles
     */
    public const MOTIFS = [
        'spam' => 'Spam ou publicité',
        'offensant' => 'Contenu offensant',
        'fausse_info' => 'Fausse information',
        'contenu_inapproprie' => 'Contenu inapproprié',
        'autre' => 'Autre motif',
    ];

    /**
     * Les statuts disponibles
     */
    public const STATUTS = [
        'en_attente' => 'En attente',
        'traite' => 'Traité',
        'ignore' => 'Ignoré',
    ];

    // ========== RELATIONS ==========

    /**
     * Relation avec la publication
     */
    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }

    /**
     * Relation avec l'utilisateur qui signale
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ========== SCOPES ==========

    /**
     * Scope pour les signalements en attente
     */
    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }
}
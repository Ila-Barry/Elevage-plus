<?php
// Modèle Share (Partage)

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle Share
 * 
 * Représente un partage d'une publication
 */
class Share extends Model
{
    use HasFactory;

    /**
     * Les attributs assignables en masse.
     */
    protected $fillable = [
        'publication_id',
        'user_id',
        'plateforme',
    ];

    /**
     * Plateformes de partage disponibles
     */
    public const PLATEFORMES = [
        'facebook',
        'twitter',
        'whatsapp',
        'linkedin',
        'copie_lien',
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
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
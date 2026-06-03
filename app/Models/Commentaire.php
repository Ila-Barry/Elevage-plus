<?php
// app/Models/Commentaire.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle Commentaire
 * 
 * Représente un commentaire sur une publication
 */
class Commentaire extends Model
{
    use HasFactory;

    /**
     * La table associée au modèle.
     */
    protected $table = 'commentaires';

    /**
     * Les attributs assignables en masse.
     */
    protected $fillable = [
        'publication_id',
        'user_id',
        'parent_id',
        'contenu',
        'nbr_likes',
        'is_edited',
    ];

    /**
     * Les attributs qui doivent être castés.
     */
    protected $casts = [
        'nbr_likes' => 'integer',
        'is_edited' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
     * Relation avec l'utilisateur (auteur du commentaire)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec le commentaire parent (pour les réponses)
     */
    public function parent()
    {
        return $this->belongsTo(Commentaire::class, 'parent_id');
    }

    /**
     * Relation avec les réponses (enfants)
     */
    public function replies()
    {
        return $this->hasMany(Commentaire::class, 'parent_id');
    }

    // ========== ACCESSORS ==========

    /**
     * Accesseur pour le temps écoulé depuis la création
     */
    protected function tempsEcoule(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->created_at->diffForHumans();
            }
        );
    }

    // ========== SCOPES ==========

    /**
     * Scope pour les commentaires principaux (sans parent)
     */
    public function scopePrincipaux($query)
    {
        return $query->whereNull('parent_id');
    }
}
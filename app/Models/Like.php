<?php
// app/Models/Like.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle Like
 * 
 * Représente un like sur une publication
 */
class Like extends Model
{
    use HasFactory;

    /**
     * Les attributs assignables en masse.
     */
    protected $fillable = [
        'publication_id',
        'user_id',
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
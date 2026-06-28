<?php
// app/Models/AnimalHistory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnimalHistory extends Model
{
    use HasFactory;

    protected $table = 'animal_historiques';

    protected $fillable = [
        'animal_id',
        'user_id',
        'champ_modifie',
        'ancienne_valeur',
        'nouvelle_valeur',
        'action',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Relation avec l'animal (peut être null si l'animal a été supprimé)
     */
    public function animal()
    {
        return $this->belongsTo(Animal::class)->withDefault();
    }

    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
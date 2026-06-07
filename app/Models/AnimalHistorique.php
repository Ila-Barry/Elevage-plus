<?php
// app/Models/AnimalHistorique.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle AnimalHistorique
 * 
 * Trace toutes les modifications apportées aux animaux
 * Conforme à ANIM-02 : Historique des modifications optionnel
 */
class AnimalHistorique extends Model
{
    protected $table = 'animal_historiques';

    protected $fillable = [
        'animal_id',
        'user_id',
        'action',
        'before_data',
        'after_data',
        'changed_fields',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'before_data' => 'array',
        'after_data' => 'array',
        'changed_fields' => 'array',
    ];

    /**
     * Relation avec l'animal
     */
    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Formate les champs modifiés pour l'affichage
     */
    public function getFormattedChangesAttribute(): array
    {
        $formatted = [];
        $fields = $this->changed_fields ?? [];
        
        $labels = [
            'nom' => 'Nom',
            'race' => 'Race',
            'espece' => 'Espèce',
            'poids' => 'Poids',
            'statut_sanitaire' => 'Statut sanitaire',
            'description' => 'Description',
            'date_naissance' => 'Date de naissance',
        ];
        
        foreach ($fields as $field) {
            $formatted[] = [
                'field' => $field,
                'label' => $labels[$field] ?? $field,
                'before' => $this->before_data[$field] ?? null,
                'after' => $this->after_data[$field] ?? null,
            ];
        }
        
        return $formatted;
    }
}
<?php
// app/Http/Resources/TacheResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource TacheResource
 * 
 * Formate la réponse API pour une tâche
 */
class TacheResource extends JsonResource
{
    /**
     * Transforme le resource en tableau.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titre' => $this->titre,
            'type' => $this->type,
            'type_label' => $this->type_label,
            'type_icone' => $this->type_icone,
            'description' => $this->description,
            'date_planifiee' => $this->date_planifiee->format('Y-m-d H:i:s'),
            'date_planifiee_humain' => $this->date_planifiee->diffForHumans(),
            'date_realisee' => $this->date_realisee?->format('Y-m-d H:i:s'),
            'terminee' => $this->terminee,
            'priorite' => $this->priorite,
            'priorite_label' => $this->priorite_label,
            'priorite_couleur' => $this->priorite_couleur,
            'rappel' => $this->rappel,
            'rappel_label' => $this->rappel_label,
            'notes' => $this->notes,
            'is_late' => $this->is_late,
            'is_today' => $this->is_today,
            'temps_restant' => $this->temps_restant,
            
            // Entité associée
            'entite' => [
                'type' => $this->animal_id ? 'animal' : 'elevage',
                'id' => $this->animal_id ?? $this->elevage_id,
                'nom' => $this->animal_id ? $this->animal?->nom : $this->elevage?->nom,
            ],
            
            'elevage' => [
                'id' => $this->elevage->id,
                'nom' => $this->elevage->nom,
            ],
            
            'animal' => $this->animal ? [
                'id' => $this->animal->id,
                'nom' => $this->animal->nom,
                'espece' => $this->animal->espece,
                'espece_label' => $this->animal->espece_label,
            ] : null,
            
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
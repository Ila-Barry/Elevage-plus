<?php
// app/Http/Resources/TacheResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource TacheResource
 */
class TacheResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titre' => $this->titre,
            'type' => $this->type,
            'type_label' => $this->type_label,
            'couleur' => $this->couleur,
            'date_planifiee' => $this->date_planifiee?->format('Y-m-d'),
            'date_planifiee_formatee' => $this->date_planifiee?->format('d/m/Y'),
            'date_realisee' => $this->date_realisee?->format('Y-m-d'),
            'date_realisee_formatee' => $this->date_realisee?->format('d/m/Y'),
            'terminee' => $this->terminee,
            'statut' => $this->statut,
            'description' => $this->description,
            'notes' => $this->notes,
            
            // Relations
            'animal' => $this->whenLoaded('animal', function() {
                return [
                    'id' => $this->animal->id,
                    'nom' => $this->animal->nom,
                    'espece' => $this->animal->espece,
                    'espece_label' => $this->animal->espece_label,
                ];
            }),
            'elevage' => $this->whenLoaded('elevage', function() {
                return [
                    'id' => $this->elevage->id,
                    'nom' => $this->elevage->nom,
                ];
            }),
            
            // Informations additionnelles
            'est_pour_elevage' => $this->estPourElevage,
            'entite_concernee' => $this->entite_concernee,
            
            // Rappels
            'rappels' => $this->whenLoaded('rappels', function() {
                return $this->rappels->map(function($rappel) {
                    return [
                        'type' => $rappel->type_rappel,
                        'statut' => $rappel->statut,
                        'heure_prevue' => $rappel->heure_envoi_prevue?->format('d/m/Y H:i'),
                    ];
                });
            }),
            
            // Métadonnées
            'created_at' => $this->created_at?->format('d/m/Y H:i'),
            'updated_at' => $this->updated_at?->format('d/m/Y H:i'),
            'is_owner' => $this->when($request->user(), function() use ($request) {
                return $this->elevage && $this->elevage->user_id === $request->user()?->id;
            }),
        ];
    }
}
<?php
// app/Http/Resources/AnimalResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource AnimalResource
 * 
 * Transforme les données de l'animal pour l'API
 */
class AnimalResource extends JsonResource
{
    /**
     * Transforme la ressource en tableau
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'race' => $this->race,
            'espece' => $this->espece,
            'espece_label' => $this->espece_label,
            'poids' => [
                'valeur' => $this->poids,
                'unite' => 'kg',
                'texte' => $this->poids . ' kg',
            ],
            'statut_sanitaire' => [
                'code' => $this->statut_sanitaire,
                'label' => $this->statut_sanitaire_label,
                'color' => $this->statut_sanitaire_color,
            ],
            'age' => $this->age,
            'date_naissance' => $this->date_naissance?->format('Y-m-d'),
            'date_naissance_formatee' => $this->date_naissance?->format('d/m/Y'),
            'description' => $this->description,
            'image_url' => $this->image_url,
            'image_thumbnail' => $this->getThumbnailUrl(),
            
            // Relations
            'elevage' => new ElevageResource($this->whenLoaded('elevage')),
            
            // Métadonnées
            'created_at' => $this->created_at?->format('d/m/Y H:i'),
            'updated_at' => $this->updated_at?->format('d/m/Y H:i'),
            'is_owner' => $this->when($request->user(), function() use ($request) {
                return $this->elevage && $this->elevage->user_id === $request->user()?->id;
            }),
        ];
    }

    /**
     * Obtient l'URL de la miniature
     */
    private function getThumbnailUrl(): ?string
    {
        if (!$this->img_url) {
            return $this->resource->image_url;
        }
        
        return asset('storage/' . $this->img_url);
    }
}
<?php
// app/Http/Resources/ElevageResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource ElevageResource
 * 
 * Transforme les données de l'élevage pour l'API
 */
class ElevageResource extends JsonResource
{
    /**
     * Transforme la ressource en tableau
     * 
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'localisation' => $this->localisation,
            'superficie' => $this->superficie,
            'superficie_texte' => $this->superficie . ' m²',
            'type_elevage' => $this->type_elevage,
            'type_elevage_label' => $this->getTypeLabel(),
            'description' => $this->description,
            'image_url' => $this->image_url,
            'image_thumbnail' => $this->getThumbnailUrl(),
            
            // Relations
            'proprietaire' => new UserResource($this->whenLoaded('proprietaire')),
            'animaux_count' => $this->whenCounted('animaux', $this->animaux_count),
            
            // Statistiques (calculées)
            'statistiques' => $this->when($request->user(), function() {
                return [
                    'total_animaux' => $this->animaux()->count(),
                    'total_produits' => $this->produits()->count(),
                    'taches_en_attente' => $this->getPendingTasksCount(),
                ];
            }),
            
            // Métadonnées
            'created_at' => $this->created_at?->format('d/m/Y H:i'),
            'updated_at' => $this->updated_at?->format('d/m/Y H:i'),
            'is_owner' => $this->when($request->user(), function() use ($request) {
                return $this->user_id === $request->user()?->id;
            }),
        ];
    }

    /**
     * Obtient le libellé du type d'élevage
     * 
     * @return string
     */
    private function getTypeLabel(): string
    {
        $labels = [
            'bovins' => '🐄 Élevage bovin',
            'ovins' => '🐑 Élevage ovin',
            'caprins' => '🐐 Élevage caprin',
            'volailles' => '🐔 Élevage de volailles',
            'mixte' => '🌾 Élevage mixte',
            'autres' => '📌 Autre type',
        ];
        
        return $labels[$this->type_elevage] ?? $this->type_elevage;
    }

    /**
     * Obtient l'URL de la miniature
     * 
     * @return string|null
     */
    private function getThumbnailUrl(): ?string
    {
        if (!$this->img_url) {
            return null;
        }
        
        // Pourrait générer une miniature avec un paramètre
        if (str_starts_with($this->img_url, 'http')) {
            return $this->img_url;
        }
        
        return asset('storage/' . $this->img_url);
    }

    /**
     * Calcule le nombre de tâches en attente
     * 
     * @return int
     */
    private function getPendingTasksCount(): int
    {
        // Relation à implémenter si nécessaire
        return 0;
    }
}

// app/Http/Resources/UserResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->when($request->user()?->id === $this->id, $this->email),
            'photo_url' => $this->photo_url,
            'bio' => $this->bio,
            'profile_visibility' => $this->profile_visibility,
            'joined_date' => $this->created_at?->format('d/m/Y'),
        ];
    }
}
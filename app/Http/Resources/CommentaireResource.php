<?php
// app/Http/Resources/CommentaireResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentaireResource extends JsonResource
{
    /**
     * Transforme le resource en tableau.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'contenu' => $this->contenu,
            'nbr_likes' => $this->nbr_likes,
            'is_edited' => (bool) $this->is_edited,
            'temps_ecoule' => $this->created_at?->diffForHumans(),
            'auteur' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'photo_url' => $this->user->photo_url,
            ],
            'replies' => CommentaireResource::collection($this->whenLoaded('replies')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
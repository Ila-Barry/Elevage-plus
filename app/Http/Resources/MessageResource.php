<?php
// app/Http/Resources/MessageResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource pour le message
 */
class MessageResource extends JsonResource
{
    /**
     * Transforme la ressource en tableau.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $userId = auth()->id();
        
        return [
            'id' => $this->id,
            'conversation_id' => $this->conversation_id,
            'expediteur' => [
                'id' => $this->expediteur->id,
                'name' => $this->expediteur->name,
                'photo_url' => $this->expediteur->photo_url,
            ],
            'destinataire' => [
                'id' => $this->destinataire->id,
                'name' => $this->destinataire->name,
                'photo_url' => $this->destinataire->photo_url,
            ],
            'contenu' => $this->contenu,
            'lu' => $this->lu,
            'lu_at' => $this->lu_at ? $this->lu_at->toISOString() : null,
            'is_sent_by_me' => $this->expediteur_id === $userId,
            'created_at' => $this->created_at->toISOString(),
            'formatted_date' => $this->created_at->diffForHumans(),
        ];
    }
}
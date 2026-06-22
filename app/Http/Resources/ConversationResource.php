<?php
// app/Http/Resources/ConversationResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource pour la conversation
 */
class ConversationResource extends JsonResource
{
    /**
     * Transforme la ressource en tableau.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $userId = auth()->id();
        $otherParticipant = $this->getOtherParticipant($userId);
        
        return [
            'id' => $this->id,
            'other_participant' => $otherParticipant ? [
                'id' => $otherParticipant->id,
                'name' => $otherParticipant->name,
                'photo_url' => $otherParticipant->photo_url,
                'email' => $otherParticipant->email,
            ] : null,
            'derniere_message' => $this->derniere_message,
            'unread_count' => $this->countUnreadMessagesForUser($userId),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
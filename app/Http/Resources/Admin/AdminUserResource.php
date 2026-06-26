<?php
// app/Http/Resources/Admin/AdminUserResource.php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'telephone' => $this->telephone,
            'role' => $this->role,
            'role_label' => $this->role === 'admin' ? 'Administrateur' : 
                           ($this->role === 'eleveur' ? 'Éleveur' : 'Visiteur'),
            'status' => $this->status,
            'status_label' => $this->status === 'active' ? 'Actif' : 'Banni',
            'bio' => $this->bio,
            'photo_url' => $this->photo_url,
            'profile_visibility' => $this->profile_visibility,
            'email_notifications' => $this->email_notifications,
            'web_notifications' => $this->web_notifications,
            'email_verified_at' => $this->email_verified_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'statistiques' => [
                'total_publications' => $this->publications()->count(),
                'total_commentaires' => $this->commentaires()->count(),
                'total_likes_reçus' => $this->publications()->sum('nbr_likes'),
                'total_elevages' => $this->elevages()->count(),
                'total_animaux' => $this->elevages()->withCount('animaux')->get()->sum('animaux_count'),
            ],
        ];
    }
}
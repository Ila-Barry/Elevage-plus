<?php
// app/Http/Resources/AnimalHistoryResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnimalHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'champ_modifie' => $this->champ_modifie ?? null,
            'ancienne_valeur' => $this->ancienne_valeur ?? null,
            'nouvelle_valeur' => $this->nouvelle_valeur ?? null,
            'action' => $this->action ?? null,
            'action_label' => $this->getActionLabel(),
            'utilisateur' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
            ],
            'ip_address' => $this->ip_address ?? null,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'created_at_human' => $this->created_at?->diffForHumans(),
        ];
    }

    protected function getActionLabel(): string
    {
        return match($this->action) {
            'create' => 'Création',
            'update' => 'Modification',
            'delete' => 'Suppression',
            default => ucfirst($this->action ?? ''),
        };
    }
}
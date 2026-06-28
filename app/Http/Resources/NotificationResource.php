<?php
// app/Http/Resources/NotificationResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray($request)
    {
        $data = $this->data ?? [];
        
        return [
            'id' => $this->id,
            'title' => $data['title'] ?? 'Notification',
            'message' => $data['message'] ?? '',
            'type' => $data['type'] ?? 'info',
            'icon' => $data['icon'] ?? '🔔',
            'url' => $data['url'] ?? null,
            'actions' => $data['actions'] ?? [],
            'read_at' => $this->read_at,
            'is_read' => !is_null($this->read_at),
            'created_at' => $this->created_at,
            'created_at_human' => $this->created_at ? $this->created_at->diffForHumans() : null,
            'updated_at' => $this->updated_at,
        ];
    }
}
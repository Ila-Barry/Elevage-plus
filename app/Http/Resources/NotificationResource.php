<?php
// app/Http/Resources/NotificationResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = $this->data ?? [];
        
        return [
            'id' => $this->id,
            'type' => $data['type'] ?? $this->type ?? 'info',
            'title' => $data['title'] ?? 'Notification',
            'message' => $data['message'] ?? '',
            'url' => $data['url'] ?? '#',
            'icon' => $data['icon'] ?? 'fa-bell',
            'actions' => $data['actions'] ?? [],
            'is_read' => $this->read_at !== null,
            'read_at' => $this->read_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
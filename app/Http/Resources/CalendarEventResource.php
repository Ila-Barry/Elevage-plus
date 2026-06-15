<?php
// app/Http/Resources/CalendarEventResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource CalendarEventResource
 * 
 * Formate la réponse API pour FullCalendar
 */
class CalendarEventResource extends JsonResource
{
    /**
     * Transforme le resource en tableau pour FullCalendar.
     */
    public function toArray(Request $request): array
    {
        $color = $this->priorite_couleur;
        
        if ($this->terminee) {
            $color = '#6B7280'; // gris
        } elseif ($this->is_late) {
            $color = '#EF4444'; // rouge pour les tâches en retard
        }
        
        return [
            'id' => (string) $this->id,
            'title' => $this->titre,
            'start' => $this->date_planifiee->toIso8601String(),
            'end' => $this->date_planifiee->copy()->addHour()->toIso8601String(),
            'allDay' => false,
            'color' => $color,
            'textColor' => '#FFFFFF',
            'className' => 'task-event task-type-' . $this->type,
            'extendedProps' => [
                'type' => $this->type,
                'type_label' => $this->type_label,
                'type_icone' => $this->type_icone,
                'priorite' => $this->priorite,
                'priorite_label' => $this->priorite_label,
                'terminee' => $this->terminee,
                'is_late' => $this->is_late,
                'description' => $this->description,
                'animal_nom' => $this->animal?->nom,
                'elevage_nom' => $this->elevage->nom,
                'url' => "/tasks/{$this->id}",
            ],
        ];
    }
}
<?php
// app/Notifications/TaskReminderNotification.php

namespace App\Notifications;

use App\Models\Tache;

class TaskReminderNotification extends BaseNotification
{
    protected Tache $tache;
    protected string $reminderType;

    public function __construct(Tache $tache, string $reminderType, string $message)
    {
        $this->tache = $tache;
        $this->reminderType = $reminderType;
        
        $this->title = match($reminderType) {
            '48h' => '🔔 Rappel tâche (après-demain)',
            '24h' => '🔔 Rappel tâche (demain)',
            '1h' => '🔔 Rappel urgent (aujourd\'hui)',
            'retard' => '⚠️ Tâche en retard',
            default => 'Rappel de tâche',
        };
        
        $this->message = $message;
        $this->type = $reminderType === 'retard' ? 'danger' : 'warning';
        $this->url = "/taches/{$tache->id}";
        
        if ($reminderType === 'retard') {
            $this->message .= " Cliquez ici pour la marquer comme terminée.";
        }
        
        $this->actions = [
            [
                'label' => 'Voir la tâche',
                'url' => "/taches/{$tache->id}",
                'type' => 'primary'
            ],
            [
                'label' => 'Marquer comme terminée',
                'url' => "/taches/{$tache->id}/complete",
                'type' => 'success'
            ]
        ];
    }
}
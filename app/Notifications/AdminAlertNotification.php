<?php
// app/Notifications/AdminAlertNotification.php

namespace App\Notifications;

class AdminAlertNotification extends BaseNotification
{
    public function __construct(string $title, string $message, string $type = 'info', ?string $url = null)
    {
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
        $this->url = $url;
        
        $this->actions = [
            [
                'label' => 'Voir les détails',
                'url' => $url ?? '/admin/dashboard',
                'type' => 'primary'
            ]
        ];
    }

    protected function getAdditionalInfo(): string
    {
        return "📅 Date : " . now()->format('d/m/Y H:i');
    }
}
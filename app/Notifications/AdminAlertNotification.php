<?php
// app/Notifications/AdminAlertNotification.php

namespace App\Notifications;

/**
 * Notification AdminAlertNotification
 * 
 * Envoie une alerte générique aux administrateurs
 */
class AdminAlertNotification extends BaseNotification
{
    /**
     * Créer une nouvelle instance
     */
    public function __construct(string $title, string $message, string $type = 'info', ?string $url = null)
    {
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
        $this->url = $url;
    }

    /**
     * Informations supplémentaires pour l'email
     */
    protected function getAdditionalInfo(): string
    {
        return "📅 Date : " . now()->format('d/m/Y H:i');
    }
}
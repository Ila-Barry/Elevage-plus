<?php
// app/Notifications/BaseNotification.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;

/**
 * Classe de base pour toutes les notifications
 * Gère l'envoi sur tous les canaux (email, database, webpush)
 */
abstract class BaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Types de canaux disponibles
     */
    protected array $channels = ['database', 'mail', 'webpush'];

    /**
     * Titre de la notification
     */
    protected string $title;

    /**
     * Message de la notification
     */
    protected string $message;

    /**
     * Type d'alerte (info, success, warning, danger)
     */
    protected string $type = 'info';

    /**
     * URL de redirection
     */
    protected ?string $url = null;

    /**
     * Icône de la notification
     */
    protected ?string $icon = null;

    /**
     * Définit les canaux d'envoi selon les préférences de l'utilisateur
     */
    public function via($notifiable): array
    {
        $channels = ['database']; // Base de données toujours incluse
        
        if ($notifiable->email_notifications) {
            $channels[] = 'mail';
        }
        
        if ($notifiable->web_notifications) {
            $channels[] = 'webpush';
        }
        
        return $channels;
    }

    /**
     * Format pour la base de données
     */
    public function toDatabase($notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'icon' => $this->getIcon(),
            'url' => $this->url,
            'timestamp' => now()->toIso8601String(),
        ];
    }

    /**
     * Format pour l'email
     */
    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->title)
            ->greeting('Bonjour ' . $notifiable->name . ' !')
            ->line($this->message)
            ->line($this->getAdditionalInfo());
        
        if ($this->url) {
            $mail->action('Voir les détails', url($this->url));
        }
        
        return $mail;
    }

    /**
     * Format pour le WebPush (notifications push mobile/desktop)
     */
    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title($this->title)
            ->icon($this->getIcon() ?? '/images/icon-512x512.png')
            ->badge('/images/badge-icon.png')
            ->body($this->message)
            ->data(['url' => $this->url ?? '/dashboard'])
            ->vibrate([200, 100, 200])
            ->requireInteraction($this->type === 'danger');
    }

    /**
     * Récupère l'icône selon le type
     */
    protected function getIcon(): string
    {
        if ($this->icon) {
            return $this->icon;
        }
        
        return match($this->type) {
            'success' => '✅',
            'warning' => '⚠️',
            'danger' => '🔴',
            default => '🔔',
        };
    }

    /**
     * Informations supplémentaires pour l'email
     */
    protected function getAdditionalInfo(): string
    {
        return '';
    }
}
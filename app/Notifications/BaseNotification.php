<?php
// app/Notifications/BaseNotification.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\WebPush\WebPushMessage;

abstract class BaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected array $channels = ['database', 'mail', 'webpush'];
    protected string $title;
    protected string $message;
    protected string $type = 'info';
    protected ?string $url = null;
    protected ?string $icon = null;
    protected array $actions = [];

    public function via($notifiable): array
    {
        $channels = ['database'];
        
        if ($notifiable->email_notifications ?? false) {
            $channels[] = 'mail';
        }
        
        if (config('webpush.enabled', false) && ($notifiable->web_notifications ?? false)) {
            $channels[] = 'webpush';
        }
        
        return $channels;
    }

    public function toDatabase($notifiable): array
    {
        return [
            'id' => uniqid(),
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'icon' => $this->getIcon(),
            'url' => $this->url,
            'actions' => $this->actions,
            'timestamp' => now()->toIso8601String(),
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->title)
            ->greeting('Bonjour ' . ($notifiable->name ?? 'Utilisateur') . ' !')
            ->line($this->message)
            ->line($this->getAdditionalInfo());
        
        if ($this->url) {
            $mail->action('Voir les détails', url($this->url));
        }
        
        return $mail->salutation('L\'équipe Élevage+');
    }

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

    protected function getAdditionalInfo(): string
    {
        return '';
    }
}
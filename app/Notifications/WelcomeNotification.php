<?php
// app/Notifications/WelcomeNotification.php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function via($notifiable): array
    {
        $channels = ['database', 'mail'];
        
        if (config('webpush.enabled', false) && ($notifiable->web_notifications ?? false)) {
            $channels[] = 'webpush';
        }
        
        return $channels;
    }

    public function toDatabase($notifiable): array
    {
        return [
            'id' => uniqid(),
            'title' => '🎉 Bienvenue sur Élevage+ !',
            'message' => "Bienvenue {$this->user->name} ! Nous sommes ravis de vous compter parmi notre communauté d'éleveurs.",
            'type' => 'success',
            'icon' => '🎉',
            'url' => '/dashboard',
            'actions' => [
                [
                    'label' => 'Explorer',
                    'url' => '/dashboard',
                    'type' => 'primary'
                ]
            ],
            'created_at' => now()->toIso8601String(),
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🎉 Bienvenue sur Élevage+ !')
            ->greeting('Bonjour ' . $this->user->name . ' !')
            ->line('Nous sommes ravis de vous accueillir sur Élevage+, la plateforme de gestion d\'élevage et d\'entraide communautaire.')
            ->line('Voici ce que vous pouvez faire dès maintenant :')
            ->line('• 📊 Gérer vos élevages et vos animaux')
            ->line('• 📅 Planifier vos tâches et recevoir des rappels')
            ->line('• 📦 Suivre vos stocks et recevoir des alertes')
            ->line('• 👥 Échanger avec la communauté d\'éleveurs')
            ->action('Accéder à mon compte', url('/login'))
            ->line('Merci de nous faire confiance !')
            ->salutation('L\'équipe Élevage+');
    }

    public function toWebPush($notifiable, $notification): \NotificationChannels\WebPush\WebPushMessage
    {
        return (new \NotificationChannels\WebPush\WebPushMessage)
            ->title('🎉 Bienvenue sur Élevage+ !')
            ->icon('/images/icon-512x512.png')
            ->badge('/images/badge-icon.png')
            ->body("Bienvenue {$this->user->name} ! Commencez à gérer votre élevage.")
            ->data(['url' => '/dashboard'])
            ->vibrate([200, 100, 200]);
    }
}
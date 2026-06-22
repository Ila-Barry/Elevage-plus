<?php
// app/Notifications/WelcomeNotification.php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\WebPush\WebPushMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Canaux d'envoi
     */
    public function via($notifiable): array
    {
        // ✅ CORRECTION : Toujours envoyer par email et database
        $channels = ['database', 'mail'];
        
        // Ajouter webpush si l'utilisateur a accepté
        if ($notifiable->web_notifications && config('webpush.enabled', false)) {
            $channels[] = 'webpush';
        }
        
        return $channels;
    }

    /**
     * Notification pour la base de données (cloche)
     */
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

    /**
     * Notification pour WebSocket (notification en temps réel)
     */
    public function toBroadcast($notifiable): array
    {
        return [
            'id' => uniqid(),
            'title' => '🎉 Bienvenue sur Élevage+ !',
            'message' => "Bienvenue {$this->user->name} !",
            'type' => 'success',
            'icon' => '🎉',
            'timestamp' => now()->toIso8601String(),
        ];
    }

    /**
     * ✅ CORRECTION : Email - maintenant toujours envoyé
     */
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

    /**
     * ✅ CORRECTION : WebPush
     */
    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('🎉 Bienvenue sur Élevage+ !')
            ->icon('/images/icon-512x512.png')
            ->badge('/images/badge-icon.png')
            ->body("Bienvenue {$this->user->name} ! Commencez à gérer votre élevage.")
            ->data(['url' => '/dashboard'])
            ->vibrate([200, 100, 200]);
    }

    /**
     * Définir la connexion de queue
     */
    public function viaQueue(): string
    {
        return 'notifications';
    }
}
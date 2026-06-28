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

    /**
     * Canaux d'envoi
     */
    public function via($notifiable): array
    {
        // Pour le développement, utiliser seulement 'database'
        // Pour la production, ajouter 'mail' et 'broadcast'
        $channels = ['database'];
        
        // Activer les emails en production uniquement
        if (app()->environment('production')) {
            $channels[] = 'mail';
            $channels[] = 'broadcast'; // Pour WebSockets
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
     * Email
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Bienvenue sur Élevage+ !')
            ->greeting('Bonjour ' . $this->user->name . ' !')
            ->line('Nous sommes ravis de vous accueillir sur Élevage+.')
            ->line('Commencez dès maintenant à gérer votre élevage et à échanger avec la communauté.')
            ->action('Accéder à mon compte', url('/login'))
            ->line('Merci de nous faire confiance !');
    }

    /**
     * Définir la connexion de queue
     */
    public function viaQueue(): string
    {
        return 'notifications';
    }
}
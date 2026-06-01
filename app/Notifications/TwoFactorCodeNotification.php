<?php
// app/Notifications/TwoFactorCodeNotification.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification TwoFactorCodeNotification
 * 
 * Envoie le code d'authentification à deux facteurs par email
 */
class TwoFactorCodeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Le code d'authentification à deux facteurs
     *
     * @var string
     */
    protected string $code;

    /**
     * Créer une nouvelle instance de notification.
     *
     * @param string $code
     */
    public function __construct(string $code)
    {
        $this->code = $code;
    }

    /**
     * Définit les canaux de livraison de la notification.
     *
     * @param mixed $notifiable
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Formate la notification pour l'envoi par email.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🔐 Code d\'authentification - Élevage+')
            ->greeting('Bonjour ' . $notifiable->name . ' !')
            ->line('Vous avez demandé à vous connecter à votre compte Élevage+.')
            ->line('Voici votre code d\'authentification à usage unique :')
            ->line('**' . $this->code . '**')
            ->line('Ce code expirera dans 10 minutes.')
            ->line('Si vous n\'êtes pas à l\'origine de cette demande, ignorez cet email.')
            ->salutation('Cordialement, L\'équipe Élevage+');
    }
}
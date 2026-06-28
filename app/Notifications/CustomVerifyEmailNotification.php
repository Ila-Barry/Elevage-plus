<?php
// app/Notifications/CustomVerifyEmailNotification.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

/**
 * Notification CustomVerifyEmailNotification
 * 
 * Envoie un email de vérification avec lien signé direct vers l'API
 * Email envoyé immédiatement (pas de queue)
 */
class CustomVerifyEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Canaux d'envoi
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Construction de l'email de vérification
     */
    public function toMail($notifiable): MailMessage
    {
        // Générer l'URL de vérification signée directement
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('🔐 Vérification de votre email - Élevage+')
            ->greeting('Bonjour ' . $notifiable->name . ' !')
            ->line('Merci de vous être inscrit sur Élevage+.')
            ->line('Pour activer votre compte, veuillez cliquer sur le bouton ci-dessous :')
            ->action('Vérifier mon email', $verificationUrl)
            ->line('Ce lien expirera dans 60 minutes.')
            ->line('Si vous n\'avez pas créé de compte, ignorez cet email.')
            ->salutation('L\'équipe Élevage+');
    }

    /**
     * Génère l'URL de vérification signée
     */
    protected function verificationUrl($notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
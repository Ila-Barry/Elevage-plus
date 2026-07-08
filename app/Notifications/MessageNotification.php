<?php
// app/Notifications/MessageNotification.php

namespace App\Notifications;

use App\Models\Message;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;
use Illuminate\Support\Facades\Log;

class MessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Message $message;
    protected User $expediteur;
    protected string $type; // 'new_message', 'message_read'

    /**
     * Create a new notification instance.
     */
    public function __construct(Message $message, string $type = 'new_message')
    {
        $this->message = $message;
        $this->expediteur = $message->expediteur;
        $this->type = $type;
        
        Log::info('🔔 Notification Message créée', [
            'message_id' => $message->id,
            'type' => $type,
            'expediteur_id' => $message->expediteur_id,
            'destinataire_id' => $message->destinataire_id
        ]);
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        $channels = ['database'];
        
        if ($notifiable->web_notifications ?? true) {
            $channels[] = WebPushChannel::class;
        }
        
        if ($notifiable->email_notifications ?? false) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable)
    {
        $data = $this->getNotificationData();
        
        Log::info('💾 Notification Message sauvegardée en base', [
            'user_id' => $notifiable->id,
            'message_id' => $this->message->id,
            'expediteur_id' => $this->expediteur->id
        ]);
        
        return $data;
    }

    /**
     * Get the web push representation of the notification.
     */
    public function toWebPush($notifiable, $notification)
    {
        $data = $this->getNotificationData();
        $expediteurNom = $this->expediteur->name;
        
        return (new WebPushMessage)
            ->title("📩 Nouveau message de {$expediteurNom}")
            ->icon($this->expediteur->photo_url ?? '/images/logo-elevage-plus.png')
            ->body($data['message'])
            ->action('Voir le message', 'view_message')
            ->data([
                'id' => $notification->id,
                'url' => $data['url'],
                'message_id' => $this->message->id,
                'conversation_id' => $this->message->conversation_id,
                'expediteur_id' => $this->expediteur->id
            ])
            ->badge('/images/badge.png')
            ->dir('auto')
            ->lang('fr')
            ->vibrate([200, 100, 200, 100, 200]);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $data = $this->getNotificationData();
        $expediteurNom = $this->expediteur->name;
        
        $contenu = $this->message->contenu;
        if ($this->message->type === 'image') {
            $contenu = '📷 [Image partagée]';
        } elseif ($this->message->type === 'file') {
            $contenu = '📎 [Fichier joint]';
        } elseif ($this->message->type === 'sticker') {
            $contenu = '🎨 [Sticker]';
        }
        
        return (new MailMessage)
            ->subject("📩 Nouveau message de {$expediteurNom}")
            ->greeting("Bonjour {$notifiable->name} !")
            ->line("Vous avez reçu un nouveau message de **{$expediteurNom}**.")
            ->line("")
            ->line("**Message :**")
            ->line($contenu)
            ->action('Voir le message', url($data['url']))
            ->line("")
            ->line("Pour répondre, connectez-vous à la plateforme Élevage+.")
            ->salutation('Cordialement, L\'équipe Élevage+');
    }

    /**
     * Get the notification data.
     */
    protected function getNotificationData(): array
    {
        $expediteurNom = $this->expediteur->name;
        $contenu = $this->message->contenu;
        
        // Tronquer le message si trop long
        if (strlen($contenu) > 100) {
            $contenu = substr($contenu, 0, 100) . '...';
        }
        
        // Gérer les différents types de messages
        $messagePreview = $contenu;
        if ($this->message->type === 'image') {
            $messagePreview = '📷 Image partagée';
        } elseif ($this->message->type === 'file') {
            $messagePreview = '📎 Fichier joint : ' . ($this->message->file_name ?? 'Fichier');
        } elseif ($this->message->type === 'sticker') {
            $messagePreview = '🎨 Sticker';
        } elseif ($this->message->type === 'video') {
            $messagePreview = '🎥 Vidéo';
        }
        
        if ($this->type === 'new_message') {
            return [
                'title' => "📩 Nouveau message de {$expediteurNom}",
                'message' => $messagePreview,
                'type' => 'message',
                'icon' => 'fa-envelope',
                'url' => '/messagerie?conversation=' . $this->message->conversation_id,
                'expediteur_id' => $this->expediteur->id,
                'expediteur_nom' => $expediteurNom,
                'message_id' => $this->message->id,
                'conversation_id' => $this->message->conversation_id,
                'is_read' => false,
                'created_at' => $this->message->created_at->toISOString(),
            ];
        }
        
        if ($this->type === 'message_read') {
            return [
                'title' => "✅ Message lu par {$expediteurNom}",
                'message' => "{$expediteurNom} a lu votre message.",
                'type' => 'info',
                'icon' => 'fa-check-circle',
                'url' => '/messagerie?conversation=' . $this->message->conversation_id,
                'expediteur_id' => $this->expediteur->id,
                'message_id' => $this->message->id,
                'conversation_id' => $this->message->conversation_id,
            ];
        }
        
        return [
            'title' => "💬 Nouveau message",
            'message' => $messagePreview,
            'type' => 'message',
            'icon' => 'fa-envelope',
            'url' => '/messagerie',
        ];
    }
}
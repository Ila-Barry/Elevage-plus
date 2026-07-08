<?php
// app/Notifications/PublicationNotification.php

namespace App\Notifications;

use App\Models\Publication;
use App\Models\User;
use App\Models\Commentaire;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;
use Illuminate\Support\Facades\Log;

class PublicationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $publication;
    protected string $type;
    protected $actor;
    protected $additionalData;

    /**
     * Types de notifications
     */
    public const TYPE_CREATED = 'created';
    public const TYPE_UPDATED = 'updated';
    public const TYPE_DELETED = 'deleted';
    public const TYPE_LIKED = 'liked';
    public const TYPE_COMMENTED = 'commented';
    public const TYPE_SHARED = 'shared';

    /**
     * Create a new notification instance.
     */
    public function __construct(Publication $publication, string $type, $actor = null, array $additionalData = [])
    {
        $this->publication = $publication;
        $this->type = $type;
        $this->actor = $actor;
        $this->additionalData = $additionalData;
        
        Log::info('🔔 Notification Publication créée', [
            'publication_id' => $publication->id,
            'type' => $type,
            'actor_id' => $actor?->id,
            'user_id' => $publication->user_id
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
        
        Log::info('💾 Notification Publication sauvegardée en base', [
            'user_id' => $notifiable->id,
            'type' => $data['type'],
            'publication_id' => $this->publication->id
        ]);
        
        return $data;
    }

    /**
     * Get the web push representation of the notification.
     */
    public function toWebPush($notifiable, $notification)
    {
        $data = $this->getNotificationData();
        
        return (new WebPushMessage)
            ->title($data['title'])
            ->icon('/images/logo-elevage-plus.png')
            ->body($data['message'])
            ->action('Voir la publication', 'view_publication')
            ->data([
                'id' => $notification->id,
                'url' => $data['url'],
                'publication_id' => $this->publication->id
            ])
            ->badge('/images/badge.png')
            ->dir('auto')
            ->lang('fr')
            ->vibrate([200, 100, 200]);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $data = $this->getNotificationData();
        
        return (new MailMessage)
            ->subject($data['title'])
            ->greeting('Bonjour ' . $notifiable->name . ' !')
            ->line($data['message'])
            ->line('')
            ->line('📝 ' . $this->publication->titre)
            ->action('Voir la publication', url($data['url']))
            ->line('Merci d\'utiliser la plateforme Élevage+ !')
            ->salutation('Cordialement, L\'équipe Élevage+');
    }

    /**
     * Get the notification data.
     */
    protected function getNotificationData(): array
    {
        $titre = $this->publication->titre;
        $categorie = $this->publication->categorie_label ?? $this->publication->categorie;
        $auteurNom = $this->publication->user?->name ?? 'Un éleveur';
        
        switch ($this->type) {
            case self::TYPE_CREATED:
                return [
                    'title' => '📝 Nouvelle publication',
                    'message' => "Vous avez publié un nouvel article '{$titre}' dans la catégorie {$categorie}.",
                    'type' => 'success',
                    'icon' => 'fa-pencil-alt',
                    'url' => '/blog/' . $this->publication->id,
                ];
            
            case self::TYPE_UPDATED:
                return [
                    'title' => '📝 Publication modifiée',
                    'message' => "Votre article '{$titre}' a été mis à jour avec succès.",
                    'type' => 'info',
                    'icon' => 'fa-edit',
                    'url' => '/blog/' . $this->publication->id,
                ];
            
            case self::TYPE_DELETED:
                return [
                    'title' => '🗑️ Publication supprimée',
                    'message' => "Votre article '{$titre}' a été supprimé.",
                    'type' => 'warning',
                    'icon' => 'fa-trash',
                    'url' => '/blog',
                ];
            
            case self::TYPE_LIKED:
                $actorName = $this->actor?->name ?? 'Un éleveur';
                return [
                    'title' => '❤️ Nouveau like',
                    'message' => "{$actorName} a aimé votre publication '{$titre}'.",
                    'type' => 'success',
                    'icon' => 'fa-heart',
                    'url' => '/blog/' . $this->publication->id,
                ];
            
            case self::TYPE_COMMENTED:
                $actorName = $this->actor?->name ?? 'Un éleveur';
                $commentContent = $this->additionalData['comment_content'] ?? '';
                if (strlen($commentContent) > 60) {
                    $commentContent = substr($commentContent, 0, 60) . '...';
                }
                return [
                    'title' => '💬 Nouveau commentaire',
                    'message' => "{$actorName} a commenté votre publication '{$titre}': \"{$commentContent}\"",
                    'type' => 'info',
                    'icon' => 'fa-comment',
                    'url' => '/blog/' . $this->publication->id,
                ];
            
            case self::TYPE_SHARED:
                $actorName = $this->actor?->name ?? 'Un éleveur';
                $platform = $this->additionalData['platform'] ?? 'une plateforme';
                return [
                    'title' => '🔄 Nouveau partage',
                    'message' => "{$actorName} a partagé votre publication '{$titre}' sur {$platform}.",
                    'type' => 'info',
                    'icon' => 'fa-share-alt',
                    'url' => '/blog/' . $this->publication->id,
                ];
            
            default:
                return [
                    'title' => '📢 Publication',
                    'message' => "Votre publication '{$titre}' a reçu une interaction.",
                    'type' => 'info',
                    'icon' => 'fa-bell',
                    'url' => '/blog/' . $this->publication->id,
                ];
        }
    }
}
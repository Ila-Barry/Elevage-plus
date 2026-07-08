<?php
// app/Notifications/ElevageNotification.php

namespace App\Notifications;

use App\Models\Elevage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;
use Illuminate\Support\Facades\Log;

class ElevageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Elevage $elevage;
    protected string $action;
    protected array $additionalData;

    /**
     * Create a new notification instance.
     *
     * @param Elevage $elevage
     * @param string $action (created, updated, deleted)
     * @param array $additionalData
     */
    public function __construct(Elevage $elevage, string $action, array $additionalData = [])
    {
        $this->elevage = $elevage;
        $this->action = $action;
        $this->additionalData = $additionalData;
        
        Log::info('🔔 Notification Elevage créée', [
            'elevage_id' => $elevage->id,
            'action' => $action,
            'user_id' => $elevage->user_id
        ]);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $channels = ['database'];
        
        // Vérifier les préférences de l'utilisateur
        if ($notifiable->web_notifications ?? true) {
            $channels[] = WebPushChannel::class;
        }
        
        if ($notifiable->email_notifications ?? false) {
            $channels[] = 'mail';
        }
        
        Log::info('📨 Canaux de notification', [
            'user_id' => $notifiable->id,
            'channels' => $channels
        ]);
        
        return $channels;
    }

    /**
     * Get the database representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        $data = $this->getNotificationData();
        
        Log::info('💾 Notification sauvegardée en base', [
            'user_id' => $notifiable->id,
            'type' => $data['type'],
            'elevage_id' => $this->elevage->id
        ]);
        
        return $data;
    }

    /**
     * Get the web push representation of the notification.
     *
     * @param mixed $notifiable
     * @param mixed $notification
     * @return WebPushMessage
     */
    public function toWebPush($notifiable, $notification)
    {
        $data = $this->getNotificationData();
        
        Log::info('📱 Envoi notification WebPush', [
            'user_id' => $notifiable->id,
            'title' => $data['title']
        ]);
        
        return (new WebPushMessage)
            ->title($data['title'])
            ->icon('/images/logo-elevage-plus.png')
            ->body($data['message'])
            ->action('Voir l\'élevage', 'view_elevage')
            ->data([
                'id' => $notification->id,
                'url' => $data['url'],
                'elevage_id' => $this->elevage->id
            ])
            ->badge('/images/badge.png')
            ->dir('auto')
            ->lang('fr')
            ->vibrate([200, 100, 200]);
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        $data = $this->getNotificationData();
        $actionText = $this->getActionText();
        
        Log::info('✉️ Envoi email notification', [
            'user_id' => $notifiable->id,
            'email' => $notifiable->email
        ]);
        
        return (new MailMessage)
            ->subject($data['title'])
            ->greeting('Bonjour ' . $notifiable->name . ' !')
            ->line($data['message'])
            ->line('Détails de l\'élevage :')
            ->line('- Nom : ' . $this->elevage->nom)
            ->line('- Type : ' . $this->elevage->type_elevage_label)
            ->line('- Localisation : ' . $this->elevage->localisation)
            ->action('Voir l\'élevage', url($data['url']))
            ->line('Merci d\'utiliser la plateforme Élevage+ !')
            ->salutation('Cordialement, L\'équipe Élevage+');
    }

    /**
     * Get the notification data.
     *
     * @return array
     */
    protected function getNotificationData(): array
    {
        $userName = $this->elevage->user->name ?? 'Un éleveur';
        
        switch ($this->action) {
            case 'created':
                return [
                    'title' => 'Nouvel élevage créé ✅',
                    'message' => "Votre élevage '{$this->elevage->nom}' a été créé avec succès.",
                    'type' => 'success',
                    'icon' => 'fa-check-circle',
                    'url' => '/elevages/' . $this->elevage->id,
                    'action' => 'created',
                ];
            
            case 'updated':
                $changes = $this->additionalData['changes'] ?? [];
                $changeMessage = $this->formatChanges($changes);
                
                return [
                    'title' => 'Élevage mis à jour 📝',
                    'message' => "L'élevage '{$this->elevage->nom}' a été modifié." . ($changeMessage ? " {$changeMessage}" : ''),
                    'type' => 'info',
                    'icon' => 'fa-edit',
                    'url' => '/elevages/' . $this->elevage->id,
                    'action' => 'updated',
                ];
            
            case 'deleted':
                return [
                    'title' => 'Élevage supprimé 🗑️',
                    'message' => "L'élevage '{$this->elevage->nom}' a été supprimé.",
                    'type' => 'warning',
                    'icon' => 'fa-trash',
                    'url' => '/elevages',
                    'action' => 'deleted',
                ];
            
            default:
                return [
                    'title' => 'Information élevage',
                    'message' => "L'élevage '{$this->elevage->nom}' a été modifié.",
                    'type' => 'info',
                    'icon' => 'fa-bell',
                    'url' => '/elevages/' . $this->elevage->id,
                    'action' => 'info',
                ];
        }
    }

    /**
     * Format les changements pour l'affichage
     */
    protected function formatChanges(array $changes): string
    {
        if (empty($changes)) return '';
        
        $formatted = [];
        $fieldLabels = [
            'nom' => 'Nom',
            'type_elevage' => 'Type',
            'localisation' => 'Localisation',
            'superficie' => 'Superficie',
            'description' => 'Description',
            'statut' => 'Statut'
        ];
        
        foreach ($changes as $field => $values) {
            $label = $fieldLabels[$field] ?? $field;
            $old = $values['old'] ?? 'null';
            $new = $values['new'] ?? 'null';
            $formatted[] = "{$label}: '{$old}' → '{$new}'";
        }
        
        return implode(', ', $formatted);
    }

    /**
     * Retourne le texte de l'action
     */
    protected function getActionText(): string
    {
        switch ($this->action) {
            case 'created':
                return 'Voir mon nouvel élevage';
            case 'updated':
                return 'Voir les modifications';
            case 'deleted':
                return 'Voir mes élevages';
            default:
                return 'Voir l\'élevage';
        }
    }
}
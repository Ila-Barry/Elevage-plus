<?php
// app/Notifications/TacheNotification.php

namespace App\Notifications;

use App\Models\Tache;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;
use Illuminate\Support\Facades\Log;

class TacheNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Tache $tache;
    protected string $type; // 'created', 'updated', 'deleted', 'reminder', 'completed'
    protected array $additionalData;

    /**
     * Create a new notification instance.
     */
    public function __construct(Tache $tache, string $type, array $additionalData = [])
    {
        $this->tache = $tache;
        $this->type = $type;
        $this->additionalData = $additionalData;
        
        Log::info('🔔 Notification Tâche créée', [
            'tache_id' => $tache->id,
            'type' => $type,
            'user_id' => $tache->user_id
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
        
        Log::info('💾 Notification Tâche sauvegardée en base', [
            'user_id' => $notifiable->id,
            'type' => $data['type'],
            'tache_id' => $this->tache->id
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
            ->action('Voir la tâche', 'view_task')
            ->data([
                'id' => $notification->id,
                'url' => $data['url'],
                'tache_id' => $this->tache->id
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
            ->line('Détails de la tâche :')
            ->line('- Titre : ' . $this->tache->titre)
            ->line('- Type : ' . $this->tache->type_label)
            ->line('- Date planifiée : ' . $this->tache->date_planifiee->format('d/m/Y à H:i'))
            ->line('- Priorité : ' . $this->tache->priorite_label)
            ->action('Voir la tâche', url($data['url']))
            ->line('Merci d\'utiliser la plateforme Élevage+ !')
            ->salutation('Cordialement, L\'équipe Élevage+');
    }

    /**
     * Get the notification data.
     */
    protected function getNotificationData(): array
    {
        $tacheNom = $this->tache->titre;
        $typeLabel = $this->tache->type_label;
        $datePlanifiee = $this->tache->date_planifiee->format('d/m/Y à H:i');
        $animalNom = $this->tache->animal?->nom ?? 'Non spécifié';
        
        switch ($this->type) {
            case 'created':
                return [
                    'title' => '📋 Nouvelle tâche créée',
                    'message' => "La tâche '{$tacheNom}' ({$typeLabel}) a été planifiée pour le {$datePlanifiee}.",
                    'type' => 'success',
                    'icon' => 'fa-tasks',
                    'url' => '/taches/' . $this->tache->id,
                ];
            
            case 'updated':
                $changes = $this->additionalData['changes'] ?? [];
                $changeMessage = $this->formatChanges($changes);
                
                return [
                    'title' => '📝 Tâche modifiée',
                    'message' => "La tâche '{$tacheNom}' a été modifiée." . ($changeMessage ? " {$changeMessage}" : ''),
                    'type' => 'info',
                    'icon' => 'fa-edit',
                    'url' => '/taches/' . $this->tache->id,
                ];
            
            case 'deleted':
                return [
                    'title' => '🗑️ Tâche supprimée',
                    'message' => "La tâche '{$tacheNom}' a été supprimée.",
                    'type' => 'warning',
                    'icon' => 'fa-trash',
                    'url' => '/taches',
                ];
            
            case 'completed':
                return [
                    'title' => '✅ Tâche terminée',
                    'message' => "La tâche '{$tacheNom}' a été marquée comme terminée.",
                    'type' => 'success',
                    'icon' => 'fa-check-circle',
                    'url' => '/taches/' . $this->tache->id,
                ];
            
            case 'reminder':
                $reminderType = $this->additionalData['reminder_type'] ?? 'info';
                $reminderMessage = $this->additionalData['message'] ?? '';
                
                $reminderLabels = [
                    '72h' => '🚨 RAPPEL IMPORTANT (72h)',
                    '48h' => '🔔 RAPPEL (48h)',
                    '24h' => '🔔 RAPPEL (24h)',
                    '1h' => '🔴 RAPPEL URGENT (1h)',
                    '30min' => '🔴 RAPPEL URGENT (30min)',
                    'now' => '⏰ TÂCHE IMMINENTE',
                    'retard' => '⚠️ TÂCHE EN RETARD',
                ];
                
                $title = $reminderLabels[$reminderType] ?? '🔔 Rappel de tâche';
                
                return [
                    'title' => $title,
                    'message' => $reminderMessage ?: "Rappel pour la tâche '{$tacheNom}' planifiée le {$datePlanifiee}.",
                    'type' => $reminderType === 'retard' ? 'danger' : 'warning',
                    'icon' => 'fa-bell',
                    'url' => '/taches/' . $this->tache->id,
                ];
            
            default:
                return [
                    'title' => 'Information tâche',
                    'message' => "La tâche '{$tacheNom}' a été modifiée.",
                    'type' => 'info',
                    'icon' => 'fa-bell',
                    'url' => '/taches/' . $this->tache->id,
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
            'titre' => 'Titre',
            'type' => 'Type',
            'date_planifiee' => 'Date',
            'priorite' => 'Priorité',
            'description' => 'Description',
        ];
        
        foreach ($changes as $field => $values) {
            $label = $fieldLabels[$field] ?? $field;
            $old = $values['old'] ?? 'null';
            $new = $values['new'] ?? 'null';
            
            // Formatage spécial pour les dates
            if ($field === 'date_planifiee') {
                $old = $old ? date('d/m/Y H:i', strtotime($old)) : 'null';
                $new = $new ? date('d/m/Y H:i', strtotime($new)) : 'null';
            }
            
            // Formatage pour les types
            if ($field === 'type') {
                $old = \App\Models\Tache::TYPES[$old] ?? $old;
                $new = \App\Models\Tache::TYPES[$new] ?? $new;
            }
            
            // Formatage pour les priorités
            if ($field === 'priorite') {
                $old = \App\Models\Tache::PRIORITES[$old] ?? $old;
                $new = \App\Models\Tache::PRIORITES[$new] ?? $new;
            }
            
            $formatted[] = "{$label}: '{$old}' → '{$new}'";
        }
        
        return implode(', ', $formatted);
    }
}
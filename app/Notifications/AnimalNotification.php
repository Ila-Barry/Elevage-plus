<?php
// app/Notifications/AnimalNotification.php

namespace App\Notifications;

use App\Models\Animal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;
use Illuminate\Support\Facades\Log;

class AnimalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Animal $animal;
    protected string $action;
    protected array $additionalData;

    /**
     * Create a new notification instance.
     *
     * @param Animal $animal
     * @param string $action (created, updated, deleted, weight_alert, health_alert)
     * @param array $additionalData
     */
    public function __construct(Animal $animal, string $action, array $additionalData = [])
    {
        $this->animal = $animal;
        $this->action = $action;
        $this->additionalData = $additionalData;
        
        Log::info('🔔 Notification Animal créée', [
            'animal_id' => $animal->id,
            'action' => $action,
            'user_id' => $animal->elevage?->user_id
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
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        $data = $this->getNotificationData();
        
        Log::info('💾 Notification Animal sauvegardée en base', [
            'user_id' => $notifiable->id,
            'type' => $data['type'],
            'animal_id' => $this->animal->id
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
        
        return (new WebPushMessage)
            ->title($data['title'])
            ->icon('/images/logo-elevage-plus.png')
            ->body($data['message'])
            ->action('Voir l\'animal', 'view_animal')
            ->data([
                'id' => $notification->id,
                'url' => $data['url'],
                'animal_id' => $this->animal->id
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
        
        return (new MailMessage)
            ->subject($data['title'])
            ->greeting('Bonjour ' . $notifiable->name . ' !')
            ->line($data['message'])
            ->line('Détails de l\'animal :')
            ->line('- Nom : ' . $this->animal->nom)
            ->line('- Espèce : ' . $this->animal->espece_label)
            ->line('- Race : ' . ($this->animal->race ?? 'Non renseignée'))
            ->line('- Poids : ' . $this->animal->poids . ' kg')
            ->action('Voir l\'animal', url($data['url']))
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
        $animalName = $this->animal->nom;
        $elevageName = $this->animal->elevage?->nom ?? 'Élevage';
        
        switch ($this->action) {
            case 'created':
                return [
                    'title' => 'Nouvel animal ajouté 🐾',
                    'message' => "L'animal '{$animalName}' a été ajouté à l'élevage '{$elevageName}'.",
                    'type' => 'success',
                    'icon' => 'fa-paw',
                    'url' => '/animaux/' . $this->animal->id,
                    'action' => 'created',
                ];
            
            case 'updated':
                $changes = $this->additionalData['changes'] ?? [];
                $changeMessage = $this->formatChanges($changes);
                
                return [
                    'title' => 'Animal modifié 📝',
                    'message' => "L'animal '{$animalName}' a été modifié." . ($changeMessage ? " {$changeMessage}" : ''),
                    'type' => 'info',
                    'icon' => 'fa-edit',
                    'url' => '/animaux/' . $this->animal->id,
                    'action' => 'updated',
                ];
            
            case 'deleted':
                return [
                    'title' => 'Animal supprimé 🗑️',
                    'message' => "L'animal '{$animalName}' a été supprimé de l'élevage '{$elevageName}'.",
                    'type' => 'warning',
                    'icon' => 'fa-trash',
                    'url' => '/animaux',
                    'action' => 'deleted',
                ];
            
            case 'weight_alert':
                $oldWeight = $this->additionalData['old_weight'] ?? 0;
                $newWeight = $this->additionalData['new_weight'] ?? $this->animal->poids;
                $loss = round((($oldWeight - $newWeight) / $oldWeight) * 100, 1);
                
                return [
                    'title' => '⚠️ Alerte perte de poids',
                    'message' => "L'animal '{$animalName}' a perdu {$loss}% de son poids en 15 jours. ({$oldWeight}kg → {$newWeight}kg)",
                    'type' => 'warning',
                    'icon' => 'fa-exclamation-triangle',
                    'url' => '/animaux/' . $this->animal->id,
                    'action' => 'weight_alert',
                ];
            
            case 'health_alert':
                $status = $this->additionalData['status'] ?? $this->animal->statut_sanitaire;
                $statusLabel = Animal::STATUTS_SANITAIRES[$status] ?? $status;
                
                return [
                    'title' => '🚨 Alerte sanitaire',
                    'message' => "L'animal '{$animalName}' est en état {$statusLabel}. Une attention immédiate est requise !",
                    'type' => 'danger',
                    'icon' => 'fa-heartbeat',
                    'url' => '/animaux/' . $this->animal->id,
                    'action' => 'health_alert',
                ];
            
            case 'death':
                return [
                    'title' => '💔 Animal décédé',
                    'message' => "L'animal '{$animalName}' est décédé. Motif : " . ($this->additionalData['motif'] ?? 'Non spécifié'),
                    'type' => 'danger',
                    'icon' => 'fa-skull',
                    'url' => '/animaux',
                    'action' => 'death',
                ];
            
            default:
                return [
                    'title' => 'Information animal',
                    'message' => "L'animal '{$animalName}' a été modifié.",
                    'type' => 'info',
                    'icon' => 'fa-bell',
                    'url' => '/animaux/' . $this->animal->id,
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
            'espece' => 'Espèce',
            'race' => 'Race',
            'poids' => 'Poids',
            'statut_sanitaire' => 'Statut sanitaire',
            'sexe' => 'Sexe',
            'statut' => 'Statut'
        ];
        
        foreach ($changes as $field => $values) {
            $label = $fieldLabels[$field] ?? $field;
            $old = $values['old'] ?? 'null';
            $new = $values['new'] ?? 'null';
            
            // Pour le poids, ajouter kg
            if ($field === 'poids') {
                $old = $old . ' kg';
                $new = $new . ' kg';
            }
            
            // Pour les statuts, utiliser les labels
            if ($field === 'statut_sanitaire') {
                $old = Animal::STATUTS_SANITAIRES[$old] ?? $old;
                $new = Animal::STATUTS_SANITAIRES[$new] ?? $new;
            }
            
            $formatted[] = "{$label}: '{$old}' → '{$new}'";
        }
        
        return implode(', ', $formatted);
    }
}
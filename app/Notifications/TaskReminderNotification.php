<?php

// ici on gere les rappels de taches, on declenche cet event lorsqu'une tache a besoin d'un rappel, ensuite on a un listener qui ecoute cet event et qui envoie la notification de rappel a l'utilisateur concerné.
// app/Notifications/TaskReminderNotification.php

namespace App\Notifications;

use App\Models\Tache;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;

class TaskReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Tache $tache;
    protected string $reminderType;
    protected string $message;

    public function __construct(Tache $tache, string $reminderType, string $message)
    {
        $this->tache = $tache;
        $this->reminderType = $reminderType;
        $this->message = $message;
    }

    public function via($notifiable): array
    {
        $channels = ['database'];
        
        if ($notifiable->email_notifications) {
            $channels[] = 'mail';
        }
        
        if ($notifiable->web_notifications) {
            $channels[] = 'webpush';
        }
        
        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject($this->getSubject())
            ->greeting('Bonjour ' . $notifiable->name . ' !')
            ->line($this->message);
        
        // Ajouter des actions différentes selon le type de rappel
        if ($this->reminderType === 'retard') {
            $message->action('Marquer comme terminée', url("/tasks/{$this->tache->id}/complete"))
                    ->line('Si vous avez déjà effectué cette tâche, merci de la marquer comme terminée.');
        } else {
            $message->action('Voir la tâche', url("/tasks/{$this->tache->id}"));
        }
        
        return $message;
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'task_reminder',
            'reminder_type' => $this->reminderType,
            'tache_id' => $this->tache->id,
            'titre' => $this->tache->titre,
            'message' => $this->message,
            'date_planifiee' => $this->tache->date_planifiee->format('Y-m-d H:i:s'),
            'url' => "/tasks/{$this->tache->id}",
        ];
    }

    public function toWebPush($notifiable, $notification)
    {
        $action = $this->reminderType === 'retard' 
            ? ['action' => 'complete', 'title' => 'Marquer terminée']
            : ['action' => 'view', 'title' => 'Voir'];
        
        return (new WebPushMessage)
            ->title($this->getSubject())
            ->icon('/images/icon-512x512.png')
            ->badge('/images/badge-icon.png')
            ->body($this->message)
            ->data(['url' => "/tasks/{$this->tache->id}"])
            ->vibrate([200, 100, 200])
            ->actions([
                $action,
                ['action' => 'dismiss', 'title' => 'Ignorer'],
            ]);
    }

    private function getSubject(): string
    {
        return match($this->reminderType) {
            '48h' => '🔔 Rappel tâche (après-demain)',
            '24h' => '🔔 Rappel tâche (demain)',
            '1h' => '🔔 Rappel urgent (aujourd\'hui)',
            'retard' => '⚠️ Tâche en retard',
            default => 'Rappel de tâche',
        };
    }
}
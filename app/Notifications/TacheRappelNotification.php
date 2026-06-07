<?php
// app/Notifications/TacheRappelNotification.php

namespace App\Notifications;

use App\Models\Tache;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class TacheRappelNotification extends Notification
{
    use Queueable;

    protected Tache $tache;
    protected string $typeRappel;

    public function __construct(Tache $tache, string $typeRappel)
    {
        $this->tache = $tache;
        $this->typeRappel = $typeRappel;
    }

    public function via($notifiable): array
    {
        $channels = ['database'];
        
        if ($notifiable->email_notifications) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        $entite = $this->tache->estPourElevage 
            ? "l'élevage {$this->tache->elevage->nom}"
            : "l'animal {$this->tache->animal->nom}";
        
        $quand = match($this->typeRappel) {
            '48h' => 'dans 48 heures',
            '24h' => 'demain',
            '1h' => 'dans 1 heure',
            '30min' => 'dans 30 minutes',
            'now' => "aujourd'hui",
            default => "le {$this->tache->date_planifiee->format('d/m/Y')}",
        };
        
        return (new MailMessage)
            ->subject("🔔 Rappel: {$this->tache->titre}")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Ceci est un rappel pour la tâche suivante:")
            ->line("**{$this->tache->titre}**")
            ->line("- Type: {$this->tache->type_label}")
            ->line("- Concerné: {$entite}")
            ->line("- À faire: {$quand}")
            ->when($this->tache->description, fn($msg) => $msg->line("- Description: {$this->tache->description}"))
            ->action('Voir la tâche', url("/tasks/{$this->tache->id}"))
            ->line("Merci d'utiliser Élevage+ !");
    }

    public function toDatabase($notifiable): DatabaseMessage
    {
        $entite = $this->tache->estPourElevage 
            ? "l'élevage {$this->tache->elevage->nom}"
            : "l'animal {$this->tache->animal->nom}";
        
        $quand = match($this->typeRappel) {
            '48h' => 'dans 48 heures',
            '24h' => 'demain',
            '1h' => 'dans 1 heure',
            '30min' => 'dans 30 minutes',
            'now' => "aujourd'hui",
            default => "le {$this->tache->date_planifiee->format('d/m/Y')}",
        };
        
        return new DatabaseMessage([
            'title' => "Rappel: {$this->tache->titre}",
            'message' => "La tâche '{$this->tache->titre}' est prévue pour {$entite} {$quand}.",
            'type' => 'rappel_tache',
            'tache_id' => $this->tache->id,
            'tache_titre' => $this->tache->titre,
            'date_planifiee' => $this->tache->date_planifiee->format('Y-m-d'),
        ]);
    }

    public function toArray($notifiable): array
    {
        return [
            'tache_id' => $this->tache->id,
            'tache_titre' => $this->tache->titre,
            'type_rappel' => $this->typeRappel,
            'date_planifiee' => $this->tache->date_planifiee->format('Y-m-d'),
        ];
    }
}
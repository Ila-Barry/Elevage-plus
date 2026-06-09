<?php
// app/Events/MessageSent.php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event envoyé quand un message est envoyé
 * Utilisé pour les notifications en temps réel via Pusher
 */
class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Le message envoyé
     *
     * @var Message
     */
    public Message $message;

    /**
     * Crée une nouvelle instance de l'event.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Le canal sur lequel l'event est broadcasté.
     */
    public function broadcastOn(): Channel
    {
        // Canal privé pour le destinataire et l'expéditeur
        return new PresenceChannel("conversation.{$this->message->conversation_id}");
    }

    /**
     * Le nom de l'event broadcasté.
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    /**
     * Les données à broadcaster.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'expediteur_id' => $this->message->expediteur_id,
            'destinataire_id' => $this->message->destinataire_id,
            'contenu' => $this->message->contenu,
            'lu' => $this->message->lu,
            'created_at' => $this->message->created_at->toISOString(),
        ];
    }
}
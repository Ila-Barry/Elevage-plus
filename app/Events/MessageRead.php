<?php
// app/Events/MessageRead.php

namespace App\Events;

use App\Models\Conversation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event envoyé quand un message est marqué comme lu
 */
class MessageRead implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * La conversation concernée
     *
     * @var Conversation
     */
    public Conversation $conversation;

    /**
     * L'ID de l'utilisateur qui a lu les messages
     *
     * @var int
     */
    public int $userId;

    /**
     * Crée une nouvelle instance de l'event.
     */
    public function __construct(Conversation $conversation, int $userId)
    {
        $this->conversation = $conversation;
        $this->userId = $userId;
    }

    /**
     * Le canal sur lequel l'event est broadcasté.
     */
    public function broadcastOn(): Channel
    {
        return new PresenceChannel("conversation.{$this->conversation->id}");
    }

    /**
     * Le nom de l'event broadcasté.
     */
    public function broadcastAs(): string
    {
        return 'message.read';
    }

    /**
     * Les données à broadcaster.
     */
    public function broadcastWith(): array
    {
        return [
            'conversation_id' => $this->conversation->id,
            'user_id' => $this->userId,
            'read_at' => now()->toISOString(),
        ];
    }
}
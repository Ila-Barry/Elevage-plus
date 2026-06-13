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

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadcastOn(): Channel
    {
        return new PresenceChannel("conversation.{$this->message->conversation_id}");
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'id'               => $this->message->id,
            'conversation_id'  => $this->message->conversation_id,
            'expediteur_id'    => $this->message->expediteur_id,
            'destinataire_id'  => $this->message->destinataire_id,
            'contenu'          => $this->message->contenu,
            'lu'               => $this->message->lu,
            'created_at'       => $this->message->created_at->toISOString(),
        ];
    }
}
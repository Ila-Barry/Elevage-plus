<?php
// notification de nouveau message, déclenchée par l'envoi d'un message dans une conversation
namespace App\Notifications;

use App\Models\User;
use App\Models\Message;

class NewMessageNotification extends BaseNotification
{
    protected User $expediteur;
    protected Message $message;

    public function __construct(User $expediteur, Message $message)
    {
        $this->expediteur = $expediteur;
        $this->message = $message;
        
        $this->title = '💬 Nouveau message';
        $this->message = "{$expediteur->name} vous a envoyé un message : " . 
                         substr($message->contenu, 0, 50) . (strlen($message->contenu) > 50 ? '...' : '');
        $this->type = 'info';
        $this->url = "/messages?conversation={$message->conversation_id}";
    }
}
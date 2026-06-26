<?php
// app/Notifications/NewMessageNotification.php

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
                         substr($message->contenu, 0, 60) . (strlen($message->contenu) > 60 ? '...' : '');
        $this->type = 'info';
        $this->url = "/messages?conversation={$message->conversation_id}";
        
        $this->actions = [
            [
                'label' => 'Voir le message',
                'url' => "/messages?conversation={$message->conversation_id}",
                'type' => 'primary'
            ],
            [
                'label' => 'Répondre',
                'url' => "/messages?conversation={$message->conversation_id}",
                'type' => 'secondary'
            ]
        ];
    }

    protected function getAdditionalInfo(): string
    {
        $message = "📎 Type de message : ";
        $message .= match($this->message->type) {
            'text' => 'Texte',
            'image' => 'Image',
            'video' => 'Vidéo',
            'file' => 'Fichier',
            'sticker' => 'Sticker',
            default => 'Inconnu',
        };
        return $message;
    }
}
<?php
// app/Notifications/NewCommentNotification.php

namespace App\Notifications;

use App\Models\User;
use App\Models\Commentaire;
use App\Models\Publication;

class NewCommentNotification extends BaseNotification
{
    protected User $auteur;
    protected Commentaire $commentaire;
    protected Publication $publication;

    public function __construct(User $auteur, Commentaire $commentaire, Publication $publication)
    {
        $this->auteur = $auteur;
        $this->commentaire = $commentaire;
        $this->publication = $publication;
        
        $this->title = '💬 Nouveau commentaire';
        $this->message = "{$auteur->name} a commenté votre publication '{$publication->titre}' : " .
                         substr($commentaire->contenu, 0, 60) . (strlen($commentaire->contenu) > 60 ? '...' : '');
        $this->type = 'info';
        $this->url = "/publications/{$publication->id}#comment-{$commentaire->id}";
        
        $this->actions = [
            [
                'label' => 'Voir le commentaire',
                'url' => "/publications/{$publication->id}#comment-{$commentaire->id}",
                'type' => 'primary'
            ],
            [
                'label' => 'Répondre',
                'url' => "/publications/{$publication->id}#reply-{$commentaire->id}",
                'type' => 'secondary'
            ]
        ];
    }
}
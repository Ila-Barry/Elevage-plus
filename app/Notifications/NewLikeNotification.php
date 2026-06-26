<?php
// app/Notifications/NewLikeNotification.php

namespace App\Notifications;

use App\Models\User;
use App\Models\Publication;

class NewLikeNotification extends BaseNotification
{
    protected User $auteur;
    protected Publication $publication;

    public function __construct(User $auteur, Publication $publication)
    {
        $this->auteur = $auteur;
        $this->publication = $publication;
        
        $this->title = '❤️ Nouveau like';
        $this->message = "{$auteur->name} a aimé votre publication '{$publication->titre}'";
        $this->type = 'success';
        $this->url = "/publications/{$publication->id}";
        
        $this->actions = [
            [
                'label' => 'Voir la publication',
                'url' => "/publications/{$publication->id}",
                'type' => 'primary'
            ],
            [
                'label' => 'Voir le profil',
                'url' => "/profilEleveur/{$auteur->id}",
                'type' => 'secondary'
            ]
        ];
    }
}
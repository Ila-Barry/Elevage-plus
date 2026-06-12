<?php
// notification de nouveau like, déclenchée par l'ajout d'un like à une publication

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
    }
}
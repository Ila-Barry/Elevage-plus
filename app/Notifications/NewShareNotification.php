<?php
// app/Notifications/NewShareNotification.php

namespace App\Notifications;

use App\Models\User;
use App\Models\Publication;

class NewShareNotification extends BaseNotification
{
    protected User $auteur;
    protected Publication $publication;
    protected string $plateforme;

    public function __construct(User $auteur, Publication $publication, string $plateforme)
    {
        $this->auteur = $auteur;
        $this->publication = $publication;
        $this->plateforme = $plateforme;
        
        $plateformes = [
            'facebook' => 'Facebook',
            'twitter' => 'Twitter',
            'whatsapp' => 'WhatsApp',
            'copie_lien' => 'copie du lien',
            'linkedin' => 'LinkedIn',
        ];
        
        $this->title = '🔄 Nouveau partage';
        $this->message = "{$auteur->name} a partagé votre publication '{$publication->titre}' sur " .
                         ($plateformes[$plateforme] ?? $plateforme);
        $this->type = 'info';
        $this->url = "/publications/{$publication->id}";
        
        $this->actions = [
            [
                'label' => 'Voir la publication',
                'url' => "/publications/{$publication->id}",
                'type' => 'primary'
            ]
        ];
    }
}
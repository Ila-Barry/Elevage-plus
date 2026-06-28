<?php
// notification de nouveau partage, déclenchée par le partage d'une publication sur une plateforme

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
        ];
        
        $this->title = '🔄 Nouveau partage';
        $this->message = "{$auteur->name} a partagé votre publication '{$publication->titre}' sur " .
                         ($plateformes[$plateforme] ?? $plateforme);
        $this->type = 'info';
        $this->url = "/publications/{$publication->id}";
    }
}
<?php
// notification de publication signalée, déclenchée par le signalement d'une publication

namespace App\Notifications;

use App\Models\User;
use App\Models\Publication;

class PublicationReportedNotification extends BaseNotification
{
    protected User $signaleur;
    protected Publication $publication;
    protected string $motif;

    public function __construct(User $signaleur, Publication $publication, string $motif)
    {
        $this->signaleur = $signaleur;
        $this->publication = $publication;
        $this->motif = $motif;
        
        $this->title = '⚠️ Publication signalée';
        $this->message = "La publication '{$publication->titre}' a été signalée par {$signaleur->name} " .
                         "pour le motif : {$motif}";
        $this->type = 'danger';
        $this->url = "/admin/publications/{$publication->id}";
    }
}
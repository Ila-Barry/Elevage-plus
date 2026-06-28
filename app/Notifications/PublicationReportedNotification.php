<?php
// app/Notifications/PublicationReportedNotification.php

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
        
        $this->actions = [
            [
                'label' => 'Examiner la publication',
                'url' => "/admin/publications/{$publication->id}",
                'type' => 'danger'
            ],
            [
                'label' => 'Voir le signalement',
                'url' => "/admin/reports/{$publication->id}",
                'type' => 'primary'
            ]
        ];
    }
}
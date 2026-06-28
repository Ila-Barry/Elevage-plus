<?php
// notification de signalement résolu, déclenchée par la résolution d'un signalement concernant une publication

namespace App\Notifications;

use App\Models\Publication;

class ReportResolvedNotification extends BaseNotification
{
    protected Publication $publication;
    protected string $action;

    public function __construct(Publication $publication, string $action)
    {
        $this->publication = $publication;
        $this->action = $action;
        
        $actions = [
            'blocked' => 'bloquée',
            'unblocked' => 'débloquée',
            'ignored' => 'ignorée',
        ];
        
        $this->title = '✅ Signalement traité';
        $this->message = "Le signalement concernant votre publication '{$publication->titre}' a été examiné " .
                         "et la publication a été {$actions[$action]} par l'équipe de modération.";
        $this->type = 'info';
        $this->url = "/publications/{$publication->id}";
    }
}
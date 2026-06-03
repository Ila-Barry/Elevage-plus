<?php
// app/Events/PublicationViewed.php

namespace App\Events;

use App\Models\Publication;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event PublicationViewed
 * 
 * Déclenché lorsqu'une publication est consultée
 */
class PublicationViewed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * La publication consultée
     *
     * @var Publication
     */
    public Publication $publication;

    /**
     * Créer une nouvelle instance de l'event.
     */
    public function __construct(Publication $publication)
    {
        $this->publication = $publication;
    }
}
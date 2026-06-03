<?php
// app/Listeners/IncrementPublicationViews.php

namespace App\Listeners;

use App\Events\PublicationViewed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Listener IncrementPublicationViews
 * 
 * Incrémente le compteur de vues lorsqu'une publication est consultée
 */
class IncrementPublicationViews implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(PublicationViewed $event): void
    {
        // Éviter les doublons de vues par session
        if (!session()->has('viewed_publication_' . $event->publication->id)) {
            session()->put('viewed_publication_' . $event->publication->id, true);
            $event->publication->incrementViews();
        }
    }
}
<?php
// app/Listeners/SendStockCritiqueAlert.php

namespace App\Listeners;

use App\Events\StockCritique;
use App\Notifications\StockCritiqueNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Listener SendStockCritiqueAlert
 * 
 * Envoie une notification lorsque le stock est critique
 */
class SendStockCritiqueAlert implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(StockCritique $event): void
    {
        $elevage = $event->produit->elevage;
        $user = $elevage->user;
        
        // Envoyer la notification à l'éleveur
        $user->notify(new StockCritiqueNotification($event->produit));
    }
}
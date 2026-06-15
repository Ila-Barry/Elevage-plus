<?php
// app/Events/StockCritique.php

namespace App\Events;

use App\Models\Produit;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event StockCritique
 * 
 * Déclenché lorsqu'un produit atteint un seuil critique
 */
class StockCritique
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Le produit en stock critique
     *
     * @var Produit
     */
    public Produit $produit;

    /**
     * Créer une nouvelle instance de l'event.
     */
    public function __construct(Produit $produit)
    {
        $this->produit = $produit;
    }
}
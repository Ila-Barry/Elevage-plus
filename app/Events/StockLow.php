<?php
// app/Events/StockLow.php

namespace App\Events;

use App\Models\Produit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockLow
{
    use Dispatchable, SerializesModels;

    public Produit $produit;

    public function __construct(Produit $produit)
    {
        $this->produit = $produit;
    }
}
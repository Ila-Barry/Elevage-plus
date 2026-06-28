<?php
// app/Notifications/StockCritiqueNotification.php

namespace App\Notifications;

use App\Models\Produit;

class StockCritiqueNotification extends BaseNotification
{
    protected Produit $produit;

    public function __construct(Produit $produit)
    {
        $this->produit = $produit;
        
        $this->title = '📦 Stock critique';
        $this->message = "Le produit '{$produit->nom}' a atteint un niveau critique. " .
                         "Stock actuel : {$produit->quantite} {$produit->unite}";
        $this->type = 'danger';
        $this->url = "/stock/produits/{$produit->id}";
        
        $this->actions = [
            [
                'label' => 'Voir le stock',
                'url' => "/stock/produits/{$produit->id}",
                'type' => 'danger'
            ],
            [
                'label' => 'Réapprovisionner',
                'url' => "/stock/produits/{$produit->id}/reapprovisionner",
                'type' => 'primary'
            ]
        ];
    }

    protected function getAdditionalInfo(): string
    {
        return "Seuil d'alerte : {$this->produit->seuil_alerte} {$this->produit->unite}";
    }
}
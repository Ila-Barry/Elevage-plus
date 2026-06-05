<?php
// app/Http/Resources/StockReportResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource StockReportResource
 * 
 * Formate la réponse API pour les rapports de stock
 */
class StockReportResource extends JsonResource
{
    /**
     * Transforme le resource en tableau.
     */
    public function toArray(Request $request): array
    {
        return [
            'resume' => [
                'total_produits' => $this['total_produits'],
                'valeur_totale_stock' => (float) $this['valeur_totale_stock'],
                'quantite_totale' => (float) $this['quantite_totale'],
                'produits_critiques' => $this['produits_critiques'],
                'produits_rupture' => $this['produits_rupture'],
                'produits_expires' => $this['produits_expires'],
            ],
            'par_categorie' => $this['par_categorie'],
            'par_statut' => $this['par_statut'],
            'mouvements_mois' => [
                'entrees' => (float) $this['total_entrees_mois'],
                'sorties' => (float) $this['total_sorties_mois'],
            ],
        ];
    }
}
<?php
// app/Http/Resources/MouvementStockResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MouvementStockResource extends JsonResource
{
    /**
     * Transforme le resource en tableau.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'type_icone' => $this->type === 'entree' ? '📥' : '📤',
            'type_couleur' => $this->type === 'entree' ? 'success' : 'warning',
            'quantite' => (float) $this->quantite,
            'quantite_avant' => (float) $this->quantite_avant,
            'quantite_apres' => (float) $this->quantite_apres,
            'motif' => $this->motif,
            'motif_label' => $this->getMotifLabel(),
            'description' => $this->description,
            'reference_facture' => $this->reference_facture,
            'fournisseur' => $this->fournisseur,
            'destinataire' => $this->destinataire,
            
            // ========== LA CORRECTION EST ICI ==========
            'produit_id' => $this->produit_id,
            'produit' => $this->produit ? [
                'id' => $this->produit->id,
                'nom' => $this->produit->nom,
                'unite' => $this->produit->unite,
                'categorie' => $this->produit->categorie,
            ] : null,
            // ============================================

            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ] : null,
            'date_mouvement' => $this->date_mouvement?->format('Y-m-d H:i:s'),
            'date_mouvement_human' => $this->date_mouvement?->diffForHumans(),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Récupère le libellé du motif
     */
    protected function getMotifLabel(): string
    {
        $motifsEntree = [
            'achat' => 'Achat',
            'don' => 'Don',
            'production' => 'Production propre',
            'retour' => 'Retour',
            'inventaire' => 'Ajustement inventaire',
            'autre' => 'Autre',
        ];
        
        $motifsSortie = [
            'consommation' => 'Consommation animale',
            'vente' => 'Vente',
            'perte' => 'Perte',
            'casse' => 'Casse',
            'inventaire' => 'Ajustement inventaire',
            'autre' => 'Autre',
        ];
        
        $motifs = $this->type === 'entree' ? $motifsEntree : $motifsSortie;
        
        return $motifs[$this->motif] ?? $this->motif;
    }
}
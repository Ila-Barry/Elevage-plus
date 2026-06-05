<?php
// app/Http/Resources/ProduitResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProduitResource extends JsonResource
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
            'nom' => $this->nom,
            'categorie' => $this->categorie,
            'categorie_label' => $this->getCategorieLabel(),
            'quantite' => (float) $this->quantite,
            'seuil_alerte' => (float) $this->seuil_alerte,
            'unite' => $this->unite,
            'unite_label' => $this->getUniteLabel(),
            'statut' => $this->statut,
            'statut_label' => $this->getStatutLabel(),
            'is_critique' => $this->isStockCritique(),
            'is_rupture' => $this->quantite <= 0,
            'pourcentage_stock' => $this->calculerPourcentageStock(),
            'fournisseur' => $this->fournisseur,
            'description' => $this->description,
            'code_barre' => $this->code_barre,
            'photo_url' => $this->getPhotoUrl(),
            'prix_unitaire' => $this->prix_unitaire ? (float) $this->prix_unitaire : null,
            'valeur_totale' => (float) $this->getValeurTotale(),
            'date_expiration' => $this->date_expiration?->format('Y-m-d'),
            'date_expiration_relative' => $this->date_expiration?->diffForHumans(),
            'emplacement_stockage' => $this->emplacement_stockage,
            'derniere_commande' => $this->derniere_commande?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'statistiques' => [
                'total_entrees' => (float) $this->entrees()->sum('quantite'),
                'total_sorties' => (float) $this->sorties()->sum('quantite'),
                'dernier_mouvement' => $this->mouvements()->latest()->first()?->date_mouvement?->format('Y-m-d H:i:s'),
            ],
        ];
    }

    /**
     * Récupère le libellé de la catégorie
     */
    protected function getCategorieLabel(): string
    {
        $categories = [
            'aliment' => 'Aliment',
            'medicament' => 'Médicament',
            'equipement' => 'Équipement',
            'vaccin' => 'Vaccin',
            'accessoire' => 'Accessoire',
            'autre' => 'Autre',
        ];
        
        return $categories[$this->categorie] ?? $this->categorie;
    }

    /**
     * Récupère le libellé de l'unité
     */
    protected function getUniteLabel(): string
    {
        $unites = [
            'kg' => 'Kilogramme',
            'g' => 'Gramme',
            'l' => 'Litre',
            'ml' => 'Millilitre',
            'piece' => 'Pièce',
            'boite' => 'Boîte',
            'sac' => 'Sac',
            'bouteille' => 'Bouteille',
            'unite' => 'Unité',
        ];
        
        return $unites[$this->unite] ?? $this->unite;
    }

    /**
     * Récupère le libellé du statut
     */
    protected function getStatutLabel(): string
    {
        $statuts = [
            'actif' => 'Actif',
            'inactif' => 'Inactif',
            'rupture' => 'En rupture',
        ];
        
        return $statuts[$this->statut] ?? $this->statut;
    }

    /**
     * Vérifie si le stock est critique
     */
    protected function isStockCritique(): bool
    {
        return $this->quantite <= $this->seuil_alerte && $this->seuil_alerte > 0;
    }

    /**
     * Calcule le pourcentage de stock
     */
    protected function calculerPourcentageStock(): ?float
    {
        if ($this->seuil_alerte <= 0) {
            return null;
        }
        
        $pourcentage = ($this->quantite / $this->seuil_alerte) * 100;
        return round(min($pourcentage, 100), 2);
    }

    /**
     * Récupère l'URL complète de la photo
     */
    protected function getPhotoUrl(): ?string
    {
        if (!$this->photo_url) {
            return null;
        }
        
        if (filter_var($this->photo_url, FILTER_VALIDATE_URL)) {
            return $this->photo_url;
        }
        
        return asset('storage/' . $this->photo_url);
    }

    /**
     * Calcule la valeur totale du stock
     */
    protected function getValeurTotale(): float
    {
        if ($this->prix_unitaire) {
            return $this->quantite * $this->prix_unitaire;
        }
        
        return $this->prix_total ?? 0;
    }
}
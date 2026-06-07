<?php
// app/Models/MouvementStock.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * Modèle MouvementStock
 * 
 * Représente un mouvement (entrée/sortie) sur un produit
 */
class MouvementStock extends Model
{
    use HasFactory;

    /**
     * Motifs d'entrée disponibles
     */
    public const MOTIFS_ENTREE = [
        'achat' => 'Achat',
        'don' => 'Don',
        'production' => 'Production propre',
        'retour' => 'Retour',
        'inventaire' => 'Ajustement inventaire',
        'autre' => 'Autre',
    ];

    /**
     * Motifs de sortie disponibles
     */
    public const MOTIFS_SORTIE = [
        'consommation' => 'Consommation animale',
        'vente' => 'Vente',
        'perte' => 'Perte',
        'casse' => 'Casse',
        'don' => 'Don',
        'inventaire' => 'Ajustement inventaire',
        'autre' => 'Autre',
    ];

    /**
     * La table associée au modèle.
     */
    protected $table = 'mouvements_stock';

    /**
     * Les attributs assignables en masse.
     */
    protected $fillable = [
        'produit_id',
        'user_id',
        'elevage_id',
        'type',
        'quantite',
        'quantite_avant',
        'quantite_apres',
        'motif',
        'description',
        'reference_facture',
        'fournisseur',
        'destinataire',
        'date_mouvement',
    ];

    /**
     * Les attributs qui doivent être castés.
     */
    protected $casts = [
        'quantite' => 'decimal:2',
        'quantite_avant' => 'decimal:2',
        'quantite_apres' => 'decimal:2',
        'date_mouvement' => 'datetime',
    ];

    // ========== RELATIONS ==========

    /**
     * Relation avec le produit
     */
    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    /**
     * Relation avec l'utilisateur qui a effectué le mouvement
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec l'élevage
     */
    public function elevage()
    {
        return $this->belongsTo(Elevage::class);
    }

    // ========== ACCESSORS ==========

    /**
     * Accesseur pour le libellé du motif
     */
    protected function motifLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                $motifs = $this->type === 'entree' 
                    ? self::MOTIFS_ENTREE 
                    : self::MOTIFS_SORTIE;
                return $motifs[$this->motif] ?? $this->motif;
            }
        );
    }

    /**
     * Accesseur pour l'icône du type de mouvement
     */
    protected function typeIcone(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->type === 'entree' ? '📥' : '📤'
        );
    }

    /**
     * Accesseur pour la couleur du type de mouvement
     */
    protected function typeCouleur(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->type === 'entree' ? 'success' : 'warning'
        );
    }

    // ========== SCOPES ==========

    /**
     * Scope pour les entrées
     */
    public function scopeEntrees($query)
    {
        return $query->where('type', 'entree');
    }

    /**
     * Scope pour les sorties
     */
    public function scopeSorties($query)
    {
        return $query->where('type', 'sortie');
    }

    /**
     * Scope pour une période donnée
     */
    public function scopeEntreDates($query, $debut, $fin)
    {
        return $query->whereBetween('date_mouvement', [$debut, $fin]);
    }

    /**
     * Scope pour les mouvements du mois en cours
     */
    public function scopeMoisEnCours($query)
    {
        return $query->whereMonth('date_mouvement', now()->month)
            ->whereYear('date_mouvement', now()->year);
    }
}
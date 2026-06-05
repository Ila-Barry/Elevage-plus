<?php
// app/Models/Produit.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\DB;

/**
 * Modèle Produit
 * 
 * Représente un produit dans le stock d'un élevage
 * 
 * @property int $id
 * @property int $elevage_id
 * @property string $nom
 * @property string $categorie
 * @property float $quantite
 * @property float $seuil_alerte
 * @property string $unite
 * @property string|null $fournisseur
 * @property string|null $description
 * @property string|null $code_barre
 * @property string|null $photo_url
 * @property string $statut
 * @property \Carbon\Carbon|null $derniere_commande
 */
class Produit extends Model
{
    use HasFactory;

    /**
     * Catégories disponibles
     */
    public const CATEGORIES = [
        'aliment' => 'Aliment',
        'medicament' => 'Médicament',
        'equipement' => 'Équipement',
        'vaccin' => 'Vaccin',
        'accessoire' => 'Accessoire',
        'autre' => 'Autre',
    ];

    /**
     * Statuts disponibles
     */
    public const STATUTS = [
        'actif' => 'Actif',
        'inactif' => 'Inactif',
        'rupture' => 'En rupture',
    ];

    /**
     * Unités disponibles
     */
    public const UNITES = [
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

    /**
     * Les attributs assignables en masse.
     */
    protected $fillable = [
        'elevage_id',
        'nom',
        'categorie',
        'quantite',
        'seuil_alerte',
        'unite',
        'fournisseur',
        'description',
        'code_barre',
        'photo_url',
        'statut',
        'derniere_commande',
        'prix_unitaire',
        'prix_total',
        'date_expiration',
        'emplacement_stockage',
    ];

    /**
     * Les attributs qui doivent être castés.
     */
    protected $casts = [
        'quantite' => 'decimal:2',
        'seuil_alerte' => 'decimal:2',
        'prix_unitaire' => 'decimal:2',
        'prix_total' => 'decimal:2',
        'derniere_commande' => 'datetime',
        'date_expiration' => 'date',
    ];

    /**
     * Les valeurs par défaut des attributs.
     */
    protected $attributes = [
        'statut' => 'actif',
        'unite' => 'unite',
    ];

    // ========== RELATIONS ==========

    /**
     * Relation avec l'élevage
     */
    public function elevage()
    {
        return $this->belongsTo(Elevage::class);
    }

    /**
     * Relation avec les mouvements de stock
     */
    public function mouvements()
    {
        return $this->hasMany(MouvementStock::class);
    }

    /**
     * Relation avec les mouvements d'entrée
     */
    public function entrees()
    {
        return $this->mouvements()->where('type', 'entree');
    }

    /**
     * Relation avec les mouvements de sortie
     */
    public function sorties()
    {
        return $this->mouvements()->where('type', 'sortie');
    }

    // ========== ACCESSORS ==========

    /**
     * Accesseur pour l'URL complète de la photo
     */
    protected function photoUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$value) {
                    return null;
                }
                if (filter_var($value, FILTER_VALIDATE_URL)) {
                    return $value;
                }
                return asset('storage/' . $value);
            }
        );
    }

    /**
     * Accesseur pour le libellé de la catégorie
     */
    protected function categorieLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => self::CATEGORIES[$this->categorie] ?? $this->categorie
        );
    }

    /**
     * Accesseur pour le libellé du statut
     */
    protected function statutLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => self::STATUTS[$this->statut] ?? $this->statut
        );
    }

    /**
     * Accesseur pour le libellé de l'unité
     */
    protected function uniteLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => self::UNITES[$this->unite] ?? $this->unite
        );
    }

    /**
     * Accesseur pour la valeur totale du stock
     */
    protected function valeurTotale(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->prix_unitaire) {
                    return $this->quantite * $this->prix_unitaire;
                }
                return $this->prix_total ?? 0;
            }
        );
    }

    /**
     * Vérifie si le stock est critique
     */
    protected function isCritique(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->quantite <= $this->seuil_alerte && $this->seuil_alerte > 0
        );
    }

    /**
     * Vérifie si le produit est en rupture
     */
    protected function isRupture(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->quantite <= 0
        );
    }

    // ========== SCOPES ==========

    /**
     * Scope pour les produits actifs
     */
    public function scopeActif($query)
    {
        return $query->where('statut', 'actif');
    }

    /**
     * Scope pour les produits en rupture
     */
    public function scopeRupture($query)
    {
        return $query->where('statut', 'rupture')->orWhere('quantite', '<=', 0);
    }

    /**
     * Scope pour les produits en stock critique
     */
    public function scopeCritique($query)
    {
        return $query->whereRaw('quantite <= seuil_alerte AND seuil_alerte > 0');
    }

    /**
     * Scope pour les produits par catégorie
     */
    public function scopeByCategorie($query, string $categorie)
    {
        return $query->where('categorie', $categorie);
    }

    /**
     * Scope pour les produits avec expiration proche
     */
    public function scopeExpirationProche($query, int $jours = 30)
    {
        return $query->whereNotNull('date_expiration')
            ->where('date_expiration', '<=', now()->addDays($jours))
            ->where('date_expiration', '>', now());
    }

    /**
     * Scope pour les produits expirés
     */
    public function scopeExpires($query)
    {
        return $query->whereNotNull('date_expiration')
            ->where('date_expiration', '<', now());
    }

    // ========== MÉTHODES UTILITAIRES ==========

    /**
     * Met à jour le statut du produit en fonction de la quantité
     */
    public function updateStatut(): void
    {
        if ($this->quantite <= 0) {
            $this->statut = 'rupture';
        } elseif ($this->quantite <= $this->seuil_alerte && $this->seuil_alerte > 0) {
            $this->statut = 'actif'; // Reste actif mais critique
        } else {
            $this->statut = 'actif';
        }
        $this->saveQuietly();
    }

    /**
     * Ajoute une entrée de stock
     */
    public function addStock(float $quantite, array $data = []): MouvementStock
    {
        $quantiteAvant = $this->quantite;
        $this->quantite += $quantite;
        $this->save();
        $this->updateStatut();

        return $this->mouvements()->create([
            'type' => 'entree',
            'quantite' => $quantite,
            'quantite_avant' => $quantiteAvant,
            'quantite_apres' => $this->quantite,
            'motif' => $data['motif'] ?? 'Achat',
            'description' => $data['description'] ?? null,
            'reference_facture' => $data['reference_facture'] ?? null,
            'fournisseur' => $data['fournisseur'] ?? $this->fournisseur,
            'destinataire' => $data['destinataire'] ?? null,
            'user_id' => $data['user_id'] ?? auth()->id(),
            'elevage_id' => $this->elevage_id,
            'date_mouvement' => $data['date_mouvement'] ?? now(),
        ]);
    }

    /**
     * Retire une quantité du stock
     */
    public function removeStock(float $quantite, array $data = []): MouvementStock
    {
        if ($this->quantite < $quantite) {
            throw new \Exception("Stock insuffisant. Quantité disponible: {$this->quantite} {$this->unite}");
        }

        $quantiteAvant = $this->quantite;
        $this->quantite -= $quantite;
        $this->save();
        $this->updateStatut();

        return $this->mouvements()->create([
            'type' => 'sortie',
            'quantite' => $quantite,
            'quantite_avant' => $quantiteAvant,
            'quantite_apres' => $this->quantite,
            'motif' => $data['motif'] ?? 'Consommation',
            'description' => $data['description'] ?? null,
            'reference_facture' => $data['reference_facture'] ?? null,
            'fournisseur' => $data['fournisseur'] ?? null,
            'destinataire' => $data['destinataire'] ?? $this->fournisseur,
            'user_id' => $data['user_id'] ?? auth()->id(),
            'elevage_id' => $this->elevage_id,
            'date_mouvement' => $data['date_mouvement'] ?? now(),
        ]);
    }

    /**
     * Vérifie si le stock est suffisant
     */
    public function hasEnoughStock(float $quantite): bool
    {
        return $this->quantite >= $quantite;
    }
}
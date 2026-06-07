<?php
// app/Models/Elevage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

/**
 * Modèle Elevage
 * 
 * Représente un élevage appartenant à un utilisateur (éleveur)
 * 
 * @property int $id
 * @property int $user_id
 * @property string $nom
 * @property string|null $img_url
 * @property string $localisation
 * @property float $superficie
 * @property string $type_elevage
 * @property string|null $description
 * @property string|null $adresse
 * @property string|null $ville
 * @property string|null $code_postal
 * @property string $pays
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $telephone
 * @property string|null $email_contact
 * @property string $statut
 * @property \Carbon\Carbon $date_creation
 */
class Elevage extends Model
{
    use HasFactory;

    /**
     * Types d'élevage disponibles
     */
    public const TYPES_ELEVAGE = [
        'bovins' => 'Bovins',
        'ovins' => 'Ovins',
        'caprins' => 'Caprins',
        'volailles' => 'Volailles',
        'porcins' => 'Porcins',
        'equins' => 'Équins',
        'apiculture' => 'Apiculture',
        'cuniculture' => 'Cuniculture',
        'mixte' => 'Mixte',
        'autre' => 'Autre',
    ];

    /**
     * Statuts disponibles
     */
    public const STATUTS = [
        'actif' => 'Actif',
        'inactif' => 'Inactif',
        'ferme' => 'Fermé',
    ];

    /**
     * Les attributs assignables en masse.
     */
    protected $fillable = [
        'user_id',
        'nom',
        'img_url',
        'localisation',
        'superficie',
        'type_elevage',
        'description',
        'adresse',
        'ville',
        'code_postal',
        'pays',
        'latitude',
        'longitude',
        'telephone',
        'email_contact',
        'statut',
        'date_creation',
    ];

    /**
     * Les attributs qui doivent être castés.
     */
    protected $casts = [
        'superficie' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'date_creation' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Les valeurs par défaut des attributs.
     */
    protected $attributes = [
        'statut' => 'actif',
        'pays' => 'Sénégal',
    ];

    // ========== RELATIONS ==========

    /**
     * Relation avec l'utilisateur (propriétaire)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec les animaux
     */
    public function animaux()
    {
        return $this->hasMany(Animal::class);
    }

    /**
     * Relation avec les produits (stock)
     */
    public function produits()
    {
        return $this->hasMany(Produit::class);
    }

    /**
     * Relation avec les mouvements de stock
     */
    public function mouvementsStock()
    {
        return $this->hasMany(MouvementStock::class);
    }

    /**
     * Relation avec les tâches
     */
    public function taches()
    {
        return $this->hasManyThrough(Tache::class, Animal::class);
    }

    // ========== ACCESSORS ==========

    /**
     * Accesseur pour l'URL complète de l'image
     */
    protected function imgUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$value) {
                    return $this->getDefaultImage();
                }
                if (filter_var($value, FILTER_VALIDATE_URL)) {
                    return $value;
                }
                return asset('storage/' . $value);
            }
        );
    }

    /**
     * Accesseur pour le libellé du type d'élevage
     */
    protected function typeElevageLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => self::TYPES_ELEVAGE[$this->type_elevage] ?? $this->type_elevage
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
     * Accesseur pour l'adresse complète formatée
     */
    protected function adresseComplete(): Attribute
    {
        return Attribute::make(
            get: function () {
                $parts = array_filter([
                    $this->adresse,
                    $this->ville,
                    $this->code_postal,
                    $this->pays,
                ]);
                return implode(', ', $parts);
            }
        );
    }

    /**
     * Accesseur pour le nombre total d'animaux
     */
    protected function totalAnimaux(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->animaux()->count()
        );
    }

    /**
     * Accesseur pour le nombre total de produits en stock
     */
    protected function totalProduits(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->produits()->count()
        );
    }

    /**
     * Accesseur pour la valeur totale du stock
     */
    protected function valeurStockTotale(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->produits()->sum(DB::raw('quantite * COALESCE(prix_unitaire, 0)'))
        );
    }

    // ========== SCOPES ==========

    /**
     * Scope pour les élevages actifs
     */
    public function scopeActif($query)
    {
        return $query->where('statut', 'actif');
    }

    /**
     * Scope pour les élevages par type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type_elevage', $type);
    }

    /**
     * Scope pour les élevages par localisation
     */
    public function scopeByLocalisation($query, string $localisation)
    {
        return $query->where('localisation', 'LIKE', "%{$localisation}%")
            ->orWhere('ville', 'LIKE', "%{$localisation}%")
            ->orWhere('pays', 'LIKE', "%{$localisation}%");
    }

    /**
     * Scope pour la recherche textuelle
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nom', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%")
              ->orWhere('localisation', 'LIKE', "%{$search}%")
              ->orWhere('ville', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Scope pour les élevages avec leurs statistiques
     */
    public function scopeWithStats($query)
    {
        return $query->withCount([
            'animaux',
            'produits',
        ]);
    }

    // ========== MÉTHODES UTILITAIRES ==========

    /**
     * Vérifie si l'utilisateur est le propriétaire
     */
    public function isOwner(int $userId): bool
    {
        return $this->user_id === $userId;
    }

    /**
     * Vérifie si l'élevage est actif
     */
    public function isActif(): bool
    {
        return $this->statut === 'actif';
    }

    /**
     * Active l'élevage
     */
    public function activate(): void
    {
        $this->update(['statut' => 'actif']);
    }

    /**
     * Désactive l'élevage
     */
    public function deactivate(): void
    {
        $this->update(['statut' => 'inactif']);
    }

    /**
     * Ferme l'élevage
     */
    public function close(): void
    {
        $this->update(['statut' => 'ferme']);
    }

    /**
     * Retourne l'image par défaut
     */
    protected function getDefaultImage(): string
    {
        $images = [
            'bovins' => '/images/default-farm-cattle.jpg',
            'ovins' => '/images/default-farm-sheep.jpg',
            'caprins' => '/images/default-farm-goat.jpg',
            'volailles' => '/images/default-farm-poultry.jpg',
            'porcins' => '/images/default-farm-pig.jpg',
            'mixte' => '/images/default-farm-mixed.jpg',
        ];
        
        return asset($images[$this->type_elevage] ?? '/images/default-farm.jpg');
    }
}
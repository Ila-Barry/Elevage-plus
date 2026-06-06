<?php
// app/Models/Elevage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * Modèle Elevage
 * 
 * Représente un élevage géré par un utilisateur (éleveur)
 * 
 * @property int $id
 * @property int $user_id
 * @property string $nom
 * @property string $localisation
 * @property int $superficie
 * @property string $type_elevage
 * @property string|null $img_url
 * @property string|null $description
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read User $proprietaire
 * @property-read \Illuminate\Database\Eloquent\Collection|Animal[] $animaux
 * @property-read \Illuminate\Database\Eloquent\Collection|Produit[] $produits
 */
class Elevage extends Model
{
    use HasFactory;

    /**
     * Types d'élevage autorisés
     */
    public const TYPES_ELEVAGE = [
        'bovins',
        'ovins', 
        'caprins',
        'volailles',
        'mixte',
        'autres'
    ];

    /**
     * Attributs assignables en masse
     */
    protected $fillable = [
        'user_id',
        'nom',
        'localisation',
        'superficie',
        'type_elevage',
        'img_url',
        'description',
    ];

    /**
     * Attributs à cacher pour la sérialisation
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * Attributs à convertir
     */
    protected $casts = [
        'superficie' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relation avec l'utilisateur propriétaire (éleveur)
     * Un élevage appartient à un utilisateur
     * 
     * @return BelongsTo
     */
    public function proprietaire(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relation avec les animaux
     * Un élevage peut avoir plusieurs animaux
     * 
     * @return HasMany
     */
    public function animaux(): HasMany
    {
        return $this->hasMany(Animal::class);
    }

    /**
     * Relation avec les produits (stocks)
     * Un élevage peut avoir plusieurs produits
     * 
     * @return HasMany
     */
    public function produits(): HasMany
    {
        return $this->hasMany(Produit::class);
    }

    /**
     * Accesseur pour l'URL complète de l'image
     * 
     * @return Attribute
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if (empty($attributes['img_url'])) {
                    return null;
                }
                // Retourne l'URL complète si le chemin est stocké relativement
                if (str_starts_with($attributes['img_url'], 'http')) {
                    return $attributes['img_url'];
                }
                return asset('storage/' . $attributes['img_url']);
            }
        );
    }

    /**
     * Accesseur pour le nombre d'animaux
     * 
     * @return int
     */
    public function getAnimauxCountAttribute(): int
    {
        return $this->animaux()->count();
    }

    /**
     * Vérifie si l'élevage appartient à un utilisateur donné
     * 
     * @param int $userId
     * @return bool
     */
    public function belongsToUser(int $userId): bool
    {
        return $this->user_id === $userId;
    }

    /**
     * Scope pour filtrer par type d'élevage
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type_elevage', $type);
    }

    /**
     * Scope pour filtrer par localisation
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $localisation
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLocatedIn($query, string $localisation)
    {
        return $query->where('localisation', 'LIKE', '%' . $localisation . '%');
    }

    /**
     * Valide si le type d'élevage est autorisé
     * 
     * @param string $type
     * @return bool
     */
    public static function isValidType(string $type): bool
    {
        return in_array($type, self::TYPES_ELEVAGE);
    }
}
?>
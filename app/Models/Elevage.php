<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Elevage extends Model
{
    use HasFactory;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'elevages';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'nom',
        'img_url',
        'localisation',
        'superficie',
        'type_elevage',
        'description',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'superficie' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========== RELATIONS ==========

    /**
     * Relation avec l'utilisateur (éleveur)
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
     * Relation avec les produits (stocks)
     */
    public function produits()
    {
        return $this->hasMany(Produit::class);
    }

    // ========== ACCESSORS ==========

    /**
     * Accesseur pour l'URL complète de l'image
     */
    public function getImgUrlAttribute($value)
    {
        if (!$value) {
            return null;
        }
        
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        
        return asset('storage/' . $value);
    }
}
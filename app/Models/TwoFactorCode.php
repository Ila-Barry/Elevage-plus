<?php
// app/Models/TwoFactorCode.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle TwoFactorCode
 * 
 * Stocke les codes d'authentification à deux facteurs
 * avec leur expiration.
 */
class TwoFactorCode extends Model
{
    use HasFactory;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'two_factor_codes';

    /**
     * Les attributs assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'code',
        'expires_at',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Vérifie si le code est encore valide
     */
    public function isValid(): bool
    {
        return $this->expires_at > now();
    }
}
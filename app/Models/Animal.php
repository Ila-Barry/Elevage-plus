<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Animal extends Model
{
    use HasFactory;

    protected $fillable = [
        'elevage_id',
        'nom',
        'race',
        'espece',
        'poids',
        'statut_sanitaire',
        'img_url',
        'date_naissance',
        'description',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'poids' => 'decimal:2',
    ];

    public function elevage()
    {
        return $this->belongsTo(Elevage::class);
    }

    public function taches()
    {
        return $this->hasMany(Tache::class);
    }
}
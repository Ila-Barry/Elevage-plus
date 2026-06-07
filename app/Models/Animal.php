<?php
// app/Models/Animal.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

/**
 * Modèle Animal
 * 
 * Représente un animal appartenant à un élevage
 * 
 * @property int $id
 * @property int $elevage_id
 * @property string $nom
 * @property string $race
 * @property string $espece
 * @property float $poids
 * @property string $statut_sanitaire
 * @property string|null $img_url
 * @property string|null $description
 * @property string $date_naissance
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read Elevage $elevage
 * @property-read \Illuminate\Database\Eloquent\Collection|AnimalHistorique[] $historiques
 */
class Animal extends Model
{
    use HasFactory;

    /**
     * Statuts sanitaires autorisés
     */
    public const STATUTS_SANITAIRES = [
        'sain' => 'Sain',
        'sous_traitement' => 'Sous traitement',
        'en_quarantaine' => 'En quarantaine',
        'malade' => 'Malade',
    ];

    /**
     * Espèces autorisées
     */
    public const ESPECES = [
        'bovin' => 'Bovin',
        'ovin' => 'Ovin',
        'caprin' => 'Caprin',
        'volaille' => 'Volaille',
        'autre' => 'Autre',
    ];

    /**
     * Attributs assignables en masse
     */
    protected $fillable = [
        'elevage_id',
        'nom',
        'race',
        'espece',
        'poids',
        'statut_sanitaire',
        'img_url',
        'description',
        'date_naissance',
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
        'poids' => 'decimal:2',
        'date_naissance' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relation avec l'élevage
     * Un animal appartient à un élevage
     * 
     * @return BelongsTo
     */
    public function elevage(): BelongsTo
    {
        return $this->belongsTo(Elevage::class);
    }

    /**
     * Relation avec l'historique des modifications
     * 
     * @return HasMany
     */
    public function historiques(): HasMany
    {
        return $this->hasMany(AnimalHistorique::class);
    }

    /**
     * Accesseur pour l'âge calculé automatiquement
     * Calcule l'âge en années, mois ou jours selon la date de naissance
     * 
     * @return Attribute
     */
    protected function age(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if (empty($attributes['date_naissance'])) {
                    return null;
                }
                
                $birthDate = Carbon::parse($attributes['date_naissance']);
                $now = Carbon::now();
                
                $years = $birthDate->diffInYears($now);
                
                if ($years > 0) {
                    return [
                        'valeur' => $years,
                        'unite' => $years > 1 ? 'ans' : 'an',
                        'texte' => $years . ' ' . ($years > 1 ? 'ans' : 'an'),
                        'en_mois' => $birthDate->diffInMonths($now),
                        'en_jours' => $birthDate->diffInDays($now),
                    ];
                }
                
                $months = $birthDate->diffInMonths($now);
                if ($months > 0) {
                    return [
                        'valeur' => $months,
                        'unite' => 'mois',
                        'texte' => $months . ' mois',
                        'en_mois' => $months,
                        'en_jours' => $birthDate->diffInDays($now),
                    ];
                }
                
                $days = $birthDate->diffInDays($now);
                return [
                    'valeur' => $days,
                    'unite' => 'jours',
                    'texte' => $days . ' ' . ($days > 1 ? 'jours' : 'jour'),
                    'en_mois' => 0,
                    'en_jours' => $days,
                ];
            }
        );
    }

    /**
     * Accesseur pour l'âge en années (simple)
     * 
     * @return int|null
     */
    public function getAgeEnAnneesAttribute(): ?int
    {
        if (!$this->date_naissance) {
            return null;
        }
        return $this->date_naissance->diffInYears(now());
    }

    /**
     * Accesseur pour l'âge en mois
     * 
     * @return int|null
     */
    public function getAgeEnMoisAttribute(): ?int
    {
        if (!$this->date_naissance) {
            return null;
        }
        return $this->date_naissance->diffInMonths(now());
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
                    return $this->getDefaultImageUrl();
                }
                if (str_starts_with($attributes['img_url'], 'http')) {
                    return $attributes['img_url'];
                }
                return asset('storage/' . $attributes['img_url']);
            }
        );
    }

    /**
     * Obtient l'URL de l'image par défaut selon l'espèce
     * 
     * @return string
     */
    private function getDefaultImageUrl(): string
    {
        $defaultImages = [
            'bovin' => '/images/defaults/cow-default.png',
            'ovin' => '/images/defaults/sheep-default.png',
            'caprin' => '/images/defaults/goat-default.png',
            'volaille' => '/images/defaults/chicken-default.png',
            'autre' => '/images/defaults/animal-default.png',
        ];
        
        return asset($defaultImages[$this->espece] ?? $defaultImages['autre']);
    }

    /**
     * Accesseur pour le libellé du statut sanitaire
     * 
     * @return string
     */
    public function getStatutSanitaireLabelAttribute(): string
    {
        return self::STATUTS_SANITAIRES[$this->statut_sanitaire] ?? $this->statut_sanitaire;
    }

    /**
     * Accesseur pour la classe CSS du statut sanitaire
     * 
     * @return string
     */
    public function getStatutSanitaireColorAttribute(): string
    {
        $colors = [
            'sain' => 'success',
            'sous_traitement' => 'warning',
            'en_quarantaine' => 'info',
            'malade' => 'danger',
        ];
        
        return $colors[$this->statut_sanitaire] ?? 'secondary';
    }

    /**
     * Accesseur pour le libellé de l'espèce
     * 
     * @return string
     */
    public function getEspeceLabelAttribute(): string
    {
        return self::ESPECES[$this->espece] ?? $this->espece;
    }

    /**
     * Vérifie si l'animal est en bonne santé
     * 
     * @return bool
     */
    public function isHealthy(): bool
    {
        return $this->statut_sanitaire === 'sain';
    }

    /**
     * Vérifie si l'animal est malade
     * 
     * @return bool
     */
    public function isSick(): bool
    {
        return $this->statut_sanitaire === 'malade';
    }

    /**
     * Vérifie si l'animal appartient à un utilisateur donné
     * 
     * @param int $userId
     * @return bool
     */
    public function belongsToUser(int $userId): bool
    {
        return $this->elevage && $this->elevage->user_id === $userId;
    }

    /**
     * Scope pour filtrer par espèce
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $espece
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfEspece($query, string $espece)
    {
        return $query->where('espece', $espece);
    }

    /**
     * Scope pour filtrer par statut sanitaire
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $statut
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithStatut($query, string $statut)
    {
        return $query->where('statut_sanitaire', $statut);
    }

    /**
     * Scope pour filtrer par âge (en années)
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $minAge
     * @param int|null $maxAge
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAgeBetween($query, int $minAge, ?int $maxAge = null)
    {
        $dateMin = now()->subYears($maxAge ?? 100);
        $dateMax = now()->subYears($minAge);
        
        $query->whereBetween('date_naissance', [$dateMin, $dateMax]);
    }

    /**
     * Scope pour les animaux jeunes (moins d'1 an)
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeYoung($query)
    {
        return $query->where('date_naissance', '>=', now()->subYear());
    }

    /**
     * Scope pour les animaux adultes (plus d'1 an)
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAdult($query)
    {
        return $query->where('date_naissance', '<', now()->subYear());
    }

    /**
     * Scope pour recherche par nom
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where('nom', 'LIKE', '%' . $search . '%')
                     ->orWhere('race', 'LIKE', '%' . $search . '%');
    }

    /**
     * Valide si l'espèce est autorisée
     * 
     * @param string $espece
     * @return bool
     */
    public static function isValidEspece(string $espece): bool
    {
        return array_key_exists($espece, self::ESPECES);
    }

    /**
     * Valide si le statut sanitaire est autorisé
     * 
     * @param string $statut
     * @return bool
     */
    public static function isValidStatut(string $statut): bool
    {
        return array_key_exists($statut, self::STATUTS_SANITAIRES);
    }
}
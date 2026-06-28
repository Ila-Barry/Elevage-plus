<?php
// app/Models/Animal.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

/**
 * Modèle Animal
 * 
 * Représente un animal dans un élevage
 * 
 * @property int $id
 * @property int $elevage_id
 * @property string $nom
 * @property string $espece
 * @property string|null $race
 * @property \Carbon\Carbon $date_naissance
 * @property float $poids
 * @property string $statut_sanitaire
 * @property string|null $img_url
 * @property string|null $numero_identification
 * @property string $sexe
 * @property string|null $couleur
 * @property string|null $signes_particuliers
 * @property string $statut
 * @property \Carbon\Carbon|null $date_deces
 * @property string|null $motif_deces
 * @property int|null $pere_id
 * @property int|null $mere_id
 */
class Animal extends Model
{
    use HasFactory;

    /**
     * Espèces disponibles
     */
    public const ESPECES = [
        'bovin' => 'Bovin',
        'ovin' => 'Ovin',
        'caprin' => 'Caprin',
        'volaille' => 'Volaille',
        'porcin' => 'Porcin',
        'equin' => 'Équin',
        'autre' => 'Autre',
    ];

    /**
     * Statuts sanitaires disponibles
     */
    public const STATUTS_SANITAIRES = [
        'bon' => 'Bon',
        'a_surveiller' => 'À surveiller',
        'malade' => 'Malade',
        'critique' => 'Critique',
    ];

    /**
     * Statuts de l'animal disponibles
     */
    public const STATUTS = [
        'actif' => 'Actif',
        'vendu' => 'Vendu',
        'decede' => 'Décédé',
        'transfere' => 'Transféré',
    ];

    /**
     * Sexes disponibles
     */
    public const SEXES = [
        'male' => 'Mâle',
        'femelle' => 'Femelle',
    ];

    /**
     * La table associée au modèle.
     */
    protected $table = 'animaux';

    /**
     * Les attributs assignables en masse.
     */
    protected $fillable = [
        'elevage_id',
        'nom',
        'espece',
        'race',
        'date_naissance',
        'poids',
        'statut_sanitaire',
        'img_url',
        'numero_identification',
        'sexe',
        'couleur',
        'signes_particuliers',
        'statut',
        'date_deces',
        'motif_deces',
        'pere_id',
        'mere_id',
    ];

    /**
     * Les attributs qui doivent être castés.
     */
    protected $casts = [
        'date_naissance' => 'date',
        'date_deces' => 'date',
        'poids' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Les valeurs par défaut des attributs.
     */
    protected $attributes = [
        'statut_sanitaire' => 'bon',
        'statut' => 'actif',
        'sexe' => 'male',
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
     * Relation avec le père
     */
    public function pere()
    {
        return $this->belongsTo(Animal::class, 'pere_id');
    }

    /**
     * Relation avec la mère
     */
    public function mere()
    {
        return $this->belongsTo(Animal::class, 'mere_id');
    }

    /**
     * Relation avec les enfants
     */
    public function enfants()
    {
        return $this->hasMany(Animal::class, 'pere_id')
            ->orWhere('mere_id', $this->id);
    }

    /**
     * Relation avec les tâches
     */
    public function taches()
    {
        return $this->hasMany(Tache::class);
    }

    /**
     * Relation avec l'historique des modifications
     */
    public function historiques()
    {
        return $this->hasMany(AnimalHistorique::class);
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
     * Accesseur pour l'âge calculé automatiquement
     */
    protected function age(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->date_naissance) {
                    return null;
                }
                
                $age = $this->date_naissance->diff(Carbon::now());
                
                return [
                    'annees' => $age->y,
                    'mois' => $age->m,
                    'jours' => $age->d,
                    'total_mois' => $this->date_naissance->diffInMonths(Carbon::now()),
                    'total_jours' => $this->date_naissance->diffInDays(Carbon::now()),
                    'formatted' => $this->formatAge($age),
                ];
            }
        );
    }

    /**
     * Accesseur pour le libellé de l'espèce
     */
    protected function especeLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => self::ESPECES[$this->espece] ?? $this->espece
        );
    }

    /**
     * Accesseur pour le libellé du statut sanitaire
     */
    protected function statutSanitaireLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => self::STATUTS_SANITAIRES[$this->statut_sanitaire] ?? $this->statut_sanitaire
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
     * Accesseur pour le libellé du sexe
     */
    protected function sexeLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => self::SEXES[$this->sexe] ?? $this->sexe
        );
    }

    /**
     * Accesseur pour la couleur du statut sanitaire (badge)
     */
    protected function statutSanitaireCouleur(): Attribute
    {
        return Attribute::make(
            get: function () {
                return match($this->statut_sanitaire) {
                    'bon' => 'success',
                    'a_surveiller' => 'warning',
                    'malade' => 'danger',
                    'critique' => 'dark',
                    default => 'secondary',
                };
            }
        );
    }

    // ========== SCOPES ==========

    /**
     * Scope pour les animaux actifs
     */
    public function scopeActif($query)
    {
        return $query->where('statut', 'actif');
    }

    /**
     * Scope pour les animaux par espèce
     */
    public function scopeByEspece($query, string $espece)
    {
        return $query->where('espece', $espece);
    }

    /**
     * Scope pour les animaux par statut sanitaire
     */
    public function scopeByStatutSanitaire($query, string $statut)
    {
        return $query->where('statut_sanitaire', $statut);
    }

    /**
     * Scope pour les animaux par âge (mois)
     */
    public function scopeByAge($query, int $moisMin, int $moisMax = null)
    {
        $dateMin = Carbon::now()->subMonths($moisMax ?? $moisMin);
        $dateMax = Carbon::now()->subMonths($moisMin);
        
        if ($moisMax) {
            return $query->whereBetween('date_naissance', [$dateMin, $dateMax]);
        }
        
        return $query->where('date_naissance', '<=', $dateMin);
    }

    /**
     * Scope pour les animaux en bonne santé
     */
    public function scopeEnBonneSante($query)
    {
        return $query->where('statut_sanitaire', 'bon');
    }

    /**
     * Scope pour les animaux malades
     */
    public function scopeMalades($query)
    {
        return $query->whereIn('statut_sanitaire', ['malade', 'critique']);
    }

    /**
     * Scope pour la recherche textuelle
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nom', 'LIKE', "%{$search}%")
              ->orWhere('race', 'LIKE', "%{$search}%")
              ->orWhere('numero_identification', 'LIKE', "%{$search}%")
              ->orWhere('couleur', 'LIKE', "%{$search}%");
        });
    }

    // ========== MÉTHODES UTILITAIRES ==========

    /**
     * Formatage de l'âge pour affichage
     */
    protected function formatAge($age): string
    {
        $parts = [];
        
        if ($age->y > 0) {
            $parts[] = $age->y . ' ' . ($age->y > 1 ? 'ans' : 'an');
        }
        if ($age->m > 0) {
            $parts[] = $age->m . ' mois';
        }
        if ($age->y == 0 && $age->m == 0 && $age->d > 0) {
            $parts[] = $age->d . ' ' . ($age->d > 1 ? 'jours' : 'jour');
        }
        
        return !empty($parts) ? implode(' ', $parts) : 'Nouveau-né';
    }

    /**
     * Vérifie si l'animal est en vie
     */
    public function isAlive(): bool
    {
        return $this->statut === 'actif';
    }

    /**
     * Marque l'animal comme décédé
     */
    public function markAsDeceased(string $motif, ?string $date = null): void
    {
        $this->update([
            'statut' => 'decede',
            'motif_deces' => $motif,
            'date_deces' => $date ?? now(),
        ]);
    }

    /**
     * Marque l'animal comme vendu
     */
    public function markAsSold(): void
    {
        $this->update(['statut' => 'vendu']);
    }

    /**
     * Met à jour le statut sanitaire
     */
    public function updateHealthStatus(string $statut, ?string $notes = null): void
    {
        $this->update([
            'statut_sanitaire' => $statut,
            'signes_particuliers' => $notes ?? $this->signes_particuliers,
        ]);
    }

    /**
     * Calcule le poids moyen par espèce
     */
    public static function poidsMoyenParEspece(int $elevageId): array
    {
        return self::where('elevage_id', $elevageId)
            ->where('statut', 'actif')
            ->select('espece', \DB::raw('AVG(poids) as poids_moyen'))
            ->groupBy('espece')
            ->get()
            ->map(fn($item) => [
                'espece' => $item->espece,
                'espece_label' => self::ESPECES[$item->espece] ?? $item->espece,
                'poids_moyen' => round($item->poids_moyen, 2),
            ])
            ->toArray();
    }

    /**
     * Retourne l'image par défaut selon l'espèce
     */
    protected function getDefaultImage(): string
    {
        $images = [
            'bovin' => '/images/default-cow.jpg',
            'ovin' => '/images/default-sheep.jpg',
            'caprin' => '/images/default-goat.jpg',
            'volaille' => '/images/default-chicken.jpg',
            'porcin' => '/images/default-pig.jpg',
            'equin' => '/images/default-horse.jpg',
            'autre' => '/images/default-animal.jpg',
        ];
        
        return asset($images[$this->espece] ?? '/images/default-animal.jpg');
    }

    /**
     * Calcule le poids moyen par espèce pour plusieurs élevages
     */
    public static function poidsMoyenParEspeceMultiple($elevageIds): array
    {
        return self::whereIn('elevage_id', $elevageIds)
            ->where('statut', 'actif')
            ->select('espece', \DB::raw('AVG(poids) as poids_moyen'))
            ->groupBy('espece')
            ->get()
            ->map(fn($item) => [
                'espece' => $item->espece,
                'espece_label' => self::ESPECES[$item->espece] ?? $item->espece,
                'poids_moyen' => round($item->poids_moyen, 2),
            ])
            ->toArray();
    }
}
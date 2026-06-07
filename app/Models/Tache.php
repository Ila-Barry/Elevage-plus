<?php
// app/Models/Tache.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

/**
 * Modèle Tache
 * 
 * Représente une tâche (vaccination, pesée, soin, etc.)
 * Peut être liée à un animal spécifique ou à un élevage entier
 * 
 * @property int $id
 * @property int|null $animal_id
 * @property int $elevage_id
 * @property string $titre
 * @property string $type
 * @property string $date_planifiee
 * @property string|null $date_realisee
 * @property bool $terminee
 * @property string|null $description
 * @property string|null $notes
 * 
 * @property-read Animal|null $animal
 * @property-read Elevage $elevage
 * @property-read \Illuminate\Database\Eloquent\Collection|TacheRappel[] $rappels
 */
class Tache extends Model
{
    use HasFactory;

    /**
     * Types de tâches autorisés
     */
    public const TYPES = [
        'vaccination' => 'Vaccination',
        'pesee' => 'Pesée',
        'vermifuge' => 'Vermifuge',
        'soin' => 'Soin',
        'alimentation' => 'Alimentation',
        'nettoyage' => 'Nettoyage',
        'autre' => 'Autre',
    ];

    /**
     * Couleurs pour FullCalendar (par type)
     */
    public const COULEURS_CALENDRIER = [
        'vaccination' => '#dc3545', // rouge
        'pesee' => '#28a745',       // vert
        'vermifuge' => '#ffc107',   // jaune
        'soin' => '#17a2b8',        // bleu
        'alimentation' => '#6f42c1', // violet
        'nettoyage' => '#fd7e14',    // orange
        'autre' => '#6c757d',        // gris
    ];

    /**
     * Délais des rappels (en heures)
     */
    public const RAPPELS_DELAIS = [
        '48h' => 48,
        '24h' => 24,
        '1h' => 1,
        '30min' => 0.5,
        'now' => 0,
    ];

    /**
     * Attributs assignables en masse
     */
    protected $fillable = [
        'animal_id',
        'elevage_id',
        'titre',
        'type',
        'date_planifiee',
        'date_realisee',
        'terminee',
        'description',
        'notes',
    ];

    /**
     * Attributs à convertir
     */
    protected $casts = [
        'date_planifiee' => 'date',
        'date_realisee' => 'date',
        'terminee' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relation avec l'animal
     */
    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }

    /**
     * Relation avec l'élevage
     */
    public function elevage(): BelongsTo
    {
        return $this->belongsTo(Elevage::class);
    }

    /**
     * Relation avec les rappels
     */
    public function rappels(): HasMany
    {
        return $this->hasMany(TacheRappel::class);
    }

    /**
     * Accesseur pour savoir si c'est une tâche d'élevage entier
     */
    public function getEstPourElevageAttribute(): bool
    {
        return is_null($this->animal_id);
    }

    /**
     * Accesseur pour le nom de l'entité concernée
     */
    public function getEntiteConcerneeAttribute(): string
    {
        if ($this->animal_id && $this->animal) {
            return $this->animal->nom . ' (' . $this->animal->espece_label . ')';
        }
        return 'Élevage entier: ' . ($this->elevage->nom ?? 'N/A');
    }

    /**
     * Accesseur pour le libellé du type
     */
    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    /**
     * Accesseur pour la couleur du type
     */
    public function getCouleurAttribute(): string
    {
        return self::COULEURS_CALENDRIER[$this->type] ?? self::COULEURS_CALENDRIER['autre'];
    }

    /**
     * Accesseur pour le statut (à faire / fait)
     */
    public function getStatutAttribute(): array
    {
        if ($this->terminee) {
            return [
                'code' => 'fait',
                'label' => 'Fait',
                'color' => 'success',
                'date' => $this->date_realisee?->format('d/m/Y'),
            ];
        }
        
        $today = Carbon::today();
        $datePlanifiee = Carbon::parse($this->date_planifiee);
        
        if ($datePlanifiee->isPast()) {
            return [
                'code' => 'en_retard',
                'label' => 'En retard',
                'color' => 'danger',
            ];
        }
        
        if ($datePlanifiee->isToday()) {
            return [
                'code' => 'aujourdhui',
                'label' => 'À faire aujourd\'hui',
                'color' => 'warning',
            ];
        }
        
        return [
            'code' => 'a_venir',
            'label' => 'À venir',
            'color' => 'info',
        ];
    }

    /**
     * Accesseur pour les rappels générés
     */
    public function getRappelsGeneresAttribute(): array
    {
        return TacheRappel::where('tache_id', $this->id)
            ->get()
            ->map(function($rappel) {
                return [
                    'type' => $rappel->type_rappel,
                    'statut' => $rappel->statut,
                    'heure_prevue' => $rappel->heure_envoi_prevue,
                ];
            })
            ->toArray();
    }

    /**
     * Marque la tâche comme terminée
     * 
     * @param string|null $dateRealisee
     * @return bool
     */
    public function marquerCommeTerminee(?string $dateRealisee = null): bool
    {
        $this->terminee = true;
        $this->date_realisee = $dateRealisee ?? Carbon::today();
        return $this->save();
    }

    /**
     * Vérifie si la tâche est en retard
     */
    public function isEnRetard(): bool
    {
        if ($this->terminee) {
            return false;
        }
        return Carbon::parse($this->date_planifiee)->isPast();
    }

    /**
     * Vérifie si la tâche est pour aujourd'hui
     */
    public function isPourAujourdhui(): bool
    {
        if ($this->terminee) {
            return false;
        }
        return Carbon::parse($this->date_planifiee)->isToday();
    }

    /**
     * Vérifie si l'utilisateur est propriétaire
     */
    public function belongsToUser(int $userId): bool
    {
        return $this->elevage && $this->elevage->user_id === $userId;
    }

    /**
     * Scope pour les tâches d'un élevage
     */
    public function scopeForElevage($query, int $elevageId)
    {
        return $query->where('elevage_id', $elevageId);
    }

    /**
     * Scope pour les tâches d'un animal spécifique
     */
    public function scopeForAnimal($query, int $animalId)
    {
        return $query->where('animal_id', $animalId);
    }

    /**
     * Scope pour les tâches non terminées
     */
    public function scopeNotCompleted($query)
    {
        return $query->where('terminee', false);
    }

    /**
     * Scope pour les tâches terminées
     */
    public function scopeCompleted($query)
    {
        return $query->where('terminee', true);
    }

    /**
     * Scope pour les tâches en retard
     */
    public function scopeLate($query)
    {
        return $query->where('terminee', false)
                     ->where('date_planifiee', '<', Carbon::today());
    }

    /**
     * Scope pour les tâches par type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope pour les tâches par période
     */
    public function scopeBetweenDates($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('date_planifiee', [$startDate, $endDate]);
    }

    /**
     * Scope pour les tâches d'aujourd'hui
     */
    public function scopeToday($query)
    {
        return $query->where('date_planifiee', Carbon::today());
    }

    /**
     * Scope pour les tâches de cette semaine
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('date_planifiee', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek(),
        ]);
    }

    /**
     * Format pour FullCalendar
     */
    public function formatForFullCalendar(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->titre,
            'start' => $this->date_planifiee->format('Y-m-d'),
            'end' => $this->date_planifiee->format('Y-m-d'),
            'color' => $this->couleur,
            'textColor' => '#ffffff',
            'extendedProps' => [
                'type' => $this->type,
                'type_label' => $this->type_label,
                'description' => $this->description,
                'est_terminee' => $this->terminee,
                'animal_nom' => $this->animal?->nom,
                'est_pour_elevage' => $this->estPourElevage,
                'statut' => $this->statut,
            ],
            'className' => $this->terminee ? 'tache-terminee' : 'tache-pendante',
        ];
    }
}
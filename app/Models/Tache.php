<?php
// app/Models/Tache.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

/**
 * Modèle Tache
 * 
 * Représente une tâche planifiée dans un élevage
 */
class Tache extends Model
{
    use HasFactory;

    /**
     * Types de tâches disponibles
     */
    public const TYPES = [
        'vaccination' => 'Vaccination',
        'pesee' => 'Pesée',
        'vermifuge' => 'Vermifuge',
        'soin' => 'Soin',
        'nettoyage' => 'Nettoyage',
        'alimentation' => 'Alimentation',
        'reproduction' => 'Reproduction',
        'visite_veterinaire' => 'Visite vétérinaire',
        'autre' => 'Autre',
    ];

    /**
     * Priorités disponibles
     */
    public const PRIORITES = [
        'basse' => 'Basse',
        'moyenne' => 'Moyenne',
        'haute' => 'Haute',
        'urgente' => 'Urgente',
    ];

    /**
     * Types de rappels automatiques
     */
    public const RAPPEL_TYPES = [
        '48h' => 'apres_demain',
        '24h' => 'demain',
        '1h' => 'aujourdhui',
        'retard' => 'retard',
    ];

    /**
     * Icônes par type de tâche
     */
    public const ICONES = [
        'vaccination' => '💉',
        'pesee' => '⚖️',
        'vermifuge' => '💊',
        'soin' => '🩺',
        'nettoyage' => '🧹',
        'alimentation' => '🍽️',
        'reproduction' => '🤰',
        'visite_veterinaire' => '👨‍⚕️',
        'autre' => '📋',
    ];

    /**
     * Couleurs par priorité
     */
    public const COULEURS_PRIORITE = [
        'basse' => '#10B981',
        'moyenne' => '#3B82F6',
        'haute' => '#F59E0B',
        'urgente' => '#EF4444',
    ];

    /**
     * Les attributs assignables en masse.
     */
    protected $fillable = [
        'animal_id',
        'elevage_id',
        'user_id',
        'titre',
        'type',
        'description',
        'date_planifiee',
        'date_realisee',
        'terminee',
        'priorite',
        'last_reminder_type',
        'last_reminder_sent_at',
        'retard_reminder_count',
        'notes',
    ];

    /**
     * Les attributs qui doivent être castés.
     */
    protected $casts = [
        'date_planifiee' => 'datetime',
        'date_realisee' => 'datetime',
        'last_reminder_sent_at' => 'datetime',
        'terminee' => 'boolean',
        'retard_reminder_count' => 'integer',
    ];

    /**
     * Les valeurs par défaut des attributs.
     */
    protected $attributes = [
        'terminee' => false,
        'priorite' => 'moyenne',
        'retard_reminder_count' => 0,
    ];

    // ========== RELATIONS ==========

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }

    public function elevage()
    {
        return $this->belongsTo(Elevage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ========== ACCESSORS ==========

    protected function typeLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => self::TYPES[$this->type] ?? $this->type
        );
    }

    protected function typeIcone(): Attribute
    {
        return Attribute::make(
            get: fn() => self::ICONES[$this->type] ?? '📋'
        );
    }

    protected function prioriteLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => self::PRIORITES[$this->priorite] ?? $this->priorite
        );
    }

    protected function prioriteCouleur(): Attribute
    {
        return Attribute::make(
            get: fn() => self::COULEURS_PRIORITE[$this->priorite] ?? '#6B7280'
        );
    }

    protected function isLate(): Attribute
    {
        return Attribute::make(
            get: fn() => !$this->terminee && $this->date_planifiee < now()
        );
    }

    protected function isToday(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->date_planifiee->isToday()
        );
    }

    protected function isTomorrow(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->date_planifiee->isTomorrow()
        );
    }

    protected function isAfterTomorrow(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->date_planifiee->isAfter(now()->addDay())
        );
    }

    protected function tempsRestant(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->terminee) {
                    return null;
                }
                
                $now = now();
                if ($this->date_planifiee < $now) {
                    return 'En retard';
                }
                
                $diff = $now->diff($this->date_planifiee);
                
                if ($diff->days > 0) {
                    return $diff->days . ' jour(s)';
                }
                if ($diff->h > 0) {
                    return $diff->h . ' heure(s)';
                }
                if ($diff->i > 0) {
                    return $diff->i . ' minute(s)';
                }
                return 'Très bientôt';
            }
        );
    }

    // ========== MÉTHODES DE RAPPELS AUTOMATIQUES ==========

    /**
     * Vérifie quels rappels doivent être envoyés
     * 
     * @return array Liste des types de rappels à envoyer
     */
    public function getRemindersToSend(): array
    {
        if ($this->terminee) {
            return [];
        }

        $now = now();
        $remindersToSend = [];

        // Ne pas envoyer de rappels si la tâche est déjà terminée
        if ($this->terminee) {
            return [];
        }

        // 1. RAPPEL 48H AVANT (après-demain)
        if ($this->shouldSend48hReminder($now)) {
            $remindersToSend[] = '48h';
        }

        // 2. RAPPEL 24H AVANT (demain)
        if ($this->shouldSend24hReminder($now)) {
            $remindersToSend[] = '24h';
        }

        // 3. RAPPEL 1H AVANT (aujourd'hui)
        if ($this->shouldSend1hReminder($now)) {
            $remindersToSend[] = '1h';
        }

        // 4. RAPPEL DE RETARD (après la date)
        if ($this->shouldSendRetardReminder($now)) {
            $remindersToSend[] = 'retard';
        }

        return $remindersToSend;
    }

    /**
     * Vérifie si le rappel 48h avant doit être envoyé
     */
    private function shouldSend48hReminder(Carbon $now): bool
    {
        $rappelMoment = $this->date_planifiee->copy()->subHours(48);
        
        // Le rappel doit être envoyé entre 48h et 47h avant
        $startWindow = $rappelMoment;
        $endWindow = $rappelMoment->copy()->addHour();
        
        $isInWindow = $now->between($startWindow, $endWindow);
        $notSentYet = $this->last_reminder_type !== '48h' || 
                      !$this->last_reminder_sent_at ||
                      $this->last_reminder_sent_at < $startWindow;
        
        return $isInWindow && $notSentYet;
    }

    /**
     * Vérifie si le rappel 24h avant doit être envoyé
     */
    private function shouldSend24hReminder(Carbon $now): bool
    {
        $rappelMoment = $this->date_planifiee->copy()->subHours(24);
        
        $startWindow = $rappelMoment;
        $endWindow = $rappelMoment->copy()->addHour();
        
        $isInWindow = $now->between($startWindow, $endWindow);
        $notSentYet = $this->last_reminder_type !== '24h' || 
                      !$this->last_reminder_sent_at ||
                      $this->last_reminder_sent_at < $startWindow;
        
        return $isInWindow && $notSentYet;
    }

    /**
     * Vérifie si le rappel 1h avant doit être envoyé
     */
    private function shouldSend1hReminder(Carbon $now): bool
    {
        $rappelMoment = $this->date_planifiee->copy()->subHour();
        
        $startWindow = $rappelMoment;
        $endWindow = $rappelMoment->copy()->addMinutes(15);
        
        $isInWindow = $now->between($startWindow, $endWindow);
        $notSentYet = $this->last_reminder_type !== '1h' || 
                      !$this->last_reminder_sent_at ||
                      $this->last_reminder_sent_at < $startWindow;
        
        return $isInWindow && $notSentYet;
    }

    /**
     * Vérifie si un rappel de retard doit être envoyé
     * Envoi toutes les 24h tant que la tâche n'est pas terminée
     */
    private function shouldSendRetardReminder(Carbon $now): bool
    {
        // Seulement si la tâche est en retard et non terminée
        if ($this->date_planifiee >= $now || $this->terminee) {
            return false;
        }
        
        // Premier rappel de retard : envoyer immédiatement après la date
        if ($this->retard_reminder_count === 0) {
            return true;
        }
        
        // Rappels suivants : tous les jours à 9h
        $lastSent = $this->last_reminder_sent_at;
        if (!$lastSent) {
            return true;
        }
        
        $nextReminderAt = $lastSent->copy()->addDay()->startOfDay()->setTime(9, 0);
        
        return $now->gte($nextReminderAt);
    }

    /**
     * Envoie les rappels et met à jour les compteurs
     */
    public function sendReminders(): void
    {
        $remindersToSend = $this->getRemindersToSend();
        
        foreach ($remindersToSend as $reminderType) {
            $message = $this->getReminderMessage($reminderType);
            event(new \App\Events\TaskReminderNeeded($this, $reminderType, $message));
            
            // Mettre à jour le dernier rappel envoyé
            $this->update([
                'last_reminder_type' => $reminderType,
                'last_reminder_sent_at' => now(),
            ]);
            
            // Incrémenter le compteur pour les rappels de retard
            if ($reminderType === 'retard') {
                $this->increment('retard_reminder_count');
            }
        }
    }

    /**
     * Génère le message du rappel selon le type
     */
    public function getReminderMessage(string $type): string
    {
        $tacheNom = $this->titre;
        
        return match($type) {
            '48h' => "🔔 RAPPEL : Vous devez effectuer \"{$tacheNom}\" après-demain.",
            '24h' => "🔔 RAPPEL : Vous devez effectuer \"{$tacheNom}\" demain.",
            '1h' => "🔔 RAPPEL URGENT : Vous devez effectuer \"{$tacheNom}\" aujourd'hui.",
            'retard' => "⚠️ TÂCHE EN RETARD : \"{$tacheNom}\" n'a pas été effectuée. Veuillez la marquer comme terminée si c'est fait, ou planifier son exécution.",
            default => "Rappel : {$tacheNom}",
        };
    }

    /**
     * Marque la tâche comme terminée
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'terminee' => true,
            'date_realisee' => now(),
        ]);
    }

    /**
     * Marque la tâche comme terminée avec confirmation
     */
    public function complete(): void
    {
        $this->markAsCompleted();
    }

    /**
     * Vérifie si l'utilisateur peut modifier la tâche
     */
    public function canBeModifiedBy(int $userId): bool
    {
        return $this->user_id === $userId;
    }

    /**
     * Vérifie si l'utilisateur peut voir la tâche
     */
    public function canBeViewedBy(int $userId): bool
    {
        return $this->user_id === $userId ||
               $this->elevage->user_id === $userId;
    }

    /**
     * Scope pour les tâches à venir (non terminées avec date future)
     */
    public function scopeAvenir($query)
    {
        return $query->where('terminee', false)
            ->where('date_planifiee', '>=', now());
    }

    /**
     * Scope pour les tâches en retard
     */
    public function scopeRetard($query)
    {
        return $query->where('terminee', false)
            ->where('date_planifiee', '<', now());
    }

    /**
     * Scope pour les tâches terminées
     */
    public function scopeTerminees($query)
    {
        return $query->where('terminee', true);
    }

    /**
     * Scope pour les tâches par type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope pour les tâches par priorité
     */
    public function scopeByPriorite($query, string $priorite)
    {
        return $query->where('priorite', $priorite);
    }

    /**
     * Scope pour les tâches par date
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('date_planifiee', $date);
    }

    /**
     * Scope pour les tâches entre deux dates
     */
    public function scopeEntreDates($query, $debut, $fin)
    {
        return $query->whereBetween('date_planifiee', [$debut, $fin]);
    }

    /**
     * Scope pour les tâches du jour
     */
    public function scopeAujourdhui($query)
    {
        return $query->whereDate('date_planifiee', now()->toDateString());
    }

    /**
     * Scope pour les tâches de la semaine
     */
    public function scopeCetteSemaine($query)
    {
        return $query->whereBetween('date_planifiee', [
            now()->startOfWeek(),
            now()->endOfWeek(),
        ]);
    }

    /**
     * Scope pour les tâches du mois
     */
    public function scopeCeMois($query)
    {
        return $query->whereBetween('date_planifiee', [
            now()->startOfMonth(),
            now()->endOfMonth(),
        ]);
    }

    /**
     * Scope pour les tâches nécessitant un rappel
     */
    public function scopeRappelRequis($query)
    {
        return $query->where('terminee', false);
    }

}
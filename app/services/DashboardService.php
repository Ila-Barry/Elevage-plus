<?php
// app/Services/DashboardService.php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * Service DashboardService
 * 
 * Gère les calculs de statistiques et les opérations de cache pour le dashboard
 * 
 * @package App\Services
 */
class DashboardService
{
    /**
     * Durée de cache par défaut (secondes)
     */
    private const DEFAULT_CACHE_DURATION = 300;

    /**
     * Récupérer les KPIs d'un utilisateur avec cache
     *
     * @param User $user
     * @param bool $refreshCache
     * @return array
     */
    public function getUserKpis(User $user, bool $refreshCache = false): array
    {
        $cacheKey = "dashboard:kpis:user:{$user->id}";
        
        if ($refreshCache) {
            Cache::forget($cacheKey);
        }
        
        return Cache::remember($cacheKey, self::DEFAULT_CACHE_DURATION, function () use ($user) {
            return $this->calculateUserKpis($user);
        });
    }

    /**
     * Calculer les KPIs d'un utilisateur
     *
     * @param User $user
     * @return array
     */
    private function calculateUserKpis(User $user): array
    {
        // Requête optimisée avec une seule passe sur la base
        $stats = DB::table('users as u')
            ->leftJoin('elevages as e', 'u.id', '=', 'e.user_id')
            ->leftJoin('animaux as a', 'e.id', '=', 'a.elevage_id')
            ->leftJoin('publications as p', 'u.id', '=', 'p.user_id')
            ->where('u.id', $user->id)
            ->select([
                DB::raw('COUNT(DISTINCT e.id) as total_elevages'),
                DB::raw('COUNT(DISTINCT a.id) as total_animaux'),
                DB::raw('COUNT(DISTINCT p.id) as total_publications'),
                DB::raw('COALESCE(SUM(p.nbr_likes), 0) as total_likes'),
                DB::raw('COALESCE(SUM(p.nbr_vues), 0) as total_vues'),
            ])
            ->first();
        
        // Taux d'engagement
        $engagementRate = 0;
        if ($stats->total_vues > 0) {
            $engagementRate = round(($stats->total_likes / $stats->total_vues) * 100, 2);
        }
        
        return [
            'total_elevages' => (int) $stats->total_elevages,
            'total_animaux' => (int) $stats->total_animaux,
            'total_publications' => (int) $stats->total_publications,
            'total_likes' => (int) $stats->total_likes,
            'total_vues' => (int) $stats->total_vues,
            'engagement_rate' => $engagementRate,
        ];
    }

    /**
     * Récupérer les statistiques d'évolution
     *
     * @param User $user
     * @param string $type
     * @param int $months
     * @return array
     */
    public function getEvolutionData(User $user, string $type, int $months = 12): array
    {
        $cacheKey = "dashboard:evolution:{$type}:user:{$user->id}:{$months}";
        
        return Cache::remember($cacheKey, self::DEFAULT_CACHE_DURATION, function () use ($user, $type, $months) {
            return $this->calculateEvolutionData($user, $type, $months);
        });
    }

    /**
     * Calculer les données d'évolution
     *
     * @param User $user
     * @param string $type
     * @param int $months
     * @return array
     */
    private function calculateEvolutionData(User $user, string $type, int $months): array
    {
        $startDate = now()->subMonths($months);
        
        $query = DB::table($this->getTableForType($type))
            ->join('elevages', $this->getJoinCondition($type), '=', 'elevages.id')
            ->where('elevages.user_id', $user->id)
            ->where($this->getDateColumnForType($type), '>=', $startDate)
            ->select([
                DB::raw('DATE_FORMAT(' . $this->getDateColumnForType($type) . ', "%Y-%m") as month'),
                DB::raw('COUNT(*) as count'),
            ])
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        return [
            'labels' => $query->pluck('month')->toArray(),
            'values' => $query->pluck('count')->toArray(),
        ];
    }

    /**
     * Obtenir la table selon le type de données
     *
     * @param string $type
     * @return string
     */
    private function getTableForType(string $type): string
    {
        return match ($type) {
            'animaux' => 'animaux',
            'publications' => 'publications',
            'taches' => 'taches',
            default => 'animaux',
        };
    }

    /**
     * Obtenir la condition de jointure
     *
     * @param string $type
     * @return string
     */
    private function getJoinCondition(string $type): string
    {
        if ($type === 'taches') {
            return 'taches.animal_id';
        }
        return $type . '.elevage_id';
    }

    /**
     * Obtenir la colonne de date selon le type
     *
     * @param string $type
     * @return string
     */
    private function getDateColumnForType(string $type): string
    {
        return $type . '.created_at';
    }
}
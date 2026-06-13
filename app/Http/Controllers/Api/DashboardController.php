<?php
// app/Http/Controllers/Api/DashboardController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

/**
 * Contrôleur DashboardController
 * 
 * Gère les tableaux de bord et les statistiques de la plateforme
 * 
 * @package App\Http\Controllers\Api
 */
class DashboardController extends Controller
{
    use ApiResponseTrait;

    /**
     * Service pour les statistiques du dashboard
     *
     * @var DashboardService
     */
    protected DashboardService $dashboardService;

    /**
     * Durée de cache pour les statistiques (secondes)
     */
    private const CACHE_DURATION = 300; // 5 minutes

    /**
     * Constructeur avec injection de dépendances
     *
     * @param DashboardService $dashboardService
     */
    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
        
        // Toutes les méthodes nécessitent une authentification
        $this->middleware('auth:api');
    }

    /**
     * Récupérer toutes les statistiques du dashboard principal
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            
            // Cache key spécifique à l'utilisateur
            $cacheKey = "dashboard:user:{$user->id}:main";
            
            $dashboardData = Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($user) {
                return [
                    'kpis' => $this->getKpis($user),
                    'elevages' => $this->getElevagesStats($user),
                    'animaux' => $this->getAnimauxStats($user),
                    'taches' => $this->getTachesStats($user),
                    'stocks' => $this->getStocksStats($user),
                    'publications' => $this->getPublicationsStats($user),
                    'recent_activity' => $this->getRecentActivity($user),
                ];
            });
            
            return $this->successResponse($dashboardData, 'Données du dashboard récupérées avec succès.');
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération du dashboard: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse('Erreur lors de la récupération des données du dashboard.', 500);
        }
    }

    /**
     * Récupérer les KPIs principaux
     *
     * @param User $user
     * @return array
     */
    private function getKpis($user): array
    {
        // Optimisation: Utiliser des requêtes agrégées avec sous-requêtes
        $kpis = DB::table('users as u')
            ->leftJoin('elevages as e', 'u.id', '=', 'e.user_id')
            ->leftJoin('animaux as a', 'e.id', '=', 'a.elevage_id')
            ->leftJoin('publications as p', 'u.id', '=', 'p.user_id')
            ->leftJoin('taches as t', function($join) {
                $join->on('a.id', '=', 't.animal_id')
                    ->orOn('e.id', '=', 't.elevage_id');
            })
            ->where('u.id', $user->id)
            ->select([
                DB::raw('COUNT(DISTINCT e.id) as total_elevages'),
                DB::raw('COUNT(DISTINCT a.id) as total_animaux'),
                DB::raw('COUNT(DISTINCT p.id) as total_publications'),
                DB::raw('SUM(CASE WHEN t.terminee = 0 AND t.date_planifiee <= NOW() THEN 1 ELSE 0 END) as taches_retard'),
                DB::raw('SUM(CASE WHEN t.terminee = 0 AND t.date_planifiee > NOW() THEN 1 ELSE 0 END) as taches_a_venir'),
                DB::raw('SUM(CASE WHEN t.terminee = 1 AND t.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as taches_realisees_30j'),
            ])
            ->first();
        
        return [
            'total_elevages' => (int) ($kpis->total_elevages ?? 0),
            'total_animaux' => (int) ($kpis->total_animaux ?? 0),
            'total_publications' => (int) ($kpis->total_publications ?? 0),
            'taches_retard' => (int) ($kpis->taches_retard ?? 0),
            'taches_a_venir' => (int) ($kpis->taches_a_venir ?? 0),
            'taches_realisees_30j' => (int) ($kpis->taches_realisees_30j ?? 0),
            'taux_realisation' => $this->calculateCompletionRate($user),
            'score_activite' => $this->calculateActivityScore($user),
        ];
    }

    /**
     * Calculer le taux de réalisation des tâches
     *
     * @param User $user
     * @return float
     */
    private function calculateCompletionRate($user): float
    {
        $total = DB::table('taches')
            ->join('animaux', 'taches.animal_id', '=', 'animaux.id')
            ->join('elevages', 'animaux.elevage_id', '=', 'elevages.id')
            ->where('elevages.user_id', $user->id)
            ->count();
        
        if ($total === 0) {
            return 0;
        }
        
        $completed = DB::table('taches')
            ->join('animaux', 'taches.animal_id', '=', 'animaux.id')
            ->join('elevages', 'animaux.elevage_id', '=', 'elevages.id')
            ->where('elevages.user_id', $user->id)
            ->where('taches.terminee', true)
            ->count();
        
        return round(($completed / $total) * 100, 2);
    }

    /**
     * Calculer le score d'activité (0-100)
     *
     * @param User $user
     * @return int
     */
    private function calculateActivityScore($user): int
    {
        $score = 0;
        
        // Publications récentes (max 30 points)
        $recentPosts = DB::table('publications')
            ->where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        $score += min(30, $recentPosts * 3);
        
        // Tâches complétées (max 30 points)
        $tasksCompleted = DB::table('taches')
            ->join('animaux', 'taches.animal_id', '=', 'animaux.id')
            ->join('elevages', 'animaux.elevage_id', '=', 'elevages.id')
            ->where('elevages.user_id', $user->id)
            ->where('taches.terminee', true)
            ->where('taches.updated_at', '>=', now()->subDays(30))
            ->count();
        $score += min(30, $tasksCompleted * 2);
        
        // Connexions récentes (max 20 points)
        $lastLogin = $user->last_login_at;
        if ($lastLogin && $lastLogin >= now()->subDays(7)) {
            $score += 20;
        } elseif ($lastLogin && $lastLogin >= now()->subDays(30)) {
            $score += 10;
        }
        
        // Interactions (likes/comments) (max 20 points)
        $interactions = DB::table('likes')
            ->where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        
        $comments = DB::table('commentaires')
            ->where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        
        $score += min(20, ($interactions + $comments) / 2);
        
        return min(100, $score);
    }

    /**
     * Statistiques détaillées des élevages
     *
     * @param User $user
     * @return array
     */
    private function getElevagesStats($user): array
    {
        // Distribution par type d'élevage avec count d'animaux
        $distributionParType = DB::table('elevages')
            ->leftJoin('animaux', 'elevages.id', '=', 'animaux.elevage_id')
            ->where('elevages.user_id', $user->id)
            ->groupBy('elevages.type_elevage')
            ->select([
                'elevages.type_elevage',
                DB::raw('COUNT(DISTINCT elevages.id) as nombre_elevages'),
                DB::raw('COUNT(animaux.id) as total_animaux'),
                DB::raw('AVG(animaux.poids) as poids_moyen'),
            ])
            ->get();
        
        // Évolution mensuelle des élevages (dernier 12 mois)
        $evolution = DB::table('elevages')
            ->where('user_id', $user->id)
            ->where('elevages.created_at', '>=', now()->subMonths(12))
            ->select([
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as mois'),
                DB::raw('COUNT(*) as nombre'),
            ])
            ->groupBy('mois')
            ->orderBy('mois')
            ->get();
        
        // Superficie totale
        $superficieTotale = DB::table('elevages')
            ->where('user_id', $user->id)
            ->sum('superficie');
        
        // Élevage avec le plus d'animaux
        $topElevage = DB::table('elevages')
            ->leftJoin('animaux', 'elevages.id', '=', 'animaux.elevage_id')
            ->where('elevages.user_id', $user->id)
            ->groupBy('elevages.id', 'elevages.nom')
            ->select([
                'elevages.id',
                'elevages.nom',
                DB::raw('COUNT(animaux.id) as total_animaux'),
            ])
            ->orderByDesc('total_animaux')
            ->first();
        
        return [
            'distribution_par_type' => $distributionParType,
            'evolution_mensuelle' => $evolution,
            'superficie_totale' => round($superficieTotale, 2),
            'nombre_total' => $distributionParType->sum('nombre_elevages'),
            'top_elevage' => $topElevage ? [
                'id' => $topElevage->id,
                'nom' => $topElevage->nom,
                'total_animaux' => $topElevage->total_animaux,
            ] : null,
        ];
    }

    /**
     * Statistiques détaillées des animaux
     *
     * @param User $user
     * @return array
     */
    private function getAnimauxStats($user): array
    {
        // Distribution par espèce
        $distributionParEspece = DB::table('animaux')
            ->join('elevages', 'animaux.elevage_id', '=', 'elevages.id')
            ->where('elevages.user_id', $user->id)
            ->groupBy('animaux.espece')
            ->select([
                'animaux.espece',
                DB::raw('COUNT(*) as nombre'),
                DB::raw('AVG(animaux.poids) as poids_moyen'),
                DB::raw('MIN(animaux.poids) as poids_min'),
                DB::raw('MAX(animaux.poids) as poids_max'),
            ])
            ->get();
        
        // Répartition par âge
        $repartitionAge = DB::table('animaux')
            ->join('elevages', 'animaux.elevage_id', '=', 'elevages.id')
            ->where('elevages.user_id', $user->id)
            ->select([
                DB::raw('CASE 
                    WHEN TIMESTAMPDIFF(MONTH, animaux.date_naissance, CURDATE()) <= 6 THEN "Moins de 6 mois"
                    WHEN TIMESTAMPDIFF(MONTH, animaux.date_naissance, CURDATE()) <= 12 THEN "6-12 mois"
                    WHEN TIMESTAMPDIFF(MONTH, animaux.date_naissance, CURDATE()) <= 24 THEN "1-2 ans"
                    WHEN TIMESTAMPDIFF(MONTH, animaux.date_naissance, CURDATE()) <= 60 THEN "2-5 ans"
                    ELSE "Plus de 5 ans"
                END as tranche_age'),
                DB::raw('COUNT(*) as nombre'),
            ])
            ->groupBy('tranche_age')
            ->get();
        
        // Évolution mensuelle des naissances
        $evolutionNaissances = DB::table('animaux')
            ->join('elevages', 'animaux.elevage_id', '=', 'elevages.id')
            ->where('elevages.user_id', $user->id)
            ->where('elevages.created_at', '>=', now()->subMonths(12))
            ->select([
                DB::raw('DATE_FORMAT(animaux.created_at, "%Y-%m") as mois'),
                DB::raw('COUNT(*) as nombre'),
            ])
            ->groupBy('mois')
            ->orderBy('mois')
            ->get();
        
        // Statistiques de santé
        $santeStats = DB::table('animaux')
            ->join('elevages', 'animaux.elevage_id', '=', 'elevages.id')
            ->where('elevages.user_id', $user->id)
            ->select([
                DB::raw('SUM(CASE WHEN animaux.statut_sanitaire = "sain" THEN 1 ELSE 0 END) as sains'),
                DB::raw('SUM(CASE WHEN animaux.statut_sanitaire = "malade" THEN 1 ELSE 0 END) as malades'),
                DB::raw('SUM(CASE WHEN animaux.statut_sanitaire = "traitement" THEN 1 ELSE 0 END) as en_traitement'),
                DB::raw('SUM(CASE WHEN animaux.statut_sanitaire = "convalescent" THEN 1 ELSE 0 END) as convalescents'),
                DB::raw('AVG(CASE WHEN animaux.statut_sanitaire = "malade" THEN 1 ELSE 0 END) * 100 as taux_maladie'),
            ])
            ->first();
        
        return [
            'distribution_par_espece' => $distributionParEspece,
            'repartition_age' => $repartitionAge,
            'evolution_naissances' => $evolutionNaissances,
            'total_animaux' => $distributionParEspece->sum('nombre'),
            'sante' => [
                'sains' => (int) ($santeStats->sains ?? 0),
                'malades' => (int) ($santeStats->malades ?? 0),
                'en_traitement' => (int) ($santeStats->en_traitement ?? 0),
                'convalescents' => (int) ($santeStats->convalescents ?? 0),
                'taux_maladie' => round($santeStats->taux_maladie ?? 0, 2),
            ],
        ];
    }

    /**
     * Statistiques des tâches
     *
     * @param User $user
     * @return array
     */
    private function getTachesStats($user): array
    {
        // Tâches par type
        $tachesParType = DB::table('taches')
            ->join('animaux', 'taches.animal_id', '=', 'animaux.id')
            ->join('elevages', 'animaux.elevage_id', '=', 'elevages.id')
            ->where('elevages.user_id', $user->id)
            ->groupBy('taches.type')
            ->select([
                'taches.type',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN taches.terminee = 1 THEN 1 ELSE 0 END) as realisees'),
                DB::raw('SUM(CASE WHEN taches.terminee = 0 AND taches.date_planifiee <= NOW() THEN 1 ELSE 0 END) as retard'),
            ])
            ->get();
        
        // Tâches des 30 derniers jours
        $taches30Jours = DB::table('taches')
            ->join('animaux', 'taches.animal_id', '=', 'animaux.id')
            ->join('elevages', 'animaux.elevage_id', '=', 'elevages.id')
            ->where('elevages.user_id', $user->id)
            ->where('taches.date_planifiee', '>=', now()->subDays(30))
            ->select([
                DB::raw('DATE(taches.date_planifiee) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN taches.terminee = 1 THEN 1 ELSE 0 END) as realisees'),
            ])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Prochaines tâches à venir
        $prochainesTaches = DB::table('taches')
            ->join('animaux', 'taches.animal_id', '=', 'animaux.id')
            ->join('elevages', 'animaux.elevage_id', '=', 'elevages.id')
            ->where('elevages.user_id', $user->id)
            ->where('taches.terminee', false)
            ->where('taches.date_planifiee', '>', now())
            ->orderBy('taches.date_planifiee')
            ->limit(10)
            ->select([
                'taches.id',
                'taches.type',
                'taches.date_planifiee',
                'animaux.nom as animal_nom',
                'elevages.nom as elevage_nom',
            ])
            ->get();
        
        return [
            'par_type' => $tachesParType,
            'taches_30_jours' => $taches30Jours,
            'prochaines_taches' => $prochainesTaches,
            'taux_realisation_global' => $this->calculateCompletionRate($user),
        ];
    }

    /**
     * Statistiques des stocks
     *
     * @param User $user
     * @return array
     */
    private function getStocksStats($user): array
    {
        // Produits critiques (stock bas)
        $produitsCritiques = DB::table('produits')
            ->join('elevages', 'produits.elevage_id', '=', 'elevages.id')
            ->where('elevages.user_id', $user->id)
            ->whereRaw('produits.quantite <= produits.seuil_alerte')
            ->select([
                'produits.id',
                'produits.nom',
                'produits.categorie',
                'produits.quantite',
                'produits.seuil_alerte',
                DB::raw('(produits.seuil_alerte - produits.quantite) as manquant'),
            ])
            ->orderByRaw('(produits.seuil_alerte - produits.quantite) DESC')
            ->limit(10)
            ->get();
        
        // Valeur totale du stock (estimation)
        $valeurStock = DB::table('produits')
            ->join('elevages', 'produits.elevage_id', '=', 'elevages.id')
            ->where('elevages.user_id', $user->id)
            ->sum(DB::raw('produits.quantite * COALESCE(produits.prix_unitaire, 0)'));
        
        // Mouvements des 30 derniers jours
        $mouvements30Jours = DB::table('stocks')
            ->join('produits', 'stocks.produit_id', '=', 'produits.id')
            ->join('elevages', 'produits.elevage_id', '=', 'elevages.id')
            ->where('elevages.user_id', $user->id)
            ->where('stocks.created_at', '>=', now()->subDays(30))
            ->select([
                DB::raw('DATE(stocks.created_at) as date'),
                DB::raw('SUM(CASE WHEN stocks.type = "entree" THEN stocks.quantite ELSE 0 END) as entrees'),
                DB::raw('SUM(CASE WHEN stocks.type = "sortie" THEN stocks.quantite ELSE 0 END) as sorties'),
            ])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        return [
            'produits_critiques' => $produitsCritiques,
            'nombre_produits_critiques' => $produitsCritiques->count(),
            'valeur_totale_stock' => round($valeurStock, 2),
            'mouvements_30_jours' => $mouvements30Jours,
        ];
    }

    /**
     * Statistiques des publications
     *
     * @param User $user
     * @return array
     */
    private function getPublicationsStats($user): array
    {
        // Publications par catégorie
        $publicationsParCategorie = DB::table('publications')
            ->where('user_id', $user->id)
            ->where('statut', 'publiee')
            ->groupBy('categorie')
            ->select([
                'categorie',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(nbr_likes) as total_likes'),
                DB::raw('AVG(nbr_likes) as likes_moyens'),
                DB::raw('SUM(nbr_vues) as total_vues'),
            ])
            ->get();
        
        // Engagement des 30 derniers jours
        $engagement30Jours = DB::table('publications')
            ->where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->select([
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as publications'),
                DB::raw('SUM(nbr_likes) as likes'),
                DB::raw('SUM(nbr_vues) as vues'),
            ])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Top publications
        $topPublications = DB::table('publications')
            ->where('user_id', $user->id)
            ->where('statut', 'publiee')
            ->orderByDesc('nbr_likes')
            ->limit(5)
            ->select([
                'id',
                'titre',
                'categorie',
                'nbr_likes',
                'nbr_vues',
                'created_at',
            ])
            ->get();
        
        return [
            'par_categorie' => $publicationsParCategorie,
            'engagement_30_jours' => $engagement30Jours,
            'top_publications' => $topPublications,
            'total_publications' => $publicationsParCategorie->sum('total'),
            'total_likes' => $publicationsParCategorie->sum('total_likes'),
            'total_vues' => $publicationsParCategorie->sum('total_vues'),
            'taux_engagement' => $this->calculateEngagementRate($user),
        ];
    }

    /**
     * Calculer le taux d'engagement des publications
     *
     * @param User $user
     * @return float
     */
    private function calculateEngagementRate($user): float
    {
        $stats = DB::table('publications')
            ->where('user_id', $user->id)
            ->where('statut', 'publiee')
            ->select([
                DB::raw('SUM(nbr_likes) as total_likes'),
                DB::raw('SUM(nbr_vues) as total_vues'),
                DB::raw('COUNT(*) as total_pubs'),
            ])
            ->first();
        
        if (!$stats || $stats->total_vues == 0) {
            return 0;
        }
        
        return round(($stats->total_likes / $stats->total_vues) * 100, 2);
    }

    /**
     * Récupérer l'activité récente
     *
     * @param User $user
     * @return array
     */
    private function getRecentActivity($user): array
    {
        // Dernières publications
        $dernieresPublications = DB::table('publications')
            ->where('user_id', $user->id)
            ->where('statut', 'publiee')
            ->orderByDesc('created_at')
            ->limit(5)
            ->select([
                'id',
                'titre',
                'categorie',
                'nbr_likes',
                'created_at',
            ])
            ->get();
        
        // Derniers mouvements de stock
        $derniersMouvements = DB::table('stocks')
            ->join('produits', 'stocks.produit_id', '=', 'produits.id')
            ->join('elevages', 'produits.elevage_id', '=', 'elevages.id')
            ->where('elevages.user_id', $user->id)
            ->orderByDesc('stocks.created_at')
            ->limit(5)
            ->select([
                'stocks.type',
                'stocks.quantite',
                'stocks.created_at',
                'produits.nom as produit_nom',
            ])
            ->get();
        
        // Dernières tâches complétées
        $dernieresTaches = DB::table('taches')
            ->join('animaux', 'taches.animal_id', '=', 'animaux.id')
            ->join('elevages', 'animaux.elevage_id', '=', 'elevages.id')
            ->where('elevages.user_id', $user->id)
            ->where('taches.terminee', true)
            ->orderByDesc('taches.updated_at')
            ->limit(5)
            ->select([
                'taches.type',
                'taches.date_realisee',
                'animaux.nom as animal_nom',
            ])
            ->get();
        
        return [
            'dernieres_publications' => $dernieresPublications,
            'derniers_mouvements_stock' => $derniersMouvements,
            'dernieres_taches' => $dernieresTaches,
        ];
    }

    /**
     * Récupérer les statistiques globales pour admin
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function adminStats(Request $request): JsonResponse
    {
        try {
            // Vérifier que l'utilisateur est admin
            $user = Auth::user();
            if ($user->role !== 'admin') {
                return $this->forbiddenResponse('Accès réservé aux administrateurs.');
            }
            
            $cacheKey = "dashboard:admin:global";
            
            $adminStats = Cache::remember($cacheKey, self::CACHE_DURATION, function () {
                return [
                    'utilisateurs' => $this->getAdminUserStats(),
                    'elevages' => $this->getAdminElevageStats(),
                    'animaux' => $this->getAdminAnimalStats(),
                    'publications' => $this->getAdminPublicationStats(),
                    'taches' => $this->getAdminTaskStats(),
                    'stocks' => $this->getAdminStockStats(),
                ];
            });
            
            return $this->successResponse($adminStats, 'Statistiques globales récupérées avec succès.');
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération des stats admin: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la récupération des statistiques globales.', 500);
        }
    }

    /**
     * Statistiques utilisateurs pour admin
     *
     * @return array
     */
    private function getAdminUserStats(): array
    {
        $total = DB::table('users')->count();
        $actifs = DB::table('users')->where('status', 'active')->count();
        $bannis = DB::table('users')->where('status', 'bannie')->count();
        
        // Nouveaux utilisateurs par mois
        $nouveauxParMois = DB::table('users')
            ->where('elevages.created_at', '>=', now()->subMonths(12))
            ->select([
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as mois'),
                DB::raw('COUNT(*) as nombre'),
            ])
            ->groupBy('mois')
            ->orderBy('mois')
            ->get();
        
        return [
            'total' => $total,
            'actifs' => $actifs,
            'bannis' => $bannis,
            'taux_activation' => $total > 0 ? round(($actifs / $total) * 100, 2) : 0,
            'nouveaux_par_mois' => $nouveauxParMois,
        ];
    }

    /**
     * Statistiques élevages pour admin
     *
     * @return array
     */
    private function getAdminElevageStats(): array
    {
        $total = DB::table('elevages')->count();
        $superficieTotale = DB::table('elevages')->sum('superficie');
        
        $distributionType = DB::table('elevages')
            ->groupBy('type_elevage')
            ->select([
                'type_elevage',
                DB::raw('COUNT(*) as nombre'),
            ])
            ->get();
        
        return [
            'total' => $total,
            'superficie_totale' => round($superficieTotale, 2),
            'distribution_par_type' => $distributionType,
        ];
    }

    /**
     * Statistiques animaux pour admin
     *
     * @return array
     */
    private function getAdminAnimalStats(): array
    {
        $total = DB::table('animaux')->count();
        
        $distributionEspece = DB::table('animaux')
            ->groupBy('espece')
            ->select([
                'espece',
                DB::raw('COUNT(*) as nombre'),
            ])
            ->get();
        
        return [
            'total' => $total,
            'distribution_par_espece' => $distributionEspece,
        ];
    }

    /**
     * Statistiques publications pour admin
     *
     * @return array
     */
    private function getAdminPublicationStats(): array
    {
        $total = DB::table('publications')->count();
        $totalLikes = DB::table('publications')->sum('nbr_likes');
        $totalVues = DB::table('publications')->sum('nbr_vues');
        
        $publicationsParMois = DB::table('publications')
            ->where('elevages.created_at', '>=', now()->subMonths(12))
            ->select([
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as mois'),
                DB::raw('COUNT(*) as nombre'),
            ])
            ->groupBy('mois')
            ->orderBy('mois')
            ->get();
        
        return [
            'total' => $total,
            'total_likes' => $totalLikes,
            'total_vues' => $totalVues,
            'taux_engagement' => $totalVues > 0 ? round(($totalLikes / $totalVues) * 100, 2) : 0,
            'publications_par_mois' => $publicationsParMois,
        ];
    }

    /**
     * Statistiques tâches pour admin
     *
     * @return array
     */
    private function getAdminTaskStats(): array
    {
        $total = DB::table('taches')->count();
        $realisees = DB::table('taches')->where('terminee', true)->count();
        $retard = DB::table('taches')
            ->where('terminee', false)
            ->where('date_planifiee', '<', now())
            ->count();
        
        return [
            'total' => $total,
            'realisees' => $realisees,
            'retard' => $retard,
            'taux_realisation' => $total > 0 ? round(($realisees / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Statistiques stocks pour admin
     *
     * @return array
     */
    private function getAdminStockStats(): array
    {
        $totalProduits = DB::table('produits')->count();
        $produitsCritiques = DB::table('produits')
            ->whereRaw('quantite <= seuil_alerte')
            ->count();
        
        $valeurTotale = DB::table('produits')
            ->sum(DB::raw('quantite * COALESCE(prix_unitaire, 0)'));
        
        return [
            'total_produits' => $totalProduits,
            'produits_critiques' => $produitsCritiques,
            'valeur_totale_stock' => round($valeurTotale, 2),
            'taux_critique' => $totalProduits > 0 ? round(($produitsCritiques / $totalProduits) * 100, 2) : 0,
        ];
    }

    /**
     * Forcer le rafraîchissement du cache
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function refreshCache(Request $request): JsonResponse
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            
            $cacheKey = "dashboard:user:{$user->id}:main";
            Cache::forget($cacheKey);
            
            if ($user->role === 'admin') {
                Cache::forget('dashboard:admin:global');
            }
            
            return $this->successResponse(null, 'Cache du dashboard rafraîchi avec succès.');
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors du rafraîchissement du cache: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors du rafraîchissement du cache.', 500);
        }
    }

    /**
     * Récupérer les statistiques pour les graphiques
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function chartData(Request $request): JsonResponse
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            
            $period = $request->get('period', 'month'); // week, month, year
            
            $data = [
                'animaux_evolution' => $this->getAnimalEvolution($user, $period),
                'taches_realisation' => $this->getTaskRealisation($user, $period),
                'publications_engagement' => $this->getPublicationEngagement($user, $period),
                'stock_mouvements' => $this->getStockMovements($user, $period),
            ];
            
            return $this->successResponse($data, 'Données des graphiques récupérées avec succès.');
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération des données graphiques: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la récupération des données graphiques.', 500);
        }
    }

    /**
     * Évolution des animaux sur la période
     *
     * @param User $user
     * @param string $period
     * @return array
     */
    private function getAnimalEvolution($user, string $period): array
    {
        $interval = $this->getDateInterval($period);
        
        $data = DB::table('animaux')
            ->join('elevages', 'animaux.elevage_id', '=', 'elevages.id')
            ->where('elevages.user_id', $user->id)
            ->where('animaux.created_at', '>=', $interval['start'])
            ->select([
                DB::raw($interval['group_by'] . ' as periode'),
                DB::raw('COUNT(*) as nombre'),
            ])
            ->groupBy('periode')
            ->orderBy('periode')
            ->get();
        
        return [
            'labels' => $data->pluck('periode')->toArray(),
            'values' => $data->pluck('nombre')->toArray(),
            'title' => 'Évolution du cheptel',
        ];
    }

    /**
     * Réalisation des tâches sur la période
     *
     * @param User $user
     * @param string $period
     * @return array
     */
    private function getTaskRealisation($user, string $period): array
    {
        $interval = $this->getDateInterval($period);
        
        $data = DB::table('taches')
            ->join('animaux', 'taches.animal_id', '=', 'animaux.id')
            ->join('elevages', 'animaux.elevage_id', '=', 'elevages.id')
            ->where('elevages.user_id', $user->id)
            ->where('taches.date_planifiee', '>=', $interval['start'])
            ->select([
                DB::raw($interval['group_by'] . ' as periode'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN terminee = 1 THEN 1 ELSE 0 END) as realisees'),
            ])
            ->groupBy('periode')
            ->orderBy('periode')
            ->get();
        
        return [
            'labels' => $data->pluck('periode')->toArray(),
            'total' => $data->pluck('total')->toArray(),
            'realisees' => $data->pluck('realisees')->toArray(),
            'title' => 'Réalisation des tâches',
        ];
    }

    /**
     * Engagement des publications sur la période
     *
     * @param User $user
     * @param string $period
     * @return array
     */
    private function getPublicationEngagement($user, string $period): array
    {
        $interval = $this->getDateInterval($period);
        
        $data = DB::table('publications')
            ->where('user_id', $user->id)
            ->where('created_at', '>=', $interval['start'])
            ->select([
                DB::raw($interval['group_by'] . ' as periode'),
                DB::raw('COUNT(*) as publications'),
                DB::raw('SUM(nbr_likes) as likes'),
                DB::raw('SUM(nbr_vues) as vues'),
            ])
            ->groupBy('periode')
            ->orderBy('periode')
            ->get();
        
        return [
            'labels' => $data->pluck('periode')->toArray(),
            'publications' => $data->pluck('publications')->toArray(),
            'likes' => $data->pluck('likes')->toArray(),
            'vues' => $data->pluck('vues')->toArray(),
            'title' => 'Engagement des publications',
        ];
    }

    /**
     * Mouvements de stock sur la période
     *
     * @param User $user
     * @param string $period
     * @return array
     */
    private function getStockMovements($user, string $period): array
    {
        $interval = $this->getDateInterval($period);
        
        $data = DB::table('stocks')
            ->join('produits', 'stocks.produit_id', '=', 'produits.id')
            ->join('elevages', 'produits.elevage_id', '=', 'elevages.id')
            ->where('elevages.user_id', $user->id)
            ->where('stocks.created_at', '>=', $interval['start'])
            ->select([
                DB::raw($interval['group_by'] . ' as periode'),
                DB::raw('SUM(CASE WHEN type = "entree" THEN quantite ELSE 0 END) as entrees'),
                DB::raw('SUM(CASE WHEN type = "sortie" THEN quantite ELSE 0 END) as sorties'),
            ])
            ->groupBy('periode')
            ->orderBy('periode')
            ->get();
        
        return [
            'labels' => $data->pluck('periode')->toArray(),
            'entrees' => $data->pluck('entrees')->toArray(),
            'sorties' => $data->pluck('sorties')->toArray(),
            'title' => 'Mouvements de stock',
        ];
    }

    /**
     * Obtenir l'intervalle de dates selon la période
     *
     * @param string $period
     * @return array
     */
    private function getDateInterval(string $period): array
    {
        switch ($period) {
            case 'week':
                return [
                    'start' => now()->subWeeks(4),
                    'group_by' => 'DATE(created_at)',
                ];
            case 'year':
                return [
                    'start' => now()->subYears(12),
                    'group_by' => 'DATE_FORMAT(created_at, "%Y-%m")',
                ];
            case 'month':
            default:
                return [
                    'start' => now()->subMonths(12),
                    'group_by' => 'DATE_FORMAT(created_at, "%Y-%m")',
                ];
        }
    }
}
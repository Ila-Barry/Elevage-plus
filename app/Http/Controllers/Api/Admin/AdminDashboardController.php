<?php
// app/Http/Controllers/Api/Admin/AdminDashboardController.php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\DashboardResource;
use App\Models\User;
use App\Models\Publication;
use App\Models\Report;
use App\Models\Commentaire;
use App\Models\Like;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller AdminDashboardController
 * 
 * Gère toutes les données du tableau de bord admin
 */
class AdminDashboardController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware(['auth:api', 'admin']);
    }

    /**
     * Récupérer les indicateurs clés (KPIs)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function kpis(Request $request)
    {
        $totalUsers = User::count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $activeUsers = User::where('status', 'active')->count();
        $bannedUsers = User::where('status', 'bannie')->count();

        $totalPublications = Publication::count();
        $reportedPublications = Publication::where('statut', 'signalee')->count();
        $blockedPublications = Publication::where('statut', 'bloquee')->count();

        $pendingReports = Report::where('statut', 'en_attente')->count();

        // Taux d'engagement global
        $totalLikes = Like::count();
        $totalComments = Commentaire::count();
        $engagementRate = $totalPublications > 0 
            ? round(($totalLikes + $totalComments) / $totalPublications, 2)
            : 0;

        return $this->successResponse([
            'utilisateurs' => [
                'total' => $totalUsers,
                'nouveaux_ce_mois' => $newUsersThisMonth,
                'actifs' => $activeUsers,
                'bannis' => $bannedUsers,
                'croissance' => $this->calculateGrowth(User::class),
            ],
            'publications' => [
                'total' => $totalPublications,
                'signalees' => $reportedPublications,
                'bloquees' => $blockedPublications,
                'croissance' => $this->calculateGrowth(Publication::class),
            ],
            'signalements' => [
                'en_attente' => $pendingReports,
                'total' => Report::count(),
            ],
            'engagement' => [
                'taux_global' => $engagementRate,
                'moyenne_likes' => $totalPublications > 0 ? round($totalLikes / $totalPublications, 1) : 0,
                'moyenne_commentaires' => $totalPublications > 0 ? round($totalComments / $totalPublications, 1) : 0,
            ],
        ]);
    }

    /**
     * Récupérer les données pour les graphiques d'évolution
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function evolution(Request $request)
    {
        $months = 12;
        $labels = [];
        $usersData = [];
        $publicationsData = [];
        $reportsData = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('M');
            $labels[] = $month;

            $usersData[] = User::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();

            $publicationsData[] = Publication::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();

            $reportsData[] = Report::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
        }

        return $this->successResponse([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Nouveaux utilisateurs',
                    'data' => $usersData,
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
                [
                    'label' => 'Nouvelles publications',
                    'data' => $publicationsData,
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                ],
                [
                    'label' => 'Signalements',
                    'data' => $reportsData,
                    'borderColor' => '#EF4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                ],
            ],
        ]);
    }

    /**
     * Récupérer les données de répartition
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function repartition(Request $request)
    {
        // Répartition des utilisateurs par rôle
        $usersByRole = User::select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->get()
            ->map(function ($item) {
                $labels = [
                    'admin' => 'Administrateurs',
                    'eleveur' => 'Éleveurs',
                    'visiteur' => 'Visiteurs',
                ];
                return [
                    'label' => $labels[$item->role] ?? $item->role,
                    'value' => $item->total,
                ];
            });

        // Répartition des publications par catégorie
        $publicationsByCategory = Publication::select('categorie', DB::raw('count(*) as total'))
            ->groupBy('categorie')
            ->get()
            ->map(function ($item) {
                $labels = [
                    'conseil' => '🌾 Conseils',
                    'experience' => '💡 Expériences',
                    'alerte' => '⚠️ Alertes',
                ];
                return [
                    'label' => $labels[$item->categorie] ?? $item->categorie,
                    'value' => $item->total,
                ];
            });

        // Répartition des publications par statut
        $publicationsByStatus = Publication::select('statut', DB::raw('count(*) as total'))
            ->groupBy('statut')
            ->get()
            ->map(function ($item) {
                $labels = [
                    'publiee' => '✅ Publiées',
                    'signalee' => '⚠️ Signalées',
                    'bloquee' => '🔴 Bloquées',
                ];
                return [
                    'label' => $labels[$item->statut] ?? $item->statut,
                    'value' => $item->total,
                ];
            });

        return $this->successResponse([
            'utilisateurs_par_role' => $usersByRole,
            'publications_par_categorie' => $publicationsByCategory,
            'publications_par_statut' => $publicationsByStatus,
        ]);
    }

    /**
     * Récupérer les activités récentes
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activitesRecentes(Request $request)
    {
        $limit = $request->get('limit', 10);

        $activities = [];

        // Nouveaux signalements
        $newReports = Report::with(['user', 'publication'])
            ->where('statut', 'en_attente')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($report) {
                return [
                    'type' => 'signalement',
                    'message' => "Nouveau signalement de {$report->user->name} sur '{$report->publication->titre}'",
                    'icon' => '⚠️',
                    'color' => 'red',
                    'url' => "/admin/reports/{$report->id}",
                    'created_at' => $report->created_at->diffForHumans(),
                ];
            });

        // Nouvelles publications
        $newPublications = Publication::with(['user'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($pub) {
                return [
                    'type' => 'publication',
                    'message' => "Nouvelle publication de {$pub->user->name} : '{$pub->titre}'",
                    'icon' => '📝',
                    'color' => 'blue',
                    'url' => "/admin/publications/{$pub->id}",
                    'created_at' => $pub->created_at->diffForHumans(),
                ];
            });

        // Nouveaux utilisateurs
        $newUsers = User::latest()
            ->limit(5)
            ->get()
            ->map(function ($user) {
                return [
                    'type' => 'utilisateur',
                    'message' => "Nouvel utilisateur inscrit : {$user->name} ({$user->email})",
                    'icon' => '👤',
                    'color' => 'green',
                    'url' => "/admin/users/{$user->id}",
                    'created_at' => $user->created_at->diffForHumans(),
                ];
            });

        // Fusionner et trier
        $activities = $newReports->concat($newPublications)->concat($newUsers)
            ->sortByDesc(function ($item) {
                return $item['created_at'];
            })
            ->take($limit)
            ->values();

        return $this->successResponse($activities);
    }

    /**
     * Récupérer les statistiques d'engagement détaillées
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function engagement(Request $request)
    {
        $totalPublications = Publication::count();
        $totalLikes = Like::count();
        $totalComments = Commentaire::count();

        // Top 10 des éleveurs les plus actifs
        $topUsers = User::where('role', 'eleveur')
            ->withCount(['publications', 'commentaires'])
            ->withSum('publications as likes_received', 'nbr_likes')
            ->orderBy('publications_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'photo_url' => $user->photo_url,
                    'total_publications' => $user->publications_count,
                    'total_commentaires' => $user->commentaires_count,
                    'likes_recus' => $user->likes_received ?? 0,
                ];
            });

        // Meilleur post
        $bestPost = Publication::with(['user'])
            ->orderBy('nbr_likes', 'desc')
            ->orderBy('nbr_vues', 'desc')
            ->first();

        $bestPostData = $bestPost ? [
            'id' => $bestPost->id,
            'titre' => $bestPost->titre,
            'auteur' => $bestPost->user->name,
            'likes' => $bestPost->nbr_likes,
            'commentaires' => $bestPost->nbr_commentaires,
            'vues' => $bestPost->nbr_vues,
            'url' => "/publications/{$bestPost->id}",
        ] : null;

        return $this->successResponse([
            'taux_engagement_global' => $totalPublications > 0 
                ? round(($totalLikes + $totalComments) / $totalPublications, 2) 
                : 0,
            'moyenne_likes_par_publication' => $totalPublications > 0 
                ? round($totalLikes / $totalPublications, 1) 
                : 0,
            'moyenne_commentaires_par_publication' => $totalPublications > 0 
                ? round($totalComments / $totalPublications, 1) 
                : 0,
            'top_eleveurs' => $topUsers,
            'meilleur_post' => $bestPostData,
        ]);
    }

    /**
     * Générer un rapport mensuel
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function rapportMensuel(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $dateStart = \Carbon\Carbon::create($year, $month, 1)->startOfDay();
        $dateEnd = $dateStart->copy()->endOfMonth();

        // Statistiques du mois
        $newUsers = User::whereBetween('created_at', [$dateStart, $dateEnd])->count();
        $newPublications = Publication::whereBetween('created_at', [$dateStart, $dateEnd])->count();
        $newReports = Report::whereBetween('created_at', [$dateStart, $dateEnd])->count();
        $newLikes = Like::whereBetween('created_at', [$dateStart, $dateEnd])->count();
        $newComments = Commentaire::whereBetween('created_at', [$dateStart, $dateEnd])->count();

        // Évolution par rapport au mois précédent
        $prevMonthStart = $dateStart->copy()->subMonth();
        $prevMonthEnd = $dateEnd->copy()->subMonth();
        
        $prevNewUsers = User::whereBetween('created_at', [$prevMonthStart, $prevMonthEnd])->count();
        $prevNewPublications = Publication::whereBetween('created_at', [$prevMonthStart, $prevMonthEnd])->count();

        $userGrowth = $prevNewUsers > 0 ? round((($newUsers - $prevNewUsers) / $prevNewUsers) * 100, 1) : 0;
        $publicationGrowth = $prevNewPublications > 0 ? round((($newPublications - $prevNewPublications) / $prevNewPublications) * 100, 1) : 0;

        // Signalements résolus
        $reportsResolved = Report::where('statut', 'traite')
            ->whereBetween('updated_at', [$dateStart, $dateEnd])
            ->count();

        return $this->successResponse([
            'mois' => $dateStart->format('F Y'),
            'resume' => [
                'nouveaux_utilisateurs' => $newUsers,
                'croissance_utilisateurs' => $userGrowth . '%',
                'nouvelles_publications' => $newPublications,
                'croissance_publications' => $publicationGrowth . '%',
                'nouveaux_signalements' => $newReports,
                'signalements_resolus' => $reportsResolved,
                'nouvelles_interactions' => [
                    'likes' => $newLikes,
                    'commentaires' => $newComments,
                ],
            ],
            'total_cumule' => [
                'utilisateurs' => User::count(),
                'publications' => Publication::count(),
                'signalements' => Report::count(),
            ],
            'date_debut' => $dateStart->format('Y-m-d'),
            'date_fin' => $dateEnd->format('Y-m-d'),
        ]);
    }

    /**
     * Calcule le taux de croissance
     */
    protected function calculateGrowth(string $model): float
    {
        $currentMonth = $model::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        $lastMonth = $model::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        if ($lastMonth == 0 && $currentMonth == 0) {
            return 0;
        }

        if ($lastMonth == 0) {
            return 100;
        }

        return round((($currentMonth - $lastMonth) / $lastMonth) * 100, 1);
    }
}
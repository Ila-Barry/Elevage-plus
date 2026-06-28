<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use App\Models\User;
use App\Models\Commentaire;
use App\Models\Like;
use App\Traits\ApiResponseTrait;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Contrôleur HomeController
 * 
 * Gère la page d'accueil publique
 * 
 * @package App\Http\Controllers\Web
 */
class HomeController extends Controller
{
    use ApiResponseTrait;

    /**
     * Durée de cache pour les statistiques (secondes)
     */
    private const CACHE_DURATION = 300; // 5 minutes

    /**
     * Afficher la page d'accueil
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            // Log de la visite
            LogService::api('GET', '/', [
                'page' => 'home',
                'user_agent' => $request->userAgent()
            ]);

            // Récupérer les statistiques en cache
            $stats = Cache::remember('home_stats', self::CACHE_DURATION, function () {
                return $this->getHomeStats();
            });

            return view('home', compact('stats'));
            
        } catch (\Exception $e) {
            LogService::security('Erreur chargement page d\'accueil', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return view('home', ['stats' => $this->getDefaultStats()]);
        }
    }

    /**
     * Récupérer les statistiques de la page d'accueil
     *
     * @return array
     */
    private function getHomeStats(): array
    {
        $stats = [];

        // Nombre d'utilisateurs actifs
        $stats['total_users'] = User::where('status', 'active')->count();

        // Nombre de publications publiées
        $stats['total_posts'] = Publication::where('statut', 'publiee')->count();

        // Total des likes sur toutes les publications
        $stats['total_likes'] = Publication::where('statut', 'publiee')->sum('nbr_likes');

        // Total des commentaires
        $stats['total_comments'] = Commentaire::count();

        // Dernières publications (pour l'API)
        $stats['recent_posts'] = Publication::with(['user'])
            ->where('statut', 'publiee')
            ->orderBy('published_at', 'desc')
            ->limit(10)
            ->get();

        return $stats;
    }

    /**
     * Statistiques par défaut en cas d'erreur
     *
     * @return array
     */
    private function getDefaultStats(): array
    {
        return [
            'total_users' => 0,
            'total_posts' => 0,
            'total_likes' => 0,
            'total_comments' => 0,
            'recent_posts' => collect([]),
        ];
    }

    /**
     * API - Récupérer les publications pour la page d'accueil
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPosts(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 10);
            $category = $request->get('category', 'all');
            
            $query = Publication::with(['user'])
                ->where('statut', 'publiee');

            // Filtrer par catégorie
            if ($category !== 'all') {
                $query->where('categorie', $category);
            }

            // Tri
            $sort = $request->get('sort', 'recent');
            switch ($sort) {
                case 'popular':
                    $query->orderBy('nbr_likes', 'desc');
                    break;
                case 'most_viewed':
                    $query->orderBy('nbr_vues', 'desc');
                    break;
                case 'most_commented':
                    $query->orderBy('nbr_commentaires', 'desc');
                    break;
                default:
                    $query->orderBy('published_at', 'desc');
            }

            $publications = $query->paginate($perPage);

            // Log de la requête
            LogService::api('GET', '/api/home/posts', [
                'page' => $page,
                'category' => $category,
                'total' => $publications->total()
            ]);

            return $this->successResponse([
                'data' => $publications->items(),
                'meta' => [
                    'current_page' => $publications->currentPage(),
                    'last_page' => $publications->lastPage(),
                    'per_page' => $publications->perPage(),
                    'total' => $publications->total(),
                ],
            ]);

        } catch (\Exception $e) {
            LogService::security('Erreur récupération publications home', [
                'error' => $e->getMessage()
            ]);
            
            return $this->errorResponse('Erreur lors du chargement des publications', 500);
        }
    }

    /**
     * API - Récupérer les statistiques de la communauté
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStats(Request $request)
    {
        try {
            $stats = Cache::remember('home_stats', self::CACHE_DURATION, function () {
                return [
                    'users' => User::where('status', 'active')->count(),
                    'posts' => Publication::where('statut', 'publiee')->count(),
                    'likes' => Publication::where('statut', 'publiee')->sum('nbr_likes'),
                    'comments' => Commentaire::count(),
                ];
            });

            LogService::api('GET', '/api/home/stats');

            return $this->successResponse($stats);

        } catch (\Exception $e) {
            LogService::security('Erreur récupération stats home', [
                'error' => $e->getMessage()
            ]);
            
            return $this->errorResponse('Erreur lors du chargement des statistiques', 500);
        }
    }
}
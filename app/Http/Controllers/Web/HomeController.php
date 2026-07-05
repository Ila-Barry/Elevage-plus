<?php
// app/Http/Controllers/Web/HomeController.php

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

class HomeController extends Controller
{
    use ApiResponseTrait;

    private const CACHE_DURATION = 300;

    public function index(Request $request)
    {
        try {
            LogService::api('GET', '/', [
                'page' => 'home',
                'user_agent' => $request->userAgent()
            ]);

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

    private function getHomeStats(): array
    {
        $stats = [];

        $stats['total_users'] = User::where('status', 'active')->count();
        $stats['total_posts'] = Publication::where('statut', 'publiee')->count();
        $stats['total_likes'] = Publication::where('statut', 'publiee')->sum('nbr_likes');
        $stats['total_comments'] = Commentaire::count();

        $stats['recent_posts'] = Publication::with(['user'])
            ->where('statut', 'publiee')
            ->orderBy('published_at', 'desc')
            ->limit(10)
            ->get();

        return $stats;
    }

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
     * API - Récupérer les publications (désordonnées / aléatoires)
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

            // ✅ Mélange aléatoire (désordonné)
            $query->inRandomOrder();

            $publications = $query->paginate($perPage);

            // ✅ Formater les publications avec toutes les statistiques
            $formatted = $publications->map(function($post) {
                return [
                    'id' => $post->id,
                    'titre' => $post->titre,
                    'categorie' => $post->categorie,
                    'categorie_label' => $this->getCategorieLabel($post->categorie),
                    'contenu' => $post->contenu,
                    'resume' => $this->getResume($post->contenu, 200),
                    'images' => $this->getImages($post),
                    'videos' => $this->getVideos($post),
                    'documents' => $this->getDocuments($post),
                    'statistiques' => [
                        'likes' => $post->nbr_likes ?? 0,
                        'commentaires' => $post->nbr_commentaires ?? 0,
                        'partages' => $post->nbr_partages ?? 0,
                        'vues' => $post->nbr_vues ?? 0,
                    ],
                    'user' => [
                        'id' => $post->user->id ?? 0,
                        'name' => $post->user->name ?? 'Utilisateur',
                        'photo_url' => $post->user->photo_url ?? null,
                        'role' => $post->user->role ?? 'user',
                    ],
                    'published_at_human' => $post->published_at?->diffForHumans() ?? 'N/A',
                    'published_at' => $post->published_at?->toIso8601String(),
                    'created_at' => $post->created_at?->toIso8601String(),
                ];
            });

            LogService::api('GET', '/api/home/posts', [
                'page' => $page,
                'category' => $category,
                'total' => $publications->total()
            ]);

            return $this->successResponse([
                'data' => $formatted,
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
     */
    public function getStats(Request $request)
    {
        try {
            // ✅ 1. Calcul ou récupération des statistiques globales
            if (app()->environment('local')) {
                // Pas de cache en développement local
                $stats = [
                    'total_users' => User::where('status', 'active')->count(),
                    'total_posts' => Publication::where('statut', 'publiee')->count(),
                    'total_likes' => (int) Publication::where('statut', 'publiee')->sum('nbr_likes'),
                    'total_comments' => Commentaire::count(),
                ];
            } else {
                // Utilisation du cache uniquement en production
                $stats = Cache::remember('home_stats', self::CACHE_DURATION, function () {
                    return [
                        'total_users' => User::where('status', 'active')->count(),
                        'total_posts' => Publication::where('statut', 'publiee')->count(),
                        'total_likes' => (int) Publication::where('statut', 'publiee')->sum('nbr_likes'),
                        'total_comments' => Commentaire::count(),
                    ];
                });
            }

            LogService::api('GET', '/api/home/stats');

            // ✅ 2. Envoi à travers le trait ApiResponseTrait
            return $this->successResponse([
                'total_users'    => $stats['total_users'],
                'total_posts'    => $stats['total_posts'],
                'total_likes'    => $stats['total_likes'],
                'total_comments' => $stats['total_comments'],
            ]);

        } catch (\Exception $e) {
            LogService::security('Erreur récupération stats home', [
                'error' => $e->getMessage()
            ]);
            
            return $this->errorResponse('Erreur lors du chargement des statistiques', 500);
        }
    }

    // ============================================================
    // MÉTHODES UTILITAIRES
    // ============================================================

    private function getCategorieLabel($categorie)
    {
        return match($categorie) {
            'experience' => '💡 Expérience',
            'conseil' => '🌾 Conseil',
            'alerte' => '⚠️ Alerte',
            default => $categorie,
        };
    }

    private function getResume($content, $length = 200)
    {
        $text = strip_tags($content);
        return strlen($text) > $length ? substr($text, 0, $length) . '...' : $text;
    }

    private function getImages($post)
    {
        $images = $post->images ?? [];
        if (is_string($images)) {
            $images = json_decode($images, true) ?? [];
        }
        if (!is_array($images)) {
            return [];
        }
        return array_map(function($path) {
            if (filter_var($path, FILTER_VALIDATE_URL)) {
                return $path;
            }
            $path = preg_replace('#^/?storage/#', '', $path);
            return asset('storage/' . $path);
        }, $images);
    }

    private function getVideos($post)
    {
        $videos = $post->videos ?? [];
        if (is_string($videos)) {
            $videos = json_decode($videos, true) ?? [];
        }
        if (!is_array($videos)) {
            return [];
        }
        return array_map(function($path) {
            if (filter_var($path, FILTER_VALIDATE_URL)) {
                return $path;
            }
            $path = preg_replace('#^/?storage/#', '', $path);
            return asset('storage/' . $path);
        }, $videos);
    }

    private function getDocuments($post)
    {
        $documents = $post->documents ?? [];
        if (is_string($documents)) {
            $documents = json_decode($documents, true) ?? [];
        }
        if (!is_array($documents)) {
            return [];
        }
        return array_map(function($doc) {
            if (is_string($doc)) {
                return [
                    'url' => filter_var($doc, FILTER_VALIDATE_URL) ? $doc : asset('storage/' . preg_replace('#^/?storage/#', '', $doc)),
                    'nom' => 'Fichier'
                ];
            }
            $url = $doc['url'] ?? '';
            return [
                'url' => filter_var($url, FILTER_VALIDATE_URL) ? $url : asset('storage/' . preg_replace('#^/?storage/#', '', $url)),
                'nom' => $doc['nom'] ?? 'Fichier'
            ];
        }, $documents);
    }
}
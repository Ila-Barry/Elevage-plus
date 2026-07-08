<?php
// app/Http/Controllers/Web/ProfileController.php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Publication;
use App\Traits\ApiResponseTrait;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    use ApiResponseTrait;

    private const CACHE_DURATION = 300;

    public function show($id, Request $request)
    {
        try {
            $user = User::where('status', 'active')
                ->with(['elevages' => function($query) {
                    $query->withCount(['animaux']);
                }])
                ->findOrFail($id);

            if ($user->profile_visibility === 'prive' && !auth()->check()) {
                LogService::security('Tentative d\'accès à un profil privé', [
                    'profile_id' => $id,
                    'ip' => $request->ip()
                ]);
                return redirect('/')->with('error', 'Ce profil est privé.');
            }

            LogService::api('GET', '/profile/' . $id, [
                'profile_id' => $id,
                'user_agent' => $request->userAgent()
            ]);

            $cacheKey = "profile_stats_{$id}";
            $stats = Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($user) {
                return $this->getProfileStats($user);
            });

            $cacheKeyPosts = "profile_posts_{$id}";
            $posts = Cache::remember($cacheKeyPosts, self::CACHE_DURATION, function () use ($user) {
                return $this->getProfilePosts($user);
            });

            return view('profilEleveur', [
                'profile' => $user,
                'stats' => $stats,
                'posts' => $posts,
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            LogService::security('Profil non trouvé', [
                'profile_id' => $id,
                'ip' => $request->ip()
            ]);
            return redirect('/')->with('error', 'Utilisateur non trouvé.');
        } catch (\Exception $e) {
            LogService::security('Erreur chargement profil', [
                'profile_id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect('/')->with('error', 'Erreur lors du chargement du profil.');
        }
    }

    private function getProfileStats(User $user): array
    {
        $publications = Publication::where('user_id', $user->id)
            ->where('statut', 'publiee')
            ->get();

        $totalLikes = $publications->sum('nbr_likes');
        $totalComments = DB::table('commentaires')
            ->whereIn('publication_id', $publications->pluck('id'))
            ->count();

        $followers = DB::table('follows')
            ->where('following_id', $user->id)
            ->count();

        $following = DB::table('follows')
            ->where('follower_id', $user->id)
            ->count();

        return [
            'publications' => $publications->count(),
            'likes' => $totalLikes,
            'comments' => $totalComments,
            'followers' => $followers,
            'following' => $following,
            'total_animaux' => $user->elevages->sum('animaux_count'),
            'total_elevages' => $user->elevages->count(),
        ];
    }

    private function getProfilePosts(User $user): array
    {
        $publications = Publication::where('user_id', $user->id)
            ->where('statut', 'publiee')
            ->orderBy('published_at', 'desc')
            ->limit(50)
            ->get();

        $posts = [];
        foreach ($publications as $publication) {
            $posts[] = [
                'id' => $publication->id,
                'titre' => $publication->titre,
                'contenu' => $publication->contenu,
                'resume' => $this->getResume($publication->contenu),
                'images' => $this->getImagesUrls($publication->image_url),
                'image_url' => $this->getFirstImage($publication->image_url),
                'likes' => (int) $publication->nbr_likes,
                'comments' => (int) $publication->nbr_commentaires,
                'views' => (int) $publication->nbr_vues,
                'published_at' => $publication->published_at,
                'published_at_human' => $publication->published_at?->diffForHumans(),
                'user_liked' => false,
                'categorie' => $publication->categorie,
                'categorie_label' => $this->getCategoryLabel($publication->categorie),
            ];
        }

        return $posts;
    }

    public function apiShow($id, Request $request)
    {
        try {
            $user = User::where('status', 'active')->findOrFail($id);

            if ($user->profile_visibility === 'prive' && !auth()->check()) {
                return $this->errorResponse('Ce profil est privé.', 403);
            }

            $cacheKey = "profile_stats_{$id}";
            $stats = Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($user) {
                return $this->getProfileStats($user);
            });

            $cacheKeyPosts = "profile_posts_{$id}";
            $posts = Cache::remember($cacheKeyPosts, self::CACHE_DURATION, function () use ($user) {
                return $this->getProfilePosts($user);
            });

            LogService::api('GET', '/api/profile/' . $id);

            return $this->successResponse([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'bio' => $user->bio,
                    'photo_url' => $user->photo_url,
                    'location' => $user->location ?? 'Non renseignée',
                    'type_elevage' => $user->type_elevage ?? 'Non spécifié',
                    'member_since' => $user->created_at?->format('F Y') ?? 'N/A',
                    'website' => $user->website ?? null,
                    'email' => $user->email,
                    'is_following' => false,
                ],
                'stats' => $stats,
                'posts' => $posts,
            ]);

        } catch (\Exception $e) {
            LogService::security('Erreur API profil', [
                'profile_id' => $id,
                'error' => $e->getMessage()
            ]);
            return $this->errorResponse('Erreur lors du chargement du profil', 500);
        }
    }

    public function apiPosts($id, Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 5);
            $sort = $request->get('sort', 'recent');

            $user = User::where('status', 'active')->findOrFail($id);

            if ($user->profile_visibility === 'prive' && !auth()->check()) {
                return $this->errorResponse('Ce profil est privé.', 403);
            }

            $query = Publication::where('user_id', $id)
                ->where('statut', 'publiee');

            switch ($sort) {
                case 'oldest':
                    $query->orderBy('published_at', 'asc');
                    break;
                case 'mostLiked':
                    $query->orderBy('nbr_likes', 'desc');
                    break;
                case 'mostViewed':
                    $query->orderBy('nbr_vues', 'desc');
                    break;
                default:
                    $query->orderBy('published_at', 'desc');
            }

            $publications = $query->paginate($perPage);

            $posts = [];
            foreach ($publications->items() as $publication) {
                $posts[] = [
                    'id' => $publication->id,
                    'titre' => $publication->titre,
                    'contenu' => $publication->contenu,
                    'resume' => $this->getResume($publication->contenu),
                    'images' => $this->getImagesUrls($publication->image_url),
                    'image_url' => $this->getFirstImage($publication->image_url),
                    'likes' => (int) $publication->nbr_likes,
                    'comments' => (int) $publication->nbr_commentaires,
                    'views' => (int) $publication->nbr_vues,
                    'published_at' => $publication->published_at,
                    'published_at_human' => $publication->published_at?->diffForHumans(),
                    'user_liked' => false,
                    'categorie' => $publication->categorie,
                    'categorie_label' => $this->getCategoryLabel($publication->categorie),
                ];
            }

            LogService::api('GET', '/api/profile/' . $id . '/posts', [
                'page' => $page,
                'sort' => $sort
            ]);

            return $this->successResponse([
                'data' => $posts,
                'meta' => [
                    'current_page' => $publications->currentPage(),
                    'last_page' => $publications->lastPage(),
                    'per_page' => $publications->perPage(),
                    'total' => $publications->total(),
                ],
            ]);

        } catch (\Exception $e) {
            LogService::security('Erreur API publications profil', [
                'profile_id' => $id,
                'error' => $e->getMessage()
            ]);
            return $this->errorResponse('Erreur lors du chargement des publications', 500);
        }
    }

    public function toggleFollow($id, Request $request)
    {
        try {
            if (!auth()->check()) {
                return $this->unauthorizedResponse('Vous devez être connecté pour suivre un utilisateur.');
            }

            $user = auth()->user();
            $targetUser = User::findOrFail($id);

            if ($user->id === $targetUser->id) {
                return $this->errorResponse('Vous ne pouvez pas vous suivre vous-même.', 422);
            }

            $isFollowing = DB::table('follows')
                ->where('follower_id', $user->id)
                ->where('following_id', $targetUser->id)
                ->exists();

            if ($isFollowing) {
                DB::table('follows')
                    ->where('follower_id', $user->id)
                    ->where('following_id', $targetUser->id)
                    ->delete();
                $message = 'Vous ne suivez plus ' . $targetUser->name;
                $following = false;
            } else {
                DB::table('follows')->insert([
                    'follower_id' => $user->id,
                    'following_id' => $targetUser->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $message = 'Vous suivez maintenant ' . $targetUser->name;
                $following = true;
            }

            Cache::forget("profile_stats_{$id}");

            LogService::auth($message, [
                'follower_id' => $user->id,
                'following_id' => $targetUser->id
            ]);

            return $this->successResponse([
                'following' => $following,
                'followers_count' => DB::table('follows')
                    ->where('following_id', $targetUser->id)
                    ->count(),
            ], $message);

        } catch (\Exception $e) {
            LogService::security('Erreur follow/unfollow', [
                'error' => $e->getMessage()
            ]);
            return $this->errorResponse('Erreur lors de l\'opération.', 500);
        }
    }

    // ========== MÉTHODES PRIVÉES ==========

    private function getCategoryLabel(string $categorie): string
    {
        return match($categorie) {
            'experience' => '💡 Expérience',
            'conseil' => '🌾 Conseil',
            'alerte' => '⚠️ Alerte',
            default => $categorie,
        };
    }

    /**
     * ✅ CORRIGÉ : Accepte null et retourne une chaîne vide
     */
    private function getResume(?string $contenu, int $length = 150): string
    {
        if (empty($contenu)) {
            return '';
        }
        $text = strip_tags($contenu);
        return strlen($text) > $length ? substr($text, 0, $length) . '...' : $text;
    }

    private function getImagesUrls(?string $imageUrl): array
    {
        if (!$imageUrl) {
            return [];
        }
        $urls = explode(',', $imageUrl);
        return array_map(function ($url) {
            $url = trim($url);
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                return $url;
            }
            return asset('storage/' . $url);
        }, $urls);
    }

    private function getFirstImage(?string $imageUrl): ?string
    {
        if (!$imageUrl) {
            return null;
        }
        $first = explode(',', $imageUrl)[0];
        $first = trim($first);
        if (filter_var($first, FILTER_VALIDATE_URL)) {
            return $first;
        }
        return asset('storage/' . $first);
    }
}
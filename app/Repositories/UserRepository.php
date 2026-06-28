<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository
{
    // Cache TTL en minutes
    protected int $cacheTTL = 60;

    public function findById(int $id): ?User
    {
        return Cache::remember("user_{$id}", $this->cacheTTL, function () use ($id) {
            return User::with(['elevages', 'publications'])->find($id);
        });
    }

    public function getPublicProfiles(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = User::active()
            ->publicProfiles()
            ->withCount(['publications', 'elevages']);
        
        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'LIKE', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'LIKE', '%' . $filters['search'] . '%');
            });
        }
        
        // Utiliser des index
        $query->orderBy('created_at', 'desc');
        
        return $query->paginate($perPage);
    }

    public function getStats(int $userId): array
    {
        return Cache::remember("user_stats_{$userId}", 30, function () use ($userId) {
            return DB::table('users')
                ->where('id', $userId)
                ->select([
                    DB::raw('(SELECT COUNT(*) FROM publications WHERE user_id = users.id) as publications_count'),
                    DB::raw('(SELECT COALESCE(SUM(nbr_likes), 0) FROM publications WHERE user_id = users.id) as total_likes'),
                    DB::raw('(SELECT COUNT(*) FROM elevages WHERE user_id = users.id) as elevages_count'),
                    DB::raw('(SELECT COUNT(*) FROM animaux WHERE elevage_id IN (SELECT id FROM elevages WHERE user_id = users.id)) as animaux_count')
                ])
                ->first();
        });
    }

    public function clearCache(int $userId): void
    {
        Cache::forget("user_{$userId}");
        Cache::forget("user_stats_{$userId}");
    }
}
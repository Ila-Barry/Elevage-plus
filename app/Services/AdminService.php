<?php
// app/Services/AdminService.php

namespace App\Services;

use App\Models\User;
use App\Models\Publication;
use App\Models\Report;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service AdminService
 * 
 * Centralise les opérations d'administration
 */
class AdminService
{
    /**
     * Bannir un utilisateur avec toutes les conséquences
     */
    public function banUser(User $user, string $motif, ?User $admin = null): void
    {
        DB::beginTransaction();

        try {
            $user->update(['status' => 'bannie']);

            // Bloquer toutes les publications de l'utilisateur
            Publication::where('user_id', $user->id)
                ->where('statut', 'publiee')
                ->update([
                    'statut' => 'bloquee',
                    'raison_blocage' => "Compte banni - {$motif}",
                ]);

            // Marquer les signalements comme traités
            Report::whereHas('publication', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('statut', 'en_attente')
              ->update(['statut' => 'traite']);

            DB::commit();

            Log::info("Utilisateur banni: {$user->id} ({$user->email}) - Motif: {$motif}");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors du bannissement: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Réactiver un utilisateur
     */
    public function unbanUser(User $user): void
    {
        $user->update(['status' => 'active']);

        Log::info("Utilisateur réactivé: {$user->id} ({$user->email})");
    }

    /**
     * Bloquer une publication
     */
    public function blockPublication(Publication $publication, string $raison, ?User $admin = null): void
    {
        $publication->update([
            'statut' => 'bloquee',
            'raison_blocage' => $raison,
        ]);

        Log::info("Publication bloquée: {$publication->id} - Raison: {$raison}");
    }

    /**
     * Débloquer une publication
     */
    public function unblockPublication(Publication $publication): void
    {
        $publication->update([
            'statut' => 'publiee',
            'raison_blocage' => null,
        ]);

        Log::info("Publication débloquée: {$publication->id}");
    }

    /**
     * Récupérer les statistiques d'un utilisateur
     */
    public function getUserStats(User $user): array
    {
        return [
            'total_publications' => $user->publications()->count(),
            'total_commentaires' => $user->commentaires()->count(),
            'total_likes_recus' => $user->publications()->sum('nbr_likes'),
            'total_likes_donnes' => $user->likes()->count(),
            'total_elevages' => $user->elevages()->count(),
            'total_animaux' => $user->elevages()->withCount('animaux')->get()->sum('animaux_count'),
            'total_signalements' => $user->reports()->count(),
            'membre_depuis' => $user->created_at->format('d/m/Y'),
            'derniere_connexion' => $user->last_login_at?->format('d/m/Y H:i') ?? 'Jamais',
        ];
    }

    /**
     * Récupérer l'activité récente d'un utilisateur
     */
    public function getUserRecentActivity(User $user, int $limit = 10): array
    {
        $activities = [];

        // Dernières publications
        $publications = $user->publications()
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($pub) {
                return [
                    'type' => 'publication',
                    'title' => $pub->titre,
                    'created_at' => $pub->created_at,
                    'url' => "/publications/{$pub->id}",
                ];
            });

        // Derniers commentaires
        $commentaires = $user->commentaires()
            ->with('publication')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($com) {
                return [
                    'type' => 'commentaire',
                    'title' => "Commentaire sur: " . ($com->publication?->titre ?? 'Publication'),
                    'created_at' => $com->created_at,
                    'url' => "/publications/{$com->publication_id}#comment-{$com->id}",
                ];
            });

        // Fusionner et trier
        $activities = $publications->concat($commentaires)
            ->sortByDesc('created_at')
            ->take($limit)
            ->values()
            ->toArray();

        return $activities;
    }

    /**
     * Vérifier si un email est déjà utilisé
     */
    public function isEmailTaken(string $email, ?int $excludeUserId = null): bool
    {
        $query = User::where('email', $email);
        
        if ($excludeUserId) {
            $query->where('id', '!=', $excludeUserId);
        }
        
        return $query->exists();
    }

    /**
     * Vérifier si un téléphone est déjà utilisé
     */
    public function isTelephoneTaken(string $telephone, ?int $excludeUserId = null): bool
    {
        $query = User::where('telephone', $telephone);
        
        if ($excludeUserId) {
            $query->where('id', '!=', $excludeUserId);
        }
        
        return $query->exists();
    }
}
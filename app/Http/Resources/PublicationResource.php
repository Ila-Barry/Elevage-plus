<?php
// app/Http/Resources/PublicationResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user();
        
        return [
            'id' => $this->id,
            'titre' => $this->titre,
            'categorie' => $this->categorie,
            'categorie_label' => $this->getCategorieLabel(),
            'contenu' => $this->contenu,
            'resume' => $this->resume,
            'temps_lecture' => $this->temps_lecture,
            
            // ✅ Images multiples - TABLEAU COMPLET
            'images' => $this->getImagesUrls(),
            'images_count' => count($this->getImagesUrls()),
            
            // ✅ Vidéos multiples - TABLEAU COMPLET
            'videos' => $this->getVideosUrls(),
            'videos_count' => count($this->getVideosUrls()),
            
            // ✅ Fichiers multiples - TABLEAU COMPLET AVEC NOMS
            'fichiers' => $this->getFichiersUrls(),
            'fichiers_count' => count($this->getFichiersUrls()),
            
            // ✅ Statistiques complètes
            'statistiques' => [
                'likes' => (int) $this->nbr_likes,
                'commentaires' => (int) $this->nbr_commentaires,
                'partages' => (int) $this->nbr_partages,
                'vues' => (int) $this->nbr_vues,
                'signalements' => $this->when($user?->isAdmin(), (int) $this->nbr_signalements),
            ],
            
            'interactions' => [
                'liked_by_user' => $this->isLikedByUser($user),
                'reported_by_user' => $this->isReportedByUser($user),
            ],
            
            'statut' => $this->statut,
            'raison_blocage' => $this->when($this->statut === 'bloquee', $this->raison_blocage),
            
            'auteur' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'photo_url' => $this->user->photo_url,
                'role' => $this->user->role,
            ],
            
            'commentaires' => CommentaireResource::collection(
                $this->whenLoaded('commentaires', function() {
                    return $this->commentaires->whereNull('parent_id');
                })
            ),
            
            'published_at' => $this->published_at?->toIso8601String(),
            'published_at_human' => $this->published_at?->diffForHumans(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    protected function getCategorieLabel(): string
    {
        return match($this->categorie) {
            'experience' => '💡 Expérience',
            'conseil' => '🌾 Conseil',
            'alerte' => '⚠️ Alerte',
            default => $this->categorie,
        };
    }

    /**
     * ✅ Retourne un tableau de toutes les images
     */
    protected function getImagesUrls(): array
    {
        if (!$this->image_url) {
            return [];
        }
        $urls = explode(',', $this->image_url);
        return array_map(function ($url) {
            $url = trim($url);
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                return $url;
            }
            return asset('storage/' . $url);
        }, array_filter($urls));
    }

    /**
     * ✅ Retourne un tableau de toutes les vidéos
     */
    protected function getVideosUrls(): array
    {
        if (!$this->video_url) {
            return [];
        }
        $urls = explode(',', $this->video_url);
        return array_map(function ($url) {
            $url = trim($url);
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                return $url;
            }
            return asset('storage/' . $url);
        }, array_filter($urls));
    }

    /**
     * ✅ Retourne un tableau de tous les fichiers avec leurs noms
     */
    protected function getFichiersUrls(): array
    {
        if (!$this->fichier_url) {
            return [];
        }
        $urls = explode(',', $this->fichier_url);
        $names = $this->fichier_nom ? explode(',', $this->fichier_nom) : [];
        
        return array_map(function ($index, $url) use ($names) {
            $url = trim($url);
            return [
                'url' => filter_var($url, FILTER_VALIDATE_URL) ? $url : asset('storage/' . $url),
                'nom' => isset($names[$index]) ? trim($names[$index]) : 'Fichier ' . ($index + 1),
                'index' => $index,
            ];
        }, array_keys($urls), $urls);
    }
}
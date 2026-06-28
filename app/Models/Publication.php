<?php
// app/Models/Publication.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Publication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'titre',
        'categorie',
        'contenu',
        'images',      // ✅ Nouveau champ JSON
        'videos',      // ✅ Nouveau champ JSON
        'documents',   // ✅ Nouveau champ JSON
        // Garder les anciens pour compatibilité
        'image_url',
        'video_url',
        'fichier_url',
        'fichier_nom',
        'nbr_likes',
        'nbr_commentaires',
        'nbr_partages',
        'nbr_vues',
        'nbr_signalements',
        'statut',
        'raison_blocage',
        'published_at',
    ];

    protected $casts = [
        'images' => 'array',
        'videos' => 'array',
        'documents' => 'array',
        'nbr_likes' => 'integer',
        'nbr_commentaires' => 'integer',
        'nbr_partages' => 'integer',
        'nbr_vues' => 'integer',
        'nbr_signalements' => 'integer',
        'published_at' => 'datetime',
    ];

    protected $attributes = [
        'statut' => 'publiee',
    ];

    // ============================================================
    // ACCESSORS - Version avec fallback sur les anciennes colonnes
    // ============================================================

    public function getImagesAttribute($value)
    {
        // ✅ Si la nouvelle colonne JSON a des données, les utiliser
        if (!empty($value)) {
            $images = is_array($value) ? $value : json_decode($value, true);
            if (is_array($images) && count($images) > 0) {
                return array_map([$this, 'formatUrl'], $images);
            }
        }
        
        // ✅ Fallback sur l'ancienne colonne image_url
        if (!empty($this->image_url)) {
            $oldImages = explode(',', $this->image_url);
            return array_map([$this, 'formatUrl'], array_map('trim', $oldImages));
        }
        
        return [];
    }

    public function getVideosAttribute($value)
    {
        if (!empty($value)) {
            $videos = is_array($value) ? $value : json_decode($value, true);
            if (is_array($videos) && count($videos) > 0) {
                return array_map([$this, 'formatUrl'], $videos);
            }
        }
        
        if (!empty($this->video_url)) {
            $oldVideos = explode(',', $this->video_url);
            return array_map([$this, 'formatUrl'], array_map('trim', $oldVideos));
        }
        
        return [];
    }

    public function getDocumentsAttribute($value)
    {
        if (!empty($value)) {
            $documents = is_array($value) ? $value : json_decode($value, true);
            if (is_array($documents) && count($documents) > 0) {
                return array_map(function($doc) {
                    return [
                        'url' => $this->formatUrl($doc['url'] ?? $doc),
                        'nom' => $doc['nom'] ?? 'Fichier'
                    ];
                }, $documents);
            }
        }
        
        if (!empty($this->fichier_url)) {
            $urls = explode(',', $this->fichier_url);
            $names = !empty($this->fichier_nom) ? explode(',', $this->fichier_nom) : [];
            return array_map(function($index, $url) use ($names) {
                return [
                    'url' => $this->formatUrl(trim($url)),
                    'nom' => isset($names[$index]) ? trim($names[$index]) : 'Fichier ' . ($index + 1)
                ];
            }, array_keys($urls), $urls);
        }
        
        return [];
    }

    private function formatUrl($path)
    {
        if (empty($path)) {
            return '';
        }
        
        $path = trim($path);
        
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }
        
        $path = preg_replace('#^/?storage/#', '', $path);
        return asset('storage/' . $path);
    }

    public function getResumeAttribute()
    {
        $text = strip_tags($this->contenu);
        return strlen($text) > 200 ? substr($text, 0, 200) . '...' : $text;
    }

    // ============================================================
    // RELATIONS
    // ============================================================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function commentaires()
    {
        return $this->hasMany(Commentaire::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // ============================================================
    // SCOPES
    // ============================================================

    public function scopePublished($query)
    {
        return $query->where('statut', 'publiee');
    }

    public function scopeByCategory($query, string $categorie)
    {
        return $query->where('categorie', $categorie);
    }

    // ============================================================
    // MÉTHODES UTILITAIRES
    // ============================================================

    public function incrementViews(): void
    {
        $this->increment('nbr_vues');
    }

    public function isLikedByUser(?User $user): bool
    {
        if (!$user) return false;
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function canManage(User $user): bool
    {
        return $this->user_id === $user->id || $user->role === 'admin';
    }
}
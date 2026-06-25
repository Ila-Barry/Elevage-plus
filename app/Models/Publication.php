<?php
// app/Models/Publication.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class Publication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'titre',
        'categorie',
        'contenu',
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
        'nbr_likes' => 'integer',
        'nbr_commentaires' => 'integer',
        'nbr_partages' => 'integer',
        'nbr_vues' => 'integer',
        'nbr_signalements' => 'integer',
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'statut' => 'publiee',
    ];

    // ============================================================
    // ACCESSORS CORRIGÉS - Version finale
    // ============================================================

    /**
     * Accesseur pour la première image
     * Retourne l'URL complète de la première image
     */
    public function getImageUrlAttribute($value)
    {
        if (!$value) {
            return null;
        }
        $first = explode(',', $value)[0];
        $first = trim($first);
        
        // Si l'URL est déjà complète, la retourner
        if (filter_var($first, FILTER_VALIDATE_URL)) {
            return $first;
        }
        
        // ✅ CORRECTION: Supprimer le préfixe 'storage/' s'il existe déjà
        $path = $first;
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, 8); // Enlever 'storage/'
        }
        
        return asset('storage/' . $path);
    }

    /**
     * Accesseur pour toutes les images (tableau)
     */
    public function getImagesAttribute()
    {
        if (!$this->image_url) {
            return [];
        }
        
        $urls = explode(',', $this->image_url);
        return array_map(function ($url) {
            $url = trim($url);
            
            // Si l'URL est déjà complète
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                return $url;
            }
            
            // ✅ CORRECTION: Supprimer le préfixe 'storage/' s'il existe déjà
            $path = $url;
            if (str_starts_with($path, 'storage/')) {
                $path = substr($path, 8);
            }
            
            return asset('storage/' . $path);
        }, $urls);
    }

    /**
     * Accesseur pour les URLs des vidéos (tableau)
     */
    public function getVideosAttribute()
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
            
            $path = $url;
            if (str_starts_with($path, 'storage/')) {
                $path = substr($path, 8);
            }
            
            return asset('storage/' . $path);
        }, $urls);
    }

    /**
     * Accesseur pour les fichiers (tableau avec nom et url)
     */
    public function getFichiersAttribute()
    {
        if (!$this->fichier_url) {
            return [];
        }
        
        $urls = explode(',', $this->fichier_url);
        $names = $this->fichier_nom ? explode(',', $this->fichier_nom) : [];
        
        return array_map(function ($index, $url) use ($names) {
            $url = trim($url);
            
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                $fullUrl = $url;
            } else {
                $path = $url;
                if (str_starts_with($path, 'storage/')) {
                    $path = substr($path, 8);
                }
                $fullUrl = asset('storage/' . $path);
            }
            
            return [
                'url' => $fullUrl,
                'nom' => isset($names[$index]) ? trim($names[$index]) : 'Fichier ' . ($index + 1)
            ];
        }, array_keys($urls), $urls);
    }

    /**
     * Accesseur pour le résumé du contenu
     */
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

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function shares()
    {
        return $this->hasMany(Share::class);
    }

    public function isLikedByUser(?User $user): bool
    {
        if (!$user) return false;
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function isReportedByUser(?User $user): bool
    {
        if (!$user) return false;
        return $this->reports()->where('user_id', $user->id)->exists();
    }

    // ============================================================
    // SCOPES
    // ============================================================

    public function scopePublished($query)
    {
        return $query->where('statut', 'publiee');
    }

    public function scopeReported($query)
    {
        return $query->where('statut', 'signalee');
    }

    public function scopeBlocked($query)
    {
        return $query->where('statut', 'bloquee');
    }

    public function scopeByCategory($query, string $categorie)
    {
        return $query->where('categorie', $categorie);
    }

    public function scopeMostLiked($query, int $limit = 10)
    {
        return $query->orderBy('nbr_likes', 'desc')->limit($limit);
    }

    public function scopeMostViewed($query, int $limit = 10)
    {
        return $query->orderBy('nbr_vues', 'desc')->limit($limit);
    }

    public function scopeRecent($query, int $limit = 10)
    {
        return $query->orderBy('published_at', 'desc')->limit($limit);
    }

    // ============================================================
    // MÉTHODES UTILITAIRES
    // ============================================================

    public function incrementViews(): void
    {
        $this->increment('nbr_vues');
    }

    public function incrementLikes(): void
    {
        $this->increment('nbr_likes');
    }

    public function decrementLikes(): void
    {
        $this->decrement('nbr_likes');
    }

    public function incrementCommentaires(): void
    {
        $this->increment('nbr_commentaires');
    }

    public function decrementCommentaires(): void
    {
        $this->decrement('nbr_commentaires');
    }

    public function incrementPartages(): void
    {
        $this->increment('nbr_partages');
    }

    public function incrementSignalements(): void
    {
        $this->increment('nbr_signalements');
        if ($this->nbr_signalements >= 5 && $this->statut === 'publiee') {
            $this->update(['statut' => 'signalee']);
        }
    }

    public function block(string $raison): void
    {
        $this->update([
            'statut' => 'bloquee',
            'raison_blocage' => $raison,
        ]);
    }

    public function unblock(): void
    {
        $this->update([
            'statut' => 'publiee',
            'raison_blocage' => null,
        ]);
    }
}
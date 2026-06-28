<?php
// app/Models/Publication.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

/**
 * Modèle Publication
 * 
 * Représente un article publié par un éleveur sur la plateforme
 * 
 * @property int $id
 * @property int $user_id
 * @property string $titre
 * @property string $categorie
 * @property string $contenu
 * @property string|null $image_url
 * @property string|null $video_url
 * @property string|null $fichier_url
 * @property string|null $fichier_nom
 * @property int $nbr_likes
 * @property int $nbr_commentaires
 * @property int $nbr_partages
 * @property int $nbr_vues
 * @property int $nbr_signalements
 * @property string $statut
 * @property string|null $raison_blocage
 * @property \Carbon\Carbon $published_at
 */
class Publication extends Model
{
    use HasFactory;

    /**
     * Les attributs assignables en masse.
     */
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

    /**
     * Les attributs qui doivent être castés.
     */
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

    /**
     * Les valeurs par défaut des attributs.
     */
    protected $attributes = [
        'statut' => 'publiee',
    ];

    // ========== RELATIONS ==========

    /**
     * Relation avec l'utilisateur (auteur)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec les commentaires
     */
    public function commentaires()
    {
        return $this->hasMany(Commentaire::class);
    }

    /**
     * Relation avec les likes
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /**
     * Relation avec les signalements
     */
    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    /**
     * Relation avec les partages
     */
    public function shares()
    {
        return $this->hasMany(Share::class);
    }

    /**
     * Vérifie si l'utilisateur a liké cette publication
     */
    public function isLikedByUser(?User $user): bool
    {
        if (!$user) {
            return false;
        }
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    /**
     * Vérifie si l'utilisateur a signalé cette publication
     */
    public function isReportedByUser(?User $user): bool
    {
        if (!$user) {
            return false;
        }
        return $this->reports()->where('user_id', $user->id)->exists();
    }

    // ========== ACCESSORS ==========

    /**
     * Accesseur pour l'URL complète de l'image
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$value) {
                    return null;
                }
                if (filter_var($value, FILTER_VALIDATE_URL)) {
                    return $value;
                }
                return asset('storage/' . $value);
            }
        );
    }

    /**
     * Accesseur pour l'URL complète du fichier
     */
    protected function fichierUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$value) {
                    return null;
                }
                return asset('storage/' . $value);
            }
        );
    }

    /**
     * Accesseur pour le résumé du contenu
     */
    protected function resume(): Attribute
    {
        return Attribute::make(
            get: function () {
                return strlen($this->contenu) > 200 
                    ? substr($this->contenu, 0, 200) . '...' 
                    : $this->contenu;
            }
        );
    }

    /**
     * Accesseur pour le temps de lecture estimé
     */
    protected function tempsLecture(): Attribute
    {
        return Attribute::make(
            get: function () {
                $mots = str_word_count(strip_tags($this->contenu), 0);
                $minutes = ceil($mots / 200); // 200 mots par minute en moyenne
                return $minutes;
            }
        );
    }

    // ========== SCOPES ==========

    /**
     * Scope pour les publications publiées (non bloquées)
     */
    public function scopePublished($query)
    {
        return $query->where('statut', 'publiee');
    }

    /**
     * Scope pour les publications signalées
     */
    public function scopeReported($query)
    {
        return $query->where('statut', 'signalee');
    }

    /**
     * Scope pour les publications bloquées
     */
    public function scopeBlocked($query)
    {
        return $query->where('statut', 'bloquee');
    }

    /**
     * Scope pour les publications par catégorie
     */
    public function scopeByCategory($query, string $categorie)
    {
        return $query->where('categorie', $categorie);
    }

    /**
     * Scope pour les publications les plus likées
     */
    public function scopeMostLiked($query, int $limit = 10)
    {
        return $query->orderBy('nbr_likes', 'desc')->limit($limit);
    }

    /**
     * Scope pour les publications les plus vues
     */
    public function scopeMostViewed($query, int $limit = 10)
    {
        return $query->orderBy('nbr_vues', 'desc')->limit($limit);
    }

    /**
     * Scope pour les publications récentes
     */
    public function scopeRecent($query, int $limit = 10)
    {
        return $query->orderBy('published_at', 'desc')->limit($limit);
    }

    // ========== MÉTHODES UTILITAIRES ==========

    /**
     * Incrémente le compteur de vues
     */
    public function incrementViews(): void
    {
        $this->increment('nbr_vues');
    }

    /**
     * Incrémente le compteur de likes
     */
    public function incrementLikes(): void
    {
        $this->increment('nbr_likes');
    }

    /**
     * Décrémente le compteur de likes
     */
    public function decrementLikes(): void
    {
        $this->decrement('nbr_likes');
    }

    /**
     * Incrémente le compteur de commentaires
     */
    public function incrementCommentaires(): void
    {
        $this->increment('nbr_commentaires');
    }

    /**
     * Décrémente le compteur de commentaires
     */
    public function decrementCommentaires(): void
    {
        $this->decrement('nbr_commentaires');
    }

    /**
     * Incrémente le compteur de partages
     */
    public function incrementPartages(): void
    {
        $this->increment('nbr_partages');
    }

    /**
     * Incrémente le compteur de signalements
     * Passe automatiquement en statut "signalee" si seuil atteint
     */
    public function incrementSignalements(): void
    {
        $this->increment('nbr_signalements');
        
        // Si 5 signalements ou plus, passer en statut signalée
        if ($this->nbr_signalements >= 5 && $this->statut === 'publiee') {
            $this->update(['statut' => 'signalee']);
        }
    }

    /**
     * Bloque la publication
     */
    public function block(string $raison): void
    {
        $this->update([
            'statut' => 'bloquee',
            'raison_blocage' => $raison,
        ]);
    }

    /**
     * Débloque la publication
     */
    public function unblock(): void
    {
        $this->update([
            'statut' => 'publiee',
            'raison_blocage' => null,
        ]);
    }
}
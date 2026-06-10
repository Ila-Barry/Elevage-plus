<?php
// app/Models/Message.php (Ajouter les attributs pour les médias)

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'expediteur_id',
        'destinataire_id',
        'contenu',
        'type',
        'media_url',
        'media_type',
        'media_size',
        'thumbnail_url',
        'file_name',
        'duration',
        'lu',
        'lu_at',
        'is_deleted',
        'deleted_for_everyone',
    ];

    protected $casts = [
        'lu' => 'boolean',
        'is_deleted' => 'boolean',
        'deleted_for_everyone' => 'boolean',
        'lu_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'media_size' => 'integer',
        'duration' => 'integer',
    ];

    // ... (autres relations et méthodes)
    
    /**
     * Vérifie si le message contient un média
     */
    public function hasMedia(): bool
    {
        return !is_null($this->media_url) && $this->type !== 'text';
    }
    
    /**
     * Vérifie si le message est une image
     */
    public function isImage(): bool
    {
        return $this->type === 'image';
    }
    
    /**
     * Vérifie si le message est une vidéo
     */
    public function isVideo(): bool
    {
        return $this->type === 'video';
    }
    
    /**
     * Vérifie si le message est un fichier
     */
    public function isFile(): bool
    {
        return $this->type === 'file';
    }
    
    /**
     * Vérifie si le message est un sticker
     */
    public function isSticker(): bool
    {
        return $this->type === 'sticker';
    }
    
    /**
     * Récupère l'URL complète du média
     */
    public function getMediaUrlAttribute($value): ?string
    {
        if (!$value) {
            return null;
        }
        
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        
        return asset('storage/' . $value);
    }
    
    /**
     * Récupère l'URL complète de la miniature
     */
    public function getThumbnailUrlAttribute($value): ?string
    {
        if (!$value) {
            return null;
        }
        
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        
        return asset('storage/' . $value);
    }
}
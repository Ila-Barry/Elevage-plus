<?php

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
        'lu'                   => 'boolean',
        'is_deleted'           => 'boolean',
        'deleted_for_everyone' => 'boolean',
        'lu_at'                => 'datetime',
        'created_at'           => 'datetime',
        'updated_at'           => 'datetime',
        'media_size'           => 'integer',
        'duration'             => 'integer',
    ];

    // ========== RELATIONS ==========

    public function expediteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'expediteur_id');
    }

    public function destinataire(): BelongsTo
    {
        return $this->belongsTo(User::class, 'destinataire_id');
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }

    // ========== MÉTHODES ==========

    public function hasMedia(): bool
    {
        return !is_null($this->media_url) && $this->type !== 'text';
    }

    public function isImage(): bool
    {
        return $this->type === 'image';
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    public function isFile(): bool
    {
        return $this->type === 'file';
    }

    public function isSticker(): bool
    {
        return $this->type === 'sticker';
    }

    public function getMediaUrlAttribute($value): ?string
    {
        if (!$value) return null;

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        return asset('storage/' . $value);
    }

    public function getThumbnailUrlAttribute($value): ?string
    {
        if (!$value) return null;

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        return asset('storage/' . $value);
    }
}
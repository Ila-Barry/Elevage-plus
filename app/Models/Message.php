<?php
// app/Models/Message.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle Message
 * 
 * Représente un message dans une conversation
 * 
 * @property int $id
 * @property int $conversation_id
 * @property int $expediteur_id
 * @property int $destinataire_id
 * @property string $contenu
 * @property bool $lu
 * @property \Carbon\Carbon|null $lu_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Message extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'conversation_id',
        'expediteur_id',
        'destinataire_id',
        'contenu',
        'lu',
        'lu_at',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'lu' => 'boolean',
        'lu_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relation avec la conversation
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Relation avec l'expéditeur
     */
    public function expediteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'expediteur_id');
    }

    /**
     * Relation avec le destinataire
     */
    public function destinataire(): BelongsTo
    {
        return $this->belongsTo(User::class, 'destinataire_id');
    }

    /**
     * Marque le message comme lu
     *
     * @return void
     */
    public function markAsRead(): void
    {
        if (!$this->lu) {
            $this->update([
                'lu' => true,
                'lu_at' => now(),
            ]);
        }
    }

    /**
     * Vérifie si l'utilisateur est l'expéditeur du message
     *
     * @param int $userId
     * @return bool
     */
    public function isSentBy(int $userId): bool
    {
        return $this->expediteur_id === $userId;
    }

    /**
     * Vérifie si l'utilisateur est le destinataire du message
     *
     * @param int $userId
     * @return bool
     */
    public function isReceivedBy(int $userId): bool
    {
        return $this->destinataire_id === $userId;
    }
}
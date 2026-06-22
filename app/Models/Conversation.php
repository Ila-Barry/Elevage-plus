<?php
// app/Models/Conversation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle Conversation
 * 
 * Représente une conversation entre deux utilisateurs
 * 
 * @property int $id
 * @property int $user1_id
 * @property int $user2_id
 * @property string|null $derniere_message
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Conversation extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user1_id',
        'user2_id',
        'derniere_message',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relation avec le premier utilisateur
     */
    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    /**
     * Relation avec le deuxième utilisateur
     */
    public function user2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    /**
     * Relation avec les messages de la conversation
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Récupère l'autre participant de la conversation
     *
     * @param int $userId
     * @return User|null
     */
    public function getOtherParticipant(int $userId): ?User
    {
        if ($this->user1_id === $userId) {
            return $this->user2;
        }
        
        if ($this->user2_id === $userId) {
            return $this->user1;
        }
        
        return null;
    }

    /**
     * Vérifie si un utilisateur participe à cette conversation
     *
     * @param int $userId
     * @return bool
     */
    public function hasParticipant(int $userId): bool
    {
        return $this->user1_id === $userId || $this->user2_id === $userId;
    }

    /**
     * Compte le nombre de messages non lus pour un utilisateur
     *
     * @param int $userId
     * @return int
     */
    public function countUnreadMessagesForUser(int $userId): int
    {
        return $this->messages()
            ->where('destinataire_id', $userId)
            ->where('lu', false)
            ->count();
    }

    /**
     * Met à jour le dernier message de la conversation
     *
     * @param string $message
     * @return void
     */
    public function updateLastMessage(string $message): void
    {
        $this->update([
            'derniere_message' => substr($message, 0, 100) // Limite à 100 caractères
        ]);
    }
}
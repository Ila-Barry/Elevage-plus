<?php
// app/Services/MessagingService.php

namespace App\Services;

use App\Models\User;
use App\Models\Message;
use App\Models\Conversation;
use App\Events\MessageSent;
use App\Events\MessageRead;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;

/**
 * Service de gestion de la messagerie
 * Contient la logique métier pour les conversations et messages
 */
class MessagingService
{
    /**
     * Récupère ou crée une conversation entre deux utilisateurs
     *
     * @param int $user1Id
     * @param int $user2Id
     * @return Conversation
     */
    public function getOrCreateConversation(int $user1Id, int $user2Id): Conversation
    {
        // Vérifier si une conversation existe déjà
        $conversation = Conversation::where(function ($query) use ($user1Id, $user2Id) {
            $query->where('user1_id', $user1Id)
                  ->where('user2_id', $user2Id);
        })->orWhere(function ($query) use ($user1Id, $user2Id) {
            $query->where('user1_id', $user2Id)
                  ->where('user2_id', $user1Id);
        })->first();

        // Créer une nouvelle conversation si elle n'existe pas
        if (!$conversation) {
            $conversation = Conversation::create([
                'user1_id' => min($user1Id, $user2Id),
                'user2_id' => max($user1Id, $user2Id),
                'derniere_message' => null,
            ]);
        }

        return $conversation;
    }
     /**
     * Envoie un message texte
     *
     * @param int $expediteurId
     * @param int $destinataireId
     * @param string $contenu
     * @return Message
     */
    public function sendTextMessage(int $expediteurId, int $destinataireId, string $contenu): Message
    {
        return $this->sendMessage($expediteurId, $destinataireId, [
            'type' => 'text',
            'contenu' => $contenu,
        ]);
    }

    /**
     * Envoie un message avec média
     *
     * @param int $expediteurId
     * @param int $destinataireId
     * @param UploadedFile $media
     * @param string $type
     * @param string|null $contenu
     * @return Message
     * @throws \Exception
     */
    public function sendMediaMessage(
        int $expediteurId, 
        int $destinataireId, 
        UploadedFile $media, 
        string $type, 
        ?string $contenu = null
    ): Message {
        $mediaUploadService = app(MediaUploadService::class);
        
        // Upload du média selon le type
        $mediaData = match($type) {
            'image' => $mediaUploadService->uploadImage($media),
            'video' => $mediaUploadService->uploadVideo($media),
            'sticker' => $mediaUploadService->uploadSticker($media),
            default => $mediaUploadService->uploadFile($media),
        };
        
        return $this->sendMessage($expediteurId, $destinataireId, array_merge($mediaData, [
            'contenu' => $contenu,
        ]));
    }

    /**
     * Envoie un sticker prédéfini
     *
     * @param int $expediteurId
     * @param int $destinataireId
     * @param string $stickerId
     * @return Message
     */
    public function sendSticker(int $expediteurId, int $destinataireId, string $stickerId): Message
    {
        // Récupérer l'URL du sticker depuis la configuration ou la base de données
        $stickerUrl = $this->getStickerUrl($stickerId);
        
        return $this->sendMessage($expediteurId, $destinataireId, [
            'type' => 'sticker',
            'media_url' => $stickerUrl,
            'media_type' => 'image/png',
            'file_name' => "sticker_{$stickerId}.png",
            'contenu' => "🎨 Sticker envoyé",
        ]);
    }

    /**
     * Envoie un emoji seul ou avec texte
     *
     * @param int $expediteurId
     * @param int $destinataireId
     * @param string $emoji
     * @param string|null $contenu
     * @return Message
     */
    public function sendEmojiMessage(int $expediteurId, int $destinataireId, string $emoji, ?string $contenu = null): Message
    {
        $fullContent = $emoji;
        if ($contenu) {
            $fullContent = $emoji . ' ' . $contenu;
        }
        
        return $this->sendTextMessage($expediteurId, $destinataireId, $fullContent);
    }

    /**
     * Méthode générique d'envoi de message
     *
     * @param int $expediteurId
     * @param int $destinataireId
     * @param array $messageData
     * @return Message
     * @throws \Exception
     */
    private function sendMessage(int $expediteurId, int $destinataireId, array $messageData): Message
    {
        DB::beginTransaction();

        try {
            // Récupérer ou créer la conversation
            $conversation = $this->getOrCreateConversation($expediteurId, $destinataireId);

            // Créer le message
            $message = Message::create(array_merge($messageData, [
                'conversation_id' => $conversation->id,
                'expediteur_id' => $expediteurId,
                'destinataire_id' => $destinataireId,
                'lu' => false,
            ]));

            // Mettre à jour le dernier message de la conversation
            $displayMessage = $this->getDisplayMessage($message);
            $conversation->updateLastMessage($displayMessage);

            DB::commit();

            // Charger les relations pour l'event
            $message->load(['expediteur', 'destinataire', 'conversation']);

            // Broadcast l'event pour le temps réel
            broadcast(new MessageSent($message))->toOthers();

            return $message;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Récupère le message à afficher dans la liste des conversations
     *
     * @param Message $message
     * @return string
     */
    private function getDisplayMessage(Message $message): string
    {
        return match($message->type) {
            'image' => '📷 Image',
            'video' => '🎥 Vidéo',
            'file' => '📎 Fichier: ' . ($message->file_name ?? 'Document'),
            'sticker' => '🎨 Sticker',
            default => $message->contenu ?: 'Message',
        };
    }

    /**
     * Récupère l'URL d'un sticker prédéfini
     *
     * @param string $stickerId
     * @return string
     */
    private function getStickerUrl(string $stickerId): string
    {
        // Stickers prédéfinis
        $stickers = [
            'hello' => '/storage/stickers/hello.png',
            'thank_you' => '/storage/stickers/thank_you.png',
            'good_job' => '/storage/stickers/good_job.png',
            'question' => '/storage/stickers/question.png',
            'celebrate' => '/storage/stickers/celebrate.png',
            'cow' => '/storage/stickers/cow.png',
            'chicken' => '/storage/stickers/chicken.png',
            'sheep' => '/storage/stickers/sheep.png',
        ];
        
        return $stickers[$stickerId] ?? $stickers['hello'];
    }

    /**
     * Récupère les conversations d'un utilisateur
     *
     * @param int $userId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getUserConversations(int $userId, int $perPage = 20): LengthAwarePaginator
    {
        return Conversation::where('user1_id', $userId)
            ->orWhere('user2_id', $userId)
            ->with(['user1', 'user2'])
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Récupère les messages d'une conversation
     *
     * @param int $conversationId
     * @param int $userId
     * @param int $perPage
     * @return LengthAwarePaginator
     * @throws \Exception
     */
    public function getConversationMessages(int $conversationId, int $userId, int $perPage = 50): LengthAwarePaginator
    {
        // Vérifier que l'utilisateur a accès à cette conversation
        $conversation = Conversation::findOrFail($conversationId);
        
        if (!$conversation->hasParticipant($userId)) {
            throw new \Exception('Vous n\'avez pas accès à cette conversation.', 403);
        }

        // Récupérer les messages avec pagination
        $messages = Message::where('conversation_id', $conversationId)
            ->with(['expediteur', 'destinataire'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return $messages;
    }

    /**
     * Marque les messages non lus comme lus
     *
     * @param int $conversationId
     * @param int $userId
     * @return int Nombre de messages marqués comme lus
     * @throws \Exception
     */
    public function markMessagesAsRead(int $conversationId, int $userId): int
    {
        // Vérifier que l'utilisateur a accès à cette conversation
        $conversation = Conversation::findOrFail($conversationId);
        
        if (!$conversation->hasParticipant($userId)) {
            throw new \Exception('Vous n\'avez pas accès à cette conversation.', 403);
        }

        // Marquer tous les messages non lus comme lus
        $updatedCount = Message::where('conversation_id', $conversationId)
            ->where('destinataire_id', $userId)
            ->where('lu', false)
            ->update([
                'lu' => true,
                'lu_at' => now(),
            ]);

        if ($updatedCount > 0) {
            // Broadcast l'event pour le temps réel
            broadcast(new MessageRead($conversation, $userId))->toOthers();
        }

        return $updatedCount;
    }

    /**
     * Récupère le nombre total de messages non lus pour un utilisateur
     *
     * @param int $userId
     * @return int
     */
    public function getTotalUnreadCount(int $userId): int
    {
        return Message::where('destinataire_id', $userId)
            ->where('lu', false)
            ->count();
    }

    /**
     * Supprime un message (soft delete ou hard delete selon configuration)
     *
     * @param int $messageId
     * @param int $userId
     * @return bool
     * @throws \Exception
     */
    public function deleteMessage(int $messageId, int $userId): bool
    {
        $message = Message::findOrFail($messageId);
        
        // Seul l'expéditeur peut supprimer son message
        if ($message->expediteur_id !== $userId) {
            throw new \Exception('Vous ne pouvez supprimer que vos propres messages.', 403);
        }

        // Mettre à jour le dernier message de la conversation
        $conversation = $message->conversation;
        
        $deleted = $message->delete();

        if ($deleted) {
            // Mettre à jour le dernier message de la conversation avec le message le plus récent
            $lastMessage = $conversation->messages()
                ->orderBy('created_at', 'desc')
                ->first();
            
            $conversation->updateLastMessage($lastMessage ? $lastMessage->contenu : '');
        }

        return $deleted;
    }

    /**
     * Vérifie si deux utilisateurs peuvent communiquer
     * (ni l'un ni l'autre n'est banni)
     *
     * @param User $expediteur
     * @param User $destinataire
     * @return bool
     */
    public function canCommunicate(User $expediteur, User $destinataire): bool
    {
        // Vérifier que les deux utilisateurs sont actifs et non bannis
        return $expediteur->status === 'active' && $destinataire->status === 'active';
    }
}

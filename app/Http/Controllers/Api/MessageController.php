<?php
// app/Http/Controllers/Api/MessageController.php (Ajouter les méthodes pour les médias)

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Services\MessagingService;
use App\Services\MediaUploadService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SendMessageRequest;
use App\Http\Requests\Api\GetConversationsRequest;
use App\Http\Requests\Api\GetMessagesRequest;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\MessageResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Contrôleur MessageController
 * 
 * Gère toutes les opérations liées à la messagerie :
 * - Envoi de messages
 * - Consultation des conversations
 * - Gestion des messages lus/non lus
 * - Suppression de messages
 * 
 * @package App\Http\Controllers\Api
 */
class MessageController extends Controller
{
    use ApiResponseTrait;

    /**
     * Service de gestion de la messagerie
     *
     * @var MessagingService
     */
    protected MessagingService $messagingService;

    protected MediaUploadService $mediaUploadService;

    public function __construct(MessagingService $messagingService, MediaUploadService $mediaUploadService)
    {
        $this->messagingService = $messagingService;
        $this->mediaUploadService = $mediaUploadService;
        $this->middleware('auth:api');
    }

        /**
     * Envoie un message (supporte texte, images, vidéos, fichiers, stickers)
     *
     * @param SendMessageRequest $request
     * @return JsonResponse
     */
    public function sendMessage(SendMessageRequest $request): JsonResponse
    {
        try {
            $expediteur = auth()->user();
            $destinataire = User::findOrFail($request->destinataire_id);

            // Vérifier que l'expéditeur et le destinataire peuvent communiquer
            if (!$this->messagingService->canCommunicate($expediteur, $destinataire)) {
                return $this->forbiddenResponse(
                    'Impossible d\'envoyer le message. Vérifiez que votre compte et celui du destinataire sont actifs.'
                );
            }

            $message = null;

            // Cas 1: Envoi avec fichier média
            if ($request->hasFile('media')) {
                $type = $request->input('type', 'file');
                $message = $this->messagingService->sendMediaMessage(
                    $expediteur->id,
                    $destinataire->id,
                    $request->file('media'),
                    $type,
                    $request->input('contenu')
                );
            }
            // Cas 2: Envoi de sticker prédéfini
            elseif ($request->input('sticker_id')) {
                $message = $this->messagingService->sendSticker(
                    $expediteur->id,
                    $destinataire->id,
                    $request->input('sticker_id')
                );
            }
            // Cas 3: Envoi d'emoji
            elseif ($request->input('emoji')) {
                $message = $this->messagingService->sendEmojiMessage(
                    $expediteur->id,
                    $destinataire->id,
                    $request->input('emoji'),
                    $request->input('contenu')
                );
            }
            // Cas 4: Message texte simple
            else {
                $contenu = $request->input('contenu', '');
                if (empty($contenu)) {
                    return $this->errorResponse('Le message ne peut pas être vide.', 422);
                }
                $message = $this->messagingService->sendTextMessage(
                    $expediteur->id,
                    $destinataire->id,
                    $contenu
                );
            }

            $message->load(['expediteur', 'destinataire', 'conversation']);

            return $this->successResponse(
                new MessageResource($message),
                'Message envoyé avec succès.',
                201
            );

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Destinataire non trouvé.');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'envoi du message: ' . $e->getMessage());
            return $this->errorResponse(
                'Erreur lors de l\'envoi du message: ' . $e->getMessage(),
                $e->getCode() === 403 ? 403 : 500
            );
        }
    }

    /**
     * Récupère la liste des conversations de l'utilisateur connecté
     *
     * @param GetConversationsRequest $request
     * @return JsonResponse
     */
    public function getConversations(GetConversationsRequest $request): JsonResponse
    {
        try {
            $userId = auth()->id();
            $perPage = $request->get('per_page', 20);

            $conversations = $this->messagingService->getUserConversations($userId, $perPage);

            return $this->successResponse([
                'conversations' => ConversationResource::collection($conversations),
                'pagination' => [
                    'current_page' => $conversations->currentPage(),
                    'per_page' => $conversations->perPage(),
                    'total' => $conversations->total(),
                    'last_page' => $conversations->lastPage(),
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération des conversations: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la récupération des conversations.', 500);
        }
    }

    /**
     * Récupère les messages d'une conversation spécifique
     *
     * @param int $conversationId
     * @param GetMessagesRequest $request
     * @return JsonResponse
     */
    public function getMessages(int $conversationId, GetMessagesRequest $request): JsonResponse
    {
        try {
            $userId = auth()->id();
            $perPage = $request->get('per_page', 50);

            $messages = $this->messagingService->getConversationMessages(
                $conversationId,
                $userId,
                $perPage
            );

            // Marquer automatiquement les messages comme lus
            $this->messagingService->markMessagesAsRead($conversationId, $userId);

            return $this->successResponse([
                'messages' => MessageResource::collection($messages),
                'conversation_id' => $conversationId,
                'pagination' => [
                    'current_page' => $messages->currentPage(),
                    'per_page' => $messages->perPage(),
                    'total' => $messages->total(),
                    'last_page' => $messages->lastPage(),
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération des messages: ' . $e->getMessage());
            
            if ($e->getCode() === 403) {
                return $this->forbiddenResponse($e->getMessage());
            }
            
            return $this->errorResponse('Erreur lors de la récupération des messages.', 500);
        }
    }

    /**
     * Récupère le nombre total de messages non lus
     *
     * @return JsonResponse
     */
    public function getUnreadCount(): JsonResponse
    {
        try {
            $userId = auth()->id();
            $unreadCount = $this->messagingService->getTotalUnreadCount($userId);

            return $this->successResponse([
                'unread_count' => $unreadCount,
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération du compteur non lu: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la récupération du compteur.', 500);
        }
    }

    /**
     * Marque tous les messages d'une conversation comme lus
     *
     * @param int $conversationId
     * @return JsonResponse
     */
    public function markConversationAsRead(int $conversationId): JsonResponse
    {
        try {
            $userId = auth()->id();
            
            $updatedCount = $this->messagingService->markMessagesAsRead(
                $conversationId,
                $userId
            );

            return $this->successResponse([
                'conversation_id' => $conversationId,
                'marked_as_read' => $updatedCount,
            ], "$updatedCount message(s) marqué(s) comme lu(s).");

        } catch (\Exception $e) {
            \Log::error('Erreur lors du marquage des messages: ' . $e->getMessage());
            
            if ($e->getCode() === 403) {
                return $this->forbiddenResponse($e->getMessage());
            }
            
            return $this->errorResponse('Erreur lors du marquage des messages.', 500);
        }
    }

        /**
     * Supprime un message avec son média associé
     *
     * @param int $messageId
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteMessage(int $messageId, Request $request): JsonResponse
    {
        try {
            $userId = auth()->id();
            $deleteForEveryone = $request->input('delete_for_everyone', false);
            
            $message = \App\Models\Message::findOrFail($messageId);
            
            // Seul l'expéditeur peut supprimer
            if ($message->expediteur_id !== $userId) {
                return $this->forbiddenResponse('Vous ne pouvez supprimer que vos propres messages.');
            }
            
            // Supprimer le média associé
            if ($message->media_url && $deleteForEveryone) {
                $this->mediaUploadService->deleteMedia($message->media_url);
                if ($message->thumbnail_url) {
                    $this->mediaUploadService->deleteMedia($message->thumbnail_url);
                }
            }
            
            if ($deleteForEveryone) {
                // Supprimer définitivement le message pour tout le monde
                $message->delete();
            } else {
                // Marquer comme supprimé seulement pour l'utilisateur (soft delete côté utilisateur)
                $message->update([
                    'is_deleted' => true,
                    'contenu' => '[Message supprimé]',
                    'media_url' => null,
                ]);
            }
            
            // Mettre à jour le dernier message de la conversation
            $conversation = $message->conversation;
            $lastMessage = $conversation->messages()
                ->where('is_deleted', false)
                ->orderBy('created_at', 'desc')
                ->first();
            
            $conversation->updateLastMessage($lastMessage ? $this->getDisplayMessageForConversation($lastMessage) : '');
            
            return $this->successResponse(null, 'Message supprimé avec succès.');
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la suppression du message: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la suppression du message.', 500);
        }
    }
    
    /**
     * Télécharge un fichier média (upload temporaire avant envoi)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadMedia(Request $request): JsonResponse
    {
        $request->validate([
            'media' => 'required|file|max:51200', // 50 MB max
            'type' => 'required|in:image,video,file,sticker',
        ]);
        
        try {
            $file = $request->file('media');
            $type = $request->input('type');
            
            $mediaData = match($type) {
                'image' => $this->mediaUploadService->uploadImage($file, false),
                'video' => $this->mediaUploadService->uploadVideo($file),
                'sticker' => $this->mediaUploadService->uploadSticker($file),
                default => $this->mediaUploadService->uploadFile($file),
            };
            
            return $this->successResponse($mediaData, 'Fichier téléchargé avec succès.');
            
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }
    
    /**
     * Récupère la liste des stickers disponibles
     *
     * @return JsonResponse
     */
    public function getAvailableStickers(): JsonResponse
    {
        $stickers = [
            ['id' => 'hello', 'name' => 'Bonjour', 'url' => '/storage/stickers/hello.png', 'category' => 'greetings'],
            ['id' => 'thank_you', 'name' => 'Merci', 'url' => '/storage/stickers/thank_you.png', 'category' => 'greetings'],
            ['id' => 'good_job', 'name' => 'Bon travail', 'url' => '/storage/stickers/good_job.png', 'category' => 'encouragement'],
            ['id' => 'question', 'name' => 'Question', 'url' => '/storage/stickers/question.png', 'category' => 'questions'],
            ['id' => 'celebrate', 'name' => 'Félicitations', 'url' => '/storage/stickers/celebrate.png', 'category' => 'celebrations'],
            ['id' => 'cow', 'name' => 'Vache', 'url' => '/storage/stickers/cow.png', 'category' => 'animals'],
            ['id' => 'chicken', 'name' => 'Poulet', 'url' => '/storage/stickers/chicken.png', 'category' => 'animals'],
            ['id' => 'sheep', 'name' => 'Mouton', 'url' => '/storage/stickers/sheep.png', 'category' => 'animals'],
            ['id' => 'pig', 'name' => 'Cochon', 'url' => '/storage/stickers/pig.png', 'category' => 'animals'],
            ['id' => 'hospital', 'name' => 'Urgence vétérinaire', 'url' => '/storage/stickers/vet.png', 'category' => 'health'],
        ];
        
        return $this->successResponse($stickers, 'Liste des stickers disponibles.');
    }
    
    /**
     * Helper pour le message d'affichage dans la conversation
     *
     * @param Message $message
     * @return string
     */
    private function getDisplayMessageForConversation(Message $message): string
    {
        if ($message->is_deleted) {
            return '[Message supprimé]';
        }
        
        return match($message->type) {
            'image' => '📷 Image',
            'video' => '🎥 Vidéo',
            'file' => '📎 ' . ($message->file_name ?? 'Fichier'),
            'sticker' => '🎨 Sticker',
            default => $message->contenu ?: 'Message',
        };
    }

    /**
     * Rechercher des utilisateurs (pour la messagerie)
     */
    public function searchUsers(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        $userId = auth()->id();
        
        if (strlen($query) < 2) {
            return $this->successResponse([], 'Tapez au moins 2 caractères.');
        }
        
        $users = User::where('id', '!=', $userId)
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->where('status', 'active')
            ->limit(10)
            ->get(['id', 'name', 'email', 'photo_url']);
        
        return $this->successResponse($users);
    }

    public function getAllUsersForMessaging()
    {
        try {
            // Sélection des colonnes réelles de votre migration
            $users = \App\Models\User::select('id', 'name', 'role', 'photo_url')
                ->where('status', 'active') // Optionnel: n'afficher que les utilisateurs actifs
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Utilisateurs récupérés avec succès',
                'data'    => $users
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des utilisateurs',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Créer une nouvelle conversation
     */
    public function createConversation(Request $request): JsonResponse
    {
        $request->validate([
            'destinataire_id' => 'required|exists:users,id|different:user_id',
        ]);
        
        try {
            $userId = auth()->id();
            $destinataireId = $request->destinataire_id;
            
            // Vérifier si la conversation existe déjà
            $conversation = Conversation::where(function ($query) use ($userId, $destinataireId) {
                $query->where('user1_id', $userId)->where('user2_id', $destinataireId);
            })->orWhere(function ($query) use ($userId, $destinataireId) {
                $query->where('user1_id', $destinataireId)->where('user2_id', $userId);
            })->first();
            
            if (!$conversation) {
                $conversation = Conversation::create([
                    'user1_id' => min($userId, $destinataireId),
                    'user2_id' => max($userId, $destinataireId),
                    'derniere_message' => 'Nouvelle conversation',
                ]);
                
                Log::info('Nouvelle conversation créée', [
                    'user1' => $userId,
                    'user2' => $destinataireId,
                    'conversation' => $conversation->id
                ]);
            }
            
            $conversation->load(['user1', 'user2']);
            
            return $this->successResponse(
                $conversation,
                'Conversation créée avec succès.',
                201
            );
            
        } catch (\Exception $e) {
            Log::error('Erreur création conversation: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la création de la conversation.', 500);
        }
    }
}
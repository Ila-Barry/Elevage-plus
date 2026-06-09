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

    /**
     * Constructeur avec injection de dépendances
     *
     * @param MessagingService $messagingService
     */
    public function __construct(MessagingService $messagingService)
    {
        $this->messagingService = $messagingService;
        
        // Toutes les méthodes nécessitent une authentification
        $this->middleware('auth:api');
    }

    /**
     * Envoie un message à un autre éleveur
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

            // Envoyer le message
            $message = $this->messagingService->sendMessage(
                $expediteur->id,
                $destinataire->id,
                $request->contenu
            );

            // Charger les relations pour la réponse
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
     * Supprime un message
     *
     * @param int $messageId
     * @return JsonResponse
     */
    public function deleteMessage(int $messageId): JsonResponse
    {
        try {
            $userId = auth()->id();
            
            $deleted = $this->messagingService->deleteMessage($messageId, $userId);

            if ($deleted) {
                return $this->successResponse(null, 'Message supprimé avec succès.');
            }

            return $this->errorResponse('Impossible de supprimer le message.', 400);

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la suppression du message: ' . $e->getMessage());
            
            if ($e->getCode() === 403) {
                return $this->forbiddenResponse($e->getMessage());
            }
            
            return $this->errorResponse('Erreur lors de la suppression du message.', 500);
        }
    }
}
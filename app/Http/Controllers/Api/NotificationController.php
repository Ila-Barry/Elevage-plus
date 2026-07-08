<?php
// app/Http/Controllers/Api/NotificationController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // ⚠️ AJOUTER CETTE LIGNE

class NotificationController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Liste des notifications non lues
     */
    public function unread(Request $request)
    {
        try {
            $user = $request->user();
            $notifications = $user->unreadNotifications()->latest()->limit(50)->get();
            
            Log::info('📋 Récupération notifications non lues', [
                'user_id' => $user->id,
                'count' => $notifications->count()
            ]);
            
            return $this->successResponse([
                'data' => NotificationResource::collection($notifications),
                'count' => $notifications->count(),
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur récupération notifications non lues: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors du chargement des notifications.', 500);
        }
    }

    /**
     * Liste de toutes les notifications (paginated)
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            $perPage = $request->get('per_page', 20);
            $notifications = $user->notifications()->latest()->paginate($perPage);
            
            Log::info('📋 Récupération toutes les notifications', [
                'user_id' => $user->id,
                'total' => $notifications->total(),
                'per_page' => $perPage
            ]);
            
            // Transformer les notifications avec NotificationResource
            $data = NotificationResource::collection($notifications);
            
            // Structure de réponse standardisée
            return $this->successResponse([
                'data' => $data,
                'meta' => [
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total(),
                    'unread_count' => $user->unreadNotifications()->count(),
                ],
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur récupération notifications: ' . $e->getMessage(), [
                'user_id' => $request->user()?->id,
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse('Erreur lors du chargement des notifications.', 500);
        }
    }

    /**
     * Marquer une notification comme lue
     */
    public function markAsRead(Request $request, string $id)
    {
        try {
            $user = $request->user();
            $notification = $user->notifications()->findOrFail($id);
            $notification->markAsRead();
            
            Log::info('✅ Notification marquée comme lue', [
                'user_id' => $user->id,
                'notification_id' => $id
            ]);
            
            return $this->successResponse(null, 'Notification marquée comme lue.');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Notification non trouvée.');
        } catch (\Exception $e) {
            Log::error('Erreur marquage notification: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors du marquage.', 500);
        }
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead(Request $request)
    {
        try {
            $user = $request->user();
            $count = $user->unreadNotifications()->count();
            $user->unreadNotifications->markAsRead();
            
            Log::info('✅ Toutes les notifications marquées comme lues', [
                'user_id' => $user->id,
                'count' => $count
            ]);
            
            return $this->successResponse([
                'marked_count' => $count,
                'unread_count' => 0,
            ], 'Toutes les notifications ont été marquées comme lues.');
            
        } catch (\Exception $e) {
            Log::error('Erreur marquage toutes notifications: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors du marquage.', 500);
        }
    }

    /**
     * Supprimer une notification
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $user = $request->user();
            $notification = $user->notifications()->findOrFail($id);
            $notification->delete();
            
            Log::info('🗑️ Notification supprimée', [
                'user_id' => $user->id,
                'notification_id' => $id
            ]);
            
            return $this->successResponse(null, 'Notification supprimée.');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Notification non trouvée.');
        } catch (\Exception $e) {
            Log::error('Erreur suppression notification: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la suppression.', 500);
        }
    }

    /**
     * Supprimer toutes les notifications
     */
    public function destroyAll(Request $request)
    {
        try {
            $user = $request->user();
            $count = $user->notifications()->count();
            $user->notifications()->delete();
            
            Log::info('🗑️ Toutes les notifications supprimées', [
                'user_id' => $user->id,
                'count' => $count
            ]);
            
            return $this->successResponse([
                'deleted_count' => $count,
            ], 'Toutes les notifications ont été supprimées.');
            
        } catch (\Exception $e) {
            Log::error('Erreur suppression toutes notifications: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la suppression.', 500);
        }
    }

    /**
     * Statistiques des notifications
     */
    public function stats(Request $request)
    {
        try {
            $user = $request->user();
            
            $notifications = $user->notifications()->get();
            
            $stats = [
                'total' => $notifications->count(),
                'unread' => $user->unreadNotifications()->count(),
                'read' => $user->readNotifications()->count(),
            ];
            
            return $this->successResponse($stats);
            
        } catch (\Exception $e) {
            Log::error('Erreur statistiques notifications: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors du chargement des statistiques.', 500);
        }
    }
}
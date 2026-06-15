<?php
// app/Http/Controllers/Api/NotificationController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Contrôleur NotificationController
 * 
 * Gère les notifications de l'utilisateur connecté
 */
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
        $user = $request->user();
        $notifications = $user->unreadNotifications()->latest()->limit(50)->get();
        
        return $this->successResponse([
            'count' => $user->unreadNotifications()->count(),
            'data' => NotificationResource::collection($notifications),
        ]);
    }

    /**
     * Liste de toutes les notifications (paginated)
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $perPage = $request->get('per_page', 20);
        $notifications = $user->notifications()->latest()->paginate($perPage);
        
        return $this->successResponse([
            'data' => NotificationResource::collection($notifications),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
                'unread_count' => $user->unreadNotifications()->count(),
            ],
        ]);
    }

    /**
     * Marquer une notification comme lue
     */
    public function markAsRead(Request $request, string $id)
    {
        $user = $request->user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        return $this->successResponse(null, 'Notification marquée comme lue.');
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        $user->unreadNotifications->markAsRead();
        
        return $this->successResponse(null, 'Toutes les notifications ont été marquées comme lues.');
    }

    /**
     * Supprimer une notification
     */
    public function destroy(Request $request, string $id)
    {
        $user = $request->user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->delete();
        
        return $this->successResponse(null, 'Notification supprimée.');
    }

    /**
     * Supprimer toutes les notifications
     */
    public function destroyAll(Request $request)
    {
        $user = $request->user();
        $user->notifications()->delete();
        
        return $this->successResponse(null, 'Toutes les notifications ont été supprimées.');
    }

    /**
     * Statistiques des notifications (CORRIGÉ)
     */
    public function stats(Request $request)
    {
        $user = $request->user();
        
        // Récupérer toutes les notifications
        $notifications = $user->notifications()->get();
        
        // Compter par type manuellement
        $byType = [];
        foreach ($notifications as $notification) {
            $type = $notification->data['type'] ?? 'info';
            if (!isset($byType[$type])) {
                $byType[$type] = 0;
            }
            $byType[$type]++;
        }
        
        // Transformer en collection
        $byTypeCollection = collect($byType)->map(function ($count, $type) {
            return (object)['type' => $type, 'count' => $count];
        })->values();
        
        // Compter par jour
        $byDay = $user->notifications()
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(7)
            ->get();
        
        $stats = [
            'total' => $notifications->count(),
            'unread' => $user->unreadNotifications()->count(),
            'read' => $user->readNotifications()->count(),
            'by_type' => $byTypeCollection,
            'by_day' => $byDay,
        ];
        
        return $this->successResponse($stats);
    }
}
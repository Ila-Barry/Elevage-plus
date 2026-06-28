<?php
// app/Http/Controllers/Api/NotificationController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $count = $user->unreadNotifications()->count();
        $user->unreadNotifications->markAsRead();
        
        return $this->successResponse([
            'marked_count' => $count,
            'unread_count' => 0,
        ], 'Toutes les notifications ont été marquées comme lues.');
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
        $count = $user->notifications()->count();
        $user->notifications()->delete();
        
        return $this->successResponse([
            'deleted_count' => $count,
        ], 'Toutes les notifications ont été supprimées.');
    }

    /**
     * Statistiques des notifications
     */
    public function stats(Request $request)
    {
        $user = $request->user();
        
        // Récupérer toutes les notifications
        $notifications = $user->notifications()->get();
        
        // Compter par type
        $byType = [];
        foreach ($notifications as $notification) {
            $data = $notification->data ?? [];
            $type = $data['type'] ?? 'info';
            if (!isset($byType[$type])) {
                $byType[$type] = 0;
            }
            $byType[$type]++;
        }
        
        // Transformer en collection
        $byTypeCollection = collect($byType)->map(function ($count, $type) {
            return (object)['type' => $type, 'count' => $count];
        })->values();
        
        // Compter par jour (7 derniers jours)
        $byDay = $user->notifications()
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'desc')
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

    /**
     * Récupérer les notifications par type
     */
    public function byType(Request $request, string $type)
    {
        $user = $request->user();
        
        $perPage = $request->get('per_page', 20);
        $notifications = $user->notifications()
            ->where('data->type', $type)
            ->latest()
            ->paginate($perPage);
        
        return $this->successResponse([
            'data' => NotificationResource::collection($notifications),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ],
        ]);
    }
}
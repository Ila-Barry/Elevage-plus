<?php
// app/Http/Controllers/Api/Admin/AdminController.php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Publication;
use App\Models\Report;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

/**
 * Controller AdminController
 * 
 * Contrôleur générique pour les opérations administratives
 * Point d'entrée pour les actions rapides
 */
class AdminController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware(['auth:api', 'admin']);
    }

    /**
     * Tableau de bord simplifié
     */
    public function dashboard(Request $request)
    {
        return $this->successResponse([
            'total_utilisateurs' => User::count(),
            'total_publications' => Publication::count(),
            'total_signalements' => Report::count(),
            'signalements_en_attente' => Report::where('statut', 'en_attente')->count(),
            'nouveaux_aujourdhui' => User::whereDate('created_at', today())->count(),
            'dernieres_activites' => $this->getLastActivities(),
        ]);
    }

    /**
     * Récupère les dernières activités
     */
    protected function getLastActivities(): array
    {
        $activities = [];

        // Derniers signalements
        $reports = Report::with(['user', 'publication'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($report) {
                return [
                    'type' => 'signalement',
                    'message' => "Signalement de {$report->user->name} sur '{$report->publication->titre}'",
                    'icon' => '⚠️',
                    'created_at' => $report->created_at->diffForHumans(),
                ];
            });

        // Dernières inscriptions
        $users = User::latest()
            ->limit(5)
            ->get()
            ->map(function ($user) {
                return [
                    'type' => 'inscription',
                    'message' => "Nouvel utilisateur : {$user->name}",
                    'icon' => '👤',
                    'created_at' => $user->created_at->diffForHumans(),
                ];
            });

        // Dernières publications
        $publications = Publication::with('user')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($pub) {
                return [
                    'type' => 'publication',
                    'message' => "Nouvelle publication de {$pub->user->name} : '{$pub->titre}'",
                    'icon' => '📝',
                    'created_at' => $pub->created_at->diffForHumans(),
                ];
            });

        return $reports->concat($users)->concat($publications)
            ->sortByDesc('created_at')
            ->take(10)
            ->values()
            ->toArray();
    }

    /**
     * Statistiques globales rapides
     */
    public function stats(Request $request)
    {
        return $this->successResponse([
            'utilisateurs' => [
                'total' => User::count(),
                'actifs' => User::where('status', 'active')->count(),
                'bannis' => User::where('status', 'bannie')->count(),
                'eleveurs' => User::where('role', 'eleveur')->count(),
                'admins' => User::where('role', 'admin')->count(),
            ],
            'publications' => [
                'total' => Publication::count(),
                'publiees' => Publication::where('statut', 'publiee')->count(),
                'signalees' => Publication::where('statut', 'signalee')->count(),
                'bloquees' => Publication::where('statut', 'bloquee')->count(),
            ],
            'signalements' => [
                'total' => Report::count(),
                'en_attente' => Report::where('statut', 'en_attente')->count(),
                'traites' => Report::where('statut', 'traite')->count(),
                'ignores' => Report::where('statut', 'ignore')->count(),
            ],
        ]);
    }
}
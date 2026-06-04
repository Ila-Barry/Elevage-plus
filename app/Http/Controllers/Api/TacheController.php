<?php
// app/Http/Controllers/Api/TacheController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TacheRequest;
use App\Http\Requests\Api\UpdateTacheRequest;
use App\Http\Resources\TacheResource;
use App\Models\Tache;
use App\Models\Elevage;
use App\Services\TacheService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Contrôleur TacheController
 * 
 * Gère toutes les opérations CRUD pour les tâches
 * Conforme au cahier des charges:
 * - CRUD complet
 * - Filtrage par date
 * - Marquer comme terminée
 * - Vue calendrier (FullCalendar)
 * - Tâches liées à un animal ou un élevage
 * - Calcul du nombre total de tâches
 */
class TacheController extends Controller
{
    use ApiResponseTrait;

    protected TacheService $tacheService;

    public function __construct(TacheService $tacheService)
    {
        $this->tacheService = $tacheService;
        
        $this->middleware('auth:api')->except(['index', 'show', 'calendar']);
    }

    /**
     * Liste des tâches avec pagination et filtres
     * GET /api/taches
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = min($request->input('per_page', 10), 50);
            
            $filters = [
                'elevage_id' => $request->input('elevage_id'),
                'animal_id' => $request->input('animal_id'),
                'type' => $request->input('type'),
                'statut' => $request->input('statut'),
                'date_debut' => $request->input('date_debut'),
                'date_fin' => $request->input('date_fin'),
                'search' => $request->input('search'),
            ];
            
            $taches = $this->tacheService->getAllPaginated($perPage, array_filter($filters));
            
            $data = [
                'data' => TacheResource::collection($taches),
                'meta' => [
                    'current_page' => $taches->currentPage(),
                    'last_page' => $taches->lastPage(),
                    'per_page' => $taches->perPage(),
                    'total' => $taches->total(),
                    'from' => $taches->firstItem(),
                    'to' => $taches->lastItem(),
                ],
            ];
            
            if (!empty(array_filter($filters))) {
                $data['filters'] = array_filter($filters);
            }
            
            $data['available_filters'] = [
                'types' => Tache::TYPES,
                'statuts' => ['terminee', 'en_attente', 'en_retard', 'aujourdhui', 'a_venir'],
            ];
            
            return $this->successResponse($data, 'Liste des tâches récupérée avec succès.');
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des tâches: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la récupération des tâches.', 500);
        }
    }

    /**
     * Vue calendrier pour FullCalendar
     * GET /api/taches/calendar
     */
    public function calendar(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'elevage_id' => 'required|exists:elevages,id',
                'start' => 'required|date',
                'end' => 'required|date',
            ]);
            
            $elevageId = $request->input('elevage_id');
            
            // Vérification des droits
            $elevage = Elevage::findOrFail($elevageId);
            if (!auth()->check() && $elevage->proprietaire->profile_visibility !== 'public') {
                return $this->forbiddenResponse('Cet élevage est privé.');
            }
            
            if (auth()->check()) {
                $this->authorize('view', $elevage);
            }
            
            $events = $this->tacheService->getForCalendar(
                $elevageId,
                $request->input('start'),
                $request->input('end')
            );
            
            return $this->successResponse($events, 'Événements calendrier récupérés avec succès.');
            
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->forbiddenResponse('Accès non autorisé à cet élevage.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération du calendrier: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la récupération du calendrier.', 500);
        }
    }

    /**
     * Création d'une nouvelle tâche
     * POST /api/taches
     */
    public function store(TacheRequest $request): JsonResponse
    {
        try {
            $elevage = Elevage::find($request->elevage_id);
            $this->authorize('create', [Tache::class, $elevage]);
            
            $data = $request->validated();
            
            $genererRappels = $request->input('generer_rappels', true);
            $typesRappels = $request->input('rappels', ['48h', '24h', '1h', '30min']);
            
            $tache = $this->tacheService->createTache($data, $genererRappels, $typesRappels);
            
            return $this->successResponse(
                new TacheResource($tache),
                'Tâche créée avec succès.',
                201
            );
            
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->forbiddenResponse('Vous n\'êtes pas autorisé à créer une tâche dans cet élevage.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la tâche: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la création de la tâche.', 500);
        }
    }

    /**
     * Détails d'une tâche
     * GET /api/taches/{id}
     */
    public function show(int $id): JsonResponse
    {
        try {
            $tache = Tache::with(['animal', 'elevage.proprietaire', 'rappels'])->findOrFail($id);
            
            if (auth()->check()) {
                $this->authorize('view', $tache);
            } else {
                $proprietaire = $tache->elevage?->proprietaire;
                if (!$proprietaire || $proprietaire->profile_visibility !== 'public') {
                    return $this->forbiddenResponse('Ce profil est privé.');
                }
            }
            
            $details = $this->tacheService->getTacheDetails($tache);
            
            return $this->successResponse(
                new TacheResource($details),
                'Détails de la tâche récupérés avec succès.'
            );
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Tâche non trouvée.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->forbiddenResponse('Vous n\'avez pas accès à cette tâche.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération de la tâche: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la récupération des détails.', 500);
        }
    }

    /**
     * Mise à jour d'une tâche
     * PUT/PATCH /api/taches/{id}
     */
    public function update(UpdateTacheRequest $request, int $id): JsonResponse
    {
        try {
            $tache = Tache::findOrFail($id);
            $this->authorize('update', $tache);
            
            $data = array_filter($request->validated(), fn($v) => !is_null($v));
            
            $updatedTache = $this->tacheService->updateTache($tache, $data);
            
            return $this->successResponse(
                new TacheResource($updatedTache),
                'Tâche mise à jour avec succès.'
            );
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Tâche non trouvée.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->forbiddenResponse('Vous n\'êtes pas autorisé à modifier cette tâche.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de la tâche: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la mise à jour de la tâche.', 500);
        }
    }

    /**
     * Marquer une tâche comme terminée
     * POST /api/taches/{id}/complete
     */
    public function complete(Request $request, int $id): JsonResponse
    {
        try {
            $tache = Tache::findOrFail($id);
            $this->authorize('complete', $tache);
            
            $dateRealisee = $request->input('date_realisee');
            
            $completedTache = $this->tacheService->completeTache($tache, $dateRealisee);
            
            return $this->successResponse(
                new TacheResource($completedTache),
                'Tâche marquée comme terminée avec succès.'
            );
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Tâche non trouvée.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->forbiddenResponse('Vous n\'êtes pas autorisé à modifier cette tâche.');
        } catch (\Exception $e) {
            Log::error('Erreur lors du marquage de la tâche: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors du marquage de la tâche.', 500);
        }
    }

    /**
     * Suppression d'une tâche
     * DELETE /api/taches/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $tache = Tache::findOrFail($id);
            $this->authorize('delete', $tache);
            
            $this->tacheService->deleteTache($tache);
            
            return $this->successResponse(null, 'Tâche supprimée avec succès.');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Tâche non trouvée.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->forbiddenResponse('Vous n\'êtes pas autorisé à supprimer cette tâche.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de la tâche: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la suppression de la tâche.', 500);
        }
    }

    /**
     * Statistiques des tâches pour un élevage
     * GET /api/elevages/{elevageId}/taches/stats
     */
    public function stats(int $elevageId): JsonResponse
    {
        try {
            $elevage = Elevage::findOrFail($elevageId);
            
            if (!auth()->check() && $elevage->proprietaire->profile_visibility !== 'public') {
                return $this->forbiddenResponse('Cet élevage est privé.');
            }
            
            $stats = $this->tacheService->getTotalTachesCount($elevageId);
            $byType = $this->tacheService->getStatsByType($elevageId);
            
            return $this->successResponse([
                'resume' => $stats,
                'par_type' => $byType,
            ], 'Statistiques récupérées avec succès.');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Élevage non trouvé.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des statistiques: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la récupération des statistiques.', 500);
        }
    }

    /**
     * Tâches de l'utilisateur connecté
     * GET /api/user/taches
     */
    public function userTaches(Request $request): JsonResponse
    {
        try {
            $userId = auth()->id();
            
            $query = Tache::whereHas('elevage', function($q) use ($userId) {
                $q->where('user_id', $userId);
            });
            
            // Filtre par statut
            if ($request->has('statut')) {
                if ($request->statut === 'terminee') {
                    $query->completed();
                } elseif ($request->statut === 'en_retard') {
                    $query->late();
                } elseif ($request->statut === 'aujourdhui') {
                    $query->today()->notCompleted();
                } elseif ($request->statut === 'a_venir') {
                    $query->notCompleted()->where('date_planifiee', '>', today());
                }
            }
            
            $taches = $query->with(['animal', 'elevage'])
                ->orderBy('date_planifiee')
                ->paginate(15);
            
            $stats = $this->tacheService->getTotalTachesCountForUser($userId);
            
            return $this->successResponse([
                'data' => TacheResource::collection($taches),
                'stats' => $stats,
                'meta' => [
                    'current_page' => $taches->currentPage(),
                    'last_page' => $taches->lastPage(),
                    'total' => $taches->total(),
                ],
            ], 'Vos tâches récupérées avec succès.');
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des tâches utilisateur: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la récupération de vos tâches.', 500);
        }
    }
}
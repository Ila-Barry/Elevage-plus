<?php
// app/Http/Controllers/Api/ElevageController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ElevageRequest;
use App\Http\Requests\Api\UpdateElevageRequest;
use App\Http\Resources\ElevageResource;
use App\Models\Elevage;
use App\Services\ElevageService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Contrôleur ElevageController
 * 
 * Gère toutes les opérations CRUD pour les élevages
 * 
 * @package App\Http\Controllers\Api
 */
class ElevageController extends Controller
{
    use ApiResponseTrait;

    /**
     * Service de gestion des élevages
     *
     * @var ElevageService
     */
    protected ElevageService $elevageService;

    /**
     * Constructeur avec injection de dépendances
     *
     * @param ElevageService $elevageService
     */
    public function __construct(ElevageService $elevageService)
    {
        $this->elevageService = $elevageService;
        
        // Application des middlewares pour la sécurité
        $this->middleware('auth:api')->except(['index', 'show']);
    }

    /**
     * Liste des élevages avec pagination
     * GET /api/elevages
     * 
     * @param Request $request     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Paramètres de pagination (conforme ELEV-04: pagination 10 par page)
            $perPage = $request->input('per_page', 10);
            $perPage = min($perPage, 50); // Limite à 50 max
            
            // Filtres optionnels
            $filters = [
                'type' => $request->input('type'),
                'localisation' => $request->input('localisation'),
                'search' => $request->input('search'),
            ];
            
            // Récupération des élevages
            $elevages = $this->elevageService->getAllPaginated($perPage, array_filter($filters));
            
            // Transformation des données
            $data = [
                'data' => ElevageResource::collection($elevages),
                'meta' => [
                    'current_page' => $elevages->currentPage(),
                    'last_page' => $elevages->lastPage(),
                    'per_page' => $elevages->perPage(),
                    'total' => $elevages->total(),
                    'from' => $elevages->firstItem(),
                    'to' => $elevages->lastItem(),
                ],
            ];
            
            // Ajout des filtres actifs dans la réponse
            if (!empty($filters['type'])) {
                $data['filters']['type'] = $filters['type'];
            }
            if (!empty($filters['localisation'])) {
                $data['filters']['localisation'] = $filters['localisation'];
            }
            if (!empty($filters['search'])) {
                $data['filters']['search'] = $filters['search'];
            }
            
            return $this->successResponse($data, 'Liste des élevages récupérée avec succès.');
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération des élevages: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la récupération des élevages.', 500);
        }
    }

    /**
     * Création d'un nouvel élevage
     * POST /api/elevages
     * 
     * @param ElevageRequest $request
     * @return JsonResponse
     */
    public function store(ElevageRequest $request): JsonResponse
    {
        try {
            // Autorisation via Policy
            $this->authorize('create', Elevage::class);
            
            // Préparation des données
            $data = $request->validated();
            
            // Création de l'élevage
            $elevage = $this->elevageService->createElevage(
                $data,
                $request->file('photo')
            );
            
            return $this->successResponse(
                new ElevageResource($elevage),
                'Élevage créé avec succès.',
                201
            );
            
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->forbiddenResponse('Vous n\'êtes pas autorisé à créer un élevage.');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création de l\'élevage: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la création de l\'élevage.', 500);
        }
    }

    /**
     * Détails d'un élevage spécifique
     * GET /api/elevages/{id}
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            // Recherche de l'élevage
            $elevage = Elevage::with(['proprietaire' => function($query) {
                $query->select('id', 'name', 'photo_url', 'bio', 'profile_visibility');
            }])->findOrFail($id);
            
            // Vérification des droits d'accès
            if (auth()->check()) {
                $this->authorize('view', $elevage);
            } else {
                // Non authentifié: seulement si profil public
                if ($elevage->proprietaire->profile_visibility !== 'public') {
                    return $this->forbiddenResponse('Ce profil est privé.');
                }
            }
            
            // Récupération des détails complets
            $details = $this->elevageService->getElevageDetails($elevage);
            
            return $this->successResponse(
                new ElevageResource($details),
                'Détails de l\'élevage récupérés avec succès.'
            );
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Élevage non trouvé.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->forbiddenResponse('Vous n\'avez pas accès à cet élevage.');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération de l\'élevage: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la récupération des détails.', 500);
        }
    }

    /**
     * Mise à jour d'un élevage
     * PUT/PATCH /api/elevages/{id}
     * 
     * @param UpdateElevageRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateElevageRequest $request, int $id): JsonResponse
    {
        try {
            // Recherche de l'élevage
            $elevage = Elevage::findOrFail($id);
            
            // Vérification du propriétaire (conforme ELEV-02 et politique de sécurité)
            $this->authorize('update', $elevage);
            
            // Préparation des données (ne garder que les champs modifiés)
            $data = array_filter($request->validated(), function($value) {
                return !is_null($value);
            });
            
            // Mise à jour
            $updatedElevage = $this->elevageService->updateElevage(
                $elevage,
                $data,
                $request->file('photo')
            );
            
            return $this->successResponse(
                new ElevageResource($updatedElevage),
                'Élevage mis à jour avec succès.'
            );
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Élevage non trouvé.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->forbiddenResponse('Vous n\'êtes pas autorisé à modifier cet élevage.');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la mise à jour de l\'élevage: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la mise à jour de l\'élevage.', 500);
        }
    }

    /**
     * Suppression d'un élevage
     * DELETE /api/elevages/{id}
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            // Recherche de l'élevage
            $elevage = Elevage::findOrFail($id);
            
            // Vérification du propriétaire (conforme ELEV-03)
            $this->authorize('delete', $elevage);
            
            // Suppression
            $this->elevageService->deleteElevage($elevage);
            
            return $this->successResponse(null, 'Élevage supprimé avec succès.');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Élevage non trouvé.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->forbiddenResponse('Vous n\'êtes pas autorisé à supprimer cet élevage.');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la suppression de l\'élevage: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la suppression de l\'élevage.', 500);
        }
    }

    /**
     * Récupération des élevages de l'utilisateur connecté
     * GET /api/user/elevages
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function userElevages(Request $request): JsonResponse
    {
        try {
            $userId = auth()->id();
            
            // Récupération des élevages de l'utilisateur
            $elevages = $this->elevageService->getUserElevages($userId);
            
            // Calcul du nombre total d'élevages (conforme au cahier des charges)
            $totalElevages = $this->elevageService->getTotalElevagesCount($userId);
            
            return $this->successResponse([
                'data' => ElevageResource::collection($elevages),
                'total' => $totalElevages,
                'stats' => [
                    'by_type' => $this->getElevagesCountByType($elevages),
                ],
            ], 'Vos élevages récupérés avec succès.');
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération des élevages utilisateur: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la récupération de vos élevages.', 500);
        }
    }

    /**
     * Statistiques des élevages par type pour l'utilisateur
     * GET /api/user/elevages/stats
     * 
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        try {
            $userId = auth()->id();
            
            $stats = [
                'total' => Elevage::where('user_id', $userId)->count(),
                'by_type' => Elevage::where('user_id', $userId)
                    ->selectRaw('type_elevage, count(*) as count')
                    ->groupBy('type_elevage')
                    ->get(),
                'total_superficie' => Elevage::where('user_id', $userId)->sum('superficie'),
                'total_animaux' => \App\Models\Animal::whereHas('elevage', function($q) use ($userId) {
                    $q->where('user_id', $userId);
                })->count(),
            ];
            
            return $this->successResponse($stats, 'Statistiques récupérées avec succès.');
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération des statistiques: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la récupération des statistiques.', 500);
        }
    }

    /**
     * Calcule le nombre d'élevages par type
     * 
     * @param \Illuminate\Database\Eloquent\Collection $elevages
     * @return array
     */
    private function getElevagesCountByType($elevages): array
    {
        $counts = [];
        foreach (Elevage::TYPES_ELEVAGE as $type) {
            $counts[$type] = $elevages->where('type_elevage', $type)->count();
        }
        return $counts;
    }
}
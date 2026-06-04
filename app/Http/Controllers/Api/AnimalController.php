<?php
// app/Http/Controllers/Api/AnimalController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AnimalRequest;
use App\Http\Requests\Api\UpdateAnimalRequest;
use App\Http\Resources\AnimalResource;
use App\Models\Animal;
use App\Models\Elevage;
use App\Services\AnimalService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Contrôleur AnimalController
 * 
 * Gère toutes les opérations CRUD pour les animaux
 * Conforme au cahier des charges:
 * - CRUD complet
 * - Calcul automatique de l'âge
 * - Filtres par espèce, âge et santé
 * - Pagination
 * - Historique des modifications
 * - Sécurisation des accès
 * 
 * @package App\Http\Controllers\Api
 */
class AnimalController extends Controller
{
    use ApiResponseTrait;

    /**
     * Service de gestion des animaux
     */
    protected AnimalService $animalService;

    /**
     * Constructeur
     */
    public function __construct(AnimalService $animalService)
    {
        $this->animalService = $animalService;
        
        // Middlewares de sécurité
        $this->middleware('auth:api')->except(['index', 'show']);
    }

    /**
     * Liste des animaux avec pagination et filtres
     * GET /api/animaux
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Pagination (10 par page par défaut)
            $perPage = min($request->input('per_page', 10), 50);
            
            // Filtres (conforme cahier des charges: espèce, âge, santé)
            $filters = [
                'espece' => $request->input('espece'),
                'statut_sanitaire' => $request->input('statut_sanitaire'),
                'age_min' => $request->input('age_min'),
                'age_max' => $request->input('age_max'),
                'elevage_id' => $request->input('elevage_id'),
                'search' => $request->input('search'),
            ];
            
            // Récupération des animaux
            $animaux = $this->animalService->getAllPaginated($perPage, array_filter($filters));
            
            // Transformation des données
            $data = [
                'data' => AnimalResource::collection($animaux),
                'meta' => [
                    'current_page' => $animaux->currentPage(),
                    'last_page' => $animaux->lastPage(),
                    'per_page' => $animaux->perPage(),
                    'total' => $animaux->total(),
                    'from' => $animaux->firstItem(),
                    'to' => $animaux->lastItem(),
                ],
            ];
            
            // Ajout des filtres actifs
            if (!empty(array_filter($filters))) {
                $data['filters'] = array_filter($filters);
            }
            
            // Liste des valeurs possibles pour les filtres
            $data['available_filters'] = [
                'especes' => Animal::ESPECES,
                'statuts_sanitaires' => Animal::STATUTS_SANITAIRES,
            ];
            
            return $this->successResponse($data, 'Liste des animaux récupérée avec succès.');
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération des animaux: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la récupération des animaux.', 500);
        }
    }

    /**
     * Création d'un nouvel animal
     * POST /api/animaux
     * 
     * @param AnimalRequest $request
     * @return JsonResponse
     */
    public function store(AnimalRequest $request): JsonResponse
    {
        try {
            // Autorisation
            $elevage = Elevage::find($request->elevage_id);
            $this->authorize('create', [Animal::class, $elevage]);
            
            // Préparation des données
            $data = $request->validated();
            
            // Création de l'animal
            $animal = $this->animalService->createAnimal(
                $data,
                $request->file('photo')
            );
            
            return $this->successResponse(
                new AnimalResource($animal),
                'Animal créé avec succès.',
                201
            );
            
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->forbiddenResponse('Vous n\'êtes pas autorisé à ajouter un animal dans cet élevage.');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création de l\'animal: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la création de l\'animal.', 500);
        }
    }

    /**
     * Détails d'un animal spécifique
     * GET /api/animaux/{id}
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            // Recherche de l'animal
            $animal = Animal::with(['elevage.proprietaire'])->findOrFail($id);
            
            // Vérification des droits d'accès
            if (auth()->check()) {
                $this->authorize('view', $animal);
            } else {
                // Non authentifié: seulement si profil public
                $proprietaire = $animal->elevage?->proprietaire;
                if (!$proprietaire || $proprietaire->profile_visibility !== 'public') {
                    return $this->forbiddenResponse('Ce profil est privé.');
                }
            }
            
            // Récupération des détails complets
            $details = $this->animalService->getAnimalDetails($animal);
            
            return $this->successResponse(
                new AnimalResource($details),
                'Détails de l\'animal récupérés avec succès.'
            );
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Animal non trouvé.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->forbiddenResponse('Vous n\'avez pas accès à cet animal.');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération de l\'animal: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la récupération des détails.', 500);
        }
    }

    /**
     * Mise à jour d'un animal
     * PUT/PATCH /api/animaux/{id}
     * 
     * @param UpdateAnimalRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateAnimalRequest $request, int $id): JsonResponse
    {
        try {
            // Recherche de l'animal
            $animal = Animal::findOrFail($id);
            
            // Vérification du propriétaire
            $this->authorize('update', $animal);
            
            // Préparation des données (ne garder que les champs modifiés)
            $data = array_filter($request->validated(), function($value) {
                return !is_null($value);
            });
            
            // Mise à jour
            $updatedAnimal = $this->animalService->updateAnimal(
                $animal,
                $data,
                $request->file('photo')
            );
            
            return $this->successResponse(
                new AnimalResource($updatedAnimal),
                'Animal mis à jour avec succès.'
            );
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Animal non trouvé.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->forbiddenResponse('Vous n\'êtes pas autorisé à modifier cet animal.');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la mise à jour de l\'animal: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la mise à jour de l\'animal.', 500);
        }
    }

    /**
     * Suppression d'un animal
     * DELETE /api/animaux/{id}
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            // Recherche de l'animal
            $animal = Animal::findOrFail($id);
            
            // Vérification du propriétaire
            $this->authorize('delete', $animal);
            
            // Suppression
            $this->animalService->deleteAnimal($animal);
            
            return $this->successResponse(null, 'Animal supprimé avec succès.');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Animal non trouvé.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->forbiddenResponse('Vous n\'êtes pas autorisé à supprimer cet animal.');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la suppression de l\'animal: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la suppression de l\'animal.', 500);
        }
    }

    /**
     * Récupération des animaux d'un élevage spécifique
     * GET /api/elevages/{elevageId}/animaux
     * 
     * @param int $elevageId
     * @param Request $request
     * @return JsonResponse
     */
    public function getByElevage(int $elevageId, Request $request): JsonResponse
    {
        try {
            // Vérification de l'existence de l'élevage
            $elevage = Elevage::findOrFail($elevageId);
            
            // Vérification des droits d'accès
            if (!auth()->check() && $elevage->proprietaire->profile_visibility !== 'public') {
                return $this->forbiddenResponse('Cet élevage est privé.');
            }
            
            // Filtres
            $filters = [
                'espece' => $request->input('espece'),
                'statut_sanitaire' => $request->input('statut_sanitaire'),
            ];
            
            // Récupération des animaux
            $animaux = $this->animalService->getElevageAnimals($elevageId, array_filter($filters));
            
            // Statistiques
            $stats = $this->animalService->getElevageStats($elevageId);
            
            return $this->successResponse([
                'data' => AnimalResource::collection($animaux),
                'stats' => $stats,
                'elevage' => [
                    'id' => $elevage->id,
                    'nom' => $elevage->nom,
                ],
            ], 'Animaux de l\'élevage récupérés avec succès.');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Élevage non trouvé.');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération des animaux par élevage: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la récupération des animaux.', 500);
        }
    }

    /**
     * Statistiques des animaux pour l'utilisateur connecté
     * GET /api/user/animaux/stats
     * 
     * @return JsonResponse
     */
    public function userStats(): JsonResponse
    {
        try {
            $userId = auth()->id();
            
            $stats = [
                'total' => $this->animalService->getTotalAnimalsCount('user', $userId),
                'by_espece' => Animal::whereHas('elevage', function($q) use ($userId) {
                    $q->where('user_id', $userId);
                })->selectRaw('espece, count(*) as count')
                  ->groupBy('espece')
                  ->get(),
                'by_statut' => Animal::whereHas('elevage', function($q) use ($userId) {
                    $q->where('user_id', $userId);
                })->selectRaw('statut_sanitaire, count(*) as count')
                  ->groupBy('statut_sanitaire')
                  ->get(),
                'poids_moyen' => Animal::whereHas('elevage', function($q) use ($userId) {
                    $q->where('user_id', $userId);
                })->avg('poids'),
            ];
            
            return $this->successResponse($stats, 'Statistiques récupérées avec succès.');
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération des statistiques: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la récupération des statistiques.', 500);
        }
    }

    /**
     * Historique des modifications d'un animal
     * GET /api/animaux/{id}/historique
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function historique(int $id): JsonResponse
    {
        try {
            $animal = Animal::findOrFail($id);
            
            // Vérification du propriétaire
            $this->authorize('view', $animal);
            
            $historique = $animal->historiques()
                ->with('user')
                ->latest()
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'action' => $item->action,
                        'date' => $item->created_at->format('d/m/Y H:i:s'),
                        'user' => $item->user?->name ?? 'Système',
                        'changed_fields' => $item->formatted_changes,
                        'ip_address' => $item->ip_address,
                    ];
                });
            
            return $this->successResponse([
                'animal' => [
                    'id' => $animal->id,
                    'nom' => $animal->nom,
                ],
                'historique' => $historique,
            ], 'Historique récupéré avec succès.');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Animal non trouvé.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->forbiddenResponse('Vous n\'avez pas accès à l\'historique de cet animal.');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération de l\'historique: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la récupération de l\'historique.', 500);
        }
    }
}
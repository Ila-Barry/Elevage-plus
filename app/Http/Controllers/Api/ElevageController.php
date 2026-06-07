<?php
// app/Http/Controllers/Api/ElevageController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Elevage\CreateElevageRequest;
use App\Http\Requests\Api\Elevage\UpdateElevageRequest;
use App\Http\Requests\Api\Elevage\ElevageFilterRequest;
use App\Http\Resources\ElevageResource;
use App\Models\Elevage;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Contrôleur ElevageController
 * 
 * Gère toutes les opérations liées aux élevages :
 * - CRUD des élevages
 * - Pagination et filtres
 * - Vérification du propriétaire
 * - Upload de photos
 * - Statistiques
 */
class ElevageController extends Controller
{
    use ApiResponseTrait;

    /**
     * Constructeur avec middleware
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Liste des élevages de l'utilisateur connecté
     *
     * @param ElevageFilterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ElevageFilterRequest $request)
    {
        $user = $request->user();
        
        $query = Elevage::where('user_id', $user->id)
            ->with(['user'])
            ->withStats();
        
        // Filtre par type d'élevage
        if ($request->filled('type_elevage')) {
            $query->byType($request->type_elevage);
        }
        
        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        
        // Recherche textuelle
        if ($request->filled('search')) {
            $query->search($request->search);
        }
        
        // Filtre par localisation
        if ($request->filled('localisation')) {
            $query->byLocalisation($request->localisation);
        }
        
        // Tri
        $sort = $request->get('sort', 'date_creation');
        $direction = $request->get('direction', 'desc');
        
        switch ($sort) {
            case 'total_animaux':
                $query->withCount('animaux')->orderBy('animaux_count', $direction);
                break;
            case 'superficie':
                $query->orderBy('superficie', $direction);
                break;
            default:
                $query->orderBy($sort, $direction);
        }
        
        $perPage = $request->get('per_page', 12);
        $elevages = $query->paginate($perPage);
        
        // Calcul du nombre total d'élevages
        $totalElevages = Elevage::where('user_id', $user->id)->count();
        
        return $this->successResponse([
            'data' => ElevageResource::collection($elevages),
            'meta' => [
                'current_page' => $elevages->currentPage(),
                'last_page' => $elevages->lastPage(),
                'per_page' => $elevages->perPage(),
                'total' => $elevages->total(),
                'total_elevages' => $totalElevages,
            ],
        ]);
    }

    /**
     * Créer un nouvel élevage
     *
     * @param CreateElevageRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateElevageRequest $request)
    {
        DB::beginTransaction();
        
        try {
            $user = $request->user();
            $data = $request->validated();
            $data['user_id'] = $user->id;
            $data['date_creation'] = now();
            
            // Upload de l'image
            if ($request->hasFile('image')) {
                $data['img_url'] = $this->uploadImage($request->file('image'));
            }
            
            $elevage = Elevage::create($data);
            
            DB::commit();
            
            return $this->successResponse(
                new ElevageResource($elevage),
                'Élevage créé avec succès.',
                201
            );
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur création élevage: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la création de l\'élevage.', 500);
        }
    }

    /**
     * Afficher un élevage spécifique
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        
        $elevage = Elevage::with(['user', 'animaux' => function ($query) {
                $query->latest()->limit(10);
            }])
            ->withStats()
            ->findOrFail($id);
        
        // Vérifier le propriétaire via Policy
        $this->authorize('view', $elevage);
        
        return $this->successResponse(
            ElevageResource::make($elevage, true)
        );
    }

    /**
     * Mettre à jour un élevage
     *
     * @param UpdateElevageRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateElevageRequest $request, $id)
    {
        $user = $request->user();
        
        $elevage = Elevage::findOrFail($id);
        
        // Vérifier le propriétaire via Policy
        $this->authorize('update', $elevage);
        
        DB::beginTransaction();
        
        try {
            $data = $request->validated();
            
            // Gestion de l'image
            if ($request->hasFile('image')) {
                // Supprimer l'ancienne image
                if ($elevage->img_url && !str_contains($elevage->img_url, 'default-farm')) {
                    $this->deleteImage($elevage->img_url);
                }
                $data['img_url'] = $this->uploadImage($request->file('image'));
            } elseif ($request->input('delete_image')) {
                if ($elevage->img_url && !str_contains($elevage->img_url, 'default-farm')) {
                    $this->deleteImage($elevage->img_url);
                }
                $data['img_url'] = null;
            }
            
            $elevage->update($data);
            
            DB::commit();
            
            return $this->successResponse(
                new ElevageResource($elevage->load('user')),
                'Élevage mis à jour avec succès.'
            );
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur mise à jour élevage: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la mise à jour de l\'élevage.', 500);
        }
    }

    /**
     * Supprimer un élevage
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        
        $elevage = Elevage::findOrFail($id);
        
        // Vérifier le propriétaire via Policy
        $this->authorize('delete', $elevage);
        
        DB::beginTransaction();
        
        try {
            // Supprimer l'image
            if ($elevage->img_url && !str_contains($elevage->img_url, 'default-farm')) {
                $this->deleteImage($elevage->img_url);
            }
            
            // Les animaux et produits seront supprimés automatiquement (cascade)
            $elevage->delete();
            
            DB::commit();
            
            return $this->successResponse(null, 'Élevage supprimé avec succès.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur suppression élevage: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la suppression de l\'élevage.', 500);
        }
    }

    /**
     * Statistiques de tous les élevages de l'utilisateur
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistiques(Request $request)
    {
        $user = $request->user();
        
        $elevages = Elevage::where('user_id', $user->id)->get();
        
        $stats = [
            'total_elevages' => $elevages->count(),
            'total_animaux' => $elevages->sum(fn($e) => $e->animaux()->count()),
            'total_produits' => $elevages->sum(fn($e) => $e->produits()->count()),
            'valeur_stock_totale' => $elevages->sum(fn($e) => $e->valeur_stock_totale),
            'superficie_totale' => (float) $elevages->sum('superficie'),
            'repartition_par_type' => [],
            'repartition_par_statut' => [],
        ];
        
        // Répartition par type
        foreach (array_keys(Elevage::TYPES_ELEVAGE) as $type) {
            $count = $elevages->where('type_elevage', $type)->count();
            if ($count > 0) {
                $stats['repartition_par_type'][$type] = [
                    'label' => Elevage::TYPES_ELEVAGE[$type],
                    'count' => $count,
                ];
            }
        }
        
        // Répartition par statut
        foreach (array_keys(Elevage::STATUTS) as $statut) {
            $count = $elevages->where('statut', $statut)->count();
            $stats['repartition_par_statut'][$statut] = [
                'label' => Elevage::STATUTS[$statut],
                'count' => $count,
            ];
        }
        
        return $this->successResponse($stats);
    }

    /**
     * Changer le statut d'un élevage
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeStatut(Request $request, $id)
    {
        $request->validate([
            'statut' => 'required|string|in:' . implode(',', array_keys(Elevage::STATUTS)),
        ]);
        
        $user = $request->user();
        
        $elevage = Elevage::findOrFail($id);
        
        // Vérifier le propriétaire
        $this->authorize('update', $elevage);
        
        $elevage->update(['statut' => $request->statut]);
        
        return $this->successResponse(
            ['statut' => $elevage->statut, 'statut_label' => $elevage->statut_label],
            'Statut de l\'élevage mis à jour.'
        );
    }

    /**
     * Upload de l'image
     *
     * @param \Illuminate\Http\UploadedFile $image
     * @return string
     */
    private function uploadImage($image): string
    {
        $filename = 'elevage_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('elevages', $filename, 'public');
        return $path;
    }

    /**
     * Suppression de l'image
     *
     * @param string $path
     * @return void
     */
    private function deleteImage(string $path): void
    {
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
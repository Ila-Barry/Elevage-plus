<?php
// app/Http/Controllers/Api/AnimalController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Animal\CreateAnimalRequest;
use App\Http\Requests\Api\Animal\UpdateAnimalRequest;
use App\Http\Requests\Api\Animal\AnimalFilterRequest;
use App\Http\Resources\AnimalResource;
use App\Models\Animal;
use App\Models\Elevage;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * Contrôleur AnimalController
 * 
 * Gère toutes les opérations liées aux animaux :
 * - CRUD des animaux
 * - Calcul automatique de l'âge
 * - Filtres par espèce, âge, santé
 * - Pagination
 * - Historique des modifications
 * - Upload de photos
 */
class AnimalController extends Controller
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
     * Liste des animaux
     *
     * @param AnimalFilterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(AnimalFilterRequest $request)
    {
        $user = $request->user();
        
        // Récupérer tous les élevages de l'utilisateur
        $elevageIds = $user->elevages()->pluck('id');
        
        if ($elevageIds->isEmpty()) {
            return $this->successResponse([
                'data' => [],
                'meta' => $this->getEmptyMeta(),
            ]);
        }
        
        $query = Animal::whereIn('elevage_id', $elevageIds)
            ->with(['elevage', 'pere', 'mere']);
        
        // Filtre par élevage
        if ($request->filled('elevage_id')) {
            $elevage = Elevage::where('id', $request->elevage_id)
                ->where('user_id', $user->id)
                ->firstOrFail();
            $query->where('elevage_id', $elevage->id);
        }
        
        // Filtre par espèce
        if ($request->filled('espece')) {
            $query->byEspece($request->espece);
        }
        
        // Filtre par statut sanitaire
        if ($request->filled('statut_sanitaire')) {
            $query->byStatutSanitaire($request->statut_sanitaire);
        }
        
        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        
        // Filtre par sexe
        if ($request->filled('sexe')) {
            $query->where('sexe', $request->sexe);
        }
        
        // Filtre par âge (en mois)
        if ($request->filled('age_min') || $request->filled('age_max')) {
            $ageMin = $request->input('age_min', 0);
            $ageMax = $request->input('age_max');
            $query->byAge($ageMin, $ageMax);
        }
        
        // Filtre par poids
        if ($request->filled('poids_min')) {
            $query->where('poids', '>=', $request->poids_min);
        }
        if ($request->filled('poids_max')) {
            $query->where('poids', '<=', $request->poids_max);
        }
        
        // Recherche textuelle
        if ($request->filled('search')) {
            $query->search($request->search);
        }
        
        // Tri
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        
        if ($sort === 'date_naissance') {
            $query->orderBy('date_naissance', $direction);
        } else {
            $query->orderBy($sort, $direction);
        }
        
        $perPage = $request->get('per_page', 15);
        $animaux = $query->paginate($perPage);
        
        // Calcul du nombre total d'animaux
        $totalAnimaux = Animal::whereIn('elevage_id', $elevageIds)->count();
        
        return $this->successResponse([
            'data' => AnimalResource::collection($animaux),
            'meta' => [
                'current_page' => $animaux->currentPage(),
                'last_page' => $animaux->lastPage(),
                'per_page' => $animaux->perPage(),
                'total' => $animaux->total(),
                'total_animaux' => $totalAnimaux,
            ],
        ]);
    }

    /**
     * Créer un nouvel animal
     *
     * @param CreateAnimalRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateAnimalRequest $request)
    {
        DB::beginTransaction();
        
        try {
            $data = $request->validated();
            
            // Vérifier que l'élevage appartient à l'utilisateur
            $elevage = Elevage::where('id', $data['elevage_id'])
                ->where('user_id', $request->user()->id)
                ->firstOrFail();
            
            // Upload de l'image
            if ($request->hasFile('image')) {
                $data['img_url'] = $this->uploadImage($request->file('image'));
            }
            
            $animal = Animal::create($data);
            
            DB::commit();
            
            return $this->successResponse(
                new AnimalResource($animal->load(['elevage', 'pere', 'mere'])),
                'Animal créé avec succès.',
                201
            );
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur création animal: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la création de l\'animal.', 500);
        }
    }

    /**
     * Afficher un animal spécifique
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        
        $animal = Animal::whereHas('elevage', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['elevage', 'pere', 'mere', 'taches'])
            ->findOrFail($id);
        
        return $this->successResponse(
            AnimalResource::make($animal, true)
        );
    }

    /**
     * Mettre à jour un animal
     *
     * @param UpdateAnimalRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateAnimalRequest $request, $id)
    {
        $user = $request->user();
        
        $animal = Animal::whereHas('elevage', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            $data = $request->validated();
            
            // Gestion de l'image
            if ($request->hasFile('image')) {
                // Supprimer l'ancienne image
                if ($animal->img_url && !str_contains($animal->img_url, 'default-')) {
                    $this->deleteImage($animal->img_url);
                }
                $data['img_url'] = $this->uploadImage($request->file('image'));
            } elseif ($request->input('delete_image')) {
                if ($animal->img_url && !str_contains($animal->img_url, 'default-')) {
                    $this->deleteImage($animal->img_url);
                }
                $data['img_url'] = null;
            }
            
            // Si l'animal est marqué comme décédé, s'assurer que les dates sont définies
            if (isset($data['statut']) && $data['statut'] === 'decede') {
                if (empty($data['date_deces'])) {
                    $data['date_deces'] = now();
                }
                if (empty($data['motif_deces'])) {
                    $data['motif_deces'] = 'Non spécifié';
                }
            }
            
            $animal->update($data);
            
            DB::commit();
            
            return $this->successResponse(
                new AnimalResource($animal->load(['elevage', 'pere', 'mere'])),
                'Animal mis à jour avec succès.'
            );
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur mise à jour animal: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la mise à jour de l\'animal.', 500);
        }
    }

    /**
     * Supprimer un animal
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        
        $animal = Animal::whereHas('elevage', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            // Supprimer l'image
            if ($animal->img_url && !str_contains($animal->img_url, 'default-')) {
                $this->deleteImage($animal->img_url);
            }
            
            // Les tâches seront supprimées automatiquement (cascade)
            $animal->delete();
            
            DB::commit();
            
            return $this->successResponse(null, 'Animal supprimé avec succès.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur suppression animal: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la suppression de l\'animal.', 500);
        }
    }

    /**
     * Statistiques des animaux
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistiques(Request $request)
    {
        $user = $request->user();
        
        $elevageIds = $user->elevages()->pluck('id');
        
        if ($elevageIds->isEmpty()) {
            return $this->successResponse($this->getEmptyStats());
        }
        
        $query = Animal::whereIn('elevage_id', $elevageIds);
        
        $stats = [
            'total_animaux' => $query->count(),
            'total_actifs' => (clone $query)->where('statut', 'actif')->count(),
            'total_males' => (clone $query)->where('sexe', 'male')->count(),
            'total_femelles' => (clone $query)->where('sexe', 'femelle')->count(),
            'poids_moyen' => (float) (clone $query)->avg('poids'),
            'repartition_par_espece' => [],
            'repartition_par_statut_sanitaire' => [],
            'repartition_par_age' => [
                'moins_1_an' => (clone $query)->byAge(0, 12)->count(),
                '1_a_3_ans' => (clone $query)->byAge(12, 36)->count(),
                '3_a_5_ans' => (clone $query)->byAge(36, 60)->count(),
                'plus_5_ans' => (clone $query)->byAge(60)->count(),
            ],
        ];
        
        // Répartition par espèce
        foreach (array_keys(Animal::ESPECES) as $espece) {
            $count = (clone $query)->byEspece($espece)->count();
            if ($count > 0) {
                $stats['repartition_par_espece'][$espece] = [
                    'label' => Animal::ESPECES[$espece],
                    'count' => $count,
                ];
            }
        }
        
        // Répartition par statut sanitaire
        foreach (array_keys(Animal::STATUTS_SANITAIRES) as $statut) {
            $count = (clone $query)->byStatutSanitaire($statut)->count();
            $stats['repartition_par_statut_sanitaire'][$statut] = [
                'label' => Animal::STATUTS_SANITAIRES[$statut],
                'count' => $count,
            ];
        }
        
        // Poids moyen par espèce
        $stats['poids_moyen_par_espece'] = Animal::poidsMoyenParEspeceMultiple($elevageIds);
        
        return $this->successResponse($stats);
    }

    /**
     * Upload de l'image
     */
    private function uploadImage($image): string
    {
        $filename = 'animal_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('animaux', $filename, 'public');
        return $path;
    }

    /**
     * Suppression de l'image
     */
    private function deleteImage(string $path): void
    {
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Métadonnées vides pour pagination
     */
    private function getEmptyMeta(): array
    {
        return [
            'current_page' => 1,
            'last_page' => 1,
            'per_page' => 15,
            'total' => 0,
            'total_animaux' => 0,
        ];
    }

    /**
     * Statistiques vides
     */
    private function getEmptyStats(): array
    {
        return [
            'total_animaux' => 0,
            'total_actifs' => 0,
            'total_males' => 0,
            'total_femelles' => 0,
            'poids_moyen' => 0,
            'repartition_par_espece' => [],
            'repartition_par_statut_sanitaire' => [],
            'repartition_par_age' => [
                'moins_1_an' => 0,
                '1_a_3_ans' => 0,
                '3_a_5_ans' => 0,
                'plus_5_ans' => 0,
            ],
            'poids_moyen_par_espece' => [],
        ];
    }
}
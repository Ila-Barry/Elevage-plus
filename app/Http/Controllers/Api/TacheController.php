<?php
// app/Http/Controllers/Api/TacheController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Tache\CreateTacheRequest;
use App\Http\Requests\Api\Tache\UpdateTacheRequest;
use App\Http\Requests\Api\Tache\TacheFilterRequest;
use App\Http\Resources\TacheResource;
use App\Http\Resources\CalendarEventResource;
use App\Models\Tache;
use App\Models\Elevage;
use App\Models\Animal;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Contrôleur TacheController
 * 
 * Gère toutes les opérations liées aux tâches :
 * - CRUD des tâches
 * - Filtrage par date
 * - Marquage comme terminée
 * - Gestion des rappels automatiques
 * - Vue calendrier FullCalendar
 */
class TacheController extends Controller
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
     * Liste des tâches
     *
     * @param TacheFilterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(TacheFilterRequest $request)
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
        
        $query = Tache::whereIn('elevage_id', $elevageIds)
            ->with(['animal', 'elevage', 'user']);
        
        // Filtre par élevage
        if ($request->filled('elevage_id')) {
            $elevage = Elevage::where('id', $request->elevage_id)
                ->where('user_id', $user->id)
                ->firstOrFail();
            $query->where('elevage_id', $elevage->id);
        }
        
        // Filtre par animal
        if ($request->filled('animal_id')) {
            $animal = Animal::where('id', $request->animal_id)
                ->whereHas('elevage', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->firstOrFail();
            $query->where('animal_id', $animal->id);
        }
        
        // Filtre par type
        if ($request->filled('type')) {
            $query->byType($request->type);
        }
        
        // Filtre par priorité
        if ($request->filled('priorite')) {
            $query->byPriorite($request->priorite);
        }
        
        // Filtre par statut
        if ($request->filled('statut')) {
            switch ($request->statut) {
                case 'terminees':
                    $query->terminees();
                    break;
                case 'a_venir':
                    $query->avenir();
                    break;
                case 'retard':
                    $query->retard();
                    break;
            }
        }
        
        // Filtre par période
        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->entreDates($request->date_debut, $request->date_fin);
        } elseif ($request->filled('date_debut')) {
            $query->whereDate('date_planifiee', '>=', $request->date_debut);
        } elseif ($request->filled('date_fin')) {
            $query->whereDate('date_planifiee', '<=', $request->date_fin);
        }
        
        // Recherche textuelle
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('titre', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }
        
        // Tri
        $sort = $request->get('sort', 'date_planifiee');
        $direction = $request->get('direction', 'asc');
        $query->orderBy($sort, $direction);
        
        $perPage = $request->get('per_page', 15);
        $taches = $query->paginate($perPage);
        
        // Calcul du nombre total de tâches
        $totalTaches = Tache::whereIn('elevage_id', $elevageIds)->count();
        $totalAvenir = Tache::whereIn('elevage_id', $elevageIds)->avenir()->count();
        $totalRetard = Tache::whereIn('elevage_id', $elevageIds)->retard()->count();
        
        return $this->successResponse([
            'data' => TacheResource::collection($taches),
            'meta' => [
                'current_page' => $taches->currentPage(),
                'last_page' => $taches->lastPage(),
                'per_page' => $taches->perPage(),
                'total' => $taches->total(),
                'total_taches' => $totalTaches,
                'total_a_venir' => $totalAvenir,
                'total_retard' => $totalRetard,
            ],
        ]);
    }

    /**
     * Vue calendrier pour FullCalendar
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calendar(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date|after:start',
        ]);
        
        $elevageIds = $user->elevages()->pluck('id');
        
        if ($elevageIds->isEmpty()) {
            return $this->successResponse([]);
        }
        
        $taches = Tache::whereIn('elevage_id', $elevageIds)
            ->with(['animal', 'elevage'])
            ->entreDates($request->start, $request->end)
            ->orderBy('date_planifiee')
            ->get();
        
        return $this->successResponse(CalendarEventResource::collection($taches));
    }

    /**
     * Créer une nouvelle tâche
     *
     * @param CreateTacheRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateTacheRequest $request)
    {
        DB::beginTransaction();
        
        try {
            $user = $request->user();
            $data = $request->validated();
            
            // Vérifier que l'élevage appartient à l'utilisateur
            $elevage = Elevage::where('id', $data['elevage_id'])
                ->where('user_id', $user->id)
                ->firstOrFail();
            
            $data['user_id'] = $user->id;
            $data['elevage_id'] = $elevage->id;
            
            $tache = Tache::create($data);
            
            DB::commit();
            
            return $this->successResponse(
                new TacheResource($tache->load(['animal', 'elevage', 'user'])),
                'Tâche créée avec succès.',
                201
            );
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur création tâche: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la création de la tâche.', 500);
        }
    }

    /**
     * Afficher une tâche spécifique
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        
        $tache = Tache::whereHas('elevage', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['animal', 'elevage', 'user'])
            ->findOrFail($id);
        
        return $this->successResponse(new TacheResource($tache));
    }

    /**
     * Mettre à jour une tâche
     *
     * @param UpdateTacheRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateTacheRequest $request, $id)
    {
        $user = $request->user();
        
        $tache = Tache::whereHas('elevage', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            $data = $request->validated();
            
            // Si la tâche est marquée comme terminée, enregistrer la date
            if (isset($data['terminee']) && $data['terminee'] && !$tache->terminee) {
                $data['date_realisee'] = now();
            }
            
            $tache->update($data);
            
            DB::commit();
            
            return $this->successResponse(
                new TacheResource($tache->load(['animal', 'elevage', 'user'])),
                'Tâche mise à jour avec succès.'
            );
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur mise à jour tâche: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la mise à jour de la tâche.', 500);
        }
    }

    /**
     * Marquer une tâche comme terminée
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function complete(Request $request, $id)
    {
        $user = $request->user();
        
        $tache = Tache::whereHas('elevage', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->findOrFail($id);
        
        if ($tache->terminee) {
            return $this->errorResponse('Cette tâche est déjà terminée.', 422);
        }
        
        $tache->markAsCompleted();
        
        return $this->successResponse(
            new TacheResource($tache->load(['animal', 'elevage', 'user'])),
            'Tâche marquée comme terminée.'
        );
    }

    /**
     * Supprimer une tâche
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        
        $tache = Tache::whereHas('elevage', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->findOrFail($id);
        
        $tache->delete();
        
        return $this->successResponse(null, 'Tâche supprimée avec succès.');
    }

    /**
     * Statistiques des tâches
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
        
        $query = Tache::whereIn('elevage_id', $elevageIds);
        
        $stats = [
            'total_taches' => $query->count(),
            'taches_terminees' => (clone $query)->terminees()->count(),
            'taches_a_venir' => (clone $query)->avenir()->count(),
            'taches_en_retard' => (clone $query)->retard()->count(),
            'taches_aujourdhui' => (clone $query)->aujourdhui()->count(),
            'taches_cette_semaine' => (clone $query)->cetteSemaine()->count(),
            'taches_ce_mois' => (clone $query)->ceMois()->count(),
            'repartition_par_type' => [],
            'repartition_par_priorite' => [],
        ];
        
        // Répartition par type
        foreach (array_keys(Tache::TYPES) as $type) {
            $count = (clone $query)->byType($type)->count();
            if ($count > 0) {
                $stats['repartition_par_type'][$type] = [
                    'label' => Tache::TYPES[$type],
                    'icone' => Tache::ICONES[$type],
                    'count' => $count,
                ];
            }
        }
        
        // Répartition par priorité
        foreach (array_keys(Tache::PRIORITES) as $priorite) {
            $stats['repartition_par_priorite'][$priorite] = [
                'label' => Tache::PRIORITES[$priorite],
                'couleur' => Tache::COULEURS_PRIORITE[$priorite],
                'count' => (clone $query)->byPriorite($priorite)->count(),
            ];
        }
        
        return $this->successResponse($stats);
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
            'total_taches' => 0,
            'total_a_venir' => 0,
            'total_retard' => 0,
        ];
    }

    /**
     * Statistiques vides
     */
    private function getEmptyStats(): array
    {
        return [
            'total_taches' => 0,
            'taches_terminees' => 0,
            'taches_a_venir' => 0,
            'taches_en_retard' => 0,
            'taches_aujourdhui' => 0,
            'taches_cette_semaine' => 0,
            'taches_ce_mois' => 0,
            'repartition_par_type' => [],
            'repartition_par_priorite' => [],
        ];
    }
}
<?php
// app/Http/Controllers/Api/AnimalController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Notifications\AnimalNotification;
use Illuminate\Support\Facades\Log;
use App\Models\AnimalHistorique; 
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
            $user = $request->user();
            $data = $request->validated();
            
            $elevage = Elevage::where('id', $data['elevage_id'])
                ->where('user_id', $user->id)
                ->firstOrFail();
            
            if ($request->hasFile('image')) {
                $data['img_url'] = $this->uploadImage($request->file('image'));
            }
            
            $animal = Animal::create($data);
            
            // ✅ ENREGISTRER L'HISTORIQUE DE CRÉATION
            try {
                AnimalHistorique::logCreation(
                    $animal->id,
                    $user->id,
                    $data
                );
                
                Log::info('📝 Historique création animal enregistré', [
                    'animal_id' => $animal->id,
                    'user_id' => $user->id
                ]);
            } catch (\Exception $e) {
                Log::error('❌ Erreur historique création', [
                    'error' => $e->getMessage()
                ]);
            }
            
            // 🔔 ENVOYER LA NOTIFICATION
            try {
                $user->notify(new AnimalNotification($animal, 'created'));
                Log::info('✅ Notification création animal envoyée');
            } catch (\Exception $e) {
                Log::error('❌ Erreur notification création', [
                    'error' => $e->getMessage()
                ]);
            }
            
            DB::commit();
            
            return $this->successResponse(
                new AnimalResource($animal->load(['elevage', 'pere', 'mere'])),
                'Animal créé avec succès.',
                201
            );
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur création animal: ' . $e->getMessage());
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
        
        // ✅ CORRECTION : Utiliser withHistory() au lieu de make()
        return $this->successResponse(
            AnimalResource::withHistory($animal),
            'Détails de l\'animal récupérés avec succès.'
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
            // Récupérer les anciennes valeurs
            $oldValues = [
                'nom' => $animal->nom,
                'espece' => $animal->espece,
                'race' => $animal->race,
                'poids' => (float) $animal->poids,
                'statut_sanitaire' => $animal->statut_sanitaire,
                'sexe' => $animal->sexe,
                'statut' => $animal->statut,
            ];
            
            $data = $request->validated();
            
            // Gestion de l'image
            if ($request->hasFile('image')) {
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
            
            // Si l'animal est marqué comme décédé
            if (isset($data['statut']) && $data['statut'] === 'decede') {
                if (empty($data['date_deces'])) {
                    $data['date_deces'] = now();
                }
                if (empty($data['motif_deces'])) {
                    $data['motif_deces'] = 'Non spécifié';
                }
            }
            
            // Mettre à jour l'animal
            $animal->update($data);
            
            // 🔍 DÉTECTER LES CHANGEMENTS ET ENREGISTRER L'HISTORIQUE
            $changes = [];
            $fieldsToCheck = ['nom', 'espece', 'race', 'poids', 'statut_sanitaire', 'sexe', 'statut'];
            
            foreach ($fieldsToCheck as $field) {
                if (isset($data[$field]) && $oldValues[$field] != $data[$field]) {
                    $changes[$field] = [
                        'old' => $oldValues[$field],
                        'new' => $data[$field]
                    ];
                    
                    // Enregistrer dans l'historique
                    try {
                        AnimalHistorique::logChange(
                            $animal->id,
                            $user->id,
                            $field,
                            $oldValues[$field],
                            $data[$field],
                            'update'
                        );
                    } catch (\Exception $e) {
                        Log::error('❌ Erreur historique changement', [
                            'field' => $field,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
            
            // 🔔 ALERTE SANITAIRE
            if (isset($data['statut_sanitaire']) && in_array($data['statut_sanitaire'], ['malade', 'critique'])) {
                try {
                    Log::info('🚨 Alerte sanitaire animal', [
                        'animal_id' => $animal->id,
                        'statut' => $data['statut_sanitaire']
                    ]);
                    
                    $user->notify(new AnimalNotification($animal, 'health_alert', [
                        'status' => $data['statut_sanitaire']
                    ]));
                    
                    Log::info('✅ Alerte sanitaire envoyée');
                } catch (\Exception $e) {
                    Log::error('❌ Erreur envoi alerte sanitaire', [
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            // 🔔 ALERTE PERTE DE POIDS (CORRIGÉE)
            if (isset($data['poids']) && isset($oldValues['poids']) && $data['poids'] < $oldValues['poids']) {
                $oldWeight = (float) $oldValues['poids'];
                $newWeight = (float) $data['poids'];
                
                if ($oldWeight > 0) {
                    $lossPercent = (($oldWeight - $newWeight) / $oldWeight) * 100;
                    
                    // Vérifier l'historique des poids sur les 15 derniers jours
                    $historique = AnimalHistorique::where('animal_id', $animal->id)
                        ->where('champ_modifie', 'poids') // ✅ CORRECTION: champ_modifie
                        ->where('action', 'update')
                        ->where('created_at', '>=', now()->subDays(15))
                        ->orderBy('created_at', 'desc')
                        ->first();
                    
                    if ($historique) {
                        $oldHistoricalWeight = (float) ($historique->ancienne_valeur ?? $oldWeight);
                        $newWeight = (float) $data['poids'];
                        $historicalLoss = (($oldHistoricalWeight - $newWeight) / max($oldHistoricalWeight, 1)) * 100;
                        
                        if ($historicalLoss >= 10) {
                            try {
                                Log::info('⚠️ Alerte perte de poids animal', [
                                    'animal_id' => $animal->id,
                                    'loss_percent' => $historicalLoss
                                ]);
                                
                                $user->notify(new AnimalNotification($animal, 'weight_alert', [
                                    'old_weight' => $oldHistoricalWeight,
                                    'new_weight' => $newWeight
                                ]));
                                
                                Log::info('✅ Alerte perte de poids envoyée');
                            } catch (\Exception $e) {
                                Log::error('❌ Erreur envoi alerte perte de poids', [
                                    'error' => $e->getMessage()
                                ]);
                            }
                        }
                    } elseif ($lossPercent >= 10) {
                        // Pas d'historique, mais perte directe significative
                        try {
                            Log::info('⚠️ Alerte perte de poids immédiate', [
                                'animal_id' => $animal->id,
                                'loss_percent' => $lossPercent
                            ]);
                            
                            $user->notify(new AnimalNotification($animal, 'weight_alert', [
                                'old_weight' => $oldWeight,
                                'new_weight' => $newWeight
                            ]));
                            
                            Log::info('✅ Alerte perte de poids immédiate envoyée');
                        } catch (\Exception $e) {
                            Log::error('❌ Erreur envoi alerte perte de poids', [
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                }
            }
            
            // 🔔 NOTIFICATION DE DÉCÈS
            if (isset($data['statut']) && $data['statut'] === 'decede' && $oldValues['statut'] !== 'decede') {
                try {
                    Log::info('💔 Animal décédé', [
                        'animal_id' => $animal->id,
                        'motif' => $data['motif_deces'] ?? 'Non spécifié'
                    ]);
                    
                    $user->notify(new AnimalNotification($animal, 'death', [
                        'motif' => $data['motif_deces'] ?? 'Non spécifié'
                    ]));
                    
                    Log::info('✅ Notification de décès envoyée');
                } catch (\Exception $e) {
                    Log::error('❌ Erreur envoi notification décès', [
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            // 🔔 NOTIFICATION DE MODIFICATION
            if (!empty($changes)) {
                try {
                    Log::info('📤 Envoi notification modification animal', [
                        'user_id' => $user->id,
                        'animal_id' => $animal->id,
                        'changes' => $changes
                    ]);
                    
                    $user->notify(new AnimalNotification($animal, 'updated', [
                        'changes' => $changes
                    ]));
                    
                    Log::info('✅ Notification modification animal envoyée');
                } catch (\Exception $e) {
                    Log::error('❌ Erreur envoi notification modification', [
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            DB::commit();
            
            return $this->successResponse(
                new AnimalResource($animal->load(['elevage', 'pere', 'mere'])),
                'Animal mis à jour avec succès.'
            );
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur mise à jour animal: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse('Erreur lors de la mise à jour de l\'animal: ' . $e->getMessage(), 500);
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
            $animalNom = $animal->nom;
            $animalId = $animal->id;
            
            // 🔔 ENVOYER LA NOTIFICATION AVANT LA SUPPRESSION
            try {
                Log::info('📤 Envoi notification suppression animal', [
                    'user_id' => $user->id,
                    'animal_id' => $animalId,
                    'animal_nom' => $animalNom
                ]);
                
                // Créer un clone pour la notification
                $animalClone = clone $animal;
                
                $user->notify(new AnimalNotification($animalClone, 'deleted'));
                
                Log::info('✅ Notification suppression animal envoyée');
            } catch (\Exception $e) {
                Log::error('❌ Erreur envoi notification suppression', [
                    'error' => $e->getMessage()
                ]);
            }
            
            // Supprimer l'image
            if ($animal->img_url && !str_contains($animal->img_url, 'default-')) {
                $this->deleteImage($animal->img_url);
            }
            
            $animal->delete();
            
            DB::commit();
            
            return $this->successResponse(null, 'Animal supprimé avec succès.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur suppression animal: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la suppression de l\'animal.', 500);
        }
    }

    // AJOUTER UNE MÉTHODE POUR VÉRIFIER LES ALERTES SANITAIRES EN ARRIÈRE-PLAN
    public function checkHealthAlerts()
    {
        try {
            $animals = Animal::where('statut', 'actif')
                ->whereIn('statut_sanitaire', ['malade', 'critique'])
                ->with('elevage.user')
                ->get();
            
            foreach ($animals as $animal) {
                $user = $animal->elevage?->user;
                if ($user) {
                    $user->notify(new AnimalNotification($animal, 'health_alert', [
                        'status' => $animal->statut_sanitaire
                    ]));
                }
            }
            
            Log::info('✅ Vérification des alertes sanitaires terminée', [
                'animals_checked' => $animals->count()
            ]);
            
            return $this->successResponse([
                'checked' => $animals->count(),
                'notified' => $animals->count()
            ], 'Alertes sanitaires vérifiées.');
            
        } catch (\Exception $e) {
            Log::error('Erreur vérification alertes sanitaires: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la vérification des alertes.', 500);
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
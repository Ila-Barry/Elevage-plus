<?php
// app/Http/Controllers/Api/ProduitController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Produit\CreateProduitRequest;
use App\Http\Requests\Api\Produit\UpdateProduitRequest;
use App\Http\Requests\Api\Stock\StockFilterRequest;
use App\Http\Resources\ProduitResource;
use App\Models\Produit;
use App\Models\Elevage;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Contrôleur ProduitController
 * 
 * Gère toutes les opérations liées aux produits :
 * - CRUD des produits
 * - Gestion du stock
 * - Alertes de stock critique
 * - Filtres et recherches
 */
class ProduitController extends Controller
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
     * Liste des produits
     *
     * @param StockFilterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(StockFilterRequest $request)
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
        
        $query = Produit::whereIn('elevage_id', $elevageIds)
            ->with(['elevage']);
        
        // Filtre par catégorie
        if ($request->filled('categorie')) {
            $query->byCategorie($request->categorie);
        }
        
        // Filtre par statut
        if ($request->filled('statut')) {
            switch ($request->statut) {
                case 'critique':
                    $query->critique();
                    break;
                case 'rupture':
                    $query->rupture();
                    break;
                case 'actif':
                    $query->actif();
                    break;
            }
        }
        
        // Filtre par élevage
        if ($request->filled('elevage_id')) {
            $elevage = Elevage::where('id', $request->elevage_id)
                ->where('user_id', $user->id)
                ->firstOrFail();
            $query->where('elevage_id', $elevage->id);
        }
        
        // Recherche textuelle
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                ->orWhere('fournisseur', 'LIKE', "%{$search}%")
                ->orWhere('code_barre', 'LIKE', "%{$search}%");
            });
        }
        
        // Tri
        $sort = $request->get('sort', 'nom');
        $direction = $request->get('direction', 'asc');
        $query->orderBy($sort, $direction);
        
        $perPage = $request->get('per_page', 15);
        $produits = $query->paginate($perPage);
        
        // Transformer la collection avec les resources
        $produits->getCollection()->transform(function ($produit) {
            return (new ProduitResource($produit));
        });
        
        return $this->successResponse([
            'data' => $produits->items(),
            'meta' => [
                'current_page' => $produits->currentPage(),
                'last_page' => $produits->lastPage(),
                'per_page' => $produits->perPage(),
                'total' => $produits->total(),
            ],
        ]);
    }

    /**
     * Créer un nouveau produit
     *
     * @param CreateProduitRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateProduitRequest $request)
    {
        DB::beginTransaction();
        
        try {
            $user = $request->user();
            
            // Vérifier que l'élevage appartient à l'utilisateur
            $elevage = Elevage::where('id', $request->elevage_id)
                ->where('user_id', $user->id)
                ->firstOrFail();
            
            $data = $request->validated();
            $data['elevage_id'] = $elevage->id;
            
            // Upload de la photo
            if ($request->hasFile('photo')) {
                $data['photo_url'] = $this->uploadPhoto($request->file('photo'));
            }
            
            // Quantité initiale
            $quantiteInitiale = $data['quantite_initiale'] ?? 0;
            unset($data['quantite_initiale']);
            $data['quantite'] = $quantiteInitiale;
            
            // Créer le produit
            $produit = Produit::create($data);
            
            // Créer un mouvement initial si quantité > 0
            if ($quantiteInitiale > 0) {
                $produit->addStock($quantiteInitiale, [
                    'motif' => 'inventaire',
                    'description' => 'Stock initial à la création',
                    'user_id' => $user->id,
                ]);
            }
            
            DB::commit();
            
            return $this->successResponse(
                new ProduitResource($produit),
                'Produit créé avec succès.',
                201
            );
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur création produit: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la création du produit.', 500);
        }
    }

    /**
     * Afficher un produit spécifique
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        
        $produit = Produit::whereHas('elevage', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['elevage', 'mouvements.user'])
            ->findOrFail($id);
        
        // Utilisation de la nouvelle méthode avec setter
        $resource = (new ProduitResource($produit))->setIncludeMouvements(true);
        
        return $this->successResponse($resource);
    }

    /**
     * Mettre à jour un produit
     *
     * @param UpdateProduitRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateProduitRequest $request, $id)
    {
        $user = $request->user();
        
        $produit = Produit::whereHas('elevage', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            $data = $request->validated();
            
            // Gestion de la photo
            if ($request->hasFile('photo')) {
                // Supprimer l'ancienne photo
                if ($produit->photo_url) {
                    $this->deletePhoto($produit->photo_url);
                }
                $data['photo_url'] = $this->uploadPhoto($request->file('photo'));
            } elseif ($request->input('delete_photo')) {
                if ($produit->photo_url) {
                    $this->deletePhoto($produit->photo_url);
                }
                $data['photo_url'] = null;
            }
            
            $produit->update($data);
            
            DB::commit();
            
            return $this->successResponse(
                new ProduitResource($produit),
                'Produit mis à jour avec succès.'
            );
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur mise à jour produit: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la mise à jour du produit.', 500);
        }
    }

    /**
     * Supprimer un produit
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        
        $produit = Produit::whereHas('elevage', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            // Supprimer la photo
            if ($produit->photo_url) {
                $this->deletePhoto($produit->photo_url);
            }
            
            // Les mouvements seront supprimés automatiquement (cascade)
            $produit->delete();
            
            DB::commit();
            
            return $this->successResponse(null, 'Produit supprimé avec succès.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur suppression produit: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la suppression du produit.', 500);
        }
    }

    /**
     * Produits en stock critique
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function produitsCritiques(Request $request)
    {
        $user = $request->user();
        
        $elevageIds = $user->elevages()->pluck('id');
        
        $produits = Produit::whereIn('elevage_id', $elevageIds)
            ->critique()
            ->orderBy('quantite', 'asc')
            ->get();
        
        return $this->successResponse([
            'count' => $produits->count(),
            'data' => ProduitResource::collection($produits),
        ]);
    }

    /**
     * Produits en rupture de stock
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function produitsRupture(Request $request)
    {
        $user = $request->user();
        
        $elevageIds = $user->elevages()->pluck('id');
        
        $produits = Produit::whereIn('elevage_id', $elevageIds)
            ->rupture()
            ->orderBy('nom', 'asc')
            ->get();
        
        return $this->successResponse([
            'count' => $produits->count(),
            'data' => ProduitResource::collection($produits),
        ]);
    }

    /**
     * Statistiques globales des stocks
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
        
        $query = Produit::whereIn('elevage_id', $elevageIds);
        
        $stats = [
            'total_produits' => $query->count(),
            'valeur_totale_stock' => $query->sum(DB::raw('quantite * COALESCE(prix_unitaire, 0)')),
            'quantite_totale' => $query->sum('quantite'),
            'produits_critiques' => (clone $query)->critique()->count(),
            'produits_rupture' => (clone $query)->rupture()->count(),
            'categories' => [],
        ];
        
        // Statistiques par catégorie
        foreach (array_keys(Produit::CATEGORIES) as $categorie) {
            $stats['categories'][$categorie] = [
                'label' => Produit::CATEGORIES[$categorie],
                'count' => (clone $query)->byCategorie($categorie)->count(),
                'valeur_totale' => (clone $query)->byCategorie($categorie)->sum(DB::raw('quantite * COALESCE(prix_unitaire, 0)')),
            ];
        }
        
        return $this->successResponse($stats);
    }

    /**
     * Upload de photo
     */
    private function uploadPhoto($photo): string
    {
        $filename = 'produit_' . time() . '_' . Str::random(10) . '.' . $photo->getClientOriginalExtension();
        $path = $photo->storeAs('produits', $filename, 'public');
        return $path;
    }

    /**
     * Suppression de photo
     */
    private function deletePhoto(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
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
        ];
    }

    /**
     * Statistiques vides
     */
    private function getEmptyStats(): array
    {
        return [
            'total_produits' => 0,
            'valeur_totale_stock' => 0,
            'quantite_totale' => 0,
            'produits_critiques' => 0,
            'produits_rupture' => 0,
            'categories' => [],
        ];
    }
}
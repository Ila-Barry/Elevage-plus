<?php
// app/Http/Controllers/Api/StockController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Stock\CreateMouvementRequest;
use App\Http\Requests\Api\Stock\StockFilterRequest;
use App\Http\Resources\MouvementStockResource;
use App\Http\Resources\StockReportResource;
use App\Models\Produit;
use App\Models\MouvementStock;
use App\Events\StockCritique;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Contrôleur StockController
 * 
 * Gère toutes les opérations liées aux mouvements de stock :
 * - Entrées et sorties
 * - Historique des mouvements
 * - Rapports et statistiques
 */
class StockController extends Controller
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
     * Historique des mouvements
     *
     * @param StockFilterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function historique(StockFilterRequest $request)
    {
        $user = $request->user();
        
        $elevageIds = $user->elevages()->pluck('id');
        
        if ($elevageIds->isEmpty()) {
            return $this->successResponse([
                'data' => [],
                'meta' => $this->getEmptyMeta(),
            ]);
        }
        
        $query = MouvementStock::whereIn('elevage_id', $elevageIds)
            ->with(['produit', 'user']);
        
        // Filtre par produit
        if ($request->filled('produit_id')) {
            $query->where('produit_id', $request->produit_id);
        }
        
        // Filtre par type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Filtre par période
        if ($request->filled('date_debut')) {
            $query->whereDate('date_mouvement', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date_mouvement', '<=', $request->date_fin);
        }
        
        // Filtre par motif
        if ($request->filled('motif')) {
            $query->where('motif', $request->motif);
        }
        
        $perPage = $request->get('per_page', 20);
        $mouvements = $query->orderBy('date_mouvement', 'desc')
            ->paginate($perPage);
        
        return $this->successResponse([
            'data' => MouvementStockResource::collection($mouvements),
            'meta' => [
                'current_page' => $mouvements->currentPage(),
                'last_page' => $mouvements->lastPage(),
                'per_page' => $mouvements->perPage(),
                'total' => $mouvements->total(),
            ],
        ]);
    }

    /**
     * Ajouter une entrée de stock
     *
     * @param CreateMouvementRequest $request
     * @param int $produitId
     * @return \Illuminate\Http\JsonResponse
     */
    public function addStock(CreateMouvementRequest $request, $produitId)
    {
        $user = $request->user();
        
        $produit = Produit::whereHas('elevage', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->findOrFail($produitId);
        
        DB::beginTransaction();
        
        try {
            $quantite = $request->quantite;
            
            $mouvement = $produit->addStock($quantite, [
                'motif' => $request->motif,
                'description' => $request->description,
                'reference_facture' => $request->reference_facture,
                'fournisseur' => $request->fournisseur,
                'user_id' => $user->id,
                'date_mouvement' => $request->date_mouvement,
            ]);
            
            DB::commit();
            
            // Vérifier si le stock est critique
            if ($produit->is_critique) {
                event(new StockCritique($produit));
            }
            
            return $this->successResponse(
                new MouvementStockResource($mouvement->load('produit', 'user')),
                'Entrée de stock ajoutée avec succès.'
            );
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur ajout stock: ' . $e->getMessage());
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    /**
     * Retirer du stock (sortie)
     *
     * @param CreateMouvementRequest $request
     * @param int $produitId
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeStock(CreateMouvementRequest $request, $produitId)
    {
        $user = $request->user();
        
        $produit = Produit::whereHas('elevage', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->findOrFail($produitId);
        
        DB::beginTransaction();
        
        try {
            $quantite = $request->quantite;
            
            $mouvement = $produit->removeStock($quantite, [
                'motif' => $request->motif,
                'description' => $request->description,
                'reference_facture' => $request->reference_facture,
                'destinataire' => $request->destinataire,
                'user_id' => $user->id,
                'date_mouvement' => $request->date_mouvement,
            ]);
            
            DB::commit();
            
            // Vérifier si le stock est critique
            if ($produit->is_critique) {
                event(new StockCritique($produit));
            }
            
            return $this->successResponse(
                new MouvementStockResource($mouvement->load('produit', 'user')),
                'Sortie de stock enregistrée avec succès.'
            );
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur retrait stock: ' . $e->getMessage());
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    /**
     * Rapport des stocks
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function rapport(Request $request)
    {
        $user = $request->user();
        
        $elevageIds = $user->elevages()->pluck('id');
        
        if ($elevageIds->isEmpty()) {
            return $this->successResponse($this->getEmptyReport());
        }
        
        $query = Produit::whereIn('elevage_id', $elevageIds);
        
        // Rapport par catégorie
        $parCategorie = [];
        foreach (array_keys(Produit::CATEGORIES) as $categorie) {
            $produitsQuery = (clone $query)->byCategorie($categorie);
            $parCategorie[] = [
                'categorie' => $categorie,
                'label' => Produit::CATEGORIES[$categorie],
                'total_produits' => $produitsQuery->count(),
                'quantite_totale' => (float) $produitsQuery->sum('quantite'),
                'valeur_totale' => (float) $produitsQuery->sum(DB::raw('quantite * COALESCE(prix_unitaire, 0)')),
                'produits_critiques' => (clone $produitsQuery)->critique()->count(),
                'produits_rupture' => (clone $produitsQuery)->rupture()->count(),
            ];
        }
        
        // Rapport par statut
        $parStatut = [
            'actif' => [
                'label' => 'Actif',
                'count' => (clone $query)->actif()->count(),
                'valeur' => (float) (clone $query)->actif()->sum(DB::raw('quantite * COALESCE(prix_unitaire, 0)')),
            ],
            'critique' => [
                'label' => 'En stock critique',
                'count' => (clone $query)->critique()->count(),
                'valeur' => (float) (clone $query)->critique()->sum(DB::raw('quantite * COALESCE(prix_unitaire, 0)')),
            ],
            'rupture' => [
                'label' => 'En rupture',
                'count' => (clone $query)->rupture()->count(),
                'valeur' => (float) (clone $query)->rupture()->sum(DB::raw('quantite * COALESCE(prix_unitaire, 0)')),
            ],
        ];
        
        // Mouvements du mois
        $totalEntreesMois = MouvementStock::whereIn('elevage_id', $elevageIds)
            ->moisEnCours()
            ->entrees()
            ->sum('quantite');
            
        $totalSortiesMois = MouvementStock::whereIn('elevage_id', $elevageIds)
            ->moisEnCours()
            ->sorties()
            ->sum('quantite');
        
        $rapport = [
            'total_produits' => $query->count(),
            'valeur_totale_stock' => (float) $query->sum(DB::raw('quantite * COALESCE(prix_unitaire, 0)')),
            'quantite_totale' => (float) $query->sum('quantite'),
            'produits_critiques' => (clone $query)->critique()->count(),
            'produits_rupture' => (clone $query)->rupture()->count(),
            'produits_expires' => (clone $query)->expires()->count(),
            'par_categorie' => $parCategorie,
            'par_statut' => $parStatut,
            'total_entrees_mois' => (float) $totalEntreesMois,
            'total_sorties_mois' => (float) $totalSortiesMois,
        ];
        
        return $this->successResponse($rapport);
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
     * Rapport vide
     */
    private function getEmptyReport(): array
    {
        return [
            'total_produits' => 0,
            'valeur_totale_stock' => 0,
            'quantite_totale' => 0,
            'produits_critiques' => 0,
            'produits_rupture' => 0,
            'produits_expires' => 0,
            'par_categorie' => [],
            'par_statut' => [],
            'total_entrees_mois' => 0,
            'total_sorties_mois' => 0,
        ];
    }
}
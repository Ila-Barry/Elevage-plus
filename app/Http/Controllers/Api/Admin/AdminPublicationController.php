<?php
// app/Http/Controllers/Api/Admin/AdminPublicationController.php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\PublicationFilterRequest;
use App\Http\Requests\Api\Admin\UpdatePublicationRequest;
use App\Http\Requests\Api\Admin\ChangePublicationStatusRequest;
use App\Http\Requests\Api\Admin\ReviewPublicationRequest;
use App\Http\Resources\Admin\AdminPublicationResource;
use App\Models\Publication;
use App\Models\Report;
use App\Services\AlertService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller AdminPublicationController
 * 
 * Gère toutes les opérations d'administration sur les publications
 */
class AdminPublicationController extends Controller
{
    use ApiResponseTrait;

    protected AlertService $alertService;

    public function __construct(AlertService $alertService)
    {
        $this->alertService = $alertService;
        $this->middleware(['auth:api', 'admin']);
    }

    /**
     * Lister toutes les publications avec filtres et pagination
     * 
     * @param PublicationFilterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(PublicationFilterRequest $request)
    {
        $query = Publication::with(['user']);

        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtre par catégorie
        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        // Filtre par période
        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        // Recherche textuelle
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('titre', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Tri
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $perPage = $request->get('per_page', 20);
        $publications = $query->paginate($perPage);

        return $this->successResponse([
            'data' => AdminPublicationResource::collection($publications),
            'meta' => [
                'current_page' => $publications->currentPage(),
                'last_page' => $publications->lastPage(),
                'per_page' => $publications->perPage(),
                'total' => $publications->total(),
                'total_publiees' => Publication::where('statut', 'publiee')->count(),
                'total_signalees' => Publication::where('statut', 'signalee')->count(),
                'total_bloquees' => Publication::where('statut', 'bloquee')->count(),
            ],
        ]);
    }

    /**
     * Obtenir les détails d'une publication spécifique
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $publication = Publication::with(['user', 'reports.user', 'commentaires.user'])
            ->findOrFail($id);

        return $this->successResponse(new AdminPublicationResource($publication));
    }

    /**
     * Modifier une publication (Admin)
     * 
     * @param UpdatePublicationRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdatePublicationRequest $request, $id)
    {
        $publication = Publication::findOrFail($id);

        DB::beginTransaction();

        try {
            $data = $request->validated();

            $publication->update($data);

            DB::commit();

            $this->alertService->sendAdminAlert(
                '📝 Publication modifiée',
                "La publication '{$publication->titre}' a été modifiée par l'admin.",
                'info'
            );

            return $this->successResponse(
                new AdminPublicationResource($publication),
                'Publication mise à jour avec succès.'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur mise à jour publication admin: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la mise à jour.', 500);
        }
    }

    /**
     * Changer le statut d'une publication
     * 
     * @param ChangePublicationStatusRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeStatus(ChangePublicationStatusRequest $request, $id)
    {
        $publication = Publication::findOrFail($id);

        DB::beginTransaction();

        try {
            $newStatus = $request->statut;
            $oldStatus = $publication->statut;

            // Si on bloque, ajouter la justification
            if ($newStatus === 'bloquee') {
                $publication->raison_blocage = $request->justification;
            } else {
                $publication->raison_blocage = null;
            }

            $publication->statut = $newStatus;
            $publication->save();

            // Traiter les signalements en attente si la publication est bloquée ou réactivée
            if ($newStatus === 'bloquee' || $newStatus === 'publiee') {
                Report::where('publication_id', $publication->id)
                    ->where('statut', 'en_attente')
                    ->update(['statut' => 'traite']);
            }

            DB::commit();

            // Notifier l'auteur
            if ($newStatus === 'bloquee') {
                $this->alertService->sendReportResolvedAlert($publication, 'blocked');
            } elseif ($newStatus === 'publiee' && $oldStatus === 'bloquee') {
                $this->alertService->sendReportResolvedAlert($publication, 'unblocked');
            }

            $this->alertService->sendAdminAlert(
                '📊 Statut publication modifié',
                "La publication '{$publication->titre}' est passée de '{$oldStatus}' à '{$newStatus}'.",
                'info'
            );

            return $this->successResponse([
                'id' => $publication->id,
                'statut' => $publication->statut,
                'statut_label' => $this->getStatutLabel($newStatus),
            ], 'Statut de la publication mis à jour.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur changement statut publication: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors du changement de statut.', 500);
        }
    }

    /**
     * Approuver / Rejeter une publication signalée
     * 
     * @param ReviewPublicationRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function review(ReviewPublicationRequest $request, $id)
    {
        $publication = Publication::findOrFail($id);

        DB::beginTransaction();

        try {
            $action = $request->action;

            if ($action === 'approve') {
                $publication->statut = 'publiee';
                $publication->raison_blocage = null;
                
                // Marquer les signalements comme traités
                Report::where('publication_id', $publication->id)
                    ->where('statut', 'en_attente')
                    ->update(['statut' => 'traite']);
                
                $message = 'Publication approuvée et réactivée.';
                
            } else {
                // Reject
                $publication->statut = 'bloquee';
                $publication->raison_blocage = $request->justification;
                
                // Marquer les signalements comme traités
                Report::where('publication_id', $publication->id)
                    ->where('statut', 'en_attente')
                    ->update(['statut' => 'traite']);
                
                $message = 'Publication rejetée et bloquée.';
            }

            $publication->save();

            DB::commit();

            // Notifier l'auteur
            if ($action === 'reject') {
                $this->alertService->sendReportResolvedAlert($publication, 'blocked');
            } else {
                $this->alertService->sendReportResolvedAlert($publication, 'unblocked');
            }

            return $this->successResponse(
                new AdminPublicationResource($publication),
                $message
            );

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur review publication: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors du traitement.', 500);
        }
    }

    /**
     * Supprimer une publication
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $publication = Publication::findOrFail($id);

        DB::beginTransaction();

        try {
            $titre = $publication->titre;
            $auteur = $publication->user->name;

            $publication->delete();

            DB::commit();

            $this->alertService->sendAdminAlert(
                '🗑️ Publication supprimée',
                "La publication '{$titre}' de l'utilisateur {$auteur} a été supprimée par l'admin.",
                'warning'
            );

            return $this->successResponse(null, 'Publication supprimée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur suppression publication: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la suppression.', 500);
        }
    }

    protected function getStatutLabel(string $statut): string
    {
        return match($statut) {
            'publiee' => '✅ Publiée',
            'signalee' => '⚠️ Signalée',
            'bloquee' => '🔴 Bloquée',
            default => $statut,
        };
    }
}
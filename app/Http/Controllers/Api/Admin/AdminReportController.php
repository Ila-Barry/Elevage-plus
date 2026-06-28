<?php
// app/Http/Controllers/Api/Admin/AdminReportController.php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\ReportFilterRequest;
use App\Http\Requests\Api\Admin\HandleReportRequest;
use App\Http\Resources\Admin\AdminReportResource;
use App\Models\Report;
use App\Models\Publication;
use App\Models\User;
use App\Services\AlertService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller AdminReportController
 * 
 * Gère toutes les opérations sur les signalements
 */
class AdminReportController extends Controller
{
    use ApiResponseTrait;

    protected AlertService $alertService;

    public function __construct(AlertService $alertService)
    {
        $this->alertService = $alertService;
        $this->middleware(['auth:api', 'admin']);
    }

    /**
     * Lister tous les signalements avec filtres
     * 
     * @param ReportFilterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ReportFilterRequest $request)
    {
        $query = Report::with(['publication.user', 'user'])
            ->whereHas('publication');

        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtre par motif
        if ($request->filled('motif')) {
            $query->where('motif', $request->motif);
        }

        // Filtre par période
        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        // Tri
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $perPage = $request->get('per_page', 20);
        $reports = $query->paginate($perPage);

        return $this->successResponse([
            'data' => AdminReportResource::collection($reports),
            'meta' => [
                'current_page' => $reports->currentPage(),
                'last_page' => $reports->lastPage(),
                'per_page' => $reports->perPage(),
                'total' => $reports->total(),
                'en_attente' => Report::where('statut', 'en_attente')->count(),
                'traites' => Report::where('statut', 'traite')->count(),
                'ignores' => Report::where('statut', 'ignore')->count(),
            ],
        ]);
    }

    /**
     * Obtenir les détails d'un signalement spécifique
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $report = Report::with(['publication.user', 'publication.reports.user', 'user'])
            ->findOrFail($id);

        return $this->successResponse(new AdminReportResource($report));
    }

    /**
     * Traiter un signalement
     * 
     * @param HandleReportRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(HandleReportRequest $request, $id)
    {
        $report = Report::with(['publication.user'])->findOrFail($id);

        if ($report->statut !== 'en_attente') {
            return $this->errorResponse('Ce signalement a déjà été traité.', 422);
        }

        DB::beginTransaction();

        try {
            $action = $request->action;
            $publication = $report->publication;
            $commentaire = $request->commentaire_moderation;

            switch ($action) {
                case 'publication_supprimee':
                    $publication->delete();
                    $message = 'La publication a été supprimée.';
                    $this->alertService->sendAdminAlert(
                        '🗑️ Publication supprimée suite à signalement',
                        "La publication '{$publication->titre}' a été supprimée suite à un signalement.",
                        'danger'
                    );
                    break;

                case 'publication_bloquee':
                    $publication->statut = 'bloquee';
                    $publication->raison_blocage = $request->justification;
                    $publication->save();
                    $message = 'La publication a été bloquée.';
                    $this->alertService->sendReportResolvedAlert($publication, 'blocked');
                    break;

                case 'utilisateur_averti':
                    // Marquer le signalement comme traité, avertir l'utilisateur
                    $message = "L'utilisateur a été averti. {$commentaire}";
                    // Notification à l'utilisateur (si implémenté)
                    break;

                case 'utilisateur_banni':
                    $publication->user->update(['status' => 'bannie']);
                    $message = "L'utilisateur a été banni. {$commentaire}";
                    $this->alertService->sendAdminAlert(
                        '🚫 Utilisateur banni suite à signalement',
                        "L'utilisateur {$publication->user->name} a été banni suite à un signalement.",
                        'danger'
                    );
                    break;

                case 'aucune_action':
                    $message = "Aucune action prise. {$commentaire}";
                    break;

                default:
                    return $this->errorResponse('Action non reconnue.', 422);
            }

            // Mettre à jour le statut du signalement
            $report->statut = 'traite';
            $report->save();

            // Si la publication n'a pas été supprimée, marquer tous ses signalements comme traités
            if ($action !== 'publication_supprimee') {
                Report::where('publication_id', $publication->id)
                    ->where('statut', 'en_attente')
                    ->update(['statut' => 'traite']);
            }

            // Envoyer une notification à l'auteur de la publication (sauf si supprimée)
            if ($action !== 'publication_supprimee' && $publication->exists) {
                $this->alertService->sendReportResolvedAlert($publication, 'blocked');
            }

            DB::commit();

            return $this->successResponse([
                'report' => new AdminReportResource($report),
                'action_effectuee' => $action,
                'message_utilisateur' => $message,
            ], 'Signalement traité avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur traitement signalement: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors du traitement du signalement.', 500);
        }
    }

    /**
     * Ignorer un signalement
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function ignore(Request $request, $id)
    {
        $report = Report::findOrFail($id);

        if ($report->statut !== 'en_attente') {
            return $this->errorResponse('Ce signalement a déjà été traité.', 422);
        }

        $report->statut = 'ignore';
        $report->save();

        $this->alertService->sendAdminAlert(
            '➖ Signalement ignoré',
            "Le signalement #{$report->id} a été ignoré par l'admin.",
            'info'
        );

        return $this->successResponse(
            new AdminReportResource($report),
            'Signalement ignoré avec succès.'
        );
    }
}
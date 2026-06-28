<?php
// app/Http/Controllers/Api/Admin/AdminUserController.php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\UserFilterRequest;
use App\Http\Requests\Api\Admin\UpdateUserRequest;
use App\Http\Requests\Api\Admin\ChangeUserStatusRequest;
use App\Http\Requests\Api\Admin\ResetPasswordRequest;
use App\Http\Resources\Admin\AdminUserResource;
use App\Models\User;
use App\Services\AlertService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Controller AdminUserController
 * 
 * Gère toutes les opérations d'administration sur les utilisateurs
 */
class AdminUserController extends Controller
{
    use ApiResponseTrait;

    protected AlertService $alertService;

    public function __construct(AlertService $alertService)
    {
        $this->alertService = $alertService;
        $this->middleware(['auth:api', 'admin']);
    }

    /**
     * Lister tous les utilisateurs avec filtres et pagination
     * 
     * @param UserFilterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(UserFilterRequest $request)
    {
        $query = User::query();

        // Filtre par rôle
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Recherche textuelle
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('telephone', 'LIKE', "%{$search}%");
            });
        }

        // Tri
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $perPage = $request->get('per_page', 15);
        $users = $query->paginate($perPage);

        return $this->successResponse([
            'data' => AdminUserResource::collection($users),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'total_eleveurs' => User::where('role', 'eleveur')->count(),
                'total_admins' => User::where('role', 'admin')->count(),
                'total_visiteurs' => User::where('role', 'visiteur')->count(),
                'total_actifs' => User::where('status', 'active')->count(),
                'total_bannis' => User::where('status', 'bannie')->count(),
            ],
        ]);
    }

    /**
     * Obtenir les détails d'un utilisateur spécifique
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = User::with(['elevages', 'publications', 'commentaires'])
            ->findOrFail($id);

        return $this->successResponse(new AdminUserResource($user));
    }

    /**
     * Modifier un utilisateur
     * 
     * @param UpdateUserRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::findOrFail($id);

        // Empêcher la modification de son propre rôle si on est admin
        if ($request->has('role') && $user->id === auth()->id()) {
            return $this->errorResponse('Vous ne pouvez pas modifier votre propre rôle.', 422);
        }

        DB::beginTransaction();

        try {
            $data = $request->validated();
            
            // Si on change le statut, envoyer une notification
            if (isset($data['status']) && $data['status'] !== $user->status) {
                $this->handleStatusChange($user, $data['status']);
            }

            $user->update($data);

            DB::commit();

            return $this->successResponse(
                new AdminUserResource($user),
                'Utilisateur mis à jour avec succès.'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur mise à jour utilisateur: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la mise à jour de l\'utilisateur.', 500);
        }
    }

    /**
     * Bannir / Réactiver un utilisateur
     * 
     * @param ChangeUserStatusRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeStatus(ChangeUserStatusRequest $request, $id)
    {
        $user = User::findOrFail($id);

        // Empêcher de bannir son propre compte
        if ($user->id === auth()->id()) {
            return $this->errorResponse('Vous ne pouvez pas modifier votre propre statut.', 422);
        }

        // Empêcher de bannir un autre admin
        if ($user->isAdmin() && $request->status === 'bannie') {
            return $this->errorResponse('Vous ne pouvez pas bannir un autre administrateur.', 422);
        }

        DB::beginTransaction();

        try {
            $this->handleStatusChange($user, $request->status, $request->motif_ban ?? null);

            $user->update(['status' => $request->status]);

            DB::commit();

            $message = $request->status === 'active' 
                ? 'Utilisateur réactivé avec succès.' 
                : 'Utilisateur banni avec succès.';

            return $this->successResponse([
                'id' => $user->id,
                'status' => $user->status,
                'status_label' => $user->status === 'active' ? 'Actif' : 'Banni',
            ], $message);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur changement statut: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors du changement de statut.', 500);
        }
    }

    /**
     * Réinitialiser le mot de passe d'un utilisateur
     * 
     * @param ResetPasswordRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request, $id)
    {
        $user = User::findOrFail($id);

        try {
            // Générer un token de réinitialisation
            $token = Password::createToken($user);
            
            // Envoyer l'email de réinitialisation
            $user->sendPasswordResetNotification($token);

            // Optionnel : envoyer une notification à l'admin
            if ($request->notify_user) {
                $this->alertService->sendAdminAlert(
                    '🔑 Réinitialisation de mot de passe',
                    "Le mot de passe de l'utilisateur {$user->name} a été réinitialisé par l'admin.",
                    'info'
                );
            }

            return $this->successResponse(null, 'Email de réinitialisation envoyé avec succès.');

        } catch (\Exception $e) {
            \Log::error('Erreur réinitialisation mot de passe: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de l\'envoi de l\'email.', 500);
        }
    }

    /**
     * Supprimer un compte utilisateur
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Empêcher de supprimer son propre compte
        if ($user->id === auth()->id()) {
            return $this->errorResponse('Vous ne pouvez pas supprimer votre propre compte via cette API.', 422);
        }

        // Vérifier la confirmation
        $request->validate([
            'confirmation' => ['required', 'string', 'in:SUPPRIMER'],
        ]);

        DB::beginTransaction();

        try {
            // Supprimer les fichiers associés
            if ($user->photo_url && !str_contains($user->photo_url, 'ui-avatars.com')) {
                $photoPath = str_replace('/storage/', '', $user->photo_url);
                Storage::disk('public')->delete($photoPath);
            }

            // Les relations seront supprimées automatiquement (cascade)
            $user->delete();

            DB::commit();

            $this->alertService->sendAdminAlert(
                '🗑️ Compte supprimé',
                "Le compte de l'utilisateur {$user->name} ({$user->email}) a été supprimé par l'admin.",
                'warning'
            );

            return $this->successResponse(null, 'Utilisateur supprimé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur suppression utilisateur: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la suppression de l\'utilisateur.', 500);
        }
    }

    /**
     * Exporter les utilisateurs en CSV
     * 
     * @param UserFilterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function exportCSV(UserFilterRequest $request)
    {
        try {
            // Appliquer les mêmes filtres que la liste
            $query = User::query();

            if ($request->filled('role')) {
                $query->where('role', $request->role);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                });
            }

            $users = $query->get();

            // Générer le CSV
            $filename = 'utilisateurs_' . now()->format('Y-m-d') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];

            $columns = ['ID', 'Nom', 'Email', 'Téléphone', 'Rôle', 'Statut', 'Date d\'inscription'];
            
            $callback = function() use ($users, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns, ';');

                foreach ($users as $user) {
                    fputcsv($file, [
                        $user->id,
                        $user->name,
                        $user->email,
                        $user->telephone,
                        $user->role,
                        $user->status,
                        $user->created_at->format('d/m/Y H:i'),
                    ], ';');
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            \Log::error('Erreur export CSV: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de l\'export des utilisateurs.', 500);
        }
    }

    /**
     * Gère le changement de statut d'un utilisateur
     */
    protected function handleStatusChange(User $user, string $newStatus, ?string $motif = null): void
    {
        if ($newStatus === 'bannie') {
            // Bannir l'utilisateur
            $this->alertService->sendAdminAlert(
                '🚫 Utilisateur banni',
                "L'utilisateur {$user->name} ({$user->email}) a été banni. Motif : " . ($motif ?? 'Non spécifié'),
                'danger'
            );
            
            // Notification à l'utilisateur (si implémenté)
            // $user->notify(new UserBannedNotification($motif));
            
        } elseif ($newStatus === 'active' && $user->status === 'bannie') {
            // Réactiver l'utilisateur
            $this->alertService->sendAdminAlert(
                '✅ Utilisateur réactivé',
                "L'utilisateur {$user->name} ({$user->email}) a été réactivé.",
                'success'
            );
            
            // Notification à l'utilisateur (si implémenté)
            // $user->notify(new UserActivatedNotification());
        }
    }
}
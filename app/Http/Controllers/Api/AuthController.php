<?php
// app/Http/Controllers/Api/AuthController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Http\Requests\Api\ChangePasswordRequest;
use App\Http\Requests\Api\TwoFactorRequest;
use App\Http\Requests\Api\DeleteAccountRequest;
use App\Models\User;
use App\Services\TwoFactorService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Hash;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;


/**
 * Contrôleur AuthController
 * * Gère toutes les opérations d'authentification et de gestion de profil
 */
class AuthController extends Controller
{
    use ApiResponseTrait;

    protected TwoFactorService $twoFactorService;

    public function __construct(TwoFactorService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
        
        $this->middleware('auth:api')->except([
            'register',
            'login',
            'verifyTwoFactor',
        ]);
    }

    /**
     * Inscription d'un nouvel utilisateur
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            $userData = $request->validated();
            
            if ($request->hasFile('photo')) {
                $photoPath = $this->uploadProfilePhoto($request->file('photo'));
                $userData['photo_url'] = $photoPath;
            }
            
            unset($userData['photo']);
            
            // ✅ Forcer le statut à 'active' lors de l'inscription
            $userData['status'] = 'active';

            $user = User::create($userData);
            
            if ($request->filled('type_elevage')) {
                $this->createDefaultFarm($user, $request->type_elevage);
            }
            
            DB::commit();
            
            $token = JWTAuth::fromUser($user);
            
            // ✅ Utilisation du ApiResponseTrait standardisé
            return $this->successResponse([
                'user' => $user->makeVisible(['email', 'telephone']),
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ], 'Inscription réussie ! ', 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return $this->validationErrorResponse($e->errors(), 'Erreur de validation');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de l\'inscription: ' . $e->getMessage());
            
            return $this->errorResponse('Une erreur est survenue lors de l\'inscription.', 500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Connexion d'un utilisateur
     */
    public function login(LoginRequest $request): JsonResponse
    {
        Log::info('Tentative de connexion', ['login' => $request->input('login')]);
        
        try {
            $login = $request->input('login');
            $password = $request->input('password');
            
            $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'telephone';
            $user = User::where($field, $login)->first();
            
            if (!$user || !Hash::check($password, $user->password)) {
                Log::warning('Identifiants incorrects', ['login' => $login]);
                return $this->errorResponse('Identifiant ou mot de passe incorrect.', 401);
            }
            
            if ($user->status === 'bannie') {
                Log::warning('Tentative de connexion sur compte banni', ['user_id' => $user->id]);
                return $this->forbiddenResponse('Votre compte a été banni.');
            }
            
            if ($user->status !== 'active' && $user->email_verified_at) {
                $user->status = 'active';
                $user->save();
            }
            
            // Auth::guard('web')->login($user, $request->input('remember', false));
            if (config('session.driver') !== 'array') { 
                try {
                    Auth::login($user, $request->input('remember', false));
                } catch (\Exception $e) {
                    Log::warning('Impossible de créer la session web, utilisation exclusive du JWT.');
                }
            }
            
            $token = JWTAuth::fromUser($user);
            $user->update(['last_login_at' => now()]);

            Log::info('Connexion réussie', ['user_id' => $user->id, 'role' => $user->role]);

            // ✅ Utilisation du ApiResponseTrait standardisé
            return $this->successResponse([
                'user' => $user->makeVisible(['email', 'telephone']),
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ], 'Connexion réussie.');
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la connexion', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse('Erreur lors de la connexion.', 500);
        }
    }

    /**
     * Vérification du code d'authentification à deux facteurs
     */
    public function verifyTwoFactor(TwoFactorRequest $request): JsonResponse
    {
        try {
            $userId = session('2fa:user_id');
            $tempToken = session('2fa:token');
            
            if (!$userId || !$tempToken) {
                return $this->unauthorizedResponse('Session expirée. Veuillez vous reconnecter.');
            }
            
            $user = User::find($userId);
            
            if (!$user) {
                return $this->unauthorizedResponse('Utilisateur introuvable.');
            }
            
            if (!$this->twoFactorService->verifyCode($user, $request->two_factor_code)) {
                return $this->errorResponse('Code d\'authentification invalide ou expiré.', 422);
            }
            
            session()->forget(['2fa:user_id', '2fa:token']);
            $user->update(['last_login_at' => now()]);
            
            return $this->successResponse([
                'user' => $user->makeVisible(['email', 'telephone']),
                'access_token' => $tempToken,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ], 'Authentification réussie.');
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification 2FA: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la vérification.', 500);
        }
    }

    /**
     * Déconnexion de l'utilisateur
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            
            Auth::guard('web')->logout();
            
            $token = JWTAuth::getToken();
            if ($token) {
                JWTAuth::invalidate($token);
            }
            
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            Log::info('Déconnexion réussie', ['user_id' => $user?->id, 'email' => $user?->email]);
            
            return $this->successResponse(null, 'Déconnexion réussie.');
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la déconnexion', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->errorResponse('Erreur lors de la déconnexion.', 500);
        }
    }

    /**
     * Rafraîchir le token JWT
     */
    public function refresh(Request $request): JsonResponse
    {
        try {
            $newToken = Auth::refresh();
            
            return $this->successResponse([
                'access_token' => $newToken,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ], 'Token rafraîchi avec succès.');
            
        } catch (\Exception $e) {
            return $this->unauthorizedResponse('Token invalide ou expiré.');
        }
    }

    /**
     * Profil de l'utilisateur connecté
     */
    public function me(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $user->load(['elevages' => function($query) {
            $query->withCount(['animaux']);
        }]);
        
        $stats = [
            'publications' => $user->publications()->count(),
            'likes_received' => $user->publications()->sum('nbr_likes') ?? 0,
            'commentaires' => $user->commentaires()->count(),
            'elevages' => $user->elevages()->count(),
            'animaux' => $user->elevages->sum('animaux_count') ?? 0,
        ];
        
        return $this->successResponse([
            'user' => $user->makeVisible(['email', 'telephone']),
            'statistics' => $stats,
        ]);
    }

    /**
     * Mise à jour du profil utilisateur
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            $user = Auth::user();
            $updateData = $request->validated();
            
            if ($request->hasFile('photo')) {
                if ($user->photo_url && !str_contains($user->photo_url, 'ui-avatars.com')) {
                    $oldPath = str_replace('/storage/', '', $user->photo_url);
                    Storage::disk('public')->delete($oldPath);
                }
                
                $photoPath = $this->uploadProfilePhoto($request->file('photo'));
                $updateData['photo_url'] = $photoPath;
            }
            
            unset($updateData['photo']);
            $user->update($updateData);
            
            DB::commit();
            
            return $this->successResponse(
                $user->fresh()->makeVisible(['email', 'telephone']),
                'Profil mis à jour avec succès.'
            );
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour du profil: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la mise à jour du profil.', 500);
        }
    }

    /**
     * Changement du mot de passe
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            Log::info('Tentative de changement de mot de passe', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
            
            $user->update([
                'password' => $request->new_password
            ]);
            
            Log::info('Mot de passe modifié avec succès', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
            
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return $this->successResponse(null, 'Mot de passe modifié avec succès. Veuillez vous reconnecter.');
            
        } catch (\Exception $e) {
            Log::error('Erreur lors du changement de mot de passe', [
                'user_id' => auth()->id(),
                'message' => $e->getMessage(),
            ]);
            return $this->errorResponse('Erreur lors du changement de mot de passe.', 500);
        }
    }

    /**
     * Activation/Désactivation du 2FA
     */
    public function toggleTwoFactor(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if ($user->two_factor_enabled) {
                $this->twoFactorService->disableTwoFactor($user);
                $message = 'Authentification à deux facteurs désactivée.';
            } else {
                $this->twoFactorService->enableTwoFactor($user, 'email_based');
                $message = 'Authentification à deux facteurs activée.';
            }
            
            return $this->successResponse([
                'two_factor_enabled' => $user->fresh()->two_factor_enabled,
            ], $message);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors du basculement 2FA: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la modification du 2FA.', 500);
        }
    }

    /**
 * Récupère les préférences de notification
 */
    public function getNotificationPreferences(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            return $this->successResponse([
                'web_notifications' => $user->web_notifications ?? true,
                'email_notifications' => $user->email_notifications ?? false,
                'message_notifications' => $user->message_notifications ?? true,
                'newsletter_subscription' => $user->newsletter_subscription ?? false,
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur récupération préférences: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la récupération des préférences.', 500);
        }
    }

    /**
     * Preference notifications
     */
    public function updateNotificationPreferences(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email_notifications' => 'sometimes|boolean',
            'web_notifications' => 'sometimes|boolean',
            'reminder_notifications' => 'sometimes|boolean',
            'newsletter_subscription' => 'sometimes|boolean',
            'message_notifications' => 'sometimes|boolean',
        ]);
        
        try {
            $user = Auth::user();
            $user->update($validated);
            
            return $this->successResponse([
                'email_notifications' => $user->email_notifications,
                'web_notifications' => $user->web_notifications,
                'reminder_notifications' => $user->reminder_notifications,
                'newsletter_subscription' => $user->newsletter_subscription,
                'message_notifications' => $user->message_notifications,
            ], 'Préférences de notification mises à jour.');
            
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la mise à jour des préférences.', 500);
        }
    }

    /**
     * Visibilité profil
     */
    public function updateProfileVisibility(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'profile_visibility' => 'required|in:public,prive',
        ]);
        
        try {
            $user = Auth::user();
            $user->update($validated);
            
            return $this->successResponse([
                'profile_visibility' => $user->profile_visibility,
            ], 'Visibilité du profil mise à jour.');
            
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la mise à jour de la visibilité.', 500);
        }
    }

    /**
     * Suppression du compte
     */
    public function deleteAccount(DeleteAccountRequest $request): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            $user = Auth::user();
            
            if ($user->photo_url && !str_contains($user->photo_url, 'ui-avatars.com')) {
                $photoPath = str_replace('/storage/', '', $user->photo_url);
                Storage::disk('public')->delete($photoPath);
            }
            
            $user->delete();
            DB::commit();
            Auth::logout();
            
            return $this->successResponse(null, 'Votre compte a été supprimé avec succès. Au revoir !');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression du compte: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la suppression du compte.', 500);
        }
    }

    /**
     * Profil public
     */
    public function publicProfile(int $id): JsonResponse
    {
        try {
            $user = User::where('status', 'active')
                ->withCount(['publications', 'publications as likes_received' => function($query) {
                    $query->select(DB::raw('COALESCE(SUM(nbr_likes), 0)'));
                }])
                ->findOrFail($id);
            
            if (!$user->isProfilePublic() && !Auth::check()) {
                return $this->forbiddenResponse('Ce profil est privé.');
            }
            
            $recentPosts = $user->publications()
                ->where('statut', 'publiee') // Corrigé de 'etat' à 'statut' pour correspondre au HomeController
                ->latest()
                ->limit(10)
                ->get();
            
            $farms = $user->elevages()->withCount('animaux')->get();
            
            return $this->successResponse([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'bio' => $user->bio,
                    'photo_url' => $user->photo_url,
                    'joined_date' => $user->created_at->format('d/m/Y'),
                    'profile_visibility' => $user->profile_visibility,
                ],
                'statistics' => [
                    'total_publications' => $user->publications_count,
                    'total_likes_received' => $user->likes_received ?? 0,
                    'total_farms' => $farms->count(),
                    'total_animals' => $farms->sum('animaux_count'),
                ],
                'recent_publications' => $recentPosts,
                'farms' => $farms,
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Utilisateur non trouvé.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération du profil public: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la récupération du profil.', 500);
        }
    }

    /**
     * Liste des utilisateurs pour la messagerie
     */
    public function getUsers(Request $request): JsonResponse
    {
        $excludeId = $request->get('exclude', auth()->id());
        
        $users = User::where('id', '!=', $excludeId)
            ->where('status', 'active')
            ->select('id', 'name', 'email', 'photo_url', 'role', 'type_elevage', 'commune')
            ->orderBy('name')
            ->limit(50)
            ->get();
        
        return $this->successResponse($users);
    }

    // ========== MÉTHODES PRIVÉES ==========

    private function uploadProfilePhoto($photo): string
    {
        $filename = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
        $path = 'avatars/' . $filename;
        
        $manager = new ImageManager(new Driver());
        $image = $manager->read($photo->getPathname());
        $image->cover(300, 300);
        
        Storage::disk('public')->put($path, (string) $image->encode());
        
        return $path;
    }

    private function createDefaultFarm(User $user, string $typeElevage): void
    {
        $user->elevages()->create([
            'nom' => 'Mon ' . $typeElevage,
            'localisation' => 'Non renseignée',
            'superficie' => 0,
            'type_elevage' => $typeElevage,
            'description' => 'Élevage créé automatiquement lors de l\'inscription.',
        ]);
    }
}
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
use App\Notifications\WelcomeNotification;
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

/**
 * Contrôleur AuthController
 * 
 * Gère toutes les opérations d'authentification et de gestion de profil
 * 
 * @package App\Http\Controllers\Api
 */
class AuthController extends Controller
{
    use ApiResponseTrait;

    /**
     * Service pour l'authentification à deux facteurs
     *
     * @var TwoFactorService
     */
    protected TwoFactorService $twoFactorService;

    /**
     * Constructeur avec injection de dépendances
     *
     * @param TwoFactorService $twoFactorService
     */
    public function __construct(TwoFactorService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
        
        // Les méthodes protégées nécessitent une authentification
        $this->middleware('auth:api')->except([
            'register',
            'login',
            'verifyTwoFactor',
        ]);
    }

    /**
     * Inscription d'un nouvel utilisateur
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            // Préparation des données
            $userData = $request->validated();
            
            // Gérer l'upload de la photo de profil
            if ($request->hasFile('photo')) {
                $photoPath = $this->uploadProfilePhoto($request->file('photo'));
                $userData['photo_url'] = $photoPath;
            }
            
            // Supprimer 'photo' du tableau car ce n'est pas une colonne
            unset($userData['photo']);
            
            // Créer l'utilisateur
            $user = User::create($userData);
            
            // Envoyer l'email de vérification
            $user->sendEmailVerificationNotification();
            
            // Envoyer la notification de bienvenue (email + database)
            $user->notify(new WelcomeNotification($user));
            
            // Créer un élevage par défaut si type_elevage est fourni
            if ($request->filled('type_elevage')) {
                $this->createDefaultFarm($user, $request->type_elevage);
            }
            
            DB::commit();
            
            // Générer le token JWT
            $token = JWTAuth::fromUser($user);
            
            return $this->successResponse([
            'user' => $user->makeVisible(['email', 'telephone']),
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
        ], 'Inscription réussie ! Un email de vérification vous a été envoyé.', 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de l\'inscription: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Une erreur est survenue lors de l\'inscription.',
                'error' => $e->getMessage() // En dev seulement
            ], 500);
        }
    }

    /**
     * Connexion d'un utilisateur
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        Log::info('Tentative de connexion', ['login' => $request->input('login')]);
        
        try {
            $login = $request->input('login');
            $password = $request->input('password');
            
            $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'telephone';
            $user = User::where($field, $login)->first();
            
            if (!$user) {
                Log::warning('Utilisateur non trouvé', ['login' => $login]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Email ou mot de passe incorrect.'
                ], 401);
            }
            
            if (!Hash::check($password, $user->password)) {
                Log::warning('Mot de passe incorrect', ['user_id' => $user->id]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Identifient ou mot de passe incorrect.'
                ], 401);
            }
            
            if ($user->status === 'bannie') {
                Log::warning('Tentative de connexion sur compte banni', ['user_id' => $user->id]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Votre compte a été banni.'
                ], 403);
            }
            
            // Auto-vérification pour les tests
            if (!$user->email_verified_at) {
                Log::info('Email non vérifié - auto vérification pour test', ['user_id' => $user->id]);
                $user->email_verified_at = now();
                $user->save();
            }
            
            if ($user->status !== 'active' && $user->email_verified_at) {
                $user->status = 'active';
                $user->save();
            }
            
            // ✅ AJOUT : Connecter l'utilisateur via session Laravel
            Auth::guard('web')->login($user, $request->input('remember', false));
            
            $token = JWTAuth::fromUser($user);
            $user->update(['last_login_at' => now()]);

            Log::info('Connexion réussie', ['user_id' => $user->id, 'role' => $user->role]);

            return response()->json([
                'status' => 'success',
                'message' => 'Connexion réussie.',
                'data' => [
                    'user' => $user->makeVisible(['email', 'telephone']),
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => config('jwt.ttl') * 60,
                ]
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la connexion', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la connexion.'
            ], 500);
        }
    }

    /**
     * Vérification du code d'authentification à deux facteurs
     *
     * @param TwoFactorRequest $request
     * @return JsonResponse
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
            
            // Vérifier le code
            if (!$this->twoFactorService->verifyCode($user, $request->two_factor_code)) {
                return $this->errorResponse('Code d\'authentification invalide ou expiré.', 422);
            }
            
            // Nettoyer la session
            session()->forget(['2fa:user_id', '2fa:token']);
            
            // Mettre à jour la dernière connexion
            $user->update(['last_login_at' => now()]);
            
            return $this->successResponse([
                'user' => $user->makeVisible(['email', 'telephone']),
                'access_token' => $tempToken,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ], 'Authentification réussie.');
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la vérification 2FA: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la vérification.', 500);
        }
    }

    /**
     * Déconnexion de l'utilisateur
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            // Récupérer l'utilisateur avant déconnexion pour les logs
            $user = auth()->user();
            
            // 1. Déconnecter de la session web
            Auth::guard('web')->logout();
            
            // 2. Invalider le token JWT
            $token = JWTAuth::getToken();
            if ($token) {
                JWTAuth::invalidate($token);
            }
            
            // 3. Nettoyer la session
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            Log::info('Déconnexion réussie', ['user_id' => $user?->id, 'email' => $user?->email]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Déconnexion réussie.'
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la déconnexion', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la déconnexion.'
            ], 500);
        }
    }

    /**
     * Rafraîchir le token JWT
     *
     * @param Request $request
     * @return JsonResponse
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
     * Récupérer le profil de l'utilisateur connecté
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Charger les relations
        $user->load(['elevages' => function($query) {
            $query->withCount(['animaux']);
        }]);
        
        // Statistiques additionnelles
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
     *
     * @param UpdateProfileRequest $request
     * @return JsonResponse
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            /** @var User $user */
            $user = Auth::user();
            
            $updateData = $request->validated();
            
            // Gérer l'upload de la nouvelle photo
            if ($request->hasFile('photo')) {
                // Supprimer l'ancienne photo si elle existe et n'est pas l'avatar par défaut
                if ($user->photo_url && !str_contains($user->photo_url, 'ui-avatars.com')) {
                    $oldPath = str_replace('/storage/', '', $user->photo_url);
                    Storage::disk('public')->delete($oldPath);
                }
                
                $photoPath = $this->uploadProfilePhoto($request->file('photo'));
                $updateData['photo_url'] = $photoPath;
            }
            
            // Supprimer 'photo' du tableau car ce n'est pas une colonne
            unset($updateData['photo']);
            
            // Mettre à jour l'utilisateur
            $user->update($updateData);
            
            DB::commit();
            
            return $this->successResponse(
                $user->fresh()->makeVisible(['email', 'telephone']),
                'Profil mis à jour avec succès.'
            );
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la mise à jour du profil: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la mise à jour du profil.', 500);
        }
    }

    /**
     * Changement du mot de passe
     *
     * @param ChangePasswordRequest $request
     * @return JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
{
    try {
        /** @var User $user */
        $user = Auth::user();
        
        Log::info('Tentative de changement de mot de passe', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);
        
        // ✅ Utiliser update() pour que le mutateur soit appelé
        $user->update([
            'password' => $request->new_password
        ]);
        
        Log::info('Mot de passe modifié avec succès', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);
        
        // 🔐Déconnecter l'utilisateur après changement de mot de passe
        // pour qu'il se reconnecte avec le nouveau mot de passe
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return $this->successResponse(null, 'Mot de passe modifié avec succès. Veuillez vous reconnecter avec votre nouveau mot de passe.');
        
    } catch (\Exception $e) {
        Log::error('Erreur lors du changement de mot de passe', [
            'user_id' => auth()->id(),
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return $this->errorResponse('Erreur lors du changement de mot de passe.', 500);
    }
}


    /**
     * Activation/Désactivation du 2FA
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function toggleTwoFactor(Request $request): JsonResponse
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            
            if ($user->two_factor_enabled) {
                $this->twoFactorService->disableTwoFactor($user);
                $message = 'Authentification à deux facteurs désactivée.';
            } else {
                // Générer un secret pour l'application d'authentification
                // Pour simplifier, on active directement avec validation par email
                $this->twoFactorService->enableTwoFactor($user, 'email_based');
                $message = 'Authentification à deux facteurs activée.';
            }
            
            return $this->successResponse([
                'two_factor_enabled' => $user->fresh()->two_factor_enabled,
            ], $message);
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors du basculement 2FA: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la modification du 2FA.', 500);
        }
    }

    /**
     * Mise à jour des préférences de notification
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateNotificationPreferences(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email_notifications' => 'sometimes|boolean',
            'web_notifications' => 'sometimes|boolean',
            'reminder_notifications' => 'sometimes|boolean',
            'newsletter_subscription' => 'sometimes|boolean',
        ]);
        
        try {
            /** @var User $user */
            $user = Auth::user();
            $user->update($validated);
            
            return $this->successResponse([
                'email_notifications' => $user->email_notifications,
                'web_notifications' => $user->web_notifications,
                'reminder_notifications' => $user->reminder_notifications,
                'newsletter_subscription' => $user->newsletter_subscription,
            ], 'Préférences de notification mises à jour.');
            
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la mise à jour des préférences.', 500);
        }
    }

    /**
     * Mise à jour de la visibilité du profil
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateProfileVisibility(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'profile_visibility' => 'required|in:public,prive',
        ]);
        
        try {
            /** @var User $user */
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
     * Suppression du compte utilisateur
     *
     * @param DeleteAccountRequest $request
     * @return JsonResponse
     */
    public function deleteAccount(DeleteAccountRequest $request): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            /** @var User $user */
            $user = Auth::user();
            
            // Supprimer la photo de profil si elle n'est pas l'avatar par défaut
            if ($user->photo_url && !str_contains($user->photo_url, 'ui-avatars.com')) {
                $photoPath = str_replace('/storage/', '', $user->photo_url);
                Storage::disk('public')->delete($photoPath);
            }
            
            // Supprimer l'utilisateur (les relations seront supprimées via CASCADE)
            $user->delete();
            
            DB::commit();
            
            // Déconnecter l'utilisateur
            Auth::logout();
            
            return $this->successResponse(null, 'Votre compte a été supprimé avec succès. Au revoir !');
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la suppression du compte: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la suppression du compte.', 500);
        }
    }

    /**
     * Profil public d'un utilisateur (sans authentification)
     *
     * @param int $id
     * @return JsonResponse
     */
    public function publicProfile(int $id): JsonResponse
    {
        try {
            $user = User::where('status', 'active')
                ->withCount(['publications', 'publications as likes_received' => function($query) {
                    $query->select(DB::raw('COALESCE(SUM(nbr_likes), 0)'));
                }])
                ->findOrFail($id);
            
            // Vérifier la visibilité du profil
            if (!$user->isProfilePublic() && !Auth::check()) {
                return $this->errorResponse('Ce profil est privé.', 403);
            }
            
            // Récupérer les dernières publications
            $recentPosts = $user->publications()
                ->where('etat', 'publiee')
                ->latest()
                ->limit(10)
                ->get();
            
            // Récupérer les élevages
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
                    'total_farms' => $user->elevages->count(),
                    'total_animals' => $farms->sum('animaux_count'),
                ],
                'recent_publications' => $recentPosts,
                'farms' => $farms,
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Utilisateur non trouvé.');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération du profil public: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la récupération du profil.', 500);
        }
    }

    // ========== MÉTHODES PRIVÉES ==========

    /**
     * Upload et traitement de la photo de profil
     *
     * @param \Illuminate\Http\UploadedFile $photo
     * @return string Chemin relatif de la photo stockée
     */
    private function uploadProfilePhoto($photo): string
    {
        // Générer un nom unique
        $filename = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
        $path = 'avatars/' . $filename;
        
        // Traiter et redimensionner l'image avec Intervention Image
        $manager = new ImageManager(new Driver());
        $image = $manager->read($photo->getPathname());
        
        // Redimensionner à 300x300 (carré)
        $image->cover(300, 300);
        
        // Sauvegarder
        Storage::disk('public')->put($path, (string) $image->encode());
        
        return $path;
    }

    /**
     * Crée un élevage par défaut pour le nouvel utilisateur
     *
     * @param User $user
     * @param string $typeElevage
     * @return void
     */
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
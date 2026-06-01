<?php
// app/Http/Controllers/Api/AuthController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Journaliser l'erreur pour le débogage
            \Log::error('Erreur lors de l\'inscription: ' . $e->getMessage());
            
            return $this->errorResponse(
                'Une erreur est survenue lors de l\'inscription. Veuillez réessayer.',
                500
            );
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
        try {
            // Tentative d'authentification
            $credentials = $request->only('email', 'password');
            
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->unauthorizedResponse('Email ou mot de passe incorrect.');
            }
            
            /** @var User $user */
            $user = Auth::user();
            
            // Vérifier si l'utilisateur est banni
            if ($user->isBanned()) {
                Auth::logout();
                return $this->forbiddenResponse('Votre compte a été banni. Veuillez contacter l\'administrateur.');
            }
            
            // Vérifier si l'email est vérifié
            // if (!$user->email_verified_at) {
            //     Auth::logout();
            //     return $this->unauthorizedResponse('Veuillez vérifier votre email avant de vous connecter.');
            // }
            
            // Gérer l'authentification à deux facteurs
            if ($user->two_factor_enabled) {
                // Générer et envoyer un code 2FA
                $code = $this->twoFactorService->generateCode($user);
                
                // Stocker le token temporairement pour la validation 2FA
                // Nous pourrions utiliser une session ou un cache
                session(['2fa:user_id' => $user->id, '2fa:token' => $token]);
                
                return $this->successResponse([
                    'requires_two_factor' => true,
                    'user_id' => $user->id,
                ], 'Code d\'authentification envoyé par email.', 200);
            }
            
            // Mettre à jour la dernière connexion
            $user->update(['last_login_at' => now()]);
            
            return $this->successResponse([
                'user' => $user->makeVisible(['email', 'telephone']),
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ], 'Connexion réussie.');
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la connexion: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la connexion. Veuillez réessayer.', 500);
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
            Auth::logout();
            return $this->successResponse(null, 'Déconnexion réussie.');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la déconnexion.', 500);
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
            'total_animals' => $user->elevages->sum('animaux_count'),
            'total_publications' => $user->publications()->count(),
            'total_likes_received' => $user->publications()->sum('nbr_likes'),
            'pending_tasks' => DB::table('taches')
                ->join('animals', 'taches.animal_id', '=', 'animals.id')
                ->join('elevages', 'animals.elevage_id', '=', 'elevages.id')
                ->where('elevages.user_id', $user->id)
                ->where('taches.terminee', false)
                ->where('taches.date_planifiee', '<=', now())
                ->count(),
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
            
            $user->update([
                'password' => $request->new_password,
            ]);
            
            // Optionnel: Déconnecter l'utilisateur et demander reconnexion
            // Auth::logout();
            
            return $this->successResponse(null, 'Mot de passe modifié avec succès.');
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors du changement de mot de passe: ' . $e->getMessage());
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
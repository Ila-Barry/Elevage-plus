<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Notification;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Hash;
use App\Notifications\TwoFactorCodeNotification;

/**
 * Modèle User (Éleveur/Admin)
 * 
 * Représente un utilisateur de la plateforme Élevage+
 * Gère l'authentification, les rôles, les préférences et les relations.
 */
class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'telephone',
        'password',
        'bio',
        'photo_url',
        'role',
        'status',
        'profile_visibility',      // public, prive
        'email_notifications',      // boolean
        'web_notifications',        // boolean
        'reminder_notifications',   // boolean
        'newsletter_subscription',  // boolean
        'two_factor_enabled',       // boolean
        'two_factor_secret',
        'email_verified_at',
    ];

    /**
     * Les attributs qui doivent être cachés pour la sérialisation.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'profile_visibility' => 'string',
        'email_notifications' => 'boolean',
        'web_notifications' => 'boolean',
        'reminder_notifications' => 'boolean',
        'newsletter_subscription' => 'boolean',
        'two_factor_enabled' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Les valeurs par défaut des attributs.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'role' => 'user',
        'status' => 'inactive',
        'profile_visibility' => 'public',
        'email_notifications' => true,
        'web_notifications' => true,
        'reminder_notifications' => true,
        'newsletter_subscription' => false,
        'two_factor_enabled' => false,
    ];

    /**
     * Vérifie si l'utilisateur est actif
     */
    // public function isActive(): bool
    // {
    //     return $this->status === 'active' && !is_null($this->email_verified_at);
    // }

    // ========== RELATIONS ==========
    
    /**
     * Relation avec les élevages
     * Un éleveur peut avoir plusieurs élevages
     */
    public function elevages()
    {
        return $this->hasMany(Elevage::class);
    }

    /**
     * Relation avec les publications
     * Un utilisateur peut avoir plusieurs publications
     */
    public function publications()
    {
        return $this->hasMany(Publication::class);
    }

    /**
     * Relation avec les commentaires
     */
    public function commentaires()
    {
        return $this->hasMany(Commentaire::class);
    }

    /**
     * Relation avec les likes
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /**
     * Messages envoyés par l'utilisateur
     */
    public function messagesEnvoyes()
    {
        return $this->hasMany(Message::class, 'expediteur_id');
    }

    /**
     * Messages reçus par l'utilisateur
     */
    public function messagesRecus()
    {
        return $this->hasMany(Message::class, 'destinataire_id');
    }

    /**
     * Conversations où l'utilisateur est participant
     */
    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_user');
    }

    /**
     * Codes d'authentification à deux facteurs
     */
    public function twoFactorCodes()
    {
        return $this->hasMany(TwoFactorCode::class);
    }

    /**
     * Envoyer l'email de vérification avec redirection vers le frontend
     * Cette méthode surcharge celle de Laravel
     */
    public function sendEmailVerificationNotification()
    {
        // Envoyer immédiatement la notification de vérification
        // Utilise sendNow pour ne pas dépendre d'un worker de queue en développement
        Notification::sendNow($this, new \App\Notifications\CustomVerifyEmailNotification());
    }

    // ========== JWT METHODS ==========

    /**
     * Récupère l'identifiant qui sera stocké dans le JWT
     * Requis par l'interface JWTSubject
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Récupère les claims personnalisés pour le JWT
     * Requis par l'interface JWTSubject
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role,
            'user_id' => $this->id,
        ];
    }

    // ========== ACCESSORS & MUTATORS ==========

    /**
     * Hash automatique du mot de passe à la création/modification
     */
    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn(string $value) => Hash::make($value),
        );
    }

    /**
     * Accesseur pour l'URL complète de la photo de profil
     */
    protected function photoUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$value) {
                    return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=4F46E5&color=fff';
                }
                
                if (filter_var($value, FILTER_VALIDATE_URL)) {
                    return $value;
                }
                
                return asset('storage/' . $value);
            }
        );
    }

    /**
     * Vérifie si l'utilisateur est admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Vérifie si l'utilisateur est actif (non banni)
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Vérifie si l'utilisateur est banni
     */
    public function isBanned(): bool
    {
        return $this->status === 'bannie';
    }

    /**
     * Vérifie si le profil est public
     */
    public function isProfilePublic(): bool
    {
        return $this->profile_visibility === 'public';
    }

    /**
     * Active ou désactive l'authentification à deux facteurs
     */
    public function setTwoFactorEnabled(bool $enabled): void
    {
        $this->two_factor_enabled = $enabled;
        $this->save();
    }

    /**
     * Génère et envoie un code 2FA
     */
    public function generateTwoFactorCode(): string
    {
        // Supprimer les anciens codes
        $this->twoFactorCodes()->delete();
        
        // Générer un nouveau code aléatoire à 6 chiffres
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Créer le code avec expiration (10 minutes)
        $this->twoFactorCodes()->create([
            'code' => Hash::make($code),
            'expires_at' => now()->addMinutes(config('auth.two_factor_expiry', 10)),
        ]);
        
        // Envoyer le code par email
        $this->notify(new TwoFactorCodeNotification($code));
        
        return $code;
    }

    /**
     * Vérifie si un code 2FA est valide
     */
    public function verifyTwoFactorCode(string $code): bool
    {
        $record = $this->twoFactorCodes()
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
            
        if (!$record) {
            return false;
        }
        
        $valid = Hash::check($code, $record->code);
        
        if ($valid) {
            $record->delete();
        }
        
        return $valid;
    }

    /**
     * Scope pour les utilisateurs actifs
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope pour les éleveurs (non admins)
     */
    public function scopeFarmers($query)
    {
        return $query->where('role', 'user');
    }

    /**
     * Scope pour les admins
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope pour les profils publics
     */
    public function scopePublicProfiles($query)
    {
        return $query->where('profile_visibility', 'public');
    }

        /**
        * Règles de validation pour la création d'un utilisateur
        */
    public static function getValidationRules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'telephone' => 'required|string|unique:users|regex:/^(\+221|00221)?(77|78|70|76|75)[0-9]{7}$/',
            'password' => 'required|string|min:6|confirmed',
            'type_elevage' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'photo' => 'nullable|image|max:5120', // 5MB max
        ];
    }
}


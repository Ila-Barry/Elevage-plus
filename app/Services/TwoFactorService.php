<?php
// app/Services/TwoFactorService.php

namespace App\Services;

use App\Models\User;
use App\Models\TwoFactorCode;
use Illuminate\Support\Facades\Hash;

/**
 * Service TwoFactorService
 * 
 * Gère toute la logique d'authentification à deux facteurs (2FA)
 */
class TwoFactorService
{
    /**
     * Génère un nouveau code 2FA pour un utilisateur
     *
     * @param User $user
     * @return string Le code généré (non hashé pour envoi email)
     */
    public function generateCode(User $user): string
    {
        // Supprimer tous les anciens codes expirés ou non utilisés
        $user->twoFactorCodes()->delete();
        
        // Générer un code aléatoire à 6 chiffres
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Stocker le code hashé avec expiration
        $user->twoFactorCodes()->create([
            'code' => Hash::make($code),
            'expires_at' => now()->addMinutes(config('auth.two_factor_expiry', 10)),
        ]);
        
        return $code;
    }
    
    /**
     * Vérifie la validité d'un code 2FA
     *
     * @param User $user
     * @param string $code
     * @return bool
     */
    public function verifyCode(User $user, string $code): bool
    {
        $record = $user->twoFactorCodes()
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
            
        if (!$record) {
            return false;
        }
        
        $valid = Hash::check($code, $record->code);
        
        // Supprimer le code après utilisation (one-time use)
        if ($valid) {
            $record->delete();
        }
        
        return $valid;
    }
    
    /**
     * Active le 2FA pour un utilisateur
     *
     * @param User $user
     * @param string $secret
     * @return void
     */
    public function enableTwoFactor(User $user, string $secret): void
    {
        $user->update([
            'two_factor_enabled' => true,
            'two_factor_secret' => $secret,
        ]);
    }
    
    /**
     * Désactive le 2FA pour un utilisateur
     *
     * @param User $user
     * @return void
     */
    public function disableTwoFactor(User $user): void
    {
        $user->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
        ]);
        
        // Supprimer tous les codes existants
        $user->twoFactorCodes()->delete();
    }
    
    /**
     * Vérifie si l'utilisateur a le 2FA activé
     *
     * @param User $user
     * @return bool
     */
    public function isEnabled(User $user): bool
    {
        return (bool) $user->two_factor_enabled;
    }
    
    /**
     * Nettoie les codes expirés de tous les utilisateurs
     * À exécuter périodiquement (cron job)
     *
     * @return int Nombre de codes supprimés
     */
    public function cleanExpiredCodes(): int
    {
        return TwoFactorCode::where('expires_at', '<', now())->delete();
    }
}
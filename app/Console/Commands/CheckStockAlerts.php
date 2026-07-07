<?php
// app/Console/Commands/CheckStockAlerts.php

namespace App\Console\Commands;

use App\Models\Produit;
use App\Models\User;
use App\Notifications\StockNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckStockAlerts extends Command
{
    protected $signature = 'stock:check-alerts';
    protected $description = 'Vérifie les alertes de stock (critique, rupture, expiration)';

    public function handle()
    {
        $this->info('🔍 Vérification des alertes de stock...');
        
        $produitsCritiques = Produit::critique()->with('elevage.user')->get();
        $produitsRupture = Produit::rupture()->with('elevage.user')->get();
        $produitsExpiration = Produit::expirationProche(30)->with('elevage.user')->get();
        
        $count = 0;
        
        // Alertes de rupture
        foreach ($produitsRupture as $produit) {
            $user = $produit->elevage?->user;
            if ($user) {
                try {
                    $user->notify(new StockNotification($produit, 'stock_rupture'));
                    $count++;
                    $this->info("🚨 Rupture: {$produit->nom}");
                } catch (\Exception $e) {
                    $this->error("❌ Erreur pour {$produit->nom}: " . $e->getMessage());
                }
            }
        }
        
        // Alertes critiques
        foreach ($produitsCritiques as $produit) {
            $user = $produit->elevage?->user;
            if ($user) {
                try {
                    $user->notify(new StockNotification($produit, 'stock_critique'));
                    $count++;
                    $this->info("⚠️ Critique: {$produit->nom}");
                } catch (\Exception $e) {
                    $this->error("❌ Erreur pour {$produit->nom}: " . $e->getMessage());
                }
            }
        }
        
        // Alertes d'expiration
        foreach ($produitsExpiration as $produit) {
            $user = $produit->elevage?->user;
            if ($user) {
                $jours = now()->diffInDays($produit->date_expiration);
                try {
                    $user->notify(new StockNotification($produit, 'stock_expiration', null, [
                        'jours' => $jours
                    ]));
                    $count++;
                    $this->info("📅 Expiration: {$produit->nom} ({$jours} jours)");
                } catch (\Exception $e) {
                    $this->error("❌ Erreur pour {$produit->nom}: " . $e->getMessage());
                }
            }
        }
        
        Log::info('Vérification des alertes de stock terminée', [
            'produits_rupture' => $produitsRupture->count(),
            'produits_critiques' => $produitsCritiques->count(),
            'produits_expiration' => $produitsExpiration->count(),
            'notifications_envoyees' => $count
        ]);
        
        $this->info("✅ Vérification terminée: {$count} notifications envoyées.");
        
        return 0;
    }
}
<?php
// app/Console/Commands/CheckStockAlerts.php

namespace App\Console\Commands;

use App\Models\Produit;
use App\Events\StockLow;
use Illuminate\Console\Command;

class CheckStockAlerts extends Command
{
    protected $signature = 'alerts:check-stock';
    protected $description = 'Vérifie et envoie les alertes de stock critique';

    public function handle(): int
    {
        $this->info('Vérification des stocks critiques...');
        
        $count = 0;
        
        // Récupérer les produits en stock critique
        $produits = Produit::critique()
            ->whereDoesntHave('notifications', function ($query) {
                $query->where('created_at', '>', now()->subDay());
            })
            ->get();
        
        foreach ($produits as $produit) {
            event(new StockLow($produit));
            $count++;
            $this->line("Alerte stock critique envoyée pour: {$produit->nom}");
        }
        
        $this->info("✅ {$count} alerte(s) stock critique envoyée(s).");
        
        return Command::SUCCESS;
    }
}
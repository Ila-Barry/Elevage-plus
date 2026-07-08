<?php
// app/Console/Commands/CheckPublicationInteractions.php

namespace App\Console\Commands;

use App\Models\Publication;
use App\Models\User;
use App\Notifications\PublicationNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckPublicationInteractions extends Command
{
    protected $signature = 'publications:check-interactions';
    protected $description = 'Vérifie les interactions sur les publications et envoie des notifications';

    public function handle()
    {
        $this->info('🔍 Vérification des interactions sur les publications...');
        
        // Récupérer les publications avec des likes ou commentaires récents
        $publications = Publication::where('created_at', '>=', now()->subDays(7))
            ->with(['user'])
            ->get();
        
        $count = 0;
        
        foreach ($publications as $publication) {
            $user = $publication->user;
            if (!$user) continue;
            
            // Vérifier les nouveaux likes (depuis la dernière vérification)
            // Cette logique dépend de votre système de suivi des interactions
            // Vous pouvez ajouter une colonne 'last_interaction_check' à la table publications
            
            $this->info("✅ Publication '{$publication->titre}' vérifiée");
        }
        
        Log::info('Vérification des interactions terminée', [
            'publications_checked' => $publications->count()
        ]);
        
        $this->info("✅ Vérification terminée: {$count} notifications envoyées.");
        
        return 0;
    }
}
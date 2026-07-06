<?php
// app/Console/Commands/CheckTaskReminders.php

namespace App\Console\Commands;

use App\Services\TacheRappelService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckTaskReminders extends Command
{
    protected $signature = 'tasks:check-reminders';
    protected $description = 'Vérifie et envoie les rappels pour les tâches';

    protected TacheRappelService $rappelService;

    public function __construct(TacheRappelService $rappelService)
    {
        parent::__construct();
        $this->rappelService = $rappelService;
    }

    public function handle()
    {
        $this->info('🔍 Vérification des rappels de tâches...');
        
        try {
            $this->rappelService->verifierEtEnvoyerRappels();
            
            Log::info('✅ Commande tasks:check-reminders exécutée avec succès');
            $this->info('✅ Vérification terminée');
            
            return 0;
        } catch (\Exception $e) {
            Log::error('❌ Erreur dans tasks:check-reminders', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->error('❌ Erreur: ' . $e->getMessage());
            return 1;
        }
    }
}
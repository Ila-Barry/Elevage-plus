<?php
// app/Console/Commands/SendTacheRappels.php

namespace App\Console\Commands;

use App\Services\RappelService;
use Illuminate\Console\Command;

/**
 * Commande pour envoyer les rappels de tâches
 * À exécuter toutes les minutes via cron
 * 
 * php artisan tache:send-rappels
 */
class SendTacheRappels extends Command
{
    protected $signature = 'tache:send-rappels';
    protected $description = 'Envoie les rappels de tâches programmés';

    protected RappelService $rappelService;

    public function __construct(RappelService $rappelService)
    {
        parent::__construct();
        $this->rappelService = $rappelService;
    }

    public function handle(): int
    {
        $this->info('Début de l\'envoi des rappels...');
        
        $stats = $this->rappelService->envoyerRappelsEnAttente();
        
        $this->info("Rappels envoyés: {$stats['envoyes']}");
        $this->info("Rappels échoués: {$stats['echoues']}");
        
        if ($stats['total'] === 0) {
            $this->info('Aucun rappel à envoyer.');
        }
        
        return Command::SUCCESS;
    }
}
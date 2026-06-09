<?php
// ici on gere les rappels de taches, on declenche cet event lorsqu'une tache a besoin d'un rappel, ensuite on a un listener qui ecoute cet event et qui envoie la notification de rappel a l'utilisateur concerné.
namespace App\Console\Commands;

use App\Models\Tache;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Command SendTaskReminders
 * 
 * Exécute la vérification des rappels de tâches
 * À planifier avec cron: php artisan task:send-reminders
 */
class SendTaskReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:send-reminders
                           {--force : Force l\'envoi des rappels même si déjà envoyés}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoie les rappels automatiques pour les tâches programmées';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Vérification des rappels de tâches...');
        
        $force = $this->option('force');
        $count = 0;
        
        // Récupérer les tâches non terminées
        $taches = Tache::where('terminee', false)->get();
        
        foreach ($taches as $tache) {
            $remindersToSend = $tache->getRemindersToSend();
            
            if (!empty($remindersToSend)) {
                // Déclencher l'event pour chaque rappel
                foreach ($remindersToSend as $reminderType) {
                    $message = $tache->getReminderMessage($reminderType);
                    event(new \App\Events\TaskReminderNeeded($tache, $reminderType, $message));
                    
                    // Mettre à jour le dernier rappel envoyé
                    $tache->update([
                        'last_reminder_type' => $reminderType,
                        'last_reminder_sent_at' => now(),
                    ]);
                    
                    // Incrémenter le compteur pour les rappels de retard
                    if ($reminderType === 'retard') {
                        $tache->increment('retard_reminder_count');
                    }
                    
                    $count++;
                    $this->line("Rappel envoyé pour: {$tache->titre} ({$reminderType})");
                }
            }
        }
        
        $this->info("✅ {$count} rappel(s) envoyé(s) avec succès.");
        
        return Command::SUCCESS;
    }
}
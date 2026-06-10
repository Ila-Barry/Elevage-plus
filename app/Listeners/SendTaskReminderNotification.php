<?php

// ici on gere les rappels de taches, on declenche cet event lorsqu'une tache a besoin d'un rappel, ensuite on a un listener qui ecoute cet event et qui envoie la notification de rappel a l'utilisateur concerné.
// app/Listeners/SendTaskReminderNotification.php

namespace App\Listeners;

use App\Events\TaskReminderNeeded;
use App\Notifications\TaskReminderNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTaskReminderNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(TaskReminderNeeded $event): void
    {
        $tache = $event->tache;
        $user = $tache->user;
        
        // Envoyer la notification avec le message personnalisé
        $user->notify(new TaskReminderNotification($tache, $event->reminderType, $event->message));
    }
}
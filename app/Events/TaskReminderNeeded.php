<?php

// ici on gere les rappels de taches, on declenche cet event lorsqu'une tache a besoin d'un rappel, ensuite on a un listener qui ecoute cet event et qui envoie la notification de rappel a l'utilisateur concerné.
// app/Events/TaskReminderNeeded.php

namespace App\Events;

use App\Models\Tache;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskReminderNeeded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Tache $tache;
    public string $reminderType;
    public string $message;

    public function __construct(Tache $tache, string $reminderType, string $message)
    {
        $this->tache = $tache;
        $this->reminderType = $reminderType;
        $this->message = $message;
    }
}
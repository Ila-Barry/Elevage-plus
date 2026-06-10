<?php
// app/Notifications/VaccinationReminderNotification.php

namespace App\Notifications;

use App\Models\Animal;
use App\Models\Tache;

class VaccinationReminderNotification extends BaseNotification
{
    protected Animal $animal;
    protected Tache $tache;

    public function __construct(Animal $animal, Tache $tache)
    {
        $this->animal = $animal;
        $this->tache = $tache;
        
        $this->title = '💉 Rappel de vaccination';
        $this->message = "L'animal '{$animal->nom}' doit être vacciné le " . 
                         $tache->date_planifiee->format('d/m/Y');
        $this->type = 'warning';
        $this->url = "/animaux/{$animal->id}";
    }
}
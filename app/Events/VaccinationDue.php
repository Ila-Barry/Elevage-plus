<?php
// app/Events/VaccinationDue.php

namespace App\Events;

use App\Models\Animal;
use App\Models\Tache;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VaccinationDue
{
    use Dispatchable, SerializesModels;

    public Animal $animal;
    public Tache $tache;

    public function __construct(Animal $animal, Tache $tache)
    {
        $this->animal = $animal;
        $this->tache = $tache;
    }
}
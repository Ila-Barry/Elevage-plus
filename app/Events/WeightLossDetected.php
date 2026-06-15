<?php
// app/Events/WeightLossDetected.php

namespace App\Events;

use App\Models\Animal;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WeightLossDetected
{
    use Dispatchable, SerializesModels;

    public Animal $animal;
    public float $poidsAvant;
    public float $poidsApres;

    public function __construct(Animal $animal, float $poidsAvant, float $poidsApres)
    {
        $this->animal = $animal;
        $this->poidsAvant = $poidsAvant;
        $this->poidsApres = $poidsApres;
    }
}
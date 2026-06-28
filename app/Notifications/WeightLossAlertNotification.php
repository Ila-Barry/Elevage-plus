<?php

// notification du perte de poids d'un animal, déclenchée par le système de suivi de poids
namespace App\Notifications;

use App\Models\Animal;

class WeightLossAlertNotification extends BaseNotification
{
    protected Animal $animal;
    protected float $perte;
    protected float $poids_avant;
    protected float $poids_apres;

    public function __construct(Animal $animal, float $poids_avant, float $poids_apres)
    {
        $this->animal = $animal;
        $this->poids_avant = $poids_avant;
        $this->poids_apres = $poids_apres;
        $this->perte = (($poids_avant - $poids_apres) / $poids_avant) * 100;
        
        $this->title = '⚠️ Alerte perte de poids';
        $this->message = "L'animal '{$animal->nom}' a perdu {$this->perte}% de son poids " .
                         "({$poids_avant}kg -> {$poids_apres}kg). Une attention vétérinaire est recommandée.";
        $this->type = 'danger';
        $this->url = "/animaux/{$animal->id}";
    }
}
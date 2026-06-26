<?php
// app/Notifications/WeightLossAlertNotification.php

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
        $this->perte = round((($poids_avant - $poids_apres) / $poids_avant) * 100, 1);
        
        $this->title = '⚠️ Alerte perte de poids';
        $this->message = "L'animal '{$animal->nom}' a perdu {$this->perte}% de son poids " .
                         "({$poids_avant}kg -> {$poids_apres}kg). Une attention vétérinaire est recommandée.";
        $this->type = 'danger';
        $this->url = "/animaux/{$animal->id}";
        
        $this->actions = [
            [
                'label' => 'Voir l\'animal',
                'url' => "/animaux/{$animal->id}",
                'type' => 'danger'
            ],
            [
                'label' => 'Contacter un vétérinaire',
                'url' => "/veterinaires",
                'type' => 'primary'
            ],
            [
                'label' => 'Enregistrer un nouveau poids',
                'url' => "/animaux/{$animal->id}/poids",
                'type' => 'secondary'
            ]
        ];
    }

    protected function getAdditionalInfo(): string
    {
        return "📉 Perte de poids : {$this->perte}% sur 15 jours | Seuil critique : 10%";
    }
}
<?php
// app/Http/Resources/AnimalResource.php (version simplifiée)

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnimalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'elevage_id' => $this->elevage_id,
            'nom' => $this->nom,
            'espece' => $this->espece,
            'espece_label' => $this->espece_label,
            'race' => $this->race,
            'date_naissance' => $this->date_naissance?->format('Y-m-d'),
            'age' => $this->age,
            'poids' => (float) $this->poids,
            'statut_sanitaire' => $this->statut_sanitaire,
            'statut_sanitaire_label' => $this->statut_sanitaire_label,
            'statut' => $this->statut,
            'statut_label' => $this->statut_label,
            'sexe' => $this->sexe,
            'sexe_label' => $this->sexe_label,
            'couleur' => $this->couleur,
            'signes_particuliers' => $this->signes_particuliers,
            'numero_identification' => $this->numero_identification,
            'img_url' => $this->img_url,
            'date_deces' => $this->date_deces?->format('Y-m-d'),
            'motif_deces' => $this->motif_deces,
            'elevage' => [
                'id' => $this->elevage->id,
                'nom' => $this->elevage->nom,
            ],
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
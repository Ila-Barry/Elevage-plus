<?php
// app/Http/Resources/ElevageResource.php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ElevageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'type_elevage' => $this->type_elevage,
            'type_elevage_label' => $this->type_elevage_label,
            'localisation' => $this->localisation,
            'superficie' => (float) $this->superficie,
            'description' => $this->description,
            'statut' => $this->statut,
            'statut_label' => $this->statut_label,
            'img_url' => $this->img_url,
            'proprietaire' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'statistiques' => [
                'total_animaux' => $this->animaux()->count(),
                'total_produits' => $this->produits()->count(),
            ],
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
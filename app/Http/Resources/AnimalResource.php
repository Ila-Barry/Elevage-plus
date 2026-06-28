<?php
// app/Http/Resources/AnimalResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\AnimalHistorique;


class AnimalResource extends JsonResource
{
    /**
     * Indique si la réponse doit inclure l'historique
     *
     * @var bool
     */
    protected bool $includeHistory = false;

    /**
     * Crée une instance avec historique inclus
     */
    public static function withHistory($resource)
    {
        $instance = parent::make($resource);
        $instance->includeHistory = true;
        return $instance;
    }

    public function toArray(Request $request): array
    {
        // Récupérer les tâches
        $tachesQuery = $this->taches();

        $data = [
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
            'statistiques' => [
                'total_taches' => $tachesQuery->count(),
                'taches_terminees' => $tachesQuery->where('terminee', true)->count(),
                'taches_en_attente' => $tachesQuery->where('terminee', false)->count(),
                'prochaines_taches' => $tachesQuery
                    ->where('terminee', false)
                    ->where('date_planifiee', '>=', now())
                    ->orderBy('date_planifiee')
                    ->limit(5)
                    ->get()
                    ->map(fn($tache) => [
                        'id' => $tache->id,
                        'type' => $tache->type,
                        'type_label' => $tache->type_label,
                        'type_icone' => $tache->type_icone,
                        'titre' => $tache->titre,
                        'date_planifiee' => $tache->date_planifiee->format('Y-m-d'),
                        'date_planifiee_humain' => $tache->date_planifiee->diffForHumans(),
                        'priorite' => $tache->priorite,
                        'priorite_label' => $tache->priorite_label,
                        'priorite_couleur' => $tache->priorite_couleur,
                    ]),
                'historique_taches' => $tachesQuery
                    ->latest('date_planifiee')
                    ->limit(10)
                    ->get()
                    ->map(fn($tache) => [
                        'id' => $tache->id,
                        'type' => $tache->type,
                        'type_label' => $tache->type_label,
                        'type_icone' => $tache->type_icone,
                        'titre' => $tache->titre,
                        'date_planifiee' => $tache->date_planifiee->format('d/m/Y'),
                        'terminee' => $tache->terminee,
                        'priorite' => $tache->priorite,
                        'priorite_label' => $tache->priorite_label,
                    ]),
            ],
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];

        // Inclure l'historique des modifications si demandé
        if ($this->includeHistory) {
            $historiques = $this->historiques()->with('user')->latest()->limit(50)->get();
            $data['historique'] = AnimalHistoryResource::collection($historiques);
        }

        return $data;
    }
}
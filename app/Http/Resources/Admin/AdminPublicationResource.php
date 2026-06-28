<?php
// app/Http/Resources/Admin/AdminPublicationResource.php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminPublicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titre' => $this->titre,
            'contenu' => $this->contenu,
            'categorie' => $this->categorie,
            'categorie_label' => $this->getCategorieLabel(),
            'statut' => $this->statut,
            'statut_label' => $this->getStatutLabel(),
            'statut_couleur' => $this->getStatutCouleur(),
            'image_url' => $this->image_url,
            'nbr_likes' => $this->nbr_likes,
            'nbr_commentaires' => $this->nbr_commentaires,
            'nbr_partages' => $this->nbr_partages,
            'nbr_vues' => $this->nbr_vues,
            'nbr_signalements' => $this->nbr_signalements,
            'raison_blocage' => $this->raison_blocage,
            'auteur' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'photo_url' => $this->user->photo_url,
            ],
            'signalements' => $this->when($this->statut === 'signalee' || $this->statut === 'bloquee', function () {
                return $this->reports()->with('user')->latest()->get()->map(function ($report) {
                    return [
                        'id' => $report->id,
                        'motif' => $report->motif,
                        'motif_label' => $report->motif_label ?? $report->motif,
                        'commentaire' => $report->commentaire,
                        'signale_par' => [
                            'id' => $report->user->id,
                            'name' => $report->user->name,
                            'email' => $report->user->email,
                        ],
                        'created_at' => $report->created_at->format('Y-m-d H:i:s'),
                    ];
                });
            }),
            'published_at' => $this->published_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    protected function getCategorieLabel(): string
    {
        return match($this->categorie) {
            'conseil' => '🌾 Conseil',
            'experience' => '💡 Expérience',
            'alerte' => '⚠️ Alerte',
            default => $this->categorie,
        };
    }

    protected function getStatutLabel(): string
    {
        return match($this->statut) {
            'publiee' => '✅ Publiée',
            'signalee' => '⚠️ Signalée',
            'bloquee' => '🔴 Bloquée',
            default => $this->statut,
        };
    }

    protected function getStatutCouleur(): string
    {
        return match($this->statut) {
            'publiee' => 'green',
            'signalee' => 'yellow',
            'bloquee' => 'red',
            default => 'gray',
        };
    }
}
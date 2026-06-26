<?php
// app/Http/Resources/Admin/AdminReportResource.php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'motif' => $this->motif,
            'motif_label' => $this->getMotifLabel(),
            'commentaire' => $this->commentaire,
            'statut' => $this->statut,
            'statut_label' => $this->getStatutLabel(),
            'statut_couleur' => $this->getStatutCouleur(),
            'contenu_signalé' => [
                'type' => 'publication',
                'id' => $this->publication->id,
                'titre' => $this->publication->titre,
                'contenu' => substr($this->publication->contenu, 0, 200) . (strlen($this->publication->contenu) > 200 ? '...' : ''),
                'auteur' => [
                    'id' => $this->publication->user->id,
                    'name' => $this->publication->user->name,
                    'email' => $this->publication->user->email,
                ],
                'statut' => $this->publication->statut,
                'url' => "/publications/{$this->publication->id}",
            ],
            'signale_par' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'historique_signalements' => $this->publication->reports()
                ->where('id', '!=', $this->id)
                ->with('user')
                ->get()
                ->map(function ($report) {
                    return [
                        'id' => $report->id,
                        'motif' => $report->motif,
                        'signale_par' => $report->user->name,
                        'created_at' => $report->created_at->format('Y-m-d H:i:s'),
                    ];
                }),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    protected function getMotifLabel(): string
    {
        $motifs = [
            'spam' => 'Spam / Publicité',
            'offensant' => 'Contenu offensant',
            'fausse_info' => 'Fausse information',
            'contenu_inapproprie' => 'Contenu inapproprié',
            'autre' => 'Autre motif',
        ];
        return $motifs[$this->motif] ?? $this->motif;
    }

    protected function getStatutLabel(): string
    {
        return match($this->statut) {
            'en_attente' => '⏳ En attente',
            'traite' => '✅ Traité',
            'ignore' => '➖ Ignoré',
            default => $this->statut,
        };
    }

    protected function getStatutCouleur(): string
    {
        return match($this->statut) {
            'en_attente' => 'yellow',
            'traite' => 'green',
            'ignore' => 'gray',
            default => 'gray',
        };
    }
}
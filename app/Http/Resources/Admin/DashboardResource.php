<?php
// app/Http/Resources/Admin/DashboardResource.php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'kpis' => $this['kpis'] ?? [],
            'evolution' => $this['evolution'] ?? [],
            'repartition' => $this['repartition'] ?? [],
            'activites_recentes' => $this['activites_recentes'] ?? [],
            'engagement' => $this['engagement'] ?? [],
        ];
    }
}
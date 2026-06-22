<?php
// app/Http/Resources/Admin/AdminUserCollection.php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Collection AdminUserCollection
 * 
 * Formate une collection d'utilisateurs pour l'admin
 */
class AdminUserCollection extends ResourceCollection
{
    /**
     * Métadonnées supplémentaires
     */
    protected array $meta = [];

    /**
     * Crée une nouvelle instance avec métadonnées
     */
    public static function make($resource, array $meta = [])
    {
        $instance = parent::make($resource);
        $instance->meta = $meta;
        return $instance;
    }

    /**
     * Transforme la collection en tableau.
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => AdminUserResource::collection($this->collection),
            'meta' => array_merge($this->getDefaultMeta(), $this->meta),
        ];
    }

    /**
     * Métadonnées par défaut
     */
    protected function getDefaultMeta(): array
    {
        return [
            'total_eleveurs' => \App\Models\User::where('role', 'eleveur')->count(),
            'total_admins' => \App\Models\User::where('role', 'admin')->count(),
            'total_visiteurs' => \App\Models\User::where('role', 'visiteur')->count(),
            'total_actifs' => \App\Models\User::where('status', 'active')->count(),
            'total_bannis' => \App\Models\User::where('status', 'bannie')->count(),
        ];
    }
}
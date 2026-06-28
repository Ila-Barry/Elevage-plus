<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['name', 'guard_name', 'description'];

    // Permissions prédéfinies
    public static function getDefaultPermissions(): array
    {
        return [
            // Gestion des utilisateurs
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'ban_users',
            
            // Gestion des publications
            'view_publications',
            'create_publications',
            'edit_publications',
            'delete_publications',
            'moderate_publications',
            
            // Gestion des élevages
            'view_farms',
            'create_farms',
            'edit_farms',
            'delete_farms',
            
            // Gestion des animaux
            'view_animals',
            'create_animals',
            'edit_animals',
            'delete_animals',
            
            // Gestion des stocks
            'view_stock',
            'manage_stock',
            
            // Administration
            'access_admin',
            'manage_users',
            'manage_reports',
            'manage_newsletter'
        ];
    }
}
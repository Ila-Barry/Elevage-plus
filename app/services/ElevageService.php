<?php
// app/Services/ElevageService.php

namespace App\Services;

use App\Models\Elevage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

/**
 * Service ElevageService
 * 
 * Contient toute la logique métier pour la gestion des élevages
 */
class ElevageService
{
    /**
     * Récupère tous les élevages avec pagination
     * 
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = Elevage::with(['proprietaire' => function($query) {
            $query->select('id', 'name', 'photo_url', 'profile_visibility');
        }]);
        
        // Application des filtres
        if (!empty($filters['type'])) {
            $query->ofType($filters['type']);
        }
        
        if (!empty($filters['localisation'])) {
            $query->locatedIn($filters['localisation']);
        }
        
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('nom', 'LIKE', '%' . $filters['search'] . '%')
                  ->orWhere('localisation', 'LIKE', '%' . $filters['search'] . '%');
            });
        }
        
        // Filtrer les élevages des utilisateurs au profil public pour les non-authentifiés
        if (!auth()->check()) {
            $query->whereHas('proprietaire', function($q) {
                $q->where('profile_visibility', 'public');
            });
        }
        
        return $query->latest()->paginate($perPage);
    }

    /**
     * Récupère les élevages d'un utilisateur spécifique
     * 
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserElevages(int $userId)
    {
        return Elevage::where('user_id', $userId)
            ->withCount('animaux')
            ->latest()
            ->get();
    }

    /**
     * Crée un nouvel élevage
     * 
     * @param array $data
     * @param \Illuminate\Http\UploadedFile|null $photo
     * @return Elevage
     */
    public function createElevage(array $data, $photo = null): Elevage
    {
        DB::beginTransaction();
        
        try {
            // Upload de la photo si présente
            if ($photo) {
                $data['img_url'] = $this->uploadElevagePhoto($photo);
            }
            
            // Création de l'élevage
            $elevage = Elevage::create($data);
            
            DB::commit();
            
            Log::info('Nouvel élevage créé', [
                'elevage_id' => $elevage->id,
                'user_id' => $elevage->user_id,
                'nom' => $elevage->nom
            ]);
            
            return $elevage->load('proprietaire');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création de l\'élevage: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Met à jour un élevage existant
     * 
     * @param Elevage $elevage
     * @param array $data
     * @param \Illuminate\Http\UploadedFile|null $photo
     * @return Elevage
     */
    public function updateElevage(Elevage $elevage, array $data, $photo = null): Elevage
    {
        DB::beginTransaction();
        
        try {
            // Gérer la nouvelle photo
            if ($photo) {
                // Supprimer l'ancienne photo
                if ($elevage->img_url) {
                    $this->deleteElevagePhoto($elevage->img_url);
                }
                $data['img_url'] = $this->uploadElevagePhoto($photo);
            }
            
            // Mise à jour
            $elevage->update($data);
            
            DB::commit();
            
            Log::info('Élevage mis à jour', [
                'elevage_id' => $elevage->id,
                'user_id' => $elevage->user_id
            ]);
            
            return $elevage->fresh('proprietaire');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour de l\'élevage: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Supprime un élevage (avec cascade)
     * 
     * @param Elevage $elevage
     * @return bool
     */
    public function deleteElevage(Elevage $elevage): bool
    {
        DB::beginTransaction();
        
        try {
            // Supprimer la photo associée
            if ($elevage->img_url) {
                $this->deleteElevagePhoto($elevage->img_url);
            }
            
            // La suppression cascade est gérée par la migration
            $result = $elevage->delete();
            
            DB::commit();
            
            Log::info('Élevage supprimé', [
                'elevage_id' => $elevage->id,
                'nom' => $elevage->nom
            ]);
            
            return $result;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression de l\'élevage: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Récupère les détails d'un élevage avec ses relations
     * 
     * @param Elevage $elevage
     * @return Elevage
     */
    public function getElevageDetails(Elevage $elevage): Elevage
    {
        return $elevage->load([
            'proprietaire' => function($query) {
                $query->select('id', 'name', 'email', 'photo_url', 'bio', 'profile_visibility');
            },
            'animaux' => function($query) {
                $query->latest()->limit(10);
            },
            'produits' => function($query) {
                $query->latest()->limit(10);
            }
        ]);
    }

    /**
     * Calcule le nombre total d'élevages d'un utilisateur
     * 
     * @param int $userId
     * @return int
     */
    public function getTotalElevagesCount(int $userId): int
    {
        return Elevage::where('user_id', $userId)->count();
    }

    /**
     * Upload de la photo d'élevage
     * 
     * @param \Illuminate\Http\UploadedFile $photo
     * @return string
     */
    private function uploadElevagePhoto($photo): string
    {
        // Générer un nom unique
        $filename = 'elevage_' . time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
        $path = 'elevages/' . $filename;
        
        // Traiter l'image avec Intervention Image
        $manager = new ImageManager(new Driver());
        $image = $manager->read($photo->getPathname());
        
        // Redimensionner pour optimiser (1200x800 max)
        $image->scale(width: 1200);
        
        // Sauvegarder
        Storage::disk('public')->put($path, (string) $image->encode());
        
        return $path;
    }

    /**
     * Supprime la photo d'élevage
     * 
     * @param string $photoUrl
     * @return bool
     */
    private function deleteElevagePhoto(string $photoUrl): bool
    {
        if (Storage::disk('public')->exists($photoUrl)) {
            return Storage::disk('public')->delete($photoUrl);
        }
        return false;
    }

    /**
     * Vérifie si l'utilisateur est propriétaire de l'élevage
     * 
     * @param Elevage $elevage
     * @param int|null $userId
     * @return bool
     */
    public function isOwner(Elevage $elevage, ?int $userId = null): bool
    {
        $userId = $userId ?? auth()->id();
        return $elevage->user_id === $userId;
    }
}
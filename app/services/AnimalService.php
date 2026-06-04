<?php
// app/Services/AnimalService.php

namespace App\Services;

use App\Models\Animal;
use App\Models\AnimalHistorique;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

/**
 * Service AnimalService
 * 
 * Contient toute la logique métier pour la gestion des animaux
 */
class AnimalService
{
    /**
     * Récupère tous les animaux avec pagination et filtres
     * 
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = Animal::with(['elevage' => function($query) {
            $query->select('id', 'nom', 'user_id')
                  ->with(['proprietaire' => function($q) {
                      $q->select('id', 'name', 'profile_visibility');
                  }]);
        }]);
        
        // Application des filtres
        if (!empty($filters['espece'])) {
            $query->ofEspece($filters['espece']);
        }
        
        if (!empty($filters['statut_sanitaire'])) {
            $query->withStatut($filters['statut_sanitaire']);
        }
        
        if (!empty($filters['age_min'])) {
            $query->ageBetween((int)$filters['age_min'], $filters['age_max'] ?? null);
        }
        
        if (!empty($filters['elevage_id'])) {
            $query->where('elevage_id', $filters['elevage_id']);
        }
        
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }
        
        // Filtrer les animaux des élevages privés pour les non-authentifiés
        if (!auth()->check()) {
            $query->whereHas('elevage.proprietaire', function($q) {
                $q->where('profile_visibility', 'public');
            });
        }
        
        return $query->latest()->paginate($perPage);
    }

    /**
     * Crée un nouvel animal
     * 
     * @param array $data
     * @param UploadedFile|null $photo
     * @return Animal
     */
    public function createAnimal(array $data, ?UploadedFile $photo = null): Animal
    {
        DB::beginTransaction();
        
        try {
            // Upload de la photo si présente
            if ($photo) {
                $data['img_url'] = $this->uploadAnimalPhoto($photo, $data['espece']);
            }
            
            // Création de l'animal
            $animal = Animal::create($data);
            
            // Enregistrement dans l'historique
            $this->logHistory($animal, 'create', null, $data);
            
            DB::commit();
            
            Log::info('Nouvel animal créé', [
                'animal_id' => $animal->id,
                'nom' => $animal->nom,
                'elevage_id' => $animal->elevage_id
            ]);
            
            return $animal->load(['elevage']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création de l\'animal: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Met à jour un animal
     * 
     * @param Animal $animal
     * @param array $data
     * @param UploadedFile|null $photo
     * @return Animal
     */
    public function updateAnimal(Animal $animal, array $data, ?UploadedFile $photo = null): Animal
    {
        DB::beginTransaction();
        
        try {
            // Sauvegarde des données avant modification pour l'historique
            $beforeData = $animal->toArray();
            
            // Gérer la nouvelle photo
            if ($photo) {
                if ($animal->img_url) {
                    $this->deleteAnimalPhoto($animal->img_url);
                }
                $data['img_url'] = $this->uploadAnimalPhoto($photo, $data['espece'] ?? $animal->espece);
            }
            
            // Mise à jour
            $animal->update($data);
            
            // Enregistrement dans l'historique
            $changedFields = $this->getChangedFields($beforeData, $animal->toArray());
            if (!empty($changedFields)) {
                $this->logHistory($animal, 'update', $beforeData, $animal->toArray(), $changedFields);
            }
            
            DB::commit();
            
            Log::info('Animal mis à jour', [
                'animal_id' => $animal->id,
                'changed_fields' => $changedFields
            ]);
            
            return $animal->fresh(['elevage']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour de l\'animal: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Supprime un animal
     * 
     * @param Animal $animal
     * @return bool
     */
    public function deleteAnimal(Animal $animal): bool
    {
        DB::beginTransaction();
        
        try {
            // Enregistrement dans l'historique avant suppression
            $this->logHistory($animal, 'delete', $animal->toArray(), null);
            
            // Supprimer la photo associée
            if ($animal->img_url) {
                $this->deleteAnimalPhoto($animal->img_url);
            }
            
            $result = $animal->delete();
            
            DB::commit();
            
            Log::info('Animal supprimé', [
                'animal_id' => $animal->id,
                'nom' => $animal->nom
            ]);
            
            return $result;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression de l\'animal: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Récupère les détails d'un animal avec ses relations
     * 
     * @param Animal $animal
     * @return Animal
     */
    public function getAnimalDetails(Animal $animal): Animal
    {
        return $animal->load([
            'elevage' => function($query) {
                $query->with(['proprietaire' => function($q) {
                    $q->select('id', 'name', 'email', 'photo_url', 'bio', 'profile_visibility');
                }]);
            },
            'historiques' => function($query) {
                $query->latest()->limit(20);
            }
        ]);
    }

    /**
     * Récupère les animaux d'un élevage spécifique
     * 
     * @param int $elevageId
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getElevageAnimals(int $elevageId, array $filters = [])
    {
        $query = Animal::where('elevage_id', $elevageId);
        
        if (!empty($filters['espece'])) {
            $query->ofEspece($filters['espece']);
        }
        
        if (!empty($filters['statut_sanitaire'])) {
            $query->withStatut($filters['statut_sanitaire']);
        }
        
        return $query->latest()->get();
    }

    /**
     * Calcule le nombre total d'animaux pour un élevage ou utilisateur
     * 
     * @param string $type
     * @param int $id
     * @return int
     */
    public function getTotalAnimalsCount(string $type, int $id): int
    {
        if ($type === 'elevage') {
            return Animal::where('elevage_id', $id)->count();
        }
        
        if ($type === 'user') {
            return Animal::whereHas('elevage', function($query) use ($id) {
                $query->where('user_id', $id);
            })->count();
        }
        
        return 0;
    }

    /**
     * Récupère les statistiques pour un élevage
     * 
     * @param int $elevageId
     * @return array
     */
    public function getElevageStats(int $elevageId): array
    {
        $stats = [
            'total' => Animal::where('elevage_id', $elevageId)->count(),
            'by_espece' => Animal::where('elevage_id', $elevageId)
                ->selectRaw('espece, count(*) as count')
                ->groupBy('espece')
                ->get(),
            'by_statut' => Animal::where('elevage_id', $elevageId)
                ->selectRaw('statut_sanitaire, count(*) as count')
                ->groupBy('statut_sanitaire')
                ->get(),
            'poids_moyen' => Animal::where('elevage_id', $elevageId)->avg('poids'),
            'age_moyen_mois' => $this->getAverageAgeInMonths($elevageId),
            'animaux_malades' => Animal::where('elevage_id', $elevageId)
                ->where('statut_sanitaire', 'malade')
                ->count(),
        ];
        
        return $stats;
    }

    /**
     * Upload de la photo d'animal
     * 
     * @param UploadedFile $photo
     * @param string $espece
     * @return string
     */
    private function uploadAnimalPhoto(UploadedFile $photo, string $espece): string
    {
        $filename = 'animal_' . time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
        $path = 'animals/' . $espece . '/' . $filename;
        
        $manager = new ImageManager(new Driver());
        $image = $manager->read($photo->getPathname());
        
        // Redimensionner (800x800 max)
        $image->cover(width: 800, height: 800);
        
        Storage::disk('public')->put($path, (string) $image->encode());
        
        return $path;
    }

    /**
     * Supprime la photo d'animal
     * 
     * @param string $photoUrl
     * @return bool
     */
    private function deleteAnimalPhoto(string $photoUrl): bool
    {
        if (Storage::disk('public')->exists($photoUrl)) {
            return Storage::disk('public')->delete($photoUrl);
        }
        return false;
    }

    /**
     * Enregistre l'historique des modifications
     * 
     * @param Animal $animal
     * @param string $action
     * @param array|null $beforeData
     * @param array|null $afterData
     * @param array|null $changedFields
     * @return void
     */
    private function logHistory(Animal $animal, string $action, ?array $beforeData, ?array $afterData, ?array $changedFields = null): void
    {
        AnimalHistorique::create([
            'animal_id' => $animal->id,
            'user_id' => auth()->id(),
            'action' => $action,
            'before_data' => $beforeData,
            'after_data' => $afterData,
            'changed_fields' => $changedFields ?? $this->getChangedFields($beforeData ?? [], $afterData ?? []),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Détermine les champs modifiés
     * 
     * @param array $before
     * @param array $after
     * @return array
     */
    private function getChangedFields(array $before, array $after): array
    {
        $changed = [];
        $fields = ['nom', 'race', 'espece', 'poids', 'statut_sanitaire', 'description', 'date_naissance'];
        
        foreach ($fields as $field) {
            $beforeValue = $before[$field] ?? null;
            $afterValue = $after[$field] ?? null;
            
            if ($beforeValue != $afterValue) {
                $changed[] = $field;
            }
        }
        
        return $changed;
    }

    /**
     * Calcule l'âge moyen en mois
     * 
     * @param int $elevageId
     * @return float
     */
    private function getAverageAgeInMonths(int $elevageId): float
    {
        $animals = Animal::where('elevage_id', $elevageId)->get();
        
        if ($animals->isEmpty()) {
            return 0;
        }
        
        $totalMonths = $animals->sum(function($animal) {
            return $animal->age['en_mois'] ?? 0;
        });
        
        return round($totalMonths / $animals->count(), 1);
    }

    /**
     * Vérifie si l'utilisateur est propriétaire de l'animal
     * 
     * @param Animal $animal
     * @param int|null $userId
     * @return bool
     */
    public function isOwner(Animal $animal, ?int $userId = null): bool
    {
        $userId = $userId ?? auth()->id();
        return $animal->elevage && $animal->elevage->user_id === $userId;
    }
}
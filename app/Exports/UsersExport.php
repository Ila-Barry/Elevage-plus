<?php
// app/Exports/UsersExport.php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Export UsersExport
 * 
 * Gère l'export des utilisateurs vers Excel/CSV
 */
class UsersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Récupère les données à exporter
     */
    public function collection()
    {
        $query = User::query();

        // Appliquer les filtres
        if (!empty($this->filters['role'])) {
            $query->where('role', $this->filters['role']);
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        return $query->get();
    }

    /**
     * En-têtes du fichier
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nom',
            'Email',
            'Téléphone',
            'Rôle',
            'Statut',
            'Bio',
            'Date d\'inscription',
            'Email vérifié le',
            'Dernière mise à jour',
        ];
    }

    /**
     * Mapping des données par ligne
     */
    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->telephone ?? 'N/A',
            $this->getRoleLabel($user->role),
            $this->getStatusLabel($user->status),
            $user->bio ?? '',
            $user->created_at->format('d/m/Y H:i'),
            $user->email_verified_at?->format('d/m/Y H:i') ?? 'Non vérifié',
            $user->updated_at->format('d/m/Y H:i'),
        ];
    }

    /**
     * Styles pour le fichier
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    protected function getRoleLabel(string $role): string
    {
        return match($role) {
            'admin' => 'Administrateur',
            'eleveur' => 'Éleveur',
            'visiteur' => 'Visiteur',
            default => $role,
        };
    }

    protected function getStatusLabel(string $status): string
    {
        return match($status) {
            'active' => 'Actif',
            'bannie' => 'Banni',
            default => $status,
        };
    }
}
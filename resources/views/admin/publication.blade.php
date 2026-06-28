{{-- resources/views/admin/publication.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Gestion des publications')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/publication.css') }}">
@endpush

@section('content')
    @php
        // ============================================================
        // CONFIGURATION DES DONNÉES SÉLECTIONNÉES SUR L'IMAGE
        // ============================================================

        // Top Statistiques conformes à l'image
        $stats = $stats ?? [
            'total'      => ['value' => 345, 'label' => 'Tous', 'bg' => 'bg-light', 'color' => 'text-dark', 'icon' => 'edit'],
            'publie'     => ['value' => 312, 'label' => 'Publiées', 'bg' => 'bg-success-soft', 'color' => 'text-success', 'icon' => 'check-square'],
            'signale'    => ['value' => 312, 'label' => 'Signalements', 'bg' => 'bg-warning-soft', 'color' => 'text-warning', 'icon' => 'exclamation-triangle'],
            'bloque'     => ['value' => 312, 'label' => 'bloquées', 'bg' => 'bg-danger-soft', 'color' => 'text-danger', 'icon' => 'times'],
        ];

        // Exemple de données pour matcher fidèlement les lignes de l'image
        $publications = $publications ?? [
            ['status' => 'publie',  'title' => 'Comment j\'ai augmenté...', 'author' => 'user_1', 'date' => '15/05/2026', 'likes' => 45, 'comments' => 12, 'shares' => 3, 'reports' => 0],
            ['status' => 'signale', 'title' => 'Comment j\'ai augmenté...', 'author' => 'user_1', 'date' => '15/05/2026', 'likes' => 0,  'comments' => 0,  'shares' => 0, 'reports' => 3],
            ['status' => 'publie',  'title' => 'Comment j\'ai augmenté...', 'author' => 'user_1', 'date' => '15/05/2026', 'likes' => 45, 'comments' => 12, 'shares' => 3, 'reports' => 0],
            ['status' => 'bloque',  'title' => 'Comment j\'ai augmenté...', 'author' => 'user_1', 'date' => '15/05/2026', 'likes' => 0,  'comments' => 0,  'shares' => 0, 'reports' => 6],
            ['status' => 'publie',  'title' => 'Comment j\'ai augmenté...', 'author' => 'user_1', 'date' => '15/05/2026', 'likes' => 45, 'comments' => 12, 'shares' => 3, 'reports' => 0],
            ['status' => 'publie',  'title' => 'Comment j\'ai augmenté...', 'author' => 'user_1', 'date' => '15/05/2026', 'likes' => 45, 'comments' => 12, 'shares' => 3, 'reports' => 0],
        ];
    @endphp

    <div class="publication-wrapper container-fluid px-4 py-3">

        {{-- BARRE DE NAVIGATION COMMUNE HAUT (Recherche globale / Admin Profil) --}}
        <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-2 rounded shadow-sm">
            <div class="search-global position-relative">
                <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-2 text-muted"></i>
                <input type="text" class="form-control form-control-sm ps-4" placeholder="rechercher...">
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="admin-profile small text-success fw-bold">
                    <i class="fas fa-user-circle me-1"></i> Wade, admin
                </div> 
                <button class="btn btn-sm btn-outline-secondary">Déconnexion</button>
            </div>
        </div>

        {{-- EN-TÊTE PRINCIPAL --}}
        <div class="row align-items-center mb-4">
            <div class="col">
                <h2 class="h4 fw-bold mb-0 text-uppercase">Gestion des publications</h2>
                <p class="text-muted small mb-0">gerer et moderer les publications de la plateforme</p>
            </div>
            <div class="col-auto d-flex gap-2">
                <button class="btn btn-success bg-gradient px-3 small d-flex align-items-center gap-1" data-toggle="modal" data-target="#addPublicationModal">
                    <span class="fs-5">+</span> Ajouter une publication
                </button>
                <button class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1 small">
                    <i class="fas fa-file-csv"></i> Exporter csv
                </button>
            </div>
        </div>

        {{-- BLOCS DE STATISTIQUES --}}
        <div class="row g-3 mb-4">
            @foreach($stats as $key => $stat)
                <div class="col">
                    <div class="card border-0 shadow-sm p-3 rounded-3 text-center h-100 {{ $stat['bg'] }}">
                        <div class="d-flex justify-content-center align-items-center gap-2 mb-1">
                            <i class="fas fa-{{ $stat['icon'] }} {{ $stat['color'] }} fs-5"></i>
                            <span class="text-muted fw-semibold small">{{ $stat['label'] }}</span>
                        </div>
                        <h3 class="fw-bold m-0">{{ $stat['value'] }}</h3>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ZONE DE FILTRES ET STATUTS --}}
        <div class="card border-0 shadow-sm p-3 mb-4">
            <h6 class="fw-bold text-muted small text-uppercase mb-3">Filtres et statuts</h6>
            
            <form action="#" method="GET">
                {{-- Barre de recherche de filtre --}}
                <div class="position-relative mb-3">
                    <input type="text" class="form-control" placeholder="rechercher par titre, auteur ....">
                    <i class="fas fa-search position-absolute top-50 end-0 translate-middle-y me-3 text-muted"></i>
                </div>

                {{-- Sélections horizontales --}}
                <div class="row g-3 align-items-center">
                    {{-- Boutons de Statuts --}}
                    <div class="col-12 col-xl-8 d-flex align-items-center flex-wrap">
    <span class="small text-muted me-5 fw-semibold">statuts:</span>
    
    <div class="d-flex gap-4 flex-wrap">
        <button type="button" class="btn btn-sm btn-success px-4 opacity-75 shadow-sm">
            <i class="fas fa-check small me-2"></i> Tous
        </button>
        
        <button type="button" class="btn btn-sm btn-success px-4 opacity-75 shadow-sm">
            <i class="fas fa-check small me-2"></i> Publié
        </button>
        
        <button type="button" class="btn btn-sm btn-warning text-white px-4 opacity-75 shadow-sm">
            <i class="fas fa-exclamation small me-2"></i> Signalé
        </button>
        
        <button type="button" class="btn btn-sm btn-danger px-4 opacity-75 shadow-sm">
            <i class="fas fa-times small me-2"></i> bloqué
        </button>
    </div>
</div>

                    {{-- Sélections déroulantes et validation --}}
                    <div class="col-12 col-xl-7 d-flex gap-3 align-items-center justify-content-xl-end flex-wrap">
                        <div class="d-flex align-items-center gap-2">
                            <span class="small text-muted whitespace-nowrap">Ctégorie :</span>
                            <select class="form-select form-select-sm" style="min-width: 120px;">
                                <option>Tous</option>
                            </select>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <span class="small text-muted whitespace-nowrap">Date :</span>
                            <select class="form-select form-select-sm" style="min-width: 120px;">
                                <option>Ce mois</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-sm btn-success bg-gradient px-4 d-flex align-items-center gap-1">
                            <i class="fas fa-search small"></i> Filtrer
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- TABLEAU PRINCIPAL --}}
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="m-0 fw-bold text-uppercase small d-flex align-items-center gap-2">
                    📋 Derniers utilisateurs inscrits
                </h6>
            </div>
            
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th scope="col" style="width: 12%;">Statut</th>
                            <th scope="col" style="width: 45%;">Titre</th>
                            <th scope="col" style="width: 15%;">Auteur</th>
                            <th scope="col" style="width: 13%;">Date</th>
                            <th scope="col" style="width: 15%; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @foreach($publications as $pub)
                            <tr>
                                {{-- Statut --}}
                                <td>
                                    @if($pub['status'] == 'publie')
                                        <span class="text-success fw-bold"><i class="fas fa-check-inner me-1"></i> Publié</span>
                                    @elseif($pub['status'] == 'signale')
                                        <span class="text-warning fw-bold"><i class="fas fa-exclamation-triangle me-1"></i> Signalé</span>
                                    @else
                                        <span class="text-danger fw-bold"><i class="fas fa-times me-1"></i> bloqué</span>
                                    @endif
                                </td>

                                {{-- Titre & Sous-compteurs --}}
                                <td>
                                    <div class="fw-bold text-dark">{{ $pub['title'] }}</div>
                                    <div class="text-muted extra-small d-flex gap-3 mt-1">
                                        @if($pub['status'] == 'publie')
                                            <span>⭐ {{ $pub['likes'] }} likes</span>
                                            <span>💬 {{ $pub['comments'] }} com</span>
                                            <span>📊 {{ $pub['shares'] }} part</span>
                                        @elseif($pub['status'] == 'signale')
                                            <span class="text-warning fw-bold">⚠️ {{ $pub['reports'] }} Signalements</span>
                                        @else
                                            <span class="text-danger fw-bold">❌ {{ $pub['reports'] }} Signalements</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Auteur --}}
                                <td class="fw-semibold text-secondary">{{ $pub['author'] }}</td>

                                {{-- Date --}}
                                <td class="text-muted">{{ $pub['date'] }}</td>

                                {{-- Actions --}}
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm p-1 text-success border rounded-circle bg-light d-flex align-items-center justify-content-center" style="width:28px; height:28px;" title="Voir"><i class="fas fa-eye fs-6"></i></button>
                                        <button class="btn btn-sm p-1 text-primary border rounded-circle bg-light d-flex align-items-center justify-content-center" style="width:28px; height:28px;" title="Modifier"><i class="fas fa-pen small"></i></button>
                                        <button class="btn btn-sm p-1 text-danger border rounded-circle bg-light d-flex align-items-center justify-content-center" style="width:28px; height:28px;" title="Supprimer"><i class="fas fa-times fs-5"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- FOOTER / PAGINATION CONFORME À L'IMAGE --}}
            <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3 border-top-0">
                <button class="btn btn-sm btn-outline-secondary px-3"><i class="fas fa-arrow-left small me-1"></i> Precedente</button>
                <div class="d-flex gap-1">
                    <button class="btn btn-sm btn-light border px-2 fw-bold active">1</button>
                    <button class="btn btn-sm btn-light border px-2">2</button>
                    <span class="px-1 align-self-end">...</span>
                    <button class="btn btn-sm btn-light border px-2">99</button>
                </div>
                <button class="btn btn-sm btn-outline-secondary px-3">Suivante <i class="fas fa-arrow-right small ms-1"></i></button>
            </div>
        </div>

    </div>
@endsection
@extends('layouts.admin.app')

@section('title', 'Gestion des publications')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/publication.css') }}">
@endpush

@section('content')

<div class="publication-wrapper">

    {{-- =============================================
        EN-TÊTE : Titre + bouton d'ajout
        ============================================= --}}
    <div class="page-header d-flex flex-wrap align-items-center justify-content-between">
        <div>
            <h1 class="page-title">
                <i class="fas fa-newspaper me-2 text-primary"></i>Gestion des publications
            </h1>
            <p class="page-subtitle text-muted">
                Gérez l'ensemble des articles et actualités de votre élevage
            </p>
        </div>
        <button class="btn btn-primary btn-add-publication"
                data-toggle="modal"
                data-target="#addPublicationModal"
                aria-label="Ajouter une nouvelle publication">
            <i class="fas fa-plus-circle me-1"></i> Nouvelle publication
        </button>
    </div>

    {{-- =============================================
        ZONE DE MESSAGES FLASH (succès / erreur)
        ============================================= --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Fermer">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Fermer">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- =============================================
        CARTES STATISTIQUES (dynamiques)
        ============================================= --}}
    @php
        // Données simulées (à remplacer par $stats venant du contrôleur)
        $stats = [
            'total'      => ['value' => 142, 'trend' => '+12', 'direction' => 'up'],
            'publie'     => ['value' => 87,  'trend' => '+5',  'direction' => 'up'],
            'brouillon'  => ['value' => 32,  'trend' => '-3',  'direction' => 'down'],
            'en_attente' => ['value' => 23,  'trend' => '-2',  'direction' => 'down'],
        ];

        // Définition des icônes et couleurs pour les cartes
        $statConfig = [
            'total'      => ['icon' => 'layer-group', 'bg' => 'bg-primary-soft', 'color' => 'text-primary'],
            'publie'     => ['icon' => 'check-circle', 'bg' => 'bg-success-soft', 'color' => 'text-success'],
            'brouillon'  => ['icon' => 'pencil-alt',    'bg' => 'bg-warning-soft', 'color' => 'text-warning'],
            'en_attente' => ['icon' => 'clock',         'bg' => 'bg-danger-soft',  'color' => 'text-danger'],
        ];

        // Libellés
        $statLabels = [
            'total'      => 'Total',
            'publie'     => 'Publiées',
            'brouillon'  => 'Brouillons',
            'en_attente' => 'En attente',
        ];
    @endphp

    <div class="row stats-row g-3 mb-4">
        @foreach($stats as $key => $stat)
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon {{ $statConfig[$key]['bg'] }}">
                        <i class="fas fa-{{ $statConfig[$key]['icon'] }} {{ $statConfig[$key]['color'] }}"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-label">{{ $statLabels[$key] }}</span>
                        <span class="stat-value">{{ $stat['value'] }}</span>
                    </div>
                    <div class="stat-trend {{ $stat['direction'] }}">
                        <i class="fas fa-arrow-{{ $stat['direction'] == 'up' ? 'up' : 'down' }}"></i>
                        {{ $stat['trend'] }} %
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- =============================================
        BARRE DE FILTRE & RECHERCHE
        ============================================= --}}
    <div class="filter-bar card p-3 mb-4">
        <div class="row g-3 align-items-center">

            <div class="col-xl-4 col-lg-4 col-md-12">
                <div class="search-wrapper">
                    <i class="fas fa-search search-icon" aria-hidden="true"></i>
                    <input type="text"
                           class="form-control search-input"
                           placeholder="Rechercher une publication..."
                           aria-label="Rechercher une publication"
                           id="searchPublications">
                </div>
            </div>

            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                <select class="form-select filter-select" aria-label="Filtrer par catégorie">
                    <option value="">Toutes les catégories</option>
                    @foreach($categories ?? ['Santé', 'Alimentation', 'Reproduction', 'Économie', 'Actualités'] as $cat)
                        <option value="{{ Str::slug($cat) }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6">
                <select class="form-select filter-select" aria-label="Filtrer par statut">
                    <option value="">Tous les statuts</option>
                    @foreach($statuses ?? ['Publié', 'Brouillon', 'En attente'] as $status)
                        <option value="{{ Str::slug($status) }}">{{ $status }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-xl-3 col-lg-3 col-md-12 d-flex gap-2 flex-wrap">
                <button class="btn btn-filter btn-primary flex-fill" type="button">
                    <i class="fas fa-filter me-1"></i> Filtrer
                </button>
                <button class="btn btn-reset btn-outline-secondary flex-fill" type="reset">
                    <i class="fas fa-undo me-1"></i> Réinitialiser
                </button>
            </div>

        </div>
    </div>

    {{-- =============================================
        TABLEAU DES PUBLICATIONS
        ============================================= --}}
    <div class="table-container card">
        <div class="table-responsive">
            <table class="table table-hover publication-table" aria-label="Liste des publications">
                <thead>
                    <tr>
                        <th scope="col" style="width: 50px;">#</th>
                        <th scope="col">Titre</th>
                        <th scope="col">Catégorie</th>
                        <th scope="col">Statut</th>
                        <th scope="col">Date de publication</th>
                        <th scope="col" style="width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($publications ?? $defaultPublications as $publication)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="pub-thumb me-2">
                                        <i class="fas fa-image text-muted" aria-hidden="true"></i>
                                    </div>
                                    <span class="fw-semibold">{{ $publication['title'] }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge-category badge-{{ Str::slug($publication['category']) }}">
                                    {{ $publication['category'] }}
                                </span>
                            </td>
                            <td>
                                <span class="badge-status badge-{{ Str::slug($publication['status']) }}">
                                    {{ $publication['status'] }}
                                </span>
                            </td>
                            <td>{{ $publication['date'] ?? '—' }}</td>
                            <td>
                                <div class="action-btns">
                                    <button class="btn-action view" title="Voir" aria-label="Voir la publication">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-action edit" title="Modifier" aria-label="Modifier la publication">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button class="btn-action delete" title="Supprimer" aria-label="Supprimer la publication">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                                Aucune publication trouvée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- =============================================
            PAGINATION
            ============================================= --}}
        <div class="table-footer d-flex flex-wrap align-items-center justify-content-between p-3 border-top">
            <div class="info-text text-muted small">
                Affichage de <strong>{{ $publications->firstItem() ?? 0 }}</strong>
                à <strong>{{ $publications->lastItem() ?? 0 }}</strong>
                sur <strong>{{ $publications->total() ?? 0 }}</strong> publications
            </div>
            <nav aria-label="Navigation des pages">
                {{ $publications->links('pagination::bootstrap-4') ?? '' }}
            </nav>
        </div>
    </div>

</div>

<div class="modal fade" id="addPublicationModal" tabindex="-1" aria-labelledby="addPublicationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPublicationModalLabel">
                    <i class="fas fa-plus-circle text-primary me-2"></i>Nouvelle publication
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.publications.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label fw-semibold">Titre de la publication</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title" placeholder="Ex: Les bonnes pratiques d'élevage"
                               value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="category" class="form-label fw-semibold">Catégorie</label>
                        <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                            <option value="">Sélectionner une catégorie</option>
                            @foreach($categories ?? ['Santé', 'Alimentation', 'Reproduction', 'Économie', 'Actualités'] as $cat)
                                <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label fw-semibold">Contenu</label>
                        <textarea class="form-control @error('content') is-invalid @enderror"
                                  id="content" name="content" rows="5"
                                  placeholder="Rédigez le contenu de votre publication..." required>{{ old('content') }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label fw-semibold">Image de couverture</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror"
                               id="image" name="image" accept="image/*">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label fw-semibold">Statut</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                            <option value="brouillon" {{ old('status') == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                            <option value="publie" {{ old('status') == 'publie' ? 'selected' : '' }}>Publié</option>
                            <option value="en_attente" {{ old('status') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Publier
                    </button>
                </div>
            </form>
        </div>
    </div>
<div class="dashboard-wrapper">

    <!-- contenu taches -->
     <h1>
        Bienvenue sur la page gestion des publications
     </h1>

</div>

@endsection
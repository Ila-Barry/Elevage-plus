{{-- resources/views/admin/publication.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Gestion des publications')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/publication.css') }}">
@endpush

@section('content')
    @php
        // ============================================================
        // DÉFINITION DES VALEURS PAR DÉFAUT (fallback)
        // Évite les erreurs si le contrôleur ne passe pas encore les données
        // ============================================================

        // Statistiques par défaut
        $defaultStats = [
            'total'      => ['value' => 0, 'trend' => null, 'direction' => 'up'],
            'publie'     => ['value' => 0, 'trend' => null, 'direction' => 'up'],
            'brouillon'  => ['value' => 0, 'trend' => null, 'direction' => 'down'],
            'en_attente' => ['value' => 0, 'trend' => null, 'direction' => 'down'],
        ];

        // Publications par défaut (exemple)
        $defaultPublications = [
            ['title' => 'Aucune publication disponible', 'category' => 'Information', 'status' => 'Brouillon', 'date' => '—'],
        ];

        // Catégories et statuts par défaut
        $defaultCategories = ['Santé', 'Alimentation', 'Reproduction', 'Économie', 'Actualités'];
        $defaultStatuses   = ['Publié', 'Brouillon', 'En attente'];

        // Utiliser les variables passées par le contrôleur ou les valeurs par défaut
        $stats       = $stats ?? $defaultStats;
        $publications = $publications ?? $defaultPublications;
        $categories  = $categories ?? $defaultCategories;
        $statuses    = $statuses ?? $defaultStatuses;

        // Configuration des icônes pour les cartes (thème vert)
        $statConfig = [
            'total'      => ['icon' => 'layer-group', 'bg' => 'bg-primary-soft', 'color' => 'text-primary'],
            'publie'     => ['icon' => 'check-circle', 'bg' => 'bg-success-soft', 'color' => 'text-success'],
            'brouillon'  => ['icon' => 'pencil-alt',    'bg' => 'bg-warning-soft', 'color' => 'text-warning'],
            'en_attente' => ['icon' => 'clock',         'bg' => 'bg-danger-soft',  'color' => 'text-danger'],
        ];

        $statLabels = [
            'total'      => 'Total',
            'publie'     => 'Publiées',
            'brouillon'  => 'Brouillons',
            'en_attente' => 'En attente',
        ];
    @endphp

    <div class="publication-wrapper">

        {{-- EN-TÊTE --}}
        <div class="page-header d-flex flex-wrap align-items-center justify-content-between">
            <div>
                <h1 class="page-title">
                    <i class="fas fa-newspaper me-2"> </i> Gestion des publications
                </h1>
                <p class="page-subtitle">
                    Gérez l'ensemble des articles et actualités de votre élevage
                </p>
            </div>
            <div class="header-actions">
                <button class="btn btn-add-publication"
                        data-toggle="modal"
                        data-target="#addPublicationModal"
                        aria-label="Ajouter une nouvelle publication">
                    <i class="fas fa-plus-circle me-1"></i> Ajouter une publication
                </button>
                <button class="btn btn-export" type="button" aria-label="Exporter en CSV">
                    <i class="fas fa-file-export me-1"></i> Exporter csv
                </button>
            </div>
        </div>

        {{-- MESSAGES FLASH --}}
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

        {{-- CARTES STATISTIQUES --}}
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
                        @if(!empty($stat['trend']))
                            <div class="stat-trend {{ $stat['direction'] }}">
                                <i class="fas fa-arrow-{{ $stat['direction'] == 'up' ? 'up' : 'down' }}"></i>
                                {{ $stat['trend'] }} %
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- BARRE DE FILTRE --}}
        <div class="filter-bar card p-3 mb-4">
            <div class="filter-bar-title">
                <i class="fas fa-sliders-h me-1"></i> Filtres et statuts
            </div>

            <div class="row g-3 align-items-center mb-3">
                <div class="col-12">
                    <div class="search-wrapper">
                        <i class="fas fa-search search-icon" aria-hidden="true"></i>
                        <input type="text"
                               class="form-control search-input"
                               placeholder="Rechercher par titre, auteur..."
                               aria-label="Rechercher une publication"
                               id="searchPublications">
                    </div>
                </div>
            </div>

            <div class="row g-3 align-items-center">
                <div class="col-xl-6 col-lg-6 col-md-12">
                    <div class="status-pills" id="statusPills" role="group" aria-label="Filtrer par statut">
                        <label class="status-pill pill-tous active">
                            <input type="checkbox" value="" checked> Tous
                        </label>
                        @foreach($statuses as $status)
                            <label class="status-pill pill-{{ Str::slug($status) }}">
                                <input type="checkbox" value="{{ Str::slug($status) }}"> {{ $status }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                    <select class="form-select filter-select" aria-label="Filtrer par catégorie">
                        <option value="">Toutes les catégories</option>
                        @foreach($categories as $cat)
                            <option value="{{ Str::slug($cat) }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 d-flex gap-2 flex-wrap">
                    <button class="btn btn-filter flex-fill" type="button">
                        <i class="fas fa-filter me-1"></i> Filtrer
                    </button>
                    <button class="btn btn-reset flex-fill" type="reset">
                        <i class="fas fa-undo me-1"></i> Réinit.
                    </button>
                </div>
            </div>
        </div>

        {{-- TABLEAU --}}
        <div class="table-container card">
            <div class="table-responsive">
                <table class="table table-hover publication-table" aria-label="Liste des publications">
                    <thead>
                        <tr>
                            <th scope="col">Statut</th>
                            <th scope="col">Titre</th>
                            <th scope="col">Catégorie</th>
                            <th scope="col">Date de publication</th>
                            <th scope="col" style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($publications as $index => $publication)
                            <tr>
                                <td>
                                    <span class="badge-status badge-{{ Str::slug($publication['status']) }}">
                                        <i class="fas fa-{{ Str::slug($publication['status']) == 'publie' ? 'check-circle' : (Str::slug($publication['status']) == 'bloque' ? 'times-circle' : 'clock') }}"></i>
                                        {{ $publication['status'] }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="pub-thumb me-2">
                                            <i class="fas fa-image" aria-hidden="true"></i>
                                        </div>
                                        <div>
                                            <span class="fw-semibold d-block">{{ $publication['title'] }}</span>
                                            @if(isset($publication['likes']) || isset($publication['comments']) || isset($publication['shares']))
                                                <div class="pub-meta">
                                                    @if(isset($publication['likes']))
                                                        <span><i class="far fa-heart"></i>{{ $publication['likes'] }}</span>
                                                    @endif
                                                    @if(isset($publication['comments']))
                                                        <span><i class="far fa-comment"></i>{{ $publication['comments'] }}</span>
                                                    @endif
                                                    @if(isset($publication['shares']))
                                                        <span><i class="fas fa-share"></i>{{ $publication['shares'] }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                            @if(!empty($publication['reports']))
                                                <div class="pub-meta text-danger">
                                                    <i class="fas fa-flag"></i>{{ $publication['reports'] }} signalement(s)
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-category badge-{{ Str::slug($publication['category']) }}">
                                        {{ $publication['category'] }}
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
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                                    Aucune publication trouvée.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            <div class="table-footer d-flex flex-wrap align-items-center justify-content-between p-3 border-top">
                <div class="info-text text-muted small">
                    @if(isset($publications) && is_object($publications) && method_exists($publications, 'total'))
                        Affichage de <strong>{{ $publications->firstItem() ?? 0 }}</strong>
                        à <strong>{{ $publications->lastItem() ?? 0 }}</strong>
                        sur <strong>{{ $publications->total() ?? 0 }}</strong> publications
                    @else
                        Affichage de <strong>{{ count($publications) }}</strong> publication(s)
                    @endif
                </div>
                <nav aria-label="Navigation des pages">
                    @if(isset($publications) && is_object($publications) && method_exists($publications, 'links'))
                        {{ $publications->links('pagination::bootstrap-4') }}
                    @else
                        {{-- Pagination statique de fallback --}}
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item disabled"><a class="page-link" href="#">Précédente</a></li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">Suivante</a></li>
                        </ul>
                    @endif
                </nav>
            </div>
        </div>

    </div>

    {{-- MODAL D'AJOUT --}}
    <div class="modal fade" id="addPublicationModal" tabindex="-1" aria-labelledby="addPublicationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPublicationModalLabel">
                        <i class="fas fa-plus-circle me-2"></i>Nouvelle publication
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" enctype="multipart/form-data">
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
                                @foreach($categories as $cat)
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
    </div>

@endsection

@push('scripts')
<script>
    // Gestion visuelle des pastilles de statut (filtre)
    document.addEventListener('DOMContentLoaded', function () {
        var pills = document.querySelectorAll('#statusPills .status-pill');
        pills.forEach(function (pill) {
            pill.addEventListener('click', function () {
                var input = pill.querySelector('input');
                var isTous = pill.classList.contains('pill-tous');

                if (isTous) {
                    pills.forEach(function (p) {
                        p.classList.remove('active');
                        p.querySelector('input').checked = false;
                    });
                    pill.classList.add('active');
                    input.checked = true;
                } else {
                    document.querySelector('#statusPills .pill-tous').classList.remove('active');
                    document.querySelector('#statusPills .pill-tous input').checked = false;
                    pill.classList.toggle('active');
                    input.checked = pill.classList.contains('active');

                    var anyActive = document.querySelectorAll('#statusPills .status-pill.active').length > 0;
                    if (!anyActive) {
                        document.querySelector('#statusPills .pill-tous').classList.add('active');
                        document.querySelector('#statusPills .pill-tous input').checked = true;
                    }
                }
            });
        });
    });
</script>
@endpush
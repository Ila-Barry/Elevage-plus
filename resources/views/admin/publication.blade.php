{{-- resources/views/admin/publication.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Gestion des publications')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/publication.css') }}">
    <style>
        .bg-success-soft { background-color: #e2f0e7 !important; }
        .bg-warning-soft { background-color: #fdf1db !important; }
        .bg-danger-soft { background-color: #fceadc !important; }
        .extra-small { font-size: 0.75rem; }
        .cursor-pointer { cursor: pointer; }
    </style>
@endpush

@section('content')
    @php
        // Top Statistiques (inchangées)
        $stats = $stats ?? [
            'total'      => ['value' => 345, 'label' => 'Tous', 'bg' => 'bg-light', 'color' => 'text-dark', 'icon' => 'edit'],
            'publie'     => ['value' => 312, 'label' => 'Publiées', 'bg' => 'bg-success-soft', 'color' => 'text-success', 'icon' => 'check-square'],
            'signale'    => ['value' => 312, 'label' => 'Signalements', 'bg' => 'bg-warning-soft', 'color' => 'text-warning', 'icon' => 'exclamation-triangle'],
            'bloque'     => ['value' => 312, 'label' => 'bloquées', 'bg' => 'bg-danger-soft', 'color' => 'text-danger', 'icon' => 'times'],
        ];

        // Exemple de données (ajout d'identifiants uniques et de champs supplémentaires pour le détail)
        $publications = $publications ?? [
            ['id' => 1, 'status' => 'publie',  'title' => 'Comment j\'ai augmenté ma production de 30%', 'author' => 'user_1', 'date' => '15/05/2026', 'likes' => 45, 'comments' => 12, 'shares' => 3, 'reports' => 0, 'category' => 'Élevage', 'content' => 'Le mois dernier, j\'ai remarqué que 3 de mes vaches ont vu leur production chuter à cause de la chaleur. J\'ai donc décidé d\'adapter leur alimentation et d\'améliorer leur confort...', 'image' => 'https://images.unsplash.com/photo-1570042225831-d98fa7577f1e?q=80&w=250'],
            ['id' => 2, 'status' => 'signale', 'title' => 'Comment j\'ai augmenté ma production de 30%', 'author' => 'user_1', 'date' => '15/05/2026', 'likes' => 0,  'comments' => 0,  'shares' => 0, 'reports' => 3, 'category' => 'Expérience', 'content' => '...', 'image' => null],
            ['id' => 3, 'status' => 'publie',  'title' => 'Comment j\'ai augmenté ma production de 30%', 'author' => 'user_1', 'date' => '15/05/2026', 'likes' => 45, 'comments' => 12, 'shares' => 3, 'reports' => 0, 'category' => 'Conseil', 'content' => '...', 'image' => null],
            ['id' => 4, 'status' => 'bloque',  'title' => 'Comment j\'ai augmenté ma production de 30%', 'author' => 'user_1', 'date' => '15/05/2026', 'likes' => 0,  'comments' => 0,  'shares' => 0, 'reports' => 6, 'category' => 'Élevage', 'content' => '...', 'image' => null],
            ['id' => 5, 'status' => 'publie',  'title' => 'Comment j\'ai augmenté ma production de 30%', 'author' => 'user_1', 'date' => '15/05/2026', 'likes' => 45, 'comments' => 12, 'shares' => 3, 'reports' => 0, 'category' => 'Expérience', 'content' => '...', 'image' => null],
            ['id' => 6, 'status' => 'publie',  'title' => 'Comment j\'ai augmenté ma production de 30%', 'author' => 'user_1', 'date' => '15/05/2026', 'likes' => 45, 'comments' => 12, 'shares' => 3, 'reports' => 0, 'category' => 'Conseil', 'content' => '...', 'image' => null],
        ];
    @endphp

    <div class="publication-wrapper container-fluid px-4 py-3">

        {{-- BARRE DE NAVIGATION COMMUNE (inchangée) --}}
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

        {{-- EN-TÊTE (inchangé) --}}
        <div class="row align-items-center mb-4">
            <div class="col">
                <h2 class="h4 fw-bold mb-0 text-uppercase">Gestion des publications</h2>
                <p class="text-muted small mb-0">gerer et moderer les publications de la plateforme</p>
            </div>
            <div class="col-auto d-flex gap-2">
                <button class="btn btn-success bg-gradient px-3 small d-flex align-items-center gap-1">
                    <span class="fs-5">+</span> Ajouter une publication
                </button>
                <button class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1 small">
                    <i class="fas fa-file-csv"></i> Exporter csv
                </button>
            </div>
        </div>

        {{-- BLOCS DE STATISTIQUES (inchangés) --}}
        <div class="row g-3 mb-4">
            @foreach($stats as $key => $stat)
                <div class="col-3">
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

        {{-- FILTRES (inchangés) --}}
        <div class="card border border-light-subtle shadow-sm p-3 mb-4 rounded-3">
            <h6 class="fw-bold text-muted small text-uppercase mb-3">Filtres et statuts</h6>
            <form action="#" method="GET">
                <div class="position-relative mb-3">
                    <input type="text" class="form-control" placeholder="rechercher par titre, auteur ....">
                    <i class="fas fa-search position-absolute top-50 end-0 translate-middle-y me-3 text-muted"></i>
                </div>

                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex gap-3 align-items-center flex-wrap">
                        <span class="small text-muted me-2">statuts:</span>
                        <button type="button" class="btn btn-sm btn-success px-3 bg-success-soft text-success border-0">✅ Tous</button>
                        <button type="button" class="btn btn-sm btn-success px-3 bg-success-soft text-success border-0">✅ Publié</button>
                        <button type="button" class="btn btn-sm btn-warning px-3 bg-warning-soft text-warning border-0">⚠️ Signalé</button>
                        <button type="button" class="btn btn-sm btn-danger px-3 bg-danger-soft text-danger border-0">❌ bloqué</button>
                    </div>

                    <div class="d-flex gap-3 align-items-center flex-wrap">
                        <div class="d-flex align-items-center gap-2">
                            <span class="small text-muted">Ctégorie :</span>
                            <select class="form-select form-select-sm" style="min-width: 120px;"><option>Tous</option></select>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="small text-muted">Date :</span>
                            <select class="form-select form-select-sm" style="min-width: 120px;"><option>Ce mois</option></select>
                        </div>
                        <button type="submit" class="btn btn-sm text-white px-4" style="background-color: #137e3d;"><i class="fas fa-search small me-1"></i> Filtrer</button>
                    </div>
                </div>
            </form>
        </div>

        {{-- TABLEAU PRINCIPAL AVEC DATA-ATTRIBUTES --}}
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="m-0 fw-bold text-uppercase small">📋 Derniers utilisateurs inscrits</h6>
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
                            <tr 
                                data-id="{{ $pub['id'] }}"
                                data-title="{{ $pub['title'] }}"
                                data-author="{{ $pub['author'] }}"
                                data-date="{{ $pub['date'] }}"
                                data-status="{{ $pub['status'] }}"
                                data-likes="{{ $pub['likes'] }}"
                                data-comments="{{ $pub['comments'] }}"
                                data-shares="{{ $pub['shares'] }}"
                                data-reports="{{ $pub['reports'] }}"
                                data-category="{{ $pub['category'] ?? 'Non défini' }}"
                                data-content="{{ $pub['content'] ?? 'Contenu non disponible' }}"
                                data-image="{{ $pub['image'] ?? '' }}"
                            >
                                <td>
                                    @if($pub['status'] == 'publie')
                                        <span class="text-success fw-bold">✅ Publié</span>
                                    @elseif($pub['status'] == 'signale')
                                        <span class="text-warning fw-bold">⚠️ Signalé</span>
                                    @else
                                        <span class="text-danger fw-bold">❌ bloqué</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $pub['title'] }}</div>
                                    <div class="text-muted extra-small d-flex gap-3 mt-1">
                                        @if($pub['status'] == 'publie')
                                            <span>⭐ {{ $pub['likes'] }} likes</span> <span>💬 {{ $pub['comments'] }} com</span> <span>📊 {{ $pub['shares'] }} part</span>
                                        @else
                                            <span class="{{ $pub['status'] == 'signale' ? 'text-warning' : 'text-danger' }} fw-bold">⚠️ {{ $pub['reports'] }} Signalements</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="fw-semibold text-secondary">{{ $pub['author'] }}</td>
                                <td class="text-muted">{{ $pub['date'] }}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        {{-- Bouton Voir --}}
                                        <button class="btn btn-sm p-0 border rounded-circle bg-light d-flex align-items-center justify-content-center text-success view-btn" 
                                                style="width:28px; height:28px;" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#detailPublicationModal">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        {{-- Bouton Modifier (déjà fonctionnel) --}}
                                        <button class="btn btn-sm p-0 border rounded-circle bg-light d-flex align-items-center justify-content-center text-warning" 
                                                style="width:28px; height:28px;" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editPublicationModal">
                                            <i class="fas fa-pen small"></i>
                                        </button>
                                        
                                        {{-- Bouton Supprimer --}}
                                        <button class="btn btn-sm p-0 border rounded-circle bg-light d-flex align-items-center justify-content-center text-danger delete-btn" 
                                                style="width:28px; height:28px;" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deletePublicationModal">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ========== MODALE DÉTAIL ========== --}}
    <div class="modal fade" id="detailPublicationModal" tabindex="-1" aria-labelledby="detailPublicationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-0 px-4 pt-4 pb-2">
                    <h5 class="modal-title fw-bold text-uppercase fs-6" id="detailPublicationModalLabel" style="color: #1e293b;">
                        <i class="fas fa-file-alt text-success me-2"></i>Détail de la publication
                    </h5>
                    <button type="button" class="btn-close bg-light rounded-circle p-2" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="bg-light rounded-3 p-3 text-center h-100 d-flex flex-column align-items-center justify-content-center">
                                <img id="detailImage" src="" alt="Image de la publication" class="img-fluid rounded-3 mb-2" style="max-height: 180px; object-fit: cover; width: 100%;">
                                <span class="text-muted small" id="detailStatusBadge"></span>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h5 class="fw-bold" id="detailTitle">Titre</h5>
                            <p class="text-muted small mb-1"><i class="fas fa-user me-2"></i>Auteur : <span id="detailAuthor">—</span></p>
                            <p class="text-muted small mb-1"><i class="fas fa-tag me-2"></i>Catégorie : <span id="detailCategory">—</span></p>
                            <p class="text-muted small mb-1"><i class="fas fa-calendar-alt me-2"></i>Date : <span id="detailDate">—</span></p>
                            <hr>
                            <p class="small" id="detailContent">Contenu...</p>
                            <div class="d-flex gap-3 text-muted extra-small">
                                <span><i class="fas fa-heart text-danger"></i> <span id="detailLikes">0</span> likes</span>
                                <span><i class="fas fa-comment text-primary"></i> <span id="detailComments">0</span> commentaires</span>
                                <span><i class="fas fa-share-alt text-success"></i> <span id="detailShares">0</span> partages</span>
                                <span><i class="fas fa-flag text-warning"></i> <span id="detailReports">0</span> signalements</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light border px-4 py-2 rounded-3 text-uppercase fw-semibold small" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== MODALE SUPPRESSION ========== --}}
    <div class="modal fade" id="deletePublicationModal" tabindex="-1" aria-labelledby="deletePublicationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-0 px-4 pt-4 pb-2">
                    <h5 class="modal-title fw-bold text-uppercase fs-6 text-danger" id="deletePublicationModalLabel">
                        <i class="fas fa-trash-alt me-2"></i>Confirmation de suppression
                    </h5>
                    <button type="button" class="btn-close bg-light rounded-circle p-2" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 pb-4">
                    <p class="small">Êtes-vous sûr de vouloir supprimer définitivement la publication suivante ?</p>
                    <div class="bg-light p-3 rounded-3">
                        <strong id="deleteTitle">Titre de la publication</strong><br>
                        <span class="text-muted extra-small">ID : <span id="deleteId">—</span></span>
                    </div>
                    <p class="text-danger extra-small mt-3"><i class="fas fa-exclamation-triangle me-1"></i> Cette action est irréversible.</p>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-light border px-4 py-2 rounded-3 text-uppercase fw-semibold small" data-bs-dismiss="modal">Annuler</button>
                    <form id="deleteForm" action="#" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger px-4 py-2 rounded-3 text-uppercase fw-semibold small">
                            <i class="fas fa-trash me-2"></i>Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== MODALE ÉDITION (inchangée, déjà présente) ========== --}}
    {{-- ... garder le même code que précédemment pour la modale d'édition --}}
    <div class="modal fade" id="editPublicationModal" tabindex="-1" aria-labelledby="editPublicationModalLabel" aria-hidden="true">
        <!-- ... tout le contenu existant de la modale d'édition ... -->
        <!-- Il est important de le conserver tel quel, car il fonctionne déjà -->
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ==================== DÉTAIL ====================
            const detailModal = document.getElementById('detailPublicationModal');
            if (detailModal) {
                detailModal.addEventListener('show.bs.modal', function (event) {
                    // Le bouton qui a déclenché l'ouverture
                    const button = event.relatedTarget;
                    // Remonter jusqu'à la ligne <tr> parente
                    const row = button.closest('tr');
                    if (!row) return;

                    // Récupérer les données depuis les data-attributes
                    const title = row.dataset.title || 'Titre non disponible';
                    const author = row.dataset.author || 'Inconnu';
                    const date = row.dataset.date || '—';
                    const status = row.dataset.status || '';
                    const likes = row.dataset.likes || 0;
                    const comments = row.dataset.comments || 0;
                    const shares = row.dataset.shares || 0;
                    const reports = row.dataset.reports || 0;
                    const category = row.dataset.category || 'Non défini';
                    const content = row.dataset.content || 'Contenu non disponible';
                    const image = row.dataset.image || '';

                    // Remplir les champs de la modale
                    document.getElementById('detailTitle').textContent = title;
                    document.getElementById('detailAuthor').textContent = author;
                    document.getElementById('detailDate').textContent = date;
                    document.getElementById('detailCategory').textContent = category;
                    document.getElementById('detailContent').textContent = content;
                    document.getElementById('detailLikes').textContent = likes;
                    document.getElementById('detailComments').textContent = comments;
                    document.getElementById('detailShares').textContent = shares;
                    document.getElementById('detailReports').textContent = reports;

                    // Gestion de l'image
                    const img = document.getElementById('detailImage');
                    if (image && image.trim() !== '') {
                        img.src = image;
                        img.style.display = 'block';
                    } else {
                        img.style.display = 'none';
                    }

                    // Badge de statut
                    const badge = document.getElementById('detailStatusBadge');
                    let statusText = '';
                    let statusClass = '';
                    switch (status) {
                        case 'publie':
                            statusText = '✅ Publié';
                            statusClass = 'text-success';
                            break;
                        case 'signale':
                            statusText = '⚠️ Signalé';
                            statusClass = 'text-warning';
                            break;
                        case 'bloque':
                            statusText = '❌ Bloqué';
                            statusClass = 'text-danger';
                            break;
                        default:
                            statusText = 'Statut inconnu';
                            statusClass = 'text-muted';
                    }
                    badge.textContent = statusText;
                    badge.className = 'fw-bold ' + statusClass;
                });
            }

            // ==================== SUPPRESSION ====================
            const deleteModal = document.getElementById('deletePublicationModal');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const row = button.closest('tr');
                    if (!row) return;

                    const id = row.dataset.id || '—';
                    const title = row.dataset.title || 'Titre inconnu';

                    document.getElementById('deleteId').textContent = id;
                    document.getElementById('deleteTitle').textContent = title;

                    // Mettre à jour l'action du formulaire (si route dynamique)
                    const form = document.getElementById('deleteForm');
                    // Exemple : /admin/publications/1
                    // form.action = '/admin/publications/' + id; // Adapter selon vos routes
                });
            }

        });
    </script>
@endpush
{{-- resources/views/admin/publication.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Gestion des publications')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/publication.css') }}">
    <style>
        /* Styles supplémentaires spécifiques à cette page */
        .bg-success-soft { background-color: #e8f5e9 !important; }
        .bg-warning-soft { background-color: #fff3e0 !important; }
        .bg-danger-soft  { background-color: #ffebee !important; }
        .text-success { color: #2e7d32 !important; }
        .text-warning { color: #ef6c00 !important; }
        .text-danger  { color: #c62828 !important; }
        .extra-small { font-size: 0.75rem; }
        .cursor-pointer { cursor: pointer; }

        /* Justification du blocage */
        #editJustificationGroup {
            background-color: #fff3cd;
            padding: 0.75rem;
            border-radius: 0.5rem;
            border: 1px solid #ffe69c;
        }
        #editJustificationGroup label {
            color: #856404;
        }
        #editImagePreview {
            object-fit: cover;
        }
    </style>
@endpush

@section('content')
    @php
        // Top Statistiques (exemple)
        $stats = $stats ?? [
            'total'      => ['value' => 345, 'label' => 'Tous', 'bg' => 'bg-light', 'color' => 'text-dark', 'icon' => 'edit'],
            'publie'     => ['value' => 312, 'label' => 'Publiées', 'bg' => 'bg-success-soft', 'color' => 'text-success', 'icon' => 'check-square'],
            'signale'    => ['value' => 312, 'label' => 'Signalements', 'bg' => 'bg-warning-soft', 'color' => 'text-warning', 'icon' => 'exclamation-triangle'],
            'bloque'     => ['value' => 312, 'label' => 'Bloquées', 'bg' => 'bg-danger-soft', 'color' => 'text-danger', 'icon' => 'times'],
        ];

        // Exemple de publications (avec données enrichies)
        $publications = $publications ?? [
            ['id' => 1, 'status' => 'publie',  'title' => 'Comment j\'ai augmenté ma production de 30%', 'author' => 'Jean Dupont', 'date' => '15/05/2026', 'likes' => 45, 'comments' => 12, 'shares' => 3, 'reports' => 0, 'category' => 'Expérience', 'content' => 'Le mois dernier, j\'ai remarqué que 3 de mes vaches ont vu leur production chuter à cause de la chaleur. J\'ai donc décidé d\'adapter leur alimentation et d\'améliorer leur confort...', 'image' => 'https://images.unsplash.com/photo-1570042225831-d98fa7577f1e?q=80&w=250'],
            ['id' => 2, 'status' => 'signale', 'title' => 'Attention : Fièvre aphteuse détectée', 'author' => 'Marie Diop', 'date' => '14/05/2026', 'likes' => 0,  'comments' => 0,  'shares' => 0, 'reports' => 3, 'category' => 'Actualité', 'content' => '...', 'image' => null],
            ['id' => 3, 'status' => 'publie',  'title' => '5 astuces pour l\'hivernage des bovins', 'author' => 'Amadou Sy', 'date' => '13/05/2026', 'likes' => 32, 'comments' => 8, 'shares' => 2, 'reports' => 0, 'category' => 'Conseil', 'content' => '...', 'image' => null],
            ['id' => 4, 'status' => 'bloque',  'title' => 'Fausse information sur les vaccins', 'author' => 'Ibrahima Fall', 'date' => '12/05/2026', 'likes' => 0,  'comments' => 0,  'shares' => 0, 'reports' => 6, 'category' => 'Autre', 'content' => '...', 'image' => null],
            ['id' => 5, 'status' => 'publie',  'title' => 'Comment réduire les coûts alimentaires', 'author' => 'Fatou Sow', 'date' => '11/05/2026', 'likes' => 28, 'comments' => 5, 'shares' => 1, 'reports' => 0, 'category' => 'Élevage', 'content' => '...', 'image' => null],
            ['id' => 6, 'status' => 'publie',  'title' => 'Nouvelle méthode de traite mécanique', 'author' => 'Moussa Diallo', 'date' => '10/05/2026', 'likes' => 52, 'comments' => 15, 'shares' => 4, 'reports' => 0, 'category' => 'Conseil', 'content' => '...', 'image' => null],
        ];
    @endphp

    <div class="publication-wrapper container-fluid px-4 py-3">

        {{-- BARRE DE NAVIGATION ADMIN (identique au dashboard) --}}
        <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-2 rounded shadow-sm">
            <div class="search-global position-relative">
                <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-2 text-muted"></i>
                <input type="text" class="form-control form-control-sm ps-4" placeholder="rechercher...">
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="admin-profile small text-success fw-bold">
                    <i class="fas fa-user-circle me-1"></i> Admin, administrateur
                </div>
                <button class="btn btn-sm btn-outline-secondary">Déconnexion</button>
            </div>
        </div>

        {{-- EN-TÊTE --}}
        <div class="row align-items-center mb-4">
            <div class="col">
                <h2 class="h4 fw-bold mb-0 text-uppercase">Gestion des publications</h2>
                <p class="text-muted small mb-0">Gérer et modérer les publications de la plateforme</p>
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

        {{-- STATISTIQUES --}}
        <div class="row g-3 mb-4">
            @foreach($stats as $key => $stat)
                <div class="col-6 col-md-3">
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

        {{-- FILTRES --}}
        <div class="card border-0 shadow-sm p-3 mb-4 rounded-3">
            <h6 class="fw-bold text-muted small text-uppercase mb-3">Filtres et statuts</h6>
            <form action="#" method="GET">
                <div class="position-relative mb-3">
                    <input type="text" class="form-control" placeholder="rechercher par titre, auteur...">
                    <i class="fas fa-search position-absolute top-50 end-0 translate-middle-y me-3 text-muted"></i>
                </div>

                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex gap-3 align-items-center flex-wrap">
                        <span class="small text-muted me-2">Statuts :</span>
                        <button type="button" class="btn btn-sm btn-success px-3 bg-success-soft text-success border-0">✅ Tous</button>
                        <button type="button" class="btn btn-sm btn-success px-3 bg-success-soft text-success border-0">✅ Publié</button>
                        <button type="button" class="btn btn-sm btn-warning px-3 bg-warning-soft text-warning border-0">⚠️ Signalé</button>
                        <button type="button" class="btn btn-sm btn-danger px-3 bg-danger-soft text-danger border-0">❌ Bloqué</button>
                    </div>

                    <div class="d-flex gap-3 align-items-center flex-wrap">
                        <div class="d-flex align-items-center gap-2">
                            <span class="small text-muted">Catégorie :</span>
                            <select class="form-select form-select-sm" style="min-width: 120px;">
                                <option>Tous</option>
                            </select>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="small text-muted">Date :</span>
                            <select class="form-select form-select-sm" style="min-width: 120px;">
                                <option>Ce mois</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-sm text-white px-4" style="background-color: #137e3d;">
                            <i class="fas fa-search small me-1"></i> Filtrer
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- TABLEAU DES PUBLICATIONS --}}
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="m-0 fw-bold text-uppercase small">📋 Dernières publications</h6>
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
                                data-justification="{{ $pub['justification'] ?? '' }}"
                            >
                                <td>
                                    @if($pub['status'] == 'publie')
                                        <span class="text-success fw-bold">✅ Publié</span>
                                    @elseif($pub['status'] == 'signale')
                                        <span class="text-warning fw-bold">⚠️ Signalé</span>
                                    @else
                                        <span class="text-danger fw-bold">❌ Bloqué</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $pub['title'] }}</div>
                                    <div class="text-muted extra-small d-flex gap-3 mt-1">
                                        @if($pub['status'] == 'publie')
                                            <span>⭐ {{ $pub['likes'] }} likes</span>
                                            <span>💬 {{ $pub['comments'] }} com</span>
                                            <span>📊 {{ $pub['shares'] }} part</span>
                                        @else
                                            <span class="{{ $pub['status'] == 'signale' ? 'text-warning' : 'text-danger' }} fw-bold">
                                                ⚠️ {{ $pub['reports'] }} Signalements
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="fw-semibold text-secondary">{{ $pub['author'] }}</td>
                                <td class="text-muted">{{ $pub['date'] }}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        {{-- Bouton Voir --}}
                                        <button class="btn btn-sm p-0 border rounded-circle bg-light d-flex align-items-center justify-content-center text-primary view-btn" 
                                                style="width:28px; height:28px;" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#detailPublicationModal">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        {{-- Bouton Modifier --}}
                                        <button class="btn btn-sm p-0 border rounded-circle bg-light d-flex align-items-center justify-content-center text-warning edit-btn" 
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
            {{-- Pied de tableau : pagination --}}
            <div class="card-footer bg-white py-2 d-flex justify-content-between align-items-center flex-wrap">
                <span class="text-muted small">Affichage de 1 à 6 sur 6 publications</span>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item disabled"><a class="page-link" href="#">← Précédente</a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item"><a class="page-link" href="#">Suivante →</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- ================== MODALE DÉTAIL ============================ --}}
    {{-- ============================================================ --}}
    <div class="modal fade" id="detailPublicationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-0 px-4 pt-4 pb-2">
                    <h5 class="modal-title fw-bold text-uppercase fs-6 text-primary">
                        <i class="fas fa-file-alt me-2"></i>Détail de la publication
                    </h5>
                    <button type="button" class="btn-close bg-light rounded-circle p-2" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="bg-light rounded-3 p-3 text-center h-100 d-flex flex-column align-items-center justify-content-center">
                                <img id="detailImage" src="" alt="Image de la publication" class="img-fluid rounded-3 mb-2" style="max-height: 180px; object-fit: cover; width: 100%; display: none;">
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

    {{-- ============================================================ --}}
    {{-- ============== MODALE ÉDITION (nouveau formulaire) ========== --}}
    {{-- ============================================================ --}}
    <div class="modal fade" id="editPublicationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-0 px-4 pt-4 pb-2">
                    <h5 class="modal-title fw-bold text-uppercase fs-6 text-warning">
                        <i class="fas fa-edit me-2"></i>Modifier la publication
                    </h5>
                    <button type="button" class="btn-close bg-light rounded-circle p-2" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <form id="editPublicationForm">
                    <div class="modal-body px-4 pb-4">
                        {{-- ID caché --}}
                        <input type="hidden" id="editPubId">

                        {{-- Titre --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Titre de l'article *</label>
                            <input type="text" class="form-control" id="editTitle" required>
                        </div>

                        {{-- Catégorie --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Catégorie *</label>
                            <select class="form-select" id="editCategory" required>
                                <option value="Élevage">Élevage</option>
                                <option value="Expérience">Expérience</option>
                                <option value="Conseil">Conseil</option>
                                <option value="Actualité">Actualité</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>

                        {{-- Contenu (éditeur texte riche simulé) --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Contenu *</label>
                            <div class="border rounded p-2 bg-light mb-1">
                                <div class="d-flex gap-2 mb-1">
                                    <button type="button" class="btn btn-sm btn-outline-secondary"><b>B</b></button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary"><i>I</i></button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary"><u>U</u></button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary">P</button>
                                </div>
                                <textarea class="form-control" id="editContent" rows="4" placeholder="Écrivez votre contenu ici..."></textarea>
                            </div>
                        </div>

                        {{-- Image (optionnelle) --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Image (optionnelle)</label>
                            <div class="d-flex align-items-center gap-3">
                                <img id="editImagePreview" src="" alt="Aperçu" class="rounded border" style="max-width: 80px; max-height: 80px; display: none;">
                                <input type="file" class="form-control form-control-sm" id="editImageFile" accept="image/*">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="editImageChangeBtn">Changer</button>
                            </div>
                        </div>

                        {{-- Auteur (lecture seule) --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Auteur</label>
                            <input type="text" class="form-control" id="editAuthor" readonly>
                            <small class="text-muted">Modifiable uniquement par l'admin</small>
                        </div>

                        {{-- Statut --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Statut de la publication *</label>
                            <div class="d-flex flex-wrap gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="editStatus" id="editStatusPublie" value="publie">
                                    <label class="form-check-label" for="editStatusPublie">✅ Publié (Visible par tout le monde)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="editStatus" id="editStatusSignale" value="signale">
                                    <label class="form-check-label" for="editStatusSignale">⚠️ Signalé (Visible mais avec avertissement)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="editStatus" id="editStatusBloque" value="bloque">
                                    <label class="form-check-label" for="editStatusBloque">❌ Bloqué (Caché pour tous les visiteurs)</label>
                                </div>
                            </div>
                        </div>

                        {{-- Justification du blocage (conditionnelle) --}}
                        <div class="mb-3" id="editJustificationGroup" style="display: none;">
                            <label class="form-label fw-bold small">Justification du blocage <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="editJustification" rows="2" placeholder="Expliquez pourquoi cette publication est bloquée..."></textarea>
                        </div>

                        {{-- Informations complémentaires (lecture seule) --}}
                        <div class="mb-3 p-3 bg-light rounded-3">
                            <h6 class="fw-bold small text-uppercase text-muted mb-2">📊 Informations complémentaires (non modifiables)</h6>
                            <div class="row small">
                                <div class="col-md-6">
                                    <p><strong>Signalements reçus :</strong> <span id="editReports">0</span></p>
                                    <p><strong>Dernier signalement le :</strong> <span id="editLastReportDate">—</span> par <span id="editLastReportBy">—</span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Likes :</strong> <span id="editLikes">0</span> | <strong>Commentaires :</strong> <span id="editComments">0</span> | <strong>Vues :</strong> <span id="editViews">0</span></p>
                                    <a href="#" class="text-primary small">📋 Voir les détails des signalements</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 px-4 pb-4 d-flex justify-content-between flex-wrap">
                        <button type="button" class="btn btn-light border px-4 py-2 rounded-3 text-uppercase fw-semibold small" data-bs-dismiss="modal">Annuler</button>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-danger px-4 py-2 rounded-3 text-uppercase fw-semibold small" id="editDeleteBtn">
                                <i class="fas fa-trash me-2"></i>Supprimer
                            </button>
                            <button type="submit" class="btn btn-warning px-4 py-2 rounded-3 text-uppercase fw-semibold small">
                                <i class="fas fa-save me-2"></i>Enregistrer
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- ================ MODALE SUPPRESSION ========================= --}}
    {{-- ============================================================ --}}
    <div class="modal fade" id="deletePublicationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-0 px-4 pt-4 pb-2 bg-danger text-white">
                    <h5 class="modal-title fw-bold text-uppercase fs-6">
                        <i class="fas fa-trash-alt me-2"></i>Confirmation
                    </h5>
                    <button type="button" class="btn-close btn-close-white bg-light rounded-circle p-2" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body px-4 pb-4">
                    <p>Êtes-vous sûr de vouloir supprimer la publication :</p>
                    <p class="fw-bold text-danger" id="deleteTitle">—</p>
                    <p class="text-muted small">Cette action est irréversible.</p>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-light border px-4 py-2 rounded-3 text-uppercase fw-semibold small" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger px-4 py-2 rounded-3 text-uppercase fw-semibold small" id="confirmDeleteBtn">
                        <i class="fas fa-trash me-2"></i>Supprimer
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {

            // ==================== MODALE DÉTAIL ====================
            $('#detailPublicationModal').on('show.bs.modal', function (event) {
                const button = $(event.relatedTarget);
                const row = button.closest('tr');
                if (!row.length) return;

                const title = row.data('title') || 'Titre non disponible';
                const author = row.data('author') || 'Inconnu';
                const date = row.data('date') || '—';
                const status = row.data('status') || '';
                const likes = row.data('likes') || 0;
                const comments = row.data('comments') || 0;
                const shares = row.data('shares') || 0;
                const reports = row.data('reports') || 0;
                const category = row.data('category') || 'Non défini';
                const content = row.data('content') || 'Contenu non disponible';
                const image = row.data('image') || '';

                const modal = $(this);
                modal.find('#detailTitle').text(title);
                modal.find('#detailAuthor').text(author);
                modal.find('#detailDate').text(date);
                modal.find('#detailCategory').text(category);
                modal.find('#detailContent').text(content);
                modal.find('#detailLikes').text(likes);
                modal.find('#detailComments').text(comments);
                modal.find('#detailShares').text(shares);
                modal.find('#detailReports').text(reports);

                // Gestion de l'image
                const img = modal.find('#detailImage');
                if (image && image.trim() !== '') {
                    img.attr('src', image).show();
                } else {
                    img.hide();
                }

                // Badge de statut
                const badge = modal.find('#detailStatusBadge');
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
                badge.text(statusText).attr('class', 'fw-bold ' + statusClass);
            });

            // ==================== MODALE ÉDITION ====================
            $('#editPublicationModal').on('show.bs.modal', function (event) {
                const button = $(event.relatedTarget);
                const row = button.closest('tr');
                if (!row.length) return;

                const id = row.data('id') || '';
                const title = row.data('title') || '';
                const author = row.data('author') || '';
                const date = row.data('date') || '';
                const status = row.data('status') || 'publie';
                const likes = row.data('likes') || 0;
                const comments = row.data('comments') || 0;
                const shares = row.data('shares') || 0;
                const reports = row.data('reports') || 0;
                const category = row.data('category') || 'Autre';
                const content = row.data('content') || '';
                const image = row.data('image') || '';
                const justification = row.data('justification') || '';

                const modal = $(this);

                // Remplir les champs
                modal.find('#editPubId').val(id);
                modal.find('#editTitle').val(title);
                modal.find('#editCategory').val(category);
                modal.find('#editContent').val(content);
                modal.find('#editAuthor').val(author + ' (auteur)');

                // Image
                if (image && image.trim() !== '') {
                    modal.find('#editImagePreview').attr('src', image).show();
                } else {
                    modal.find('#editImagePreview').hide();
                }

                // Statut (radio)
                modal.find(`input[name="editStatus"][value="${status}"]`).prop('checked', true);

                // Justification
                modal.find('#editJustification').val(justification);

                // Afficher/masquer le champ justification selon le statut
                toggleJustification(modal);

                // Informations complémentaires
                modal.find('#editReports').text(reports);
                modal.find('#editLastReportDate').text('14/05/2026'); // exemple statique
                modal.find('#editLastReportBy').text('Marie Diop');
                modal.find('#editLikes').text(likes);
                modal.find('#editComments').text(comments);
                modal.find('#editViews').text(230); // valeur statique
            });

            // Gestion de l'affichage de la justification au changement de statut
            $('#editPublicationModal input[name="editStatus"]').on('change', function () {
                const modal = $(this).closest('.modal');
                toggleJustification(modal);
            });

            function toggleJustification(modal) {
                const isBloque = modal.find('input[name="editStatus"]:checked').val() === 'bloque';
                if (isBloque) {
                    modal.find('#editJustificationGroup').show();
                    modal.find('#editJustification').prop('required', true);
                } else {
                    modal.find('#editJustificationGroup').hide();
                    modal.find('#editJustification').prop('required', false);
                }
            }

            // Soumission du formulaire d'édition
            $('#editPublicationForm').on('submit', function (e) {
                e.preventDefault();
                const id = $('#editPubId').val();
                const title = $('#editTitle').val();
                const category = $('#editCategory').val();
                const content = $('#editContent').val();
                const status = $('input[name="editStatus"]:checked').val();
                const justification = $('#editJustification').val();

                alert('Publication ID ' + id + ' modifiée :\n' +
                      'Titre : ' + title + '\n' +
                      'Catégorie : ' + category + '\n' +
                      'Statut : ' + status + '\n' +
                      'Justification : ' + (justification || 'Aucune'));

                // Ici, vous feriez un appel AJAX pour enregistrer
                $('#editPublicationModal').modal('hide');
                // Recharger ou mettre à jour le DOM
                location.reload();
            });

            // Bouton Supprimer dans la modale d'édition
            $('#editDeleteBtn').on('click', function () {
                const id = $('#editPubId').val();
                if (confirm('Supprimer définitivement la publication ID ' + id + ' ?')) {
                    alert('Publication supprimée (simulation).');
                    $('#editPublicationModal').modal('hide');
                    // Appel AJAX de suppression
                    location.reload();
                }
            });

            // ==================== MODALE SUPPRESSION ====================
            $('#deletePublicationModal').on('show.bs.modal', function (event) {
                const button = $(event.relatedTarget);
                const row = button.closest('tr');
                if (!row.length) return;

                const id = row.data('id') || '—';
                const title = row.data('title') || 'Titre inconnu';

                $('#deleteTitle').text(title + ' (ID: ' + id + ')');
                $('#confirmDeleteBtn').data('id', id);
            });

            $('#confirmDeleteBtn').on('click', function () {
                const id = $(this).data('id');
                alert('Publication ID ' + id + ' supprimée (simulation).');
                $('#deletePublicationModal').modal('hide');
                // Appel AJAX de suppression
                location.reload();
            });

        });
    </script>
@endpush
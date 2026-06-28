@extends('layouts.menu')

@section('title', 'Élevages')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/eleveurCSS/elevage.css') }}">
@endpush

@section('content')
<div class="dashboard-wrapper container-fluid py-4">
    
   <div class="d-flex flex-column align-items-start mb-4 gap-2">
        <h2 class="text-uppercase font-weight-bold mb-2" style="font-size: 1.5rem; letter-spacing: 0.5px; color: #000;">Mes Élevages</h2>
        <button type="button" class="btn btn-create d-flex align-items-center" id="openCreateModal">
            <i class="fas fa-plus mr-2"></i> Créer un nouvel élevage
        </button>
    </div>

    <!-- Container des élevages -->
    <div class="elevages-list d-flex flex-column gap-3 mb-4" id="elevagesList">
        <!-- Les cartes seront générées par JavaScript -->
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <button class="btn btn-outline-dark btn-pagination d-flex align-items-center" id="prevPage" disabled>
            <i class="fas fa-caret-left mr-2"></i> précédente
        </button>
        <div id="pageInfo" style="font-size: 0.9rem; color: #6c757d;">Page 1 / 1</div>
        <button class="btn btn-outline-dark btn-pagination d-flex align-items-center" id="nextPage">
            suivante <i class="fas fa-caret-right ml-2"></i>
        </button>
    </div>
</div>

<!-- ================= MODAL CRÉER ================= -->
<div class="modal fade" id="createElevageModal" tabindex="-1" aria-labelledby="createElevageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content create-elevage-modal">
            <div class="modal-header border-0 align-items-center pt-4 px-4 pb-2">
                <h5 class="modal-title font-weight-bold d-flex align-items-center" id="createElevageModalLabel">
                    <i class="fas fa-plus-circle mr-2 text-dark"></i> CRÉER UN ÉLEVAGE
                </h5>
                <button type="button" class="close text-danger border-0 bg-transparent" data-dismiss="modal" aria-label="Close" style="font-size: 24px; line-height: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body px-4 pb-4">
                <form id="createElevageForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form-section-box p-3 mb-3 rounded border" style="background-color: #fafafa;">
                        <label class="form-label font-weight-bold mb-2">
                            <i class="far fa-image text-success mr-1"></i> Photo <span class="font-weight-normal text-muted text-lowercase">(optionnelle)</span>
                        </label>
                        <div class="d-flex align-items-center flex-wrap gap-3">
                            <div class="image-preview-placeholder d-flex align-items-center justify-content-center rounded border border-dashed bg-white" id="imagePreview" style="width: 70px; height: 70px;">
                                <i class="far fa-image fa-2x text-muted"></i>
                            </div>
                            <div class="d-flex gap-2 flex-grow-1">
                                <label class="btn btn-sm btn-outline-success mb-0 d-flex align-items-center justify-content-center cursor-pointer" style="border-radius: 6px;">
                                    <i class="far fa-image mr-2"></i> Choisir une image
                                    <input type="file" name="photo" id="photoInput" class="d-none" accept="image/*">
                                </label>
                                <button type="button" class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center" id="removePhotoBtn" style="border-radius: 6px; display: none;">
                                    <i class="fas fa-times mr-2"></i> Supprimer
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold">
                            <i class="far fa-sticky-note text-warning mr-1"></i> Nom de l'élevage <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nom_elevage" id="nomElevage" class="form-control custom-input" placeholder="Élevage bovin de Thiès" required style="border-radius: 6px; font-size: 14px;">
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold">
                            <i class="fas fa-cow text-secondary mr-1"></i> Type d'élevage <span class="text-danger">*</span>
                        </label>
                        <div class="select-wrapper">
                            <select name="type_elevage" id="typeElevage" class="form-control form-select" style="border-radius: 6px; font-size: 14px;" required>
                                <option value="bovins">Bovins</option>
                                <option value="caprins">Caprins</option>
                                <option value="ovins">Ovins</option>
                                <option value="volailles">Volailles</option>
                                <option value="mixtes">Mixtes</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold">
                            <i class="fas fa-map-marker-alt text-danger mr-1"></i> Localisation
                        </label>
                        <input type="text" name="localisation" id="localisation" class="form-control custom-input" placeholder="Thiès, Sénégal" style="border-radius: 6px; font-size: 14px;">
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold">Superficie (hectares)</label>
                        <input type="number" name="superficie" id="superficie" class="form-control custom-input" placeholder="5" min="0" step="any" style="border-radius: 6px; font-size: 14px;">
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold">Nombre d'animaux</label>
                        <input type="number" name="animaux" id="animaux" class="form-control custom-input" placeholder="45" min="0" style="border-radius: 6px; font-size: 14px;">
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label font-weight-bold">
                            <i class="fas fa-feather-alt text-secondary mr-1"></i> Description <span class="font-weight-normal text-muted text-lowercase">(optionnelle)</span>
                        </label>
                        <textarea name="description" id="description" class="form-control custom-textarea" rows="3" placeholder="Élevage spécialisé dans la production laitière" style="border-radius: 6px; font-size: 14px;"></textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2">
                        <button type="button" class="btn btn-outline-secondary d-flex align-items-center" data-dismiss="modal" style="border-radius: 6px; min-width: 100px; justify-content: center;">
                            <i class="fas fa-times-circle mr-2 text-danger"></i> Annuler
                        </button>
                        <button type="submit" class="btn btn-success d-flex align-items-center" style="background-color: #198754; border-radius: 6px; min-width: 100px; justify-content: center;">
                            <i class="fas fa-check-square mr-2 text-white"></i> Valider
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL MODIFIER ================= -->
<div class="modal fade" id="modifierElevageModal" tabindex="-1" role="dialog" aria-labelledby="modifierElevageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
            <div class="modal-header border-0 align-items-center pt-4 px-4 pb-2 d-flex justify-content-between">
                <h5 class="modal-title font-weight-bold d-flex align-items-center" id="modifierElevageModalLabel" style="font-size: 16px; color: #1a1a1a;">
                    <i class="fas fa-users-cog mr-2 text-dark"></i> MODIFIER L'ÉLEVAGE
                </h5>
                <button type="button" class="close text-danger border-0 bg-transparent" data-dismiss="modal" aria-label="Close" style="font-size: 24px; line-height: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body px-4 pb-4">
                <form id="editElevageForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="edit_id" id="editId">

                    <div class="p-3 mb-3 border rounded" style="background-color: #fafafa;">
                        <label class="form-label fw-bold small text-muted">🖼️ Photo <span class="text-muted font-weight-normal">(optionnelle)</span></label>
                        <div class="d-flex align-items-center gap-3 mt-1">
                            <div class="border rounded d-flex align-items-center justify-content-center bg-white" id="editImagePreview" style="width: 70px; height: 70px; border-style: dashed !important;">
                                🖼️
                            </div>
                            <label class="btn btn-sm btn-outline-success d-flex align-items-center gap-1 mb-0 cursor-pointer" style="border-radius: 6px;">
                                <i class="far fa-image"></i> Choisir une image
                                <input type="file" name="edit_photo" id="editPhotoInput" class="d-none" accept="image/*">
                            </label>
                            <button type="button" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1" id="editRemovePhotoBtn" style="border-radius: 6px; display: none;">
                                ❌ Supprimer
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">🏷️ Nom de l'élevage <span class="text-danger">*</span></label>
                        <input type="text" name="edit_nom_elevage" id="editNomElevage" class="form-control" style="border-radius: 6px; font-size: 14px;" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">🐄 Type d'élevage <span class="text-danger">*</span></label>
                        <div class="select-wrapper">
                            <select name="edit_type_elevage" id="editTypeElevage" class="form-control form-select" style="border-radius: 6px; font-size: 14px;" required>
                                <option value="bovins">Bovins</option>
                                <option value="caprins">Caprins</option>
                                <option value="ovins">Ovins</option>
                                <option value="volailles">Volailles</option>
                                <option value="mixtes">Mixtes</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">📍 Localisation</label>
                        <input type="text" name="edit_localisation" id="editLocalisation" class="form-control" style="border-radius: 6px; font-size: 14px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Superficie (hectares)</label>
                        <input type="number" name="edit_superficie" id="editSuperficie" class="form-control" min="0" step="any" style="border-radius: 6px; font-size: 14px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nombre d'animaux</label>
                        <input type="number" name="edit_animaux" id="editAnimaux" class="form-control" min="0" style="border-radius: 6px; font-size: 14px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">📝 Description <span class="text-muted font-weight-normal">(optionnelle)</span></label>
                        <textarea name="edit_description" id="editDescription" class="form-control" rows="3" style="border-radius: 6px; font-size: 14px;"></textarea>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-outline-secondary d-flex align-items-center gap-1" data-dismiss="modal" style="border-radius: 6px; min-width: 100px; justify-content: center;">
                            ❎ Annuler
                        </button>
                        <button type="submit" class="btn btn-success d-flex align-items-center gap-1" style="background-color: #198754; border-radius: 6px; min-width: 100px; justify-content: center;">
                            ✅ Valider
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL VOIR ================= -->
<div class="modal fade" id="voirElevageModal" tabindex="-1" role="dialog" aria-labelledby="voirElevageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
            <div class="modal-header border-0 pb-0 d-flex justify-content-between align-items-center pt-4 px-4">
                <h5 class="modal-title fw-bold text-uppercase" id="voirElevageModalLabel" style="font-size: 16px; color: #1a1a1a;">
                    <i class="fas fa-eye mr-2 text-dark"></i> DÉTAILS DE L'ÉLEVAGE
                </h5>
                <button type="button" class="close text-danger border-0 bg-transparent" data-dismiss="modal" aria-label="Close" style="font-size: 24px; line-height: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body px-4 pb-4" id="viewElevageBody">
                <div class="text-center mb-3">
                    <img src="" id="viewElevageImage" class="img-fluid rounded border mb-2" style="max-height: 180px; object-fit: cover; width: 100%;">
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><strong>Nom :</strong></span>
                        <span class="text-muted" id="viewNom">-</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><strong>Type :</strong></span>
                        <span class="badge bg-success text-white px-3 py-2" id="viewType" style="border-radius: 20px;">-</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><strong>📍 Localisation :</strong></span>
                        <span class="text-muted" id="viewLocalisation">-</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><strong>📐 Superficie :</strong></span>
                        <span class="text-muted" id="viewSuperficie">-</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><strong>🐄 Animaux :</strong></span>
                        <span class="text-muted" id="viewAnimaux">-</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><strong>📅 Créé le :</strong></span>
                        <span class="text-muted" id="viewDate">-</span>
                    </li>
                    <li class="list-group-item px-0 border-bottom-0">
                        <p class="mb-1"><strong>📝 Description :</strong></p>
                        <p class="text-muted small bg-light p-2 rounded" id="viewDescription">-</p>
                    </li>
                </ul>
                <div class="d-flex justify-content-end mt-3">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 6px;">Fermer</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL SUPPRIMER ================= -->
<div class="modal fade" id="supprimerElevageModal" tabindex="-1" role="dialog" aria-labelledby="supprimerElevageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
            <div class="modal-header border-0 pb-0 d-flex justify-content-between align-items-center pt-4 px-4">
                <h5 class="modal-title fw-bold text-uppercase" style="font-size: 16px; color: #dc3545;">
                    <i class="fas fa-exclamation-triangle mr-2"></i> CONFIRMER LA SUPPRESSION
                </h5>
                <button type="button" class="close text-danger border-0 bg-transparent" data-dismiss="modal" aria-label="Close" style="font-size: 24px; line-height: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body px-4 pb-4 text-center">
                <i class="fas fa-trash-alt" style="font-size: 48px; color: #dc3545; margin-bottom: 15px;"></i>
                <h5 id="deleteElevageName" style="font-weight: 600; margin-bottom: 10px;">Êtes-vous sûr de vouloir supprimer cet élevage ?</h5>
                <p class="text-muted" style="font-size: 14px;">Cette action est irréversible. Toutes les données associées seront supprimées.</p>
                <input type="hidden" id="deleteElevageId">
                <div class="d-flex justify-content-center gap-3 mt-3">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 6px; min-width: 100px;">Annuler</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn" style="border-radius: 6px; min-width: 100px;">
                        <i class="fas fa-trash-alt mr-2"></i> Supprimer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// ================= DONNÉES =================
let elevages = [
    {
        id: 1,
        titre: 'ÉLEVAGE BOVIN - THIÈS',
        type: 'bovins',
        localisation: 'Thiès, Sénégal',
        superficie: '5 hectares',
        animaux: '45 bovins',
        date: '15/03/2025',
        description: 'Élevage spécialisé dans la production laitière de haute qualité. Troupeau de 45 vaches laitières de race locale améliorée.',
        image: '{{ asset('images/img-elevage.jpeg') }}'
    },
    {
        id: 2,
        titre: 'ÉLEVAGE CAPRIN - DAKAR',
        type: 'caprins',
        localisation: 'Dakar, Sénégal',
        superficie: '3 hectares',
        animaux: '20 caprins',
        date: '10/03/2024',
        description: 'Élevage caprin en zone périurbaine. Production de lait de chèvre et de viande pour le marché local.',
        image: '{{ asset('images/img-elevage.jpeg') }}'
    },
    {
        id: 3,
        titre: 'ÉLEVAGE BOVIN - SAINT-LOUIS',
        type: 'bovins',
        localisation: 'Saint-Louis, Sénégal',
        superficie: '8 hectares',
        animaux: '60 bovins',
        date: '22/04/2025',
        description: 'Grand élevage bovin extensif. Viande de qualité exportée vers les marchés de la sous-région.',
        image: '{{ asset('images/img-elevage.jpeg') }}'
    },
    {
        id: 4,
        titre: 'ÉLEVAGE OVIN - KAOLACK',
        type: 'ovins',
        localisation: 'Kaolack, Sénégal',
        superficie: '4 hectares',
        animaux: '35 ovins',
        date: '05/01/2025',
        description: 'Élevage ovin pour la production de viande et de laine. Race locale adaptée au climat sahélien.',
        image: '{{ asset('images/img-elevage.jpeg') }}'
    },
    {
        id: 5,
        titre: 'ÉLEVAGE VOLAILLE - DAKAR',
        type: 'volailles',
        localisation: 'Dakar, Sénégal',
        superficie: '1 hectare',
        animaux: '250 volailles',
        date: '12/06/2024',
        description: 'Élevage de poulets de chair et de pondeuses. Production d\'œufs frais pour les marchés locaux.',
        image: '{{ asset('images/img-elevage.jpeg') }}'
    }
];

let currentPage = 1;
const itemsPerPage = 3;
let currentEditId = null;
let toastTimeout = null;

// ================= FONCTIONS TOAST =================
function showToast(message, type = 'info') {
    const existingToast = document.querySelector('.custom-toast');
    if (existingToast) existingToast.remove();
    if (toastTimeout) clearTimeout(toastTimeout);
    
    const toast = document.createElement('div');
    toast.className = `custom-toast ${type}`;
    
    let icon = 'fa-info-circle';
    if (type === 'success') icon = 'fa-check-circle';
    else if (type === 'danger') icon = 'fa-exclamation-circle';
    else if (type === 'warning') icon = 'fa-exclamation-triangle';
    
    toast.innerHTML = `<div class="toast-content"><i class="fas ${icon}"></i><span>${message}</span></div>`;
    document.body.appendChild(toast);
    
    setTimeout(() => toast.classList.add('show'), 10);
    
    toastTimeout = setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// ================= AFFICHAGE DES ÉLEVAGES =================
function renderElevages() {
    const container = document.getElementById('elevagesList');
    const totalPages = Math.ceil(elevages.length / itemsPerPage);
    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const pageItems = elevages.slice(start, end);
    
    if (pageItems.length === 0) {
        container.innerHTML = `
            <div class="alert alert-info text-center py-5" style="border-radius: 12px;">
                <i class="fas fa-info-circle" style="font-size: 24px; display: block; margin-bottom: 10px;"></i>
                <h5>Aucun élevage enregistré</h5>
                <p class="text-muted">Cliquez sur "Créer un nouvel élevage" pour commencer.</p>
            </div>
        `;
        document.getElementById('pageInfo').textContent = 'Page 0 / 0';
        document.getElementById('prevPage').disabled = true;
        document.getElementById('nextPage').disabled = true;
        return;
    }
    
    container.innerHTML = pageItems.map(elevage => `
        <div class="elevage-card bg-white rounded shadow-sm border mb-3" data-id="${elevage.id}">
            <div class="row no-gutters align-items-stretch">
                <div class="col-12 col-md-4 col-lg-3 col-image-container">
                    <div class="img-container-full">
                        <img src="${elevage.image || '{{ asset('images/img-elevage.jpeg') }}'}" alt="${elevage.titre}" class="img-fluid h-100 w-100 object-fit-cover rounded-left">
                        <div class="img-overlay-text text-uppercase text-center">
                            Ferme Intégrée<br>
                            <small>"${elevage.titre.split(' - ')[1] || 'Sénégal'}"</small>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-6 p-4 d-flex flex-column justify-content-center">
                    <h2 class="elevage-card-title text-uppercase mb-3" style="font-size: 1.15rem; font-weight: 700;">${elevage.titre}</h2>

                    <div class="elevage-info-grid d-flex flex-column gap-2">
                        <div class="info-item-row pb-1 d-flex align-items-center">
                            <div class="info-label-side mr-2">
                                <i class="fas fa-map-marker-alt text-danger info-icon mr-2"></i>
                                <strong>Localisation :</strong>
                            </div>
                            <span class="text-muted">${elevage.localisation}</span>
                        </div>

                        <div class="info-item-row pb-1 d-flex align-items-center">
                            <div class="info-label-side mr-2">
                                <i class="fas fa-layer-group text-secondary info-icon mr-2"></i>
                                <strong>Superficie :</strong>
                            </div>
                            <span class="text-muted">${elevage.superficie}</span>
                        </div>

                        <div class="info-item-row pb-1 d-flex align-items-center">
                            <div class="info-label-side mr-2">
                                <i class="fas fa-layer-group text-secondary info-icon mr-2"></i>
                                <strong>Animaux :</strong>
                            </div>
                            <span class="text-muted">${elevage.animaux}</span>
                        </div>

                        <div class="info-item-row pb-1 d-flex align-items-center">
                            <div class="info-label-side mr-2">
                                <i class="far fa-calendar-alt text-danger info-icon mr-2"></i>
                                <strong>Créé le :</strong>
                            </div>
                            <span class="text-muted">${elevage.date}</span>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-2 col-lg-3 p-4 border-top border-md-top-0 d-flex align-items-center justify-content-center">
                    <div class="action-buttons-group d-flex flex-row flex-md-column align-items-stretch justify-content-center gap-2 w-100">
                        <button type="button" class="btn btn-action btn-view d-flex align-items-center justify-content-center gap-2" onclick="viewElevage(${elevage.id})">
                            <i class="far fa-eye"></i>
                            <span>voir</span>
                        </button>

                        <button type="button" class="btn btn-action btn-edit d-flex align-items-center justify-content-center gap-2" onclick="openEditModal(${elevage.id})">
                            <i class="fas fa-pencil-alt"></i>
                            <span>modifier</span>
                        </button>

                        <button type="button" class="btn btn-action btn-delete d-flex align-items-center justify-content-center gap-2" onclick="openDeleteModal(${elevage.id})">
                            <i class="far fa-trash-alt"></i>
                            <span>Supprimer</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
    
    // Mettre à jour la pagination
    document.getElementById('pageInfo').textContent = `Page ${currentPage} / ${totalPages}`;
    document.getElementById('prevPage').disabled = currentPage === 1;
    document.getElementById('nextPage').disabled = currentPage === totalPages;
}

// ================= PAGINATION =================
document.getElementById('prevPage').addEventListener('click', function() {
    if (currentPage > 1) {
        currentPage--;
        renderElevages();
        window.scrollTo({ top: document.getElementById('elevagesList').offsetTop - 20, behavior: 'smooth' });
    }
});

document.getElementById('nextPage').addEventListener('click', function() {
    const totalPages = Math.ceil(elevages.length / itemsPerPage);
    if (currentPage < totalPages) {
        currentPage++;
        renderElevages();
        window.scrollTo({ top: document.getElementById('elevagesList').offsetTop - 20, behavior: 'smooth' });
    }
});

// ================= CRÉER UN ÉLEVAGE =================
document.getElementById('openCreateModal').addEventListener('click', function() {
    // Vider le formulaire
    document.getElementById('createElevageForm').reset();
    document.getElementById('imagePreview').innerHTML = '<i class="far fa-image fa-2x text-muted"></i>';
    document.getElementById('removePhotoBtn').style.display = 'none';
    $('#createElevageModal').modal('show');
});

// Aperçu de l'image
document.getElementById('photoInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('imagePreview').innerHTML = `<img src="${event.target.result}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px;">`;
            document.getElementById('removePhotoBtn').style.display = 'flex';
        };
        reader.readAsDataURL(file);
    }
});

document.getElementById('removePhotoBtn').addEventListener('click', function() {
    document.getElementById('photoInput').value = '';
    document.getElementById('imagePreview').innerHTML = '<i class="far fa-image fa-2x text-muted"></i>';
    this.style.display = 'none';
});

// Soumission du formulaire de création
document.getElementById('createElevageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const nom = document.getElementById('nomElevage').value.trim();
    if (!nom) {
        showToast('Veuillez saisir un nom pour l\'élevage', 'warning');
        return;
    }
    
    const newElevage = {
        id: elevages.length > 0 ? Math.max(...elevages.map(e => e.id)) + 1 : 1,
        titre: nom.toUpperCase(),
        type: document.getElementById('typeElevage').value,
        localisation: document.getElementById('localisation').value || 'Sénégal',
        superficie: document.getElementById('superficie').value ? document.getElementById('superficie').value + ' hectares' : 'Non spécifié',
        animaux: document.getElementById('animaux').value ? document.getElementById('animaux').value + ' animaux' : 'Non spécifié',
        date: new Date().toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' }),
        description: document.getElementById('description').value || 'Aucune description',
        image: '{{ asset('images/img-elevage.jpeg') }}'
    };
    
    elevages.push(newElevage);
    currentPage = Math.ceil(elevages.length / itemsPerPage);
    renderElevages();
    $('#createElevageModal').modal('hide');
    showToast(`Élevage "${nom}" créé avec succès !`, 'success');
});

// ================= VOIR ÉLEVAGE =================
function viewElevage(id) {
    const elevage = elevages.find(e => e.id === id);
    if (!elevage) {
        showToast('Élevage non trouvé', 'danger');
        return;
    }
    
    document.getElementById('viewNom').textContent = elevage.titre;
    document.getElementById('viewType').textContent = elevage.type.charAt(0).toUpperCase() + elevage.type.slice(1);
    document.getElementById('viewType').className = `badge px-3 py-2` + 
        (elevage.type === 'bovins' ? ' bg-success' : 
         elevage.type === 'caprins' ? ' bg-info' : 
         elevage.type === 'ovins' ? ' bg-primary' : 
         elevage.type === 'volailles' ? ' bg-warning' : ' bg-secondary');
    document.getElementById('viewLocalisation').textContent = elevage.localisation;
    document.getElementById('viewSuperficie').textContent = elevage.superficie;
    document.getElementById('viewAnimaux').textContent = elevage.animaux;
    document.getElementById('viewDate').textContent = elevage.date;
    document.getElementById('viewDescription').textContent = elevage.description || 'Aucune description';
    document.getElementById('viewElevageImage').src = elevage.image || '{{ asset('images/img-elevage.jpeg') }}';
    
    $('#voirElevageModal').modal('show');
}

// ================= MODIFIER ÉLEVAGE =================
function openEditModal(id) {
    const elevage = elevages.find(e => e.id === id);
    if (!elevage) {
        showToast('Élevage non trouvé', 'danger');
        return;
    }
    
    currentEditId = id;
    document.getElementById('editId').value = id;
    document.getElementById('editNomElevage').value = elevage.titre;
    document.getElementById('editTypeElevage').value = elevage.type;
    document.getElementById('editLocalisation').value = elevage.localisation;
    document.getElementById('editSuperficie').value = elevage.superficie.replace(' hectares', '');
    document.getElementById('editAnimaux').value = elevage.animaux.replace(' animaux', '');
    document.getElementById('editDescription').value = elevage.description || '';
    
    // Réinitialiser l'aperçu de l'image
    document.getElementById('editImagePreview').innerHTML = elevage.image ? 
        `<img src="${elevage.image}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px;">` : '🖼️';
    document.getElementById('editRemovePhotoBtn').style.display = 'none';
    
    $('#modifierElevageModal').modal('show');
}

// Aperçu de l'image pour l'édition
document.getElementById('editPhotoInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('editImagePreview').innerHTML = `<img src="${event.target.result}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px;">`;
            document.getElementById('editRemovePhotoBtn').style.display = 'flex';
        };
        reader.readAsDataURL(file);
    }
});

document.getElementById('editRemovePhotoBtn').addEventListener('click', function() {
    document.getElementById('editPhotoInput').value = '';
    document.getElementById('editImagePreview').innerHTML = '🖼️';
    this.style.display = 'none';
});

// Soumission du formulaire de modification
document.getElementById('editElevageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const id = parseInt(document.getElementById('editId').value);
    const elevage = elevages.find(e => e.id === id);
    if (!elevage) {
        showToast('Élevage non trouvé', 'danger');
        return;
    }
    
    const nom = document.getElementById('editNomElevage').value.trim();
    if (!nom) {
        showToast('Veuillez saisir un nom pour l\'élevage', 'warning');
        return;
    }
    
    elevage.titre = nom.toUpperCase();
    elevage.type = document.getElementById('editTypeElevage').value;
    elevage.localisation = document.getElementById('editLocalisation').value || 'Sénégal';
    elevage.superficie = document.getElementById('editSuperficie').value ? document.getElementById('editSuperficie').value + ' hectares' : 'Non spécifié';
    elevage.animaux = document.getElementById('editAnimaux').value ? document.getElementById('editAnimaux').value + ' animaux' : 'Non spécifié';
    elevage.description = document.getElementById('editDescription').value || 'Aucune description';
    
    renderElevages();
    $('#modifierElevageModal').modal('hide');
    showToast(`Élevage "${nom}" modifié avec succès !`, 'success');
});

// ================= SUPPRIMER ÉLEVAGE =================
function openDeleteModal(id) {
    const elevage = elevages.find(e => e.id === id);
    if (!elevage) {
        showToast('Élevage non trouvé', 'danger');
        return;
    }
    
    document.getElementById('deleteElevageId').value = id;
    document.getElementById('deleteElevageName').textContent = `Êtes-vous sûr de vouloir supprimer "${elevage.titre}" ?`;
    $('#supprimerElevageModal').modal('show');
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    const id = parseInt(document.getElementById('deleteElevageId').value);
    const elevage = elevages.find(e => e.id === id);
    
    if (elevage) {
        const name = elevage.titre;
        elevages = elevages.filter(e => e.id !== id);
        
        // Ajuster la page si nécessaire
        const totalPages = Math.ceil(elevages.length / itemsPerPage);
        if (currentPage > totalPages && totalPages > 0) {
            currentPage = totalPages;
        } else if (totalPages === 0) {
            currentPage = 1;
        }
        
        renderElevages();
        $('#supprimerElevageModal').modal('hide');
        showToast(`Élevage "${name}" supprimé avec succès`, 'success');
    }
});

// ================= RECHERCHE (optionnel) =================
// Ajout d'une fonctionnalité de recherche (sera activée plus tard)
function searchElevages(query) {
    if (!query || query.trim() === '') {
        renderElevages();
        return;
    }
    
    const filtered = elevages.filter(e => 
        e.titre.toLowerCase().includes(query.toLowerCase()) ||
        e.localisation.toLowerCase().includes(query.toLowerCase()) ||
        e.type.toLowerCase().includes(query.toLowerCase())
    );
    
    const container = document.getElementById('elevagesList');
    if (filtered.length === 0) {
        container.innerHTML = `
            <div class="alert alert-info text-center py-5" style="border-radius: 12px;">
                <i class="fas fa-search" style="font-size: 24px; display: block; margin-bottom: 10px;"></i>
                <h5>Aucun résultat pour "${query}"</h5>
                <p class="text-muted">Essayez avec d'autres mots-clés.</p>
            </div>
        `;
        document.getElementById('pageInfo').textContent = 'Page 0 / 0';
        document.getElementById('prevPage').disabled = true;
        document.getElementById('nextPage').disabled = true;
    } else {
        // Afficher les résultats filtrés
        const originalElevages = elevages;
        elevages = filtered;
        currentPage = 1;
        renderElevages();
        elevages = originalElevages; // Restaurer les données
    }
}

// ================= INITIALISATION =================
document.addEventListener('DOMContentLoaded', function() {
    renderElevages();
    
    // Ajouter une barre de recherche simple en haut
    const searchHTML = `
        <div class="search-container mb-3" style="display: flex; gap: 10px; max-width: 400px;">
            <input type="text" id="searchInput" class="form-control" placeholder="🔍 Rechercher un élevage..." style="border-radius: 6px; font-size: 14px;">
            <button id="searchBtn" class="btn btn-outline-success" style="border-radius: 6px;">
                <i class="fas fa-search"></i>
            </button>
            <button id="clearSearchBtn" class="btn btn-outline-secondary" style="border-radius: 6px; display: none;">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    const header = document.querySelector('.d-flex.flex-column.align-items-start.mb-4');
    if (header) {
        header.insertAdjacentHTML('afterend', searchHTML);
    }
    
    // Événements de recherche
    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');
    const clearBtn = document.getElementById('clearSearchBtn');
    
    if (searchBtn && searchInput) {
        searchBtn.addEventListener('click', function() {
            const query = searchInput.value;
            if (query.trim()) {
                searchElevages(query);
                clearBtn.style.display = 'flex';
            }
        });
        
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchBtn.click();
            }
        });
    }
    
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            this.style.display = 'none';
            renderElevages();
        });
    }
    
    // Fermer les modals avec la touche Escape (déjà géré par Bootstrap)
    // Ajouter les styles manquants
    const style = document.createElement('style');
    style.textContent = `
        .custom-toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 10000;
            transform: translateX(400px);
            transition: transform 0.3s ease;
        }
        .custom-toast.show { transform: translateX(0); }
        .custom-toast .toast-content {
            background: #343a40;
            color: white;
            padding: 12px 20px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .custom-toast.success .toast-content { background: #28a745; }
        .custom-toast.danger .toast-content { background: #dc3545; }
        .custom-toast.warning .toast-content { background: #ffc107; color: #343a40; }
        .btn-action span { 
            display: inline-block; 
        }
        @media (max-width: 967.98px) {
            .btn-action span {
                display: none !important;
            }
        }
        .search-container {
            display: flex;
            gap: 10px;
            max-width: 400px;
            margin-bottom: 15px;
        }
    `;
    document.head.appendChild(style);
});
</script>

@endsection
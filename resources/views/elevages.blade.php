{{-- resources/views/elevages.blade.php --}}

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

    <!-- Barre de recherche -->
    <div class="search-container mb-3">
        <input type="text" id="searchInput" class="form-control" placeholder="🔍 Rechercher un élevage..." style="border-radius: 6px; font-size: 14px;">
        <button id="searchBtn" class="btn btn-outline-success" style="border-radius: 6px;">
            <i class="fas fa-search"></i>
        </button>
        <button id="clearSearchBtn" class="btn btn-outline-secondary" style="border-radius: 6px; display: none;">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Container des élevages -->
    <div class="elevages-list d-flex flex-column gap-3 mb-4" id="elevagesList">
        <!-- Les cartes seront générées par JavaScript -->
        <div class="text-center py-5">
            <div class="spinner-border text-success" role="status">
                <span class="sr-only">Chargement...</span>
            </div>
            <p class="mt-2 text-muted">Chargement des élevages...</p>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <button class="btn btn-outline-dark btn-pagination d-flex align-items-center" id="prevPage" disabled>
            <i class="fas fa-caret-left mr-2"></i> précédente
        </button>
        <div id="pageInfo" style="font-size: 0.9rem; color: #6c757d;">Page 1 / 1</div>
        <button class="btn btn-outline-dark btn-pagination d-flex align-items-center" id="nextPage" disabled>
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
                <div id="createError" class="alert alert-danger" style="display: none;"></div>
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
                            <div class="d-flex gap-2 flex-fill">
                                <label class="btn btn-sm btn-outline-success mb-0 d-flex align-items-center justify-content-center cursor-pointer" style="border-radius: 6px;">
                                    <i class="far fa-image mr-2"></i> Choisir une image
                                    <input type="file" name="image" id="photoInput" class="d-none" accept="image/*">
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
                        <input type="text" name="nom" id="nomElevage" class="form-control custom-input" placeholder="Élevage bovin de Thiès" required style="border-radius: 6px; font-size: 14px;">
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
                        <button type="submit" class="btn btn-success d-flex align-items-center" id="createSubmitBtn" style="background-color: #198754; border-radius: 6px; min-width: 100px; justify-content: center;">
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
                <div id="editError" class="alert alert-danger" style="display: none;"></div>
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
                                <input type="file" name="edit_image" id="editPhotoInput" class="d-none" accept="image/*">
                            </label>
                            <button type="button" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1" id="editRemovePhotoBtn" style="border-radius: 6px; display: none;">
                                ❌ Supprimer
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">🏷️ Nom de l'élevage <span class="text-danger">*</span></label>
                        <input type="text" name="edit_nom" id="editNomElevage" class="form-control" style="border-radius: 6px; font-size: 14px;" required>
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
                        <label class="form-label fw-bold small">📝 Description <span class="text-muted font-weight-normal">(optionnelle)</span></label>
                        <textarea name="edit_description" id="editDescription" class="form-control" rows="3" style="border-radius: 6px; font-size: 14px;"></textarea>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-outline-secondary d-flex align-items-center gap-1" data-dismiss="modal" style="border-radius: 6px; min-width: 100px; justify-content: center;">
                            ❎ Annuler
                        </button>
                        <button type="submit" class="btn btn-success d-flex align-items-center gap-1" id="editSubmitBtn" style="background-color: #198754; border-radius: 6px; min-width: 100px; justify-content: center;">
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
                        <span class="badge px-3 py-2" id="viewType" style="border-radius: 20px;">-</span>
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
// ================= CONFIGURATION =================
const API_URL = '/api';
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '';
const token = localStorage.getItem('access_token');

// ================= VARIABLES =================
let elevages = [];
let currentPage = 1;
let totalPages = 1;
const itemsPerPage = 3;
let currentEditId = null;
let toastTimeout = null;
let searchQuery = '';
let isLoading = false;

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

// ================= API CALLS =================
async function fetchElevages(page = 1, search = '') {
    try {
        let url = `${API_URL}/elevages?page=${page}&per_page=${itemsPerPage}`;
        if (search && search.trim()) {
            url += `&search=${encodeURIComponent(search.trim())}`;
        }
        
        console.log('🔍 Appel API:', url);
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
                'X-CSRF-TOKEN': CSRF_TOKEN
            }
        });

        const result = await response.json();

        if (!response.ok) {
            throw result;
        }

        console.log('✅ Réponse API reçue');
        return result;
    } catch (error) {
        console.error('❌ Erreur fetch elevages:', error);
        throw error;
    }
}

// ================= CRÉER UN ÉLEVAGE =================
async function createElevage(formData) {
    try {
        const response = await fetch(`${API_URL}/elevages`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: formData
        });

        const result = await response.json();
        if (!response.ok) throw result;
        return result;
    } catch (error) {
        console.error('❌ Erreur création elevage:', error);
        throw error;
    }
}

// ================= MODIFIER UN ÉLEVAGE =================
async function updateElevage(id, formData) {
    try {
        const nom = formData.get('nom');
        const type = formData.get('type_elevage');
        
        if (!nom || !nom.trim()) {
            throw { message: 'Le nom de l\'élevage est obligatoire.' };
        }
        
        if (!type || !type.trim()) {
            throw { message: 'Le type d\'élevage est obligatoire.' };
        }
        
        const response = await fetch(`${API_URL}/elevages/${id}`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'X-HTTP-Method-Override': 'PUT'
            },
            body: formData
        });

        const result = await response.json();
        
        if (!response.ok) {
            if (response.status === 422 && result.errors) {
                let errorMessages = [];
                for (const [field, errors] of Object.entries(result.errors)) {
                    errorMessages.push(`${field}: ${errors.join(', ')}`);
                }
                throw { 
                    message: errorMessages.join('\n'),
                    errors: result.errors
                };
            }
            throw result;
        }
        
        return result;
    } catch (error) {
        console.error('❌ Erreur mise à jour elevage:', error);
        throw error;
    }
}

// ================= SUPPRIMER UN ÉLEVAGE =================
async function deleteElevage(id) {
    try {
        const response = await fetch(`${API_URL}/elevages/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
                'X-CSRF-TOKEN': CSRF_TOKEN
            }
        });

        const result = await response.json();
        if (!response.ok) throw result;
        return result;
    } catch (error) {
        console.error('❌ Erreur suppression elevage:', error);
        throw error;
    }
}

// ================= CHARGEMENT DES ÉLEVAGES =================
async function loadElevages(page = 1, search = '') {
    if (isLoading) return;
    isLoading = true;
    
    try {
        const container = document.getElementById('elevagesList');
        container.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-success" role="status">
                    <span class="sr-only">Chargement...</span>
                </div>
                <p class="mt-2 text-muted">Chargement des élevages...</p>
            </div>
        `;
        
        const result = await fetchElevages(page, search);
        
        if ((result.status === 'success' || result.success === true) && result.data) {
            const elevagesData = result.data.data || [];
            const meta = result.data.meta || {};
            
            elevages = elevagesData;
            window.elevages = elevagesData;
            
            currentPage = meta.current_page || 1;
            totalPages = meta.last_page || 1;
            
            renderElevages();
        } else {
            showToast(result.message || 'Erreur lors du chargement des élevages', 'danger');
            elevages = [];
            window.elevages = [];
            renderElevages();
        }
    } catch (error) {
        console.error('❌ Erreur:', error);
        showToast('Erreur lors du chargement des élevages.', 'danger');
        elevages = [];
        window.elevages = [];
        renderElevages();
    } finally {
        isLoading = false;
    }
}

// ================= AFFICHAGE DES ÉLEVAGES =================
function renderElevages() {
    const container = document.getElementById('elevagesList');
    
    const dataToRender = elevages && elevages.length > 0 ? elevages : (window.elevages || []);
    
    if (!dataToRender || dataToRender.length === 0) {
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
    
    const validElevages = dataToRender.filter(e => e && e.id);
    if (validElevages.length === 0) {
        container.innerHTML = `
            <div class="alert alert-warning text-center py-5" style="border-radius: 12px;">
                <i class="fas fa-exclamation-triangle" style="font-size: 24px; display: block; margin-bottom: 10px;"></i>
                <h5>Données invalides</h5>
                <p class="text-muted">Les données des élevages sont corrompues.</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = validElevages.map(elevage => {
        const nom = elevage.nom || 'Sans nom';
        const type = elevage.type_elevage_label || elevage.type_elevage || 'Non spécifié';
        const localisation = elevage.localisation || 'Non renseignée';
        const superficie = elevage.superficie !== undefined && elevage.superficie !== null ? `${elevage.superficie} ha` : 'Non spécifiée';
        const dateCreation = elevage.created_at ? new Date(elevage.created_at).toLocaleDateString('fr-FR') : 'N/A';
        const imageUrl = elevage.img_url || "{{ asset('images/img-elevage.jpeg') }}";
        
        return `
        <div class="elevage-card bg-white rounded shadow-sm border mb-3" data-id="${elevage.id}">
            <div class="row no-gutters align-items-stretch">
                <div class="col-12 col-md-4 col-lg-3 col-image-container">
                    <div class="img-container-full">
                        <img src="${imageUrl}" alt="${nom}" class="img-fluid h-100 w-100 object-fit-cover rounded-left" onerror="this.src='{{ asset('images/img-elevage.jpeg') }}'">
                        <div class="img-overlay-text text-uppercase text-center">
                            Ferme Intégrée<br>
                            <small>"${type}"</small>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-6 p-4 d-flex flex-column justify-content-center">
                    <h2 class="elevage-card-title text-uppercase mb-3" style="font-size: 1.15rem; font-weight: 700;">${nom}</h2>

                    <div class="elevage-info-grid d-flex flex-column gap-2">
                        <div class="info-item-row pb-1 d-flex align-items-center">
                            <div class="info-label-side mr-2">
                                <i class="fas fa-map-marker-alt text-danger info-icon mr-2"></i>
                                <strong>Localisation :</strong>
                            </div>
                            <span class="text-muted">${localisation}</span>
                        </div>

                        <div class="info-item-row pb-1 d-flex align-items-center">
                            <div class="info-label-side mr-2">
                                <i class="fas fa-layer-group text-secondary info-icon mr-2"></i>
                                <strong>Superficie :</strong>
                            </div>
                            <span class="text-muted">${superficie}</span>
                        </div>

                        <div class="info-item-row pb-1 d-flex align-items-center">
                            <div class="info-label-side mr-2">
                                <i class="far fa-calendar-alt text-danger info-icon mr-2"></i>
                                <strong>Créé le :</strong>
                            </div>
                            <span class="text-muted">${dateCreation}</span>
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
        `;
    }).join('');
    
    document.getElementById('pageInfo').textContent = `Page ${currentPage} / ${totalPages}`;
    document.getElementById('prevPage').disabled = currentPage === 1 || totalPages === 0;
    document.getElementById('nextPage').disabled = currentPage === totalPages || totalPages === 0;
}

// ================= FONCTION COULEUR =================
function getTypeColor(type) {
    const colors = {
        'bovins': 'success',
        'caprins': 'info',
        'ovins': 'primary',
        'volailles': 'warning',
        'porcins': 'secondary',
        'equins': 'dark',
        'mixte': 'secondary',
        'apiculture': 'warning',
        'cuniculture': 'info',
        'autre': 'secondary'
    };
    return colors[type] || 'secondary';
}

// ================= PAGINATION =================
document.getElementById('prevPage').addEventListener('click', function() {
    if (currentPage > 1 && !isLoading) {
        currentPage--;
        loadElevages(currentPage, searchQuery);
        window.scrollTo({ top: document.getElementById('elevagesList').offsetTop - 20, behavior: 'smooth' });
    }
});

document.getElementById('nextPage').addEventListener('click', function() {
    if (currentPage < totalPages && !isLoading) {
        currentPage++;
        loadElevages(currentPage, searchQuery);
        window.scrollTo({ top: document.getElementById('elevagesList').offsetTop - 20, behavior: 'smooth' });
    }
});

// ================= RECHERCHE =================
document.getElementById('searchBtn').addEventListener('click', function() {
    const query = document.getElementById('searchInput').value.trim();
    searchQuery = query;
    currentPage = 1;
    loadElevages(1, query);
    document.getElementById('clearSearchBtn').style.display = query ? 'flex' : 'none';
});

document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        document.getElementById('searchBtn').click();
    }
});

document.getElementById('clearSearchBtn').addEventListener('click', function() {
    document.getElementById('searchInput').value = '';
    this.style.display = 'none';
    searchQuery = '';
    currentPage = 1;
    loadElevages(1, '');
});

// ================= CRÉATION - OUVERTURE MODAL =================
document.getElementById('openCreateModal').addEventListener('click', function() {
    document.getElementById('createElevageForm').reset();
    document.getElementById('imagePreview').innerHTML = '<i class="far fa-image fa-2x text-muted"></i>';
    document.getElementById('removePhotoBtn').style.display = 'none';
    document.getElementById('createError').style.display = 'none';
    $('#createElevageModal').modal('show');
});

// ================= CRÉATION - APERÇU IMAGE =================
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

// ================= CRÉATION - SOUMISSION =================
document.getElementById('createElevageForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('createSubmitBtn');
    const errorDiv = document.getElementById('createError');
    errorDiv.style.display = 'none';
    
    const nom = document.getElementById('nomElevage').value.trim();
    if (!nom) {
        showToast('Veuillez saisir un nom pour l\'élevage', 'warning');
        return;
    }
    
    const formData = new FormData(this);
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> En cours...';
    
    try {
        const result = await createElevage(formData);
        
        if (result.status === 'success' || result.success === true) {
            $('#createElevageModal').modal('hide');
            showToast(result.message || 'Élevage créé avec succès !', 'success');
            currentPage = 1;
            loadElevages(1, searchQuery);
        } else {
            if (result.errors) {
                let errorMessages = '';
                Object.values(result.errors).forEach(errors => {
                    errorMessages += errors.join('\n') + '\n';
                });
                errorDiv.textContent = errorMessages;
                errorDiv.style.display = 'block';
            } else {
                errorDiv.textContent = result.message || 'Erreur lors de la création';
                errorDiv.style.display = 'block';
            }
        }
    } catch (error) {
        if (error.errors) {
            let errorMessages = '';
            Object.values(error.errors).forEach(errors => {
                errorMessages += errors.join('\n') + '\n';
            });
            errorDiv.textContent = errorMessages;
            errorDiv.style.display = 'block';
        } else {
            errorDiv.textContent = error.message || 'Erreur lors de la création';
            errorDiv.style.display = 'block';
        }
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-check-square mr-2 text-white"></i> Valider';
    }
});

// ================= VOIR ÉLEVAGE =================
async function viewElevage(id) {
    try {
        const response = await fetch(`${API_URL}/elevages/${id}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
                'X-CSRF-TOKEN': CSRF_TOKEN
            }
        });

        const result = await response.json();

        if ((result.status === 'success' || result.success === true) && result.data) {
            const elevage = result.data;
            
            document.getElementById('viewNom').textContent = elevage.nom || '-';
            document.getElementById('viewType').textContent = elevage.type_elevage_label || elevage.type_elevage || '-';
            document.getElementById('viewType').className = `badge px-3 py-2 bg-${getTypeColor(elevage.type_elevage)}`;
            document.getElementById('viewLocalisation').textContent = elevage.localisation || 'Non renseignée';
            document.getElementById('viewSuperficie').textContent = elevage.superficie ? elevage.superficie + ' ha' : 'Non spécifiée';
            document.getElementById('viewDate').textContent = elevage.created_at ? new Date(elevage.created_at).toLocaleDateString('fr-FR') : 'N/A';
            document.getElementById('viewDescription').textContent = elevage.description || 'Aucune description';
            document.getElementById('viewElevageImage').src = elevage.img_url || "{{ asset('images/img-elevage.jpeg') }}";
            
            $('#voirElevageModal').modal('show');
        } else {
            showToast(result.message || 'Élevage non trouvé', 'danger');
        }
    } catch (error) {
        console.error('❌ Erreur view:', error);
        showToast('Erreur lors du chargement des détails', 'danger');
    }
}

// ================= MODIFICATION - OUVERTURE MODAL =================
async function openEditModal(id) {
    try {
        const response = await fetch(`${API_URL}/elevages/${id}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
                'X-CSRF-TOKEN': CSRF_TOKEN
            }
        });

        const result = await response.json();

        if ((result.status === 'success' || result.success === true) && result.data) {
            const elevage = result.data;
            
            currentEditId = id;
            document.getElementById('editId').value = id;
            document.getElementById('editNomElevage').value = elevage.nom || '';
            document.getElementById('editTypeElevage').value = elevage.type_elevage || 'bovins';
            document.getElementById('editLocalisation').value = elevage.localisation || '';
            document.getElementById('editSuperficie').value = elevage.superficie !== null && elevage.superficie !== undefined ? elevage.superficie : '';
            document.getElementById('editDescription').value = elevage.description || '';
            
            if (elevage.img_url) {
                document.getElementById('editImagePreview').innerHTML = `<img src="${elevage.img_url}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px;">`;
            } else {
                document.getElementById('editImagePreview').innerHTML = '🖼️';
            }
            
            document.getElementById('editPhotoInput').value = '';
            document.getElementById('editRemovePhotoBtn').style.display = 'none';
            document.getElementById('editRemovePhotoBtn').dataset.action = '';
            document.getElementById('editError').style.display = 'none';
            
            $('#modifierElevageModal').modal('show');
        } else {
            showToast(result.message || 'Élevage non trouvé', 'danger');
        }
    } catch (error) {
        console.error('❌ Erreur edit:', error);
        showToast('Erreur lors du chargement des données', 'danger');
    }
}

// ================= MODIFICATION - APERÇU IMAGE =================
document.getElementById('editPhotoInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('editImagePreview');
    const removeBtn = document.getElementById('editRemovePhotoBtn');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            preview.innerHTML = `<img src="${event.target.result}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px;">`;
            removeBtn.style.display = 'flex';
            removeBtn.dataset.action = 'replace';
        };
        reader.readAsDataURL(file);
    }
});

document.getElementById('editRemovePhotoBtn').addEventListener('click', function() {
    const preview = document.getElementById('editImagePreview');
    const input = document.getElementById('editPhotoInput');
    
    if (this.dataset.action === 'replace') {
        preview.innerHTML = '🖼️';
        input.value = '';
        this.style.display = 'none';
        this.dataset.action = '';
    } else {
        preview.innerHTML = '🖼️';
        input.value = '';
        this.style.display = 'none';
        this.dataset.action = 'delete';
    }
});

// ================= MODIFICATION - SOUMISSION =================
document.getElementById('editElevageForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('editSubmitBtn');
    const errorDiv = document.getElementById('editError');
    errorDiv.style.display = 'none';
    
    const id = parseInt(document.getElementById('editId').value);
    const nom = document.getElementById('editNomElevage').value.trim();
    const type = document.getElementById('editTypeElevage').value;
    
    if (!nom) {
        showToast('Le nom de l\'élevage est obligatoire.', 'warning');
        document.getElementById('editNomElevage').focus();
        return;
    }
    
    if (!type) {
        showToast('Le type d\'élevage est obligatoire.', 'warning');
        document.getElementById('editTypeElevage').focus();
        return;
    }
    
    const formData = new FormData();
    formData.append('nom', nom);
    formData.append('type_elevage', type);
    
    const localisation = document.getElementById('editLocalisation').value.trim();
    const superficie = document.getElementById('editSuperficie').value;
    const description = document.getElementById('editDescription').value.trim();
    
    if (localisation) formData.append('localisation', localisation);
    if (superficie !== '' && superficie !== null && superficie !== undefined) {
        formData.append('superficie', superficie);
    }
    if (description) formData.append('description', description);
    
    const photoInput = document.getElementById('editPhotoInput');
    if (photoInput.files && photoInput.files[0]) {
        formData.append('image', photoInput.files[0]);
    }
    
    const removeBtn = document.getElementById('editRemovePhotoBtn');
    if (removeBtn.dataset.action === 'delete') {
        formData.append('delete_image', 'true');
    }
    
    formData.append('_method', 'PUT');
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> En cours...';
    
    try {
        const result = await updateElevage(id, formData);
        
        if (result.status === 'success' || result.success === true) {
            $('#modifierElevageModal').modal('hide');
            showToast(result.message || 'Élevage modifié avec succès !', 'success');
            loadElevages(currentPage, searchQuery);
        } else {
            if (result.errors) {
                let errorMessages = '';
                Object.values(result.errors).forEach(errors => {
                    errorMessages += errors.join('\n') + '\n';
                });
                errorDiv.textContent = errorMessages;
                errorDiv.style.display = 'block';
            } else {
                errorDiv.textContent = result.message || 'Erreur lors de la modification';
                errorDiv.style.display = 'block';
            }
        }
    } catch (error) {
        if (error.errors) {
            let errorMessages = '';
            Object.values(error.errors).forEach(errors => {
                errorMessages += errors.join('\n') + '\n';
            });
            errorDiv.textContent = errorMessages;
            errorDiv.style.display = 'block';
        } else {
            errorDiv.textContent = error.message || 'Erreur lors de la modification';
            errorDiv.style.display = 'block';
        }
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '✅ Valider';
    }
});

// ================= SUPPRESSION =================
function openDeleteModal(id) {
    document.getElementById('deleteElevageId').value = id;
    
    const currentElevages = elevages && elevages.length > 0 ? elevages : (window.elevages || []);
    const elevage = currentElevages.find(e => e.id === id);
    
    document.getElementById('deleteElevageName').textContent = elevage 
        ? `Êtes-vous sûr de vouloir supprimer "${elevage.nom}" ?` 
        : 'Êtes-vous sûr de vouloir supprimer cet élevage ?';
    $('#supprimerElevageModal').modal('show');
}

document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
    const id = parseInt(document.getElementById('deleteElevageId').value);
    const btn = this;
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Suppression...';
    
    try {
        const result = await deleteElevage(id);
        
        if (result.status === 'success' || result.success === true) {
            $('#supprimerElevageModal').modal('hide');
            showToast(result.message || 'Élevage supprimé avec succès', 'success');
            loadElevages(currentPage, searchQuery);
        } else {
            showToast(result.message || 'Erreur lors de la suppression', 'danger');
        }
    } catch (error) {
        showToast(error.message || 'Erreur lors de la suppression', 'danger');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-trash-alt mr-2"></i> Supprimer';
    }
});

// ================= STYLES DYNAMIQUES =================
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
    
    .btn-action span { display: inline-block; }
    @media (max-width: 967.98px) {
        .btn-action span { display: none !important; }
    }
    
    .search-container {
        display: flex;
        gap: 10px;
        max-width: 400px;
        margin-bottom: 15px;
    }
    
    .bg-success { background-color: #28a745 !important; color: white !important; }
    .bg-info { background-color: #17a2b8 !important; color: white !important; }
    .bg-primary { background-color: #007bff !important; color: white !important; }
    .bg-warning { background-color: #ffc107 !important; color: #212529 !important; }
    .bg-dark { background-color: #343a40 !important; color: white !important; }
    .bg-secondary { background-color: #6c757d !important; color: white !important; }
`;
document.head.appendChild(style);

// ================= INITIALISATION =================
document.addEventListener('DOMContentLoaded', function() {
    console.log('📋 Page Élevages chargée, chargement des données...');
    loadElevages(1, '');
});

// ================================================================
// CORRECTION : Forcer la fermeture des modals via les boutons
// (croix et "Annuler") qui ne fonctionnaient pas
// ================================================================
document.addEventListener('DOMContentLoaded', function() {
    // Sélectionner tous les boutons de fermeture (data-dismiss ou data-bs-dismiss)
    const closeButtons = document.querySelectorAll('[data-dismiss="modal"], [data-bs-dismiss="modal"]');
    
    closeButtons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            // Empêcher le comportement par défaut
            e.preventDefault();
            // Trouver le modal parent
            const modal = this.closest('.modal');
            if (modal) {
                // Fermer le modal via Bootstrap (jQuery)
                $(modal).modal('hide');
            }
        });
    });
});

</script>


@endsection
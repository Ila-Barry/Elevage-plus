{{-- resources/views/animaux.blade.php --}}

@extends('layouts.menu')

@section('title', 'Gestion des Animaux')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/eleveurCSS/animaux.css') }}">
@endpush

@section('content')

<div class="animaux-container">
    
    <div class="page-header">
        <div>
            <h2>GESTION DES ANIMAUX</h2>
            <span class="total-count" id="totalCount">0 animaux</span>
        </div>

        <div class="header-actions">
            <button class="btn-add-animal" id="openAddModal">
                <i class="fas fa-plus"></i>
                Ajouter un animal
            </button>

            <div class="search-box">
                <input type="text" id="searchInput" placeholder="rechercher..." value="{{ request('search') }}">
                <button id="searchBtn">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="filters-section">
        <span class="filter-label">Filtres :</span>
        <select id="filterType">
            <option value="all">Tous</option>
            <option value="bovin">Bovin</option>
            <option value="ovin">Ovin</option>
            <option value="caprin">Caprin</option>
            <option value="volaille">Volaille</option>
            <option value="autre">Autre</option>
        </select>

        <select id="filterHealth">
            <option value="all">Santé</option>
            <option value="bon">Bonne</option>
            <option value="a_surveiller">À surveiller</option>
            <option value="malade">Malade</option>
            <option value="critique">Critique</option>
        </select>

        <select id="filterAge">
            <option value="all">Âge</option>
            <option value="0-1">Moins d'1 an</option>
            <option value="1-3">1-3 ans</option>
            <option value="3-5">3-5 ans</option>
            <option value="5+">Plus de 5 ans</option>
        </select>

        <button class="btn-clear-filters btn-filtre" id="clearFilters">
            <i class="fas fa-times"></i> Effacer les filtres
        </button>
    </div>

    <div class="animals-list" id="animalsList">
        <div class="text-center py-5">
            <div class="spinner-border text-success" role="status">
                <span class="sr-only">Chargement...</span>
            </div>
            <p class="mt-2 text-muted">Chargement des animaux...</p>
        </div>
    </div>

    <div class="pagination-section">
        <div class="pagination-custom" id="pagination">
            <button id="prevPage" disabled><i class="fas fa-angle-left"></i></button>
            <div id="pageNumbers" class="page-numbers"></div>
            <button id="nextPage"><i class="fas fa-angle-right"></i></button>
        </div>

        <div class="pagination-info" id="paginationInfo">
            Affichage : 0/0
        </div>
    </div>
</div>

<div id="detailAnimalModal" class="modal">
    <div class="modal-content modal-detail">
        <div class="modal-header">
            <h2>
                <i class="fas fa-info-circle" style="color: #198754; margin-right: 10px;"></i>
                DÉTAIL DE L'ANIMAL : <span id="detailAnimalNom">-</span>
            </h2>
            <span class="modal-close" onclick="closeModal('detailAnimalModal')">&times;</span>
        </div>
        <div class="modal-body">
            <div class="detail-container">
                <div class="detail-left">
                    <div class="detail-photo">
                        <img id="detailAnimalImage" src="" alt="Animal" onerror="this.src='https://images.unsplash.com/photo-1516467508483-a7212febe31a?w=400'">
                        <div class="detail-photo-overlay" id="detailPhotoOverlay">
                            <span id="detailSexeBadge" class="sexe-badge">♂</span>
                        </div>
                    </div>
                    <div class="info-section">
                        <h3><i class="fas fa-info-circle"></i> INFORMATIONS GÉNÉRALES</h3>
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Nom :</span>
                                <span class="info-value" id="detailNom">-</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Espèce :</span>
                                <span class="info-value" id="detailEspece">-</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Race :</span>
                                <span class="info-value" id="detailRace">-</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Âge :</span>
                                <span class="info-value" id="detailAge">-</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Poids :</span>
                                <span class="info-value" id="detailPoids">-</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Sexe :</span>
                                <span class="info-value" id="detailSexe">-</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Statut sanitaire :</span>
                                <span class="info-value">
                                    <span class="badge-sante-detail" id="detailStatutSanitaire">-</span>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Élevage :</span>
                                <span class="info-value" id="detailElevage">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="detail-right">
                    <div class="tasks-section">
                        <h3><i class="fas fa-history"></i> HISTORIQUE DES TÂCHES</h3>
                        <div class="tasks-list" id="detailHistoriqueTaches">
                            </div>
                    </div>

                    <div class="weight-chart-section">
                        <h3><i class="fas fa-chart-line"></i> COURBE DE POIDS</h3>
                        <div class="chart-container">
                            <canvas id="weightChart" width="400" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="action-buttons">
                <button class="action-btn btn-modify" id="detailModifyBtn">
                    <i class="fas fa-edit"></i> Modifier l'animal
                </button>
                <button class="action-btn btn-delete" id="detailDeleteBtn">
                    <i class="fas fa-trash-alt"></i> Supprimer
                </button>
            </div>
        </div>
    </div>
</div>

<div id="addAnimalModal" class="modal">
    <div class="modal-content">
        <div class="modal-header mt-3">
            <h2>
                <i class="fas fa-paw" style="color: #198754; margin-right: 10px;"></i>
                AJOUTER UN ANIMAL
            </h2>
            <span class="modal-close" onclick="closeModal('addAnimalModal')">&times;</span>
        </div>
        <div class="modal-body">
            <div id="addError" class="alert alert-danger" style="display: none;"></div>

            <form id="addAnimalForm" enctype="multipart/form-data">
                @csrf
                <div class="form-section-box p-3 mb-3 rounded">
                    <label class="form-label font-weight-bold mb-2">
                        <i class="far fa-image text-success mr-1"></i> Photo <span class="font-weight-normal text-muted">(optionnelle)</span>
                    </label>
                    <div class="photo-actions-wrapper">
                        <div class="image-preview-placeholder d-flex align-items-center justify-content-center rounded border border-dashed" id="addImagePreview">
                            <i class="far fa-image fa-2x text-muted"></i>
                        </div>
                        <label class="btn btn-outline-success btn-photo-action mb-0 d-flex align-items-center justify-content-center cursor-pointer">
                            <i class="far fa-image mr-2"></i> Choisir une image
                            <input type="file" name="image" id="addAnimalImage" class="d-none" accept="image/*">
                        </label>
                        <button type="button" class="btn btn-outline-danger btn-photo-action d-flex align-items-center justify-content-center" id="addRemoveImage" style="display: none;">
                            <i class="fas fa-times mr-2"></i> Supprimer
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label>NOM *</label>
                    <input type="text" name="nom" id="addNom" placeholder="nom animal" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>ESPÈCE *</label>
                    <select name="espece" id="addEspece" class="form-control" required>
                        <option value="">Sélectionnez...</option>
                        <option value="bovin">Bovin</option>
                        <option value="ovin">Ovin</option>
                        <option value="caprin">Caprin</option>
                        <option value="volaille">Volaille</option>
                        <option value="autre">Autre</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>RACE</label>
                    <input type="text" name="race" id="addRace" placeholder="race animal" class="form-control">
                </div>

                <div class="form-group">
                    <label>DATE DE NAISSANCE *</label>
                    <input type="date" name="date_naissance" id="addDateNaissance" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>POIDS (kg) *</label>
                    <input type="number" step="0.1" name="poids" id="addPoids" placeholder="poids animal" class="form-control" min="0" required>
                </div>

                <div class="form-group">
                    <label>STATUT SANITAIRE</label>
                    <select name="statut_sanitaire" id="addSante" class="form-control">
                        <option value="">Sélectionnez...</option>
                        <option value="bon">Bonne</option>
                        <option value="a_surveiller">À surveiller</option>
                        <option value="malade">Malade</option>
                        <option value="critique">Critique</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>SEXE *</label>
                    <select name="sexe" id="addSexe" class="form-control" required>
                        <option value="">Sélectionnez...</option>
                        <option value="male">Mâle</option>
                        <option value="femelle">Femelle</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>ÉLEVAGE *</label>
                    <select name="elevage_id" id="addElevage" class="form-control" required>
                        <option value="">Sélectionnez un élevage...</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>NOTE <span class="optional">(optionnelle)</span></label>
                    <textarea name="notes" id="addNote" rows="3" placeholder="description de l'animal" class="form-control"></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal('addAnimalModal')">Annuler</button>
                    <button type="submit" class="btn-save" id="addSubmitBtn">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="editAnimalModal" class="modal">
    <div class="modal-content">
        <div class="modal-header mt-3">
            <h2>
                <i class="fas fa-edit" style="color: #198754; margin-right: 10px;"></i>
                MODIFIER L'ANIMAL
            </h2>
            <span class="modal-close" onclick="closeModal('editAnimalModal')">&times;</span>
        </div>
        <div class="modal-body">
            <div id="editError" class="alert alert-danger" style="display: none;"></div>

            <form id="editAnimalForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="edit_id" id="editAnimalId">

                <div class="form-section-box p-3 mb-3 rounded">
                    <label class="form-label font-weight-bold mb-2">
                        <i class="far fa-image text-success mr-1"></i> Photo
                    </label>
                    <div class="photo-actions-wrapper">
                        <div class="image-preview-placeholder d-flex align-items-center justify-content-center rounded border border-dashed" id="editImagePreview">
                            <i class="far fa-image fa-2x text-muted"></i>
                        </div>
                        <label class="btn btn-outline-success btn-photo-action mb-0 d-flex align-items-center justify-content-center cursor-pointer">
                            <i class="far fa-image mr-2"></i> Choisir une image
                            <input type="file" name="image" id="editAnimalImage" class="d-none" accept="image/*">
                        </label>
                        <button type="button" class="btn btn-outline-danger btn-photo-action d-flex align-items-center justify-content-center" id="editRemoveImage" style="display: none;">
                            <i class="fas fa-times mr-2"></i> Supprimer
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label>NOM *</label>
                    <input type="text" name="nom" id="editNom" placeholder="nom animal" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>ESPÈCE *</label>
                    <select name="espece" id="editEspece" class="form-control" required>
                        <option value="bovin">Bovin</option>
                        <option value="ovin">Ovin</option>
                        <option value="caprin">Caprin</option>
                        <option value="volaille">Volaille</option>
                        <option value="autre">Autre</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>RACE</label>
                    <input type="text" name="race" id="editRace" placeholder="race animal" class="form-control">
                </div>

                <div class="form-group">
                    <label>DATE DE NAISSANCE *</label>
                    <input type="date" name="date_naissance" id="editDateNaissance" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>POIDS (kg)</label>
                    <input type="number" step="0.1" name="poids" id="editPoids" placeholder="poids animal" class="form-control" min="0">
                </div>

                <div class="form-group">
                    <label>STATUT SANITAIRE</label>
                    <select name="statut_sanitaire" id="editSante" class="form-control">
                        <option value="bon">Bonne</option>
                        <option value="a_surveiller">À surveiller</option>
                        <option value="malade">Malade</option>
                        <option value="critique">Critique</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>SEXE *</label>
                    <select name="sexe" id="editSexe" class="form-control" required>
                        <option value="male">Mâle</option>
                        <option value="femelle">Femelle</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>ÉLEVAGE *</label>
                    <select name="elevage_id" id="editElevage" class="form-control" required>
                        <option value="">Sélectionnez...</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>NOTE <span class="optional">(optionnelle)</span></label>
                    <textarea name="notes" id="editNote" rows="3" placeholder="description de l'animal" class="form-control"></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal('editAnimalModal')">Annuler</button>
                    <button type="submit" class="btn-save" id="editSubmitBtn">
                        <i class="fas fa-save"></i> Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="deleteAnimalModal" class="modal">
    <div class="modal-content" style="max-width: 450px;">
        <div class="modal-header">
            <h2 style="color: #dc3545;">
                <i class="fas fa-exclamation-triangle" style="color: #dc3545; margin-right: 10px;"></i>
                SUPPRIMER L'ANIMAL
            </h2>
            <span class="modal-close" onclick="closeModal('deleteAnimalModal')">&times;</span>
        </div>
        <div class="modal-body" style="text-align: center; padding: 30px;">
            <i class="fas fa-trash-alt" style="font-size: 48px; color: #dc3545; margin-bottom: 15px;"></i>
            <h3 id="deleteAnimalName" style="margin-bottom: 10px;">Êtes-vous sûr de vouloir supprimer cet animal ?</h3>
            <p style="color: #6c757d; font-size: 14px;">Cette action est irréversible.</p>
            <input type="hidden" id="deleteAnimalId">
            <div style="display: flex; gap: 15px; justify-content: center; margin-top: 20px;">
                <button class="btn-cancel" onclick="closeModal('deleteAnimalModal')" style="padding: 10px 30px;">Annuler</button>
                <button class="btn-save" id="confirmDeleteBtn" style="background: #dc3545; padding: 10px 30px;">
                    <i class="fas fa-trash-alt"></i> Supprimer
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// ================= CONFIGURATION & FIX AUTHENTIFICATION =================
const API_URL = window.location.origin + '/api';
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '';
const rawToken = localStorage.getItem('access_token');
const token = rawToken ? rawToken.replace(/^"(.*)"$/, '$1') : null;

console.log('🔍 Configuration:', { API_URL, token: token ? '✅ Présent' : '❌ Absent' });

// ================= VARIABLES GLOBALES =================
let animals = [];
let currentPage = 1;
let totalPages = 1;
const itemsPerPage = 6;
let currentViewId = null;
let toastTimeout = null;
let searchQuery = '';
let filterType = 'all';
let filterHealth = 'all';
let filterAge = 'all';

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

// ================= FONCTIONS MODALES =================
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) closeModal(this.id);
    });
});

// ================= API CALLS =================
async function handleResponse(response) {
    const result = await response.json();
    if (response.status === 401) {
        localStorage.removeItem('access_token');
        showToast('Session expirée. Redirection...', 'danger');
        setTimeout(() => window.location.href = '/auth/login', 2000);
        throw result;
    }
    if (!response.ok) throw result;
    return result;
}

async function fetchAnimals(page = 1, search = '', type = 'all', health = 'all', age = 'all') {
    try {
        let url = `${API_URL}/animaux?page=${page}&per_page=${itemsPerPage}`;
        if (search) url += `&search=${encodeURIComponent(search)}`;
        if (type !== 'all') url += `&espece=${encodeURIComponent(type)}`;
        if (health !== 'all') url += `&statut_sanitaire=${encodeURIComponent(health)}`;
        
        if (age !== 'all') {
            switch(age) {
                case '0-1': url += `&age_max=12`; break;
                case '1-3': url += `&age_min=12&age_max=36`; break;
                case '3-5': url += `&age_min=36&age_max=60`; break;
                case '5+': url += `&age_min=60`; break;
            }
        }
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
                'X-CSRF-TOKEN': CSRF_TOKEN
            }
        });
        return await handleResponse(response);
    } catch (error) {
        console.error('❌ Erreur fetch animaux:', error);
        throw error;
    }
}

async function fetchElevages() {
    try {
        const response = await fetch(`${API_URL}/elevages?per_page=50`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
                'X-CSRF-TOKEN': CSRF_TOKEN
            }
        });
        return await handleResponse(response);
    } catch (error) {
        console.error('❌ Erreur fetch elevages:', error);
        throw error;
    }
}

// ================= GESTION DU PREVIEW DES IMAGES =================
function setupImagePreview(inputId, previewId, removeBtnId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    const removeBtn = document.getElementById(removeBtnId);

    if (!input || !preview || !removeBtn) return;

    input.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" style="max-width:100%; max-height:100%; object-fit:cover;" class="rounded">`;
                removeBtn.style.display = 'flex';
            };
            reader.readAsDataURL(file);
        }
    });

    removeBtn.addEventListener('click', function() {
        input.value = '';
        preview.innerHTML = `<i class="far fa-image fa-2x text-muted"></i>`;
        removeBtn.style.display = 'none';
    });
}

// ================= CHARGEMENT GLOBAL =================
async function loadAnimals(page = 1) {
    try {
        const result = await fetchAnimals(page, searchQuery, filterType, filterHealth, filterAge);
        if (result.success || result.status === 'success') {
            animals = result.data.data || [];
            currentPage = result.data.meta?.current_page || 1;
            totalPages = result.data.meta?.last_page || 1;
            renderAnimals(animals);
        }
    } catch (error) {
        document.getElementById('animalsList').innerHTML = `<div class="alert alert-danger text-center">Erreur de chargement des données.</div>`;
    }
}

async function loadElevagesForSelects() {
    try {
        const result = await fetchElevages();
        const elevages = result.data?.data || result.data || [];
        const selectOptions = '<option value="">Sélectionnez un élevage...</option>' + 
            elevages.map(el => `<option value="${el.id}">${el.nom}</option>`).join('');
        
        const addSelect = document.getElementById('addElevage');
        const editSelect = document.getElementById('editElevage');
        if (addSelect) addSelect.innerHTML = selectOptions;
        if (editSelect) editSelect.innerHTML = selectOptions;
    } catch (error) {
        console.error('Erreur chargement selects élevages:', error);
    }
}

// ================= AFFICHAGE DES ANIMAUX =================
function renderAnimals(data) {
    const container = document.getElementById('animalsList');
    if (!data || data.length === 0) {
        container.innerHTML = `<div class="empty-state text-center py-5"><h4>Aucun animal trouvé</h4></div>`;
        document.getElementById('paginationInfo').textContent = 'Affichage : 0/0';
        document.getElementById('totalCount').textContent = '0 animaux';
        return;
    }
    
    container.innerHTML = data.map(animal => {
        const sexeIcon = animal.sexe === 'male' ? '♂' : '♀';
        const sexeColor = animal.sexe === 'male' ? '#2196F3' : '#E91E63';
        
        // Sécurité anti-404 pour l'affichage de la carte
        const fallbackImg = 'https://images.unsplash.com/photo-1516467508483-a7212febe31a?w=400';
        const animalImg = (animal.img_url && !animal.img_url.includes('default-goat.jpg')) ? animal.img_url : fallbackImg;

        return `
        <div class="animal-card" data-id="${animal.id}">
            <div class="animal-image">
                <img src="${animalImg}" alt="${animal.nom}" onerror="this.src='${fallbackImg}'">
                <span class="sexe-badge-small" style="background: ${sexeColor};">${sexeIcon}</span>
            </div>
            <div class="animal-info">
                <h5>NOM : ${animal.nom.toUpperCase()}</h5>
                <div class="animal-details">
                    <div><i class="fas fa-tag"></i> ${animal.espece_label || animal.espece}</div>
                    <div><i class="fas fa-birthday-cake"></i> ${formatAge(animal.age)}</div>
                    <div><i class="fas fa-weight-hanging"></i> ${animal.poids} kg</div>
                    <div><span class="badge-sante ${animal.statut_sanitaire}">${animal.statut_sanitaire_label || animal.statut_sanitaire}</span></div>
                </div>
            </div>
            <div class="animal-actions">
                <button class="btn-detail" onclick="viewAnimal(${animal.id})"><i class="fas fa-eye"></i> Détail</button>
                <button class="btn-edit" onclick="openEditModal(${animal.id})"><i class="fas fa-pen"></i> Modifier</button>
                <button class="btn-delete" onclick="openDeleteModal(${animal.id}, '${animal.nom}')"><i class="fas fa-trash"></i> Supprimer</button>
            </div>
        </div>`;
    }).join('');
    
    document.getElementById('totalCount').textContent = data.length + ' animaux';
    updatePaginationDOM();
}

function formatAge(age) {
    if (!age) return 'N/A';
    if (typeof age === 'string' || typeof age === 'number') return age + ' mois';
    const years = Math.floor(age.annees || 0);
    const months = age.mois || 0;
    if (years === 0) return months + ' mois';
    return `${years} an(s) ${months} mois`;
}

// ================= PAGINATION ET FILTRES =================
function updatePaginationDOM() {
    const pageNumbers = document.getElementById('pageNumbers');
    document.getElementById('prevPage').disabled = currentPage === 1;
    document.getElementById('nextPage').disabled = currentPage === totalPages || totalPages === 0;
    document.getElementById('paginationInfo').textContent = `Affichage : Page ${currentPage} / ${totalPages}`;
    
    let html = '';
    for (let i = 1; i <= totalPages; i++) {
        html += `<button class="page-num ${i === currentPage ? 'active' : ''}" onclick="changePage(${i})">${i}</button>`;
    }
    pageNumbers.innerHTML = html;
}

window.changePage = function(page) {
    currentPage = page;
    loadAnimals(page);
};

document.getElementById('prevPage').addEventListener('click', () => { if (currentPage > 1) changePage(currentPage - 1); });
document.getElementById('nextPage').addEventListener('click', () => { if (currentPage < totalPages) changePage(currentPage + 1); });

// ================= ACTIONS FILTRES =================
document.getElementById('searchBtn').addEventListener('click', () => {
    searchQuery = document.getElementById('searchInput').value;
    currentPage = 1;
    loadAnimals(1);
});

document.getElementById('filterType').addEventListener('change', (e) => { filterType = e.target.value; changePage(1); });
document.getElementById('filterHealth').addEventListener('change', (e) => { filterHealth = e.target.value; changePage(1); });
document.getElementById('filterAge').addEventListener('change', (e) => { filterAge = e.target.value; changePage(1); });
document.getElementById('clearFilters').addEventListener('click', () => {
    document.getElementById('filterType').value = 'all';
    document.getElementById('filterHealth').value = 'all';
    document.getElementById('filterAge').value = 'all';
    document.getElementById('searchInput').value = '';
    searchQuery = ''; filterType = 'all'; filterHealth = 'all'; filterAge = 'all';
    changePage(1);
});

// ================= ACTIONS CRUD MODALES =================
document.getElementById('openAddModal').addEventListener('click', () => {
    document.getElementById('addAnimalForm').reset();
    document.getElementById('addImagePreview').innerHTML = `<i class="far fa-image fa-2x text-muted"></i>`;
    document.getElementById('addRemoveImage').style.display = 'none';
    document.getElementById('addError').style.display = 'none';
    openModal('addAnimalModal');
});

document.getElementById('addAnimalForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    try {
        const response = await fetch(`${API_URL}/animaux`, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token, 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: formData
        });
        await handleResponse(response);
        closeModal('addAnimalModal');
        showToast('Animal ajouté avec succès !', 'success');
        loadAnimals(1);
    } catch (error) {
        document.getElementById('addError').textContent = error.message || 'Erreur lors de l\'enregistrement';
        document.getElementById('addError').style.display = 'block';
    }
});


// ================= GRAPHIQUE DE POIDS =================
function drawWeightChart(animal) {
    const canvas = document.getElementById('weightChart');
    if (!canvas) {
        console.warn('Canvas weightChart non trouvé');
        return;
    }
    
    const ctx = canvas.getContext('2d');
    
    // ✅ Détruire l'ancien graphique s'il existe
    if (window.weightChartInstance) {
        window.weightChartInstance.destroy();
    }
    
    // ✅ Vérifier que Chart.js est chargé
    if (typeof Chart === 'undefined') {
        console.warn('Chart.js non chargé');
        // Afficher un message alternatif
        const container = canvas.parentElement;
        container.innerHTML = `
            <div style="text-align: center; padding: 20px; color: #6c757d;">
                <i class="fas fa-chart-line" style="font-size: 24px; margin-bottom: 10px; display: block;"></i>
                <p>Graphique non disponible</p>
                <small>Veuillez recharger la page</small>
            </div>
        `;
        return;
    }
    
    // ✅ Générer des données simulées basées sur le poids actuel
    const currentWeight = animal.poids || 100;
    const data = [];
    const labels = [];
    
    // Créer 6 points de données (simulés)
    for (let i = 5; i >= 0; i--) {
        const variation = (Math.random() - 0.5) * 15;
        const weight = Math.max(10, currentWeight + variation - i * 2);
        data.push(Math.round(weight * 10) / 10);
        const date = new Date();
        date.setMonth(date.getMonth() - i);
        labels.push(date.toLocaleDateString('fr-FR', { month: 'short', day: 'numeric' }));
    }
    
    // ✅ Créer le graphique
    window.weightChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Poids (kg)',
                data: data,
                borderColor: '#198754',
                backgroundColor: 'rgba(25, 135, 84, 0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#198754',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + ' kg';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    },
                    ticks: {
                        callback: function(value) {
                            return value + ' kg';
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
}

// ================= VOIR ANIMAL =================
window.viewAnimal = async function(id) {
    try {
        const response = await fetch(`${API_URL}/animaux/${id}`, {
            method: 'GET',
            headers: { 
                'Accept': 'application/json', 
                'Authorization': 'Bearer ' + token,
                'X-CSRF-TOKEN': CSRF_TOKEN
            }
        });
        const result = await handleResponse(response);
        const a = result.data;
        
        // Informations générales
        document.getElementById('detailAnimalNom').textContent = a.nom.toUpperCase();
        document.getElementById('detailNom').textContent = a.nom;
        document.getElementById('detailEspece').textContent = a.espece_label || a.espece;
        document.getElementById('detailRace').textContent = a.race || '-';
        document.getElementById('detailAge').textContent = formatAge(a.age);
        document.getElementById('detailPoids').textContent = a.poids + ' kg';
        document.getElementById('detailSexe').textContent = a.sexe === 'male' ? 'Mâle' : 'Femelle';
        
        const badgeSante = document.getElementById('detailStatutSanitaire');
        badgeSante.textContent = a.statut_sanitaire_label || a.statut_sanitaire;
        badgeSante.className = `badge-sante-detail ${a.statut_sanitaire}`;

        document.getElementById('detailElevage').textContent = a.elevage?.nom || '-';
        
        const fallbackImg = 'https://images.unsplash.com/photo-1516467508483-a7212febe31a?w=400';
        document.getElementById('detailAnimalImage').src = (a.img_url && !a.img_url.includes('default-goat.jpg')) ? a.img_url : fallbackImg;
        document.getElementById('detailSexeBadge').textContent = a.sexe === 'male' ? '♂' : '♀';
        
        // ✅ Historique des tâches
        const tasksContainer = document.getElementById('detailHistoriqueTaches');
        if (a.statistiques?.historique_taches && a.statistiques.historique_taches.length > 0) {
            tasksContainer.innerHTML = a.statistiques.historique_taches.map(task => `
                <div class="task-item ${task.terminee ? 'task-done' : ''}">
                    <span class="task-type">${task.type_icone || '📋'} ${task.type_label || task.type}</span>
                    <span class="task-title">${task.titre}</span>
                    <span class="task-date">${task.date_planifiee}</span>
                    <span class="task-status ${task.terminee ? 'status-done' : 'status-pending'}">
                        ${task.terminee ? '✅ Fait' : '⏳ En attente'}
                    </span>
                </div>
            `).join('');
        } else {
            tasksContainer.innerHTML = '<p style="text-align: center; color: #6c757d; padding: 10px;">Aucune tâche enregistrée</p>';
        }
        
        // ✅ Graphique de poids
        drawWeightChart(a);
        
        // Boutons d'action
        document.getElementById('detailModifyBtn').onclick = () => { 
            closeModal('detailAnimalModal'); 
            setTimeout(() => openEditModal(a.id), 300);
        };
        document.getElementById('detailDeleteBtn').onclick = () => { 
            closeModal('detailAnimalModal'); 
            setTimeout(() => openDeleteModal(a.id, a.nom), 300);
        };

        openModal('detailAnimalModal');
    } catch (e) { 
        showToast('Impossible de charger le détail', 'danger');
        console.error('Erreur viewAnimal:', e);
    }
};

// ================= MODIFICATION CORRIGÉE (ANTI-404) =================
window.openEditModal = async function(id) {
    try {
        const response = await fetch(`${API_URL}/animaux/${id}`, {
            method: 'GET',
            headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token }
        });
        const result = await handleResponse(response);
        const a = result.data;

        document.getElementById('editAnimalId').value = a.id;
        document.getElementById('editNom').value = a.nom;
        document.getElementById('editEspece').value = a.espece;
        document.getElementById('editRace').value = a.race || '';
        document.getElementById('editDateNaissance').value = a.date_naissance || '';
        document.getElementById('editPoids').value = a.poids;
        document.getElementById('editSante').value = a.statut_sanitaire;
        document.getElementById('editSexe').value = a.sexe;
        document.getElementById('editElevage').value = a.elevage_id || '';
        document.getElementById('editNote').value = a.notes || '';
        document.getElementById('editError').style.display = 'none';

        // Remise à zéro de l'input pour éviter tout résidu de sélection précédente
        document.getElementById('editAnimalImage').value = '';

        const preview = document.getElementById('editImagePreview');
        const removeBtn = document.getElementById('editRemoveImage');
        
        // Anti-404 : On n'affiche le rendu d'image que si l'image est valide et n'est pas celle par défaut
        if (a.img_url && !a.img_url.includes('default-goat.jpg')) {
            preview.innerHTML = `<img src="${a.img_url}" style="max-width:100%; max-height:100%; object-fit:cover;" class="rounded" onerror="this.src='https://images.unsplash.com/photo-1516467508483-a7212febe31a?w=400'">`;
            removeBtn.style.display = 'flex';
        } else {
            preview.innerHTML = `<i class="far fa-image fa-2x text-muted"></i>`;
            removeBtn.style.display = 'none';
        }

        openModal('editAnimalModal');
    } catch (e) { showToast('Erreur lors de la récupération des données', 'danger'); }
};

// ================= SOUMISSION MODIFICATION CORRIGÉE (ANTI-422) =================
// ================= SOUMISSION MODIFICATION =================
document.getElementById('editAnimalForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const id = document.getElementById('editAnimalId').value;
    const formData = new FormData(this);
    
    // ✅ CORRECTION : Ajouter _method pour Laravel
    formData.append('_method', 'PUT');
    
    // ✅ CORRECTION : Si pas de nouvelle image, supprimer le champ image
    const imageInput = document.getElementById('editAnimalImage');
    if (!imageInput.files || imageInput.files.length === 0) {
        formData.delete('image');
    }
    
    const submitBtn = document.getElementById('editSubmitBtn');
    const errorDiv = document.getElementById('editError');
    errorDiv.style.display = 'none';
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> En cours...';
    
    try {
        // ✅ CORRECTION : Utiliser POST avec _method=PUT
        const response = await fetch(`${API_URL}/animaux/${id}`, {
            method: 'POST',  // ← POST pour les fichiers, avec _method=PUT
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: formData
        });
        
        const result = await response.json();
        
        if (!response.ok) {
            throw result;
        }
        
        closeModal('editAnimalModal');
        showToast('Animal modifié avec succès !', 'success');
        loadAnimals(currentPage);
    } catch (error) {
        console.error('Erreur modification:', error);
        let errorMsg = error.message || 'Erreur lors de la modification';
        if (error.errors) {
            errorMsg = Object.values(error.errors).flat().join('<br>');
        }
        errorDiv.innerHTML = errorMsg;
        errorDiv.style.display = 'block';
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save"></i> Mettre à jour';
    }
});

window.openDeleteModal = function(id, name = 'cet animal') {
    document.getElementById('deleteAnimalId').value = id;
    document.getElementById('deleteAnimalName').textContent = `Êtes-vous sûr de vouloir supprimer l'animal "${name.toUpperCase()}" ?`;
    openModal('deleteAnimalModal');
};

document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
    const id = document.getElementById('deleteAnimalId').value;
    try {
        const response = await fetch(`${API_URL}/animaux/${id}`, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token, 'X-CSRF-TOKEN': CSRF_TOKEN }
        });
        await handleResponse(response);
        closeModal('deleteAnimalModal');
        showToast('Animal supprimé avec succès.', 'success');
        loadAnimals(currentPage);
    } catch (e) { showToast('Erreur lors de la suppression', 'danger'); }
});

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
    if (!token) {
        showToast('Non connecté. Redirection...', 'danger');
        window.location.href = '/auth/login';
        return;
    }
    
    // Lancement des écouteurs pour les aperçus d'images
    setupImagePreview('addAnimalImage', 'addImagePreview', 'addRemoveImage');
    setupImagePreview('editAnimalImage', 'editImagePreview', 'editRemoveImage');
    
    loadElevagesForSelects();
    loadAnimals(1);
});
</script>
@endsection
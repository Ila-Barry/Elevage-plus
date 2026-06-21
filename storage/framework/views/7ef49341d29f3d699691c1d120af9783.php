<?php $__env->startSection('title', 'Gestion des animaux'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/eleveurCSS/animaux.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<div class="animals-page">

    <!-- HEADER -->
    <div class="page-header">
        <div>
            <h2>GESTION DES ANIMAUX</h2>
        </div>

        <div class="header-actions">
            <button class="btn-add-animal" id="openAddModal">
                <i class="fas fa-plus"></i>
                Ajouter un animal
            </button>

            <div class="search-box">
                <input type="text" id="searchInput" placeholder="rechercher...">
                <button id="searchBtn">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- FILTRES -->
    <div class="filters-section">
        <span class="filter-label">Filtres :</span>
        <select id="filterType">
            <option value="all">Tous</option>
            <option value="bovin">Bovin</option>
            <option value="ovin">Ovin</option>
            <option value="caprin">Caprin</option>
            <option value="volaille">Volaille</option>
        </select>

        <select id="filterHealth">
            <option value="all">Santé</option>
            <option value="bonne">Bonne</option>
            <option value="moyenne">Moyenne</option>
            <option value="critique">Critique</option>
        </select>

        <select id="filterAge">
            <option value="all">Âge</option>
            <option value="0-1">Moins d'1 an</option>
            <option value="1-3">1-3 ans</option>
            <option value="3-5">3-5 ans</option>
            <option value="5+">Plus de 5 ans</option>
        </select>
    </div>

    <!-- LISTE DES ANIMAUX -->
    <div class="animals-list" id="animalsList">
        <!-- Les cartes seront générées par JavaScript -->
    </div>

    <!-- PAGINATION -->
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

<!-- ================= MODALE DÉTAIL ================= -->
<div id="detailAnimalModal" class="modal">
    <div class="modal-content modal-detail">
        <div class="modal-header">
            <h2>
                <i class="fas fa-info-circle" style="color: #198754; margin-right: 10px;"></i>
                DÉTAIL DE L'ANIMAL : <span id="detailAnimalNom">MARGUERITE</span>
            </h2>
            <span class="modal-close" onclick="closeModal('detailAnimalModal')">&times;</span>
        </div>
        <div class="modal-body">
            <div class="detail-container">
                <div class="detail-left">
                    <div class="detail-photo">
                        <img id="detailAnimalImage" src="" alt="Animal">
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
                            <!-- Généré par JS -->
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
                <button class="action-btn btn-add-task" id="detailAddTaskBtn">
                    <i class="fas fa-plus-circle"></i> Ajouter une tâche
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODALE AJOUTER ================= -->
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
            <div class="modal-logo">
                <span class="logo-text">ÉLEVAGE<span style="color: #198754;">+</span></span>
            </div>

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
                        <input type="file" id="addAnimalImage" class="d-none" accept="image/*">
                    </label>
                    <button type="button" class="btn btn-outline-danger btn-photo-action d-flex align-items-center justify-content-center" id="addRemoveImage">
                        <i class="fas fa-times mr-2"></i> Supprimer
                    </button>
                </div>
            </div>

            <form id="addAnimalForm">
                <div class="form-group">
                    <label>NOM *</label>
                    <input type="text" id="addNom" placeholder="nom animal" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>ESPÈCE *</label>
                    <select id="addEspece" class="form-control" required>
                        <option value="">Sélectionnez...</option>
                        <option value="bovin">Bovin</option>
                        <option value="ovin">Ovin</option>
                        <option value="caprin">Caprin</option>
                        <option value="volaille">Volaille</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>RACE</label>
                    <input type="text" id="addRace" placeholder="race animal" class="form-control">
                </div>

                <div class="form-group">
                    <label>ÂGE (mois)</label>
                    <input type="number" id="addAge" placeholder="âge en mois" class="form-control" min="0">
                </div>

                <div class="form-group">
                    <label>POIDS (kg)</label>
                    <input type="number" step="0.1" id="addPoids" placeholder="poids animal" class="form-control" min="0">
                </div>

                <div class="form-group">
                    <label>SANTÉ</label>
                    <select id="addSante" class="form-control">
                        <option value="bonne">Bonne</option>
                        <option value="moyenne">Moyenne</option>
                        <option value="critique">Critique</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>ÉLEVAGE</label>
                    <select id="addElevage" class="form-control">
                        <option value="">Sélectionnez...</option>
                        <option value="Ferme des Monts">Ferme des Monts</option>
                        <option value="Vallée Verte">Vallée Verte</option>
                        <option value="Prairie Fleurie">Prairie Fleurie</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>NOTE <span class="optional">(optionnelle)</span></label>
                    <textarea id="addNote" rows="3" placeholder="description de l'animal" class="form-control"></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal('addAnimalModal')">Annuler</button>
                    <button type="submit" class="btn-save">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODALE MODIFIER ================= -->
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
            <div class="modal-logo">
                <span class="logo-text">ÉLEVAGE<span style="color: #198754;">+</span></span>
            </div>

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
                        <input type="file" id="editAnimalImage" class="d-none" accept="image/*">
                    </label>
                    <button type="button" class="btn btn-outline-danger btn-photo-action d-flex align-items-center justify-content-center" id="editRemoveImage">
                        <i class="fas fa-times mr-2"></i> Supprimer
                    </button>
                </div>
            </div>

            <form id="editAnimalForm">
                <input type="hidden" id="editAnimalId">
                <div class="form-group">
                    <label>NOM *</label>
                    <input type="text" id="editNom" placeholder="nom animal" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>ESPÈCE *</label>
                    <select id="editEspece" class="form-control" required>
                        <option value="">Sélectionnez...</option>
                        <option value="bovin">Bovin</option>
                        <option value="ovin">Ovin</option>
                        <option value="caprin">Caprin</option>
                        <option value="volaille">Volaille</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>RACE</label>
                    <input type="text" id="editRace" placeholder="race animal" class="form-control">
                </div>

                <div class="form-group">
                    <label>ÂGE (mois)</label>
                    <input type="number" id="editAge" placeholder="âge en mois" class="form-control" min="0">
                </div>

                <div class="form-group">
                    <label>POIDS (kg)</label>
                    <input type="number" step="0.1" id="editPoids" placeholder="poids animal" class="form-control" min="0">
                </div>

                <div class="form-group">
                    <label>SANTÉ</label>
                    <select id="editSante" class="form-control">
                        <option value="bonne">Bonne</option>
                        <option value="moyenne">Moyenne</option>
                        <option value="critique">Critique</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>ÉLEVAGE</label>
                    <select id="editElevage" class="form-control">
                        <option value="">Sélectionnez...</option>
                        <option value="Ferme des Monts">Ferme des Monts</option>
                        <option value="Vallée Verte">Vallée Verte</option>
                        <option value="Prairie Fleurie">Prairie Fleurie</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>NOTE <span class="optional">(optionnelle)</span></label>
                    <textarea id="editNote" rows="3" placeholder="description de l'animal" class="form-control"></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal('editAnimalModal')">Annuler</button>
                    <button type="submit" class="btn-save">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODALE SUPPRESSION ================= -->
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
            <p style="color: #6c757d; font-size: 14px;">Cette action est irréversible. Toutes les données associées seront supprimées.</p>
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
// ================= DONNÉES =================
let animals = [
    {
        id: 1,
        nom: "Marguerite",
        espece: "bovin",
        race: "Brune",
        age: 48,
        poids: 450,
        sante: "bonne",
        elevage: "Ferme des Monts",
        note: "Vache laitière très productive",
        image: "https://images.unsplash.com/photo-1516467508483-a7212febe31a?w=400",
        tasks: [
            { type: "Vaccination", date: "10/03/2025" },
            { type: "Poids", date: "430 kg" },
            { type: "Vermifuge", date: "15/01/2025" },
            { type: "Prochaine vaccination", date: "15/06/2025" }
        ],
        weightHistory: [350, 370, 390, 410, 430, 450]
    },
    {
        id: 2,
        nom: "Blanchette",
        espece: "bovin",
        race: "Brune",
        age: 18,
        poids: 250,
        sante: "bonne",
        elevage: "Vallée Verte",
        note: "Génisse en croissance",
        image: "https://images.unsplash.com/photo-1516467508483-a7212febe31a?w=400",
        tasks: [
            { type: "Vaccination", date: "20/02/2025" },
            { type: "Poids", date: "230 kg" },
            { type: "Prochaine vaccination", date: "20/08/2025" }
        ],
        weightHistory: [180, 200, 215, 230, 250]
    },
    {
        id: 3,
        nom: "Roussette",
        espece: "bovin",
        race: "Brune",
        age: 32,
        poids: 400,
        sante: "bonne",
        elevage: "Prairie Fleurie",
        note: "Bonne productrice de lait",
        image: "https://images.unsplash.com/photo-1516467508483-a7212febe31a?w=400",
        tasks: [
            { type: "Vaccination", date: "05/03/2025" },
            { type: "Poids", date: "380 kg" },
            { type: "Vermifuge", date: "10/02/2025" }
        ],
        weightHistory: [320, 340, 360, 380, 400]
    },
    {
        id: 4,
        nom: "Daisy",
        espece: "ovin",
        race: "Mérinos",
        age: 24,
        poids: 75,
        sante: "bonne",
        elevage: "Ferme des Monts",
        note: "Brebis reproductrice",
        image: "https://images.unsplash.com/photo-1548550023-2bdb3c5beed7?w=400",
        tasks: [
            { type: "Vaccination", date: "12/04/2025" },
            { type: "Tonte", date: "01/04/2025" }
        ],
        weightHistory: [60, 65, 70, 75]
    },
    {
        id: 5,
        nom: "Bella",
        espece: "caprin",
        race: "Saanen",
        age: 36,
        poids: 65,
        sante: "moyenne",
        elevage: "Vallée Verte",
        note: "Chèvre laitière, légère perte de poids",
        image: "https://images.unsplash.com/photo-1516467508483-a7212febe31a?w=400",
        tasks: [
            { type: "Vaccination", date: "08/04/2025" },
            { type: "Poids", date: "68 kg" },
            { type: "Consultation vétérinaire", date: "15/04/2025" }
        ],
        weightHistory: [70, 68, 66, 65]
    }
];

let nextId = 6;
let currentPage = 1;
const itemsPerPage = 3;
let currentViewId = null;
let toastTimeout = null;
let weightChart = null;

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
    document.getElementById(id).style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
    document.body.style.overflow = '';
}

// Fermer les modales en cliquant à l'extérieur
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.style.display = 'none';
            document.body.style.overflow = '';
        }
    });
});

// ================= AFFICHAGE DES ANIMAUX =================
function renderAnimals() {
    const container = document.getElementById('animalsList');
    const filtered = getFilteredAnimals();
    const totalPages = Math.ceil(filtered.length / itemsPerPage);
    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const pageItems = filtered.slice(start, end);
    
    if (pageItems.length === 0) {
        container.innerHTML = `
            <div class="empty-state" style="text-align: center; padding: 60px 20px; background: white; border-radius: 12px;">
                <i class="fas fa-paw" style="font-size: 48px; color: #6c757d; margin-bottom: 15px;"></i>
                <h4>Aucun animal trouvé</h4>
                <p style="color: #6c757d;">Aucun animal ne correspond à vos critères de recherche.</p>
            </div>
        `;
        document.getElementById('paginationInfo').textContent = 'Affichage : 0/0';
        return;
    }
    
    container.innerHTML = pageItems.map(animal => `
        <div class="animal-card" data-id="${animal.id}">
            <div class="animal-image">
                <img src="${animal.image}" alt="${animal.nom}">
            </div>
            <div class="animal-info">
                <h5>NOM : ${animal.nom.toUpperCase()}</h5>
                <div class="animal-details">
                    <div><i class="fas fa-tag"></i> ${animal.espece.charAt(0).toUpperCase() + animal.espece.slice(1)}</div>
                    <div><i class="fas fa-tag"></i> ${animal.race}</div>
                    <div><i class="fas fa-birthday-cake"></i> ${formatAge(animal.age)}</div>
                    <div><i class="fas fa-weight-hanging"></i> ${animal.poids} kg</div>
                    <div style="grid-column: 1 / -1; display: flex; align-items: center; gap: 6px;">
                        <i class="fas fa-heartbeat"></i> 
                        <span class="badge-sante ${animal.sante}">${animal.sante.charAt(0).toUpperCase() + animal.sante.slice(1)}</span>
                    </div>
                </div>
            </div>
            <div class="animal-actions">
                <button class="btn-detail" onclick="viewAnimal(${animal.id})">
                    <i class="fas fa-eye"></i> Détail
                </button>
                <button class="btn-edit" onclick="openEditModal(${animal.id})">
                    <i class="fas fa-pen"></i> Modifier
                </button>
                <button class="btn-delete" onclick="openDeleteModal(${animal.id})">
                    <i class="fas fa-trash"></i> Supprimer
                </button>
            </div>
        </div>
    `).join('');
    
    updatePagination(filtered.length);
}

function formatAge(months) {
    if (months < 12) {
        return months + ' mois';
    }
    const years = Math.floor(months / 12);
    const remainingMonths = months % 12;
    if (remainingMonths === 0) {
        return years + ' an' + (years > 1 ? 's' : '');
    }
    return years + ' ans ' + remainingMonths + ' mois';
}

// ================= FILTRES ET RECHERCHE =================
function getFilteredAnimals() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
    const type = document.getElementById('filterType').value;
    const health = document.getElementById('filterHealth').value;
    const age = document.getElementById('filterAge').value;
    
    return animals.filter(animal => {
        // Recherche
        if (searchTerm && !animal.nom.toLowerCase().includes(searchTerm)) {
            return false;
        }
        
        // Type
        if (type !== 'all' && animal.espece !== type) {
            return false;
        }
        
        // Santé
        if (health !== 'all' && animal.sante !== health) {
            return false;
        }
        
        // Âge
        if (age !== 'all') {
            const months = animal.age;
            if (age === '0-1' && months >= 12) return false;
            if (age === '1-3' && (months < 12 || months >= 36)) return false;
            if (age === '3-5' && (months < 36 || months >= 60)) return false;
            if (age === '5+' && months < 60) return false;
        }
        
        return true;
    });
}

// ================= PAGINATION =================
function updatePagination(total) {
    const totalPages = Math.ceil(total / itemsPerPage);
    const pageNumbers = document.getElementById('pageNumbers');
    const prevBtn = document.getElementById('prevPage');
    const nextBtn = document.getElementById('nextPage');
    
    document.getElementById('paginationInfo').textContent = `Affichage : ${Math.min(total, (currentPage - 1) * itemsPerPage + 1)}/${total}`;
    
    if (totalPages <= 1) {
        pageNumbers.innerHTML = '';
        prevBtn.disabled = true;
        nextBtn.disabled = true;
        return;
    }
    
    let html = '';
    for (let i = 1; i <= totalPages; i++) {
        html += `<button class="${i === currentPage ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
    }
    pageNumbers.innerHTML = html;
    
    prevBtn.disabled = currentPage === 1;
    nextBtn.disabled = currentPage === totalPages;
}

function goToPage(page) {
    currentPage = page;
    renderAnimals();
    window.scrollTo({ top: document.getElementById('animalsList').offsetTop - 20, behavior: 'smooth' });
}

// ================= VOIR ANIMAL =================
function viewAnimal(id) {
    const animal = animals.find(a => a.id === id);
    if (!animal) return;
    
    currentViewId = id;
    
    document.getElementById('detailAnimalNom').textContent = animal.nom.toUpperCase();
    document.getElementById('detailAnimalImage').src = animal.image;
    document.getElementById('detailNom').textContent = animal.nom;
    document.getElementById('detailEspece').textContent = animal.espece.charAt(0).toUpperCase() + animal.espece.slice(1);
    document.getElementById('detailRace').textContent = animal.race;
    document.getElementById('detailAge').textContent = formatAge(animal.age);
    document.getElementById('detailPoids').textContent = animal.poids + ' kg';
    document.getElementById('detailElevage').textContent = animal.elevage;
    
    const santeBadge = document.getElementById('detailStatutSanitaire');
    santeBadge.textContent = animal.sante.charAt(0).toUpperCase() + animal.sante.slice(1);
    santeBadge.className = 'badge-sante-detail ' + animal.sante;
    
    // Tâches
    const tasksContainer = document.getElementById('detailHistoriqueTaches');
    if (animal.tasks && animal.tasks.length > 0) {
        tasksContainer.innerHTML = animal.tasks.map(task => `
            <div class="task-item">
                <span class="task-type">${task.type}</span>
                <span class="task-date">${task.date}</span>
            </div>
        `).join('');
    } else {
        tasksContainer.innerHTML = '<p style="text-align: center; color: #6c757d; padding: 10px;">Aucune tâche enregistrée</p>';
    }
    
    // Graphique
    drawWeightChart(animal);
    
    openModal('detailAnimalModal');
}

// ================= GRAPHIQUE DE POIDS =================
function drawWeightChart(animal) {
    const ctx = document.getElementById('weightChart').getContext('2d');
    
    if (weightChart) {
        weightChart.destroy();
    }
    
    const data = animal.weightHistory || [animal.poids];
    const labels = data.map((_, i) => `M${i+1}`);
    
    const gradient = ctx.createLinearGradient(0, 0, 0, 250);
    gradient.addColorStop(0, 'rgba(25, 135, 84, 0.3)');
    gradient.addColorStop(1, 'rgba(25, 135, 84, 0.0)');
    
    weightChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Poids (kg)',
                data: data,
                borderColor: '#198754',
                backgroundColor: gradient,
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
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
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

// ================= AJOUTER UN ANIMAL =================
document.getElementById('openAddModal').addEventListener('click', function() {
    document.getElementById('addAnimalForm').reset();
    document.getElementById('addImagePreview').innerHTML = '<i class="far fa-image fa-2x text-muted"></i>';
    openModal('addAnimalModal');
});

// Aperçu image ajout
document.getElementById('addAnimalImage').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('addImagePreview').innerHTML = `<img src="${event.target.result}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px;">`;
        };
        reader.readAsDataURL(file);
    }
});

document.getElementById('addRemoveImage').addEventListener('click', function() {
    document.getElementById('addAnimalImage').value = '';
    document.getElementById('addImagePreview').innerHTML = '<i class="far fa-image fa-2x text-muted"></i>';
});

// Soumission formulaire ajout
document.getElementById('addAnimalForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const nom = document.getElementById('addNom').value.trim();
    const espece = document.getElementById('addEspece').value;
    const race = document.getElementById('addRace').value.trim();
    const age = parseInt(document.getElementById('addAge').value) || 0;
    const poids = parseFloat(document.getElementById('addPoids').value) || 0;
    const sante = document.getElementById('addSante').value;
    const elevage = document.getElementById('addElevage').value || 'Non spécifié';
    const note = document.getElementById('addNote').value.trim();
    
    if (!nom) {
        showToast('Veuillez saisir un nom', 'warning');
        return;
    }
    
    if (!espece) {
        showToast('Veuillez sélectionner une espèce', 'warning');
        return;
    }
    
    const newAnimal = {
        id: nextId++,
        nom: nom,
        espece: espece,
        race: race || 'Non spécifié',
        age: age,
        poids: poids,
        sante: sante,
        elevage: elevage,
        note: note,
        image: 'https://images.unsplash.com/photo-1516467508483-a7212febe31a?w=400',
        tasks: [{ type: "Ajout", date: new Date().toLocaleDateString('fr-FR') }],
        weightHistory: [poids > 0 ? poids : 0]
    };
    
    animals.push(newAnimal);
    renderAnimals();
    closeModal('addAnimalModal');
    showToast(`Animal "${nom}" ajouté avec succès !`, 'success');
});

// ================= MODIFIER UN ANIMAL =================
function openEditModal(id) {
    const animal = animals.find(a => a.id === id);
    if (!animal) return;
    
    document.getElementById('editAnimalId').value = animal.id;
    document.getElementById('editNom').value = animal.nom;
    document.getElementById('editEspece').value = animal.espece;
    document.getElementById('editRace').value = animal.race;
    document.getElementById('editAge').value = animal.age;
    document.getElementById('editPoids').value = animal.poids;
    document.getElementById('editSante').value = animal.sante;
    document.getElementById('editElevage').value = animal.elevage;
    document.getElementById('editNote').value = animal.note || '';
    
    document.getElementById('editImagePreview').innerHTML = `<img src="${animal.image}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px;">`;
    
    openModal('editAnimalModal');
}

// Aperçu image modification
document.getElementById('editAnimalImage').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('editImagePreview').innerHTML = `<img src="${event.target.result}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px;">`;
        };
        reader.readAsDataURL(file);
    }
});

document.getElementById('editRemoveImage').addEventListener('click', function() {
    document.getElementById('editAnimalImage').value = '';
    const id = parseInt(document.getElementById('editAnimalId').value);
    const animal = animals.find(a => a.id === id);
    if (animal) {
        document.getElementById('editImagePreview').innerHTML = `<img src="${animal.image}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px;">`;
    }
});

// Soumission formulaire modification
document.getElementById('editAnimalForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const id = parseInt(document.getElementById('editAnimalId').value);
    const animal = animals.find(a => a.id === id);
    if (!animal) return;
    
    const nom = document.getElementById('editNom').value.trim();
    const espece = document.getElementById('editEspece').value;
    
    if (!nom) {
        showToast('Veuillez saisir un nom', 'warning');
        return;
    }
    
    if (!espece) {
        showToast('Veuillez sélectionner une espèce', 'warning');
        return;
    }
    
    animal.nom = nom;
    animal.espece = espece;
    animal.race = document.getElementById('editRace').value.trim() || 'Non spécifié';
    animal.age = parseInt(document.getElementById('editAge').value) || 0;
    animal.poids = parseFloat(document.getElementById('editPoids').value) || 0;
    animal.sante = document.getElementById('editSante').value;
    animal.elevage = document.getElementById('editElevage').value || 'Non spécifié';
    animal.note = document.getElementById('editNote').value.trim();
    
    renderAnimals();
    closeModal('editAnimalModal');
    showToast(`Animal "${nom}" modifié avec succès !`, 'success');
});

// ================= SUPPRIMER UN ANIMAL =================
function openDeleteModal(id) {
    const animal = animals.find(a => a.id === id);
    if (!animal) return;
    
    document.getElementById('deleteAnimalId').value = id;
    document.getElementById('deleteAnimalName').textContent = `Êtes-vous sûr de vouloir supprimer "${animal.nom}" ?`;
    openModal('deleteAnimalModal');
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    const id = parseInt(document.getElementById('deleteAnimalId').value);
    const animal = animals.find(a => a.id === id);
    
    if (animal) {
        const name = animal.nom;
        animals = animals.filter(a => a.id !== id);
        renderAnimals();
        closeModal('deleteAnimalModal');
        showToast(`Animal "${name}" supprimé avec succès`, 'success');
    }
});

// ================= BOUTONS D'ACTION DANS LE DÉTAIL =================
document.getElementById('detailModifyBtn').addEventListener('click', function() {
    if (currentViewId) {
        closeModal('detailAnimalModal');
        setTimeout(() => openEditModal(currentViewId), 300);
    }
});

document.getElementById('detailDeleteBtn').addEventListener('click', function() {
    if (currentViewId) {
        closeModal('detailAnimalModal');
        setTimeout(() => openDeleteModal(currentViewId), 300);
    }
});

document.getElementById('detailAddTaskBtn').addEventListener('click', function() {
    if (currentViewId) {
        const task = prompt('Entrez une nouvelle tâche :');
        if (task && task.trim()) {
            const animal = animals.find(a => a.id === currentViewId);
            if (animal) {
                animal.tasks.push({ type: task.trim(), date: new Date().toLocaleDateString('fr-FR') });
                viewAnimal(currentViewId);
                showToast('Tâche ajoutée avec succès !', 'success');
            }
        }
    }
});

// ================= ÉVÉNEMENTS DE RECHERCHE ET FILTRES =================
document.getElementById('searchInput').addEventListener('input', function() {
    currentPage = 1;
    renderAnimals();
});

document.getElementById('searchBtn').addEventListener('click', function() {
    currentPage = 1;
    renderAnimals();
});

document.getElementById('filterType').addEventListener('change', function() {
    currentPage = 1;
    renderAnimals();
});

document.getElementById('filterHealth').addEventListener('change', function() {
    currentPage = 1;
    renderAnimals();
});

document.getElementById('filterAge').addEventListener('change', function() {
    currentPage = 1;
    renderAnimals();
});

// ================= PAGINATION =================
document.getElementById('prevPage').addEventListener('click', function() {
    if (currentPage > 1) {
        currentPage--;
        renderAnimals();
    }
});

document.getElementById('nextPage').addEventListener('click', function() {
    const filtered = getFilteredAnimals();
    const totalPages = Math.ceil(filtered.length / itemsPerPage);
    if (currentPage < totalPages) {
        currentPage++;
        renderAnimals();
    }
});

// ================= INITIALISATION =================
document.addEventListener('DOMContentLoaded', function() {
    renderAnimals();
    
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
        .custom-toast.success .toast-content { background: #198754; }
        .custom-toast.danger .toast-content { background: #dc3545; }
        .custom-toast.warning .toast-content { background: #ffc107; color: #343a40; }
        .custom-toast.info .toast-content { background: #0dcaf0; color: #343a40; }
        
        @media (max-width: 768px) {
            .custom-toast {
                left: 15px;
                right: 15px;
                bottom: 15px;
                transform: translateY(100px);
            }
            .custom-toast.show { transform: translateY(0); }
        }
        
        .badge-sante.bonne { background: #d4edda; color: #198754; }
        .badge-sante.moyenne { background: #fff3cd; color: #856404; }
        .badge-sante.critique { background: #f8d7da; color: #dc3545; }
        
        .badge-sante-detail.bonne { background: #d4edda; color: #198754; }
        .badge-sante-detail.moyenne { background: #fff3cd; color: #856404; }
        .badge-sante-detail.critique { background: #f8d7da; color: #dc3545; }
        
        .page-numbers {
            display: flex;
            gap: 8px;
        }
        .page-numbers button {
            width: 35px;
            height: 35px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: white;
            cursor: pointer;
        }
        .page-numbers button.active {
            background: #198754;
            color: white;
            border-color: #198754;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 12px;
        }
        .modal {
            z-index: 1050;
        }
    `;
    document.head.appendChild(style);
});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Projets\Elevage-plus\resources\views/animaux.blade.php ENDPATH**/ ?>
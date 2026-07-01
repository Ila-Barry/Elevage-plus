{{-- resources/views/taches.blade.php --}}

@extends('layouts.menu')

@section('title', 'Tâches')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/eleveurCSS/taches.css') }}">
@endpush

@section('content')

<div class="taches-container">

<!-- ========================================= -->
<!-- TITRE PAGE -->
<!-- ========================================= -->
<div class="page-header">
    <h1>CALENDRIER DES TÂCHES</h1>
    <div class="task-stats">
        <span class="stat-badge total">Total: <strong id="totalTasks">0</strong></span>
        <span class="stat-badge done">📅 Aujourd'hui: <strong id="todayTasksCount">0</strong></span>
        <span class="stat-badge pending">📋 À venir: <strong id="upcomingTasksCount">0</strong></span>
    </div>
</div>

<div class="tasks-grid">

    <!-- ========================================= -->
    <!-- COLONNE GAUCHE -->
    <!-- ========================================= -->
    <div class="tasks-left">

        <!-- BOUTONS VUE -->
        <div class="calendar-filters">
            <button class="filter-btn active" data-view="month" onclick="switchView('month')">
                <i class="fas fa-calendar-alt"></i> Mois
            </button>
            <button class="filter-btn" data-view="week" onclick="switchView('week')">
                <i class="far fa-calendar"></i> Semaines
            </button>
            <button class="filter-btn" data-view="day" onclick="switchView('day')">
                <i class="far fa-calendar-check"></i> Jours
            </button>
        </div>

        <!-- CALENDRIER -->
        <div class="calendar-card">
            <div class="calendar-top">
                <button class="calendar-nav" onclick="changeWeek(-1)">
    <i class="fas fa-chevron-left"></i>
</button>

<h3 id="currentMonthYear"></h3>

<button class="calendar-nav" onclick="changeWeek(1)">
    <i class="fas fa-chevron-right"></i>
</button>
            </div>

            <table class="calendar-table">
                <thead>
                    <tr>
                        <th>Lun</th>
                        <th>Mar</th>
                        <th>Mer</th>
                        <th>Jeu</th>
                        <th>Ven</th>
                        <th>Sam</th>
                        <th>Dim</th>
                    </tr>
                </thead>
                <tbody id="calendarBody">
                    <!-- Généré par JavaScript -->
                </tbody>
            </table>

            <div class="calendar-legend">
                <span class="legend-dot"></span>
                Tâche(s) planifiée(s)
                <span class="legend-dot today-dot"></span>
                Aujourd'hui
            </div>
        </div>

        <!-- TACHES A VENIR -->
        <div class="upcoming-card">
            <div class="section-header">
                <i class="far fa-calendar-alt"></i>
                TÂCHES À VENIR
            </div>
            <ul id="upcomingTasks">
                <!-- Généré par JavaScript -->
            </ul>
        </div>

    </div>

    <!-- ========================================= -->
    <!-- COLONNE DROITE -->
    <!-- ========================================= -->
    <div class="tasks-right">

        <button class="btn-add-task" onclick="openAddModal()">
            <i class="fas fa-plus"></i>
            Ajouter une tâche
        </button>

        <div class="today-card">
            <div class="section-header">
                <i class="far fa-calendar-alt"></i>
                TÂCHES DU <span id="todayDate">--</span>
            </div>
            <div id="todayTasks">
                <!-- Généré par JavaScript -->
            </div>
        </div>

    </div>

</div>

</div>

<!-- ========================================= -->
<!-- MODALE AJOUTER UNE TÂCHE -->
<!-- ========================================= -->
<div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fw-bold" id="addTaskModalLabel">
                    <i class="fas fa-plus-circle text-success"></i> AJOUTER UNE TÂCHE
                </h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer">&times;</button>
            </div>
            <div class="modal-body">
                <div id="addError" class="alert alert-danger" style="display: none;"></div>
                <form id="addTaskForm">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">🐄 Animal concerné <span class="text-danger">*</span></label>
                        <select class="form-select" id="addAnimal" required>
                            <option value="">Sélectionner un animal</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">📋 Type de tâche <span class="text-danger">*</span></label>
                        <select class="form-select" id="addType" required>
                            <option value="">Choisir un type</option>
                            <option value="vaccination">Vaccination</option>
                            <option value="vermifuge">Vermifuge</option>
                            <option value="pesee">Pesée</option>
                            <option value="soin">Soin</option>
                            <option value="nettoyage">Nettoyage</option>
                            <option value="alimentation">Alimentation</option>
                            <option value="visite_veterinaire">Visite vétérinaire</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">📝 Titre de la tâche <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="addTitre" placeholder="Ex: Vaccination annuelle" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">📅 Date planifiée <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="addDate" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">🔴 Priorité</label>
                            <select class="form-select" id="addPriorite">
                                <option value="basse">Basse</option>
                                <option value="moyenne" selected>Moyenne</option>
                                <option value="haute">Haute</option>
                                <option value="urgente">Urgente</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">📝 Description (optionnelle)</label>
                        <textarea class="form-control" id="addDescription" rows="3" placeholder="Ajouter une description..."></textarea>
                    </div>

                    <div class="d-flex justify-content-center gap-3">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                            onclick="window.location.reload();">
                            ❌ Annuler
                        </button>
                        <button type="submit" class="btn btn-success" id="addSubmitBtn">✅ Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ========================================= -->
<!-- MODALE MODIFIER UNE TÂCHE -->
<!-- ========================================= -->
<div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fw-bold" id="editTaskModalLabel">
                    <i class="fas fa-edit text-warning"></i> MODIFIER UNE TÂCHE
                </h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer">&times;</button>
            </div>
            <div class="modal-body">
                <div id="editError" class="alert alert-danger" style="display: none;"></div>
                <form id="editTaskForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editTaskId">

                    <div class="mb-3">
                        <label class="form-label">🐄 Animal concerné</label>
                        <select class="form-select" id="editAnimal">
                            <option value="">Sélectionner un animal</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">📋 Type de tâche</label>
                        <select class="form-select" id="editType">
                            <option value="vaccination">Vaccination</option>
                            <option value="vermifuge">Vermifuge</option>
                            <option value="pesee">Pesée</option>
                            <option value="soin">Soin</option>
                            <option value="nettoyage">Nettoyage</option>
                            <option value="alimentation">Alimentation</option>
                            <option value="visite_veterinaire">Visite vétérinaire</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">📝 Titre de la tâche</label>
                        <input type="text" class="form-control" id="editTitre" placeholder="Ex: Vaccination annuelle">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">📅 Date planifiée</label>
                            <input type="datetime-local" class="form-control" id="editDate">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">🔴 Priorité</label>
                            <select class="form-select" id="editPriorite">
                                <option value="basse">Basse</option>
                                <option value="moyenne">Moyenne</option>
                                <option value="haute">Haute</option>
                                <option value="urgente">Urgente</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">📝 Description</label>
                        <textarea class="form-control" id="editDescription" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">✅ Statut</label>
                        <select class="form-select" id="editStatut">
                            <option value="0">En attente</option>
                            <option value="1">Terminée</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-center gap-3">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                            onclick="window.location.reload();">
                            Annuler
                        </button>
                        <button type="submit" class="btn btn-success" id="editSubmitBtn">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ========================================= -->
<!-- MODALE SUPPRESSION -->
<!-- ========================================= -->
<div class="modal fade" id="deleteTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fw-bold" style="color: #dc3545;">
                    <i class="fas fa-exclamation-triangle text-danger"></i> SUPPRIMER LA TÂCHE
                </h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-trash-alt" style="font-size: 48px; color: #dc3545; margin-bottom: 15px;"></i>
                <h5 id="deleteTaskName">Êtes-vous sûr de vouloir supprimer cette tâche ?</h5>
                <p class="text-muted">Cette action est irréversible.</p>
                <input type="hidden" id="deleteTaskId">
                <div class="d-flex justify-content-center gap-3 mt-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="fas fa-trash-alt"></i> Supprimer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// =====================================================
// CONFIGURATION API & AUTH
// =====================================================

const API_URL = window.location.origin + '/api';

// CSRF (utile si routes web, sinon API token suffit)
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '';

// Token API (JWT / Sanctum)
const rawToken = localStorage.getItem('access_token');
const token = rawToken ? rawToken.replace(/^"(.*)"$/, '$1') : null;

console.log('🔍 Taches init:', {
    API_URL,
    token: token ? 'OK' : 'MISSING'
});


// =====================================================
// VARIABLES GLOBALES
// =====================================================

let tasks = [];              // liste des tâches
let animals = [];            // liste des animaux

let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();

let currentView = 'month';   // month | week | day
let weekOffset = 0;          // navigation semaine

let toastTimeout = null;


// =====================================================
// TOAST NOTIFICATIONS
// =====================================================

function showToast(message, type = 'info') {

    // supprimer ancien toast
    document.querySelector('.custom-toast')?.remove();
    if (toastTimeout) clearTimeout(toastTimeout);

    const toast = document.createElement('div');
    toast.className = `custom-toast ${type}`;

    const icons = {
        success: 'fa-check-circle',
        danger: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };

    toast.innerHTML = `
        <div class="toast-content">
            <i class="fas ${icons[type] || icons.info}"></i>
            <span>${message}</span>
        </div>
    `;

    document.body.appendChild(toast);

    setTimeout(() => toast.classList.add('show'), 10);

    toastTimeout = setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}


// =====================================================
// APIS - FETCH TASKS
// =====================================================

async function fetchTasks() {
    try {
        const response = await fetch(`${API_URL}/taches`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
                'X-CSRF-TOKEN': CSRF_TOKEN
            }
        });

        const result = await response.json();

        if (!response.ok) throw result;

        // compat backend (data ou data.data)
        const data = result.data?.data || result.data || [];

        return { status: 'success', data };

    } catch (error) {
        console.error('❌ fetchTasks error:', error);
        return { status: 'error', data: [] };
    }
}


// =====================================================
// APIS - FETCH ANIMALS
// =====================================================

async function fetchAnimals() {
    try {
        const response = await fetch(`${API_URL}/animaux?per_page=50`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
                'X-CSRF-TOKEN': CSRF_TOKEN
            }
        });

        const result = await response.json();

        if (!response.ok) throw result;

        const data = result.data?.data || result.data || [];

        return { success: true, data };

    } catch (error) {
        console.error('❌ fetchAnimals error:', error);
        return { success: false, data: [] };
    }
}


// =====================================================
// CREATE / UPDATE / DELETE TASK
// =====================================================

async function createTask(data) {
    const response = await fetch(`${API_URL}/taches`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': 'Bearer ' + token,
            'X-CSRF-TOKEN': CSRF_TOKEN
        },
        body: JSON.stringify(data)
    });

    const result = await response.json();
    if (!response.ok) throw result;
    return result;
}

async function updateTask(id, data) {
    const response = await fetch(`${API_URL}/taches/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': 'Bearer ' + token,
            'X-CSRF-TOKEN': CSRF_TOKEN
        },
        body: JSON.stringify(data)
    });

    const result = await response.json();
    if (!response.ok) throw result;
    return result;
}

async function deleteTask(id) {
    const response = await fetch(`${API_URL}/taches/${id}`, {
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
}

// =====================================================
// MARQUER UNE TÂCHE COMME TERMINÉE / NON TERMINÉE
// =====================================================

async function toggleTaskComplete(id) {

    try {
        const response = await fetch(`${API_URL}/taches/${id}/complete`, {
            method: 'PATCH',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
                'X-CSRF-TOKEN': CSRF_TOKEN
            }
        });

        const result = await response.json();

        if (!response.ok) throw result;

        // 🔄 recharge l'interface après changement
        await loadData();

        showToast('Statut mis à jour', 'success');

        return result;

    } catch (error) {
        console.error('toggleTaskComplete error:', error);
        showToast('Erreur lors de la mise à jour du statut', 'danger');
    }
}

// =====================================================
// DÉPLACER UNE TÂCHE (DRAG & DROP CALENDRIER)
// =====================================================

async function moveTask(id, date) {

    try {
        await updateTask(id, {
            date_planifiee: date
        });

        showToast('Tâche déplacée avec succès', 'success');

        // 🔄 met à jour calendrier + listes
        await loadData();

    } catch (error) {
        console.error('moveTask error:', error);
        showToast('Erreur déplacement tâche', 'danger');
    }
}

// =====================================================
// CHARGEMENT GLOBAL
// =====================================================

async function loadData() {
    try {
        const tasksResult = await fetchTasks();
        tasks = tasksResult.data || [];

        const animalsResult = await fetchAnimals();
        animals = animalsResult.data || [];

        populateAnimalSelects();
        renderAll();

    } catch (error) {
        console.error(error);
        showToast('Erreur chargement données', 'danger');
    }
}


// =====================================================
// SELECT ANIMALS
// =====================================================

function populateAnimalSelects() {
    ['addAnimal', 'editAnimal'].forEach(id => {

        const select = document.getElementById(id);
        if (!select) return;

        select.innerHTML = `<option value="">Sélectionner un animal</option>`;

        animals.forEach(a => {
            const option = document.createElement('option');
            option.value = a.id;
            option.textContent = `${a.nom || 'Animal'} (${a.espece || ''})`;
            select.appendChild(option);
        });
    });
}


// =====================================================
// CHANGER LA SEMAINE DU CALENDRIER
// =====================================================
// Permet de naviguer avec les flèches gauche / droite

function changeWeek(delta) {

    // Si on est en affichage mois
    if (currentView === 'month') {

        // changer le mois
        currentMonth += delta;


        // gérer changement année
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }


        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }

    }

    // Si on est en semaine
    else if (currentView === 'week') {

        weekOffset += delta;

    }


    renderCalendar();

}


// =====================================================
// SWITCH VIEW
// =====================================================

function switchView(view) {
    currentView = view;

    // reset semaine si month
    if (view === 'month') weekOffset = 0;

    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.view === view);
    });

    renderCalendar();
    showToast(`Vue ${view}`, 'info');
}


// =====================================================
// CALENDAR RENDER
// =====================================================

function renderCalendar() {
    const tbody = document.getElementById('calendarBody');
    const today = new Date();
const title = document.getElementById('currentMonthYear');

if(title){

    if(currentView === 'month'){

<<<<<<< Updated upstream
// ================= SELECTION TÂCHE =================
function selectTask(taskId) {
    const task = tasks.find(t => t.id === taskId);
    if (task) {
        const date = new Date(task.date_planifiee);
        showToast(`📋 ${task.type_label || task.type} - ${task.animal?.nom || 'N/A'} (${date.toLocaleDateString('fr-FR')})`, 'info');
    }
}

// ================= TOGGLE TÂCHE =================
async function toggleTask(taskId) {
    try {
        const result = await toggleTaskComplete(taskId);
        if (result.status === 'success') {
            const task = tasks.find(t => t.id === taskId);
            if (task) {
                task.terminee = !task.terminee;
            }
            renderAll();
            showToast(
                task?.terminee ? '✅ Tâche terminée !' : '🔄 Tâche réouverte',
                task?.terminee ? 'success' : 'info'
            );
        } else {
            showToast(result.message || 'Erreur lors de la mise à jour', 'danger');
        }
    } catch (error) {
        showToast('Erreur lors de la mise à jour', 'danger');
    }
}

// ================= MODALE AJOUTER =================
function openAddModal() {
    document.getElementById('addTaskForm').reset();

    document.getElementById('addAnimal').value = "";

    const now = new Date();
    const localDatetime = new Date(now.getTime() - now.getTimezoneOffset() * 60000)
        .toISOString()
        .slice(0, 16);

    document.getElementById('addDate').value = localDatetime;
    document.getElementById('addError').style.display = 'none';

    const modalEl = document.getElementById('addTaskModal');
    let modal = bootstrap.Modal.getInstance(modalEl);

    if (!modal) {
        modal = new bootstrap.Modal(modalEl);
    }

    modal.show();
}

// ================= MODALE AJOUTER =================
document.getElementById('addTaskForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('addSubmitBtn');
    const errorDiv = document.getElementById('addError');
    errorDiv.style.display = 'none';
    
    const animalId = document.getElementById('addAnimal').value;
    const type = document.getElementById('addType').value;
    const titre = document.getElementById('addTitre').value.trim();
    const date = document.getElementById('addDate').value;
    const priorite = document.getElementById('addPriorite').value;
    const description = document.getElementById('addDescription').value.trim();
    
    if (!animalId || !type || !titre || !date) {
        showToast('Veuillez remplir les champs obligatoires', 'warning');
        return;
    }
    
    const animal = animals.find(a => a.id == animalId);
    if (!animal) {
        showToast('Animal non trouvé', 'danger');
        return;
    }
    
    const data = {
        animal_id: parseInt(animalId),
        elevage_id: animal.elevage_id,
        titre: titre,
        type: type,
        description: description || null,
        date_planifiee: date,
        priorite: priorite
    };
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> En cours...';
    
    try {
        const result = await createTask(data);

if (result.status === 'success' || result.success === true) {

    const modal = bootstrap.Modal.getInstance(document.getElementById('addTaskModal'));

    if (modal) {
        modal.hide();
    }

    sessionStorage.setItem('toastSuccess', 'Tâche ajoutée avec succès !');

    setTimeout(() => {
        window.location.reload();
    }, 300);

} else {
    errorDiv.textContent = result.message || 'Erreur lors de la création';
    errorDiv.style.display = 'block';
}
    } catch (error) {
        errorDiv.textContent = error.message || 'Erreur lors de la création';
        errorDiv.style.display = 'block';
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '✅ Ajouter';
    }
});

// ================= MODALE MODIFIER =================
async function openEditModal(taskId) {
    console.log('📝 Ouverture du modal de modification pour la tâche:', taskId);
    
    try {
        const response = await fetch(`${API_URL}/taches/${taskId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token
            }

        title.textContent = new Date(
            currentYear,
            currentMonth
        ).toLocaleDateString('fr-FR', {
            month: 'long',
            year: 'numeric'

        });

    }


    else if(currentView === 'week'){


    const start = new Date();

    start.setDate(
        start.getDate()
        - start.getDay()
        + 1
        + (weekOffset * 7)
    );


    const end = new Date(start);

    end.setDate(start.getDate() + 6);



    const startText = start.toLocaleDateString(
        'fr-FR',
        {
            day: 'numeric',
            month: 'long'
        }
    );


    const endText = end.toLocaleDateString(
        'fr-FR',
        {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        }
    );


    title.textContent = 
    `${startText} - ${endText}`;

}


    else {

        title.textContent = today.toLocaleDateString(
            'fr-FR',
            {
                weekday:'long',
                day:'numeric',
                month:'long'
            }
        );

    }

}
    let html = '';

    // ================= MONTH VIEW =================
if (currentView === 'month') {


    const firstDay = new Date(currentYear, currentMonth, 1);

    const lastDay = new Date(currentYear, currentMonth + 1, 0);


    const daysInMonth = lastDay.getDate();


    // nombre de cases avant le 1er jour
    const startOffset = (firstDay.getDay() === 0)
        ? 6
        : firstDay.getDay() - 1;


    let day = 1;

    let nextMonthDay = 1;

    let row = 0;



    while (day <= daysInMonth || nextMonthDay <= 4) {


        html += '<tr>';



        for (let i = 0; i < 7; i++) {



            // Cases avant le début du mois
            if (row === 0 && i < startOffset) {


                const prevDate =
                    new Date(currentYear, currentMonth, 0);


                const prevDay =
                    prevDate.getDate()
                    - startOffset
                    + i
                    + 1;


                html += `
                    <td class="other-month">
                        ${prevDay}
                    </td>
                `;

            }



            // Jours du mois actuel
            else if (day <= daysInMonth) {



                const dateStr =
                `${currentYear}-${String(currentMonth + 1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;



                const hasTask = tasks.some(t => {

                    const d =
                    new Date(t.date_planifiee)
                    .toISOString()
                    .split('T')[0];


                    return d === dateStr && !t.terminee;

                });



                const isToday =
                dateStr === today.toISOString().split('T')[0];



                html += `
                <td
                    class="${hasTask ? 'task-day' : ''} ${isToday ? 'today' : ''}"
                    data-date="${dateStr}"
                    onclick="selectDate('${dateStr}')"
                    ondrop="dropTask(event)"
                    ondragover="allowDrop(event)"
                >
                    ${day}
                </td>
                `;



                day++;



            }



            // Début du mois suivant
            else {



                html += `
                    <td class="other-month">
                        ${nextMonthDay}
                    </td>
                `;


                nextMonthDay++;


            }


        }



        html += '</tr>';


        row++;


        // sécurité pour éviter boucle infinie
        if(row > 6) break;

    }

}

    // ================= WEEK VIEW =================
    else if (currentView === 'week') {

        const start = new Date();
        start.setDate(start.getDate() - start.getDay() + 1 + (weekOffset * 7));

        html += '<tr>';

        for (let i = 0; i < 7; i++) {

            const d = new Date(start);
            d.setDate(start.getDate() + i);

            const dateStr = d.toISOString().split('T')[0];

            const hasTask = tasks.some(t => {
                const tDate = new Date(t.date_planifiee).toISOString().split('T')[0];
                return tDate === dateStr && !t.terminee;
            });

            html += `
                <td 
    class="${hasTask ? 'task-day' : ''}"
    data-date="${dateStr}"
    onclick="selectDate('${dateStr}')"
    ondrop="dropTask(event)"
    ondragover="allowDrop(event)"
>
                    ${d.getDate()}<br>
                    <small>${d.toLocaleDateString('fr-FR', { weekday: 'short' })}</small>
                </td>
            `;
        }

        html += '</tr>';
    }

    // ================= DAY VIEW =================
    else {

        const dateStr = today.toISOString().split('T')[0];

        const dayTasks = tasks.filter(t => {
            const d = new Date(t.date_planifiee).toISOString().split('T')[0];
            return d === dateStr;
        });

              html = `
        <tr>
            <td colspan="7">

                <strong>Tâches du jour</strong>

                ${
                    dayTasks.length
                    ? dayTasks.map(t => `
                        <div 
                            class="task-item task-draggable"
                            data-id="${t.id}"
                        >
                            ${t.titre || t.type}
                        </div>
                    `).join('')
                    : '<p>Aucune tâche</p>'
                }

            </td>
        </tr>
       `;

    }


    // Injection dans le calendrier
    tbody.innerHTML = html;


    // Active le drag après génération
    enableDrag();

}

// =====================================================
// RENDER ALL
// =====================================================

function renderAll() {
    renderCalendar();
}

// =====================================================
// ACTIVE LE DRAG & DROP SUR LES TÂCHES
// =====================================================
// Cette fonction rend chaque tâche "déplaçable" (drag & drop)
// Elle est rappelée après chaque rendu du calendrier

function enableDrag() {

    // On sélectionne toutes les tâches qui doivent être draggable
    document.querySelectorAll('.task-draggable').forEach(el => {

        // On autorise le drag HTML5
        el.setAttribute('draggable', true);

        // =================================================
        // DÉBUT DU DRAG
        // =================================================
        el.ondragstart = (e) => {

            // On stocke l'ID de la tâche dans l'événement drag
            // pour pouvoir la récupérer lors du drop
            e.dataTransfer.setData('taskId', el.dataset.id);

            // Effet visuel : la tâche devient semi-transparente
            el.classList.add('dragging');
        };

        // =================================================
        // FIN DU DRAG
        // =================================================
        el.ondragend = () => {

            // On retire l'effet visuel après le drag
            el.classList.remove('dragging');
        };
    });
}

// =====================================================
// OUVRIR MODALE AJOUTER TÂCHE
// =====================================================

function openAddModal() {

    const modalElement = document.getElementById('addTaskModal');

    if (!modalElement) {
        console.error("❌ Modale ajout introuvable");
        return;
    }
<<<<<<< Updated upstream
    
    const data = {
        titre: titre,
        type: type,
        description: description || null,
        date_planifiee: date,
        priorite: priorite,
        terminee: terminee
    };
    
    if (animalId) data.animal_id = parseInt(animalId);
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> En cours...';
    
    try {
        const result = await updateTask(taskId, data);
        if (result.status === 'success' || result.success === true) {
            $('#editTaskModal').modal('hide');
            sessionStorage.setItem('toastSuccess', 'Tâche modifiée avec succès !');
            setTimeout(() => {
                window.location.reload();
            }, 300);
        } else {
            errorDiv.textContent = result.message || 'Erreur lors de la modification';
            errorDiv.style.display = 'block';
        }
    } catch (error) {
        errorDiv.textContent = error.message || 'Erreur lors de la modification';
        errorDiv.style.display = 'block';
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Enregistrer';
    }
});




    const modal = new bootstrap.Modal(modalElement);

    modal.show();

}
// =====================================================
// INIT
// =====================================================

document.addEventListener('DOMContentLoaded', () => {

    if (!token) {
        showToast('Non connecté', 'danger');
        window.location.href = '/login';
        return;
    }

    loadData();
});

// =====================================================
// DRAG & DROP HANDLERS
// =====================================================

function allowDrop(e) {
    e.preventDefault();
    e.currentTarget.classList.add('drag-over');
}

function dropTask(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('drag-over');

    const newDate = e.currentTarget.dataset.date;
    const taskId = e.dataTransfer.getData('taskId');

    if (!taskId || !newDate) return;

    moveTask(taskId, newDate);
}
</script>
@endsection
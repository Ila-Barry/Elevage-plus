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
                <button class="calendar-nav" onclick="changeMonth(-1)">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <h3 id="currentMonthYear">MAI 2026</h3>
                <button class="calendar-nav" onclick="changeMonth(1)">
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
// ================= CONFIGURATION & FIX AUTHENTIFICATION =================
const API_URL = window.location.origin + '/api';
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '';
const rawToken = localStorage.getItem('access_token');
const token = rawToken ? rawToken.replace(/^"(.*)"$/, '$1') : null;

console.log('🔍 Configuration Taches:', { API_URL, token: token ? '✅ Présent' : '❌ Absent' });

// ================= VARIABLES =================
let tasks = [];
let animals = [];
let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();
let currentView = 'month';
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

// ================= API CALLS =================
async function fetchTasks() {
    try {
        console.log('📤 Récupération des tâches...');
        
        const response = await fetch(`${API_URL}/taches`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
                'X-CSRF-TOKEN': CSRF_TOKEN
            }
        });

        const result = await response.json();
        console.log('📥 Réponse brute tâches:', result);

        if (!response.ok) {
            throw result;
        }

        if (result.status === 'success' || result.success === true) {
            const data = result.data?.data || result.data || [];
            console.log('✅ Tâches extraites:', data.length);
            return { status: 'success', data: data };
        }

        return { status: 'error', data: [] };
    } catch (error) {
        console.error('❌ Erreur fetch taches:', error);
        throw error;
    }
}

async function fetchAnimals() {
    try {
        console.log('📤 Récupération des animaux...');
        
        const response = await fetch(`${API_URL}/animaux?per_page=50`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
                'X-CSRF-TOKEN': CSRF_TOKEN
            }
        });

        const result = await response.json();
        console.log('📥 Réponse animaux:', result);

        if (!response.ok) {
            throw result;
        }

        if (result.success === true) {
            const animalsData = result.data?.data || result.data || [];
            console.log('✅ Animaux extraits:', animalsData.length);
            return { success: true, data: animalsData };
        }

        return { success: false, data: [] };
    } catch (error) {
        console.error('❌ Erreur fetch animaux:', error);
        throw error;
    }
}

async function createTask(data) {
    try {
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
    } catch (error) {
        console.error('❌ Erreur création tache:', error);
        throw error;
    }
}

async function updateTask(id, data) {
    try {
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
    } catch (error) {
        console.error('❌ Erreur mise à jour tache:', error);
        throw error;
    }
}

async function deleteTask(id) {
    try {
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
    } catch (error) {
        console.error('❌ Erreur suppression tache:', error);
        throw error;
    }
}

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
        return result;
    } catch (error) {
        console.error('❌ Erreur toggle tache:', error);
        throw error;
    }
}

// ================= CHARGEMENT DES DONNÉES =================
async function loadData() {
    try {
        console.log('🔄 Début chargement des données...');
        
        const tasksResult = await fetchTasks();
        if (tasksResult.status === 'success') {
            tasks = tasksResult.data || [];
        } else {
            tasks = [];
        }

        const animalsResult = await fetchAnimals();
        if (animalsResult.success === true) {
            animals = animalsResult.data || [];
            populateAnimalSelects();
        } else {
            animals = [];
        }

        renderAll();
        console.log('✅ Chargement terminé');
    } catch (error) {
        console.error('❌ Erreur chargement données:', error);
        showToast('Erreur lors du chargement des données', 'danger');
    }
}

// ================= POPULER LES SELECTS D'ANIMAUX =================
function populateAnimalSelects() {
    const selects = ['addAnimal', 'editAnimal'];
    
    selects.forEach(selectId => {
        const select = document.getElementById(selectId);
        if (!select) return;
        
        select.innerHTML = '';
        
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = 'Sélectionner un animal';
        defaultOption.selected = true;
        defaultOption.disabled = true;
        select.appendChild(defaultOption);
        
        if (!Array.isArray(animals) || animals.length === 0) {
            const noOption = document.createElement('option');
            noOption.value = '';
            noOption.textContent = '❌ Aucun animal disponible';
            noOption.disabled = true;
            select.appendChild(noOption);
            return;
        }
        
        animals.forEach(animal => {
            const option = document.createElement('option');
            option.value = animal.id;
            const nom = animal.nom || `Animal #${animal.id}`;
            const espece = animal.espece_label || animal.espece || '';
            option.textContent = espece ? `${nom} (${espece})` : nom;
            select.appendChild(option);
        });
    });
}

// ================= CALENDRIER =================
function renderCalendar() {
    const firstDay = new Date(currentYear, currentMonth, 1);
    const lastDay = new Date(currentYear, currentMonth + 1, 0);
    const daysInMonth = lastDay.getDate();
    const startDayOfWeek = firstDay.getDay();
    const startOffset = (startDayOfWeek === 0) ? 6 : startDayOfWeek - 1;
    
    const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
                        'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
    document.getElementById('currentMonthYear').textContent = 
        monthNames[currentMonth] + ' ' + currentYear;
    
    let html = '';
    let day = 1;
    let row = 0;
    let started = false;
    
    while (day <= daysInMonth || !started) {
        html += '<tr>';
        for (let col = 0; col < 7; col++) {
            if (row === 0 && col < startOffset) {
                const prevMonthDays = new Date(currentYear, currentMonth, 0).getDate();
                const prevDay = prevMonthDays - startOffset + col + 1;
                html += `<td class="other-month">${prevDay}</td>`;
            } else if (day <= daysInMonth) {
                const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const hasTask = tasks.some(t => t.date_planifiee && t.date_planifiee.startsWith(dateStr) && !t.terminee);
                const isToday = dateStr === new Date().toISOString().split('T')[0];
                const classes = [];
                if (hasTask) classes.push('task-day');
                if (isToday) classes.push('today');
                html += `<td class="${classes.join(' ')}" onclick="selectDate('${dateStr}')">${day}</td>`;
                day++;
                started = true;
            } else {
                const nextDay = day - daysInMonth;
                html += `<td class="other-month">${nextDay}</td>`;
                day++;
            }
        }
        html += '</tr>';
        row++;
    }
    
    document.getElementById('calendarBody').innerHTML = html;
}

function changeMonth(delta) {
    currentMonth += delta;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    } else if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }
    renderCalendar();
    renderUpcomingTasks();
    renderTodayTasks();
    updateStats();
}

function switchView(view) {
    currentView = view;
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.view === view);
    });
    renderCalendar();
    showToast(`Vue: ${view.charAt(0).toUpperCase() + view.slice(1)}`, 'info');
}

function selectDate(dateStr) {
    const date = new Date(dateStr + 'T00:00:00');
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    showToast(`📅 ${date.toLocaleDateString('fr-FR', options)}`, 'info');
    document.querySelector('.today-card').scrollIntoView({ behavior: 'smooth' });
}

// ================= TÂCHES À VENIR =================
function renderUpcomingTasks() {
    const container = document.getElementById('upcomingTasks');
    const today = new Date().toISOString().split('T')[0];
    
    const upcoming = tasks
        .filter(t => {
            if (t.terminee) return false;
            if (!t.date_planifiee) return false;
            return t.date_planifiee >= today;
        })
        .sort((a, b) => a.date_planifiee.localeCompare(b.date_planifiee))
        .slice(0, 10);
    
    if (upcoming.length === 0) {
        container.innerHTML = `
            <li style="padding: 12px 14px; color: #6c757d; text-align: center;">
                <i class="fas fa-check-circle" style="color: #28a745;"></i> Aucune tâche à venir
            </li>
        `;
        return;
    }
    
    container.innerHTML = upcoming.map(t => {
        const date = new Date(t.date_planifiee);
        const dateStr = date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' });
        const timeStr = date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        const animalNom = t.animal?.nom || 'N/A';
        const typeLabel = t.type_label || t.type || 'Tâche';
        
        return `
            <li style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; border-bottom: 1px solid #f0f0f0;">
                <div style="flex: 1; cursor: pointer;" onclick="selectTask(${t.id})">
                    <div style="font-size: 13px; font-weight: 600; color: #198754;">
                        ${dateStr} ${timeStr} - ${typeLabel}
                    </div>
                    <div style="font-size: 12px; color: #6c757d;">${t.titre || t.description || animalNom}</div>
                </div>
                <div style="display: flex; gap: 6px;">
                    <button onclick="event.stopPropagation(); openEditModal(${t.id})" 
                            style="background: #fff3cd; border: none; border-radius: 4px; padding: 4px 8px; cursor: pointer; color: #856404;">
                        <i class="fas fa-pen" style="font-size: 11px;"></i>
                    </button>
                    <button onclick="event.stopPropagation(); openDeleteModal(${t.id})" 
                            style="background: #f8d7da; border: none; border-radius: 4px; padding: 4px 8px; cursor: pointer; color: #dc3545;">
                        <i class="fas fa-trash" style="font-size: 11px;"></i>
                    </button>
                </div>
            </li>
        `;
    }).join('');
}

// ================= TÂCHES DU JOUR =================
function renderTodayTasks() {
    const container = document.getElementById('todayTasks');
    const today = new Date().toISOString().split('T')[0];
    
    const todayTasks = tasks.filter(t => {
        if (!t.date_planifiee) return false;
        return t.date_planifiee.startsWith(today);
    });
    
    const dateObj = new Date(today + 'T00:00:00');
    document.getElementById('todayDate').textContent = 
        dateObj.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' }).toUpperCase();
    
    if (todayTasks.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 20px; color: #6c757d;">
                <i class="fas fa-calendar-check" style="font-size: 24px; display: block; margin-bottom: 8px;"></i>
                Aucune tâche prévue aujourd'hui
            </div>
        `;
        return;
    }
    
    container.innerHTML = todayTasks.map(t => {
        const typeIcone = t.type_icone || '📋';
        const typeLabel = t.type_label || t.type || 'Tâche';
        const titre = t.titre || t.description || t.animal?.nom || '';
        const isCompleted = t.terminee || false;
        
        return `
            <div class="task-item ${isCompleted ? 'completed' : ''}" data-id="${t.id}" style="border-bottom: 1px solid #f0f0f0; padding-bottom: 12px; margin-bottom: 12px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        ${typeIcone} ${typeLabel}
                        ${isCompleted ? '<span class="badge-completed">✓ Terminée</span>' : ''}
                    </div>
                    <div style="display: flex; gap: 6px;">
                        <button onclick="event.stopPropagation(); openEditModal(${t.id})" 
                                style="background: #fff3cd; border: none; border-radius: 4px; padding: 4px 8px; cursor: pointer; color: #856404;">
                            <i class="fas fa-pen" style="font-size: 11px;"></i>
                        </button>
                        <button onclick="event.stopPropagation(); openDeleteModal(${t.id})" 
                                style="background: #f8d7da; border: none; border-radius: 4px; padding: 4px 8px; cursor: pointer; color: #dc3545;">
                            <i class="fas fa-trash" style="font-size: 11px;"></i>
                        </button>
                    </div>
                </div>
                <div style="font-size: 13px; color: #495057; margin: 6px 0;">${titre}</div>
                <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-top: 8px;">
                    <button onclick="toggleTask(${t.id})" 
                            style="flex: 1; padding: 6px 12px; border: 1px solid #28a745; background: #d4edda; color: #28a745; border-radius: 6px; cursor: pointer; font-size: 12px;">
                        <i class="fas ${isCompleted ? 'fa-undo' : 'fa-check-square'}"></i>
                        ${isCompleted ? 'Rouvrir' : 'Marquer fait'}
                    </button>
                </div>
            </div>
        `;
    }).join('');
}

// ================= STATISTIQUES  =================
function updateStats() {
    const today = new Date().toISOString().split('T')[0];
    
    // Total des tâches
    const total = tasks.length;
    
    // ✅ Nombre de tâches du jour (aujourd'hui)
    const todayTasksCount = tasks.filter(t => {
        if (!t.date_planifiee) return false;
        return t.date_planifiee.startsWith(today);
    }).length;
    
    // ✅ Nombre de tâches à venir (futures et non terminées)
    const upcomingTasksCount = tasks.filter(t => {
        if (t.terminee) return false;
        if (!t.date_planifiee) return false;
        return t.date_planifiee > today;
    }).length;
    
    document.getElementById('totalTasks').textContent = total;
    document.getElementById('todayTasksCount').textContent = todayTasksCount;
    document.getElementById('upcomingTasksCount').textContent = upcomingTasksCount;
}

// ================= RENDU GLOBAL =================
function renderAll() {
    renderCalendar();
    renderUpcomingTasks();
    renderTodayTasks();
    updateStats();
}

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
        });

        const result = await response.json();
        console.log('📥 Résultat brut de l\'API pour la tâche:', result);

        const taskData = result.data || result;
        
        if (taskData && taskData.id) {
            document.getElementById('editTaskId').value = taskData.id;
            document.getElementById('editType').value = taskData.type || '';
            document.getElementById('editTitre').value = taskData.titre || '';
            document.getElementById('editPriorite').value = taskData.priorite || 'moyenne';
            document.getElementById('editDescription').value = taskData.description || '';
            document.getElementById('editStatut').value = taskData.terminee ? '1' : '0';
            
            const targetAnimalId = taskData.animal_id || (taskData.animal ? taskData.animal.id : null);
            console.log('🐖 ID de l\'animal détecté dans la tâche:', targetAnimalId);

            const editAnimalSelect = document.getElementById('editAnimal');
            if (editAnimalSelect) {
                if (targetAnimalId !== null && targetAnimalId !== undefined) {
                    editAnimalSelect.value = targetAnimalId.toString();
                    
                    if (editAnimalSelect.value === "") {
                        console.warn(`⚠️ L'ID animal ${targetAnimalId} n'a pas été trouvé dans les options existantes du select.`);
                        for (let option of editAnimalSelect.options) {
                            if (option.value == targetAnimalId) {
                                option.selected = true;
                                break;
                            }
                        }
                    }
                } else {
                    editAnimalSelect.value = '';
                }
            }
            
            if (taskData.date_planifiee) {
                const date = new Date(taskData.date_planifiee);
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                const hours = String(date.getHours()).padStart(2, '0');
                const minutes = String(date.getMinutes()).padStart(2, '0');
                document.getElementById('editDate').value = `${year}-${month}-${day}T${hours}:${minutes}`;
            }
            
            document.getElementById('editError').style.display = 'none';
            
            const editModalEl = document.getElementById('editTaskModal');
            if (window.bootstrap && bootstrap.Modal) {
                let modalInstance = bootstrap.Modal.getInstance(editModalEl);
                if (!modalInstance) modalInstance = new bootstrap.Modal(editModalEl);
                modalInstance.show();
            } else {
                $(editModalEl).modal('show');
            }
        } else {
            showToast(result.message || 'Erreur lors du chargement', 'danger');
        }
    } catch (error) {
        console.error('❌ Erreur openEditModal:', error);
        showToast('Erreur lors du chargement de la tâche', 'danger');
    }
}

document.getElementById('editTaskForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('editSubmitBtn');
    const errorDiv = document.getElementById('editError');
    errorDiv.style.display = 'none';
    
    const taskId = parseInt(document.getElementById('editTaskId').value);
    const animalId = document.getElementById('editAnimal').value;
    const type = document.getElementById('editType').value;
    const titre = document.getElementById('editTitre').value.trim();
    const date = document.getElementById('editDate').value;
    const priorite = document.getElementById('editPriorite').value;
    const description = document.getElementById('editDescription').value.trim();
    const terminee = document.getElementById('editStatut').value === '1';
    
    if (!titre || !date) {
        showToast('Veuillez remplir les champs requis', 'warning');
        return;
    }
    
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

// ================= MODALE SUPPRESSION =================
function openDeleteModal(taskId) {
    const task = tasks.find(t => t.id === taskId);
    if (!task) return;
    
    document.getElementById('deleteTaskId').value = taskId;
    document.getElementById('deleteTaskName').textContent = 
        `Êtes-vous sûr de vouloir supprimer la tâche "${task.titre || task.type}" ?`;
    $('#deleteTaskModal').modal('show');
}

document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
    const taskId = parseInt(document.getElementById('deleteTaskId').value);
    const btn = this;
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Suppression...';
    
    try {
        const result = await deleteTask(taskId);
        if (result.status === 'success' || result.success === true) {
            $('#deleteTaskModal').modal('hide');
            showToast('Tâche supprimée avec succès', 'success');
            await loadData();
        } else {
            showToast(result.message || 'Erreur lors de la suppression', 'danger');
        }
    } catch (error) {
        showToast(error.message || 'Erreur lors de la suppression', 'danger');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-trash-alt"></i> Supprimer';
    }
});

// ================= INITIALISATION =================
document.addEventListener('DOMContentLoaded', function() {
    if (!token) {
        showToast('Non connecté. Redirection...', 'danger');
        window.location.href = '/auth/login';
        return;
    }
    
    loadData();
});
</script>

@endsection
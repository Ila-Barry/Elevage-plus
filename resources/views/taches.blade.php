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

                    <div class="modal-footer" style="border-top: none; padding: 16px 24px 24px 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; gap: 15px;">
        <button
            type="button"
            class="btn btn-cancel"
            data-bs-dismiss="modal"
            onclick="window.location.reload();"
            style="
                background: white;
                color: #000000;
                border: 2px solid #d1d1d1;
                padding: 10px 30px;
                border-radius: 10px;
                font-weight: 600;
                font-size: 14px;
                cursor: pointer;
                transition: all 0.2s ease;
                min-width: 120px;
            "
            onmouseover="this.style.background='#f8f9fa'"
            onmouseout="this.style.background='white'">
            ❌ Annuler
        </button>
        <button 
            type="submit" 
            class="btn btn-success" 
            id="addSubmitBtn"
            style="
                background: #198754;
                color: white;
                border: none;
                padding: 10px 30px;
                border-radius: 10px;
                font-weight: 600;
                font-size: 14px;
                cursor: pointer;
                transition: all 0.2s ease;
                min-width: 120px;
            "
            onmouseover="this.style.background='#146c43'"
            onmouseout="this.style.background='#198754'">
            ✅ Ajouter
        </button>
    </div>
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

                    <div class="modal-footer" style="border-top: none; padding: 16px 24px 24px 24px;">
                        <div class="btn-container">
                            <button
                                type="button"
                                class="btn-cancel-modal"
                                data-bs-dismiss="modal"
                                onclick="window.location.reload();">
                                ❌ Annuler
                            </button>
                            <button 
                                type="submit" 
                                class="btn-add-modal" 
                                id="editSubmitBtn">
                                ✅ Enregistrer
                            </button>
                        </div>
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
// ⚙️ CONFIGURATION DE L'API ET AUTHENTIFICATION
// =====================================================

// URL de base de l'API (ex: http://monsite.com/api)
const API_URL = window.location.origin + '/api';

// Récupération du token CSRF pour la sécurité des formulaires
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '';

// Récupération du token JWT stocké dans le localStorage
// On enlève les guillemets si présents avec replace()
const rawToken = localStorage.getItem('access_token');
const token = rawToken ? rawToken.replace(/^"(.*)"$/, '$1') : null;

// Affichage dans la console pour vérifier que tout est OK
console.log('🔍 Taches init:', { 
    API_URL, 
    token: token ? 'OK' : 'MISSING' // On affiche OK si token existe, MISSING sinon
});

// =====================================================
// 📦 VARIABLES GLOBALES
// =====================================================

let tasks = [];          // Tableau qui contiendra toutes les tâches
let animals = [];        // Tableau qui contiendra tous les animaux
let currentMonth = new Date().getMonth();  // Mois actuel (0 = Janvier)
let currentYear = new Date().getFullYear(); // Année actuelle
let currentView = 'month'; // Vue par défaut: 'month', 'week' ou 'day'
let weekOffset = 0;      // Décalage pour la navigation semaine (-1, 0, 1, ...)
let toastTimeout = null; // Pour gérer les notifications temporaires

// =====================================================
// 🔔 SYSTÈME DE NOTIFICATIONS (TOAST)
// =====================================================

function showToast(message, type = 'info') {
    // Supprime l'ancien toast s'il existe pour éviter les doublons
    document.querySelector('.custom-toast')?.remove();
    
    // Annule le timeout précédent si la fonction est rappelée
    if (toastTimeout) clearTimeout(toastTimeout);

    // Création de l'élément toast
    const toast = document.createElement('div');
    toast.className = `custom-toast ${type}`; // Ajoute la classe CSS selon le type
    
    // Définition des icônes pour chaque type de notification
    const icons = {
        success: 'fa-check-circle',   // ✅ Succès
        danger: 'fa-exclamation-circle', // ❌ Erreur
        warning: 'fa-exclamation-triangle', // ⚠️ Avertissement
        info: 'fa-info-circle'        // ℹ️ Information
    };

    // Structure HTML du toast avec l'icône et le message
    toast.innerHTML = `
        <div class="toast-content">
            <i class="fas ${icons[type] || icons.info}"></i>
            <span>${message}</span>
        </div>
    `;

    // Ajout du toast dans la page
    document.body.appendChild(toast);

    // Animation d'apparition (ajout de la classe 'show' après 10ms)
    setTimeout(() => toast.classList.add('show'), 10);

    // Auto-suppression après 3 secondes
    toastTimeout = setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// =====================================================
// 🌐 APPELS API (BACKEND)
// =====================================================

/**
 * Récupère toutes les tâches depuis l'API
 * @returns {Object} { status: 'success'|'error', data: [] }
 */
async function fetchTasks() {
    try {
        // Envoi d'une requête GET à l'API
        const response = await fetch(`${API_URL}/taches`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',        // On attend du JSON
                'Authorization': 'Bearer ' + token,  // Authentification JWT
                'X-CSRF-TOKEN': CSRF_TOKEN           // Protection CSRF
            }
        });
        
        // Conversion de la réponse en JSON
        const result = await response.json();
        
        // Si la réponse n'est pas OK, on lance une erreur
        if (!response.ok) throw result;
        
        // Extraction des données (compatibilité avec différents formats d'API)
        const data = result.data?.data || result.data || [];
        
        return { status: 'success', data };
        
    } catch (error) {
        console.error('❌ fetchTasks error:', error);
        return { status: 'error', data: [] };
    }
}

/**
 * Récupère tous les animaux depuis l'API
 * @returns {Object} { success: true|false, data: [] }
 */
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

/**
 * Crée une nouvelle tâche
 * @param {Object} data - Données de la tâche à créer
 * @returns {Object} Réponse de l'API
 */
async function createTask(data) {
    const response = await fetch(`${API_URL}/taches`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',      // On envoie du JSON
            'Accept': 'application/json',
            'Authorization': 'Bearer ' + token,
            'X-CSRF-TOKEN': CSRF_TOKEN
        },
        body: JSON.stringify(data) // Conversion des données en JSON
    });
    
    const result = await response.json();
    if (!response.ok) throw result;
    return result;
}

/**
 * Met à jour une tâche existante
 * @param {number} id - ID de la tâche à modifier
 * @param {Object} data - Nouvelles données de la tâche
 * @returns {Object} Réponse de l'API
 */
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

/**
 * Supprime une tâche
 * @param {number} id - ID de la tâche à supprimer
 * @returns {Object} Réponse de l'API
 */
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

/**
 * Bascule le statut d'une tâche (terminée / non terminée)
 * @param {number} id - ID de la tâche
 */
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
        
        // Recharge les données pour mettre à jour l'affichage
        await loadData();
        showToast('Statut mis à jour', 'success');
        return result;
        
    } catch (error) {
        console.error('toggleTaskComplete error:', error);
        showToast('Erreur lors de la mise à jour du statut', 'danger');
    }
}

/**
 * Déplace une tâche vers une nouvelle date (Drag & Drop)
 * @param {number} id - ID de la tâche
 * @param {string} date - Nouvelle date au format YYYY-MM-DD
 */
async function moveTask(id, date) {
    try {
        await updateTask(id, { date_planifiee: date });
        showToast('Tâche déplacée avec succès', 'success');
        await loadData(); // Recharge les données pour rafraîchir l'affichage
    } catch (error) {
        console.error('moveTask error:', error);
        showToast('Erreur déplacement tâche', 'danger');
    }
}

// =====================================================
// 🧭 NAVIGATION DU CALENDRIER
// =====================================================

/**
 * Change la semaine ou le mois selon la vue active
 * @param {number} delta - Direction du changement (-1 pour gauche, 1 pour droite)
 */
function changeWeek(delta) {
    if (currentView === 'month') {
        // Changement du mois
        currentMonth += delta;
        
        // Gestion du changement d'année
        if (currentMonth > 11) {
            currentMonth = 0;      // Janvier
            currentYear++;         // Année suivante
        }
        if (currentMonth < 0) {
            currentMonth = 11;     // Décembre
            currentYear--;         // Année précédente
        }
    } else if (currentView === 'week') {
        // Changement de semaine (on décale de 7 jours)
        weekOffset += delta;
    }
    
    renderCalendar(); // On redessine le calendrier
}

/**
 * Change la vue du calendrier (Mois / Semaine / Jour)
 * @param {string} view - 'month', 'week' ou 'day'
 */
function switchView(view) {
    currentView = view;
    if (view === 'month') weekOffset = 0; // Reset de la semaine en mode mois
    
    // Met à jour les classes 'active' des boutons
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.view === view);
    });
    
    renderCalendar();
    showToast(`Vue ${view}`, 'info');
}

/**
 * Affiche les détails d'une tâche dans un toast
 * @param {number} taskId - ID de la tâche
 */
function selectTask(taskId) {
    const task = tasks.find(t => t.id === taskId);
    if (task) {
        const date = new Date(task.date_planifiee);
        showToast(`📋 ${task.type_label || task.type} - ${task.animal?.nom || 'N/A'} (${date.toLocaleDateString('fr-FR')})`, 'info');
    }
}

/**
 * Action quand on clique sur une date du calendrier
 * @param {string} dateStr - Date au format YYYY-MM-DD
 */
function selectDate(dateStr) {
    showToast(`📅 Date sélectionnée: ${dateStr}`, 'info');
}

// =====================================================
// 📅 RENDU DU CALENDRIER
// =====================================================

/**
 * Fonction principale qui dessine le calendrier
 * selon la vue active (month, week ou day)
 */
function renderCalendar() {
    const tbody = document.getElementById('calendarBody'); // Conteneur du tableau
    const today = new Date(); // Date du jour
    const title = document.getElementById('currentMonthYear'); // Titre du calendrier

    // ========== MISE À JOUR DU TITRE ==========
    if (title) {
        if (currentView === 'month') {
            // Affichage : "Janvier 2026"
            title.textContent = new Date(currentYear, currentMonth)
                .toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' });
                
        } else if (currentView === 'week') {
            // Affichage : "1 - 7 Janvier 2026"
            const start = new Date();
            start.setDate(start.getDate() - start.getDay() + 1 + (weekOffset * 7));
            const end = new Date(start);
            end.setDate(start.getDate() + 6);
            
            const startText = start.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long' });
            const endText = end.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });
            title.textContent = `${startText} - ${endText}`;
            
        } else {
            // Mode jour : "Mardi 1 Janvier 2026"
            title.textContent = today.toLocaleDateString('fr-FR', {
                weekday: 'long',
                day: 'numeric',
                month: 'long'
            });
        }
    }

    let html = ''; // Variable qui va contenir le HTML du tableau

    // ========== VUE MOIS ==========
    if (currentView === 'month') {
        const firstDay = new Date(currentYear, currentMonth, 1); // 1er jour du mois
        const lastDay = new Date(currentYear, currentMonth + 1, 0); // Dernier jour du mois
        const daysInMonth = lastDay.getDate(); // Nombre de jours dans le mois
        
        // Calcul du décalage : combien de jours avant le 1er du mois dans la semaine
        // Si Lundi = 1, Mardi = 2, ...
        const startOffset = (firstDay.getDay() === 0) ? 6 : firstDay.getDay() - 1;

        let day = 1;          // Jour actuel dans le mois
        let nextMonthDay = 1; // Jour du mois suivant (pour remplir la dernière ligne)
        let row = 0;          // Ligne en cours

        // Boucle pour générer les lignes (semaines) du calendrier
        // On continue tant qu'on a des jours dans le mois ou jusqu'à avoir 4 jours du mois suivant
        while (day <= daysInMonth || nextMonthDay <= 4) {
            html += '<tr>'; // Nouvelle ligne (semaine)
            
            for (let i = 0; i < 7; i++) {
                // ====== JOURS DU MOIS PRÉCÉDENT (grisés) ======
                if (row === 0 && i < startOffset) {
                    const prevDate = new Date(currentYear, currentMonth, 0);
                    const prevDay = prevDate.getDate() - startOffset + i + 1;
                    html += `<td class="other-month">${prevDay}</td>`;
                    
                // ====== JOURS DU MOIS ACTUEL ======
                } else if (day <= daysInMonth) {
                    // Construction de la date au format YYYY-MM-DD
                    const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                    
                    // Vérifie s'il y a des tâches ce jour-là (non terminées)
                    const hasTask = tasks.some(t => {
                        const d = (t.date_planifiee ?? '').substring(0, 10);
                        return d === dateStr && !(t.terminee == 1);
                    });
                    
                    // Vérifie si c'est aujourd'hui
                    const isToday = dateStr === today.toISOString().split('T')[0];
                    
                    // Construction de la cellule
                    html += `<td 
                                class="${hasTask ? 'task-day' : ''} ${isToday ? 'today' : ''}" 
                                data-date="${dateStr}" 
                                onclick="selectDate('${dateStr}')" 
                                ondrop="dropTask(event)" 
                                ondragover="allowDrop(event)">
                                ${day}
                            </td>`;
                    day++;
                    
                // ====== JOURS DU MOIS SUIVANT (grisés) ======
                } else {
                    html += `<td class="other-month">${nextMonthDay}</td>`;
                    nextMonthDay++;
                }
            }
            
            html += '</tr>';
            row++;
            if (row > 6) break; // Sécurité : maximum 6 lignes
        }
    }

    // ========== VUE SEMAINE ==========
    else if (currentView === 'week') {
        // Calcule le début de la semaine (lundi) avec le décalage (weekOffset)
        const start = new Date();
        start.setDate(start.getDate() - start.getDay() + 1 + (weekOffset * 7));
        
        html += '<tr>';
        for (let i = 0; i < 7; i++) {
            const d = new Date(start);
            d.setDate(start.getDate() + i);
            const dateStr = d.toISOString().split('T')[0];
            
            // Vérifie s'il y a des tâches ce jour
            const hasTask = tasks.some(t => {
                const tDate = (t.date_planifiee ?? '').substring(0, 10);
                return tDate === dateStr && !(t.terminee == 1);
            });
            
            html += `<td 
                        class="${hasTask ? 'task-day' : ''}" 
                        data-date="${dateStr}" 
                        onclick="selectDate('${dateStr}')" 
                        ondrop="dropTask(event)" 
                        ondragover="allowDrop(event)">
                            ${d.getDate()}<br>
                            <small>${d.toLocaleDateString('fr-FR', { weekday: 'short' })}</small>
                    </td>`;
        }
        html += '</tr>';
    }

    // ========== VUE JOUR ==========
    else {
        const dateStr = today.toISOString().split('T')[0];
        const dayTasks = tasks.filter(t => {
            const d = (t.date_planifiee ?? '').substring(0, 10);
            return d === dateStr;
        });
        
        html = `<tr>
                    <td colspan="7">
                        <strong>Tâches du jour</strong>
                        ${dayTasks.length ? 
                            dayTasks.map(t => `
                                <div class="task-item task-draggable" data-id="${t.id}">
                                    ${t.titre || t.type}
                                </div>
                            `).join('') 
                            : '<p>Aucune tâche</p>'}
                    </td>
                </tr>`;
    }

    // ========== INJECTION DANS LE DOM ==========
    tbody.innerHTML = html;
    
    // Active le Drag & Drop sur les tâches
    enableDrag();
}

// =====================================================
// 🖱️ DRAG & DROP (DÉPLACEMENT DES TÂCHES)
// =====================================================

/**
 * Rend les tâches "draggable" (déplaçables)
 * Appelée après chaque rendu du calendrier
 */
function enableDrag() {
    // Sélection de tous les éléments qui doivent être déplaçables
    document.querySelectorAll('.task-draggable').forEach(el => {
        // Active la propriété draggable HTML5
        el.setAttribute('draggable', true);
        
        // ====== DÉBUT DU DRAG ======
        el.ondragstart = (e) => {
            // Stocke l'ID de la tâche dans le drag pour le récupérer au drop
            e.dataTransfer.setData('taskId', el.dataset.id);
            // Effet visuel : la tâche devient semi-transparente
            el.classList.add('dragging');
        };
        
        // ====== FIN DU DRAG ======
        el.ondragend = () => {
            // Enlève l'effet visuel
            el.classList.remove('dragging');
        };
    });
}

/**
 * Autorise le dépôt (drop) sur une cellule du calendrier
 * @param {Event} e - L'événement drag
 */
function allowDrop(e) {
    e.preventDefault(); // Nécessaire pour autoriser le drop
    e.currentTarget.classList.add('drag-over'); // Effet visuel
}

/**
 * Gère le dépôt (drop) d'une tâche sur une nouvelle date
 * @param {Event} e - L'événement drop
 */
function dropTask(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('drag-over');
    
    // Récupère la nouvelle date depuis la cellule où on a déposé
    const newDate = e.currentTarget.dataset.date;
    // Récupère l'ID de la tâche depuis le drag
    const taskId = e.dataTransfer.getData('taskId');
    
    if (!taskId || !newDate) return;
    
    // Déplace la tâche vers la nouvelle date
    moveTask(taskId, newDate);
}

// =====================================================
// 📋 RENDU DES LISTES DE TÂCHES
// =====================================================

function renderTaskLists() {
    console.log('🔄 Rendu des listes de tâches...');
    
    // ====== TÂCHES À VENIR ======
    const upcomingList = document.getElementById('upcomingTasks');
    if (upcomingList) {
        const today = new Date().toISOString().split('T')[0];
        
        const upcomingTasks = tasks
            .filter(t => {
                const taskDate = (t.date_planifiee ?? '').substring(0, 10);
                return taskDate > today && !(t.terminee == 1);
            })
            .sort((a, b) => new Date(a.date_planifiee) - new Date(b.date_planifiee))
            .slice(0, 10);
        
        console.log('📋 Tâches à venir:', upcomingTasks.length);
        
        if (upcomingTasks.length === 0) {
            upcomingList.innerHTML = `
                <li style="padding: 15px; text-align: center; color: #6c757d;">
                    <i class="fas fa-check-circle" style="color: #28a745;"></i>
                    Aucune tâche à venir
                </li>
            `;
        } else {
            upcomingList.innerHTML = upcomingTasks.map(t => `
                <li>
                    <a href="#" class="upcoming-link" onclick="selectTask(${t.id})">
                        <i class="fas fa-tasks" style="color: #198754;"></i>
                        <div style="flex: 1;">
                            <strong>${t.titre || t.type}</strong>
                            <br>
                            <small style="color: #6c757d;">
                                📅 ${new Date(t.date_planifiee).toLocaleDateString('fr-FR')}
                                ${t.animal ? `• 🐄 ${t.animal.nom}` : ''}
                            </small>
                        </div>
                        <span class="badge" style="background: ${getPriorityColor(t.priorite)}; color: white; padding: 2px 8px; border-radius: 12px; font-size: 11px;">
                            ${t.priorite || 'moyenne'}
                        </span>
                    </a>
                </li>
            `).join('');
        }
    }
    
    // ====== TÂCHES DU JOUR ======
    const todayContainer = document.getElementById('todayTasks');
    const todayDateSpan = document.getElementById('todayDate');
    
    if (todayContainer) {
        const today = new Date();
        const todayStr = today.toISOString().split('T')[0];
        
        if (todayDateSpan) {
            todayDateSpan.textContent = today.toLocaleDateString('fr-FR', {
                weekday: 'long',
                day: 'numeric',
                month: 'long'
            });
        }
        
        const todayTasks = tasks.filter(t => {
            const taskDate = (t.date_planifiee ?? '').substring(0, 10);
            return taskDate === todayStr;
        });
        
        console.log('📋 Tâches du jour:', todayTasks.length);
        
        if (todayTasks.length === 0) {
            todayContainer.innerHTML = `
                <div style="padding: 20px; text-align: center; color: #6c757d;">
                    <i class="fas fa-calendar-check" style="font-size: 24px; color: #198754;"></i>
                    <p style="margin-top: 10px;">Aucune tâche pour aujourd'hui</p>
                </div>
            `;
        } else {
            todayContainer.innerHTML = todayTasks.map(t => `
                <div class="task-item ${t.terminee == 1 ? 'completed' : ''}">
                    <div class="task-time">
                        ${t.titre || t.type}
                        ${t.terminee == 1 ? '<span class="badge-completed">✅ Terminée</span>' : ''}
                        <small style="color: #6c757d; font-size: 12px; font-weight: normal;">
                            ${t.date_planifiee ? new Date(t.date_planifiee).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }) : ''}
                        </small>
                    </div>
                    
                    ${t.description ? `<div class="task-desc">${t.description}</div>` : ''}
                    
                    <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-top: 8px;">
                        <span class="badge" style="background: ${getPriorityColor(t.priorite)}; color: white; padding: 2px 10px; border-radius: 12px; font-size: 11px;">
                            ${t.priorite || 'moyenne'}
                        </span>
                        ${t.animal ? `<span style="font-size: 12px; color: #6c757d;">🐄 ${t.animal.nom}</span>` : ''}
                    </div>
                    
                    <div class="task-actions">
                        <button class="btn-hide-task" onclick="toggleTask(${t.id})">
                            ✅ Masquer fait
                        </button>
                        <button class="btn-edit-task" onclick="openEditModal(${t.id})">
    <i class="fas fa-pen" style="color: #dc3545;"></i> Modifier
</button>
                    </div>
                </div>
            `).join('');
        }
    }
}
// =====================================================
// 📊 STATISTIQUES - COMPTEURS EN HAUT DE PAGE
// =====================================================

/**
 * Met à jour les compteurs de statistiques dans le header
 * Cette fonction calcule et affiche :
 * - Le nombre total de toutes les tâches
 * - Le nombre de tâches prévues pour aujourd'hui
 * - Le nombre de tâches à venir (futures)
 * 
 * Elle est appelée à chaque fois que les données changent :
 * - Après chargement de la page
 * - Après création d'une tâche
 * - Après modification d'une tâche
 * - Après suppression d'une tâche
 * - Après changement de statut (terminée/réouverte)
 */
function updateStats() {
    // ====== 1. CALCUL DU TOTAL ======
    // Récupère le nombre total de tâches (toutes confondues)
    const total = tasks.length;
    
    // ====== 2. DATE D'AUJOURD'HUI ======
    // Récupère la date du jour au format YYYY-MM-DD
    // Exemple : "2026-07-02"
    const today = new Date().toISOString().split('T')[0];
    
    // ====== 3. COMPTE DES TÂCHES D'AUJOURD'HUI ======
    // Parcourt toutes les tâches et ne garde que celles :
    // - Dont la date correspond à aujourd'hui
    // - ET qui ne sont pas terminées (terminee == 0 ou false)
    // .length donne le nombre de tâches correspondantes
    const todayCount = tasks.filter(t => {
        // Extrait uniquement la date (YYYY-MM-DD) de la date_planifiee
        const taskDate = (t.date_planifiee ?? '').substring(0, 10);
        // Compare avec aujourd'hui et vérifie que la tâche n'est pas terminée
        return taskDate === today && !(t.terminee == 1);
    }).length;
    
    // ====== 4. COMPTE DES TÂCHES À VENIR ======
    // Parcourt toutes les tâches et ne garde que celles :
    // - Dont la date est POSTÉRIEURE à aujourd'hui (futures)
    // - ET qui ne sont pas terminées (terminee == 0 ou false)
    // .length donne le nombre de tâches correspondantes
    const upcomingCount = tasks.filter(t => {
        // Extrait uniquement la date (YYYY-MM-DD) de la date_planifiee
        const taskDate = (t.date_planifiee ?? '').substring(0, 10);
        // Compare avec aujourd'hui (taskDate > today = dans le futur)
        // ET vérifie que la tâche n'est pas terminée
        return taskDate > today && !(t.terminee == 1);
    }).length;
    
    // ====== 5. MISE À JOUR DE L'AFFICHAGE ======
    // Insère les valeurs calculées dans les éléments HTML correspondants
    // Ces éléments sont dans le header de la page (stat-badge)
    document.getElementById('totalTasks').textContent = total;          // Total des tâches
    document.getElementById('todayTasksCount').textContent = todayCount; // Tâches d'aujourd'hui
    document.getElementById('upcomingTasksCount').textContent = upcomingCount; // Tâches à venir
}

// =====================================================
// 🎨 COULEUR DE PRIORITÉ
// =====================================================

function getPriorityColor(priority) {
    const colors = {
        'urgente': '#dc3545',   // Rouge
        'haute': '#fd7e14',      // Orange
        'moyenne': '#ffc107',    // Jaune
        'basse': '#28a745'       // Vert
    };
    return colors[priority] || '#6c757d';
}

/**
 * Rafraîchit tout l'affichage
 */
function renderAll() {
    renderCalendar();      // Calendrier
    renderTaskLists();     // Listes de tâches
    updateStats();         // Statistiques
}

// =====================================================
// 📋 REMPLISSAGE DES SÉLECTEURS D'ANIMAUX
// =====================================================

/**
 * Remplit les <select> des modales avec la liste des animaux
 * Utilise les IDs 'addAnimal' et 'editAnimal'
 */
function populateAnimalSelects() {
    // Pour chaque select à remplir
    ['addAnimal', 'editAnimal'].forEach(id => {
        const select = document.getElementById(id);
        if (!select) return; // Si le select n'existe pas, on passe
        
        // On réinitialise avec une option vide
        select.innerHTML = `<option value="">Sélectionner un animal</option>`;
        
        // On ajoute chaque animal comme option
        animals.forEach(a => {
            const option = document.createElement('option');
            option.value = a.id; // La valeur est l'ID de l'animal
            option.textContent = `${a.nom || 'Animal'} (${a.espece || ''})`;
            select.appendChild(option);
        });
    });
}

// =====================================================
// 🚀 CHARGEMENT GLOBAL DES DONNÉES
// =====================================================

/**
 * Fonction principale qui charge toutes les données nécessaires à la page
 * 
 * Cette fonction est le point d'entrée pour l'initialisation et les mises à jour
 * Elle est appelée :
 * - Au chargement de la page (DOMContentLoaded)
 * - Après la création d'une tâche
 * - Après la modification d'une tâche  
 * - Après la suppression d'une tâche
 * - Après le changement de statut d'une tâche
 * - Après le déplacement d'une tâche (Drag & Drop)
 * 
 * Elle garantit que toutes les données et l'interface sont synchronisées
 */
async function loadData() {
    // ====== 1. DÉBUT DU CHARGEMENT ======
    // Affiche un message dans la console pour suivre l'exécution
    console.log('🔄 Chargement des données...');
    
    try {
        // ==========================================
        // 2. CHARGEMENT DES TÂCHES
        // ==========================================
        // Appelle l'API pour récupérer toutes les tâches
        // La fonction fetchTasks() retourne un objet avec { status, data }
        const tasksResult = await fetchTasks();
        
        // Extrait les données (si data n'existe pas, on met un tableau vide)
        // Compatibilité avec différents formats d'API :
        // - { data: [...] } ou { data: { data: [...] } }
        tasks = tasksResult.data || [];
        
        // Affiche le nombre de tâches chargées dans la console
        console.log('✅ Tâches chargées:', tasks.length, 'tâches');
        
        // ==========================================
        // 3. CHARGEMENT DES ANIMAUX
        // ==========================================
        // Appelle l'API pour récupérer tous les animaux
        // La fonction fetchAnimals() retourne un objet avec { success, data }
        const animalsResult = await fetchAnimals();
        
        // Extrait les données (si data n'existe pas, on met un tableau vide)
        animals = animalsResult.data || [];
        
        // Affiche le nombre d'animaux chargés dans la console
        console.log('✅ Animaux chargés:', animals.length, 'animaux');
        
        // ==========================================
        // 4. REMPLISSAGE DES SÉLECTEURS
        // ==========================================
        // Remplit les listes déroulantes (select) dans les modales
        // avec la liste des animaux récupérés
        // Cela permet de choisir un animal dans les formulaires d'ajout/modification
        populateAnimalSelects();
        
        // ==========================================
        // 5. MISE À JOUR DE L'INTERFACE
        // ==========================================
        // Appelle la fonction qui coordonne tous les rendus :
        // - Le calendrier (avec les points verts)
        // - Les listes de tâches (À venir et Aujourd'hui)
        renderAll();
        
        // ==========================================
        // 6. MISE À JOUR DES STATISTIQUES
        // ==========================================
        // Met à jour les compteurs en haut de page :
        // - Total des tâches
        // - Tâches d'aujourd'hui
        // - Tâches à venir
        // Cette fonction utilise le tableau 'tasks' qui vient d'être chargé
        updateStats();
        
        // ==========================================
        // 7. FIN DU CHARGEMENT
        // ==========================================
        // Confirme que tout s'est bien passé dans la console
        console.log('✅ Affichage mis à jour');
        
    } catch (error) {
        // ==========================================
        // 8. GESTION DES ERREURS
        // ==========================================
        // Si une erreur survient à n'importe quelle étape :
        // - Affiche l'erreur dans la console pour le débogage
        console.error('❌ loadData error:', error);
        
        // - Affiche une notification à l'utilisateur (toast rouge)
        showToast('Erreur chargement données', 'danger');
        
        // La page reste fonctionnelle mais avec des données potentiellement incomplètes
        // L'utilisateur peut réessayer en rechargeant la page
    }
}

// =====================================================
// 🏗️ MODALE AJOUTER UNE TÂCHE
// =====================================================

/**
 * Ouvre la modale d'ajout de tâche
 */
function openAddModal() {
    console.log('🟢 Ouverture du modal Ajouter');
    
    // Réinitialise le formulaire (efface les champs)
    document.getElementById('addTaskForm').reset();
    document.getElementById('addAnimal').value = "";
    
    // Définit la date par défaut : maintenant (en format local)
    const now = new Date();
    const localDatetime = new Date(now.getTime() - now.getTimezoneOffset() * 60000)
        .toISOString()
        .slice(0, 16);
    document.getElementById('addDate').value = localDatetime;
    
    // Cache le message d'erreur si affiché
    document.getElementById('addError').style.display = 'none';
    
    // Ouvre la modale avec Bootstrap
    const modalEl = document.getElementById('addTaskModal');
    let modal = bootstrap.Modal.getInstance(modalEl);
    if (!modal) {
        modal = new bootstrap.Modal(modalEl);
    }
    modal.show();
}

// =====================================================
// 🔧 MODALE MODIFIER UNE TÂCHE
// =====================================================

/**
 * Ouvre la modale de modification avec les données de la tâche
 * @param {number} taskId - ID de la tâche à modifier
 */
async function openEditModal(taskId) {
    console.log('📝 Ouverture du modal de modification pour la tâche:', taskId);

    try {
        // Récupère les données de la tâche depuis l'API
        const response = await fetch(`${API_URL}/taches/${taskId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token
            }
        });

        if (!response.ok) {
            throw new Error('Erreur lors du chargement de la tâche');
        }

        const result = await response.json();
        const task = result.data || result; // Compatibilité des formats

        // ====== REMPLISSAGE DU FORMULAIRE ======
        document.getElementById('editTaskId').value = task.id;
        document.getElementById('editAnimal').value = task.animal_id || '';
        document.getElementById('editType').value = task.type || '';
        document.getElementById('editTitre').value = task.titre || '';
        
        // Formatage de la date pour l'input datetime-local
        document.getElementById('editDate').value = task.date_planifiee ? 
            new Date(task.date_planifiee).toISOString().slice(0, 16) : '';
            
        document.getElementById('editPriorite').value = task.priorite || 'moyenne';
        document.getElementById('editDescription').value = task.description || '';
        document.getElementById('editStatut').value = task.terminee ? 1 : 0;

        // Cache le message d'erreur
        document.getElementById('editError').style.display = 'none';

        // Ouvre la modale
        const modalElement = document.getElementById('editTaskModal');
        const modal = new bootstrap.Modal(modalElement);
        modal.show();

    } catch (error) {
        console.error('❌ Erreur openEditModal:', error);
        showToast('Erreur lors du chargement de la tâche', 'danger');
    }
}

// =====================================================
// 🗑️ SUPPRESSION D'UNE TÂCHE
// =====================================================

/**
 * Ouvre la modale de confirmation de suppression
 * @param {number} taskId - ID de la tâche à supprimer
 * @param {string} taskName - Nom de la tâche pour le message
 */
function openDeleteModal(taskId, taskName) {
    document.getElementById('deleteTaskId').value = taskId;
    document.getElementById('deleteTaskName').textContent = 
        `Êtes-vous sûr de vouloir supprimer "${taskName || 'cette tâche'}" ?`;
    const modal = new bootstrap.Modal(document.getElementById('deleteTaskModal'));
    modal.show();
}

// Gestionnaire du bouton "Confirmer la suppression"
document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
    const taskId = document.getElementById('deleteTaskId').value;
    
    try {
        await deleteTask(taskId);
        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteTaskModal'));
        if (modal) modal.hide();
        
        sessionStorage.setItem('toastSuccess', 'Tâche supprimée avec succès !');
        setTimeout(() => window.location.reload(), 300);
    } catch (error) {
        showToast('Erreur lors de la suppression', 'danger');
    }
});

// =====================================================
// 📝 GESTION DES FORMULAIRES
// =====================================================

// ====== FORMULAIRE D'AJOUT ======
document.getElementById('addTaskForm').addEventListener('submit', async function(e) {
    e.preventDefault(); // Empêche le rechargement de la page
    
    const submitBtn = document.getElementById('addSubmitBtn');
    const errorDiv = document.getElementById('addError');
    errorDiv.style.display = 'none';
    
    // Récupération des données du formulaire
    const animalId = document.getElementById('addAnimal').value;
    const type = document.getElementById('addType').value;
    const titre = document.getElementById('addTitre').value.trim();
    const date = document.getElementById('addDate').value;
    const priorite = document.getElementById('addPriorite').value;
    const description = document.getElementById('addDescription').value.trim();
    
    // Validation : vérifie que les champs obligatoires sont remplis
    if (!animalId || !type || !titre || !date) {
        showToast('Veuillez remplir les champs obligatoires', 'warning');
        return;
    }
    
    // Recherche de l'animal pour récupérer l'elevage_id
    const animal = animals.find(a => a.id == animalId);
    if (!animal) {
        showToast('Animal non trouvé', 'danger');
        return;
    }
    
    // Construction des données à envoyer
    const data = {
        animal_id: parseInt(animalId),
        elevage_id: animal.elevage_id,
        titre: titre,
        type: type,
        description: description || null,
        date_planifiee: date,
        priorite: priorite
    };
    
    // Désactive le bouton et affiche un spinner
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> En cours...';
    
    try {
        const result = await createTask(data);
        
        if (result.status === 'success' || result.success === true) {
            // Ferme la modale
            const modal = bootstrap.Modal.getInstance(document.getElementById('addTaskModal'));
            if (modal) modal.hide();
            
            // Stocke un message de succès pour après le rechargement
            sessionStorage.setItem('toastSuccess', 'Tâche ajoutée avec succès !');
            
            // Recharge la page pour voir les nouvelles données
            setTimeout(() => window.location.reload(), 300);
        } else {
            errorDiv.textContent = result.message || 'Erreur lors de la création';
            errorDiv.style.display = 'block';
        }
    } catch (error) {
        errorDiv.textContent = error.message || 'Erreur lors de la création';
        errorDiv.style.display = 'block';
    } finally {
        // Réactive le bouton
        submitBtn.disabled = false;
        submitBtn.innerHTML = '✅ Ajouter';
    }
});

// ====== FORMULAIRE DE MODIFICATION ======
document.getElementById('editTaskForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const taskId = document.getElementById('editTaskId').value;
    const submitBtn = document.getElementById('editSubmitBtn');
    const errorDiv = document.getElementById('editError');
    errorDiv.style.display = 'none';
    
    // Récupération des données du formulaire
    const data = {
        animal_id: parseInt(document.getElementById('editAnimal').value) || null,
        type: document.getElementById('editType').value,
        titre: document.getElementById('editTitre').value.trim(),
        date_planifiee: document.getElementById('editDate').value,
        priorite: document.getElementById('editPriorite').value,
        description: document.getElementById('editDescription').value.trim(),
        terminee: parseInt(document.getElementById('editStatut').value)
    };
    
    // Désactive le bouton
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> En cours...';
    
    try {
        const result = await updateTask(taskId, data);
        
        if (result.status === 'success' || result.success === true) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('editTaskModal'));
            if (modal) modal.hide();
            
            sessionStorage.setItem('toastSuccess', 'Tâche modifiée avec succès !');
            setTimeout(() => window.location.reload(), 300);
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

// =====================================================
// 🔄 BASCULEMENT DU STATUT D'UNE TÂCHE
// =====================================================

/**
 * Bascule une tâche entre "terminée" et "non terminée"
 * @param {number} taskId - ID de la tâche
 */
async function toggleTask(taskId) {
    try {
        const result = await toggleTaskComplete(taskId);
        
        if (result.status === 'success') {
            // Met à jour localement le statut de la tâche
            const task = tasks.find(t => t.id === taskId);
            if (task) task.terminee = !task.terminee;
            
            renderAll(); // Rafraîchit l'affichage
            
            const message = task?.terminee ? '✅ Tâche terminée !' : '🔄 Tâche réouverte';
            const type = task?.terminee ? 'success' : 'info';
            showToast(message, type);
        }
    } catch (error) {
        showToast('Erreur lors de la mise à jour', 'danger');
    }
}

// =====================================================
// 🚀 INITIALISATION AU CHARGEMENT DE LA PAGE
// =====================================================

/**
 * S'exécute automatiquement quand la page est chargée
 * Vérifie l'authentification et charge les données
 */
document.addEventListener('DOMContentLoaded', () => {
    // Vérifie que l'utilisateur est connecté (token présent)
    if (!token) {
        showToast('Non connecté', 'danger');
        window.location.href = '/login'; // Redirige vers la page de connexion
        return;
    }
    
    // Charge les données (tâches + animaux)
    loadData();
});
</script>
@endsection
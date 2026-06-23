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
        <span class="stat-badge done">Terminées: <strong id="doneTasks">0</strong></span>
        <span class="stat-badge pending">En attente: <strong id="pendingTasks">0</strong></span>
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

    </div> <!-- fin tasks-left -->

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
                TÂCHES DU <span id="todayDate">14 MAI 2026</span>
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
                <form id="addTaskForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">🐄 Animal concerné <span class="text-danger">*</span></label>
                        <select class="form-select" id="addAnimal" required>
                            <option value="">Sélectionner un animal</option>
                            <option value="1">Marguerite (n°123)</option>
                            <option value="2">Bella (n°124)</option>
                            <option value="3">Roussette (n°125)</option>
                            <option value="4">Blanchette (n°126)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">🏠 Type de tâche <span class="text-danger">*</span></label>
                        <select class="form-select" id="addType" required>
                            <option value="">Choisir un type</option>
                            <option value="Vaccination">Vaccination</option>
                            <option value="Vermifuge">Vermifuge</option>
                            <option value="Pesée">Pesée</option>
                            <option value="Nettoyage enclos">Nettoyage enclos</option>
                            <option value="Contrôle vétérinaire">Contrôle vétérinaire</option>
                            <option value="Alimentation">Alimentation</option>
                            <option value="Tonte">Tonte</option>
                            <option value="Autre">Autre</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">📅 Date planifiée <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="addDate" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">⏰ Heure (optionnel)</label>
                            <input type="time" class="form-control" id="addTime">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">📝 Notes (optionnelle)</label>
                        <textarea class="form-control" id="addNotes" rows="4" placeholder="Ajouter une description ou une remarque..."></textarea>
                    </div>

                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">❌ Annuler</button>
                        <button type="submit" class="btn btn-success">✅ Ajouter</button>
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
                <form id="editTaskForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editTaskId">

                    <div class="mb-3">
                        <label class="form-label">🐄 Animal concerné</label>
                        <select class="form-select" id="editAnimal">
                            <option value="1">Marguerite (n°123)</option>
                            <option value="2">Bella (n°124)</option>
                            <option value="3">Roussette (n°125)</option>
                            <option value="4">Blanchette (n°126)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">📋 Type de tâche</label>
                        <select class="form-select" id="editType">
                            <option value="Vaccination">Vaccination</option>
                            <option value="Vermifuge">Vermifuge</option>
                            <option value="Pesée">Pesée</option>
                            <option value="Nettoyage enclos">Nettoyage enclos</option>
                            <option value="Contrôle vétérinaire">Contrôle vétérinaire</option>
                            <option value="Alimentation">Alimentation</option>
                            <option value="Tonte">Tonte</option>
                            <option value="Autre">Autre</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">📅 Date planifiée</label>
                            <input type="date" class="form-control" id="editDate">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">⏰ Heure</label>
                            <input type="time" class="form-control" id="editTime">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">📝 Notes</label>
                        <textarea class="form-control" id="editNotes" rows="4"></textarea>
                    </div>

                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// ================= DONNÉES =================
let tasks = [
    { id: 1, animal: "Marguerite (n°123)", type: "Vaccination", date: "2026-05-14", time: "09:00", notes: "Troupeau bovin - Élevage de Thiès", completed: false },
    { id: 2, animal: "Marguerite (n°123)", type: "Pesée", date: "2026-05-14", time: "14:00", notes: "Animal : Marguerite (n°123)", completed: false },
    { id: 3, animal: "Bella (n°124)", type: "Nettoyage enclos", date: "2026-05-14", time: "16:00", notes: "Élevage bovin - Enclos nord", completed: false },
    { id: 4, animal: "Roussette (n°125)", type: "Vermifuge", date: "2026-05-15", time: "10:00", notes: "Vermifuge annuel", completed: false },
    { id: 5, animal: "Blanchette (n°126)", type: "Contrôle vétérinaire", date: "2026-05-20", time: "11:30", notes: "Contrôle de routine", completed: false },
    { id: 6, animal: "Troupeau entier", type: "Vaccination rappel", date: "2026-05-26", time: "09:00", notes: "Vaccination rappel pour tout le troupeau", completed: false }
];

let nextId = 7;
let currentMonth = 4; // Mai = 4 (0-indexé)
let currentYear = 2026;
let currentView = 'month';
let toastTimeout = null;

// ================= TOAST =================
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

// ================= CALENDRIER =================
function renderCalendar() {
    const firstDay = new Date(currentYear, currentMonth, 1);
    const lastDay = new Date(currentYear, currentMonth + 1, 0);
    const daysInMonth = lastDay.getDate();
    const startDayOfWeek = firstDay.getDay(); // 0 = Dimanche
    
    // Ajuster pour commencer par Lundi
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
                // Jours du mois précédent
                const prevMonthDays = new Date(currentYear, currentMonth, 0).getDate();
                const prevDay = prevMonthDays - startOffset + col + 1;
                html += `<td class="other-month">${prevDay}</td>`;
            } else if (day <= daysInMonth) {
                const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const hasTask = tasks.some(t => t.date === dateStr && !t.completed);
                const isToday = dateStr === new Date().toISOString().split('T')[0];
                const classes = [];
                if (hasTask) classes.push('task-day');
                if (isToday) classes.push('today');
                html += `<td class="${classes.join(' ')}" onclick="selectDate('${dateStr}')">${day}</td>`;
                day++;
                started = true;
            } else {
                // Jours du mois suivant
                const nextDay = day - daysInMonth;
                html += `<td class="other-month">${nextDay}</td>`;
                day++;
            }
        }
        html += '</tr>';
        row++;
    }
    
    document.getElementById('calendarBody').innerHTML = html;
    renderUpcomingTasks();
    renderTodayTasks();
    updateStats();
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
    const date = new Date(dateStr);
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    showToast(`📅 ${date.toLocaleDateString('fr-FR', options)}`, 'info');
    // Scroll vers les tâches du jour
    document.querySelector('.today-card').scrollIntoView({ behavior: 'smooth' });
}

// ================= TÂCHES À VENIR =================
function renderUpcomingTasks() {
    const container = document.getElementById('upcomingTasks');
    const today = new Date().toISOString().split('T')[0];
    const upcoming = tasks
        .filter(t => t.date >= today && !t.completed)
        .sort((a, b) => a.date.localeCompare(b.date))
        .slice(0, 5);
    
    if (upcoming.length === 0) {
        container.innerHTML = `
            <li style="padding: 12px 14px; color: #6c757d; text-align: center;">
                <i class="fas fa-check-circle" style="color: #28a745;"></i> Aucune tâche à venir
            </li>
        `;
        return;
    }
    
    container.innerHTML = upcoming.map(t => {
        const date = new Date(t.date + 'T' + (t.time || '00:00'));
        const dateStr = date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' });
        const timeStr = t.time ? ` ${t.time}` : '';
        return `
            <li>
                <a href="#" class="upcoming-link" onclick="event.preventDefault(); selectTask(${t.id})">
                    <i class="far fa-calendar"></i>
                    <span>${dateStr}${timeStr} - ${t.type} (${t.animal})</span>
                </a>
            </li>
        `;
    }).join('');
}

// ================= TÂCHES DU JOUR =================
function renderTodayTasks() {
    const container = document.getElementById('todayTasks');
    const today = new Date().toISOString().split('T')[0];
    const todayTasks = tasks.filter(t => t.date === today);
    
    // Mettre à jour la date
    const dateObj = new Date(today);
    document.getElementById('todayDate').textContent = 
        dateObj.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' }).toUpperCase();
    
    if (todayTasks.length === 0) {
        container.innerHTML = `
            <div class="task-item" style="text-align: center; padding: 20px; color: #6c757d;">
                <i class="fas fa-calendar-check" style="font-size: 24px; display: block; margin-bottom: 8px;"></i>
                Aucune tâche prévue aujourd'hui
            </div>
        `;
        return;
    }
    
    container.innerHTML = todayTasks.map(t => `
        <div class="task-item ${t.completed ? 'completed' : ''}" data-id="${t.id}">
            <div class="task-time">
                ⏰ ${t.time || '00:00'} - ${t.type}
                ${t.completed ? '<span class="badge-completed">✓ Terminée</span>' : ''}
            </div>
            <div class="task-desc">${t.notes || t.animal}</div>
            <div class="task-actions">
                <button class="btn-success-task" onclick="toggleTask(${t.id})">
                    <i class="fas ${t.completed ? 'fa-undo' : 'fa-check-square'}"></i>
                    ${t.completed ? 'Rouvrir' : 'Marquer fait'}
                </button>
                <button class="btn-edit-task" onclick="openEditModal(${t.id})">
                    <i class="fas fa-pen"></i> Modifier
                </button>
                <button class="btn-delete-task" onclick="deleteTask(${t.id})">
                    <i class="fas fa-trash"></i> Supprimer
                </button>
            </div>
        </div>
    `).join('');
}

function selectTask(taskId) {
    const task = tasks.find(t => t.id === taskId);
    if (task) {
        const date = new Date(task.date + 'T' + (task.time || '00:00'));
        showToast(`📋 ${task.type} - ${task.animal} (${date.toLocaleDateString('fr-FR')})`, 'info');
    }
}

// ================= STATISTIQUES =================
function updateStats() {
    const total = tasks.length;
    const done = tasks.filter(t => t.completed).length;
    const pending = total - done;
    
    document.getElementById('totalTasks').textContent = total;
    document.getElementById('doneTasks').textContent = done;
    document.getElementById('pendingTasks').textContent = pending;
}

// ================= TOGGLE TÂCHE =================
function toggleTask(taskId) {
    const task = tasks.find(t => t.id === taskId);
    if (!task) return;
    
    task.completed = !task.completed;
    renderCalendar();
    renderTodayTasks();
    renderUpcomingTasks();
    updateStats();
    showToast(
        task.completed ? '✅ Tâche terminée !' : '🔄 Tâche réouverte',
        task.completed ? 'success' : 'info'
    );
}

// ================= SUPPRIMER TÂCHE =================
function deleteTask(taskId) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?')) return;
    
    const task = tasks.find(t => t.id === taskId);
    if (!task) return;
    
    tasks = tasks.filter(t => t.id !== taskId);
    renderCalendar();
    renderTodayTasks();
    renderUpcomingTasks();
    updateStats();
    showToast(`Tâche "${task.type}" supprimée`, 'danger');
}

// ================= MODALE AJOUTER =================
function openAddModal() {
    document.getElementById('addTaskForm').reset();
    document.getElementById('addDate').value = new Date().toISOString().split('T')[0];
    $('#addTaskModal').modal('show');
}

document.getElementById('addTaskForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const animal = document.getElementById('addAnimal').value;
    const type = document.getElementById('addType').value;
    const date = document.getElementById('addDate').value;
    const time = document.getElementById('addTime').value || '00:00';
    const notes = document.getElementById('addNotes').value.trim();
    
    if (!animal) {
        showToast('Veuillez sélectionner un animal', 'warning');
        return;
    }
    if (!type) {
        showToast('Veuillez sélectionner un type de tâche', 'warning');
        return;
    }
    if (!date) {
        showToast('Veuillez sélectionner une date', 'warning');
        return;
    }
    
    const animalName = document.querySelector(`#addAnimal option[value="${animal}"]`).text;
    
    const newTask = {
        id: nextId++,
        animal: animalName,
        type: type,
        date: date,
        time: time,
        notes: notes || `${type} - ${animalName}`,
        completed: false
    };
    
    tasks.push(newTask);
    renderCalendar();
    renderTodayTasks();
    renderUpcomingTasks();
    updateStats();
    $('#addTaskModal').modal('hide');
    showToast(`✅ Tâche "${type}" ajoutée avec succès !`, 'success');
});

// ================= MODALE MODIFIER =================
function openEditModal(taskId) {
    const task = tasks.find(t => t.id === taskId);
    if (!task) return;
    
    document.getElementById('editTaskId').value = taskId;
    document.getElementById('editAnimal').value = 
        ['1', '2', '3', '4'].find((_, i) => 
            ['Marguerite (n°123)', 'Bella (n°124)', 'Roussette (n°125)', 'Blanchette (n°126)'][i] === task.animal
        ) || '1';
    document.getElementById('editType').value = task.type;
    document.getElementById('editDate').value = task.date;
    document.getElementById('editTime').value = task.time;
    document.getElementById('editNotes').value = task.notes;
    
    $('#editTaskModal').modal('show');
}

document.getElementById('editTaskForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const taskId = parseInt(document.getElementById('editTaskId').value);
    const task = tasks.find(t => t.id === taskId);
    if (!task) return;
    
    const animal = document.getElementById('editAnimal').value;
    const type = document.getElementById('editType').value;
    const date = document.getElementById('editDate').value;
    const time = document.getElementById('editTime').value || '00:00';
    const notes = document.getElementById('editNotes').value.trim();
    
    const animalName = document.querySelector(`#editAnimal option[value="${animal}"]`).text;
    
    task.animal = animalName;
    task.type = type;
    task.date = date;
    task.time = time;
    task.notes = notes || `${type} - ${animalName}`;
    
    renderCalendar();
    renderTodayTasks();
    renderUpcomingTasks();
    updateStats();
    $('#editTaskModal').modal('hide');
    showToast(`✅ Tâche "${type}" modifiée avec succès !`, 'success');
});

// ================= INITIALISATION =================
document.addEventListener('DOMContentLoaded', function() {
    renderCalendar();
    renderTodayTasks();
    renderUpcomingTasks();
    updateStats();
    
    // Gérer la fermeture des modales avec la touche Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modals = document.querySelectorAll('.modal.show');
            modals.forEach(modal => {
                $(modal).modal('hide');
            });
        }
    });
});

// ================= STYLES ADDITIONNELS =================
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
    .custom-toast.info .toast-content { background: #17a2b8; color: white; }
    
    @media (max-width: 768px) {
        .custom-toast {
            left: 15px;
            right: 15px;
            bottom: 15px;
            transform: translateY(100px);
        }
        .custom-toast.show { transform: translateY(0); }
    }
    
    .task-stats {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        margin-top: 8px;
    }
    .stat-badge {
        font-size: 14px;
        padding: 4px 12px;
        border-radius: 20px;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
    }
    .stat-badge.total { background: #e9ecef; }
    .stat-badge.done { background: #d4edda; border-color: #28a745; }
    .stat-badge.pending { background: #fff3cd; border-color: #ffc107; }
    .stat-badge strong { font-weight: 700; }
    
    .calendar-table td.today {
        background: #198754;
        color: white !important;
        border-radius: 50%;
        font-weight: 700;
    }
    .calendar-table td.today::after {
        display: none;
    }
    .calendar-table td.today:hover {
        background: #146c43;
        color: white !important;
    }
    .calendar-table td.other-month {
        color: #adb5bd;
        cursor: default;
    }
    .calendar-table td.other-month:hover {
        background: transparent !important;
        box-shadow: none !important;
    }
    
    .task-item.completed {
        opacity: 0.7;
        background: #f8f9fa;
    }
    .task-item.completed .task-time {
        text-decoration: line-through;
        color: #6c757d;
    }
    
    .badge-completed {
        background: #28a745;
        color: white;
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        margin-left: 8px;
    }
    
    .btn-delete-task {
        flex: 1;
        border: 1px solid #f8d7da;
        background: #fff5f5;
        color: #dc3545;
        padding: 7px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-delete-task:hover {
        background: #f8d7da;
        border-color: #dc3545;
    }
    
    .task-actions {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
    }
    .task-actions button {
        flex: 1;
        min-width: 80px;
    }
    
    .modal-header .btn-close {
        background: none;
        border: none;
        font-size: 28px;
        cursor: pointer;
        color: #6c757d;
        transition: all 0.3s ease;
        padding: 0 8px;
    }
    .modal-header .btn-close:hover {
        color: #dc3545;
        transform: rotate(90deg);
    }
    
    @media (max-width: 768px) {
        .task-stats {
            gap: 8px;
        }
        .stat-badge {
            font-size: 12px;
            padding: 3px 10px;
        }
        .task-actions {
            flex-direction: column;
        }
        .task-actions button {
            width: 100%;
        }
        .page-header h1 {
            font-size: 18px;
        }
    }
    
    @media (max-width: 480px) {
        .stat-badge {
            font-size: 11px;
            padding: 2px 8px;
        }
        .task-stats {
            gap: 5px;
        }
    }
`;
document.head.appendChild(style);
</script>

@endsection
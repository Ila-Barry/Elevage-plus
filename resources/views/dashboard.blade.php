@extends('layouts.menu')

@section('title', 'Dashboard - Élevage+')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/eleveurCSS/dashboard.css') }}">
@endpush

@section('content')

<div class="dashboard-container">

    <!-- BANNIERE -->
    <div class="welcome-banner">
        <div class="banner-text-group">
            <h1>BONJOUR JEAN ! 👋</h1>
            <p>Voici ce qu'il se passe aujourd'hui</p>
        </div>
        <div class="banner-date">
            <i class="fas fa-calendar-alt"></i>
            <span id="currentDate"></span>
        </div>
    </div>

    <!-- KPI -->
    <div class="stats-row">

        <div class="stat-card green" onclick="navigateTo('/animaux')">
            <div class="number" id="statAnimals">45</div>
            <div class="label">Animaux</div>
            <a href="#" onclick="event.preventDefault(); navigateTo('/animaux')">voir détail →</a>
        </div>

        <div class="stat-card blue" onclick="navigateTo('/taches')">
            <div class="number" id="statTasks">12</div>
            <div class="label">Tâches ce mois</div>
            <a href="#" onclick="event.preventDefault(); navigateTo('/taches')">voir calendrier →</a>
        </div>

        <div class="stat-card yellow" onclick="navigateTo('/notification')">
            <div class="number" id="statAlerts">3</div>
            <div class="label">Alertes actives</div>
            <a href="#" onclick="event.preventDefault(); navigateTo('/notification')">voir alertes →</a>
        </div>

        <div class="stat-card red" onclick="navigateTo('/animaux')">
            <div class="number" id="statHealth">89%</div>
            <div class="label">Santé troupeau</div>
            <a href="#" onclick="event.preventDefault(); navigateTo('/animaux')">voir rapports →</a>
        </div>

    </div>

    <!-- GRID -->
    <div class="dashboard-grid">

        <!-- TACHES -->
        <div class="card-box">
            <div class="box-header between">
                <span>📋 TÂCHES DU JOUR</span>
                <span class="task-count" id="taskCount">3 tâches</span>
            </div>

            <div id="tasksContainer">
                <div class="task-row" data-task="1">
                    <span>✅ Vaccination - Troupeau bovin</span>
                    <span class="badge done">Terminée</span>
                </div>

                <div class="task-row" data-task="2">
                    <span>⏰ Pesée - Vache #123</span>
                    <span class="badge todo">À faire</span>
                </div>

                <div class="task-row" data-task="3">
                    <span>🧹 Nettoyage enclos</span>
                    <span class="badge todo">À faire</span>
                </div>

                <div class="task-row" data-task="4">
                    <span>💉 Vermifuge - Troupeau ovin</span>
                    <span class="badge todo">À faire</span>
                </div>
            </div>

            <div class="box-footer">
                <button class="btn-add-task" onclick="addTask()">
                    <i class="fas fa-plus"></i> Ajouter une tâche
                </button>
            </div>
        </div>

        <!-- GRAPH -->
        <div class="card-box">
            <div class="box-header between">
                <span>📈 ÉVOLUTION DU POIDS MOYEN</span>
                <select id="weightPeriod" onchange="updateWeightChart()">
                    <option value="6">6 derniers mois</option>
                    <option value="12">12 derniers mois</option>
                    <option value="24">24 derniers mois</option>
                </select>
            </div>
            <div class="chart-wrapper">
                <canvas id="weightChart"></canvas>
            </div>
        </div>

        <!-- ALERTES -->
        <div class="card-box">
            <div class="box-header between">
                <span>🔔 ALERTES ACTIVES</span>
                <span class="alert-count" id="alertCount">2 alertes</span>
            </div>

            <div id="alertsContainer">
                <div class="alert-card warning" onclick="markAlertRead(this)">
                    <div class="alert-header">
                        <h5>⚠️ Stock de médicaments faible</h5>
                        <button class="alert-close" onclick="event.stopPropagation(); dismissAlert(this)">×</button>
                    </div>
                    <p>Il reste très peu de doses en stock. Pensez à réapprovisionner.</p>
                    <span class="alert-time">Il y a 2 heures</span>
                </div>

                <div class="alert-card danger" onclick="markAlertRead(this)">
                    <div class="alert-header">
                        <h5>⚠️ Animal #127 : Perte de poids suspecte</h5>
                        <button class="alert-close" onclick="event.stopPropagation(); dismissAlert(this)">×</button>
                    </div>
                    <p>Baisse importante détectée cette semaine. Une consultation vétérinaire est recommandée.</p>
                    <span class="alert-time">Il y a 5 heures</span>
                </div>

                <div class="alert-card info" onclick="markAlertRead(this)">
                    <div class="alert-header">
                        <h5>ℹ️ Rappel : Vaccination annuelle</h5>
                        <button class="alert-close" onclick="event.stopPropagation(); dismissAlert(this)">×</button>
                    </div>
                    <p>La vaccination annuelle du troupeau est prévue dans 3 jours.</p>
                    <span class="alert-time">Il y a 1 jour</span>
                </div>
            </div>
        </div>

        <!-- DONUT -->
        <div class="card-box">
            <div class="box-header">
                <span>📊 Résumé par espèce</span>
            </div>

            <div class="species-wrapper">
                <canvas id="speciesChart"></canvas>
                <div class="species-legend" id="speciesLegend">
                    <div><span class="dot green"></span> Bovins <span class="species-count" id="bovinsCount">28</span> (62%)</div>
                    <div><span class="dot yellow"></span> Ovins <span class="species-count" id="ovinsCount">10</span> (22%)</div>
                    <div><span class="dot blue"></span> Caprins <span class="species-count" id="caprinsCount">5</span> (11%)</div>
                    <div><span class="dot red"></span> Volailles <span class="species-count" id="volaillesCount">2</span> (5%)</div>
                </div>
            </div>

            <h3 class="total">
                TOTAL = <span id="totalAnimals">45</span>
            </h3>

            <div class="box-footer">
                <button class="btn-add-species" onclick="showAddSpecies()">
                    <i class="fas fa-plus"></i> Ajouter une espèce
                </button>
            </div>
        </div>

        <!-- ACTIVITE -->
        <div class="card-box activity-box">
            <div class="box-header between">
                <span>🏆 ACTIVITÉ RÉCENTE</span>
                <span class="next-btn" onclick="nextActivity()">suivante ▶</span>
            </div>

            <div id="activitiesContainer">
                <div class="activity-item">
                    👍 Votre publication a reçu 12 likes
                    <small>Il y a 2 heures</small>
                </div>

                <div class="activity-item">
                    💬 Marie Diop a commenté votre article
                    <small>Il y a 5 heures</small>
                </div>

                <div class="activity-item">
                    🚜 Stock d'aliments mis à jour
                    <small>Il y a 1 jour</small>
                </div>

                <div class="activity-item">
                    📅 Rendez-vous vétérinaire programmé
                    <small>Il y a 2 jours</small>
                </div>
            </div>

            <a href="#" class="history-link" onclick="showHistory()">
                Voir tout l'historique →
            </a>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// ================= VARIABLES GLOBALES =================
let weightChart = null;
let speciesChart = null;
let currentActivityIndex = 0;
const activitiesPerPage = 3;
let toastTimeout = null;

// ================= DONNÉES =================
const speciesData = {
    bovins: 28,
    ovins: 10,
    caprins: 5,
    volailles: 2
};

const weightData = {
    '6': { labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'], data: [420, 435, 448, 462, 480, 495] },
    '12': { labels: ['Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc', 'Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'], data: [380, 395, 410, 420, 435, 448, 462, 475, 480, 485, 490, 495] },
    '24': { labels: ['2024', '2025', '2026'], data: [420, 450, 495] }
};

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

// ================= DATE =================
function updateDate() {
    const now = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('currentDate').textContent = now.toLocaleDateString('fr-FR', options);
}

// ================= NAVIGATION =================
function navigateTo(url) {
    window.location.href = url;
}

// ================= TÂCHES =================
function addTask() {
    const taskName = prompt('Entrez le nom de la nouvelle tâche :');
    if (taskName && taskName.trim()) {
        const container = document.getElementById('tasksContainer');
        const taskCount = container.children.length + 1;
        const newTask = document.createElement('div');
        newTask.className = 'task-row';
        newTask.dataset.task = taskCount;
        newTask.innerHTML = `
            <span>⏰ ${taskName.trim()}</span>
            <span class="badge todo" onclick="event.stopPropagation(); toggleTaskStatus(this)">À faire</span>
            <button class="task-delete" onclick="event.stopPropagation(); deleteTask(this)">×</button>
        `;
        container.appendChild(newTask);
        updateTaskCount();
        showToast('Tâche ajoutée avec succès !', 'success');
    }
}

function deleteTask(btn) {
    if (confirm('Supprimer cette tâche ?')) {
        btn.closest('.task-row').remove();
        updateTaskCount();
        showToast('Tâche supprimée', 'info');
    }
}

function toggleTaskStatus(badge) {
    const row = badge.closest('.task-row');
    if (badge.textContent.includes('À faire')) {
        badge.textContent = 'Terminée';
        badge.className = 'badge done';
        row.querySelector('span:first-child').textContent = '✅ ' + row.querySelector('span:first-child').textContent.substring(3);
        showToast('Tâche terminée ! 🎉', 'success');
    } else {
        badge.textContent = 'À faire';
        badge.className = 'badge todo';
        row.querySelector('span:first-child').textContent = '⏰ ' + row.querySelector('span:first-child').textContent.substring(3);
        showToast('Tâche réouverte', 'info');
    }
}

function updateTaskCount() {
    const count = document.querySelectorAll('.task-row').length;
    document.getElementById('taskCount').textContent = count + ' tâche' + (count > 1 ? 's' : '');
}

// ================= ALERTES =================
function dismissAlert(btn) {
    const alert = btn.closest('.alert-card');
    if (confirm('Supprimer cette alerte ?')) {
        alert.style.maxHeight = '0';
        alert.style.overflow = 'hidden';
        alert.style.padding = '0';
        alert.style.margin = '0';
        setTimeout(() => {
            alert.remove();
            updateAlertCount();
            showToast('Alerte supprimée', 'info');
        }, 300);
    }
}

function markAlertRead(alert) {
    if (!alert.classList.contains('read')) {
        alert.classList.add('read');
        alert.style.opacity = '0.7';
        showToast('Alerte marquée comme lue', 'info');
    }
}

function updateAlertCount() {
    const count = document.querySelectorAll('.alert-card').length;
    document.getElementById('alertCount').textContent = count + ' alerte' + (count > 1 ? 's' : '');
}

// ================= GRAPHIQUE POIDS =================
function initWeightChart(period = '6') {
    const ctx = document.getElementById('weightChart').getContext('2d');
    const data = weightData[period] || weightData['6'];
    
    if (weightChart) {
        weightChart.destroy();
    }
    
    weightChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Poids moyen (kg)',
                data: data.data,
                borderColor: '#22c55e',
                backgroundColor: 'rgba(34,197,94,0.15)',
                borderWidth: 3,
                pointRadius: 5,
                pointHoverRadius: 7,
                tension: 0.4,
                fill: true
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
                    min: Math.min(...data.data) - 20,
                    max: Math.max(...data.data) + 20,
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

function updateWeightChart() {
    const period = document.getElementById('weightPeriod').value;
    initWeightChart(period);
    showToast('Graphique mis à jour', 'info');
}

// ================= GRAPHIQUE ESPÈCES =================
function initSpeciesChart() {
    const ctx = document.getElementById('speciesChart').getContext('2d');
    const data = Object.values(speciesData);
    const labels = Object.keys(speciesData);
    const colors = ['#22c55e', '#f59e0b', '#2563eb', '#ef4444'];
    
    if (speciesChart) {
        speciesChart.destroy();
    }
    
    speciesChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors,
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '65%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = Object.values(speciesData).reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}

function updateSpeciesLegend() {
    const total = Object.values(speciesData).reduce((a, b) => a + b, 0);
    document.getElementById('totalAnimals').textContent = total;
    
    for (const [key, value] of Object.entries(speciesData)) {
        const countElement = document.getElementById(key + 'Count');
        if (countElement) {
            const percentage = ((value / total) * 100).toFixed(0);
            countElement.textContent = value;
            countElement.parentElement.textContent = 
                countElement.parentElement.textContent.replace(/\([^)]*\)/, '(' + percentage + '%)');
        }
    }
}

function showAddSpecies() {
    const name = prompt('Entrez le nom de la nouvelle espèce :');
    if (name && name.trim()) {
        const count = parseInt(prompt('Nombre d\'animaux :')) || 0;
        if (count > 0) {
            const key = name.trim().toLowerCase();
            speciesData[key] = count;
            updateSpeciesLegend();
            initSpeciesChart();
            showToast('Espèce ajoutée avec succès !', 'success');
        }
    }
}

// ================= ACTIVITÉS =================
function nextActivity() {
    const activities = document.querySelectorAll('.activity-item');
    const total = activities.length;
    const maxIndex = Math.max(0, total - activitiesPerPage);
    
    currentActivityIndex += activitiesPerPage;
    if (currentActivityIndex >= total) {
        currentActivityIndex = 0;
    }
    
    activities.forEach((item, index) => {
        if (index >= currentActivityIndex && index < currentActivityIndex + activitiesPerPage) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

function showHistory() {
    showToast('📜 Affichage de tout l\'historique', 'info');
}

// ================= INITIALISATION =================
document.addEventListener('DOMContentLoaded', function() {
    updateDate();
    initWeightChart();
    initSpeciesChart();
    updateSpeciesLegend();
    updateTaskCount();
    updateAlertCount();
    
    // Initialiser l'affichage des activités
    nextActivity();
    
    // Animation des KPI
    document.querySelectorAll('.stat-card .number').forEach(el => {
        const target = parseInt(el.textContent) || 0;
        if (target > 0) {
            animateNumber(el, target);
        }
    });
});

// ================= ANIMATION DES NOMBRES =================
function animateNumber(element, target) {
    let current = 0;
    const increment = Math.ceil(target / 30);
    const interval = setInterval(() => {
        current += increment;
        if (current >= target) {
            current = target;
            clearInterval(interval);
        }
        element.textContent = current;
    }, 50);
}

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
    
    .banner-date {
        margin-left: auto;
        color: #555;
        font-size: 0.95rem;
        background: rgba(255,255,255,0.7);
        padding: 6px 14px;
        border-radius: 20px;
    }
    .banner-date i {
        margin-right: 8px;
        color: #198754;
    }
    
    .task-count, .alert-count {
        font-weight: 400;
        font-size: 0.8rem;
        color: #6c757d;
        background: #e9ecef;
        padding: 2px 10px;
        border-radius: 12px;
    }
    
    .badge {
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .badge.done {
        background: #d4edda;
        color: #198754;
    }
    .badge.todo {
        background: #fff3cd;
        color: #856404;
    }
    .badge:hover {
        transform: scale(1.05);
    }
    
    .task-delete {
        background: none;
        border: none;
        color: #dc3545;
        cursor: pointer;
        font-size: 16px;
        padding: 0 8px;
        opacity: 0.5;
        transition: all 0.2s;
    }
    .task-delete:hover {
        opacity: 1;
        transform: scale(1.2);
    }
    
    .alert-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    .alert-close {
        background: none;
        border: none;
        color: #6c757d;
        font-size: 18px;
        cursor: pointer;
        padding: 0 4px;
        transition: all 0.2s;
    }
    .alert-close:hover {
        color: #dc3545;
        transform: scale(1.2);
    }
    
    .alert-time {
        font-size: 0.7rem;
        color: #6c757d;
        display: block;
        margin-top: 6px;
    }
    
    .alert-card.read {
        opacity: 0.6;
    }
    .alert-card.read h5 {
        text-decoration: line-through;
    }
    
    .alert-card {
        transition: all 0.3s ease;
        overflow: hidden;
        max-height: 200px;
    }
    .alert-card.info {
        background: #e8f4fd;
    }
    
    .box-footer {
        padding: 10px 12px;
        border-top: 1px solid #eee;
        display: flex;
        gap: 10px;
    }
    
    .btn-add-task, .btn-add-species {
        background: #198754;
        color: white;
        border: none;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .btn-add-task:hover, .btn-add-species:hover {
        background: #146c43;
        transform: translateY(-2px);
    }
    
    .species-count {
        font-weight: 700;
    }
    
    .next-btn {
        cursor: pointer;
        color: #198754;
        font-size: 0.8rem;
        transition: all 0.2s;
    }
    .next-btn:hover {
        color: #146c43;
        transform: translateX(4px);
    }
    
    .chart-wrapper {
        padding: 10px;
        height: 250px;
    }
    
    .activity-item {
        transition: all 0.3s ease;
    }
    
    @media (max-width: 768px) {
        .welcome-banner {
            flex-direction: column;
            align-items: flex-start;
            height: auto;
            padding: 20px;
            gap: 10px;
        }
        .banner-date {
            margin-left: 0;
            width: 100%;
            text-align: center;
        }
        .chart-wrapper {
            height: 200px;
        }
        .box-footer {
            flex-direction: column;
        }
        .btn-add-task, .btn-add-species {
            width: 100%;
            justify-content: center;
        }
    }
`;
document.head.appendChild(style);
</script>

@endsection
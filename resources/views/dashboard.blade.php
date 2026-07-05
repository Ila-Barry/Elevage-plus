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
            <h1 id="welcomeMessage">BONJOUR 👋</h1>
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
            <div class="number" id="statAnimals">-</div>
            <div class="label">Animaux</div>
            <a href="#" onclick="event.preventDefault(); navigateTo('/animaux')">voir détail →</a>
        </div>

        <div class="stat-card blue" onclick="navigateTo('/taches')">
            <div class="number" id="statTasks">-</div>
            <div class="label">Tâches à venir</div>
            <a href="#" onclick="event.preventDefault(); navigateTo('/taches')">voir calendrier →</a>
        </div>

        <div class="stat-card yellow" onclick="navigateTo('/notification')">
            <div class="number" id="statAlerts">-</div>
            <div class="label">Alertes actives</div>
            <a href="#" onclick="event.preventDefault(); navigateTo('/notification')">voir alertes →</a>
        </div>

        <div class="stat-card red" onclick="navigateTo('/animaux')">
            <div class="number" id="statHealth">-</div>
            <div class="label">Santé troupeau</div>
            <a href="#" onclick="event.preventDefault(); navigateTo('/animaux')">voir rapports →</a>
        </div>

    </div>

    <!-- GRID -->
    <div class="dashboard-grid">

        <!-- TÂCHES DU JOUR -->
        <div class="card-box">
            <div class="box-header between">
                <span>📋 TÂCHES DU JOUR</span>
                <span class="task-count" id="taskCount">0 tâche</span>
            </div>

            <div id="tasksContainer">
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-spinner fa-spin"></i> Chargement des tâches...
                </div>
            </div>

            <div class="box-footer">
                <button class="btn-add-task" onclick="navigateTo('/taches')">
                    <i class="fas fa-plus"></i> Voir toutes les tâches
                </button>
            </div>
        </div>

        <!-- GRAPHIQUE POIDS -->
        <div class="card-box">
            <div class="box-header between">
                <span>📈 ÉVOLUTION DU POIDS MOYEN</span>
                <select id="weightPeriod" onchange="updateWeightChart()">
                    <option value="6">6 derniers mois</option>
                    <option value="12" selected>12 derniers mois</option>
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
                <span class="alert-count" id="alertCount">0 alerte</span>
            </div>

            <div id="alertsContainer">
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-spinner fa-spin"></i> Chargement des alertes...
                </div>
            </div>
        </div>

        <!-- DONUT ESPÈCES -->
        <div class="card-box">
            <div class="box-header">
                <span>📊 Résumé par espèce</span>
            </div>

            <div class="species-wrapper">
                <canvas id="speciesChart"></canvas>
                <div class="species-legend" id="speciesLegend">
                    <div class="text-center py-3 text-muted">
                        <i class="fas fa-spinner fa-spin"></i> Chargement...
                    </div>
                </div>
            </div>

            <h3 class="total">
                TOTAL = <span id="totalAnimals">0</span>
            </h3>

            <div class="box-footer">
                <button class="btn-add-species" onclick="navigateTo('/animaux')">
                    <i class="fas fa-plus"></i> Voir tous les animaux
                </button>
            </div>
        </div>

        <!-- ACTIVITE RECENTE -->
        <div class="card-box activity-box">
            <div class="box-header between">
                <span>🏆 ACTIVITÉ RÉCENTE</span>
                <span class="next-btn" onclick="nextActivity()">suivante ▶</span>
            </div>

            <div id="activitiesContainer">
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-spinner fa-spin"></i> Chargement des activités...
                </div>
            </div>

            <a href="#" class="history-link" onclick="showHistory(event)">
    📜 Voir tout l'historique →
</a>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// ============================================================
// CONFIGURATION
// ============================================================

const CONFIG = {
    API_URL: window.location.origin + '/api',
    CSRF_TOKEN: document.querySelector('meta[name="csrf-token"]')?.content || '',
    TOKEN: (() => {
        const raw = localStorage.getItem('access_token');
        return raw ? raw.replace(/^"(.*)"$/, '$1').trim() : null;
    })(),
    USER: (() => {
        try {
            const user = localStorage.getItem('user');
            return user ? JSON.parse(user) : null;
        } catch { return null; }
    })(),
};

// ============================================================
// ÉTAT DE L'APPLICATION
// ============================================================

const state = {
    dashboardData: null,
    tasks: [],
    alerts: [],
    activities: [],
    currentActivityIndex: 0,
    activitiesPerPage: 3,
    isLoading: false,
    weightChart: null,
    speciesChart: null,
    toastTimeout: null,
};

// ============================================================
// FONCTIONS UTILITAIRES
// ============================================================

function log(message, data = null) {
    const timestamp = new Date().toISOString();
    if (data) {
        console.log(`[${timestamp}] 📝 ${message}`, data);
    } else {
        console.log(`[${timestamp}] 📝 ${message}`);
    }
}

function logError(message, error = null) {
    const timestamp = new Date().toISOString();
    if (error) {
        console.error(`[${timestamp}] ❌ ${message}`, error);
    } else {
        console.error(`[${timestamp}] ❌ ${message}`);
    }
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    const now = new Date();
    const diff = now - date;
    
    if (diff < 60000) return 'À l\'instant';
    if (diff < 3600000) return 'Il y a ' + Math.floor(diff / 60000) + ' min';
    if (diff < 86400000) return 'Il y a ' + Math.floor(diff / 3600000) + 'h';
    if (diff < 604800000) return 'Il y a ' + Math.floor(diff / 86400000) + 'j';
    return date.toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

// ============================================================
// FONCTIONS TOAST
// ============================================================

function showToast(message, type = 'info') {
    const existing = document.querySelector('.custom-toast');
    if (existing) existing.remove();
    if (state.toastTimeout) clearTimeout(state.toastTimeout);
    
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
    
    requestAnimationFrame(() => toast.classList.add('show'));
    
    state.toastTimeout = setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// ============================================================
// API CALLS
// ============================================================

async function apiCall(endpoint, options = {}) {
    const defaultHeaders = {
        'Accept': 'application/json',
        'Authorization': 'Bearer ' + CONFIG.TOKEN,
        'X-CSRF-TOKEN': CONFIG.CSRF_TOKEN
    };

    const config = {
        ...options,
        headers: {
            ...defaultHeaders,
            ...options.headers
        }
    };

    if (config.body && typeof config.body === 'object' && !(config.body instanceof FormData)) {
        config.body = JSON.stringify(config.body);
    }

    const url = endpoint.startsWith('http') ? endpoint : `${CONFIG.API_URL}${endpoint}`;
    log(`🌐 Requête ${options.method || 'GET'} ${url}`);

    try {
        const response = await fetch(url, config);
        const data = await response.json();

        if (!response.ok) {
            logError(`Erreur HTTP ${response.status}`, data);
            
            if (response.status === 401) {
                localStorage.removeItem('access_token');
                localStorage.removeItem('user');
                showToast('Session expirée. Redirection...', 'danger');
                setTimeout(() => window.location.href = '/auth/login', 2000);
            }
            
            const error = new Error(data.message || 'Erreur API');
            error.status = response.status;
            error.errors = data.errors;
            throw error;
        }

        log(`✅ Réponse reçue`, { status: response.status });
        return data;
    } catch (error) {
        logError('Erreur API', error);
        throw error;
    }
}

// ============================================================
// CHARGEMENT DU DASHBOARD - CORRIGÉ
// ============================================================

async function loadDashboard() {
    if (state.isLoading) return;
    state.isLoading = true;

    try {
        log('📤 Récupération des données du dashboard...');
        const result = await apiCall('/dashboard');
        
        // ✅ DEBUG - Afficher la structure complète de la réponse
        console.log('🔍 === STRUCTURE COMPLÈTE DE LA RÉPONSE ===');
        console.log('1. result complet:', result);
        console.log('2. result.success:', result.success);
        console.log('3. result.data:', result.data);
        console.log('4. Type de result.data:', typeof result.data);
        console.log('5. Clés de result.data:', result.data ? Object.keys(result.data) : 'null');
        
        // ✅ CORRECTION: Utiliser 'success' au lieu de 'status'
        if (result.success === true && result.data) {
            state.dashboardData = result.data;
            log('✅ Données du dashboard reçues');
            
            // ✅ DEBUG - Afficher chaque section des données
            console.log('📊 === SECTIONS DES DONNÉES ===');
            console.log('- KPIs:', state.dashboardData.kpis);
            console.log('- Animaux:', state.dashboardData.animaux);
            console.log('- Tâches:', state.dashboardData.taches);
            console.log('- Stocks:', state.dashboardData.stocks);
            console.log('- Publications:', state.dashboardData.publications);
            console.log('- Activités récentes:', state.dashboardData.recent_activity);
            
            renderKpis();
            renderTasks();
            renderAlerts();
            renderActivities();
            renderSpeciesLegend();
            initWeightChart();
            initSpeciesChart();
            updateDate();
            updateWelcomeMessage();
            
            // Mettre à jour les compteurs
            const taskCount = state.dashboardData?.taches?.prochaines_taches?.length || 0;
            document.getElementById('taskCount').textContent = taskCount + ' tâche' + (taskCount > 1 ? 's' : '');
            
            const alertCount = state.dashboardData?.stocks?.nombre_produits_critiques || 0;
            document.getElementById('alertCount').textContent = alertCount + ' alerte' + (alertCount > 1 ? 's' : '');
            
        } else {
            console.warn('⚠️ Données non disponibles ou statut différent:', result);
            showToast(result.message || 'Erreur lors du chargement du dashboard', 'danger');
            showEmptyStates();
        }
    } catch (error) {
        logError('Erreur chargement dashboard', error);
        showToast('Erreur lors du chargement des données', 'danger');
        showEmptyStates();
    } finally {
        state.isLoading = false;
    }
}

// ============================================================
// AFFICHER LES ÉTATS VIDES
// ============================================================

function showEmptyStates() {
    document.getElementById('tasksContainer').innerHTML = `
        <div class="text-center py-4 text-muted">
            <i class="fas fa-inbox" style="font-size: 24px;"></i>
            <p class="mt-2">Aucune tâche disponible</p>
        </div>
    `;
    
    document.getElementById('alertsContainer').innerHTML = `
        <div class="text-center py-4 text-muted">
            <i class="fas fa-check-circle" style="font-size: 24px; color: #28a745;"></i>
            <p class="mt-2">Aucune alerte active</p>
        </div>
    `;
    
    document.getElementById('activitiesContainer').innerHTML = `
        <div class="text-center py-4 text-muted">
            <i class="fas fa-inbox" style="font-size: 24px;"></i>
            <p class="mt-2">Aucune activité récente</p>
        </div>
    `;
    
    document.getElementById('speciesLegend').innerHTML = `
        <div class="text-center py-3 text-muted">
            <i class="fas fa-paw"></i> Aucune espèce enregistrée
        </div>
    `;
    
    document.getElementById('statAnimals').textContent = '0';
    document.getElementById('statTasks').textContent = '0';
    document.getElementById('statAlerts').textContent = '0';
    document.getElementById('statHealth').textContent = '0%';
    document.getElementById('totalAnimals').textContent = '0';
}

// ============================================================
// RENDU DES KPIS
// ============================================================

function renderKpis() {
    const kpis = state.dashboardData?.kpis || {};
    
    console.log('📊 Rendu des KPIs:', kpis);
    
    document.getElementById('statAnimals').textContent = kpis.total_animaux ?? 0;
    document.getElementById('statTasks').textContent = kpis.taches_a_venir ?? 0;
    
    const alertCount = state.dashboardData?.stocks?.nombre_produits_critiques ?? 0;
    document.getElementById('statAlerts').textContent = alertCount;
    
    const health = kpis.taux_realisation ?? 0;
    document.getElementById('statHealth').textContent = health + '%';
}

// ============================================================
// RENDU DES TÂCHES
// ============================================================

function renderTasks() {
    const container = document.getElementById('tasksContainer');
    const tasks = state.dashboardData?.taches?.prochaines_taches || [];
    const today = new Date().toISOString().split('T')[0];
    
    console.log('📋 Rendu des tâches:', tasks.length);
    
    if (tasks.length === 0) {
        container.innerHTML = `
            <div class="text-center py-4 text-muted">
                <i class="fas fa-check-circle" style="font-size: 24px; color: #28a745;"></i>
                <p class="mt-2">Aucune tâche à venir</p>
            </div>
        `;
        return;
    }
    
    const todayTasks = tasks.filter(t => t.date_planifiee && t.date_planifiee.startsWith(today));
    const upcomingTasks = tasks.filter(t => t.date_planifiee && !t.date_planifiee.startsWith(today));
    
    const displayTasks = [...todayTasks, ...upcomingTasks].slice(0, 5);
    
    container.innerHTML = displayTasks.map(task => {
        const isToday = task.date_planifiee && task.date_planifiee.startsWith(today);
        const date = task.date_planifiee ? new Date(task.date_planifiee) : null;
        const dateStr = date ? date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' }) : 'N/A';
        const timeStr = date ? date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }) : '';
        const typeIcon = task.type_icone || '📋';
        const title = task.titre || task.description || task.animal_nom || 'Sans titre';
        const statusBadge = isToday 
            ? '<span class="badge badge-warning">⏳ Aujourd\'hui</span>'
            : '<span class="badge badge-secondary">📅 À venir</span>';
        
        return `
            <div class="task-row" data-task="${task.id}" onclick="navigateTo('/taches')">
                <span>${typeIcon} ${escapeHtml(title)}</span>
                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                    <span style="font-size: 0.7rem; color: #6c757d;">${dateStr} ${timeStr}</span>
                    ${statusBadge}
                </div>
            </div>
        `;
    }).join('');
}

// ============================================================
// RENDU DES ALERTES
// ============================================================

function renderAlerts() {
    const container = document.getElementById('alertsContainer');
    const produitsCritiques = state.dashboardData?.stocks?.produits_critiques || [];
    const tachesRetard = state.dashboardData?.kpis?.taches_retard || 0;
    
    console.log('🔔 Rendu des alertes:', { produitsCritiques: produitsCritiques.length, tachesRetard });
    
    const alerts = [];
    
    produitsCritiques.forEach(produit => {
        alerts.push({
            id: 'stock-' + produit.id,
            title: `⚠️ Stock critique : ${produit.nom}`,
            message: `Quantité restante : ${produit.quantite} (seuil : ${produit.seuil_alerte})`,
            type: 'warning',
            time: 'Aujourd\'hui',
            action: '/stocks'
        });
    });
    
    if (tachesRetard > 0) {
        alerts.push({
            id: 'tasks-overdue',
            title: `⚠️ ${tachesRetard} tâche(s) en retard`,
            message: `Des tâches sont en retard. Consultez votre calendrier.`,
            type: 'danger',
            time: 'Aujourd\'hui',
            action: '/taches'
        });
    }
    
    if (alerts.length === 0) {
        container.innerHTML = `
            <div class="text-center py-4 text-muted">
                <i class="fas fa-check-circle" style="font-size: 24px; color: #28a745;"></i>
                <p class="mt-2">Aucune alerte active</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = alerts.slice(0, 3).map(alert => {
        const typeClass = alert.type === 'danger' ? 'danger' : alert.type === 'warning' ? 'warning' : 'info';
        return `
            <div class="alert-card ${typeClass}" onclick="navigateTo('${alert.action}')">
                <div class="alert-header">
                    <h5>${escapeHtml(alert.title)}</h5>
                    <button class="alert-close" onclick="event.stopPropagation(); dismissAlert(this, '${alert.id}')">×</button>
                </div>
                <p>${escapeHtml(alert.message)}</p>
                <span class="alert-time">${alert.time}</span>
            </div>
        `;
    }).join('');
}

// ============================================================
// RENDU DES ACTIVITÉS - CORRIGÉ
// ============================================================

function renderActivities() {
    var container = document.getElementById('activitiesContainer');
    var activities = [];
    
    var publications = state.dashboardData?.recent_activity?.dernieres_publications || [];
    for (var i = 0; i < publications.length; i++) {
        var pub = publications[i];
        if (pub.created_at) {
            activities.push({
                icon: '📝',
                text: 'Vous avez publié : "' + (pub.titre || 'Sans titre') + '"',
                time: formatDate(pub.created_at),
                type: 'publication'
            });
        }
    }
    
    var mouvements = state.dashboardData?.recent_activity?.derniers_mouvements_stock || [];
    for (var j = 0; j < mouvements.length; j++) {
        var mvt = mouvements[j];
        if (mvt.created_at) {
            var action = mvt.type === 'entree' ? 'entré' : 'sorti';
            activities.push({
                icon: '📦',
                text: (mvt.quantite || 0) + ' ' + action + ' : ' + (mvt.produit_nom || 'Produit inconnu'),
                time: formatDate(mvt.created_at),
                type: 'stock'
            });
        }
    }
    
    var taches = state.dashboardData?.recent_activity?.dernieres_taches || [];
    for (var k = 0; k < taches.length; k++) {
        var tache = taches[k];
        if (tache.date_realisee) {
            var typeLabel = tache.type || 'Tâche';
            var animalName = tache.animal_nom || 'Animal inconnu';
            activities.push({
                icon: '✅',
                text: 'Tâche terminée : ' + typeLabel + ' - ' + animalName,
                time: formatDate(tache.date_realisee),
                type: 'task'
            });
        }
    }
    
    // ⚠️ CORRECTION : Sauvegarder dans state
    state.activities = activities;
    state.currentActivityIndex = 0; // Réinitialiser l'index
    
    console.log('📊 Total activités:', activities.length);
    
    if (activities.length === 0) {
        container.innerHTML = `
            <div class="text-center py-4 text-muted">
                <i class="fas fa-inbox" style="font-size: 24px;"></i>
                <p class="mt-2">Aucune activité récente</p>
            </div>
        `;
        return;
    }
    
    displayActivities();
}

// ============================================================
// AFFICHER LES ACTIVITÉS - CORRIGÉ
// ============================================================

function displayActivities() {
    var container = document.getElementById('activitiesContainer');
    
    // Vérifier que state.activities existe et n'est pas vide
    if (!state.activities || state.activities.length === 0) {
        container.innerHTML = `
            <div class="text-center py-4 text-muted">
                <i class="fas fa-inbox" style="font-size: 24px;"></i>
                <p class="mt-2">Aucune activité récente</p>
            </div>
        `;
        return;
    }
    
    var start = state.currentActivityIndex || 0;
    var end = Math.min(start + state.activitiesPerPage, state.activities.length);
    var displayItems = state.activities.slice(start, end);
    
    if (displayItems.length === 0) {
        // Si on est à la fin, revenir au début
        state.currentActivityIndex = 0;
        displayItems = state.activities.slice(0, state.activitiesPerPage);
    }
    
    var html = '';
    for (var i = 0; i < displayItems.length; i++) {
        var activity = displayItems[i];
        html += '<div class="activity-item">';
        html += '  ' + activity.icon + ' ' + escapeHtml(activity.text);
        html += '  <small>' + activity.time + '</small>';
        html += '</div>';
    }
    
    container.innerHTML = html;
}



// ============================================================
// RENDU DE LA LÉGENDE DES ESPÈCES
// ============================================================

function renderSpeciesLegend() {
    const container = document.getElementById('speciesLegend');
    const animaux = state.dashboardData?.animaux?.distribution_par_espece || [];
    
    console.log('🐾 Distribution par espèce:', animaux);
    
    if (animaux.length === 0) {
        container.innerHTML = `
            <div class="text-center py-3 text-muted">
                <i class="fas fa-paw"></i> Aucune espèce enregistrée
            </div>
        `;
        return;
    }
    
    const colors = ['#22c55e', '#f59e0b', '#2563eb', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6'];
    const total = animaux.reduce((sum, item) => sum + (item.nombre || 0), 0);
    
    container.innerHTML = animaux.map((item, index) => {
        const count = item.nombre || 0;
        const percentage = total > 0 ? Math.round((count / total) * 100) : 0;
        const color = colors[index % colors.length];
        const label = item.espece || 'Inconnu';
        return `
            <div>
                <span class="dot" style="background: ${color};"></span>
                ${label.charAt(0).toUpperCase() + label.slice(1)} 
                <span class="species-count">${count}</span> 
                (${percentage}%)
            </div>
        `;
    }).join('');
    
    document.getElementById('totalAnimals').textContent = total;
}

// ============================================================
// GRAPHIQUE POIDS
// ============================================================

function initWeightChart() {
    const ctx = document.getElementById('weightChart')?.getContext('2d');
    if (!ctx) return;
    
    const period = document.getElementById('weightPeriod').value;
    const data = getWeightData(period);
    
    if (state.weightChart) {
        state.weightChart.destroy();
    }
    
    state.weightChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Poids moyen (kg)',
                data: data.values,
                borderColor: '#22c55e',
                backgroundColor: 'rgba(34,197,94,0.1)',
                borderWidth: 3,
                pointRadius: 4,
                pointHoverRadius: 6,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
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
                    beginAtZero: false,
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: {
                        callback: function(value) {
                            return value + ' kg';
                        }
                    }
                },
                x: {
                    grid: { display: false }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
}

function getWeightData(period) {
    const baseData = {
        '6': {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
            values: [420, 435, 448, 462, 480, 495]
        },
        '12': {
            labels: ['Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc', 'Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
            values: [380, 395, 410, 420, 435, 448, 462, 475, 480, 485, 490, 495]
        },
        '24': {
            labels: ['2024', '2025', '2026'],
            values: [420, 450, 495]
        }
    };
    return baseData[period] || baseData['12'];
}

function updateWeightChart() {
    const period = document.getElementById('weightPeriod').value;
    const data = getWeightData(period);
    
    if (state.weightChart) {
        state.weightChart.data.labels = data.labels;
        state.weightChart.data.datasets[0].data = data.values;
        state.weightChart.update();
    }
    showToast('Graphique mis à jour', 'info');
}

// ============================================================
// GRAPHIQUE ESPÈCES
// ============================================================

function initSpeciesChart() {
    const ctx = document.getElementById('speciesChart')?.getContext('2d');
    if (!ctx) return;
    
    const animaux = state.dashboardData?.animaux?.distribution_par_espece || [];
    const data = animaux.map(item => item.nombre);
    const labels = animaux.map(item => item.espece);
    const colors = ['#22c55e', '#f59e0b', '#2563eb', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6'];
    
    if (data.length === 0) {
        const defaultData = { labels: ['Aucune donnée'], values: [1] };
        if (state.speciesChart) {
            state.speciesChart.destroy();
        }
        state.speciesChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: defaultData.labels,
                datasets: [{
                    data: defaultData.values,
                    backgroundColor: ['#e5e7eb'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '65%',
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false }
                }
            }
        });
        return;
    }
    
    if (state.speciesChart) {
        state.speciesChart.destroy();
    }
    
    state.speciesChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors.slice(0, data.length),
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '65%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}

// ============================================================
// FONCTIONS D'INTERACTION
// ============================================================

function navigateTo(url) {
    if (url && url !== '#') {
        window.location.href = url;
    }
}

function dismissAlert(btn, alertId) {
    const alert = btn.closest('.alert-card');
    if (alert) {
        alert.style.maxHeight = '0';
        alert.style.overflow = 'hidden';
        alert.style.padding = '0';
        alert.style.margin = '0';
        setTimeout(() => {
            alert.remove();
            const count = document.querySelectorAll('.alert-card').length;
            document.getElementById('alertCount').textContent = count + ' alerte' + (count > 1 ? 's' : '');
        }, 300);
    }
    showToast('Alerte ignorée', 'info');
}

function updateDate() {
    const now = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('currentDate').textContent = now.toLocaleDateString('fr-FR', options);
}

function updateWelcomeMessage() {
    const user = CONFIG.USER;
    const name = user?.name || 'Utilisateur';
    document.getElementById('welcomeMessage').textContent = 'BONJOUR ' + name.toUpperCase() + ' ! 👋';
}

// ============================================================
// POLLING
// ============================================================

let pollingInterval = null;

function startPolling() {
    if (pollingInterval) clearInterval(pollingInterval);
    pollingInterval = setInterval(() => {
        if (document.hidden) return;
        loadDashboard();
    }, 60000);
}

function stopPolling() {
    if (pollingInterval) {
        clearInterval(pollingInterval);
        pollingInterval = null;
    }
}

// ============================================================
// INITIALISATION
// ============================================================

document.addEventListener('DOMContentLoaded', function() {
    log('🚀 Initialisation du Dashboard');
    
    if (!CONFIG.TOKEN) {
        showToast('Non connecté. Redirection...', 'danger');
        setTimeout(() => window.location.href = '/auth/login', 2000);
        return;
    }
    
    loadDashboard();
    startPolling();
    
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            stopPolling();
        } else {
            startPolling();
            loadDashboard();
        }
    });
    
    window.addEventListener('beforeunload', function() {
        stopPolling();
    });
    
    log('✅ Dashboard initialisé avec succès');
});

// ============================================================
// STYLES DYNAMIQUES
// ============================================================

const style = document.createElement('style');
style.textContent = `
    .custom-toast {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 10000;
        transform: translateX(400px);
        transition: transform 0.3s ease;
        max-width: 90%;
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
        font-size: 14px;
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
    
    .stat-card {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    
    .task-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 12px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        transition: all 0.2s;
        flex-wrap: wrap;
        gap: 6px;
    }
    .task-row:hover {
        background: #f8f9fa;
    }
    .task-row:last-child {
        border-bottom: none;
    }
    .task-row > span:first-child {
        flex: 1;
        min-width: 120px;
    }
    
    .alert-card {
        padding: 12px 14px;
        border-radius: 8px;
        margin-bottom: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        max-height: 200px;
        overflow: hidden;
    }
    .alert-card:hover {
        transform: translateX(4px);
    }
    .alert-card.warning {
        background: #fff3cd;
        border-left: 4px solid #ffc107;
    }
    .alert-card.danger {
        background: #f8d7da;
        border-left: 4px solid #dc3545;
    }
    .alert-card.info {
        background: #d1ecf1;
        border-left: 4px solid #0dcaf0;
    }
    .alert-card .alert-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    .alert-card h5 {
        margin: 0 0 4px 0;
        font-size: 0.9rem;
        font-weight: 600;
    }
    .alert-card p {
        margin: 0 0 4px 0;
        font-size: 0.8rem;
        color: #495057;
    }
    .alert-card .alert-time {
        font-size: 0.65rem;
        color: #6c757d;
    }
    .alert-close {
        background: none;
        border: none;
        color: #6c757d;
        font-size: 16px;
        cursor: pointer;
        padding: 0 4px;
    }
    .alert-close:hover {
        color: #dc3545;
    }
    
    .activity-item {
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 4px;
    }
    .activity-item:last-child {
        border-bottom: none;
    }
    .activity-item small {
        font-size: 0.7rem;
        color: #6c757d;
    }
    
    .species-wrapper {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 10px;
    }
    .species-wrapper canvas {
        max-width: 150px;
        max-height: 150px;
    }
    .species-legend {
        flex: 1;
        font-size: 0.85rem;
    }
    .species-legend .dot {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 6px;
    }
    .species-count {
        font-weight: 700;
    }
    
    .total {
        text-align: center;
        font-size: 1.1rem;
        margin: 10px 0;
    }
    .total span {
        color: #198754;
        font-weight: 700;
        font-size: 1.3rem;
    }
    
    .box-header.between {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }
    
    .box-footer {
        padding: 10px 12px;
        border-top: 1px solid #eee;
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
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-add-task:hover, .btn-add-species:hover {
        background: #146c43;
        transform: translateY(-2px);
    }
    
    .next-btn {
        cursor: pointer;
        color: #198754;
        font-size: 0.8rem;
        transition: all 0.2s;
    }
    .next-btn:hover {
        color: #146c43;
    }
    
    .history-link {
        display: block;
        text-align: center;
        padding: 10px;
        color: #198754;
        text-decoration: none;
        font-size: 0.85rem;
        transition: all 0.2s;
    }
    .history-link:hover {
        background: #f8f9fa;
        text-decoration: underline;
    }
    
    .chart-wrapper {
        padding: 10px;
        height: 250px;
    }
    
    .task-count, .alert-count {
        font-weight: 400;
        font-size: 0.75rem;
        color: #6c757d;
        background: #e9ecef;
        padding: 2px 10px;
        border-radius: 12px;
    }
    
    .badge {
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    .badge-success { background: #d4edda; color: #198754; }
    .badge-warning { background: #fff3cd; color: #856404; }
    .badge-danger { background: #f8d7da; color: #dc3545; }
    .badge-secondary { background: #e9ecef; color: #6c757d; }
    
    @media (max-width: 768px) {
        .welcome-banner {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }
        .banner-date {
            width: 100%;
            text-align: center;
        }
        .chart-wrapper {
            height: 200px;
        }
        .species-wrapper {
            flex-direction: column;
            align-items: center;
        }
        .species-wrapper canvas {
            max-width: 120px;
            max-height: 120px;
        }
        .species-legend {
            width: 100%;
        }
        .box-header.between {
            flex-direction: column;
            align-items: flex-start;
        }
    }
`;
document.head.appendChild(style);

// ============================================================
// FONCTIONS POUR L'HISTORIQUE DES TÂCHES
// ============================================================

function showHistory(event) {
    event.preventDefault();
    
    // Récupérer les tâches depuis state.dashboardData
    var allTasks = state.dashboardData?.recent_activity?.dernieres_taches || [];
    var tasks = state.dashboardData?.taches?.prochaines_taches || [];
    
    // Combiner et trier par date
    var recentTasks = allTasks.concat(tasks)
        .filter(function(task) {
            return task.date_planifiee || task.date_realisee;
        })
        .sort(function(a, b) {
            var dateA = new Date(a.date_planifiee || a.date_realisee || 0);
            var dateB = new Date(b.date_planifiee || b.date_realisee || 0);
            return dateB - dateA;
        })
        .slice(0, 20);
    
    if (recentTasks.length === 0) {
        showToast('Aucune tâche récente', 'info');
        return;
    }
    
    openTaskHistoryModal(recentTasks);
}

function openTaskHistoryModal(tasks) {
    // Supprimer un ancien modal
    var oldModal = document.getElementById('taskHistoryModal');
    if (oldModal) oldModal.remove();
    
    var today = new Date().toISOString().split('T')[0];
    
    var modal = document.createElement('div');
    modal.id = 'taskHistoryModal';
    modal.className = 'modal-overlay';
    
    var html = '';
    html += '<div class="modal-box">';
    html += '  <div class="modal-header">';
    html += '    <h2>📋 Historique des tâches</h2>';
    html += '    <button class="modal-close" onclick="closeTaskHistoryModal()">×</button>';
    html += '  </div>';
    html += '  <div class="modal-body">';
    html += '    <div class="modal-stats">';
    html += '      <span>Total : <strong>' + tasks.length + '</strong> tâche' + (tasks.length > 1 ? 's' : '') + '</span>';
    
    var todayCount = tasks.filter(function(t) {
        return t.date_planifiee === today || t.date_realisee === today;
    }).length;
    html += '      <span>' + todayCount + ' aujourd\'hui</span>';
    html += '    </div>';
    html += '    <div class="modal-list">';
    
    for (var i = 0; i < tasks.length; i++) {
        var task = tasks[i];
        var date = task.date_planifiee || task.date_realisee;
        var dateObj = date ? new Date(date) : null;
        var dateStr = dateObj ? dateObj.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' }) : 'N/A';
        var timeStr = dateObj ? dateObj.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }) : '';
        var isToday = date && date.startsWith(today);
        var isCompleted = !!task.date_realisee;
        var title = task.titre || task.description || task.animal_nom || 'Sans titre';
        var typeIcon = task.type_icone || (isCompleted ? '✅' : '📋');
        var statusLabel = isCompleted ? '✅ Terminée' : '⏳ À venir';
        var statusClass = isCompleted ? 'completed' : 'pending';
        var animalHtml = task.animal_nom ? '<div class="task-animal">🐄 ' + escapeHtml(task.animal_nom) + '</div>' : '';
        var descHtml = (task.description && task.description !== task.titre) ? '<div class="task-desc">' + escapeHtml(task.description) + '</div>' : '';
        
        html += '<div class="modal-task-item ' + statusClass + '">';
        html += '  <div class="task-icon">' + typeIcon + '</div>';
        html += '  <div class="task-content">';
        html += '    <div class="task-title">' + escapeHtml(title) + '</div>';
        html += '    <div class="task-meta">';
        html += '      <span class="task-date">' + dateStr + ' ' + timeStr + '</span>';
        if (isToday) html += '<span class="task-badge today">Aujourd\'hui</span>';
        html += '      <span class="task-badge ' + statusClass + '">' + statusLabel + '</span>';
        html += '    </div>';
        html += animalHtml;
        html += descHtml;
        html += '  </div>';
        html += '</div>';
    }
    
    html += '    </div>';
    html += '  </div>';
    html += '  <div class="modal-footer">';
    html += '    <button onclick="navigateTo(\'/taches\')" class="btn-primary">📅 Voir le calendrier</button>';
    html += '    <button onclick="closeTaskHistoryModal()" class="btn-secondary">Fermer</button>';
    html += '  </div>';
    html += '</div>';
    
    modal.innerHTML = html;
    document.body.appendChild(modal);
    
    // Ouvrir avec animation
    setTimeout(function() {
        modal.classList.add('open');
    }, 10);
    
    // Fermer en cliquant sur l'overlay
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeTaskHistoryModal();
        }
    });
    
    // Fermer avec Echap
    var escHandler = function(e) {
        if (e.key === 'Escape') {
            closeTaskHistoryModal();
            document.removeEventListener('keydown', escHandler);
        }
    };
    document.addEventListener('keydown', escHandler);
}

function closeTaskHistoryModal() {
    var modal = document.getElementById('taskHistoryModal');
    if (modal) {
        modal.classList.remove('open');
        setTimeout(function() {
            modal.remove();
        }, 300);
    }
}

function nextActivity() {
    // Vérifier qu'il y a des activités
    if (!state.activities || state.activities.length === 0) {
        showToast('Aucune activité à afficher', 'info');
        return;
    }
    
    var total = state.activities.length;
    var maxStart = Math.max(0, total - state.activitiesPerPage);
    
    // Passer à la page suivante
    state.currentActivityIndex += state.activitiesPerPage;
    if (state.currentActivityIndex > maxStart) {
        state.currentActivityIndex = 0;
    }
    
    displayActivities();
}
</script>
@endsection
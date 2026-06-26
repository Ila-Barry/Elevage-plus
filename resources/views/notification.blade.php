@extends('layouts.menu')

@section('title', 'Notifications')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/eleveurCSS/notification.css') }}">
@endpush

@section('content')

<div class="dashboard-wrapper">

    <div class="notifications-container">

        <!-- En-tête de la page -->
        <div class="notifications-header">
            <div class="header-title">
                <i class="fas fa-bell"></i>
                <h1>Notifications</h1>
                <span class="notification-badge" id="totalNotifCount">0</span>
            </div>
            <div class="header-actions">
                <button class="btn-mark-all" id="markAllReadBtn">
                    <i class="fas fa-check-double"></i>
                    <span>Tout marquer comme lu</span>
                </button>
                <button class="btn-settings" id="notificationSettingsBtn">
                    <i class="fas fa-cog"></i>
                </button>
            </div>
        </div>

        <!-- Filtres -->
        <div class="notifications-filters">
            <button class="filter-btn active" data-filter="all">
                <i class="fas fa-list"></i>
                <span>Toutes</span>
                <span class="filter-count" id="allCount">0</span>
            </button>
            <button class="filter-btn" data-filter="unread">
                <i class="fas fa-envelope"></i>
                <span>Non lues</span>
                <span class="filter-count" id="unreadCount">0</span>
            </button>
            <button class="filter-btn" data-filter="read">
                <i class="fas fa-envelope-open"></i>
                <span>Lues</span>
                <span class="filter-count" id="readCount">0</span>
            </button>
            <div class="filter-divider"></div>
            <button class="filter-btn" data-filter="success">
                <i class="fas fa-check-circle"></i>
                <span>Succès</span>
            </button>
            <button class="filter-btn" data-filter="warning">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Alertes</span>
            </button>
            <button class="filter-btn" data-filter="danger">
                <i class="fas fa-times-circle"></i>
                <span>Urgents</span>
            </button>
            <button class="filter-btn" data-filter="info">
                <i class="fas fa-info-circle"></i>
                <span>Infos</span>
            </button>
        </div>

        <!-- Liste des notifications -->
        <div class="notifications-list" id="notificationsList">
            <!-- Les notifications seront chargées dynamiquement via JavaScript -->
        </div>

        <!-- Message "Aucune notification" -->
        <div class="empty-notifications" id="emptyNotifications" style="display: none;">
            <div class="empty-icon">
                <i class="fas fa-bell-slash"></i>
            </div>
            <h3>Aucune notification</h3>
            <p>Vous n'avez aucune notification pour le moment.</p>
        </div>

        <!-- Pagination -->
        <div class="notifications-pagination" id="paginationContainer">
            <button class="page-btn prev" id="prevPageBtn" disabled>
                <i class="fas fa-chevron-left"></i>
                <span>Précédent</span>
            </button>
            <div class="page-numbers" id="pageNumbers">
                <!-- Généré par JavaScript -->
            </div>
            <button class="page-btn next" id="nextPageBtn">
                <span>Suivant</span>
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>

    </div>

</div>

@push('scripts')
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
};

// ============================================================
// ÉTAT DE L'APPLICATION
// ============================================================

const state = {
    notifications: [],
    currentFilter: 'all',
    currentPage: 1,
    totalPages: 1,
    perPage: 15,
    total: 0,
    unreadCount: 0,
    isLoading: false,
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
    if (diff < 3600000) return Math.floor(diff / 60000) + ' min';
    if (diff < 86400000) return 'Il y a ' + Math.floor(diff / 3600000) + 'h';
    if (diff < 604800000) return 'Il y a ' + Math.floor(diff / 86400000) + 'j';
    return date.toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
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

    if (config.body && typeof config.body === 'object') {
        config.body = JSON.stringify(config.body);
    }

    const url = endpoint.startsWith('http') ? endpoint : `${CONFIG.API_URL}${endpoint}`;
    log(`🌐 Requête ${options.method || 'GET'} ${url}`);

    try {
        const response = await fetch(url, config);
        const data = await response.json();

        if (!response.ok) {
            logError(`Erreur HTTP ${response.status}`, data);
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
// CHARGEMENT DES NOTIFICATIONS
// ============================================================

async function loadNotifications(page = 1, filter = 'all') {
    if (state.isLoading) return;
    state.isLoading = true;
    state.currentPage = page;
    state.currentFilter = filter;

    const container = document.getElementById('notificationsList');
    container.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-success" role="status">
                <span class="sr-only">Chargement...</span>
            </div>
            <p class="mt-2 text-muted">Chargement des notifications...</p>
        </div>
    `;

    try {
        let endpoint = '/notifications';
        let params = `page=${page}&per_page=${state.perPage}`;

        if (filter === 'unread') {
            endpoint = '/notifications/unread';
        } else if (filter !== 'all') {
            params += `&type=${filter}`;
        }

        const result = await apiCall(`${endpoint}?${params}`);

        if (result.status === 'success') {
            const data = result.data;
            
            // Gérer les différents formats de réponse
            if (Array.isArray(data)) {
                state.notifications = data;
                state.total = data.length;
                state.totalPages = 1;
            } else if (data.data) {
                state.notifications = data.data;
                state.total = data.meta?.total || data.data.length;
                state.totalPages = data.meta?.last_page || 1;
                state.unreadCount = data.meta?.unread_count || 0;
            } else {
                state.notifications = [];
                state.total = 0;
                state.totalPages = 1;
            }

            renderNotifications();
            updateCounters();
            updatePagination();
        } else {
            showToast(result.message || 'Erreur lors du chargement', 'danger');
            renderNotifications();
        }
    } catch (error) {
        logError('Erreur chargement notifications', error);
        showToast('Erreur lors du chargement des notifications', 'danger');
        renderNotifications();
    } finally {
        state.isLoading = false;
    }
}

// ============================================================
// RENDU DES NOTIFICATIONS
// ============================================================

function renderNotifications() {
    const container = document.getElementById('notificationsList');
    const emptyDiv = document.getElementById('emptyNotifications');

    if (state.notifications.length === 0) {
        container.style.display = 'none';
        emptyDiv.style.display = 'flex';
        return;
    }

    container.style.display = 'flex';
    emptyDiv.style.display = 'none';

    container.innerHTML = state.notifications.map(notification => {
        const isRead = notification.is_read || false;
        const icon = notification.icon || '🔔';
        const type = notification.type || 'info';
        const url = notification.url || '#';
        const title = notification.title || 'Notification';
        const message = notification.message || '';
        const time = formatDate(notification.created_at);
        const actions = notification.actions || [];

        // Déterminer la classe CSS selon le type
        const iconClass = getIconClass(type);

        return `
            <div class="notification-item ${isRead ? '' : 'unread'}" 
                 data-id="${notification.id}" 
                 data-url="${url}"
                 data-type="${type}">
                <div class="notification-icon ${iconClass}">
                    <i class="${getIconType(type)}"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-header">
                        <h4>${escapeHtml(title)}</h4>
                        <span class="notification-time">${time}</span>
                    </div>
                    <p class="notification-message">${escapeHtml(message)}</p>
                    ${actions.length > 0 ? `
                        <div class="notification-footer">
                            ${actions.map(action => `
                                <button class="action-btn ${action.type || ''}" 
                                        onclick="handleAction('${notification.id}', '${action.label}', '${action.url || '#'}')">
                                    ${action.label}
                                </button>
                            `).join('')}
                        </div>
                    ` : ''}
                </div>
                ${!isRead ? `
                    <button class="mark-read-btn" onclick="markAsRead('${notification.id}')" title="Marquer comme lu">
                        <i class="fas fa-circle"></i>
                    </button>
                ` : `
                    <button class="mark-read-btn" style="opacity: 0.4;" disabled>
                        <i class="fas fa-circle"></i>
                    </button>
                `}
            </div>
        `;
    }).join('');

    // Ajouter les événements de clic sur les notifications
    document.querySelectorAll('.notification-item').forEach(item => {
        item.addEventListener('click', function(e) {
            if (e.target.closest('button')) return;
            const id = this.dataset.id;
            const url = this.dataset.url;
            if (url && url !== '#') {
                window.location.href = url;
            }
            if (!this.classList.contains('unread')) return;
            markAsRead(id);
        });
    });
}

// ============================================================
// FONCTIONS D'ICÔNES
// ============================================================

function getIconClass(type) {
    const classes = {
        'success': 'success-icon',
        'warning': 'alert-icon',
        'danger': 'report-icon',
        'info': 'message-icon',
        'stock': 'stock-icon',
        'task': 'task-icon',
        'message': 'message-icon',
        'like': 'like-icon',
        'share': 'share-icon',
        'comment': 'comment-icon',
        'report': 'report-icon',
        'welcome': 'welcome-icon',
        'user': 'user-icon',
    };
    return classes[type] || 'message-icon';
}

function getIconType(type) {
    const icons = {
        'success': 'fa-check-circle',
        'warning': 'fa-exclamation-triangle',
        'danger': 'fa-times-circle',
        'info': 'fa-info-circle',
        'stock': 'fa-box',
        'task': 'fa-tasks',
        'message': 'fa-comment-dots',
        'like': 'fa-heart',
        'share': 'fa-share-alt',
        'comment': 'fa-comment',
        'report': 'fa-flag',
        'welcome': 'fa-hand-peace',
        'user': 'fa-user-plus',
    };
    return icons[type] || 'fa-bell';
}

// ============================================================
// ACTIONS DES NOTIFICATIONS
// ============================================================

function handleAction(notificationId, label, url) {
    log(`📌 Action: ${label} sur la notification ${notificationId}`);
    
    // Marquer comme lue avant de rediriger
    markAsRead(notificationId, false);
    
    if (url && url !== '#') {
        setTimeout(() => {
            window.location.href = url;
        }, 300);
    } else {
        showToast(`Action: ${label}`, 'info');
    }
}

// ============================================================
// MARQUER COMME LU
// ============================================================

async function markAsRead(notificationId, showToastMsg = true) {
    try {
        const result = await apiCall(`/notifications/${notificationId}/read`, {
            method: 'POST'
        });

        if (result.status === 'success') {
            // Mettre à jour localement
            const item = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
            if (item) {
                item.classList.remove('unread');
                const markBtn = item.querySelector('.mark-read-btn');
                if (markBtn) {
                    markBtn.style.opacity = '0.4';
                    markBtn.disabled = true;
                }
            }
            
            updateCounters();
            
            if (showToastMsg) {
                showToast('Notification marquée comme lue', 'success');
            }
        }
    } catch (error) {
        logError('Erreur marquage comme lu', error);
        if (showToastMsg) {
            showToast('Erreur lors du marquage', 'danger');
        }
    }
}

// ============================================================
// TOUT MARQUER COMME LU
// ============================================================

async function markAllAsRead() {
    try {
        const result = await apiCall('/notifications/read-all', {
            method: 'POST'
        });

        if (result.status === 'success') {
            // Mettre à jour localement
            document.querySelectorAll('.notification-item.unread').forEach(item => {
                item.classList.remove('unread');
                const markBtn = item.querySelector('.mark-read-btn');
                if (markBtn) {
                    markBtn.style.opacity = '0.4';
                    markBtn.disabled = true;
                }
            });
            
            updateCounters();
            showToast('Toutes les notifications ont été marquées comme lues', 'success');
        }
    } catch (error) {
        logError('Erreur marquage tout comme lu', error);
        showToast('Erreur lors du marquage', 'danger');
    }
}

// ============================================================
// METTRE À JOUR LES COMPTEURS
// ============================================================

function updateCounters() {
    const total = state.notifications.length;
    const unread = document.querySelectorAll('.notification-item.unread').length;
    const read = total - unread;

    document.getElementById('totalNotifCount').textContent = total;
    document.getElementById('allCount').textContent = total;
    document.getElementById('unreadCount').textContent = unread;
    document.getElementById('readCount').textContent = read;
}

// ============================================================
// PAGINATION
// ============================================================

function updatePagination() {
    const pageNumbers = document.getElementById('pageNumbers');
    const prevBtn = document.getElementById('prevPageBtn');
    const nextBtn = document.getElementById('nextPageBtn');

    if (state.totalPages <= 1) {
        pageNumbers.innerHTML = '';
        prevBtn.disabled = true;
        nextBtn.disabled = true;
        return;
    }

    let html = '';
    const maxVisible = 5;
    let start = Math.max(1, state.currentPage - Math.floor(maxVisible / 2));
    let end = Math.min(state.totalPages, start + maxVisible - 1);

    if (end - start < maxVisible - 1) {
        start = Math.max(1, end - maxVisible + 1);
    }

    if (start > 1) {
        html += `<button class="page-number" onclick="goToPage(1)">1</button>`;
        if (start > 2) html += `<button class="page-number" disabled>...</button>`;
    }

    for (let i = start; i <= end; i++) {
        html += `<button class="page-number ${i === state.currentPage ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
    }

    if (end < state.totalPages) {
        if (end < state.totalPages - 1) html += `<button class="page-number" disabled>...</button>`;
        html += `<button class="page-number" onclick="goToPage(${state.totalPages})">${state.totalPages}</button>`;
    }

    pageNumbers.innerHTML = html;
    prevBtn.disabled = state.currentPage === 1;
    nextBtn.disabled = state.currentPage === state.totalPages;
}

function goToPage(page) {
    if (page === state.currentPage || state.isLoading) return;
    loadNotifications(page, state.currentFilter);
    document.querySelector('.notifications-list').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// ============================================================
// FILTRES
// ============================================================

function setupFilters() {
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            state.currentFilter = filter;
            state.currentPage = 1;
            loadNotifications(1, filter);
        });
    });
}

// ============================================================
// TOAST NOTIFICATIONS
// ============================================================

function showToast(message, type = 'info') {
    const existing = document.querySelector('.custom-toast');
    if (existing) existing.remove();
    
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
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// ============================================================
// POLLING - RÉCUPÉRATION EN TEMPS RÉEL
// ============================================================

let pollingInterval = null;

function startPolling() {
    if (pollingInterval) clearInterval(pollingInterval);
    pollingInterval = setInterval(() => {
        if (document.hidden) return;
        loadNotifications(state.currentPage, state.currentFilter);
    }, 30000); // Toutes les 30 secondes
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
    log('🚀 Initialisation de la page Notifications');
    
    if (!CONFIG.TOKEN) {
        showToast('Non connecté. Redirection...', 'danger');
        setTimeout(() => window.location.href = '/auth/login', 2000);
        return;
    }
    
    // Charger les notifications
    loadNotifications(1, 'all');
    
    // Configurer les filtres
    setupFilters();
    
    // Événement "Tout marquer comme lu"
    document.getElementById('markAllReadBtn').addEventListener('click', markAllAsRead);
    
    // Événement "Paramètres"
    document.getElementById('notificationSettingsBtn').addEventListener('click', function() {
        showToast('Paramètres des notifications (bientôt disponible)', 'info');
    });
    
    // Pagination
    document.getElementById('prevPageBtn').addEventListener('click', function() {
        if (state.currentPage > 1) goToPage(state.currentPage - 1);
    });
    
    document.getElementById('nextPageBtn').addEventListener('click', function() {
        if (state.currentPage < state.totalPages) goToPage(state.currentPage + 1);
    });
    
    // Démarrer le polling
    startPolling();
    
    // Arrêter le polling quand la page est cachée
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            stopPolling();
        } else {
            startPolling();
            // Rafraîchir immédiatement
            loadNotifications(state.currentPage, state.currentFilter);
        }
    });
    
    // Nettoyer le polling avant de quitter
    window.addEventListener('beforeunload', function() {
        stopPolling();
    });
    
    log('✅ Page Notifications initialisée avec succès');
});
</script>
@endpush
@endsection
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
                <span class="filter-count" id="successCount">0</span>
            </button>
            <button class="filter-btn" data-filter="warning">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Alertes</span>
                <span class="filter-count" id="warningCount">0</span>
            </button>
            <button class="filter-btn" data-filter="danger">
                <i class="fas fa-times-circle"></i>
                <span>Urgents</span>
                <span class="filter-count" id="dangerCount">0</span>
            </button>
            <button class="filter-btn" data-filter="info">
                <i class="fas fa-info-circle"></i>
                <span>Infos</span>
                <span class="filter-count" id="infoCount">0</span>
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
            <p id="emptyMessage">Vous n'avez aucune notification pour le moment.</p>
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
    allNotifications: [], // Toutes les notifications en cache
    currentFilter: 'all',
    currentPage: 1,
    totalPages: 1,
    perPage: 15,
    total: 0,
    unreadCount: 0,
    readCount: 0,
    successCount: 0,
    warningCount: 0,
    dangerCount: 0,
    infoCount: 0,
    isLoading: false,
    pollingInterval: null,
    notificationPollingInterval: null,
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
// MISE À JOUR DU BADGE - UNIQUEMENT POUR LES NON LUES
// ============================================================

function updateNotificationBadge() {
    const count = state.unreadCount;
    
    const badgeElements = document.querySelectorAll('.notification-badge, .menu-notification-badge');
    badgeElements.forEach(el => {
        if (count > 0) {
            el.textContent = count;
            el.style.display = 'inline-block';
            el.classList.remove('pulse');
            void el.offsetWidth;
            el.classList.add('pulse');
        } else {
            el.textContent = '0';
            el.style.display = 'none';
        }
    });

    if (count > 0) {
        document.title = `(${count}) Notifications - Élevage+`;
    } else {
        document.title = 'Notifications - Élevage+';
    }
}

// ============================================================
// API CALLS
// ============================================================

async function apiCall(endpoint, options = {}) {
    if (!CONFIG.TOKEN) {
        logError('❌ Token manquant pour l\'appel API', endpoint);
        const raw = localStorage.getItem('access_token');
        CONFIG.TOKEN = raw ? raw.replace(/^"(.*)"$/, '$1').trim() : null;
        if (!CONFIG.TOKEN) {
            throw new Error('Non authentifié');
        }
    }

    const defaultHeaders = {
        'Accept': 'application/json',
        'Authorization': 'Bearer ' + CONFIG.TOKEN,
        'X-CSRF-TOKEN': CONFIG.CSRF_TOKEN,
        'Content-Type': 'application/json',
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
        
        if (response.status === 401) {
            logError('🔑 Token expiré, tentative de rafraîchissement...');
            try {
                const refreshResult = await refreshToken();
                if (refreshResult) {
                    config.headers.Authorization = 'Bearer ' + CONFIG.TOKEN;
                    const retryResponse = await fetch(url, config);
                    const retryData = await retryResponse.json();
                    if (retryResponse.ok) {
                        return retryData;
                    }
                }
            } catch (refreshError) {
                logError('❌ Échec du rafraîchissement du token', refreshError);
                window.location.href = '/auth/login';
                throw new Error('Session expirée, veuillez vous reconnecter');
            }
        }

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

async function refreshToken() {
    try {
        const response = await fetch(`${CONFIG.API_URL}/auth/refresh`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + CONFIG.TOKEN,
                'X-CSRF-TOKEN': CONFIG.CSRF_TOKEN,
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            if (data.data && data.data.access_token) {
                const newToken = data.data.access_token;
                localStorage.setItem('access_token', JSON.stringify(newToken));
                CONFIG.TOKEN = newToken;
                log('✅ Token rafraîchi avec succès');
                return true;
            }
        }
        return false;
    } catch (error) {
        logError('❌ Erreur rafraîchissement token', error);
        return false;
    }
}

// ============================================================
// RÉCUPÉRATION DES STATISTIQUES
// ============================================================

async function fetchNotificationStats() {
    try {
        const result = await apiCall('/notifications/stats');
        if (result.status === 'success' || result.success === true) {
            const stats = result.data;
            return {
                total: stats.total || 0,
                unread: stats.unread || 0,
                read: stats.read || 0
            };
        }
        return { total: 0, unread: 0, read: 0 };
    } catch (error) {
        logError('Erreur récupération stats', error);
        return { total: 0, unread: 0, read: 0 };
    }
}

// ============================================================
// FILTRAGE DES NOTIFICATIONS PAR TYPE
// ============================================================

function filterNotificationsByType(notifications, type) {
    if (type === 'all') return notifications;
    if (type === 'unread') return notifications.filter(n => !n.read_at && !n.is_read);
    if (type === 'read') return notifications.filter(n => n.read_at || n.is_read);
    return notifications.filter(n => n.data?.type === type || n.type === type);
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
    if (!container) return;
    
    container.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-success" role="status">
                <span class="sr-only">Chargement...</span>
            </div>
            <p class="mt-2 text-muted">Chargement des notifications...</p>
        </div>
    `;

    try {
        // Récupérer toutes les notifications
        const result = await apiCall(`/notifications?page=1&per_page=100`);
        
        if (result.status === 'success' || result.success === true) {
            const responseData = result.data;
            let allNotifications = [];
            
            if (responseData && responseData.data && Array.isArray(responseData.data)) {
                allNotifications = responseData.data;
                state.total = responseData.meta?.total || responseData.data.length;
            } else if (Array.isArray(responseData)) {
                allNotifications = responseData;
                state.total = responseData.length;
            }
            
            // 🔥 Calculer les statistiques sur TOUTES les notifications
            state.unreadCount = allNotifications.filter(n => !n.read_at && !n.is_read).length;
            state.readCount = allNotifications.filter(n => n.read_at || n.is_read).length;
            
            // 🔥 Statistiques par type
            state.successCount = allNotifications.filter(n => (n.data?.type || n.type) === 'success').length;
            state.warningCount = allNotifications.filter(n => (n.data?.type || n.type) === 'warning').length;
            state.dangerCount = allNotifications.filter(n => (n.data?.type || n.type) === 'danger').length;
            state.infoCount = allNotifications.filter(n => (n.data?.type || n.type) === 'info').length;
            
            // 🔥 Appliquer le filtre pour l'affichage
            let filteredNotifications = filterNotificationsByType(allNotifications, filter);
            
            // Pagination manuelle
            const start = (page - 1) * state.perPage;
            const end = start + state.perPage;
            const paginated = filteredNotifications.slice(start, end);
            
            state.notifications = paginated;
            state.totalPages = Math.ceil(filteredNotifications.length / state.perPage) || 1;
            
            // Mettre à jour le badge (UNIQUEMENT non lues)
            updateNotificationBadge();
            
            // Mettre à jour tous les compteurs
            updateAllCounters();
            
            renderNotifications(filteredNotifications.length);
            updatePagination();
        } else {
            showToast(result.message || 'Erreur lors du chargement', 'danger');
            renderNotifications(0);
        }
    } catch (error) {
        logError('Erreur chargement notifications', error);
        if (error.message === 'Non authentifié') {
            showToast('Session expirée, veuillez vous reconnecter', 'danger');
            setTimeout(() => window.location.href = '/auth/login', 2000);
        } else {
            showToast('Erreur lors du chargement des notifications', 'danger');
        }
        renderNotifications(0);
    } finally {
        state.isLoading = false;
    }
}

// ============================================================
// RENDU DES NOTIFICATIONS
// ============================================================

function renderNotifications(totalCount) {
    const container = document.getElementById('notificationsList');
    const emptyDiv = document.getElementById('emptyNotifications');

    if (!container || !emptyDiv) return;

    if (state.notifications.length === 0) {
        container.style.display = 'none';
        emptyDiv.style.display = 'flex';
        
        const filterLabels = {
            'all': 'Vous n\'avez aucune notification.',
            'unread': 'Vous n\'avez aucune notification non lue.',
            'read': 'Vous n\'avez aucune notification lue.',
            'success': 'Vous n\'avez aucune notification de succès.',
            'warning': 'Vous n\'avez aucune alerte.',
            'danger': 'Vous n\'avez aucune notification urgente.',
            'info': 'Vous n\'avez aucune notification d\'information.'
        };
        document.getElementById('emptyMessage').textContent = filterLabels[state.currentFilter] || 'Aucune notification trouvée.';
        return;
    }

    container.style.display = 'flex';
    emptyDiv.style.display = 'none';

    container.innerHTML = state.notifications.map(notification => {
        const isRead = notification.read_at !== null || notification.is_read === true;
        
        // 🔥 Récupérer le type depuis la structure de données
        let type = 'info';
        if (notification.data && notification.data.type) {
            type = notification.data.type;
        } else if (notification.type) {
            type = notification.type;
        }
        
        // 🔥 Récupérer le titre
        let title = 'Notification';
        if (notification.data && notification.data.title) {
            title = notification.data.title;
        } else if (notification.title) {
            title = notification.title;
        }
        
        // 🔥 Récupérer le message
        let message = '';
        if (notification.data && notification.data.message) {
            message = notification.data.message;
        } else if (notification.message) {
            message = notification.message;
        }
        
        // 🔥 Récupérer l'URL
        let url = '#';
        if (notification.data && notification.data.url) {
            url = notification.data.url;
        } else if (notification.url) {
            url = notification.url;
        }
        
        const time = formatDate(notification.created_at);
        const actions = notification.data?.actions || [];

        // 🔥 Déterminer l'icône
        const iconClass = getIconClass(type);
        const iconType = getIconType(type);

        return `
            <div class="notification-item ${isRead ? '' : 'unread'}" 
                 data-id="${notification.id}" 
                 data-url="${url}"
                 data-type="${type}">
                <div class="notification-icon ${iconClass}">
                    <i class="fas ${iconType}"></i>
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
                                        onclick="handleAction('${notification.id}', '${escapeHtml(action.label)}', '${action.url || '#'}')">
                                    ${escapeHtml(action.label)}
                                </button>
                            `).join('')}
                        </div>
                    ` : ''}
                </div>
                ${!isRead ? `
                    <button class="mark-read-btn" onclick="event.stopPropagation(); markAsRead('${notification.id}')" title="Marquer comme lu">
                        <i class="fas fa-circle"></i>
                    </button>
                ` : `
                    <button class="mark-read-btn" style="opacity: 0.4;" disabled>
                        <i class="fas fa-check-circle"></i>
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
            
            if (this.classList.contains('unread')) {
                markAsRead(id, false);
            }
            
            if (url && url !== '#') {
                window.location.href = url;
            }
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
        'created': 'fa-plus-circle',
        'updated': 'fa-edit',
        'deleted': 'fa-trash',
        'liked': 'fa-heart',
        'commented': 'fa-comment',
        'shared': 'fa-share-alt',
        'stock_entree': 'fa-arrow-down',
        'stock_sortie': 'fa-arrow-up',
        'stock_critique': 'fa-exclamation-triangle',
        'stock_rupture': 'fa-times-circle',
        'stock_expiration': 'fa-clock',
        'reminder': 'fa-bell',
        'new_message': 'fa-envelope',
        'message_read': 'fa-check-double',
        'task_reminder': 'fa-bell',
    };
    
    return icons[type] || 'fa-bell';
}

// ============================================================
// MISE À JOUR DE TOUS LES COMPTEURS
// ============================================================

function updateAllCounters() {
    // Badge totalNotifCount : UNIQUEMENT les NON LUES
    document.getElementById('totalNotifCount').textContent = state.unreadCount || 0;
    
    // allCount : TOTAL de toutes les notifications
    document.getElementById('allCount').textContent = state.total || 0;
    
    // unreadCount : UNIQUEMENT les NON LUES
    document.getElementById('unreadCount').textContent = state.unreadCount || 0;
    
    // readCount : UNIQUEMENT les LUES
    document.getElementById('readCount').textContent = state.readCount || 0;
    
    // Compteurs par type
    document.getElementById('successCount').textContent = state.successCount || 0;
    document.getElementById('warningCount').textContent = state.warningCount || 0;
    document.getElementById('dangerCount').textContent = state.dangerCount || 0;
    document.getElementById('infoCount').textContent = state.infoCount || 0;
}

// ============================================================
// ACTIONS DES NOTIFICATIONS
// ============================================================

function handleAction(notificationId, label, url) {
    event.stopPropagation();
    log(`📌 Action: ${label} sur la notification ${notificationId}`);
    
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

        if (result.status === 'success' || result.success === true) {
            const item = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
            if (item) {
                item.classList.remove('unread');
                const markBtn = item.querySelector('.mark-read-btn');
                if (markBtn) {
                    markBtn.style.opacity = '0.4';
                    markBtn.innerHTML = '<i class="fas fa-check-circle"></i>';
                    markBtn.disabled = true;
                }
                
                state.unreadCount = Math.max(0, state.unreadCount - 1);
                state.readCount = state.readCount + 1;
                
                updateNotificationBadge();
                updateAllCounters();
            }
            
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

        if (result.status === 'success' || result.success === true) {
            document.querySelectorAll('.notification-item.unread').forEach(item => {
                item.classList.remove('unread');
                const markBtn = item.querySelector('.mark-read-btn');
                if (markBtn) {
                    markBtn.style.opacity = '0.4';
                    markBtn.innerHTML = '<i class="fas fa-check-circle"></i>';
                    markBtn.disabled = true;
                }
            });
            
            state.readCount = state.readCount + state.unreadCount;
            state.unreadCount = 0;
            
            updateNotificationBadge();
            updateAllCounters();
            showToast('Toutes les notifications ont été marquées comme lues', 'success');
        }
    } catch (error) {
        logError('Erreur marquage tout comme lu', error);
        showToast('Erreur lors du marquage', 'danger');
    }
}

// ============================================================
// PAGINATION
// ============================================================

function updatePagination() {
    const pageNumbers = document.getElementById('pageNumbers');
    const prevBtn = document.getElementById('prevPageBtn');
    const nextBtn = document.getElementById('nextPageBtn');

    if (!pageNumbers || !prevBtn || !nextBtn) return;

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
        if (start > 2) html += `<span class="page-number disabled">...</span>`;
    }

    for (let i = start; i <= end; i++) {
        html += `<button class="page-number ${i === state.currentPage ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
    }

    if (end < state.totalPages) {
        if (end < state.totalPages - 1) html += `<span class="page-number disabled">...</span>`;
        html += `<button class="page-number" onclick="goToPage(${state.totalPages})">${state.totalPages}</button>`;
    }

    pageNumbers.innerHTML = html;
    prevBtn.disabled = state.currentPage === 1;
    nextBtn.disabled = state.currentPage === state.totalPages;
}

function goToPage(page) {
    if (page === state.currentPage || state.isLoading) return;
    loadNotifications(page, state.currentFilter);
    const list = document.querySelector('.notifications-list');
    if (list) {
        list.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
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
            <span>${escapeHtml(message)}</span>
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
// VÉRIFICATION DES NOUVELLES NOTIFICATIONS
// ============================================================

async function checkNewNotifications() {
    try {
        // Recharger complètement les données
        await loadNotifications(state.currentPage, state.currentFilter);
    } catch (error) {
        logError('Erreur vérification nouvelles notifications', error);
    }
}

// ============================================================
// POLLING
// ============================================================

function startAllPolling() {
    if (state.notificationPollingInterval) {
        clearInterval(state.notificationPollingInterval);
    }
    state.notificationPollingInterval = setInterval(() => {
        if (!document.hidden) {
            checkNewNotifications();
        }
    }, 15000);

    if (state.pollingInterval) {
        clearInterval(state.pollingInterval);
    }
    state.pollingInterval = setInterval(() => {
        if (!document.hidden && document.getElementById('notificationsList')) {
            loadNotifications(state.currentPage, state.currentFilter);
        }
    }, 30000);
}

function stopAllPolling() {
    if (state.pollingInterval) {
        clearInterval(state.pollingInterval);
        state.pollingInterval = null;
    }
    if (state.notificationPollingInterval) {
        clearInterval(state.notificationPollingInterval);
        state.notificationPollingInterval = null;
    }
}

// ============================================================
// SERVICE WORKER
// ============================================================

function registerServiceWorker() {
    if (!('serviceWorker' in navigator)) {
        log('⚠️ Service Workers non supportés');
        return;
    }

    navigator.serviceWorker.register('/sw.js')
        .then(registration => {
            log('✅ Service Worker enregistré avec succès', registration);
            
            if ('Notification' in window && Notification.permission === 'granted') {
                subscribeToPushNotifications();
            }
        })
        .catch(error => {
            logError('❌ Erreur enregistrement Service Worker', error);
        });
}

function subscribeToPushNotifications() {
    if (!('serviceWorker' in navigator)) return;
    if (!('PushManager' in window)) return;
    
    navigator.serviceWorker.ready.then(registration => {
        registration.pushManager.getSubscription().then(subscription => {
            if (subscription) {
                log('✅ Déjà abonné aux notifications push', subscription);
                return;
            }
            
            const vapidPublicKey = document.querySelector('meta[name="vapid-public-key"]')?.content || '';
            if (!vapidPublicKey) {
                log('⚠️ Clé VAPID publique non trouvée');
                return;
            }
            
            const applicationServerKey = urlBase64ToUint8Array(vapidPublicKey);
            
            registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: applicationServerKey
            })
            .then(subscription => {
                log('✅ Abonnement push réussi', subscription);
                return saveSubscription(subscription);
            })
            .then(response => {
                if (response) {
                    log('✅ Subscription sauvegardée sur le serveur', response);
                }
            })
            .catch(error => {
                logError('❌ Erreur abonnement push', error);
            });
        });
    });
}

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding)
        .replace(/-/g, '+')
        .replace(/_/g, '/');
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

async function saveSubscription(subscription) {
    try {
        const result = await apiCall('/webpush/subscribe', {
            method: 'POST',
            body: { subscription: subscription }
        });
        return result;
    } catch (error) {
        logError('❌ Erreur sauvegarde subscription', error);
        return null;
    }
}

// ============================================================
// INITIALISATION
// ============================================================

document.addEventListener('DOMContentLoaded', function() {
    log('🚀 Initialisation de la page Notifications');
    
    if (!CONFIG.TOKEN) {
        try {
            const raw = localStorage.getItem('access_token');
            CONFIG.TOKEN = raw ? raw.replace(/^"(.*)"$/, '$1').trim() : null;
        } catch (e) {
            CONFIG.TOKEN = null;
        }
        
        if (!CONFIG.TOKEN) {
            log('⚠️ Non authentifié, redirection vers login');
            showToast('Veuillez vous connecter', 'danger');
            setTimeout(() => window.location.href = '/auth/login', 2000);
            return;
        }
    }
    
    loadNotifications(1, 'all');
    setupFilters();
    
    const markAllBtn = document.getElementById('markAllReadBtn');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', markAllAsRead);
    }
    
    const settingsBtn = document.getElementById('notificationSettingsBtn');
    if (settingsBtn) {
        settingsBtn.addEventListener('click', function() {
            showToast('Paramètres des notifications (bientôt disponible)', 'info');
        });
    }
    
    const prevBtn = document.getElementById('prevPageBtn');
    const nextBtn = document.getElementById('nextPageBtn');
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            if (state.currentPage > 1) goToPage(state.currentPage - 1);
        });
    }
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            if (state.currentPage < state.totalPages) goToPage(state.currentPage + 1);
        });
    }
    
    startAllPolling();
    registerServiceWorker();
    
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            stopAllPolling();
        } else {
            startAllPolling();
            checkNewNotifications();
            loadNotifications(state.currentPage, state.currentFilter);
        }
    });
    
    window.addEventListener('beforeunload', function() {
        stopAllPolling();
    });
    
    window.addEventListener('online', function() {
        log('🌐 Réseau rétabli, rechargement des notifications');
        showToast('Connexion rétablie', 'success');
        loadNotifications(state.currentPage, state.currentFilter);
    });
    
    window.addEventListener('offline', function() {
        log('🌐 Réseau perdu');
        showToast('Connexion perdue. Vérifiez votre réseau.', 'warning');
    });
    
    log('✅ Page Notifications initialisée avec succès');
});
</script>
@endpush
@endsection
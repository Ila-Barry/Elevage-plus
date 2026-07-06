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
// MISE À JOUR DU BADGE (CORRECTION)
// ============================================================

function updateNotificationBadge(count) {
    // Mettre à jour le badge dans le menu principal
    const badgeElements = document.querySelectorAll('.notification-badge, .menu-notification-badge');
    badgeElements.forEach(el => {
        if (count > 0) {
            el.textContent = count;
            el.style.display = 'inline-block';
            // Animation
            el.classList.remove('pulse');
            void el.offsetWidth; // Force reflow
            el.classList.add('pulse');
        } else {
            el.textContent = '0';
            el.style.display = 'none';
        }
    });

    // Mettre à jour le titre de la page
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
    // Vérifier le token avant chaque appel
    if (!CONFIG.TOKEN) {
        logError('❌ Token manquant pour l\'appel API', endpoint);
        // Tentative de récupération du token depuis le localStorage
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
        
        // Gestion spéciale pour 401 (token expiré)
        if (response.status === 401) {
            logError('🔑 Token expiré, tentative de rafraîchissement...');
            try {
                const refreshResult = await refreshToken();
                if (refreshResult) {
                    // Réessayer la requête avec le nouveau token
                    config.headers.Authorization = 'Bearer ' + CONFIG.TOKEN;
                    const retryResponse = await fetch(url, config);
                    const retryData = await retryResponse.json();
                    if (retryResponse.ok) {
                        return retryData;
                    }
                }
            } catch (refreshError) {
                logError('❌ Échec du rafraîchissement du token', refreshError);
                // Rediriger vers la page de connexion
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

// Fonction de rafraîchissement du token
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
        let endpoint = '/notifications';
        let params = `page=${page}&per_page=${state.perPage}`;

        if (filter === 'unread') {
            endpoint = '/notifications/unread';
        } else if (filter !== 'all' && ['success', 'warning', 'danger', 'info'].includes(filter)) {
            params += `&type=${filter}`;
        }

        const result = await apiCall(`${endpoint}?${params}`);

        console.log('🔍 RÉPONSE COMPLÈTE:', JSON.stringify(result, null, 2));

        // ✅ CORRECTION : Vérifier "success" au lieu de "status"
        if (result.success === true || result.status === 'success') {
            const responseData = result.data;
            
            console.log('🔍 responseData:', responseData);
            
            // ✅ CORRECTION : La structure est data.data
            let notifications = [];
            let total = 0;
            let lastPage = 1;
            let unreadCount = 0;

            // La structure correcte est: result.data.data (tableau)
            if (responseData && responseData.data && Array.isArray(responseData.data)) {
                notifications = responseData.data;
                total = responseData.meta?.total || responseData.data.length;
                lastPage = responseData.meta?.last_page || 1;
                unreadCount = responseData.meta?.unread_count || 0;
                console.log('✅ Cas 1: responseData.data est un tableau');
            }
            // Fallback: si responseData est un tableau
            else if (Array.isArray(responseData)) {
                notifications = responseData;
                total = responseData.length;
                lastPage = 1;
                unreadCount = responseData.filter(n => !n.read_at).length;
                console.log('✅ Cas 2: responseData est un tableau');
            }
            // Fallback: si responseData est un objet avec des clés numériques
            else if (responseData && typeof responseData === 'object') {
                const values = Object.values(responseData);
                if (values.length > 0 && values[0] && (values[0].id || values[0].data)) {
                    notifications = values;
                    total = values.length;
                    lastPage = 1;
                    unreadCount = values.filter(n => !n.read_at).length;
                    console.log('✅ Cas 3: Objet avec clés numériques');
                }
            }

            console.log('📊 RÉSULTAT FINAL:');
            console.log('  - Notifications:', notifications);
            console.log('  - Total:', total);
            console.log('  - LastPage:', lastPage);
            console.log('  - UnreadCount:', unreadCount);
            console.log('  - Première notification:', notifications.length > 0 ? notifications[0] : 'Aucune');

            state.notifications = notifications;
            state.total = total;
            state.totalPages = lastPage;
            state.unreadCount = unreadCount;

            // Mettre à jour le badge
            updateNotificationBadge(state.unreadCount);
            
            renderNotifications();
            updateCounters();
            updatePagination();
        } else {
            console.log('❌ Erreur dans la réponse:', result);
            showToast(result.message || 'Erreur lors du chargement', 'danger');
            renderNotifications();
        }
    } catch (error) {
        console.error('❌ Erreur fatale:', error);
        logError('Erreur chargement notifications', error);
        if (error.message === 'Non authentifié') {
            showToast('Session expirée, veuillez vous reconnecter', 'danger');
            setTimeout(() => window.location.href = '/auth/login', 2000);
        } else {
            showToast('Erreur lors du chargement des notifications', 'danger');
        }
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

    if (!container || !emptyDiv) return;

    if (state.notifications.length === 0) {
        container.style.display = 'none';
        emptyDiv.style.display = 'flex';
        return;
    }

    container.style.display = 'flex';
    emptyDiv.style.display = 'none';

    container.innerHTML = state.notifications.map(notification => {
        const isRead = notification.read_at !== null || notification.is_read === true;
        const type = notification.data?.type || notification.type || 'info';
        const url = notification.data?.url || notification.url || '#';
        const title = notification.data?.title || notification.title || 'Notification';
        const message = notification.data?.message || notification.message || '';
        const time = formatDate(notification.created_at);
        const actions = notification.data?.actions || [];

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
            
            // Marquer comme lue si non lue
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
    };
    return icons[type] || 'fa-bell';
}

// ============================================================
// ACTIONS DES NOTIFICATIONS
// ============================================================

function handleAction(notificationId, label, url) {
    event.stopPropagation();
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
                    markBtn.innerHTML = '<i class="fas fa-check-circle"></i>';
                    markBtn.disabled = true;
                }
                
                // Mettre à jour le compteur
                state.unreadCount = Math.max(0, state.unreadCount - 1);
                updateNotificationBadge(state.unreadCount);
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
                    markBtn.innerHTML = '<i class="fas fa-check-circle"></i>';
                    markBtn.disabled = true;
                }
            });
            
            state.unreadCount = 0;
            updateNotificationBadge(0);
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

    const totalBadge = document.getElementById('totalNotifCount');
    const allCount = document.getElementById('allCount');
    const unreadCount = document.getElementById('unreadCount');
    const readCount = document.getElementById('readCount');
    
    if (totalBadge) totalBadge.textContent = total;
    if (allCount) allCount.textContent = total;
    if (unreadCount) unreadCount.textContent = unread;
    if (readCount) readCount.textContent = read;
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
        const result = await apiCall('/notifications/unread');
        if (result.status === 'success') {
            let unreadData = result.data;
            let count = 0;
            
            if (Array.isArray(unreadData)) {
                count = unreadData.length;
            } else if (unreadData && unreadData.data) {
                count = unreadData.data.length;
            } else if (unreadData && typeof unreadData === 'object' && 'count' in unreadData) {
                count = unreadData.count;
            }
            
            // Mettre à jour le badge
            if (count > 0) {
                updateNotificationBadge(count);
                
                // Si la page est cachée, afficher une notification toast
                if (document.hidden) {
                    const latest = Array.isArray(unreadData) ? unreadData[0] : 
                                   (unreadData.data ? unreadData.data[0] : null);
                    if (latest) {
                        const title = latest.data?.title || latest.title || 'Nouvelle notification';
                        const message = latest.data?.message || latest.message || '';
                        showToast(`${title}: ${message}`, 'info');
                    } else {
                        showToast(`Vous avez ${count} nouvelle(s) notification(s)`, 'info');
                    }
                }
                
                // Recharger la liste si on est sur la page
                if (document.getElementById('notificationsList') && !document.hidden) {
                    loadNotifications(state.currentPage, state.currentFilter);
                }
            } else {
                // Pas de nouvelles notifications
                if (document.hidden) {
                    // Ne rien faire
                }
            }
        }
    } catch (error) {
        // Silencieux
        logError('Erreur vérification nouvelles notifications', error);
    }
}

// ============================================================
// POLLING - RÉCUPÉRATION EN TEMPS RÉEL
// ============================================================

function startAllPolling() {
    // Polling pour les nouvelles notifications (15 secondes)
    if (state.notificationPollingInterval) {
        clearInterval(state.notificationPollingInterval);
    }
    state.notificationPollingInterval = setInterval(() => {
        if (!document.hidden) {
            checkNewNotifications();
        }
    }, 15000);

    // Polling pour rafraîchir la liste (30 secondes)
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
// SERVICE WORKER POUR NOTIFICATIONS PUSH
// ============================================================

function registerServiceWorker() {
    if (!('serviceWorker' in navigator)) {
        log('⚠️ Service Workers non supportés');
        return;
    }

    navigator.serviceWorker.register('/sw.js')
        .then(registration => {
            log('✅ Service Worker enregistré avec succès', registration);
            
            // Vérifier la permission des notifications
            if ('Notification' in window && Notification.permission === 'granted') {
                subscribeToPushNotifications();
            }
        })
        .catch(error => {
            logError('❌ Erreur enregistrement Service Worker', error);
        });
}

function requestNotificationPermission() {
    if (!('Notification' in window)) {
        log('⚠️ Les notifications ne sont pas supportées');
        return;
    }
    
    if (Notification.permission === 'granted') {
        log('✅ Permission notifications déjà accordée');
        subscribeToPushNotifications();
        return;
    }
    
    if (Notification.permission === 'denied') {
        log('⚠️ Permission notifications refusée');
        showToast('Veuillez autoriser les notifications dans les paramètres de votre navigateur', 'warning');
        return;
    }
    
    // Demander la permission
    Notification.requestPermission().then(permission => {
        if (permission === 'granted') {
            log('✅ Permission notifications accordée');
            showToast('Notifications activées !', 'success');
            subscribeToPushNotifications();
        } else {
            log('⚠️ Permission notifications refusée');
            showToast('Pour recevoir les notifications, veuillez autoriser les notifications.', 'warning');
        }
    });
}

function subscribeToPushNotifications() {
    if (!('serviceWorker' in navigator)) return;
    if (!('PushManager' in window)) return;
    
    navigator.serviceWorker.ready.then(registration => {
        // Vérifier si déjà abonné
        registration.pushManager.getSubscription().then(subscription => {
            if (subscription) {
                log('✅ Déjà abonné aux notifications push', subscription);
                return;
            }
            
            // S'abonner
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
                // Envoyer la subscription au serveur
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
    
    // Vérifier l'authentification
    if (!CONFIG.TOKEN) {
        // Essayer de récupérer depuis le localStorage
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
    
    // Charger les notifications
    loadNotifications(1, 'all');
    
    // Configurer les filtres
    setupFilters();
    
    // Événement "Tout marquer comme lu"
    const markAllBtn = document.getElementById('markAllReadBtn');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', markAllAsRead);
    }
    
    // Événement "Paramètres"
    const settingsBtn = document.getElementById('notificationSettingsBtn');
    if (settingsBtn) {
        settingsBtn.addEventListener('click', function() {
            showToast('Paramètres des notifications (bientôt disponible)', 'info');
        });
    }
    
    // Pagination
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
    
    // Démarrer le polling
    startAllPolling();
    
    // Enregistrer le service worker pour les notifications push
    registerServiceWorker();
    
    // Gestion de la visibilité de la page
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            stopAllPolling();
        } else {
            startAllPolling();
            // Rafraîchir immédiatement
            checkNewNotifications();
            loadNotifications(state.currentPage, state.currentFilter);
        }
    });
    
    // Nettoyage avant de quitter
    window.addEventListener('beforeunload', function() {
        stopAllPolling();
    });
    
    // Gestion des erreurs réseau
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
</script>
@endpush
@endsection
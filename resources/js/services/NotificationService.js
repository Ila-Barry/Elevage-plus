// resources/js/services/NotificationService.js

/**
 * Service NotificationService
 * 
 * Gère les notifications en temps réel et les WebSockets
 */
class NotificationService {
    constructor() {
        this.echo = null;
        this.userId = null;
        this.listeners = [];
    }

    /**
     * Initialise le service avec Echo (Laravel WebSocket)
     */
    init(userId) {
        this.userId = userId;
        
        // Importer Echo dynamiquement (si disponible)
        if (typeof window.Echo !== 'undefined') {
            this.echo = window.Echo;
            this.setupListeners();
        } else {
            // Fallback: polling toutes les 30 secondes
            this.setupPolling();
        }
    }

    /**
     * Configure les écouteurs WebSocket
     */
    setupListeners() {
        if (!this.echo || !this.userId) return;

        // Écouter les notifications privées
        this.echo.private(`App.Models.User.${this.userId}`)
            .notification((notification) => {
                this.handleNotification(notification);
            });

        // Écouter les notifications de nouveau message
        this.echo.private(`App.Models.User.${this.userId}`)
            .listen('MessageSent', (event) => {
                this.handleNotification({
                    type: 'message',
                    title: '💬 Nouveau message',
                    message: event.message,
                    data: event
                });
            });
    }

    /**
     * Fallback: polling pour les notifications
     */
    setupPolling() {
        setInterval(() => {
            if (this.userId) {
                this.fetchUnreadNotifications();
            }
        }, 30000);
    }

    /**
     * Récupère les notifications non lues
     */
    async fetchUnreadNotifications() {
        try {
            const response = await fetch('/api/notifications/unread', {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('access_token')}`,
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.data) {
                    data.data.forEach(notification => {
                        this.handleNotification(notification);
                    });
                }
            }
        } catch (error) {
            console.error('Erreur lors de la récupération des notifications:', error);
        }
    }

    /**
     * Gère une notification entrante
     */
    handleNotification(notification) {
        // Afficher un toast
        this.showToast(notification);

        // Mettre à jour le compteur de notifications
        this.updateCounter();

        // Notifier les écouteurs
        this.listeners.forEach(listener => {
            try {
                listener(notification);
            } catch (e) {
                console.error('Erreur dans un écouteur de notification:', e);
            }
        });
    }

    /**
     * Affiche un toast de notification
     */
    showToast(notification) {
        // Vérifier si la notification doit être affichée (préférences utilisateur)
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        if (!user.web_notifications && notification.type !== 'critical') {
            return;
        }

        // Utiliser l'API Notification du navigateur
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification(notification.title || 'Élevage+', {
                body: notification.message || notification.body || '',
                icon: notification.icon || '/images/icon-192x192.png',
                vibrate: [200, 100, 200],
                data: notification.data || {}
            });
        } else {
            // Fallback: toast HTML
            this.showHTMLToast(notification);
        }
    }

    /**
     * Affiche un toast HTML
     */
    showHTMLToast(notification) {
        const existingToast = document.querySelector('.custom-toast');
        if (existingToast) existingToast.remove();

        const toast = document.createElement('div');
        toast.className = `custom-toast ${notification.type || 'info'}`;
        
        let icon = 'fa-info-circle';
        if (notification.type === 'success') icon = 'fa-check-circle';
        else if (notification.type === 'danger') icon = 'fa-exclamation-circle';
        else if (notification.type === 'warning') icon = 'fa-exclamation-triangle';
        
        toast.innerHTML = `
            <div class="toast-content">
                <i class="fas ${icon}"></i>
                <div>
                    <strong>${notification.title || 'Notification'}</strong>
                    <p>${notification.message || notification.body || ''}</p>
                </div>
                <button class="toast-close"><i class="fas fa-times"></i></button>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Animation
        setTimeout(() => toast.classList.add('show'), 10);
        
        // Fermeture
        toast.querySelector('.toast-close').addEventListener('click', () => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        });
        
        // Auto-fermeture
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }

    /**
     * Met à jour le compteur de notifications
     */
    async updateCounter() {
        try {
            const response = await fetch('/api/notifications/unread', {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('access_token')}`,
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                const count = data.count || 0;
                
                // Mettre à jour le badge
                const badge = document.querySelector('.notification-badge');
                if (badge) {
                    if (count > 0) {
                        badge.textContent = count > 99 ? '99+' : count;
                        badge.style.display = 'flex';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            }
        } catch (error) {
            console.error('Erreur lors de la mise à jour du compteur:', error);
        }
    }

    /**
     * Ajoute un écouteur de notifications
     */
    onNotification(callback) {
        if (typeof callback === 'function') {
            this.listeners.push(callback);
        }
    }

    /**
     * Supprime un écouteur
     */
    offNotification(callback) {
        const index = this.listeners.indexOf(callback);
        if (index > -1) {
            this.listeners.splice(index, 1);
        }
    }

    /**
     * Demande la permission de notification
     */
    async requestPermission() {
        if (!('Notification' in window)) {
            return { granted: false, message: 'Notifications non supportées' };
        }

        if (Notification.permission === 'granted') {
            return { granted: true };
        }

        if (Notification.permission === 'denied') {
            return { granted: false, message: 'Permissions refusées' };
        }

        try {
            const permission = await Notification.requestPermission();
            return { granted: permission === 'granted' };
        } catch (error) {
            return { granted: false, message: error.message };
        }
    }
}

export default new NotificationService();
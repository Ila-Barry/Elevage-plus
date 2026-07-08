// public/sw.js
// Service Worker pour les notifications push

const CACHE_NAME = 'elevage-plus-v1';
const urlsToCache = [
    '/',
    '/css/app.css',
    '/js/app.js',
    '/images/logo-elevage-plus.png',
    '/images/badge.png',
    '/favicon.ico',
    '/offline.html'
];

// ============================================
// INSTALLATION
// ============================================
self.addEventListener('install', function(event) {
    console.log('🔧 Service Worker installation...');
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(function(cache) {
                console.log('📦 Cache ouvert');
                return cache.addAll(urlsToCache).catch(function(error) {
                    console.warn('⚠️ Certaines ressources n\'ont pas pu être mises en cache:', error);
                });
            })
            .then(function() {
                console.log('✅ Service Worker installé avec succès');
                return self.skipWaiting();
            })
    );
});

// ============================================
// ACTIVATION
// ============================================
self.addEventListener('activate', function(event) {
    console.log('🚀 Service Worker activation...');
    event.waitUntil(
        caches.keys().then(function(cacheNames) {
            return Promise.all(
                cacheNames.map(function(cacheName) {
                    if (cacheName !== CACHE_NAME) {
                        console.log('🗑️ Suppression du cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
        .then(function() {
            console.log('✅ Service Worker activé avec succès');
            return self.clients.claim();
        })
    );
});

// ============================================
// INTERCEPTION DES REQUÊTES
// ============================================
self.addEventListener('fetch', function(event) {
    event.respondWith(
        caches.match(event.request)
            .then(function(response) {
                if (response) {
                    return response;
                }
                return fetch(event.request).catch(function() {
                    return caches.match('/offline.html');
                });
            })
    );
});

// ============================================
// GESTION DES NOTIFICATIONS PUSH
// ============================================
self.addEventListener('push', function(event) {
    console.log('📨 Notification push reçue:', event);
    
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        console.warn('⚠️ Permission de notification non accordée');
        return;
    }

    let data = {};
    let title = '📩 Nouveau message';
    let options = {
        body: 'Vous avez reçu un nouveau message',
        icon: '/images/logo-elevage-plus.png',
        badge: '/images/badge.png',
        data: {
            url: '/messagerie'
        },
        actions: [
            { action: 'view', title: '📖 Voir le message' },
            { action: 'close', title: '❌ Fermer' }
        ],
        vibrate: [200, 100, 200, 100, 200],
        tag: 'message-notification-' + Date.now(),
        renotify: true,
        requireInteraction: true,
        silent: false,
        timestamp: Date.now()
    };
    
    // Extraire les données du push
    if (event.data) {
        try {
            data = event.data.json();
            console.log('📦 Données push:', data);
            
            // Titre personnalisé pour les messages
            if (data.expediteur_nom) {
                title = `📩 Nouveau message de ${data.expediteur_nom}`;
            } else if (data.title) {
                title = data.title;
            }
            
            options.body = data.body || data.message || 'Vous avez reçu un nouveau message';
            options.icon = data.icon || '/images/logo-elevage-plus.png';
            options.badge = data.badge || '/images/badge.png';
            options.data = {
                url: data.url || '/messagerie',
                message_id: data.message_id,
                conversation_id: data.conversation_id,
                expediteur_id: data.expediteur_id
            };
            
            // Si c'est une notification de message, ajouter des actions spécifiques
            if (data.type === 'message') {
                options.actions = [
                    { action: 'view', title: '📖 Répondre' },
                    { action: 'close', title: '❌ Fermer' }
                ];
            }
            
        } catch (e) {
            console.warn('⚠️ Erreur parsing des données push:', e);
            // Garder les valeurs par défaut
        }
    }

    console.log('📤 Affichage notification:', title, options);
    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

// ============================================
// GESTION DU CLIC SUR LES NOTIFICATIONS
// ============================================
self.addEventListener('notificationclick', function(event) {
    console.log('🖱️ Clic sur notification:', event.action, event.notification.data);
    
    // Fermer la notification
    event.notification.close();
    
    const data = event.notification.data || {};
    const url = data.url || '/messagerie';
    const action = event.action;
    
    // Actions spécifiques
    if (action === 'view' || action === 'view_message') {
        // Ouvrir la messagerie
        event.waitUntil(
            clients.openWindow(url)
        );
        return;
    }
    
    if (action === 'view_elevage' && data.elevage_id) {
        const elevageUrl = `/elevages/${data.elevage_id}`;
        event.waitUntil(
            clients.openWindow(elevageUrl)
        );
        return;
    }
    
    if (action === 'close') {
        // Ne rien faire, juste fermer la notification
        return;
    }
    
    // Par défaut, ouvrir l'URL
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then(function(windowClients) {
                // Vérifier si une fenêtre est déjà ouverte sur cette URL
                for (let client of windowClients) {
                    if (client.url === url && 'focus' in client) {
                        return client.focus();
                    }
                }
                // Sinon, ouvrir une nouvelle fenêtre
                if (clients.openWindow) {
                    return clients.openWindow(url);
                }
            })
    );
});

// ============================================
// GESTION DES CHANGEMENTS D'ABONNEMENT PUSH
// ============================================
self.addEventListener('pushsubscriptionchange', function(event) {
    console.log('🔄 Changement d\'abonnement push');
    
    event.waitUntil(
        // Récupérer le nouvel abonnement
        self.registration.pushManager.getSubscription()
            .then(function(subscription) {
                console.log('📤 Nouvel abonnement:', subscription);
                
                // Envoyer le nouvel abonnement au serveur
                return fetch('/api/webpush/update-subscription', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        subscription: subscription,
                        old_endpoint: event.oldSubscription?.endpoint
                    })
                });
            })
            .then(function(response) {
                if (response.ok) {
                    console.log('✅ Abonnement mis à jour sur le serveur');
                } else {
                    console.warn('⚠️ Échec mise à jour abonnement sur le serveur');
                }
                return response;
            })
            .catch(function(error) {
                console.error('❌ Erreur mise à jour abonnement:', error);
            })
    );
});

// ============================================
// GESTION DES MESSAGES DU CLIENT
// ============================================
self.addEventListener('message', function(event) {
    console.log('📨 Message reçu du client:', event.data);
    
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

// ============================================
// GESTION DES ERREURS
// ============================================
self.addEventListener('error', function(event) {
    console.error('❌ Erreur dans le Service Worker:', event.error);
});

// ============================================
// LOGS D'ACTIVITÉ
// ============================================
console.log('✅ Service Worker chargé avec succès');
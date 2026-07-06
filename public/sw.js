// public/sw.js
// Service Worker pour les notifications push

self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open('elevage-plus-v1').then(function(cache) {
            return cache.addAll([
                '/',
                '/css/app.css',
                '/js/app.js',
                '/images/logo-elevage-plus.png',
                '/images/badge.png'
            ]);
        })
    );
    self.skipWaiting();
});

self.addEventListener('activate', function(event) {
    event.waitUntil(clients.claim());
});

// Gestion des notifications push
self.addEventListener('push', function(event) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        return;
    }

    let data = {};
    
    if (event.data) {
        try {
            data = event.data.json();
        } catch (e) {
            data = {
                title: 'Élevage+',
                body: event.data.text(),
                icon: '/images/logo-elevage-plus.png',
                badge: '/images/badge.png'
            };
        }
    }

    const options = {
        body: data.body || 'Nouvelle notification',
        icon: data.icon || '/images/logo-elevage-plus.png',
        badge: data.badge || '/images/badge.png',
        data: data.data || {},
        actions: data.actions || [],
        vibrate: [200, 100, 200],
        tag: data.tag || 'elevage-notification',
        renotify: true,
        requireInteraction: true,
        silent: false,
    };

    event.waitUntil(
        self.registration.showNotification(data.title || 'Élevage+', options)
    );
});

// Gestion du clic sur la notification
self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    const url = event.notification.data?.url || '/notifications';
    const action = event.action;

    // Si l'action est "view_elevage", ouvrir la page de l'élevage
    if (action === 'view_elevage' && event.notification.data?.elevage_id) {
        const elevageUrl = `/elevages/${event.notification.data.elevage_id}`;
        event.waitUntil(
            clients.openWindow(elevageUrl)
        );
        return;
    }

    // Sinon, ouvrir l'URL par défaut
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then(windowClients => {
                for (let client of windowClients) {
                    if (client.url === url && 'focus' in client) {
                        return client.focus();
                    }
                }
                if (clients.openWindow) {
                    return clients.openWindow(url);
                }
            })
    );
});

// Gestion des erreurs de push
self.addEventListener('pushsubscriptionchange', function(event) {
    // Mettre à jour la subscription
    event.waitUntil(
        fetch('/api/webpush/update-subscription', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
                subscription: event.newSubscription
            })
        })
    );
});
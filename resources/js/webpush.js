// resources/js/webpush.js

export const initWebPush = async (userId) => {
    // Vérifier si le navigateur supporte les notifications
    if (!('Notification' in window)) {
        console.log('Ce navigateur ne supporte pas les notifications');
        return;
    }

    // Vérifier si le service worker est supporté
    if (!('serviceWorker' in navigator)) {
        console.log('Service Worker non supporté');
        return;
    }

    // Si permission non accordée, la demander
    if (Notification.permission === 'default') {
        const permission = await Notification.requestPermission();
        if (permission === 'granted') {
            await subscribeToPush(userId);
        }
    } else if (Notification.permission === 'granted') {
        await subscribeToPush(userId);
    }
};

const subscribeToPush = async (userId) => {
    try {
        const registration = await navigator.serviceWorker.register('/sw.js');
        await navigator.serviceWorker.ready;
        
        const response = await fetch('/api/vapid-public-key');
        const { publicKey } = await response.json();
        
        const subscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(publicKey)
        });
        
        await fetch('/api/webpush/subscribe', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: userId, subscription })
        });
        
        console.log('Abonnement push réussi');
    } catch (error) {
        console.error('Erreur push:', error);
    }
};
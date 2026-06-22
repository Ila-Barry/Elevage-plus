// resources/js/services/messaging.js

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

class MessagingService {
    constructor() {
        this.echo = null;
        this.initEcho();
    }

    initEcho() {
        window.Pusher = Pusher;
        
        this.echo = new Echo({
            broadcaster: 'pusher',
            key: process.env.MIX_PUSHER_APP_KEY,
            cluster: process.env.MIX_PUSHER_APP_CLUSTER,
            forceTLS: true,
            authEndpoint: '/broadcasting/auth',
        });
    }

    /**
     * Écouter les nouveaux messages dans une conversation
     */
    listenToConversation(conversationId, callback) {
        if (!this.echo) return null;

        return this.echo.join(`conversation.${conversationId}`)
            .listen('message.sent', (data) => {
                callback(data);
            });
    }

    /**
     * Écouter les messages marqués comme lus
     */
    listenToReadReceipts(conversationId, callback) {
        if (!this.echo) return null;

        return this.echo.join(`conversation.${conversationId}`)
            .listen('message.read', (data) => {
                callback(data);
            });
    }

    /**
     * Quitter un canal
     */
    leaveConversation(conversationId) {
        if (this.echo) {
            this.echo.leave(`conversation.${conversationId}`);
        }
    }

    /**
     * Envoyer un message
     */
    async sendMessage(destinataireId, contenu) {
        const response = await axios.post('/api/messaging/send', {
            destinataire_id: destinataireId,
            contenu: contenu
        });
        
        return response.data;
    }

    /**
     * Récupérer les conversations
     */
    async getConversations(page = 1, perPage = 20) {
        const response = await axios.get('/api/messaging/conversations', {
            params: { page, per_page: perPage }
        });
        
        return response.data;
    }

    /**
     * Récupérer les messages d'une conversation
     */
    async getMessages(conversationId, page = 1, perPage = 50) {
        const response = await axios.get(`/api/messaging/conversations/${conversationId}/messages`, {
            params: { page, per_page: perPage }
        });
        
        return response.data;
    }

    /**
     * Marquer une conversation comme lue
     */
    async markAsRead(conversationId) {
        const response = await axios.post(`/api/messaging/conversations/${conversationId}/read`);
        
        return response.data;
    }

    /**
     * Supprimer un message
     */
    async deleteMessage(messageId) {
        const response = await axios.delete(`/api/messaging/messages/${messageId}`);
        
        return response.data;
    }

    /**
     * Récupérer le nombre de messages non lus
     */
    async getUnreadCount() {
        const response = await axios.get('/api/messaging/unread-count');
        
        return response.data;
    }
}

export default new MessagingService();
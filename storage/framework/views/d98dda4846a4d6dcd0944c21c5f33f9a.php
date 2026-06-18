<?php $__env->startSection('title', 'Messagerie'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/eleveurCSS/messages.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<div class="messagerie-container">
    
    <!-- En-tête de la messagerie -->
    <div class="messagerie-header">
        <h1 class="messagerie-title">
            <i class="fas fa-envelope mr-2 text-success"></i> MESSAGERIE
        </h1>
        <div class="search-bar">
            <i class="fas fa-search search-icon"></i>
            <input type="text" placeholder="rechercher un éleveur..." class="search-input" id="searchInput">
        </div>
    </div>

    <!-- Corps principal : Conversations + Discussion -->
    <div class="messagerie-body">
        
        <!-- COLONNE GAUCHE : Liste des conversations -->
        <div class="conversations-list">
            <div class="conversations-header">
                <h3><i class="fas fa-comments mr-2"></i> CONVERSATIONS</h3>
            </div>
            
            <div class="conversations-scroll" id="conversationsScroll">
                <!-- Les conversations seront générées par JavaScript -->
            </div>

            <div class="app-banner-buttons" style="padding: 15px;">
                <button class="btn-new-conversation" id="newConversationBtn">
                    <i class="fas fa-plus mr-2"></i> Nouvelle conversation
                </button>
            </div>
        </div>

        <!-- Bouton Nouvelle conversation (mobile) -->
        <button class="btn-new-conversation-mobile" id="newConversationMobileBtn">
            <i class="fas fa-plus"></i>
            <span>Nouvelle conversation</span>
        </button>

        <!-- COLONNE DROITE : Zone de discussion -->
        <div class="discussion-area" id="discussionArea">
            
            <!-- En-tête de la discussion -->
            <div class="discussion-header">
                <div class="discussion-contact">
                    <div class="discussion-avatar">
                        <img src="https://ui-avatars.com/api/?name=Amadou+Sy&background=198754&color=fff&rounded=true&size=45" alt="Avatar" id="discussionAvatar">
                        <span class="online-dot large"></span>
                    </div>
                    <div class="discussion-info">
                        <h3 id="discussionName">Avec: Amadou Sy</h3>
                        <span class="online-status" id="discussionStatus"><i class="fas fa-circle"></i> En ligne</span>
                    </div>
                </div>
                <div class="discussion-actions">
                    <button class="action-btn" title="Appeler" id="callBtn">
                        <i class="fas fa-phone"></i>
                    </button>
                    <button class="action-btn" title="Vidéo" id="videoBtn">
                        <i class="fas fa-video"></i>
                    </button>
                    <button class="action-btn" title="Plus" id="moreActionsBtn">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                </div>
            </div>

            <!-- Messages de la discussion -->
            <div class="messages-container">
                <div class="messages-scroll" id="messagesScroll">
                    <!-- Les messages seront générés par JavaScript -->
                </div>
            </div>

            <!-- Zone d'envoi de message -->
            <div class="message-input-area">
                <div class="input-actions">
                    <button class="input-action-btn" title="Joindre un fichier" id="attachBtn">
                        <i class="fas fa-paperclip"></i>
                    </button>
                    <button class="input-action-btn" title="Émojis" id="emojiBtn">
                        <i class="fas fa-smile-wink"></i>
                    </button>
                    <button class="input-action-btn" title="Image" id="imageBtn">
                        <i class="fas fa-image"></i>
                    </button>
                </div>
                <div class="message-input-wrapper">
                    <input type="text" placeholder="Écrire un message..." class="message-input" id="messageInput">
                    <button class="send-btn" id="sendBtn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>

        </div>
        
    </div>
</div>

<script>
// ================= DONNÉES =================
const conversationsData = [
    {
        id: 1,
        name: "AMADOU SY",
        preview: "Bonjour, as-tu...",
        lastMessage: "Bonjour, as-tu des nouvelles de la fièvre aphteuse ?",
        time: "10:30",
        date: "10:30",
        isRead: true,
        isOnline: true,
        avatar: "https://ui-avatars.com/api/?name=Amadou+Sy&background=198754&color=fff&rounded=true&size=45",
        messages: [
            { id: 1, sender: 'me', text: 'Bonjour Amadou, as-tu des nouvelles de la fièvre aphteuse ?', time: '10:30', read: true },
            { id: 2, sender: 'other', text: 'Oui, 2 nouveaux cas signalés hier à Rufisque', time: '10:32', read: true },
            { id: 3, sender: 'other', text: 'Je te conseille de vacciner tout le troupeau', time: '10:33', read: true },
            { id: 4, sender: 'me', text: 'Merci beaucoup pour l\'info ! Je vais prendre rdv avec le vétérinaire dès aujourd\'hui.', time: '10:40', read: true },
            { id: 5, sender: 'other', text: 'Parfait, tiens-moi au courant si tu as besoin d\'aide 👍', time: '10:42', read: true }
        ]
    },
    {
        id: 2,
        name: "FATOU DIOP",
        preview: "Merci pour les conseils...",
        lastMessage: "Merci pour les conseils sur l'alimentation des caprins",
        time: "hier",
        date: "2026-06-14",
        isRead: true,
        isOnline: true,
        avatar: "https://ui-avatars.com/api/?name=Fatou+Diop&background=198754&color=fff&rounded=true&size=45",
        messages: [
            { id: 1, sender: 'other', text: 'Bonjour, j\'ai vu votre publication sur l\'alimentation des caprins', time: '14:20', read: true },
            { id: 2, sender: 'me', text: 'Bonjour Fatou ! Oui, c\'est une méthode que j\'utilise depuis 2 ans', time: '14:25', read: true },
            { id: 3, sender: 'other', text: 'Merci pour les conseils sur l\'alimentation des caprins', time: '14:30', read: true },
            { id: 4, sender: 'me', text: 'Avec plaisir ! N\'hésitez pas si vous avez d\'autres questions', time: '14:35', read: true }
        ]
    },
    {
        id: 3,
        name: "MOUSSA DIALLO",
        preview: "J'ai reçu les vaccins...",
        lastMessage: "J'ai reçu les vaccins pour la fièvre aphteuse",
        time: "2 ma",
        date: "2026-06-12",
        isRead: false,
        isOnline: true,
        avatar: "https://ui-avatars.com/api/?name=Moussa+Diallo&background=198754&color=fff&rounded=true&size=45",
        messages: [
            { id: 1, sender: 'other', text: 'J\'ai reçu les vaccins pour la fièvre aphteuse', time: '09:00', read: false },
            { id: 2, sender: 'other', text: 'Est-ce que je peux commencer la vaccination dès maintenant ?', time: '09:05', read: false }
        ]
    },
    {
        id: 4,
        name: "AMINATA BA",
        preview: "Rendez-vous demain...",
        lastMessage: "Rendez-vous demain pour la visite du troupeau",
        time: "1 ja",
        date: "2026-06-11",
        isRead: true,
        isOnline: false,
        avatar: "https://ui-avatars.com/api/?name=Aminata+Ba&background=198754&color=fff&rounded=true&size=45",
        messages: [
            { id: 1, sender: 'me', text: 'Bonjour Aminata, je confirme la visite de demain', time: '16:00', read: true },
            { id: 2, sender: 'other', text: 'Rendez-vous demain pour la visite du troupeau', time: '16:15', read: true },
            { id: 3, sender: 'me', text: 'Parfait, à demain 9h', time: '16:20', read: true }
        ]
    },
    {
        id: 5,
        name: "IBRAHIMA FALL",
        preview: "Nouveau fournisseur...",
        lastMessage: "Je connais un nouveau fournisseur d'aliments",
        time: "3 ja",
        date: "2026-06-09",
        isRead: true,
        isOnline: false,
        avatar: "https://ui-avatars.com/api/?name=Ibrahima+Fall&background=198754&color=fff&rounded=true&size=45",
        messages: [
            { id: 1, sender: 'other', text: 'Je connais un nouveau fournisseur d\'aliments de qualité', time: '11:00', read: true },
            { id: 2, sender: 'me', text: 'Intéressant ! Peux-tu me donner son contact ?', time: '11:15', read: true }
        ]
    }
];

// Variables d'état
let currentConversationId = 1;
let toastTimeout = null;
let typingTimeout = null;

// ================= FONCTIONS TOAST =================
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

// ================= RENDU DES CONVERSATIONS =================
function renderConversations() {
    const container = document.getElementById('conversationsScroll');
    
    container.innerHTML = conversationsData.map(conv => `
        <div class="conversation-item ${conv.id === currentConversationId ? 'active' : ''}" 
             data-id="${conv.id}" onclick="selectConversation(${conv.id})">
            <div class="conversation-avatar">
                <img src="${conv.avatar}" alt="${conv.name}">
                ${conv.isOnline ? '<span class="online-dot"></span>' : ''}
            </div>
            <div class="conversation-info">
                <div class="conversation-name">${conv.name}</div>
                <div class="conversation-preview">${conv.preview}</div>
            </div>
            <div class="conversation-time">
                <span>${conv.time}</span>
                ${!conv.isRead ? '<i class="fas fa-circle" style="color: #198754; font-size: 10px; margin-top: 4px; display: block;"></i>' : ''}
                ${conv.isRead ? '<i class="fas fa-check-double read-marker"></i>' : ''}
            </div>
        </div>
    `).join('');
}

// ================= SÉLECTION D'UNE CONVERSATION =================
function selectConversation(id) {
    currentConversationId = id;
    const conversation = conversationsData.find(c => c.id === id);
    if (!conversation) return;
    
    // Mettre à jour l'UI
    renderConversations();
    renderMessages(id);
    updateDiscussionHeader(id);
    
    // Marquer comme lu
    conversation.isRead = true;
    
    // Scroll en bas des messages
    scrollToBottom();
    
    // Animation
    const container = document.getElementById('messagesScroll');
    container.style.opacity = '0';
    setTimeout(() => {
        container.style.opacity = '1';
    }, 150);
}

// ================= MISE À JOUR DE L'EN-TÊTE =================
function updateDiscussionHeader(id) {
    const conversation = conversationsData.find(c => c.id === id);
    if (!conversation) return;
    
    document.getElementById('discussionName').textContent = `Avec: ${conversation.name}`;
    document.getElementById('discussionAvatar').src = conversation.avatar;
    
    const statusEl = document.getElementById('discussionStatus');
    if (conversation.isOnline) {
        statusEl.innerHTML = '<i class="fas fa-circle" style="color: #22c55e;"></i> En ligne';
        statusEl.style.color = '#22c55e';
    } else {
        statusEl.innerHTML = '<i class="fas fa-circle" style="color: #9ca3af;"></i> Hors ligne';
        statusEl.style.color = '#9ca3af';
    }
}

// ================= RENDU DES MESSAGES =================
function renderMessages(id) {
    const container = document.getElementById('messagesScroll');
    const conversation = conversationsData.find(c => c.id === id);
    if (!conversation) return;
    
    if (conversation.messages.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 40px 20px; color: #9ca3af;">
                <i class="fas fa-comment" style="font-size: 32px; display: block; margin-bottom: 10px;"></i>
                <p>Aucun message pour le moment</p>
                <p style="font-size: 0.8rem;">Commencez la conversation !</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = conversation.messages.map(msg => `
        <div class="message ${msg.sender === 'me' ? 'message-sent' : 'message-received'}">
            <div class="message-bubble">
                <p>${escapeHtml(msg.text)}</p>
                <span class="message-time">
                    ${msg.time}
                    ${msg.sender === 'me' ? `<i class="fas fa-check-double ${msg.read ? 'read' : ''}"></i>` : ''}
                </span>
            </div>
        </div>
    `).join('');
}

// ================= ENVOI DE MESSAGE =================
function sendMessage() {
    const input = document.getElementById('messageInput');
    const text = input.value.trim();
    
    if (text === '') {
        showToast('Veuillez écrire un message', 'warning');
        return;
    }
    
    const conversation = conversationsData.find(c => c.id === currentConversationId);
    if (!conversation) {
        showToast('Veuillez sélectionner une conversation', 'warning');
        return;
    }
    
    const now = new Date();
    const time = now.getHours() + ':' + (now.getMinutes() < 10 ? '0' : '') + now.getMinutes();
    
    // Ajouter le message
    const newMessage = {
        id: conversation.messages.length + 1,
        sender: 'me',
        text: text,
        time: time,
        read: true
    };
    
    conversation.messages.push(newMessage);
    conversation.preview = text.substring(0, 30) + (text.length > 30 ? '...' : '');
    conversation.time = time;
    conversation.isRead = true;
    conversation.lastMessage = text;
    
    // Mettre à jour l'UI
    renderMessages(currentConversationId);
    renderConversations();
    input.value = '';
    scrollToBottom();
    showToast('Message envoyé', 'success');
    
    // Simuler une réponse (après 1-3 secondes)
    simulateReply(currentConversationId);
}

// ================= SIMULATION DE RÉPONSE =================
function simulateReply(convId) {
    const replies = [
        "Merci pour votre message, je vous réponds dès que possible.",
        "Très intéressant, pouvez-vous m'en dire plus ?",
        "Je suis d'accord avec vous, c'est une bonne approche.",
        "Je vais vérifier cela et je vous tiens au courant.",
        "Merci pour ces informations, très utiles !",
        "Parfait, je suis disponible pour en discuter.",
        "Je comprends parfaitement votre situation.",
        "Je vais contacter le vétérinaire et vous reviens."
    ];
    
    const randomDelay = Math.floor(Math.random() * 2000) + 1000; // 1-3 secondes
    
    setTimeout(() => {
        const conversation = conversationsData.find(c => c.id === convId);
        if (!conversation) return;
        
        const replyText = replies[Math.floor(Math.random() * replies.length)];
        const now = new Date();
        const time = now.getHours() + ':' + (now.getMinutes() < 10 ? '0' : '') + now.getMinutes();
        
        const replyMessage = {
            id: conversation.messages.length + 1,
            sender: 'other',
            text: replyText,
            time: time,
            read: true
        };
        
        conversation.messages.push(replyMessage);
        conversation.preview = replyText.substring(0, 30) + (replyText.length > 30 ? '...' : '');
        conversation.time = time;
        conversation.isRead = false;
        conversation.lastMessage = replyText;
        
        renderMessages(currentConversationId);
        renderConversations();
        scrollToBottom();
        
        // Notification
        if (document.hidden) {
            document.title = `📩 Nouveau message de ${conversation.name}`;
            setTimeout(() => {
                document.title = 'Messagerie - Élevage+';
            }, 5000);
        }
        
        showToast(`📩 Nouveau message de ${conversation.name}`, 'info');
    }, randomDelay);
}

// ================= SCROLL VERS LE BAS =================
function scrollToBottom() {
    const container = document.getElementById('messagesScroll');
    setTimeout(() => {
        container.scrollTop = container.scrollHeight;
    }, 50);
}

// ================= FONCTION D'ÉCHAPPEMENT HTML =================
function escapeHtml(text) {
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// ================= RECHERCHE =================
function searchConversations(query) {
    const items = document.querySelectorAll('.conversation-item');
    const term = query.toLowerCase().trim();
    
    let found = false;
    items.forEach(item => {
        const name = item.querySelector('.conversation-name').textContent.toLowerCase();
        const preview = item.querySelector('.conversation-preview').textContent.toLowerCase();
        
        if (name.includes(term) || preview.includes(term)) {
            item.style.display = 'flex';
            found = true;
        } else {
            item.style.display = 'none';
        }
    });
    
    if (!found && term !== '') {
        // Afficher un message "Aucun résultat"
        const container = document.getElementById('conversationsScroll');
        const noResult = container.querySelector('.no-result-message');
        if (!noResult) {
            const div = document.createElement('div');
            div.className = 'no-result-message';
            div.style.cssText = 'text-align: center; padding: 30px 20px; color: #9ca3af;';
            div.innerHTML = `
                <i class="fas fa-search" style="font-size: 24px; display: block; margin-bottom: 10px;"></i>
                <p>Aucune conversation trouvée pour "${term}"</p>
            `;
            container.appendChild(div);
        }
    } else {
        const noResult = container.querySelector('.no-result-message');
        if (noResult) noResult.remove();
    }
}

// ================= CRÉER UNE NOUVELLE CONVERSATION =================
function openNewConversation() {
    if (!name || name.trim() === '') return;
    
    // Vérifier si la conversation existe déjà
    const existing = conversationsData.find(c => c.name.toLowerCase() === name.trim().toUpperCase());
    if (existing) {
        showToast(`Vous avez déjà une conversation avec ${name}`, 'warning');
        selectConversation(existing.id);
        return;
    }
    
    const newConversation = {
        id: Math.max(...conversationsData.map(c => c.id)) + 1,
        name: name.trim().toUpperCase(),
        preview: "Démarrer une conversation...",
        lastMessage: "",
        time: "maintenant",
        date: new Date().toLocaleDateString('fr-FR'),
        isRead: true,
        isOnline: false,
        avatar: `https://ui-avatars.com/api/?name=${encodeURIComponent(name.trim())}&background=198754&color=fff&rounded=true&size=45`,
        messages: []
    };
    
    conversationsData.push(newConversation);
    renderConversations();
    selectConversation(newConversation.id);
    showToast(`Nouvelle conversation avec ${name} créée !`, 'success');
}

// ================= GESTION DES TYPIQUES =================
function handleTyping() {
    if (typingTimeout) clearTimeout(typingTimeout);
    
    // Afficher le statut "En train d'écrire..."
    const statusEl = document.getElementById('discussionStatus');
    statusEl.innerHTML = '<i class="fas fa-pencil-alt" style="color: #198754;"></i> En train d\'écrire...';
    statusEl.style.color = '#198754';
    
    typingTimeout = setTimeout(() => {
        const conversation = conversationsData.find(c => c.id === currentConversationId);
        if (conversation) {
            if (conversation.isOnline) {
                statusEl.innerHTML = '<i class="fas fa-circle" style="color: #22c55e;"></i> En ligne';
                statusEl.style.color = '#22c55e';
            } else {
                statusEl.innerHTML = '<i class="fas fa-circle" style="color: #9ca3af;"></i> Hors ligne';
                statusEl.style.color = '#9ca3af';
            }
        }
    }, 2000);
}

// ================= INITIALISATION =================
document.addEventListener('DOMContentLoaded', function() {
    // Afficher les conversations
    renderConversations();
    
    // Afficher les messages de la première conversation
    if (conversationsData.length > 0) {
        renderMessages(currentConversationId);
        updateDiscussionHeader(currentConversationId);
        scrollToBottom();
    }
    
    // Événement d'envoi de message
    document.getElementById('sendBtn').addEventListener('click', sendMessage);
    
    document.getElementById('messageInput').addEventListener('keypress', function(e) {
        if (e.which === 13 && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
    
    // Gestion du typing
    document.getElementById('messageInput').addEventListener('input', handleTyping);
    
    // Recherche
    document.getElementById('searchInput').addEventListener('input', function() {
        searchConversations(this.value);
    });
    
    // Nouvelle conversation
    document.getElementById('newConversationBtn').addEventListener('click', openNewConversation);
    document.getElementById('newConversationMobileBtn').addEventListener('click', openNewConversation);
    
    // Actions de la discussion
    document.getElementById('callBtn').addEventListener('click', function() {
        showToast('🔊 Appel en cours... (fonctionnalité à venir)', 'info');
    });
    
    document.getElementById('videoBtn').addEventListener('click', function() {
        showToast('📹 Appel vidéo en cours... (fonctionnalité à venir)', 'info');
    });
    
    document.getElementById('moreActionsBtn').addEventListener('click', function() {
        const actions = [
            '📎 Partager un fichier',
            '📍 Partager la position',
            '📅 Planifier un rendez-vous',
            '🔇 Mettre en sourdine',
            '🚫 Bloquer l\'utilisateur'
        ];
        const action = prompt('Choisissez une action :\n\n' + actions.map((a, i) => `${i+1}. ${a}`).join('\n'));
        if (action) {
            const index = parseInt(action) - 1;
            if (index >= 0 && index < actions.length) {
                showToast(`Action: ${actions[index]} (fonctionnalité à venir)`, 'info');
            }
        }
    });
    
    // Actions de l'input
    document.getElementById('attachBtn').addEventListener('click', function() {
        showToast('📎 Joindre un fichier (fonctionnalité à venir)', 'info');
    });
    
    document.getElementById('emojiBtn').addEventListener('click', function() {
        const emojis = ['👍', '❤️', '😊', '😂', '😍', '🤔', '🙏', '💪', '👋', '✨'];
        const randomEmoji = emojis[Math.floor(Math.random() * emojis.length)];
        document.getElementById('messageInput').value += randomEmoji;
        document.getElementById('messageInput').focus();
    });
    
    document.getElementById('imageBtn').addEventListener('click', function() {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';
        input.onchange = function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const conversation = conversationsData.find(c => c.id === currentConversationId);
                    if (conversation) {
                        const now = new Date();
                        const time = now.getHours() + ':' + (now.getMinutes() < 10 ? '0' : '') + now.getMinutes();
                        
                        conversation.messages.push({
                            id: conversation.messages.length + 1,
                            sender: 'me',
                            text: '📷 Image partagée',
                            time: time,
                            read: true,
                            image: event.target.result
                        });
                        
                        renderMessages(currentConversationId);
                        scrollToBottom();
                        showToast('📷 Image partagée', 'success');
                    }
                };
                reader.readAsDataURL(file);
            }
        };
        input.click();
    });
    
    // Ajouter les styles manquants
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
        
        #messagesScroll {
            transition: opacity 0.15s ease;
        }
        .no-result-message {
            text-align: center;
            padding: 30px 20px;
            color: #9ca3af;
        }
        @media (max-width: 768px) {
            .custom-toast {
                left: 15px;
                right: 15px;
                bottom: 15px;
                transform: translateY(100px);
            }
            .custom-toast.show { transform: translateY(0); }
        }
        .app-banner-buttons {
            padding: 15px;
            border-top: 1px solid #eef2f6;
        }
        .btn-new-conversation {
            width: 100%;
            background: #198754;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-new-conversation:hover {
            background: #146c43;
            transform: translateY(-2px);
        }
        .btn-new-conversation-mobile {
            display: none;
            margin: 15px;
            padding: 12px;
            background: #198754;
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-new-conversation-mobile:hover {
            background: #146c43;
        }
        @media (max-width: 768px) {
            .btn-new-conversation-mobile {
                display: block;
            }
            .app-banner-buttons {
                display: none;
            }
        }
        .message-bubble img {
            max-width: 100%;
            border-radius: 8px;
            margin-top: 5px;
        }
    `;
    document.head.appendChild(style);
});

// ================= GESTION DE L'ONGLET ACTIF =================
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        // L'utilisateur est revenu sur la page
        const conversation = conversationsData.find(c => c.id === currentConversationId);
        if (conversation) {
            conversation.isRead = true;
            renderConversations();
        }
        document.title = 'Messagerie - Élevage+';
    }
});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\USER\Desktop\Projet\Elevage-plus\resources\views/messages.blade.php ENDPATH**/ ?>
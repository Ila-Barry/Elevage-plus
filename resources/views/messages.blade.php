@extends('layouts.menu')

@section('title', 'Messagerie')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/eleveurCSS/messages.css') }}">
@endpush

@section('content')
<div class="messagerie-container">
    
    <div class="messagerie-header">
        <h1 class="messagerie-title">
            <i class="fas fa-envelope mr-2 text-success"></i> MESSAGERIE
            <span class="badge-unread" id="totalUnread">0</span>
        </h1>
        <div class="search-bar">
            <i class="fas fa-search search-icon"></i>
            <input type="text" placeholder="rechercher un éleveur..." class="search-input" id="searchContact">
        </div>
    </div>

    <div class="messagerie-body">
        
        <div class="conversations-list">
            <div class="conversations-header">
                <h3><i class="fas fa-comments mr-2"></i> CONVERSATIONS</h3>
                <span class="conversations-count" id="conversationsCount">0</span>
            </div>
            
            <div class="conversations-scroll" id="conversationsContainer">
                <div class="text-center py-4 text-muted">Chargement des conversations...</div>
            </div>
            
            <div class="app-banner-buttons mt-3">
                <button class="btn-new-conversation" id="btnNewConv">
                    <i class="fas fa-plus mr-2"></i> Nouvelle conversation
                </button>
            </div>
        </div>

        <div class="discussion-area" id="discussionArea" style="display: none;">
            <div class="discussion-header">
                <div class="discussion-contact">
                    <div class="discussion-avatar">
                        <img id="activeChatAvatar" src="" alt="Avatar">
                        <span class="online-dot large"></span>
                    </div>
                    <div class="discussion-info">
                        <h3 id="activeChatName">Sélectionnez un contact</h3>
                        <span class="online-status" id="activeChatStatus">
                            <i class="fas fa-circle"></i> En ligne
                        </span>
                    </div>
                </div>
                <div class="discussion-actions">
                    <button class="action-btn" title="Appeler" id="callBtn"><i class="fas fa-phone"></i></button>
                    <button class="action-btn" title="Vidéo" id="videoBtn"><i class="fas fa-video"></i></button>
                    <button class="action-btn" title="Plus" id="moreBtn"><i class="fas fa-ellipsis-v"></i></button>
                </div>
            </div>

            <div class="messages-container">
                <div class="messages-scroll" id="messagesContainer">
                    <!-- Messages chargés dynamiquement -->
                </div>
            </div>

            <form id="sendMessageForm" class="message-input-area">
                <div class="input-actions">
                    <button type="button" class="input-action-btn" title="Joindre un fichier" id="attachBtn">
                        <i class="fas fa-paperclip"></i>
                    </button>
                    <button type="button" class="input-action-btn" title="Émojis" id="emojiBtn">
                        <i class="fas fa-smile-wink"></i>
                    </button>
                    <button type="button" class="input-action-btn" title="Image" id="imageBtn">
                        <i class="fas fa-image"></i>
                    </button>
                </div>
                <div class="message-input-wrapper">
                    <input type="text" id="messageContent" placeholder="Écrire un message..." class="message-input" autocomplete="off">
                    <button type="submit" class="send-btn" id="sendBtn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </form>
        </div>

        <div class="empty-state" id="emptyDiscussionArea">
            <i class="fas fa-comments"></i>
            <h3>Vos discussions s'afficheront ici</h3>
            <p>Sélectionnez une conversation dans la liste de gauche pour commencer à échanger.</p>
        </div>
        
    </div>
</div>

<!-- MODALE NOUVELLE CONVERSATION -->
<div class="custom-modal-overlay" id="newConversationModal" style="display: none;">
    <div class="custom-modal-card">
        <div class="custom-modal-header">
            <h2>NOUVELLE CONVERSATION</h2>
            <button class="close-modal-x" id="btnCloseModalX">&times;</button>
        </div>
        
        <div class="modal-search-wrapper">
            <i class="fas fa-search modal-search-icon"></i>
            <input type="text" id="searchUserField" placeholder="rechercher un éleveur..." class="modal-search-input">
        </div>

        <div class="modal-results-title">
            <i class="fas fa-th-list text-muted mr-1"></i> RÉSULTATS :
        </div>

        <div class="modal-users-list" id="modalUsersContainer">
            <div class="text-center py-3 text-muted">Chargement des utilisateurs...</div>
        </div>

        <div class="custom-modal-footer">
            <button class="btn-close-modal-text" id="btnCloseModalBtn">FERMER</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // ================= CONFIGURATION =================
        const API_URL = window.location.origin + '/api';
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const rawToken = localStorage.getItem('access_token');
        const token = rawToken ? rawToken.replace(/^"(.*)"$/, '$1').trim() : null;

        let currentUser = null;
        try {
            currentUser = JSON.parse(localStorage.getItem('user'));
        } catch(e) {
            console.error("❌ Impossible de parser l'utilisateur connecté", e);
        }
        const currentUserId = currentUser ? parseInt(currentUser.id, 10) : null;

        if (!token) {
            window.location.href = '/auth/login';
            return;
        }

        // ================= ÉTAT =================
        let state = {
            activeConversationId: null,
            activeDestinataireId: null,
            pollingInterval: null,
            allUsersCache: []
        };

        // ================= PROTECTION XSS =================
        function escapeHtml(text) {
            if (!text) return '';
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // ================= REQUÊTES AUTHENTIFIÉES =================
        async function fetchWithAuth(url, options = {}) {
            const headers = {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
                'X-CSRF-TOKEN': CSRF_TOKEN,
                ...options.headers
            };
            
            const response = await fetch(url, { ...options, headers });

            if (response.status === 401) {
                console.error('❌ Session expirée [401]');
                localStorage.clear();
                window.location.href = '/auth/login';
                throw new Error('Non authentifié');
            }

            const result = await response.json();
            if (!response.ok) throw result;
            return result;
        }

        // ================= UTILITAIRES =================
        function scrollToBottom() {
            const box = document.getElementById('messagesContainer');
            if (box) {
                box.scrollTop = box.scrollHeight;
            }
        }

        function openDiscussionUI(name, avatar) {
            document.getElementById('activeChatName').textContent = 'Avec: ' + name;
            document.getElementById('activeChatAvatar').src = avatar;
            document.getElementById('emptyDiscussionArea').style.display = 'none';
            document.getElementById('discussionArea').style.display = 'flex';
        }

        function startPolling(conversationId) {
            clearInterval(state.pollingInterval);
            if (!conversationId) return;
            
            state.pollingInterval = setInterval(() => {
                loadMessages(conversationId, false);
            }, 4000);
        }

        // ================= CHARGEMENT DES CONVERSATIONS =================
        async function loadConversations() {
            try {
                const res = await fetchWithAuth(`${API_URL}/messaging/conversations?per_page=50`);
                const conversations = res.data?.conversations || [];
                const container = document.getElementById('conversationsContainer');
                container.innerHTML = '';

                document.getElementById('conversationsCount').textContent = conversations.length;

                if (conversations.length === 0) {
                    container.innerHTML = '<div class="text-center py-4 text-muted">Aucune discussion en cours</div>';
                    return;
                }

                // Calculer le total des non-lus
                let totalUnread = 0;

                conversations.forEach(conv => {
                    const partnerObj = conv.other_participant || {};
                    const partnerId = partnerObj.id ? parseInt(partnerObj.id, 10) : null;

                    if (!partnerId || isNaN(partnerId)) {
                        console.error("❌ Impossible d'extraire l'ID du destinataire :", conv);
                        return;
                    }

                    const partnerName = partnerObj.name || `Éleveur #${partnerId}`;
                    const unreadCount = parseInt(conv.unread_count, 10) || 0;
                    totalUnread += unreadCount;
                    const isUnread = unreadCount > 0 ? 'unread' : '';
                    const isActive = conv.id === state.activeConversationId ? 'active' : '';
                    const lastMsg = conv.derniere_message || 'Aucun message';
                    
                    const avatar = partnerObj.photo_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(partnerName)}&background=198754&color=fff&rounded=true&size=45`;

                    const itemHtml = `
                        <div class="conversation-item ${isActive} ${isUnread}" 
                            data-id="${conv.id}" 
                            data-user-id="${partnerId}" 
                            data-name="${escapeHtml(partnerName)}" 
                            data-avatar="${avatar}">
                            <div class="conversation-avatar">
                                <img src="${avatar}" alt="Avatar">
                                <span class="online-dot"></span>
                            </div>
                            <div class="conversation-info">
                                <div class="conversation-name">${escapeHtml(partnerName.toUpperCase())}</div>
                                <div class="conversation-preview">${escapeHtml(lastMsg)}</div>
                            </div>
                            <div class="conversation-time">
                                <span>${conv.updated_at ? new Date(conv.updated_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : ''}</span>
                                ${unreadCount > 0 ? `<span class="unread-badge">${unreadCount}</span>` : '<i class="fas fa-check-double read-marker"></i>'}
                            </div>
                        </div>
                    `;
                    container.innerHTML += itemHtml;
                });

                document.getElementById('totalUnread').textContent = totalUnread > 0 ? totalUnread : '';

            } catch (error) {
                console.error('❌ Erreur chargement conversations:', error);
            }
        }

        // ================= CHARGEMENT DES MESSAGES =================
        async function loadMessages(conversationId, scrollDown = false) {
            if (!conversationId) return;
            try {
                const res = await fetchWithAuth(`${API_URL}/messaging/conversations/${conversationId}/messages?per_page=100`);
                const messages = res.data?.messages || [];
                const container = document.getElementById('messagesContainer');
                
                if (!scrollDown && container.children.length === messages.length) {
                    return;
                }

                container.innerHTML = '';

                messages.forEach(msg => {
                    const isMe = msg.is_sent_by_me;
                    const msgClass = isMe ? 'message-sent' : 'message-received';
                    const time = msg.created_at ? new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '';
                    const readCheck = msg.lu ? '<i class="fas fa-check-double read"></i>' : '<i class="fas fa-check"></i>';

                    let contentHtml = `<p>${escapeHtml(msg.contenu)}</p>`;
                    
                    if (msg.type === 'image' && msg.media_url) {
                        contentHtml = `<img src="${msg.media_url}" class="message-media" alt="Image"><p>${escapeHtml(msg.contenu || '')}</p>`;
                    } else if (msg.type === 'file' && msg.media_url) {
                        contentHtml = `<a href="${msg.media_url}" target="_blank" class="message-file"><i class="fas fa-paperclip"></i> ${escapeHtml(msg.file_name || 'Fichier joint')}</a>`;
                    }

                    const messageHtml = `
                        <div class="message ${msgClass}">
                            <div class="message-bubble">
                                ${contentHtml}
                                <span class="message-time">${time} ${isMe ? readCheck : ''}</span>
                            </div>
                        </div>
                    `;
                    container.innerHTML += messageHtml;
                });

                if (scrollDown) {
                    scrollToBottom();
                }

            } catch (error) {
                console.error(`❌ Erreur chargement messages (${conversationId}):`, error);
            }
        }

        // ================= SÉLECTION D'UNE DISCUSSION =================
        document.addEventListener('click', function(e) {
            const item = e.target.closest('.conversation-item');
            if (!item) return;

            document.querySelectorAll('.conversation-item').forEach(el => el.classList.remove('active'));
            item.classList.add('active');
            item.classList.remove('unread');
            const badge = item.querySelector('.unread-badge');
            if (badge) badge.remove();

            state.activeConversationId = parseInt(item.dataset.id, 10);
            state.activeDestinataireId = parseInt(item.dataset.userId, 10);

            openDiscussionUI(item.dataset.name, item.dataset.avatar);
            loadMessages(state.activeConversationId, true);

            fetchWithAuth(`${API_URL}/messaging/conversations/${state.activeConversationId}/read`, { 
                method: 'POST' 
            }).catch(e => console.error('Erreur lecture', e));

            startPolling(state.activeConversationId);
        });

        // ================= ENVOI DE MESSAGE =================
        document.getElementById('sendMessageForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const input = document.getElementById('messageContent');
            const contenu = input.value.trim();
            const intDestinataireId = parseInt(state.activeDestinataireId, 10);
            
            if (!contenu || !intDestinataireId || isNaN(intDestinataireId)) {
                console.warn("⚠️ Envoi bloqué");
                return;
            }
            
            const payload = {
                destinataire_id: intDestinataireId,
                contenu: contenu,
                type: 'text'
            };

            const timeNow = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            const tempHtml = `
                <div class="message message-sent parsing-pending" style="opacity: 0.6;">
                    <div class="message-bubble">
                        <p>${escapeHtml(contenu)}</p>
                        <span class="message-time">${timeNow} <i class="fas fa-clock"></i></span>
                    </div>
                </div>
            `;
            document.getElementById('messagesContainer').innerHTML += tempHtml;
            scrollToBottom();
            input.value = '';

            try {
                const res = await fetchWithAuth(`${API_URL}/messaging/send`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                
                document.querySelectorAll('.parsing-pending').forEach(el => el.remove());
                
                if (!state.activeConversationId && res.data?.conversation_id) {
                    state.activeConversationId = res.data.conversation_id;
                    startPolling(state.activeConversationId);
                }
                
                await loadMessages(state.activeConversationId, true);
                loadConversations();

            } catch (error) {
                console.error("❌ Échec de l'envoi:", error);
                document.querySelectorAll('.parsing-pending').forEach(el => el.remove());
                input.value = contenu;
                alert("Impossible d'envoyer le message : " + (error.message || "Erreur réseau"));
            }
        });

        // ================= MODALE NOUVELLE CONVERSATION =================
        document.getElementById('btnNewConv').addEventListener('click', function() {
            document.getElementById('newConversationModal').style.display = 'flex';
            document.getElementById('searchUserField').value = '';
            fetchUsersForModal();
        });

        function closeModal() {
            document.getElementById('newConversationModal').style.display = 'none';
        }

        document.getElementById('btnCloseModalX').addEventListener('click', closeModal);
        document.getElementById('btnCloseModalBtn').addEventListener('click', closeModal);

        document.getElementById('newConversationModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

        async function fetchUsersForModal() {
            const container = document.getElementById('modalUsersContainer');
            container.innerHTML = '<div class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin mr-2"></i>Chargement...</div>';

            try {
                const res = await fetchWithAuth(`${API_URL}/messaging/users`);
                const originalData = res.data || res || [];
                
                state.allUsersCache = originalData.filter(u => parseInt(u.id, 10) !== currentUserId);
                renderModalUsers(state.allUsersCache);

            } catch (error) {
                console.error('❌ Erreur chargement utilisateurs:', error);
                container.innerHTML = '<div class="text-center py-3 text-danger">Erreur de récupération des éleveurs.</div>';
            }
        }

        function renderModalUsers(usersList) {
            const container = document.getElementById('modalUsersContainer');
            container.innerHTML = '';

            if (usersList.length === 0) {
                container.innerHTML = '<div class="text-center py-3 text-muted">Aucun éleveur trouvé</div>';
                return;
            }

            usersList.forEach(user => {
                const avatar = `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=198754&color=fff&rounded=true&size=40`;
                const description = user.elevage_type ? `${user.role || 'Éleveur'} - ${user.elevage_type}` : `${user.role || 'Éleveur'} - ${user.commune || 'Sénégal'}`;

                const rowHtml = `
                    <div class="modal-user-item">
                        <div class="modal-user-left">
                            <img src="${avatar}" class="modal-user-avatar" alt="Avatar">
                            <div>
                                <div class="modal-user-name">${escapeHtml(user.name)}</div>
                                <div class="modal-user-role">${escapeHtml(description)}</div>
                            </div>
                        </div>
                        <button class="btn-modal-contact action-start-chat" 
                                data-id="${user.id}" 
                                data-name="${escapeHtml(user.name)}" 
                                data-avatar="${avatar}">
                            <i class="far fa-envelope text-success"></i> Contacter
                        </button>
                    </div>
                `;
                container.innerHTML += rowHtml;
            });
        }

        document.getElementById('searchUserField').addEventListener('keyup', function() {
            const term = this.value.toLowerCase();
            const filtered = state.allUsersCache.filter(user => {
                return user.name.toLowerCase().includes(term) || 
                    (user.elevage_type && user.elevage_type.toLowerCase().includes(term));
            });
            renderModalUsers(filtered);
        });

        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.action-start-chat');
            if (!btn) return;

            const userId = parseInt(btn.dataset.id, 10);
            const name = btn.dataset.name;
            const avatar = btn.dataset.avatar;

            if (!userId || isNaN(userId)) {
                console.error("❌ ID utilisateur introuvable");
                return;
            }

            closeModal();

            const existingConv = document.querySelector(`.conversation-item[data-user-id="${userId}"]`);

            if (existingConv) {
                existingConv.click();
            } else {
                state.activeConversationId = null;
                state.activeDestinataireId = userId;

                openDiscussionUI(name, avatar);
                document.getElementById('messagesContainer').innerHTML = 
                    '<div class="text-center py-4 text-muted">Envoyez un premier message pour démarrer la discussion.</div>';
                
                clearInterval(state.pollingInterval);
            }
        });

        // ================= RECHERCHE =================
        document.getElementById('searchContact').addEventListener('keyup', function() {
            const term = this.value.toLowerCase();
            document.querySelectorAll('.conversation-item').forEach(item => {
                const name = (item.dataset.name || '').toLowerCase();
                item.style.display = name.includes(term) ? 'flex' : 'none';
            });
        });

        // ================= BOUTONS D'ACTION =================
        document.getElementById('callBtn').addEventListener('click', function() {
            alert('🔊 Appel en cours... (fonctionnalité à venir)');
        });

        document.getElementById('videoBtn').addEventListener('click', function() {
            alert('📹 Appel vidéo en cours... (fonctionnalité à venir)');
        });

        document.getElementById('moreBtn').addEventListener('click', function() {
            const actions = ['📎 Partager un fichier', '📍 Partager la position', '📅 Planifier un rendez-vous', '🔇 Mettre en sourdine'];
            const choice = prompt('Choisissez une action :\n\n' + actions.map((a, i) => `${i+1}. ${a}`).join('\n'));
            if (choice) {
                const index = parseInt(choice) - 1;
                if (index >= 0 && index < actions.length) {
                    alert(`Action: ${actions[index]} (fonctionnalité à venir)`);
                }
            }
        });

        document.getElementById('attachBtn').addEventListener('click', function() {
            alert('📎 Joindre un fichier (fonctionnalité à venir)');
        });

        document.getElementById('emojiBtn').addEventListener('click', function() {
            const emojis = ['👍', '❤️', '😊', '😂', '😍', '🤔', '🙏', '💪', '👋', '✨'];
            const randomEmoji = emojis[Math.floor(Math.random() * emojis.length)];
            document.getElementById('messageContent').value += randomEmoji;
            document.getElementById('messageContent').focus();
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
                        const conversation = document.getElementById('messagesContainer');
                        const timeNow = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                        const tempHtml = `
                            <div class="message message-sent parsing-pending">
                                <div class="message-bubble">
                                    <img src="${event.target.result}" class="message-media" alt="Image partagée">
                                    <span class="message-time">${timeNow} <i class="fas fa-clock"></i></span>
                                </div>
                            </div>
                        `;
                        conversation.innerHTML += tempHtml;
                        scrollToBottom();
                        alert('📷 Image partagée (fonctionnalité à venir)');
                    };
                    reader.readAsDataURL(file);
                }
            };
            input.click();
        });

        // ================= INITIALISATION =================
        loadConversations();
    });
</script>
@endpush

@endsection
@extends('layouts.menu')

@section('title', 'Messagerie')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/eleveurCSS/messages.css') }}">
@endpush

@section('content')

<div class="messagerie-container">
    
    <!-- En-tête de la messagerie -->
    <div class="messagerie-header">
        <h1 class="messagerie-title">
            <i class="fas fa-envelope mr-2 text-success"></i> MESSAGERIE
        </h1>
        <div class="search-bar">
            <i class="fas fa-search search-icon"></i>
            <input type="text" placeholder="rechercher un éleveur..." class="search-input">
        </div>
    </div>

    <!-- Corps principal : Conversations + Discussion -->
    <div class="messagerie-body">
        
        <!-- COLONNE GAUCHE : Liste des conversations -->
        <div class="conversations-list">
            <div class="conversations-header">
                <h3><i class="fas fa-comments mr-2"></i> CONVERSATIONS</h3>
            </div>
            
            <div class="conversations-scroll">
                <!-- Conversation 1 - En ligne -->
                <div class="conversation-item active">
                    <div class="conversation-avatar">
                        <img src="https://ui-avatars.com/api/?name=Amadou+Sy&background=198754&color=fff&rounded=true&size=45" alt="Avatar">
                        <span class="online-dot"></span>
                    </div>
                    <div class="conversation-info">
                        <div class="conversation-name">AMADOU SY</div>
                        <div class="conversation-preview">Bonjour, as-tu...</div>
                    </div>
                    <div class="conversation-time">
                        <span>10:30</span>
                        <i class="fas fa-check-double read-marker"></i>
                    </div>
                </div>

                <!-- Conversation 2 -->
                <div class="conversation-item">
                    <div class="conversation-avatar">
                        <img src="https://ui-avatars.com/api/?name=Fatou+Diop&background=198754&color=fff&rounded=true&size=45" alt="Avatar">
                        <span class="online-dot"></span>
                    </div>
                    <div class="conversation-info">
                        <div class="conversation-name">FATOU DIOP</div>
                        <div class="conversation-preview">Merci pour les conseils...</div>
                    </div>
                    <div class="conversation-time">
                        <span>hier</span>
                    </div>
                </div>

                <!-- Conversation 3 -->
                <div class="conversation-item">
                    <div class="conversation-avatar">
                        <img src="https://ui-avatars.com/api/?name=Moussa+Diallo&background=198754&color=fff&rounded=true&size=45" alt="Avatar">
                        <span class="online-dot"></span>
                    </div>
                    <div class="conversation-info">
                        <div class="conversation-name">MOUSSA DIALLO</div>
                        <div class="conversation-preview">J'ai reçu les vaccins...</div>
                    </div>
                    <div class="conversation-time">
                        <span>2 ma</span>
                    </div>
                </div>

                <!-- Conversation 4 -->
                <div class="conversation-item">
                    <div class="conversation-avatar">
                        <img src="https://ui-avatars.com/api/?name=Aminata+Ba&background=198754&color=fff&rounded=true&size=45" alt="Avatar">
                    </div>
                    <div class="conversation-info">
                        <div class="conversation-name">AMINATA BA</div>
                        <div class="conversation-preview">Rendez-vous demain...</div>
                    </div>
                    <div class="conversation-time">
                        <span>1 ja</span>
                    </div>

                </div> 
            </div> <br> <br> <br>

                <div class="app-banner-buttons">
                <button class="btn-new-conversation">
                    <i class="fas fa-plus mr-2"></i> Nouvelle conversation
                </button>
            </div>
        </div>

            <!-- Bouton Nouvelle conversation (mobile) -->
            <button class="btn-new-conversation-mobile">
                <i class="fas fa-plus"></i>
                <span>Nouvelle conversation</span>
            </button>
        </div>

        <!-- COLONNE DROITE : Zone de discussion -->
        <div class="discussion-area">
            
            <!-- En-tête de la discussion -->
            <div class="discussion-header">
                <div class="discussion-contact">
                    <div class="discussion-avatar">
                        <img src="https://ui-avatars.com/api/?name=Amadou+Sy&background=198754&color=fff&rounded=true&size=45" alt="Avatar">
                        <span class="online-dot large"></span>
                    </div>
                    <div class="discussion-info">
                        <h3>Avec: Amadou Sy</h3>
                        <span class="online-status"><i class="fas fa-circle"></i> En ligne</span>
                    </div>
                </div>
                <div class="discussion-actions">
                    <button class="action-btn" title="Appeler">
                        <i class="fas fa-phone"></i>
                    </button>
                    <button class="action-btn" title="Vidéo">
                        <i class="fas fa-video"></i>
                    </button>
                    <button class="action-btn" title="Plus">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                </div>
            </div>

            <!-- Messages de la discussion -->
            <div class="messages-container">
                <div class="messages-scroll">
                    
                    <!-- Message Moi -->
                    <div class="message message-sent">
                        <div class="message-bubble">
                            <p>Bonjour Amadou, as-tu des nouvelles de la fièvre aphteuse ?</p>
                            <span class="message-time">10:30 <i class="fas fa-check-double read"></i></span>
                        </div>
                    </div>

                    <!-- Message Amadou -->
                    <div class="message message-received">
                        <div class="message-bubble">
                            <p>Oui, 2 nouveaux cas signalés hier à Rufisque</p>
                            <span class="message-time">10:32</span>
                        </div>
                    </div>

                    <!-- Message Amadou -->
                    <div class="message message-received">
                        <div class="message-bubble">
                            <p>Je te conseille de vacciner tout le troupeau</p>
                            <span class="message-time">10:33</span>
                        </div>
                    </div>

                    <!-- Message Moi -->
                    <div class="message message-sent">
                        <div class="message-bubble">
                            <p>Merci beaucoup pour l'info ! Je vais prendre rdv avec le vétérinaire dès aujourd'hui.</p>
                            <span class="message-time">10:40 <i class="fas fa-check-double read"></i></span>
                        </div>
                    </div>

                    <!-- Message Amadou -->
                    <div class="message message-received">
                        <div class="message-bubble">
                            <p>Parfait, tiens-moi au courant si tu as besoin d'aide 👍</p>
                            <span class="message-time">10:42</span>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Zone d'envoi de message -->
            <div class="message-input-area">
                <div class="input-actions">
                    <button class="input-action-btn" title="Joindre un fichier">
                        <i class="fas fa-paperclip"></i>
                    </button>
                    <button class="input-action-btn" title="Émojis">
                        <i class="fas fa-smile-wink"></i>
                    </button>
                    <button class="input-action-btn" title="Image">
                        <i class="fas fa-image"></i>
                    </button>
                </div>
                <div class="message-input-wrapper">
                    <input type="text" placeholder="Écrire un message..." class="message-input">
                    <button class="send-btn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>

        </div>
        
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Sélection d'une conversation
        $('.conversation-item').click(function() {
            // Enlever la classe active de tous les éléments
            $('.conversation-item').removeClass('active');
            // Ajouter la classe active à l'élément cliqué
            $(this).addClass('active');
            
            // Récupérer le nom de l'éleveur
            const eleveurName = $(this).find('.conversation-name').text();
            const avatarSrc = $(this).find('.conversation-avatar img').attr('src');
            
            // Mettre à jour l'en-tête de la discussion
            $('.discussion-contact h3').text('Avec: ' + eleveurName);
            $('.discussion-avatar img').attr('src', avatarSrc);
            
            // Animation de transition
            $('.messages-scroll').animate({ scrollTop: $('.messages-scroll')[0].scrollHeight }, 300);
        });
        
        // Envoi d'un message
        function sendMessage() {
            const messageText = $('.message-input').val().trim();
            if (messageText === '') return;
            
            const now = new Date();
            const time = now.getHours() + ':' + (now.getMinutes() < 10 ? '0' : '') + now.getMinutes();
            
            // Créer le nouveau message
            const newMessage = `
                <div class="message message-sent">
                    <div class="message-bubble">
                        <p>${escapeHtml(messageText)}</p>
                        <span class="message-time">${time} <i class="fas fa-check-double"></i></span>
                    </div>
                </div>
            `;
            
            // Ajouter le message
            $('.messages-scroll').append(newMessage);
            
            // Effacer l'input
            $('.message-input').val('');
            
            // Scroll vers le bas
            $('.messages-scroll').animate({ scrollTop: $('.messages-scroll')[0].scrollHeight }, 300);
        }
        
        // Fonction d'échappement HTML
        function escapeHtml(text) {
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
        
        // Envoi avec la touche Entrée
        $('.message-input').keypress(function(e) {
            if (e.which === 13 && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
        
        // Envoi avec le bouton
        $('.send-btn').click(function() {
            sendMessage();
        });
        
        // Auto-scroll au chargement
        $('.messages-scroll').animate({ scrollTop: $('.messages-scroll')[0].scrollHeight }, 0);
        
        // Bouton nouvelle conversation
        $('.btn-new-conversation, .btn-new-conversation-mobile').click(function() {
            alert('Fonctionnalité à venir : Créer une nouvelle conversation');
        });
        
        // Actions (appel, vidéo, etc.)
        $('.action-btn').click(function() {
            alert('Fonctionnalité à venir');
        });
        
        // Recherche d'éleveur
        $('.search-input').on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase();
            $('.conversation-item').each(function() {
                const name = $(this).find('.conversation-name').text().toLowerCase();
                if (name.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    });
</script>
@endpush
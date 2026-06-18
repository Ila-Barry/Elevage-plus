@extends('layouts.menu')

@section('title', 'Nouvelle Conversation')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/eleveurCSS/nouvelle-conversation.css') }}">
@endpush

@section('content')

<div class="nouvelle-conversation-container">
    
    <!-- En-tête avec titre et bouton fermer -->
    <div class="conversation-header">
        <h1 class="conversation-title">
            <i class="fas fa-comment-dots mr-2 text-success"></i> NOUVELLE CONVERSATION
        </h1>
        <button class="btn-close-conversation" onclick="window.history.back()">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Barre de recherche -->
    <div class="search-section">
        <div class="search-bar">
            <i class="fas fa-search search-icon"></i>
            <input type="text" placeholder="Rechercher un éleveur..." class="search-input" id="searchEleveur">
        </div>
    </div>

    <!-- Résultats de recherche -->
    <div class="results-section">
        <h3 class="results-title">RÉSULTATS :</h3>
        
        <div class="results-list" id="resultsList">
            
            <!-- Éleveur 1 - Amadeu Sy -->
            <div class="result-item" data-name="Amadeu Sy" data-specialty="Éleveur évent" data-location="Dakar">
                <div class="result-avatar">
                    <img src="https://ui-avatars.com/api/?name=Amadeu+Sy&background=198754&color=fff&rounded=true&size=50" alt="Amadeu Sy">
                </div>
                <div class="result-info">
                    <div class="result-name">Amadeu Sy</div>
                    <div class="result-details">
                        <span class="result-specialty">Éleveur évent</span>
                        <span class="result-location">- Dakar</span>
                    </div>
                </div>
                <button class="btn-contacter" data-eleveur="Amadeu Sy">
                    Contacter
                </button>
            </div>

            <!-- Éleveur 2 - Marie Diop -->
            <div class="result-item" data-name="Marie Diop" data-specialty="Éleveur caprine" data-location="Thiès">
                <div class="result-avatar">
                    <img src="https://ui-avatars.com/api/?name=Marie+Diop&background=198754&color=fff&rounded=true&size=50" alt="Marie Diop">
                </div>
                <div class="result-info">
                    <div class="result-name">Marie Diop</div>
                    <div class="result-details">
                        <span class="result-specialty">Éleveur caprine</span>
                        <span class="result-location">- Thiès</span>
                    </div>
                </div>
                <button class="btn-contacter" data-eleveur="Marie Diop">
                    Contacter
                </button>
            </div>

            <!-- Éleveur 3 - Ibrahim Fali -->
            <div class="result-item" data-name="Ibrahim Fali" data-specialty="Éleveur bovin" data-location="Saint-Louis">
                <div class="result-avatar">
                    <img src="https://ui-avatars.com/api/?name=Ibrahim+Fali&background=198754&color=fff&rounded=true&size=50" alt="Ibrahim Fali">
                </div>
                <div class="result-info">
                    <div class="result-name">Ibrahim Fali</div>
                    <div class="result-details">
                        <span class="result-specialty">Éleveur bovin</span>
                        <span class="result-location">- Saint-Louis</span>
                    </div>
                </div>
                <button class="btn-contacter" data-eleveur="Ibrahim Fali">
                    Contacter
                </button>
            </div>

        </div>
    </div>

    <!-- Mots-clés / Message d'exemple -->
    <div class="keywords-section">
        <div class="keywords-badge">
            <i class="fas fa-tag text-success mr-2"></i>
            <span class="keywords-text">Bonjour Amadeu, as-tu des nouvelles de la fièvre aphteuse ?</span>
        </div>
    </div>

    <!-- Zone d'envoi de message (cachée par défaut, s'affiche après clic sur Contacter) -->
    <div class="message-input-area" id="messageInputArea" style="display: none;">
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
            <input type="text" placeholder="Écrire un message..." class="message-input" id="messageInput">
            <button class="send-btn" id="sendMessageBtn">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        
        // Variable pour stocker l'éleveur sélectionné
        let selectedEleveur = '';

        /**
         * Fonction : Filtrer les résultats de recherche
         * Permet de rechercher un éleveur par nom
         */
        $('#searchEleveur').on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase().trim();
            
            $('.result-item').each(function() {
                const name = $(this).data('name').toLowerCase();
                const specialty = $(this).data('specialty').toLowerCase();
                const location = $(this).data('location').toLowerCase();
                
                // Recherche dans le nom, la spécialité ou la localisation
                if (name.includes(searchTerm) || 
                    specialty.includes(searchTerm) || 
                    location.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        /**
         * Fonction : Gérer le clic sur le bouton "Contacter"
         * Affiche la zone de saisie et met à jour les mots-clés
         */
        $('.btn-contacter').click(function() {
            const eleveurName = $(this).data('eleveur');
            selectedEleveur = eleveurName;
            
            // Mettre à jour les mots-clés avec le nom de l'éleveur
            $('.keywords-text').text(`Bonjour ${eleveurName}, `);
            
            // Afficher la zone de saisie avec animation
            $('#messageInputArea').slideDown(300);
            
            // Mettre le focus sur l'input
            $('#messageInput').focus();
            
            // Mettre en évidence l'éleveur sélectionné
            $('.result-item').removeClass('selected');
            $(this).closest('.result-item').addClass('selected');
            
            // Scroll vers la zone de saisie
            setTimeout(() => {
                $('#messageInputArea')[0].scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
            }, 350);
        });

        /**
         * Fonction : Envoyer un message
         * Simule l'envoi d'un message et redirige vers la messagerie
         */
        function sendMessage() {
            const messageText = $('#messageInput').val().trim();
            
            if (messageText === '') {
                // Effet de secousse si le message est vide
                $('#messageInput').addClass('shake');
                setTimeout(() => {
                    $('#messageInput').removeClass('shake');
                }, 500);
                return;
            }
            
            if (selectedEleveur === '') {
                alert('Veuillez sélectionner un éleveur en cliquant sur "Contacter".');
                return;
            }
            
            // Simuler l'envoi avec une confirmation
            const confirmMessage = confirm(
                `Envoyer le message à ${selectedEleveur} ?\n\n"${messageText}"`
            );
            
            if (confirmMessage) {
                // Animation de succès
                $('.send-btn').addClass('sending');
                $('.send-btn i').removeClass('fa-paper-plane').addClass('fa-spinner fa-spin');
                
                setTimeout(() => {
                    // Rediriger vers la messagerie avec le message
                    // Ici on simule un retour vers la messagerie
                    alert(`✅ Message envoyé à ${selectedEleveur} !`);
                    
                    // Redirection vers la messagerie
                    window.location.href = "{{ route('messagerie') }}";
                    
                }, 1000);
            }
        }

        /**
         * Envoi du message avec la touche Entrée
         */
        $('#messageInput').keypress(function(e) {
            if (e.which === 13 && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        /**
         * Envoi du message avec le bouton d'envoi
         */
        $('#sendMessageBtn').click(function() {
            sendMessage();
        });

        /**
         * Bouton de fermeture - Retour à la messagerie
         */
        $('.btn-close-conversation').click(function() {
            window.history.back();
        });

        /**
         * Gestion des actions supplémentaires (pièce jointe, émojis, etc.)
         */
        $('.input-action-btn').click(function() {
            const action = $(this).find('i').attr('class');
            if (action.includes('paperclip')) {
                alert('📎 Fonctionnalité : Joindre un fichier');
            } else if (action.includes('smile')) {
                alert('😊 Fonctionnalité : Insérer un émoji');
            } else if (action.includes('image')) {
                alert('🖼️ Fonctionnalité : Insérer une image');
            }
        });

        /**
         * Animation : Survol des résultats
         */
        $('.result-item').hover(
            function() {
                $(this).find('.btn-contacter').addClass('hover-effect');
            },
            function() {
                $(this).find('.btn-contacter').removeClass('hover-effect');
            }
        );

    });
</script>
@endpush
@extends('layouts.menu')

@section('title', 'Messages')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/eleveurCSS/messages.css') }}">
<style>
/* ==============================================
   STYLES DE LA MODALE NOUVELLE CONVERSATION
   ============================================== */

/* ---------- MODALE PERSONNALISÉE ---------- */
#newConversationModal .modal-content {
    border-radius: 20px;
    border: none;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    overflow: hidden;
}

#newConversationModal .modal-header {
    background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%);
    border-bottom: 2px solid #eef2f6;
    padding: 20px 28px;
}

#newConversationModal .modal-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: #1f2937;
    letter-spacing: -0.3px;
}

#newConversationModal .modal-title i {
    color: #198754;
}

#newConversationModal .close {
    font-size: 1.8rem;
    font-weight: 300;
    color: #6b7280;
    opacity: 1;
    padding: 0;
    margin: -10px -10px -10px 0;
    transition: all 0.3s ease;
}

#newConversationModal .close:hover {
    color: #dc2626;
    transform: rotate(90deg);
}

#newConversationModal .modal-body {
    padding: 28px;
    background: #f8f9fc;
}

#newConversationModal .modal-footer {
    border-top: 2px solid #eef2f6;
    padding: 16px 28px;
    background: white;
}

#newConversationModal .modal-footer .btn-secondary {
    background: #f3f4f6;
    border: none;
    color: #4b5563;
    padding: 8px 28px;
    border-radius: 40px;
    font-weight: 600;
    transition: all 0.3s ease;
}

#newConversationModal .modal-footer .btn-secondary:hover {
    background: #e5e7eb;
    color: #1f2937;
}

/* ---------- BARRE DE RECHERCHE DANS LA MODALE ---------- */
.search-people-bar {
    position: relative;
    margin-bottom: 24px;
}

.search-people-bar .search-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    z-index: 2;
    font-size: 0.9rem;
}

.search-people-bar .search-input {
    width: 100%;
    padding: 14px 18px 14px 46px;
    border: 2px solid #e5e7eb;
    border-radius: 14px;
    font-size: 0.95rem;
    background: white;
    transition: all 0.3s ease;
    outline: none;
}

.search-people-bar .search-input:focus {
    border-color: #198754;
    box-shadow: 0 0 0 4px rgba(25, 135, 84, 0.1);
}

.search-people-bar .search-input::placeholder {
    color: #9ca3af;
}

/* ---------- RÉSULTATS DE RECHERCHE ---------- */
.search-results {
    background: white;
    border-radius: 16px;
    padding: 16px 0;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
}

.results-label {
    font-size: 0.75rem;
    font-weight: 700;
    color: #6b7280;
    letter-spacing: 0.8px;
    padding: 0 20px 14px 20px;
    border-bottom: 1px solid #eef2f6;
    text-transform: uppercase;
}

.results-label i {
    color: #198754;
}

/* ---------- ÉLÉMENT DE RÉSULTAT ---------- */
.people-result {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 12px 20px;
    transition: all 0.25s ease;
    border-left: 4px solid transparent;
    cursor: default;
}

.people-result:hover {
    background: #f9fafb;
}

.people-result:last-child {
    border-bottom: none;
}

.people-avatar {
    flex-shrink: 0;
}

.people-avatar img {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #e5e7eb;
    transition: border-color 0.3s;
}

.people-result:hover .people-avatar img {
    border-color: #198754;
}

.people-info {
    flex: 1;
    min-width: 0;
}

.people-name {
    font-weight: 600;
    font-size: 0.95rem;
    color: #1f2937;
    margin-bottom: 2px;
}

.people-spec {
    font-size: 0.8rem;
    color: #6b7280;
}

/* ---------- BOUTON CONTACTER ---------- */
.btn-contact {
    padding: 8px 18px;
    border: 2px solid #198754;
    border-radius: 40px;
    background: transparent;
    color: #198754;
    font-weight: 600;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
    font-family: inherit;
}

.btn-contact:hover {
    background: #198754;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(25, 135, 84, 0.3);
}

.btn-contact i {
    font-size: 0.75rem;
}

/* ---------- AUCUN RÉSULTAT ---------- */
.no-results {
    text-align: center;
    padding: 40px 20px;
    background: white;
    border-radius: 16px;
}

.no-results i {
    font-size: 2.5rem;
    color: #d1d5db;
    margin-bottom: 12px;
    display: block;
}

.no-results p {
    color: #6b7280;
    font-size: 0.95rem;
    margin: 0;
}


.keyword-label {
    font-size: 0.75rem;
    font-weight: 700;
    color: #6b7280;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    margin-bottom: 10px;
}

.keyword-label i {
    color: #198754;
}

.keyword-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.keyword-tag {
    background: #f0fdf4;
    color: #1f2937;
    padding: 8px 14px;
    border-radius: 40px;
    font-size: 0.85rem;
    font-weight: 500;
    border: 1px solid #bbf7d0;
}

/* ---------- RESPONSIVE MODALE ---------- */

/* Tablettes */
@media (max-width: 992px) {
    #newConversationModal .modal-body {
        padding: 20px;
    }
    
    #newConversationModal .modal-header {
        padding: 16px 20px;
    }
}

/* Mobiles */
@media (max-width: 768px) {
    #newConversationModal .modal-dialog {
        margin: 10px;
    }
    
    #newConversationModal .modal-content {
        border-radius: 16px;
    }
    
    #newConversationModal .modal-body {
        padding: 16px;
    }
    
    #newConversationModal .modal-header {
        padding: 14px 16px;
    }
    
    #newConversationModal .modal-title {
        font-size: 1.1rem;
    }
    
    .people-result {
        padding: 10px 14px;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .people-avatar img {
        width: 40px;
        height: 40px;
    }
    
    .people-name {
        font-size: 0.85rem;
    }
    
    .people-spec {
        font-size: 0.75rem;
    }
    
    .btn-contact {
        padding: 6px 14px;
        font-size: 0.75rem;
    }
    
    .search-people-bar .search-input {
        padding: 12px 14px 12px 42px;
        font-size: 0.9rem;
        border-radius: 12px;
    }
    
    .keyword-tag {
        font-size: 0.8rem;
        padding: 6px 12px;
    }
}

/* Très petits mobiles */
@media (max-width: 480px) {
    #newConversationModal .modal-body {
        padding: 12px;
    }
    
    .people-result {
        padding: 8px 10px;
        gap: 8px;
    }
    
    .people-avatar img {
        width: 36px;
        height: 36px;
    }
    
    .people-name {
        font-size: 0.8rem;
    }
    
    .people-spec {
        font-size: 0.7rem;
    }
    
    .btn-contact {
        padding: 4px 10px;
        font-size: 0.7rem;
        border-width: 1.5px;
    }
    
    .results-label {
        font-size: 0.65rem;
        padding: 0 12px 10px 12px;
    }
    
    .keyword-suggestion {
        padding: 12px 14px;
    }
    
    .keyword-tag {
        font-size: 0.75rem;
        padding: 5px 10px;
    }
}

/* ---------- ANIMATION D'ENTRÉE ---------- */
.modal.fade .modal-dialog {
    transform: scale(0.95) translateY(-20px);
    transition: all 0.3s ease;
}

.modal.show .modal-dialog {
    transform: scale(1) translateY(0);
}

/* ---------- SCROLLBAR MODALE ---------- */
.search-results::-webkit-scrollbar {
    width: 4px;
}

.search-results::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.search-results::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.search-results::-webkit-scrollbar-thumb:hover {
    background: #198754;
}
</style>
@endpush

@section('content')

<div class="dashboard-wrapper">

    <div class="messagerie-container">
        <!-- En-tête -->
        <div class="messagerie-header">
            <h1 class="messagerie-title">
                <i class="fas fa-comments text-success mr-2"></i>Messages
            </h1>
            <div class="search-bar">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" placeholder="Rechercher une conversation...">
            </div>
        </div>

        <!-- Corps messagerie -->
        <div class="messagerie-body">
            <!-- Colonne gauche : Liste conversations -->
            <div class="conversations-list">
                <div class="conversations-header">
                    <h3><i class="fas fa-comment-dots mr-2"></i>Conversations</h3>
                </div>
                <div class="conversations-scroll">
                    <!-- Exemple de conversation -->
                    <div class="conversation-item active">
                        <div class="conversation-avatar">
                            <img src="https://ui-avatars.com/api/?name=Amadeu+Sy&background=198754&color=fff" alt="Avatar">
                            <span class="online-dot"></span>
                        </div>
                        <div class="conversation-info">
                            <div class="conversation-name">Amadeu Sy</div>
                            <div class="conversation-preview">Bonjour, as-tu des nouvelles...</div>
                        </div>
                        <div class="conversation-time">
                            <span>10:30</span>
                            <div class="read-marker"><i class="fas fa-check-circle"></i></div>
                        </div>
                    </div>

                    <div class="conversation-item">
                        <div class="conversation-avatar">
                            <img src="https://ui-avatars.com/api/?name=Marie+Diop&background=198754&color=fff" alt="Avatar">
                        </div>
                        <div class="conversation-info">
                            <div class="conversation-name">Marie Diop</div>
                            <div class="conversation-preview">Merci pour les informations</div>
                        </div>
                        <div class="conversation-time">
                            <span>Hier</span>
                        </div>
                    </div>

                    <div class="conversation-item">
                        <div class="conversation-avatar">
                            <img src="https://ui-avatars.com/api/?name=Ibrahim+Fati&background=198754&color=fff" alt="Avatar">
                        </div>
                        <div class="conversation-info">
                            <div class="conversation-name">Ibrahim Fati</div>
                            <div class="conversation-preview">Je vous envoie les documents</div>
                        </div>
                        <div class="conversation-time">
                            <span>Hier</span>
                        </div>
                    </div>
                </div>
                <!-- Bouton nouvelle conversation mobile -->
                <button class="btn-new-conversation-mobile" onclick="openNewConversationModal()">
                    <i class="fas fa-plus mr-2"></i> Nouvelle conversation
                </button>
            </div>

            <!-- Colonne droite : Discussion -->
            <div class="discussion-area">
                <!-- En-tête discussion -->
                <div class="discussion-header">
                    <div class="discussion-contact">
                        <div class="discussion-avatar">
                            <img src="https://ui-avatars.com/api/?name=Amadeu+Sy&background=198754&color=fff" alt="Avatar">
                            <span class="online-dot large"></span>
                        </div>
                        <div class="discussion-info">
                            <h3>Amadeu Sy</h3>
                            <span class="online-status"><i class="fas fa-circle"></i> En ligne</span>
                        </div>
                    </div>
                    <div class="discussion-actions">
                        <button class="action-btn"><i class="fas fa-phone"></i></button>
                        <button class="action-btn"><i class="fas fa-video"></i></button>
                        <button class="action-btn"><i class="fas fa-ellipsis-v"></i></button>
                    </div>
                </div>

                <!-- Messages -->
                <div class="messages-container">
                    <div class="messages-scroll">
                        <div class="message message-received">
                            <div class="message-bubble">
                                <p>Bonjour Amadeu, as-tu des nouvelles de la fièvre aphteuse ?</p>
                                <div class="message-time">10:28</div>
                            </div>
                        </div>
                        <div class="message message-sent">
                            <div class="message-bubble">
                                <p>Oui, j'ai reçu des informations. La situation semble stable pour le moment.</p>
                                <div class="message-time">10:30 <span class="read"><i class="fas fa-check-double"></i></span></div>
                            </div>
                        </div>
                        <div class="message message-received">
                            <div class="message-bubble">
                                <p>Merci pour l'information. Tiens-moi au courant.</p>
                                <div class="message-time">10:32</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Zone d'envoi -->
                <div class="message-input-area">
                    <div class="input-actions">
                        <button class="input-action-btn"><i class="fas fa-paperclip"></i></button>
                        <button class="input-action-btn"><i class="fas fa-image"></i></button>
                        <button class="input-action-btn"><i class="fas fa-microphone"></i></button>
                    </div>
                    <div class="message-input-wrapper">
                        <input type="text" class="message-input" placeholder="Écrire un message...">
                        <button class="send-btn"><i class="fas fa-paper-plane"></i></button>
                    </div>
                </div>
            </div>
        </div>

       <!-- Bannière application mobile -->
        <div class="app-banner">
            <div class="app-banner-content">
                <div class="app-banner-text">
                    <h4><i class="fas fa-mobile-alt mr-2"></i>Application mobile</h4>
                  
                </div>
                <div class="app-banner-buttons">
                    <button class="btn-new-conversation" onclick="openNewConversationModal()">
                        <i class="fas fa-plus mr-2"></i> Nouvelle conversation
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ========== MODALE NOUVELLE CONVERSATION ========== -->
<div class="modal fade" id="newConversationModal" tabindex="-1" role="dialog" aria-labelledby="newConversationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <!-- En-tête de la modale -->
            <div class="modal-header">
                <h5 class="modal-title" id="newConversationModalLabel">
                    <i class="fas fa-comment-dots text-success mr-2"></i>NOUVELLE CONVERSATION
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Corps de la modale -->
            <div class="modal-body">
                <!-- Barre de recherche -->
                <div class="search-people-bar">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Rechercher un éleveur..." id="searchElevage">
                </div>

                <!-- Résultats -->
                <div class="search-results" id="searchResults">
                    <div class="results-label"><i class="fas fa-users mr-2"></i>RÉSULTATS :</div>
                    
                    <!-- Résultat 1 -->
<div class="people-result" data-name="Amadou Sy" data-spec="Éleveur évent - Dakar">
    <div class="people-avatar">
        <div class="avatar-icon-wrapper" style="width: 48px; height: 48px; border-radius: 50%; background: #f0fdf4; display: flex; align-items: center; justify-content: center; border: 2px solid #198754;">
            <i class="fas fa-user" style="font-size: 24px; color: #198754;"></i>
        </div>
    </div>
    <div class="people-info">
        <div class="people-name">Amadeu Sy</div>
        <div class="people-spec">Éleveur évent - Dakar</div>
    </div>
    <button class="btn-contact" onclick="startConversation('Amadeu Sy')">
        <i class="fas fa-comment mr-1"></i> Contacter
    </button>
</div>

              <!-- Résultat 2 -->
<div class="people-result" data-name="Marie diop" data-spec="Éleveur évent - Dakar">
    <div class="people-avatar">
        <div class="avatar-icon-wrapper" style="width: 48px; height: 48px; border-radius: 50%; background: #f0fdf4; display: flex; align-items: center; justify-content: center; border: 2px solid #198754;">
            <i class="fas fa-user" style="font-size: 24px; color: #198754;"></i>
        </div>
    </div>
    <div class="people-info">
        <div class="people-name">Marie diop</div>
        <div class="people-spec">Éleveur caprine - Thiès</div>
    </div>
    <button class="btn-contact" onclick="startConversation('Marie diop')">
        <i class="fas fa-comment mr-1"></i> Contacter
    </button>
</div>

                 <!-- Résultat 3 -->
<div class="people-result" data-name="Ibrahim Fall" data-spec="Éleveur évent - Dakar">
    <div class="people-avatar">
        <div class="avatar-icon-wrapper" style="width: 48px; height: 48px; border-radius: 50%; background: #f0fdf4; display: flex; align-items: center; justify-content: center; border: 2px solid #198754;">
            <i class="fas fa-user" style="font-size: 24px; color: #198754;"></i>
        </div>
    </div>
    <div class="people-info">
        <div class="people-name">Ibrahim Fall</div>
        <div class="people-spec">Éleveur bovin - Saint-Louis</div>
    </div>
    <button class="btn-contact" onclick="startConversation('Ibrahim Fall')">
        <i class="fas fa-comment mr-1"></i> Contacter
    </button>
</div>

                <!-- Aucun résultat (caché par défaut) -->
                <div class="no-results" id="noResults" style="display: none;">
                    <i class="fas fa-user-slash"></i>
                    <p>Aucun éleveur trouvé</p>
                </div>

                
            </div>

            <!-- Pied de la modale -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>


@endsection

@push('scripts')
<script>
    /**
     * Ouvre la modale de nouvelle conversation
     * Réinitialise les champs de recherche
     */
    function openNewConversationModal() {
        $('#newConversationModal').modal('show');
        // Réinitialiser la recherche
        $('#searchElevage').val('');
        $('#searchResults').show();
        $('#noResults').hide();
        // Réafficher tous les résultats
        $('.people-result').show();
    }

    /**
     * Démarre une conversation avec l'éleveur sélectionné
     * @param {string} name - Nom de l'éleveur
     */
    function startConversation(name) {
        $('#newConversationModal').modal('hide');
        // Message de confirmation
        Swal.fire({
            icon: 'success',
            title: 'Conversation démarrée',
            text: 'Vous pouvez maintenant échanger avec ' + name,
            confirmButtonColor: '#198754',
            confirmButtonText: 'OK'
        });
        // Ici vous pouvez ajouter la logique pour ouvrir la conversation
        // Par exemple : window.location.href = '/messages/' + encodeURIComponent(name);
    }

    /**
     * Recherche dynamique dans la liste des éleveurs
     */
    $(document).ready(function() {
        $('#searchElevage').on('keyup', function() {
            const query = $(this).val().toLowerCase().trim();
            const results = $('.people-result');
            let hasResults = false;

            results.each(function() {
                const name = $(this).data('name').toLowerCase();
                const spec = $(this).data('spec').toLowerCase();
                
                // Recherche dans le nom ou la spécialité
                if (name.includes(query) || spec.includes(query)) {
                    $(this).show();
                    hasResults = true;
                } else {
                    $(this).hide();
                }
            });

            // Afficher ou cacher le message "aucun résultat"
            if (hasResults) {
                $('#searchResults').show();
                $('#noResults').hide();
            } else {
                $('#searchResults').hide();
                $('#noResults').show();
            }
        });

        // Gestion du clic sur le bouton Contacter (alternative)
        $('.btn-contact').on('click', function(e) {
            // Empêcher l'exécution multiple si onclick est déjà défini
            const name = $(this).closest('.people-result').find('.people-name').text();
            startConversation(name);
        });
    });
</script>
@endpush
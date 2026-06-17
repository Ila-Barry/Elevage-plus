@extends('layouts.menu')

@section('title', 'Paramètres - Élevage+')

@section('content')
<link rel="stylesheet" href="{{ asset('css/authCSS/parametre.css') }}">

<div class="parametre-wrapper">
    
    <!-- En-tête de la page -->
    <div class="parametre-header">
        <h1 class="parametre-title">
            <i class="fas fa-sliders-h"></i> Paramètres
        </h1>
        <p class="parametre-subtitle">Gérez la sécurité, les notifications et la confidentialité de votre compte</p>
    </div>

    <!-- Onglets de navigation paramètres -->
    <div class="parametre-tabs">
        <button class="tab-btn active" data-tab="securite">
            <i class="fas fa-lock"></i> Sécurité
        </button>
        <button class="tab-btn" data-tab="notifications">
            <i class="fas fa-bell"></i> Notifications
        </button>
        <button class="tab-btn" data-tab="confidentialite">
            <i class="fas fa-user-secret"></i> Confidentialité
        </button>
    </div>

    <!-- Contenu des onglets -->
    <div class="parametre-content">

        <!-- ==================== ONGLET SÉCURITÉ ==================== -->
        <div class="tab-pane active" id="securite-pane">
            
            <!-- Section Mot de passe -->
            <div class="settings-card">
                <div class="card-header">
                    <h3><i class="fas fa-key"></i> Sécurité et mot de passe</h3>
                </div>
                
                <div class="card-body">
                    <div class="form-group">
                        <label>Mot de passe actuel</label>
                        <div class="password-input-wrapper">
                            <input type="password" class="form-control" value="12345678" id="currentPassword" readonly disabled>
                            <i class="fas fa-eye-slash toggle-password" data-target="currentPassword"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Nouveau mot de passe</label>
                        <div class="password-input-wrapper">
                            <input type="password" class="form-control" placeholder="Entrez votre nouveau mot de passe" id="newPassword">
                            <i class="fas fa-eye-slash toggle-password" data-target="newPassword"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Confirmer le nouveau mot de passe</label>
                        <div class="password-input-wrapper">
                            <input type="password" class="form-control" placeholder="Confirmez votre nouveau mot de passe" id="confirmPassword">
                            <i class="fas fa-eye-slash toggle-password" data-target="confirmPassword"></i>
                        </div>
                    </div>

                    <div class="password-hint">
                        <i class="fas fa-info-circle"></i> Le mot de passe doit contenir au moins 6 caractères
                    </div>

                    <button class="btn btn-primary btn-change-password">
                        <i class="fas fa-save"></i> Changer le mot de passe
                    </button>
                </div>
            </div>

            <!-- Section 2FA -->
            <div class="settings-card">
                <div class="card-header">
                    <h3><i class="fas fa-shield-alt"></i> Authentification à deux facteurs (2FA)</h3>
                </div>
                <div class="card-body">
                    <p class="twofa-desc">Une couche de sécurité supplémentaire pour votre compte</p>
                    
                    <div class="twofa-toggle">
                        <span class="twofa-label">
                            <i class="fas fa-mobile-alt"></i> Activer l'authentification à deux facteurs
                        </span>
                        <label class="switch">
                            <input type="checkbox" id="twofaToggle">
                            <span class="slider round"></span>
                        </label>
                    </div>

                    <div class="twofa-info" style="display: none;">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Vous recevrez un code de vérification par SMS ou application d'authentification
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== ONGLET NOTIFICATIONS ==================== -->
        <div class="tab-pane" id="notifications-pane">
            <div class="settings-card">
                <div class="card-header">
                    <h3><i class="fas fa-bell"></i> Préférences de notifications</h3>
                </div>
                
                <div class="card-body">
                    <div class="notifications-list">
                        <label class="notification-item">
                            <input type="checkbox" checked class="notif-checkbox">
                            <span class="notif-text">
                                <i class="fas fa-envelope"></i> Recevoir une notification quand on me contacte
                            </span>
                        </label>

                        <label class="notification-item">
                            <input type="checkbox" checked class="notif-checkbox">
                            <span class="notif-text">
                                <i class="fas fa-newspaper"></i> Recevoir une notification quand on demande ma publication
                            </span>
                        </label>

                        <label class="notification-item">
                            <input type="checkbox" class="notif-checkbox">
                            <span class="notif-text">
                                <i class="fas fa-comments"></i> Recevoir une notification quand on me communique
                            </span>
                        </label>

                        <label class="notification-item">
                            <input type="checkbox" checked class="notif-checkbox">
                            <span class="notif-text">
                                <i class="fas fa-tasks"></i> Recevoir des rappels de tâches (vaccinations, etc.)
                            </span>
                        </label>

                        <label class="notification-item">
                            <input type="checkbox" class="notif-checkbox">
                            <span class="notif-text">
                                <i class="fas fa-envelope-open-text"></i> Recevoir la newsletter hebdomadaire
                            </span>
                        </label>

                        <label class="notification-item">
                            <input type="checkbox" checked class="notif-checkbox">
                            <span class="notif-text">
                                <i class="fas fa-exclamation-triangle"></i> Recevoir des alertes sanitaires de la région
                            </span>
                        </label>
                    </div>

                    <div class="reception-mode">
                        <label class="mode-label">Mode de réception :</label>
                        <div class="mode-options">
                            <label class="radio-option">
                                <input type="radio" name="receptionMode" checked>
                                <span><i class="fas fa-envelope"></i> Email et notifications web</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="receptionMode">
                                <span><i class="fas fa-envelope"></i> Email uniquement</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="receptionMode">
                                <span><i class="fas fa-globe"></i> Web uniquement</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== ONGLET CONFIDENTIALITÉ ==================== -->
        <div class="tab-pane" id="confidentialite-pane">
            <div class="settings-card">
                <div class="card-header">
                    <h3><i class="fas fa-eye"></i> Visibilité du profil</h3>
                </div>
                <div class="card-body">
                    <div class="visibility-options">
                        <label class="radio-option-card">
                            <input type="radio" name="visibilite" value="public" checked>
                            <div class="option-content">
                                <i class="fas fa-globe"></i>
                                <div>
                                    <strong>Public</strong>
                                    <p>Tout le monde peut voir mon profil</p>
                                </div>
                            </div>
                        </label>

                        <label class="radio-option-card">
                            <input type="radio" name="visibilite" value="prive">
                            <div class="option-content">
                                <i class="fas fa-lock"></i>
                                <div>
                                    <strong>Privé</strong>
                                    <p>Seuls les éleveurs connectés peuvent voir mon profil</p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="settings-card">
                <div class="card-header">
                    <h3><i class="fas fa-comment-dots"></i> Messagerie</h3>
                </div>
                <div class="card-body">
                    <div class="messaging-options">
                        <label class="radio-option-card">
                            <input type="radio" name="messagerie" value="tous" checked>
                            <div class="option-content">
                                <i class="fas fa-users"></i>
                                <div>
                                    <strong>Tout éleveur peut me contacter</strong>
                                </div>
                            </div>
                        </label>

                        <label class="radio-option-card">
                            <input type="radio" name="messagerie" value="suivis">
                            <div class="option-content">
                                <i class="fas fa-user-friends"></i>
                                <div>
                                    <strong>Seuls les éleveurs que je suis peuvent me contacter</strong>
                                </div>
                            </div>
                        </label>

                        <label class="radio-option-card">
                            <input type="radio" name="messagerie" value="personne">
                            <div class="option-content">
                                <i class="fas fa-ban"></i>
                                <div>
                                    <strong>Personne ne peut me contacter</strong>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Zone Danger -->
            <div class="settings-card danger-zone">
                <div class="card-header">
                    <h3><i class="fas fa-exclamation-triangle"></i> Zone Danger</h3>
                </div>
                <div class="card-body">
                    <div class="danger-content">
                        <div class="danger-icon">
                            <i class="fas fa-trash-alt"></i>
                        </div>
                        <div class="danger-text">
                            <strong>Supprimer mon compte</strong>
                            <p>Cette action est irréversible. Toutes vos données (animaux, stocks, messages) seront définitivement supprimées.</p>
                        </div>
                        <button class="btn btn-danger btn-delete-account">
                            <i class="fas fa-trash"></i> Supprimer mon compte
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions globales -->
        <div class="form-actions">
            <button class="btn btn-secondary btn-cancel">
                <i class="fas fa-times"></i> Annuler
            </button>
            <button class="btn btn-primary btn-save-all">
                <i class="fas fa-save"></i> Enregistrer
            </button>
        </div>

    </div>
</div>

<!-- Scripts spécifiques -->
@push('scripts')
<script>
    $(document).ready(function() {
        
        // ========== GESTION DES ONGLETS ==========
        $('.tab-btn').on('click', function() {
            // Retirer la classe active de tous les onglets
            $('.tab-btn').removeClass('active');
            // Ajouter la classe active à l'onglet cliqué
            $(this).addClass('active');
            
            // Récupérer la cible
            var target = $(this).data('tab');
            
            // Cacher tous les panes
            $('.tab-pane').removeClass('active');
            // Afficher le pane correspondant
            $('#' + target + '-pane').addClass('active');
        });
        
        // ========== TOGGLE MOT DE PASSE (AFFICHER/MASQUER) ==========
        $('.toggle-password').on('click', function() {
            var targetId = $(this).data('target');
            var input = $('#' + targetId);
            var icon = $(this);
            
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            }
        });
        
        // ========== TOGGLE 2FA ==========
        $('#twofaToggle').on('change', function() {
            if($(this).is(':checked')) {
                $('.twofa-info').slideDown();
            } else {
                $('.twofa-info').slideUp();
            }
        });
        
        // ========== ACTIONS BOUTONS ==========
        $('.btn-change-password').on('click', function() {
            var newPass = $('#newPassword').val();
            var confirmPass = $('#confirmPassword').val();
            
            if(newPass.length < 6 && newPass !== '') {
                alert('Le mot de passe doit contenir au moins 6 caractères');
                return;
            }
            
            if(newPass !== confirmPass) {
                alert('Les mots de passe ne correspondent pas');
                return;
            }
            
            if(newPass === '') {
                alert('Veuillez entrer un nouveau mot de passe');
                return;
            }
            
            alert('Mot de passe modifié avec succès !');
            $('#newPassword, #confirmPassword').val('');
        });
        
        $('.btn-delete-account').on('click', function() {
            if(confirm('ATTENTION : Cette action est irréversible. Voulez-vous vraiment supprimer votre compte ?')) {
                alert('Fonctionnalité à implémenter avec le backend');
            }
        });
        
        $('.btn-save-all').on('click', function() {
            alert('Tous les paramètres ont été sauvegardés !');
        });
        
        $('.btn-cancel').on('click', function() {
            if(confirm('Annuler les modifications ?')) {
                location.reload();
            }
        });
    });
</script>
@endpush

@endsection
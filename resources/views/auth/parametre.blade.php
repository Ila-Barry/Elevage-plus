@extends('layouts.menu')

@section('title', 'Paramètres - Élevage+')

@section('content')
<link rel="stylesheet" href="{{ asset('css/authCSS/parametre.css') }}">

<div class="dashboard-wrapper">
    <div class="settings-container">
        
        <!-- En-tête -->
        <div class="settings-header">
            <div class="settings-title-section">
                <i class="fas fa-sliders-h"></i>
                <h1>Paramètres</h1>
            </div>
            <div class="settings-subtitle">
                <p>Gérez vos paramètres ici !</p>
            </div>
        </div>

        <!-- Navigation des onglets -->
        <div class="settings-tabs">
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
        <div class="settings-content">
            
            <!-- ==================== ONGLET SÉCURITÉ ==================== -->
            <div class="tab-pane active" id="securite">
                
                <!-- TOUT DANS UNE SEULE COLONNE -->
                
                <!-- 1. Sécurité et mot de passe -->
                <div class="settings-card">
                    <div class="card-header">
                        <i class="fas fa-lock"></i>
                        <h3>Sécurité et mot de passe</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Mot de passe actuel <span class="required">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" placeholder="......" id="currentPassword">
                                <button type="button" class="toggle-password" data-target="currentPassword">
                                    <i class="far fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Nouveau mot de passe</label>
                            <div class="input-group">
                                <input type="password" class="form-control" placeholder="......" id="newPassword">
                                <button type="button" class="toggle-password" data-target="newPassword">
                                    <i class="far fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Confirmer le nouveau mot de passe</label>
                            <div class="input-group">
                                <input type="password" class="form-control" placeholder="......" id="confirmPassword">
                                <button type="button" class="toggle-password" data-target="confirmPassword">
                                    <i class="far fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="password-hint">
                            <i class="fas fa-info-circle"></i>
                            <span>Le mot de passe doit contenir au moins 8 caractères</span>
                        </div>
                        <button type="submit" class="btn-primary" id="changePasswordBtn">
                            Changer le mot de passe
                        </button>
                    </div>
                </div>

                <!-- 2. Authentification à deux facteurs (2FA) -->
                <div class="settings-card">
                    <div class="card-header">
                        <i class="fas fa-shield-alt"></i>
                        <h3>Authentification à deux facteurs (2FA)</h3>
                    </div>
                    <div class="card-body">
                        <div class="twofa-content">
                            <div class="twofa-text">
                                <i class="fas fa-mobile-alt"></i>
                                <span>Une couche de sécurité supplémentaire pour votre compte</span>
                            </div>
                            <button class="btn-2fa" id="activate2faBtn">
                                <i class="fas fa-plus-circle"></i> Activer
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 3. Application mobile -->
                <div class="settings-card">
                    <div class="card-header">
                        <i class="fas fa-mobile-alt"></i>
                        <h3>Application mobile</h3>
                    </div>
                    <div class="card-body">
                        <div class="mobile-content">
                            <div class="qr-placeholder">
                                <i class="fas fa-qrcode"></i>
                            </div>
                            <div class="mobile-text">
                                <p>Gérez votre élevage partout, à tout moment</p>
                                <div class="store-buttons">
                                    <button class="store-btn"><i class="fab fa-apple"></i> App Store</button>
                                    <button class="store-btn"><i class="fab fa-google-play"></i> Google Play</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==================== ONGLET NOTIFICATIONS ==================== -->
            <div class="tab-pane" id="notifications">
                <div class="settings-card">
                    <div class="card-header">
                        <i class="fas fa-bell"></i>
                        <h3>Notifications</h3>
                    </div>
                    <div class="card-body">
                        <div class="notifications-list">
                            <label class="checkbox-item">
                                <input type="checkbox" checked>
                                <span class="checkmark"></span>
                                <span>Recevoir une notification quand on me contacte</span>
                            </label>
                            <label class="checkbox-item">
                                <input type="checkbox" checked>
                                <span class="checkmark"></span>
                                <span>Recevoir une notification quand on commente ma publication</span>
                            </label>
                            <label class="checkbox-item">
                                <input type="checkbox" checked>
                                <span class="checkmark"></span>
                                <span>Recevoir une notification quand on like ma publication</span>
                            </label>
                            <label class="checkbox-item">
                                <input type="checkbox" checked>
                                <span class="checkmark"></span>
                                <span>Recevoir des rappels de tâches (vaccinations, etc.)</span>
                            </label>
                            <label class="checkbox-item">
                                <input type="checkbox">
                                <span class="checkmark"></span>
                                <span>Recevoir la newsletter hebdomadaire</span>
                            </label>
                            <label class="checkbox-item">
                                <input type="checkbox" checked>
                                <span class="checkmark"></span>
                                <span>Recevoir des alertes sanitaires de la région</span>
                            </label>
                        </div>

                        <div class="reception-mode">
                            <label class="reception-label">Mode de réception :</label>
                            <div class="radio-group">
                                <label class="radio-item">
                                    <input type="radio" name="receptionMode" checked>
                                    <span class="radio-custom"></span>
                                    <span>Email et notifications web</span>
                                </label>
                                <label class="radio-item">
                                    <input type="radio" name="receptionMode">
                                    <span class="radio-custom"></span>
                                    <span>Email uniquement</span>
                                </label>
                                <label class="radio-item">
                                    <input type="radio" name="receptionMode">
                                    <span class="radio-custom"></span>
                                    <span>Web uniquement</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==================== ONGLET CONFIDENTIALITÉ ==================== -->
            <div class="tab-pane" id="confidentialite">
                <div class="settings-card">
                    <div class="card-header">
                        <i class="fas fa-user-secret"></i>
                        <h3>Confidentialité et visibilité</h3>
                    </div>
                    <div class="card-body">
                        <!-- Visibilité du profil -->
                        <div class="confidentialite-section">
                            <label class="section-label">Visibilité du profil :</label>
                            <div class="radio-group">
                                <label class="radio-item">
                                    <input type="radio" name="visibility">
                                    <span class="radio-custom"></span>
                                    <div class="radio-text">
                                        <strong>Public</strong>
                                        <span>Tout le monde peut voir mon profil</span>
                                    </div>
                                </label>
                                <label class="radio-item">
                                    <input type="radio" name="visibility" checked>
                                    <span class="radio-custom"></span>
                                    <div class="radio-text">
                                        <strong>Privé</strong>
                                        <span>Seuls les éleveurs connectés peuvent voir mon profil</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Messagerie -->
                        <div class="confidentialite-section">
                            <label class="section-label">Messagerie :</label>
                            <div class="radio-group">
                                <label class="radio-item">
                                    <input type="radio" name="messaging" checked>
                                    <span class="radio-custom"></span>
                                    <span>Tout éleveur peut me contacter</span>
                                </label>
                                <label class="radio-item">
                                    <input type="radio" name="messaging">
                                    <span class="radio-custom"></span>
                                    <span>Seuls les éleveurs que je suis peuvent me contacter</span>
                                </label>
                                <label class="radio-item">
                                    <input type="radio" name="messaging">
                                    <span class="radio-custom"></span>
                                    <span>Personne ne peut me contacter</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Zone Danger -->
                <div class="danger-zone">
                    <div class="danger-header">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h3>Supprimer mon compte</h3>
                    </div>
                    <div class="danger-body">
                        <p>Cette action est irréversible. Toutes vos données (animaux, publications, messages) seront définitivement supprimées.</p>
                        <div class="danger-buttons">
                            <button class="btn-outline" id="cancelDeleteBtn">Annuler</button>
                            <button class="btn-danger" id="confirmDeleteBtn">Supprimer</button>
                        </div>
                    </div>
                </div>

                <div class="action-buttons">
                    <button class="btn-outline">Annuler</button>
                    <button class="btn-primary">Enregistrer</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Gestion des onglets
        $('.tab-btn').on('click', function() {
            var tabId = $(this).data('tab');
            $('.tab-btn').removeClass('active');
            $(this).addClass('active');
            $('.tab-pane').removeClass('active');
            $('#' + tabId).addClass('active');
        });

        // Toggle mot de passe
        $('.toggle-password').on('click', function() {
            var target = $(this).data('target');
            var input = $('#' + target);
            var icon = $(this).find('i');
            
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // Changer mot de passe
        $('#changePasswordBtn').on('click', function() {
            var newPwd = $('#newPassword').val();
            var confirmPwd = $('#confirmPassword').val();
            if(newPwd !== confirmPwd) {
                alert('Les nouveaux mots de passe ne correspondent pas');
                return;
            }
            alert('Fonctionnalité à implémenter');
        });

        // Activer 2FA
        $('#activate2faBtn').on('click', function() {
            alert('Configuration 2FA à implémenter');
        });

        // Suppression
        $('#confirmDeleteBtn').on('click', function() {
            if(confirm('Supprimer définitivement votre compte ?')) {
                alert('Suppression à implémenter');
            }
        });

        $('#cancelDeleteBtn').on('click', function() {
            alert('Opération annulée');
        });
    });
</script>

@endsection
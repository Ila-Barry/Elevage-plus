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
                        <div id="passwordStrength" style="margin-top: 8px; display: none;">
                            <div style="display: flex; gap: 8px; align-items: center;">
                                <span style="font-size: 0.8rem; color: #6c757d;">Force du mot de passe :</span>
                                <div id="strengthBar" style="flex: 1; height: 6px; background: #e9ecef; border-radius: 4px; overflow: hidden;">
                                    <div id="strengthFill" style="width: 0%; height: 100%; border-radius: 4px; transition: width 0.3s;"></div>
                                </div>
                                <span id="strengthText" style="font-size: 0.75rem; font-weight: 600; min-width: 50px;">Faible</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Confirmer le nouveau mot de passe</label>
                        <div class="password-input-wrapper">
                            <input type="password" class="form-control" placeholder="Confirmez votre nouveau mot de passe" id="confirmPassword">
                            <i class="fas fa-eye-slash toggle-password" data-target="confirmPassword"></i>
                        </div>
                        <div id="passwordMatch" style="margin-top: 6px; font-size: 0.8rem; display: none;"></div>
                    </div>

                    <div class="password-hint">
                        <i class="fas fa-info-circle"></i> Le mot de passe doit contenir au moins 6 caractères
                    </div>

                    <button class="btn btn-primary btn-change-password" id="changePasswordBtn">
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
                        <div style="margin-top: 12px;">
                            <button class="btn btn-secondary" id="setup2faBtn" style="display: none;">
                                <i class="fas fa-qrcode"></i> Configurer 2FA
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section Sessions actives -->
            <div class="settings-card">
                <div class="card-header">
                    <h3><i class="fas fa-desktop"></i> Sessions actives</h3>
                </div>
                <div class="card-body">
                    <div id="activeSessions">
                        <div class="session-item">
                            <div class="session-info">
                                <i class="fas fa-desktop"></i>
                                <div>
                                    <strong>Chrome - Windows</strong>
                                    <p>Paris, France - Actuelle</p>
                                </div>
                            </div>
                            <span class="badge badge-success">Actif</span>
                        </div>
                        <div class="session-item">
                            <div class="session-info">
                                <i class="fas fa-mobile-alt"></i>
                                <div>
                                    <strong>Application mobile - Android</strong>
                                    <p>Dakar, Sénégal - Il y a 2 heures</p>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-danger revoke-session">Révoquer</button>
                        </div>
                        <div class="session-item">
                            <div class="session-info">
                                <i class="fas fa-tablet-alt"></i>
                                <div>
                                    <strong>Safari - iPad</strong>
                                    <p>Thiès, Sénégal - Il y a 1 jour</p>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-danger revoke-session">Révoquer</button>
                        </div>
                    </div>
                    <button class="btn btn-secondary" id="revokeAllSessions" style="margin-top: 12px;">
                        <i class="fas fa-power-off"></i> Révoquer toutes les sessions
                    </button>
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
                            <input type="checkbox" checked class="notif-checkbox" data-notif="messages">
                            <span class="notif-text">
                                <i class="fas fa-envelope"></i> Recevoir une notification quand on me contacte
                            </span>
                        </label>

                        <label class="notification-item">
                            <input type="checkbox" checked class="notif-checkbox" data-notif="publications">
                            <span class="notif-text">
                                <i class="fas fa-newspaper"></i> Recevoir une notification quand on demande ma publication
                            </span>
                        </label>

                        <label class="notification-item">
                            <input type="checkbox" class="notif-checkbox" data-notif="comments">
                            <span class="notif-text">
                                <i class="fas fa-comments"></i> Recevoir une notification quand on me communique
                            </span>
                        </label>

                        <label class="notification-item">
                            <input type="checkbox" checked class="notif-checkbox" data-notif="tasks">
                            <span class="notif-text">
                                <i class="fas fa-tasks"></i> Recevoir des rappels de tâches (vaccinations, etc.)
                            </span>
                        </label>

                        <label class="notification-item">
                            <input type="checkbox" class="notif-checkbox" data-notif="newsletter">
                            <span class="notif-text">
                                <i class="fas fa-envelope-open-text"></i> Recevoir la newsletter hebdomadaire
                            </span>
                        </label>

                        <label class="notification-item">
                            <input type="checkbox" checked class="notif-checkbox" data-notif="alerts">
                            <span class="notif-text">
                                <i class="fas fa-exclamation-triangle"></i> Recevoir des alertes sanitaires de la région
                            </span>
                        </label>
                    </div>

                    <div class="reception-mode">
                        <label class="mode-label">Mode de réception :</label>
                        <div class="mode-options">
                            <label class="radio-option">
                                <input type="radio" name="receptionMode" value="both" checked>
                                <span><i class="fas fa-envelope"></i> Email et notifications web</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="receptionMode" value="email">
                                <span><i class="fas fa-envelope"></i> Email uniquement</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="receptionMode" value="web">
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

                        <label class="radio-option-card">
                            <input type="radio" name="visibilite" value="contacts">
                            <div class="option-content">
                                <i class="fas fa-user-friends"></i>
                                <div>
                                    <strong>Mes contacts uniquement</strong>
                                    <p>Seuls les éleveurs que j'ai ajoutés peuvent voir mon profil</p>
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
                        <button class="btn btn-danger btn-delete-account" id="deleteAccountBtn">
                            <i class="fas fa-trash"></i> Supprimer mon compte
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions globales -->
        <div class="form-actions">
            <button class="btn btn-secondary btn-cancel" id="cancelBtn">
                <i class="fas fa-times"></i> Annuler
            </button>
            <button class="btn btn-primary btn-save-all" id="saveAllBtn">
                <i class="fas fa-save"></i> Enregistrer
            </button>
        </div>

    </div>
</div>

<script>
// ================= VARIABLES GLOBALES =================
let toastTimeout = null;
let hasChanges = false;
let passwordStrength = 0;

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

// ================= GESTION DES ONGLETS =================
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        const target = this.dataset.tab;
        document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
        document.getElementById(target + '-pane').classList.add('active');
    });
});

// ================= FORCE DU MOT DE PASSE =================
function checkPasswordStrength(password) {
    let score = 0;
    
    if (password.length >= 6) score++;
    if (password.length >= 10) score++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
    if (/\d/.test(password)) score++;
    if (/[^a-zA-Z0-9]/.test(password)) score++;
    
    return score;
}

function updateStrengthDisplay(strength) {
    const container = document.getElementById('passwordStrength');
    const fill = document.getElementById('strengthFill');
    const text = document.getElementById('strengthText');
    
    if (strength === 0) {
        container.style.display = 'none';
        return;
    }
    
    container.style.display = 'flex';
    
    const percentages = [0, 20, 40, 60, 80, 100];
    const colors = ['#dc3545', '#dc3545', '#ffc107', '#ffc107', '#28a745', '#28a745'];
    const labels = ['Faible', 'Faible', 'Moyen', 'Moyen', 'Fort', 'Très fort'];
    
    const index = Math.min(strength, 5);
    fill.style.width = percentages[index] + '%';
    fill.style.background = colors[index];
    text.textContent = labels[index];
    text.style.color = colors[index];
}

// ================= VÉRIFICATION DE LA CONFIRMATION =================
function checkPasswordMatch() {
    const newPass = document.getElementById('newPassword').value;
    const confirmPass = document.getElementById('confirmPassword').value;
    const matchDiv = document.getElementById('passwordMatch');
    
    if (confirmPass === '') {
        matchDiv.style.display = 'none';
        return;
    }
    
    matchDiv.style.display = 'block';
    
    if (newPass === confirmPass) {
        matchDiv.innerHTML = '<i class="fas fa-check-circle" style="color: #28a745;"></i> Les mots de passe correspondent';
        matchDiv.style.color = '#28a745';
    } else {
        matchDiv.innerHTML = '<i class="fas fa-times-circle" style="color: #dc3545;"></i> Les mots de passe ne correspondent pas';
        matchDiv.style.color = '#dc3545';
    }
}

// ================= TOGGLE MOT DE PASSE =================
document.querySelectorAll('.toggle-password').forEach(btn => {
    btn.addEventListener('click', function() {
        const targetId = this.dataset.target;
        const input = document.getElementById(targetId);
        
        if (input.type === 'password') {
            input.type = 'text';
            this.classList.remove('fa-eye-slash');
            this.classList.add('fa-eye');
        } else {
            input.type = 'password';
            this.classList.remove('fa-eye');
            this.classList.add('fa-eye-slash');
        }
    });
});

// ================= ÉVÉNEMENTS MOT DE PASSE =================
document.getElementById('newPassword').addEventListener('input', function() {
    const strength = checkPasswordStrength(this.value);
    passwordStrength = strength;
    updateStrengthDisplay(strength);
    checkPasswordMatch();
});

document.getElementById('confirmPassword').addEventListener('input', checkPasswordMatch);

// ================= CHANGEMENT DE MOT DE PASSE =================
document.getElementById('changePasswordBtn').addEventListener('click', function() {
    const newPass = document.getElementById('newPassword').value;
    const confirmPass = document.getElementById('confirmPassword').value;
    
    if (newPass === '') {
        showToast('Veuillez entrer un nouveau mot de passe', 'warning');
        return;
    }
    
    if (newPass.length < 6) {
        showToast('Le mot de passe doit contenir au moins 6 caractères', 'warning');
        return;
    }
    
    if (newPass !== confirmPass) {
        showToast('Les mots de passe ne correspondent pas', 'danger');
        return;
    }
    
    if (passwordStrength < 2) {
        showToast('Votre mot de passe est trop faible. Utilisez des majuscules, chiffres et caractères spéciaux.', 'warning');
        return;
    }
    
    showToast('Mot de passe modifié avec succès !', 'success');
    document.getElementById('newPassword').value = '';
    document.getElementById('confirmPassword').value = '';
    document.getElementById('passwordMatch').style.display = 'none';
    document.getElementById('passwordStrength').style.display = 'none';
});

// ================= 2FA TOGGLE =================
document.getElementById('twofaToggle').addEventListener('change', function() {
    const info = document.querySelector('.twofa-info');
    const setupBtn = document.getElementById('setup2faBtn');
    
    if (this.checked) {
        info.style.display = 'block';
        setTimeout(() => {
            setupBtn.style.display = 'inline-flex';
        }, 300);
        showToast('2FA activé avec succès !', 'success');
    } else {
        info.style.display = 'none';
        setupBtn.style.display = 'none';
        showToast('2FA désactivé', 'info');
    }
});

document.getElementById('setup2faBtn').addEventListener('click', function() {
    showToast('📱 Configuration 2FA : Scannez le QR code avec votre application d\'authentification', 'info');
});

// ================= SESSIONS ACTIVES =================
document.querySelectorAll('.revoke-session').forEach(btn => {
    btn.addEventListener('click', function() {
        const session = this.closest('.session-item');
        if (confirm('Voulez-vous révoquer cette session ?')) {
            session.style.opacity = '0';
            setTimeout(() => {
                session.remove();
                showToast('Session révoquée avec succès', 'success');
            }, 300);
        }
    });
});

document.getElementById('revokeAllSessions').addEventListener('click', function() {
    if (confirm('Voulez-vous révoquer toutes les sessions actives (sauf celle-ci) ?')) {
        const sessions = document.querySelectorAll('.session-item');
        sessions.forEach((session, index) => {
            if (index > 0) {
                setTimeout(() => {
                    session.style.opacity = '0';
                    setTimeout(() => session.remove(), 300);
                }, index * 100);
            }
        });
        setTimeout(() => {
            showToast('Toutes les sessions ont été révoquées', 'success');
        }, sessions.length * 100 + 300);
    }
});

// ================= SAUVEGARDE DES PARAMÈTRES =================
function saveAllSettings() {
    // Récupérer les notifications
    const notifSettings = {};
    document.querySelectorAll('.notif-checkbox').forEach(cb => {
        notifSettings[cb.dataset.notif] = cb.checked;
    });
    
    // Récupérer le mode de réception
    const receptionMode = document.querySelector('input[name="receptionMode"]:checked');
    const mode = receptionMode ? receptionMode.value : 'both';
    
    // Récupérer la visibilité
    const visibilite = document.querySelector('input[name="visibilite"]:checked');
    const vis = visibilite ? visibilite.value : 'public';
    
    // Récupérer les préférences de messagerie
    const messagerie = document.querySelector('input[name="messagerie"]:checked');
    const msg = messagerie ? messagerie.value : 'tous';
    
    // Afficher un résumé
    console.log('Paramètres sauvegardés :', {
        notifications: notifSettings,
        receptionMode: mode,
        visibilite: vis,
        messagerie: msg,
        twofa: document.getElementById('twofaToggle').checked
    });
    
    showToast('Tous les paramètres ont été sauvegardés avec succès !', 'success');
    hasChanges = false;
}

document.getElementById('saveAllBtn').addEventListener('click', saveAllSettings);

// ================= ANNULATION =================
document.getElementById('cancelBtn').addEventListener('click', function() {
    if (hasChanges) {
        if (confirm('Voulez-vous vraiment annuler toutes les modifications ?')) {
            location.reload();
        }
    } else {
        showToast('Aucune modification à annuler', 'info');
    }
});

// ================= SUPPRESSION DE COMPTE =================
document.getElementById('deleteAccountBtn').addEventListener('click', function() {
    // Demander une confirmation en plusieurs étapes
    const confirm1 = confirm('⚠️ ATTENTION : Cette action est irréversible. Voulez-vous vraiment supprimer votre compte ?');
    if (!confirm1) return;
    
    const confirm2 = confirm('Êtes-vous absolument sûr ? Toutes vos données seront perdues.');
    if (!confirm2) return;
    
    const reason = prompt('Pourquoi souhaitez-vous supprimer votre compte ? (Optionnel)');
    
    showToast('Votre compte a été supprimé avec succès. Nous sommes tristes de vous voir partir. 🙁', 'danger');
    
    // Redirection après 3 secondes
    setTimeout(() => {
        window.location.href = '/';
    }, 3000);
});

// ================= DÉTECTION DES CHANGEMENTS =================
document.querySelectorAll('input, select, textarea').forEach(el => {
    el.addEventListener('change', function() {
        hasChanges = true;
    });
});

// ================= INITIALISATION =================
document.addEventListener('DOMContentLoaded', function() {
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
        .custom-toast.success .toast-content { background: #28a745; }
        .custom-toast.danger .toast-content { background: #dc3545; }
        .custom-toast.warning .toast-content { background: #ffc107; color: #343a40; }
        .custom-toast.info .toast-content { background: #0dcaf0; color: #343a40; }
        
        @media (max-width: 768px) {
            .custom-toast {
                left: 15px;
                right: 15px;
                bottom: 15px;
                transform: translateY(100px);
            }
            .custom-toast.show { transform: translateY(0); }
        }
        
        .session-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #edf2f7;
            transition: opacity 0.3s ease;
        }
        .session-item:last-child {
            border-bottom: none;
        }
        .session-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .session-info i {
            font-size: 1.2rem;
            color: #28a745;
            width: 24px;
        }
        .session-info strong {
            display: block;
            font-size: 0.9rem;
            color: #2d3748;
        }
        .session-info p {
            margin: 0;
            font-size: 0.8rem;
            color: #718096;
        }
        .badge-success {
            background: #28a745;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
        }
        .btn-sm {
            padding: 4px 12px;
            font-size: 0.8rem;
        }
        .btn-outline-danger {
            background: transparent;
            border: 1px solid #dc3545;
            color: #dc3545;
        }
        .btn-outline-danger:hover {
            background: #dc3545;
            color: white;
        }
    `;
    document.head.appendChild(style);
});

// ================= RACCOURCIS CLAVIER =================
document.addEventListener('keydown', function(e) {
    // Ctrl+S pour sauvegarder
    if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        saveAllSettings();
    }
    
    // Escape pour annuler
    if (e.key === 'Escape') {
        document.getElementById('cancelBtn').click();
    }
});
</script>

@endsection
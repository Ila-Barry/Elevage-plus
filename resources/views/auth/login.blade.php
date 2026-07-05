{{-- resources/views/auth/login.blade.php --}}

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Élevage+</title>
    
    <!-- CSRF Token pour les requêtes AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link rel="icon" href="{{ asset('images/logoE.png') }}" type="image/png">

    <!-- Bootstrap 4.6 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/authCSS/login.css') }}">
</head>
<body>
<div class="login-container">
    <!-- Logo et en-tête -->
    <div class="login-header">
        <a href="{{ url('/') }}">
            <div class="logo">
                <img class="img-logo" src="{{ asset('images/logoE.png') }}" alt="Logo">
                <span>ÉLEVAGE+</span>
            </div>
        </a>
        <p class="login-subtitle">Pas encore inscrit ? 
            <a href="{{ url('/auth/register') }}" class="signup-link">S'inscrire</a>
        </p>
    </div>

    <div class="login-card mx-auto">
        <div class="logo"><img src="{{ asset('images/logoE.png') }}" alt="Logo Élevage+"></div>
        
        <!-- Titre du formulaire -->
        <div class="form-title">
            <i class="fas fa-sign-in-alt"></i>
            <span>SE CONNECTER À MON COMPTE</span>
        </div>

        <!-- Message d'erreur général -->
        <div id="errorMessage" class="alert alert-danger" style="display: none;">
            <i class="fas fa-exclamation-circle"></i> <span id="errorText"></span>
        </div>

        <!-- Message de succès -->
        <div id="successMessage" class="alert alert-success" style="display: none;">
            <i class="fas fa-check-circle"></i> <span id="successText"></span>
        </div>

        <!-- Formulaire de connexion -->
        <form class="login-form" id="loginForm">
            @csrf

            <!-- Champ Email / Téléphone -->
            <div class="input-group-custom" id="emailGroup">
                <div class="input-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <input type="text" name="email" id="loginInput" placeholder="Adresse e-mail ou Téléphone" required autofocus>
                <div class="input-validation" style="display: none;">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="field-error" id="loginError" style="display: none;"></div>

            <!-- Champ Mot de passe -->
            <div class="input-group-custom" id="passwordGroup">
                <div class="input-icon">
                    <i class="fas fa-lock"></i>
                </div>
                <input type="password" name="password" id="passwordInput" placeholder="Mot de passe" required>
                <button type="button" class="toggle-password" id="togglePassword">
                    <i class="fas fa-eye"></i>
                </button>
                <div class="input-validation" style="display: none;">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="field-error" id="passwordError" style="display: none;"></div>

            <!-- Indicateur de force du mot de passe -->
            <div class="password-strength" id="passwordStrength" style="display: none;">
                <div class="strength-bar"></div>
                <div class="strength-bar"></div>
                <div class="strength-bar"></div>
                <div class="strength-bar"></div>
                <span class="strength-text"></span>
            </div>

            <!-- Options supplémentaires -->
            <div class="form-options">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember" id="rememberCheckbox">
                    <span class="checkmark"></span>
                    Se souvenir de moi
                </label>
                <a href="#" class="forgot-link" id="forgotPasswordLink">Mot de passe oublié ?</a>
            </div>

            <!-- Bouton de connexion avec loader -->
            <button type="submit" class="login-btn" id="loginBtn">
                <span class="btn-text">Se connecter</span>
                <span class="btn-loader" style="display: none;">
                    <i class="fas fa-spinner fa-spin"></i>
                </span>
            </button>
        </form>

        <!-- Séparateur -->
        <div class="divider">
            <span>Ou</span>
        </div>

        <!-- Connexion avec réseaux sociaux -->
        <div class="social-login">
            <button class="social-btn google" id="googleLogin">
                <i class="fab fa-google"></i>
                Google
            </button>
            <button class="social-btn facebook" id="facebookLogin">
                <i class="fab fa-facebook-f"></i>
                Facebook
            </button>
            <button class="social-btn instagram" id="instagramLogin">
                <i class="fab fa-instagram"></i>
                Instagram
            </button>
        </div>

        <!-- Lien d'inscription en bas -->
        <div class="signup-footer">
            Pas de compte ? <a href="{{ url('/auth/register') }}">S'inscrire</a>
        </div>
    </div>
</div>

<!-- Modal Mot de passe oublié -->
<div class="custom-modal" id="forgotPasswordModal" style="display: none;">
    <div class="modal-overlay"></div>
    <div class="modal-container">
        <div class="modal-header">
            <h3><i class="fas fa-key"></i> Réinitialisation du mot de passe</h3>
            <button class="modal-close" id="closeModalBtn"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <p>Entrez votre adresse e-mail pour recevoir un lien de réinitialisation.</p>
            <div class="input-group-custom" style="margin-top: 20px;">
                <div class="input-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <input type="email" id="resetEmail" placeholder="Votre adresse e-mail">
            </div>
            <div id="resetError" class="field-error" style="display: none;"></div>
            <div id="resetSuccess" class="alert alert-success" style="display: none;"></div>
            <button class="login-btn" id="sendResetLinkBtn" style="margin-top: 20px;">
                <span class="btn-text">Envoyer le lien</span>
                <span class="btn-loader" style="display: none;">
                    <i class="fas fa-spinner fa-spin"></i>
                </span>
            </button>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // ================= CONFIGURATION =================
    const API_URL = '/api';
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // ================= VARIABLES GLOBALES =================
    let toastTimeout = null;
    let failedAttempts = 0;
    const maxAttempts = 5;
    let lockUntil = null;

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

    // ================= VALIDATION DES CHAMPS =================
    function validateEmail(email) {
        const emailRegex = /^[^\s@]+@([^\s@]+\.)+[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Validation téléphone simple (uniquement des chiffres, espaces ou +, longueur entre 8 et 15)
    function validatePhone(phone) {
        const phoneRegex = /^\+?[0-9\s]{8,15}$/;
        return phoneRegex.test(phone);
    }

    function validatePassword(password) {
        return password.length >= 6;
    }

    function getPasswordStrength(password) {
        let strength = 0;
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]+/)) strength++;
        if (password.match(/[A-Z]+/)) strength++;
        if (password.match(/[0-9]+/)) strength++;
        if (password.match(/[$@#&!]+/)) strength++;
        return strength;
    }

    function updatePasswordStrength(password) {
        const strengthDiv = document.getElementById('passwordStrength');
        if (!password) {
            strengthDiv.style.display = 'none';
            return;
        }
        
        const strength = getPasswordStrength(password);
        const bars = strengthDiv.querySelectorAll('.strength-bar');
        const textSpan = strengthDiv.querySelector('.strength-text');
        
        strengthDiv.style.display = 'flex';
        
        bars.forEach((bar, index) => {
            bar.classList.remove('weak', 'medium', 'strong', 'very-strong');
            if (index < strength) {
                if (strength <= 2) bar.classList.add('weak');
                else if (strength === 3) bar.classList.add('medium');
                else if (strength === 4) bar.classList.add('strong');
                else bar.classList.add('very-strong');
            }
        });
        
        if (strength <= 2) textSpan.textContent = 'Faible';
        else if (strength === 3) textSpan.textContent = 'Moyen';
        else if (strength === 4) textSpan.textContent = 'Fort';
        else textSpan.textContent = 'Très fort';
    }

    // ================= VALIDATION EN TEMPS RÉEL (EMAIL OU TÉLÉPHONE) =================
    function validateLoginInput() {
        const loginInput = document.getElementById('loginInput');
        const loginValue = loginInput.value.trim();
        const loginGroup = document.getElementById('emailGroup');
        const loginError = document.getElementById('loginError');
        const validationIcon = loginGroup.querySelector('.input-validation');
        const inputIcon = loginGroup.querySelector('.input-icon i');
        
        if (loginValue === '') {
            loginGroup.classList.remove('valid', 'invalid');
            validationIcon.style.display = 'none';
            loginError.style.display = 'none';
            inputIcon.className = 'fas fa-envelope'; // Icone par défaut
            return false;
        }
        
        // Détection dynamique du type de saisie pour changer l'icône de gauche
        if (/^\+?[0-9]/.test(loginValue)) {
            inputIcon.className = 'fas fa-phone-alt'; // Icône téléphone
        } else {
            inputIcon.className = 'fas fa-envelope'; // Icône Email
        }

        // Validation finale : Soit c'est un email valide, soit un téléphone valide
        if (validateEmail(loginValue) || validatePhone(loginValue)) {
            loginGroup.classList.add('valid');
            loginGroup.classList.remove('invalid');
            validationIcon.style.display = 'flex';
            loginError.style.display = 'none';
            return true;
        } else {
            loginGroup.classList.add('invalid');
            loginGroup.classList.remove('valid');
            validationIcon.style.display = 'none';
            loginError.textContent = 'Veuillez entrer une adresse email ou un numéro de téléphone valide';
            loginError.style.display = 'block';
            return false;
        }
    }

    function validatePasswordInput() {
        const passwordInput = document.getElementById('passwordInput');
        const passwordValue = passwordInput.value;
        const passwordGroup = document.getElementById('passwordGroup');
        const passwordError = document.getElementById('passwordError');
        const validationIcon = passwordGroup.querySelector('.input-validation');
        
        updatePasswordStrength(passwordValue);
        
        if (passwordValue === '') {
            passwordGroup.classList.remove('valid', 'invalid');
            validationIcon.style.display = 'none';
            passwordError.style.display = 'none';
            return false;
        }
        
        if (validatePassword(passwordValue)) {
            passwordGroup.classList.add('valid');
            passwordGroup.classList.remove('invalid');
            validationIcon.style.display = 'flex';
            passwordError.style.display = 'none';
            return true;
        } else {
            passwordGroup.classList.add('invalid');
            passwordGroup.classList.remove('valid');
            validationIcon.style.display = 'none';
            passwordError.textContent = 'Le mot de passe doit contenir au moins 6 caractères';
            passwordError.style.display = 'block';
            return false;
        }
    }

    // ================= AFFICHAGE DES ERREURS =================
    function showError(message) {
        const errorDiv = document.getElementById('errorMessage');
        const errorText = document.getElementById('errorText');
        errorText.textContent = message;
        errorDiv.style.display = 'block';
        
        setTimeout(() => {
            errorDiv.style.opacity = '0';
            setTimeout(() => {
                errorDiv.style.display = 'none';
                errorDiv.style.opacity = '1';
            }, 300);
        }, 5000);
    }

    function showSuccess(message) {
        const successDiv = document.getElementById('successMessage');
        const successText = document.getElementById('successText');
        successText.textContent = message;
        successDiv.style.display = 'block';
        
        setTimeout(() => {
            successDiv.style.opacity = '0';
            setTimeout(() => {
                successDiv.style.display = 'none';
                successDiv.style.opacity = '1';
            }, 300);
        }, 5000);
    }

    function clearErrors() {
        if(document.getElementById('errorMessage')) document.getElementById('errorMessage').style.display = 'none';
        if(document.getElementById('successMessage')) document.getElementById('successMessage').style.display = 'none';
    }

    // ================= GESTION DU LOADER =================
    function setLoading(button, isLoading) {
        const btnText = button.querySelector('.btn-text');
        const btnLoader = button.querySelector('.btn-loader');
        
        if (isLoading) {
            btnText.style.display = 'none';
            btnLoader.style.display = 'inline-block';
            button.disabled = true;
        } else {
            btnText.style.display = 'inline-block';
            btnLoader.style.display = 'none';
            button.disabled = false;
        }
    }

    // ================= TENTATIVES DE CONNEXION =================
    function checkLockout() {
        if (lockUntil && new Date() < lockUntil) {
            const remainingMinutes = Math.ceil((lockUntil - new Date()) / 60000);
            showError(`Trop de tentatives échouées. Réessayez dans ${remainingMinutes} minute(s).`);
            return true;
        }
        return false;
    }

    function incrementFailedAttempts() {
        failedAttempts++;
        if (failedAttempts >= maxAttempts) {
            lockUntil = new Date(Date.now() + 15 * 60000);
            showError('Trop de tentatives échouées. Compte bloqué pour 15 minutes.');
        }
    }

    function resetFailedAttempts() {
        failedAttempts = 0;
        lockUntil = null;
    }

    // ================= API CALLS =================
    async function loginUser(credentials) {
        try {
            const response = await fetch(`${API_URL}/auth/login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify(credentials)
            });

            const data = await response.json();

            // Si le serveur répond avec une erreur (ex: 401, 403, 500)
            if (!response.ok) {
                throw new Error(data.message || 'Identifiants incorrects.');
            }

            return data;
        } catch (error) {
            throw error;
        }
    }

    // ================= SUBMIT FORMULAIRE =================
    document.getElementById('loginForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (checkLockout()) return;
        
        clearErrors();
        
        const isLoginValid = validateLoginInput();
        const isPasswordValid = validatePasswordInput();
        
        if (!isLoginValid || !isPasswordValid) {
            showError('Veuillez corriger les erreurs dans le formulaire.');
            return;
        }
        
        const login = document.getElementById('loginInput').value.trim();
        const password = document.getElementById('passwordInput').value;
        const remember = document.getElementById('rememberCheckbox').checked;
        const submitBtn = document.getElementById('loginBtn');
        
        setLoading(submitBtn, true);
        
        try {
            console.log('📤 Envoi de la requête de connexion...');
            const result = await loginUser({ login, password, remember });
            console.log('📥 Réponse reçue:', result);
            
            if (result.success === true || result.status === 'success') {
                resetFailedAttempts();
                
                // ✅ Vérifier que le token est présent
                if (result.data?.access_token) {
                    localStorage.setItem('access_token', result.data.access_token);
                    localStorage.setItem('token_expiry', Date.now() + 3600 * 1000);
                    
                    if (result.data.user) {
                        localStorage.setItem('user', JSON.stringify(result.data.user));
                        console.log('✅ Utilisateur stocké:', result.data.user);
                    }
                    
                    if (remember) {
                        localStorage.setItem('remember_login', login);
                    } else {
                        localStorage.removeItem('remember_login');
                    }
                } else {
                    console.error('❌ Token manquant dans la réponse');
                }
                
                // Récupération du rôle utilisateur
                const user = result.data?.user;
                let redirectUrl = '/dashboard';
                
                console.log('👤 Rôle utilisateur:', user?.role);
                
                if (user?.role === 'admin') {
                    redirectUrl = '/admin/dashboard';
                }
                
                console.log('🔀 Redirection vers:', redirectUrl);
                
                showSuccess('Connexion réussie ! Redirection en cours...');
                
                setTimeout(() => {
                    console.log('🔄 Redirection vers:', redirectUrl);
                    window.location.href = redirectUrl;
                }, 1500);
            } else {
                incrementFailedAttempts();
                showError(result.message || 'Identifiants incorrects.');
                document.getElementById('passwordInput').value = '';
                document.getElementById('passwordInput').focus();
            }
        } catch (error) {
            console.error('❌ Erreur:', error);
            incrementFailedAttempts();
            showError(error.message || 'Identifiants incorrects ou erreur serveur.');
        } finally {
            setLoading(submitBtn, false);
        }
    });

    // ================= AFFICHAGE/MASQUAGE MOT DE PASSE =================
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('passwordInput');
        const icon = this.querySelector('i');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.className = 'fas fa-eye-slash';
            showToast('Mot de passe visible', 'info');
        } else {
            passwordInput.type = 'password';
            icon.className = 'fas fa-eye';
            showToast('Mot de passe masqué', 'info');
        }
    });

    // ================= VALIDATION EN TEMPS RÉEL =================
    document.getElementById('loginInput').addEventListener('input', validateLoginInput);
    document.getElementById('passwordInput').addEventListener('input', validatePasswordInput);

    // ================= CHARGEMENT DES DONNÉES SAUVEGARDÉES =================
    const savedLogin = localStorage.getItem('remember_login');
    if (savedLogin) {
        document.getElementById('loginInput').value = savedLogin;
        document.getElementById('rememberCheckbox').checked = true;
        validateLoginInput();
    }

    // ================= MODAL MOT DE PASSE OUBLIÉ =================
    const forgotModal = document.getElementById('forgotPasswordModal');
    const forgotLink = document.getElementById('forgotPasswordLink');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const sendResetBtn = document.getElementById('sendResetLinkBtn');

    function openModal() {
        forgotModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        setTimeout(() => forgotModal.classList.add('show'), 10);
    }

    function closeModal() {
        forgotModal.classList.remove('show');
        setTimeout(() => {
            forgotModal.style.display = 'none';
            document.body.style.overflow = '';
        }, 300);
    }

    forgotLink.addEventListener('click', function(e) {
        e.preventDefault();
        openModal();
    });

    closeModalBtn.addEventListener('click', closeModal);
    document.querySelector('#forgotPasswordModal .modal-overlay').addEventListener('click', closeModal);

    sendResetBtn.addEventListener('click', async function() {
        const email = document.getElementById('resetEmail').value.trim();
        const resetError = document.getElementById('resetError');
        const resetSuccess = document.getElementById('resetSuccess');
        
        resetError.style.display = 'none';
        resetSuccess.style.display = 'none';
        
        if (!email || !validateEmail(email)) {
            resetError.textContent = 'Veuillez entrer une adresse e-mail valide.';
            resetError.style.display = 'block';
            return;
        }
        
        setLoading(sendResetBtn, true);
        
        try {
            const response = await fetch(`${API_URL}/email/resend`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify({ email })
            });

            const data = await response.json();

            if (response.ok) {
                resetSuccess.innerHTML = '<i class="fas fa-envelope"></i> Un lien de réinitialisation a été envoyé.';
                resetSuccess.style.display = 'block';
                setTimeout(() => {
                    closeModal();
                    showToast('Email de réinitialisation envoyé !', 'success');
                }, 2000);
            } else {
                resetError.textContent = data.message || 'Une erreur est survenue.';
                resetError.style.display = 'block';
            }
        } catch (error) {
            resetError.textContent = 'Erreur de connexion au serveur.';
            resetError.style.display = 'block';
        } finally {
            setLoading(sendResetBtn, false);
        }
    });

    // ================= RÉSEAUX SOCIAUX =================
    document.getElementById('googleLogin').addEventListener('click', () => showToast('Connexion avec Google (bientôt disponible)', 'info'));
    document.getElementById('facebookLogin').addEventListener('click', () => showToast('Connexion avec Facebook (bientôt disponible)', 'info'));
    document.getElementById('instagramLogin').addEventListener('click', () => showToast('Connexion avec Instagram (bientôt disponible)', 'info'));

    // ================= ANIMATIONS =================
    document.querySelectorAll('.input-group-custom input').forEach(input => {
        input.addEventListener('focus', function() { this.parentElement.classList.add('focused'); });
        input.addEventListener('blur', function() { this.parentElement.classList.remove('focused'); });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const card = document.querySelector('.login-card');
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100);
    });

    // ================= STYLES DYNAMIQUES =================
    const style = document.createElement('style');
    style.textContent = `
        .input-group-custom {
            position: relative;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 0 12px;
            background: #f7fafc;
            margin-bottom: 15px;
        }
        .input-group-custom.focused { border-color: #2e7d32; box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1); background: white; }
        .input-group-custom.valid { border-color: #28a745; background: #f0fff4; }
        .input-group-custom.invalid { border-color: #dc3545; background: #fff5f5; }
        .input-group-custom .input-icon { display: flex; align-items: center; color: #a0aec0; font-size: 1rem; margin-right: 10px; width: 20px; }
        .input-group-custom input { flex: 1; border: none; background: transparent; padding: 12px 0; font-size: 0.95rem; outline: none; width: 100%; color: #2d3748; }
        .input-validation { position: absolute; right: 40px; top: 50%; transform: translateY(-50%); color: #28a745; font-size: 18px; display: none; align-items: center; }
        .toggle-password { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #a0aec0; cursor: pointer; font-size: 16px; z-index: 10; }
        .field-error { font-size: 12px; color: #dc3545; margin-top: -10px; margin-bottom: 10px; padding-left: 15px; }
        .password-strength { display: flex; gap: 8px; margin: -10px 0 15px 0; align-items: center; flex-wrap: wrap; }
        .strength-bar { flex: 1; height: 4px; background: #e0e0e0; border-radius: 2px; transition: all 0.3s ease; }
        .strength-bar.weak { background: #dc3545; }
        .strength-bar.medium { background: #ffc107; }
        .strength-bar.strong { background: #17a2b8; }
        .strength-bar.very-strong { background: #28a745; }
        .strength-text { font-size: 11px; color: #666; margin-left: 5px; }
        .alert { border-radius: 12px; padding: 12px 15px; margin-bottom: 20px; font-size: 14px; transition: opacity 0.3s ease; }
        .form-options { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }
        .checkbox-label { display: flex; align-items: center; gap: 8px; font-size: 0.9rem; color: #4a5568; cursor: pointer; position: relative; padding-left: 28px; }
        .checkbox-label input { position: absolute; opacity: 0; cursor: pointer; height: 0; width: 0; }
        .checkmark { position: absolute; left: 0; height: 18px; width: 18px; background: #fff; border: 2px solid #cbd5e0; border-radius: 4px; }
        .checkbox-label input:checked ~ .checkmark { background: #2e7d32; border-color: #2e7d32; }
        .checkbox-label .checkmark:after { left: 6px; top: 2px; width: 5px; height: 10px; border: solid white; border-width: 0 2px 2px 0; transform: rotate(45deg); position: absolute; content: ""; display: none; }
        .checkbox-label input:checked ~ .checkmark:after { display: block; }
        .forgot-link { color: #2e7d32; font-size: 0.9rem; text-decoration: none; font-weight: 500; }
        .divider { text-align: center; margin: 25px 0; position: relative; }
        .divider::before, .divider::after { content: ''; position: absolute; top: 50%; width: calc(50% - 30px); height: 1px; background: #e0e0e0; }
        .divider::before { left: 0; } .divider::after { right: 0; }
        .divider span { background: white; padding: 0 15px; color: #999; font-size: 14px; }
        .social-login { display: flex; gap: 12px; margin-top: 20px; flex-wrap: wrap; }
        .social-btn { flex: 1; padding: 12px; border: 1px solid #e0e0e0; border-radius: 16px; background: white; font-size: 14px; font-weight: 500; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .social-btn.google { color: #db4437; border-color: #db4437; }
        .social-btn.facebook { color: #4267b2; border-color: #4267b2; }
        .social-btn.instagram { color: #e4405f; border-color: #e4405f; }
        .custom-toast { position: fixed; bottom: 30px; right: 30px; z-index: 10000; transform: translateX(400px); transition: transform 0.3s ease; }
        .custom-toast.show { transform: translateX(0); }
        .custom-toast .toast-content { background: #343a40; color: white; padding: 12px 20px; border-radius: 10px; display: flex; align-items: center; gap: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
        .custom-toast.success .toast-content { background: #28a745; }
        .custom-toast.danger .toast-content { background: #dc3545; }
        .custom-modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 10001; display: flex; align-items: center; justify-content: center; visibility: hidden; opacity: 0; transition: all 0.3s ease; }
        .custom-modal.show { visibility: visible; opacity: 1; }
        .modal-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal-container { position: relative; background: white; border-radius: 12px; max-width: 450px; width: 90%; z-index: 10002; transform: scale(0.9); transition: transform 0.3s ease; }
        .custom-modal.show .modal-container { transform: scale(1); }
        .modal-header { display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; border-bottom: 1px solid #dee2e6; }
        .modal-header h3 { margin: 0; font-size: 18px; color: #2e7d32; }
        .modal-close { background: none; border: none; font-size: 20px; cursor: pointer; color: #999; }
        .modal-body { padding: 20px; }
        @media (max-width: 768px) {
            .custom-toast { left: 15px; right: 15px; bottom: 15px; transform: translateY(100px); }
            .custom-toast.show { transform: translateY(0); }
            .social-login { flex-direction: column; }
            .form-options { flex-direction: column; align-items: flex-start; }
        }
    `;
    document.head.appendChild(style);
</script>
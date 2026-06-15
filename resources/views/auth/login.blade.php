<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Élevage+</title>
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
        <form class="login-form" id="loginForm" method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Champ Email / Téléphone -->
            <div class="input-group-custom" id="emailGroup">
                <div class="input-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <input type="text" name="login" id="loginInput" placeholder="Adresse e-mail ou Numéro de téléphone" required autofocus>
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
// ================= VARIABLES =================
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
    const phoneRegex = /^(\+221|00221)?(77|78|70|76|75)[0-9]{7}$/;
    return emailRegex.test(email) || phoneRegex.test(email);
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

// ================= VALIDATION EN TEMPS RÉEL =================
function validateLoginInput() {
    const loginInput = document.getElementById('loginInput');
    const loginValue = loginInput.value.trim();
    const loginGroup = document.getElementById('emailGroup');
    const loginError = document.getElementById('loginError');
    const validationIcon = loginGroup.querySelector('.input-validation');
    
    if (loginValue === '') {
        loginGroup.classList.remove('valid', 'invalid');
        validationIcon.style.display = 'none';
        loginError.style.display = 'none';
        return false;
    }
    
    if (validateEmail(loginValue)) {
        loginGroup.classList.add('valid');
        loginGroup.classList.remove('invalid');
        validationIcon.style.display = 'flex';
        loginError.style.display = 'none';
        return true;
    } else {
        loginGroup.classList.add('invalid');
        loginGroup.classList.remove('valid');
        validationIcon.style.display = 'none';
        loginError.textContent = 'Veuillez entrer un email valide ou un numéro de téléphone sénégalais (77, 78, 70, 76, 75)';
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
    document.getElementById('errorMessage').style.display = 'none';
    document.getElementById('successMessage').style.display = 'none';
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
        showError(`Trop de tentatives échouées. Réessayez dans ${remainingMinutes} minute(s).`, 'danger');
        return true;
    }
    return false;
}

function incrementFailedAttempts() {
    failedAttempts++;
    if (failedAttempts >= maxAttempts) {
        lockUntil = new Date(Date.now() + 15 * 60000);
        showError('Trop de tentatives échouées. Compte bloqué pour 15 minutes.', 'danger');
    }
}

function resetFailedAttempts() {
    failedAttempts = 0;
    lockUntil = null;
}

// ================= SIMULATION DE CONNEXION =================
async function simulateLogin(login, password, remember) {
    // Simulation d'un appel API
    return new Promise((resolve) => {
        setTimeout(() => {
            // Pour la démo, accepter ces identifiants
            const validCredentials = {
                'admin@elevageplus.com': 'admin123',
                'user@elevageplus.com': 'user123',
                '771234567': 'user123'
            };
            
            if (validCredentials[login] === password) {
                resolve({ success: true, message: 'Connexion réussie ! Redirection...' });
            } else {
                resolve({ success: false, message: 'Email ou mot de passe incorrect.' });
            }
        }, 1500);
    });
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
        const result = await simulateLogin(login, password, remember);
        
        if (result.success) {
            resetFailedAttempts();
            showSuccess(result.message);
            
            // Sauvegarder email si "Se souvenir de moi"
            if (remember) {
                localStorage.setItem('savedLogin', login);
            } else {
                localStorage.removeItem('savedLogin');
            }
            
            setTimeout(() => {
                window.location.href = "{{ url('/dashboard') }}";
            }, 1500);
        } else {
            incrementFailedAttempts();
            showError(result.message);
            document.getElementById('passwordInput').value = '';
            document.getElementById('passwordInput').focus();
        }
    } catch (error) {
        showError('Une erreur est survenue. Veuillez réessayer.');
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
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
        showToast('Mot de passe visible', 'info');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
        showToast('Mot de passe masqué', 'info');
    }
});

// ================= VALIDATION EN TEMPS RÉEL =================
document.getElementById('loginInput').addEventListener('input', validateLoginInput);
document.getElementById('passwordInput').addEventListener('input', validatePasswordInput);

// ================= CHARGEMENT DES DONNÉES SAUVEGARDÉES =================
const savedLogin = localStorage.getItem('savedLogin');
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
    
    if (!email) {
        resetError.textContent = 'Veuillez entrer votre adresse e-mail.';
        resetError.style.display = 'block';
        return;
    }
    
    const emailRegex = /^[^\s@]+@([^\s@]+\.)+[^\s@]+$/;
    if (!emailRegex.test(email)) {
        resetError.textContent = 'Veuillez entrer une adresse e-mail valide.';
        resetError.style.display = 'block';
        return;
    }
    
    setLoading(sendResetBtn, true);
    
    setTimeout(() => {
        setLoading(sendResetBtn, false);
        resetSuccess.innerHTML = '<i class="fas fa-envelope"></i> Un lien de réinitialisation a été envoyé à votre adresse e-mail.';
        resetSuccess.style.display = 'block';
        
        setTimeout(() => {
            closeModal();
            showToast('Email de réinitialisation envoyé !', 'success');
        }, 2000);
    }, 1500);
});

// ================= RÉSEAUX SOCIAUX =================
document.getElementById('googleLogin').addEventListener('click', function() {
    showToast('Connexion avec Google (démonstration)', 'info');
    setTimeout(() => {
        showSuccess('Connexion Google réussie ! Redirection...');
        setTimeout(() => window.location.href = "{{ url('/dashboard') }}", 1500);
    }, 1000);
});

document.getElementById('facebookLogin').addEventListener('click', function() {
    showToast('Connexion avec Facebook (démonstration)', 'info');
    setTimeout(() => {
        showSuccess('Connexion Facebook réussie ! Redirection...');
        setTimeout(() => window.location.href = "{{ url('/dashboard') }}", 1500);
    }, 1000);
});

document.getElementById('instagramLogin').addEventListener('click', function() {
    showToast('Connexion avec Instagram (démonstration)', 'info');
    setTimeout(() => {
        showSuccess('Connexion Instagram réussie ! Redirection...');
        setTimeout(() => window.location.href = "{{ url('/dashboard') }}", 1500);
    }, 1000);
});

// ================= ANIMATIONS =================
// Animation de focus sur les champs
document.querySelectorAll('.input-group-custom input').forEach(input => {
    input.addEventListener('focus', function() {
        this.parentElement.classList.add('focused');
    });
    
    input.addEventListener('blur', function() {
        this.parentElement.classList.remove('focused');
    });
});

// Animation de la carte au chargement
document.addEventListener('DOMContentLoaded', function() {
    const card = document.querySelector('.login-card');
    card.style.opacity = '0';
    card.style.transform = 'translateY(20px)';
    
    setTimeout(() => {
        card.style.transition = 'all 0.5s ease';
        card.style.opacity = '1';
        card.style.transform = 'translateY(0)';
    }, 100);
    
    // Vérifier si un message flash existe
    const urlParams = new URLSearchParams(window.location.search);
    const registered = urlParams.get('registered');
    if (registered === 'success') {
        showSuccess('Inscription réussie ! Vous pouvez maintenant vous connecter.');
    }
});

// ================= STYLES DYNAMIQUES =================
const style = document.createElement('style');
style.textContent = `
    .input-group-custom {
        position: relative;
        transition: all 0.3s ease;
    }
    
    .input-group-custom.focused {
        border-color: #2e7d32;
        box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
        background: white;
    }
    
    .input-group-custom.valid {
        border-color: #28a745;
        background: #f0fff4;
    }
    
    .input-group-custom.invalid {
        border-color: #dc3545;
        background: #fff5f5;
    }
    
    .input-validation {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #28a745;
        font-size: 18px;
        display: none;
        align-items: center;
    }
    
    .toggle-password {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #999;
        cursor: pointer;
        font-size: 16px;
        z-index: 10;
    }
    
    .toggle-password:hover {
        color: #2e7d32;
    }
    
    .field-error {
        font-size: 12px;
        color: #dc3545;
        margin-top: -12px;
        margin-bottom: 15px;
        padding-left: 15px;
    }
    
    .password-strength {
        display: flex;
        gap: 8px;
        margin: -10px 0 15px 0;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .strength-bar {
        flex: 1;
        height: 4px;
        background: #e0e0e0;
        border-radius: 2px;
        transition: all 0.3s ease;
    }
    
    .strength-bar.weak { background: #dc3545; }
    .strength-bar.medium { background: #ffc107; }
    .strength-bar.strong { background: #17a2b8; }
    .strength-bar.very-strong { background: #28a745; }
    
    .strength-text {
        font-size: 11px;
        color: #666;
        margin-left: 5px;
    }
    
    .alert {
        border-radius: 12px;
        padding: 12px 15px;
        margin-bottom: 20px;
        font-size: 14px;
        transition: opacity 0.3s ease;
    }
    
    .btn-loader {
        margin-left: 8px;
    }
    
    .login-btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
    
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
    
    .custom-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 10001;
        display: flex;
        align-items: center;
        justify-content: center;
        visibility: hidden;
        opacity: 0;
        transition: all 0.3s ease;
    }
    
    .custom-modal.show {
        visibility: visible;
        opacity: 1;
    }
    
    .modal-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
    }
    
    .modal-container {
        position: relative;
        background: white;
        border-radius: 12px;
        max-width: 450px;
        width: 90%;
        z-index: 10002;
        transform: scale(0.9);
        transition: transform 0.3s ease;
    }
    
    .custom-modal.show .modal-container { transform: scale(1); }
    
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        border-bottom: 1px solid #dee2e6;
    }
    
    .modal-header h3 {
        margin: 0;
        font-size: 18px;
        color: #2e7d32;
    }
    
    .modal-close {
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: #999;
    }
    
    .modal-body {
        padding: 20px;
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
`;
document.head.appendChild(style);
</script>

</body>
</html>
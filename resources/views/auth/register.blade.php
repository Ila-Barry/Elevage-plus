{{-- resources/views/auth/register.blade.php --}}

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Inscription | ÉLEVAGE+</title>
    
    <!-- CSRF Token pour les requêtes AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link rel="icon" href="{{ asset('images/logoE.png') }}" type="image/png">

    <!-- Bootstrap 4.6 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/authCSS/register.css') }}">
</head>
<body>

<!-- En-tête : Logo + lien connexion -->
<div class="register-header">
    <a href="{{ url('/') }}">
        <div class="logo">
            <img class="img-logo" src="{{ asset('images/logoE.png') }}" alt="Logo">
            <span>ÉLEVAGE+</span>
        </div>
    </a>
    <p class="register-subtitle">Déjà inscrit ? 
        <a href="{{ url('/auth/login') }}">
            <i class="fas fa-sign-in-alt"></i> Se connecter
        </a>
    </p>
</div>

<div class="register-container">
    <div class="register-card mx-auto">
        <div class="logo"><img src="{{ asset('images/logoE.png') }}" alt="Logo Élevage+"></div>

        <!-- Titre central CRÉER UN COMPTE -->
        <div class="text-center mb-4 mt-2">
            <h3 class="fw-bold text-dark mb-1">CRÉER UN COMPTE</h3>
            <div class="underline-title mx-auto"></div>
        </div>

        <!-- Message d'erreur général -->
        <div id="errorMessage" class="alert alert-danger" style="display: none;">
            <i class="fas fa-exclamation-circle"></i> <span id="errorText"></span>
        </div>

        <!-- Message de succès -->
        <div id="successMessage" class="alert alert-success" style="display: none;">
            <i class="fas fa-check-circle"></i> <span id="successText"></span>
        </div>

        <!-- Formulaire d'inscription -->
        <form id="registerForm">
            @csrf

            <!-- 1. NOM -->
            <div class="form-group mb-4">
                <label class="form-label fw-semibold">Nom complet</label>
                <div class="input-group custom-input-group" id="nameGroup">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-user text-muted"></i></span>
                    </div>
                    <input type="text" class="form-control border-start-0 ps-0" id="fullName" name="name" placeholder="Jean Dupont" required autofocus>
                    <div class="input-validation" style="display: none;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="field-error" id="nameError" style="display: none;"></div>
            </div>

            <!-- 2. ADRESSE E-MAIL -->
            <div class="form-group mb-4">
                <label class="form-label fw-semibold">Adresse e-mail</label>
                <div class="input-group custom-input-group" id="emailGroup">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                    </div>
                    <input type="email" class="form-control border-start-0 ps-0" id="email" name="email" placeholder="exemple@email.com" required>
                    <div class="input-validation" style="display: none;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="field-error" id="emailError" style="display: none;"></div>
            </div>

            <!-- 3. NUMÉRO TÉLÉPHONE -->
            <div class="form-group mb-4">
                <label class="form-label fw-semibold">Numéro téléphone</label>
                <div class="input-group custom-input-group" id="phoneGroup">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-phone-alt text-muted"></i></span>
                    </div>
                    <input type="tel" class="form-control border-start-0 ps-0" id="phone" name="telephone" placeholder="77 123 45 67" required>
                    <div class="input-validation" style="display: none;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="field-error" id="phoneError" style="display: none;"></div>
            </div>

            <!-- 4. MOT DE PASSE -->
            <div class="form-group mb-4">
                <label class="form-label fw-semibold">Mot de passe</label>
                <div class="input-group custom-input-group" id="passwordGroup">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-lock text-muted"></i></span>
                    </div>
                    <input type="password" class="form-control border-start-0 ps-0" id="password" name="password" placeholder="Mot de passe" required>
                    <button type="button" class="toggle-password" data-target="password">
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
            </div>

            <!-- 5. CONFIRMER MOT DE PASSE -->
            <div class="form-group mb-4">
                <label class="form-label fw-semibold">Confirmer mot de passe</label>
                <div class="input-group custom-input-group" id="passwordConfirmGroup">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-lock text-muted"></i></span>
                    </div>
                    <input type="password" class="form-control border-start-0 ps-0" id="password_confirmation" name="password_confirmation" placeholder="Confirmer mot de passe" required>
                    <button type="button" class="toggle-password" data-target="password_confirmation">
                        <i class="fas fa-eye"></i>
                    </button>
                    <div class="input-validation" style="display: none;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="field-error" id="passwordConfirmError" style="display: none;"></div>
            </div>

            <!-- 6. BIOGRAPHIE (optionnel) -->
            <div class="form-group mb-4">
                <label class="form-label fw-semibold">Biographie (optionnel)</label>
                <div class="input-group custom-input-group" id="bioGroup">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-align-left text-muted"></i></span>
                    </div>
                    <textarea class="form-control border-start-0 ps-0" id="bio" name="bio" placeholder="Parlez-nous de vous..." rows="3"></textarea>
                    <div class="input-validation" style="display: none;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="field-error" id="bioError" style="display: none;"></div>
            </div>

            <!-- 7. Case à cocher conditions générales -->
            <div class="form-group form-check mb-4 d-flex align-items-center">
                <input class="form-check-input me-2" type="checkbox" id="acceptTerms" style="width: 18px; height: 18px;" required>
                <label class="form-check-label text-dark" for="acceptTerms">
                    J'accepte les <a href="#" id="termsLink">conditions d'utilisation</a>
                </label>
            </div>

            <!-- Bouton S'INSCRIRE -->
            <button type="submit" class="btn btn-success btn-block rounded-pill py-3 fw-bold btn-submit w-100 shadow-sm" id="registerBtn">
                <span class="btn-text">S'INSCRIRE</span>
                <span class="btn-loader" style="display: none;">
                    <i class="fas fa-spinner fa-spin"></i>
                </span>
                <i class="fas fa-arrow-right ms-2 arrow-icon"></i>
            </button>

            <!-- Lien supplémentaire en bas -->
            <div class="text-center mt-4">
                <span class="text-muted">Déjà un compte ?</span>
                <a href="{{ url('/auth/login') }}" class="fw-bold text-decoration-none ms-1 link-login">Se connecter</a>
            </div>
        </form>

        <!-- Séparateur -->
        <div class="divider">
            <span>Ou</span>
        </div>

        <!-- Inscription avec réseaux sociaux -->
        <div class="social-register">
            <button class="social-btn google" id="googleRegister">
                <i class="fab fa-google"></i>
                Google
            </button>
            <button class="social-btn facebook" id="facebookRegister">
                <i class="fab fa-facebook-f"></i>
                Facebook
            </button>
            <button class="social-btn instagram" id="instagramRegister">
                <i class="fab fa-instagram"></i>
                Instagram
            </button>
        </div>
    </div>
</div>

<!-- Modal Conditions d'utilisation -->
<div class="custom-modal" id="termsModal" style="display: none;">
    <div class="modal-overlay"></div>
    <div class="modal-container">
        <div class="modal-header">
            <h3><i class="fas fa-file-contract"></i> Conditions d'utilisation</h3>
            <button class="modal-close" id="closeTermsModal"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div class="terms-content">
                <h4>1. Acceptation des conditions</h4>
                <p>En créant un compte sur ÉLEVAGE+, vous acceptez pleinement ces conditions d'utilisation.</p>
                
                <h4>2. Utilisation de la plateforme</h4>
                <p>ÉLEVAGE+ est une plateforme dédiée aux éleveurs. Vous vous engagez à partager du contenu pertinent et respectueux.</p>
                
                <h4>3. Protection des données</h4>
                <p>Vos données personnelles sont protégées conformément au RGPD. Nous ne partageons jamais vos informations sans consentement.</p>
                
                <h4>4. Responsabilité</h4>
                <p>Vous êtes seul responsable du contenu que vous publiez. ÉLEVAGE+ se réserve le droit de modérer tout contenu inapproprié.</p>
                
                <h4>5. Modification des conditions</h4>
                <p>Les conditions peuvent être modifiées à tout moment. Vous serez informé des changements majeurs.</p>
            </div>
            <button class="login-btn" id="acceptTermsBtn" style="margin-top: 20px;">
                <i class="fas fa-check"></i> J'accepte les conditions
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

// ================= VARIABLES =================
let toastTimeout = null;

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

// ================= FONCTIONS DE VALIDATION =================
function validateName(name) {
    return name.trim().length >= 2;
}

function validateEmail(email) {
    const emailRegex = /^[^\s@]+@([^\s@]+\.)+[^\s@]+$/;
    return emailRegex.test(email);
}

function validatePhone(phone) {
    const phoneRegex = /^(\+221|00221)?(77|78|70|76|75)[0-9]{7}$/;
    const cleanPhone = phone.replace(/[\s\-]/g, '');
    return phoneRegex.test(cleanPhone);
}

function validatePassword(password) {
    return password.length >= 6;
}

function validatePasswordMatch(password, confirm) {
    return password === confirm;
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
function validateNameInput() {
    const input = document.getElementById('fullName');
    const value = input.value;
    const group = document.getElementById('nameGroup');
    const errorDiv = document.getElementById('nameError');
    const validationIcon = group.querySelector('.input-validation');
    
    if (value === '') {
        group.classList.remove('valid', 'invalid');
        validationIcon.style.display = 'none';
        errorDiv.style.display = 'none';
        return false;
    }
    
    if (validateName(value)) {
        group.classList.add('valid');
        group.classList.remove('invalid');
        validationIcon.style.display = 'flex';
        errorDiv.style.display = 'none';
        return true;
    } else {
        group.classList.add('invalid');
        group.classList.remove('valid');
        validationIcon.style.display = 'none';
        errorDiv.textContent = 'Le nom doit contenir au moins 2 caractères';
        errorDiv.style.display = 'block';
        return false;
    }
}

function validateEmailInput() {
    const input = document.getElementById('email');
    const value = input.value;
    const group = document.getElementById('emailGroup');
    const errorDiv = document.getElementById('emailError');
    const validationIcon = group.querySelector('.input-validation');
    
    if (value === '') {
        group.classList.remove('valid', 'invalid');
        validationIcon.style.display = 'none';
        errorDiv.style.display = 'none';
        return false;
    }
    
    if (validateEmail(value)) {
        group.classList.add('valid');
        group.classList.remove('invalid');
        validationIcon.style.display = 'flex';
        errorDiv.style.display = 'none';
        return true;
    } else {
        group.classList.add('invalid');
        group.classList.remove('valid');
        validationIcon.style.display = 'none';
        errorDiv.textContent = 'Veuillez entrer une adresse e-mail valide';
        errorDiv.style.display = 'block';
        return false;
    }
}

function validatePhoneInput() {
    const input = document.getElementById('phone');
    const value = input.value;
    const group = document.getElementById('phoneGroup');
    const errorDiv = document.getElementById('phoneError');
    const validationIcon = group.querySelector('.input-validation');
    
    if (value === '') {
        group.classList.remove('valid', 'invalid');
        validationIcon.style.display = 'none';
        errorDiv.style.display = 'none';
        return false;
    }
    
    if (validatePhone(value)) {
        group.classList.add('valid');
        group.classList.remove('invalid');
        validationIcon.style.display = 'flex';
        errorDiv.style.display = 'none';
        return true;
    } else {
        group.classList.add('invalid');
        group.classList.remove('valid');
        validationIcon.style.display = 'none';
        errorDiv.textContent = 'Format invalide (ex: 771234567 ou +221771234567)';
        errorDiv.style.display = 'block';
        return false;
    }
}

function validatePasswordInput() {
    const input = document.getElementById('password');
    const value = input.value;
    const group = document.getElementById('passwordGroup');
    const errorDiv = document.getElementById('passwordError');
    const validationIcon = group.querySelector('.input-validation');
    
    updatePasswordStrength(value);
    
    if (value === '') {
        group.classList.remove('valid', 'invalid');
        validationIcon.style.display = 'none';
        errorDiv.style.display = 'none';
        validatePasswordMatchInput();
        return false;
    }
    
    if (validatePassword(value)) {
        group.classList.add('valid');
        group.classList.remove('invalid');
        validationIcon.style.display = 'flex';
        errorDiv.style.display = 'none';
    } else {
        group.classList.add('invalid');
        group.classList.remove('valid');
        validationIcon.style.display = 'none';
        errorDiv.textContent = 'Le mot de passe doit contenir au moins 6 caractères';
        errorDiv.style.display = 'block';
    }
    
    validatePasswordMatchInput();
    return validatePassword(value);
}

function validatePasswordMatchInput() {
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('password_confirmation').value;
    const group = document.getElementById('passwordConfirmGroup');
    const errorDiv = document.getElementById('passwordConfirmError');
    const validationIcon = group.querySelector('.input-validation');
    
    if (confirm === '') {
        group.classList.remove('valid', 'invalid');
        validationIcon.style.display = 'none';
        errorDiv.style.display = 'none';
        return false;
    }
    
    if (validatePasswordMatch(password, confirm)) {
        group.classList.add('valid');
        group.classList.remove('invalid');
        validationIcon.style.display = 'flex';
        errorDiv.style.display = 'none';
        return true;
    } else {
        group.classList.add('invalid');
        group.classList.remove('valid');
        validationIcon.style.display = 'none';
        errorDiv.textContent = 'Les mots de passe ne correspondent pas';
        errorDiv.style.display = 'block';
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
    const arrowIcon = button.querySelector('.arrow-icon');
    
    if (isLoading) {
        btnText.style.display = 'none';
        btnLoader.style.display = 'inline-block';
        if (arrowIcon) arrowIcon.style.display = 'none';
        button.disabled = true;
    } else {
        btnText.style.display = 'inline-block';
        btnLoader.style.display = 'none';
        if (arrowIcon) arrowIcon.style.display = 'inline-block';
        button.disabled = false;
    }
}

// ================= API CALLS =================
async function registerUser(userData) {
    try {
        const response = await fetch(`${API_URL}/auth/register`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: JSON.stringify(userData)
        });

        const data = await response.json();
        console.log('Réponse du serveur:', data);

        if (!response.ok) {
            throw data;
        }

        return data;
    } catch (error) {
        console.error('Erreur complète:', error);
        throw error;
    }
}

// ================= SUBMIT FORMULAIRE =================
document.getElementById('registerForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    clearErrors();
    
    // Valider tous les champs
    const isNameValid = validateNameInput();
    const isEmailValid = validateEmailInput();
    const isPhoneValid = validatePhoneInput();
    const isPasswordValid = validatePasswordInput();
    const isPasswordMatchValid = validatePasswordMatchInput();
    const termsAccepted = document.getElementById('acceptTerms').checked;
    
    if (!isNameValid) {
        showError('Veuillez entrer votre nom complet.');
        document.getElementById('fullName').focus();
        return;
    }
    
    if (!isEmailValid) {
        showError('Veuillez entrer une adresse e-mail valide.');
        document.getElementById('email').focus();
        return;
    }
    
    if (!isPhoneValid) {
        showError('Veuillez entrer un numéro de téléphone valide.');
        document.getElementById('phone').focus();
        return;
    }
    
    if (!isPasswordValid) {
        showError('Le mot de passe doit contenir au moins 6 caractères.');
        document.getElementById('password').focus();
        return;
    }
    
    if (!isPasswordMatchValid) {
        showError('Les mots de passe ne correspondent pas.');
        document.getElementById('password_confirmation').focus();
        return;
    }
    
    if (!termsAccepted) {
        showError('Vous devez accepter les conditions d\'utilisation.');
        return;
    }
    
    // Nettoyer le numéro de téléphone (supprimer les espaces et tirets)
    const phoneValue = document.getElementById('phone').value.replace(/[\s\-]/g, '');
    
    const formData = {
        name: document.getElementById('fullName').value.trim(),
        email: document.getElementById('email').value.trim(),
        telephone: phoneValue,
        password: document.getElementById('password').value,
        password_confirmation: document.getElementById('password_confirmation').value,
        bio: document.getElementById('bio').value.trim()
    };
    
    const submitBtn = document.getElementById('registerBtn');
    setLoading(submitBtn, true);
    
    try {
        const result = await registerUser(formData);
        
        if (result.success === true) {
            // Stocker les tokens
            if (result.data?.access_token) {
                localStorage.setItem('access_token', result.data.access_token);
                localStorage.setItem('token_expiry', Date.now() + 3600 * 1000);
                
                if (result.data.user) {
                    localStorage.setItem('user', JSON.stringify(result.data.user));
                }
            }
            
            showSuccess('Inscription réussie ! Vous allez être redirigé vers la page de connexion.');
            
            // Redirection vers la page de connexion
            setTimeout(() => {
                window.location.href = '/auth/login?registered=success';
            }, 3000);
        } else {
            // Gérer les erreurs de validation du backend
            if (result.errors) {
                let errorMsg = '';
                for (const [field, messages] of Object.entries(result.errors)) {
                    const fieldNames = {
                        'name': 'Nom',
                        'email': 'Email',
                        'telephone': 'Téléphone',
                        'password': 'Mot de passe',
                        'bio': 'Biographie'
                    };
                    const fieldName = fieldNames[field] || field;
                    errorMsg += `${fieldName}: ${messages.join(', ')}\n`;
                }
                showError(errorMsg || result.message || 'Une erreur est survenue.');
            } else {
                showError(result.message || 'Une erreur est survenue lors de l\'inscription.');
            }
        }
    } catch (error) {
        console.error('Erreur:', error);
        if (error.errors) {
            let errorMsg = '';
            for (const [field, messages] of Object.entries(error.errors)) {
                const fieldNames = {
                    'name': 'Nom',
                    'email': 'Email',
                    'telephone': 'Téléphone',
                    'password': 'Mot de passe',
                    'bio': 'Biographie'
                };
                const fieldName = fieldNames[field] || field;
                errorMsg += `${fieldName}: ${messages.join(', ')}\n`;
            }
            showError(errorMsg);
        } else {
            showError(error.message || 'Une erreur est survenue. Veuillez réessayer.');
        }
    } finally {
        setLoading(submitBtn, false);
    }
});

// ================= AFFICHAGE/MASQUAGE MOT DE PASSE =================
document.querySelectorAll('.toggle-password').forEach(button => {
    button.addEventListener('click', function() {
        const targetId = this.getAttribute('data-target');
        const input = document.getElementById(targetId);
        const icon = this.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
            showToast('Mot de passe visible', 'info');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
            showToast('Mot de passe masqué', 'info');
        }
    });
});

// ================= VALIDATION EN TEMPS RÉEL (Écouteurs) =================
document.getElementById('fullName').addEventListener('input', validateNameInput);
document.getElementById('email').addEventListener('input', validateEmailInput);
document.getElementById('phone').addEventListener('input', validatePhoneInput);
document.getElementById('password').addEventListener('input', validatePasswordInput);
document.getElementById('password_confirmation').addEventListener('input', validatePasswordMatchInput);

// ================= MODAL CONDITIONS =================
const termsModal = document.getElementById('termsModal');
const termsLink = document.getElementById('termsLink');
const closeTermsModal = document.getElementById('closeTermsModal');
const acceptTermsBtn = document.getElementById('acceptTermsBtn');
const termsCheckbox = document.getElementById('acceptTerms');

function openTermsModal() {
    termsModal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    setTimeout(() => termsModal.classList.add('show'), 10);
}

function closeTermsModalFunc() {
    termsModal.classList.remove('show');
    setTimeout(() => {
        termsModal.style.display = 'none';
        document.body.style.overflow = '';
    }, 300);
}

if (termsLink) termsLink.addEventListener('click', function(e) {
    e.preventDefault();
    openTermsModal();
});

if (closeTermsModal) closeTermsModal.addEventListener('click', closeTermsModalFunc);
if (termsModal) document.querySelector('#termsModal .modal-overlay').addEventListener('click', closeTermsModalFunc);

if (acceptTermsBtn) {
    acceptTermsBtn.addEventListener('click', function() {
        termsCheckbox.checked = true;
        closeTermsModalFunc();
        showToast('Vous avez accepté les conditions d\'utilisation', 'success');
    });
}

// ================= RÉSEAUX SOCIAUX =================
document.getElementById('googleRegister').addEventListener('click', function() {
    showToast('Inscription avec Google (bientôt disponible)', 'info');
});

document.getElementById('facebookRegister').addEventListener('click', function() {
    showToast('Inscription avec Facebook (bientôt disponible)', 'info');
});

document.getElementById('instagramRegister').addEventListener('click', function() {
    showToast('Inscription avec Instagram (bientôt disponible)', 'info');
});

// ================= ANIMATIONS =================
document.querySelectorAll('.custom-input-group input, .custom-input-group textarea').forEach(input => {
    input.addEventListener('focus', function() {
        this.closest('.custom-input-group').classList.add('focused');
    });
    
    input.addEventListener('blur', function() {
        this.closest('.custom-input-group').classList.remove('focused');
    });
});

// Animation de la carte au chargement
document.addEventListener('DOMContentLoaded', function() {
    const card = document.querySelector('.register-card');
    card.style.opacity = '0';
    card.style.transform = 'translateY(20px)';
    
    setTimeout(() => {
        card.style.transition = 'all 0.5s ease';
        card.style.opacity = '1';
        card.style.transform = 'translateY(0)';
    }, 100);
    
    // Vérifier si un email a été pré-rempli
    const savedEmail = localStorage.getItem('registeredEmail');
    if (savedEmail) {
        document.getElementById('email').value = savedEmail;
        validateEmailInput();
        localStorage.removeItem('registeredEmail');
    }
    
    // Formater le téléphone en temps réel
    const phoneInput = document.getElementById('phone');
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 9) value = value.slice(0, 9);
        e.target.value = value;
    });
});

// ================= STYLES DYNAMIQUES =================
const style = document.createElement('style');
style.textContent = `
    .custom-input-group {
        position: relative;
        transition: all 0.3s ease;
    }
    
    .custom-input-group.focused {
        border-color: #2e7d32;
        box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
    }
    
    .custom-input-group.valid {
        border-color: #28a745;
        background: #f0fff4;
    }
    
    .custom-input-group.invalid {
        border-color: #dc3545;
        background: #fff5f5;
    }
    
    .input-validation {
        position: absolute;
        right: 45px;
        top: 50%;
        transform: translateY(-50%);
        color: #28a745;
        font-size: 18px;
        display: none;
        align-items: center;
        z-index: 10;
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
        margin-top: 5px;
        padding-left: 15px;
    }
    
    .password-strength {
        display: flex;
        gap: 8px;
        margin-top: 8px;
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
    
    .login-btn:disabled, .btn-submit:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
    
    .divider {
        text-align: center;
        margin: 25px 0;
        position: relative;
    }
    
    .divider::before,
    .divider::after {
        content: '';
        position: absolute;
        top: 50%;
        width: calc(50% - 30px);
        height: 1px;
        background: #e0e0e0;
    }
    
    .divider::before { left: 0; }
    .divider::after { right: 0; }
    
    .divider span {
        background: white;
        padding: 0 15px;
        color: #999;
        font-size: 14px;
    }
    
    .social-register {
        display: flex;
        gap: 12px;
        margin-top: 20px;
        flex-wrap: wrap;
    }
    
    .social-btn {
        flex: 1;
        padding: 12px;
        border: 1px solid #e0e0e0;
        border-radius: 16px;
        background: white;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    
    .social-btn i { font-size: 18px; }
    
    .social-btn.google { color: #db4437; border-color: #db4437; }
    .social-btn.google:hover { background: #db4437; color: white; }
    .social-btn.facebook { color: #4267b2; border-color: #4267b2; }
    .social-btn.facebook:hover { background: #4267b2; color: white; }
    .social-btn.instagram { color: #e4405f; border-color: #e4405f; }
    .social-btn.instagram:hover { background: linear-gradient(45deg, #f09433, #d62976, #962fbf); color: white; border-color: transparent; }
    .social-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    
    .terms-content h4 {
        font-size: 16px;
        margin: 15px 0 8px 0;
        color: #2e7d32;
    }
    
    .terms-content p {
        font-size: 13px;
        line-height: 1.5;
        color: #555;
        margin-bottom: 10px;
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
        max-width: 500px;
        width: 90%;
        max-height: 80vh;
        overflow: auto;
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
        .social-register { flex-direction: column; }
        .social-btn { justify-content: center; }
    }
    
    @media (max-width: 576px) {
        .register-card { margin: 0 15px; }
        .card-body { padding: 1.5rem !important; }
    }
`;
document.head.appendChild(style);
</script>

</body>
</html>
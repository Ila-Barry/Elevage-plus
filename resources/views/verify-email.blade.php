<!-- resources/views/verify-email.blade.php -->

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification d'email - Élevage+</title>
    <style>
        /* ================= STYLES COMPLETS ================= */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f4f6f9 0%, #e8ecf1 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            background: #ffffff;
            border-radius: 20px;
            padding: 45px 40px 35px;
            max-width: 520px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #4F46E5, #7C3AED, #4F46E5);
            background-size: 200% 100%;
            animation: gradientMove 3s ease-in-out infinite;
        }

        @keyframes gradientMove {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .logo {
            font-size: 28px;
            font-weight: 800;
            color: #4F46E5;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .logo span {
            background: linear-gradient(135deg, #4F46E5, #7C3AED);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .logo-icon {
            font-size: 36px;
            -webkit-text-fill-color: initial;
        }

        .icon {
            font-size: 72px;
            margin-bottom: 15px;
            display: block;
            animation: iconPulse 2s ease-in-out infinite;
        }

        @keyframes iconPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        h1 {
            color: #1f2937;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .subtitle {
            color: #6b7280;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: #4F46E5;
            color: #ffffff;
            padding: 14px 40px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 200px;
            position: relative;
        }

        .button:hover:not(:disabled) {
            background: #4338CA;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.3);
        }

        .button:active:not(:disabled) {
            transform: translateY(0);
        }

        .button:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            transform: none;
        }

        .button-success {
            background: #10b981;
        }

        .button-success:hover:not(:disabled) {
            background: #059669;
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
        }

        .button-danger {
            background: #ef4444;
        }

        .button-danger:hover:not(:disabled) {
            background: #dc2626;
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.3);
        }

        .button-outline {
            background: transparent;
            color: #4F46E5;
            border: 2px solid #4F46E5;
        }

        .button-outline:hover:not(:disabled) {
            background: #4F46E5;
            color: white;
        }

        .error-box {
            color: #ef4444;
            background: #fef2f2;
            padding: 14px 18px;
            border-radius: 12px;
            margin: 15px 0;
            border-left: 4px solid #ef4444;
            text-align: left;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .error-box i {
            font-size: 20px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .success-box {
            color: #10b981;
            background: #f0fdf4;
            padding: 14px 18px;
            border-radius: 12px;
            margin: 15px 0;
            border-left: 4px solid #10b981;
            text-align: left;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .success-box i {
            font-size: 20px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .loader {
            display: inline-block;
            width: 24px;
            height: 24px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top: 3px solid #ffffff;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        .loader-dark {
            border: 3px solid rgba(79, 70, 229, 0.2);
            border-top: 3px solid #4F46E5;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .footer {
            margin-top: 25px;
            color: #9ca3af;
            font-size: 13px;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }

        .footer a {
            color: #4F46E5;
            text-decoration: none;
            font-weight: 500;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .resend-link {
            color: #4F46E5;
            cursor: pointer;
            font-weight: 600;
            transition: color 0.3s;
            background: none;
            border: none;
            font-size: inherit;
            text-decoration: underline;
        }

        .resend-link:hover {
            color: #4338CA;
        }

        .timer {
            font-size: 14px;
            color: #6b7280;
            margin-top: 12px;
        }

        .timer strong {
            color: #4F46E5;
        }

        .state-container {
            animation: fadeSlide 0.4s ease;
        }

        @keyframes fadeSlide {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 640px) {
            .container {
                padding: 30px 20px 25px;
                border-radius: 16px;
            }

            .logo {
                font-size: 22px;
            }

            .icon {
                font-size: 56px;
            }

            h1 {
                font-size: 20px;
            }

            .subtitle {
                font-size: 14px;
            }

            .button {
                padding: 12px 24px;
                font-size: 14px;
                min-width: 160px;
                width: 100%;
            }

            .error-box, .success-box {
                font-size: 14px;
                padding: 12px 14px;
            }

            .footer {
                font-size: 12px;
            }
        }

        @media (max-width: 400px) {
            .container {
                padding: 20px 16px;
            }

            .logo {
                font-size: 18px;
            }

            .icon {
                font-size: 44px;
            }

            h1 {
                font-size: 18px;
            }

            .button {
                padding: 10px 16px;
                font-size: 13px;
                min-width: 140px;
            }
        }

        /* ================= TOAST ================= */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 380px;
            width: 100%;
        }

        .toast {
            padding: 14px 18px;
            border-radius: 12px;
            color: white;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
            animation: slideInRight 0.4s ease;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .toast-success {
            background: #10b981;
        }

        .toast-error {
            background: #ef4444;
        }

        .toast-info {
            background: #4F46E5;
        }

        .toast-warning {
            background: #f59e0b;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .toast-close {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 18px;
            margin-left: auto;
            opacity: 0.7;
            transition: opacity 0.3s;
        }

        .toast-close:hover {
            opacity: 1;
        }
    </style>
</head>
<body>
    <div class="toast-container" id="toastContainer"></div>

    <div class="container" id="app">
        <div class="logo">
            <span class="logo-icon">🐄</span>
            <span>ÉLEVAGE+</span>
        </div>

        <!-- ========== ÉTAT INITIAL ========== -->
        <div id="initial-state" class="state-container">
            <div class="icon">📧</div>
            <h1>Vérification de votre email</h1>
            <p class="subtitle">
                Un email de vérification a été envoyé à votre adresse.<br>
                Cliquez sur le bouton ci-dessous pour confirmer votre compte.
            </p>
            <button class="button" onclick="verifyEmail()" id="verifyBtn">
                <i class="fas fa-shield-alt"></i> Vérifier mon email
            </button>
            <div class="timer">
                ⏱️ Ce lien expirera dans <strong id="countdown">60:00</strong>
            </div>
            <div class="footer">
                Vous n'avez pas reçu l'email ? 
                <button class="resend-link" onclick="resendVerification()">Renvoyer</button>
            </div>
        </div>

        <!-- ========== ÉTAT CHARGEMENT ========== -->
        <div id="loading-state" class="state-container" style="display: none;">
            <div class="icon">⏳</div>
            <h1>Vérification en cours...</h1>
            <p class="subtitle">Veuillez patienter pendant que nous vérifions votre email.</p>
            <div class="loader" style="margin: 20px auto;"></div>
            <p style="color: #6b7280; font-size: 14px; margin-top: 10px;">Ne fermez pas cette page.</p>
        </div>

        <!-- ========== ÉTAT SUCCÈS ========== -->
        <div id="success-state" class="state-container" style="display: none;">
            <div class="icon" style="animation: none;">✅</div>
            <h1 style="color: #10b981;">Email vérifié avec succès !</h1>
            <div class="success-box">
                <i>🎉</i>
                <div>
                    <strong>Félicitations !</strong> Votre compte est maintenant actif.
                    Vous pouvez accéder à toutes les fonctionnalités de la plateforme.
                </div>
            </div>
            <a href="{{ url('/auth/login') }}" class="button button-success">
                <i class="fas fa-sign-in-alt"></i> Se connecter
            </a>
            <div class="footer">
                <a href="{{ url('/') }}">Retour à l'accueil</a>
            </div>
        </div>

        <!-- ========== ÉTAT ERREUR ========== -->
        <div id="error-state" class="state-container" style="display: none;">
            <div class="icon" style="animation: none;">❌</div>
            <h1 style="color: #ef4444;">Erreur de vérification</h1>
            <div class="error-box" id="error-message">
                <i>⚠️</i>
                <span>Une erreur est survenue lors de la vérification.</span>
            </div>
            <p class="subtitle">Voici quelques solutions :</p>
            <div style="text-align: left; padding: 0 10px; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px; font-size: 14px; color: #4b5563;">
                    <span>1.</span>
                    <span>Vérifiez que vous utilisez le bon lien</span>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px; font-size: 14px; color: #4b5563;">
                    <span>2.</span>
                    <span>Le lien a peut-être expiré</span>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; font-size: 14px; color: #4b5563;">
                    <span>3.</span>
                    <span>Demandez un nouvel email de vérification</span>
                </div>
            </div>
            <div style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: center;">
                <button class="button button-danger" onclick="resendVerification()">
                    <i class="fas fa-envelope"></i> Renvoyer l'email
                </button>
                <a href="{{ url('/auth/login') }}" class="button button-outline">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
            <div class="footer">
                <a href="{{ url('/contact') }}">Besoin d'aide ? Contactez-nous</a>
            </div>
        </div>

        <!-- ========== ÉTAT DÉJÀ VÉRIFIÉ ========== -->
        <div id="already-verified-state" class="state-container" style="display: none;">
            <div class="icon" style="animation: none;">✅</div>
            <h1 style="color: #10b981;">Email déjà vérifié</h1>
            <div class="success-box">
                <i>ℹ️</i>
                <div>
                    <strong>Bon retour !</strong> Votre compte est déjà actif.
                    Vous pouvez vous connecter directement.
                </div>
            </div>
            <a href="{{ url('/auth/login') }}" class="button button-success">
                <i class="fas fa-sign-in-alt"></i> Se connecter
            </a>
            <div class="footer">
                <a href="{{ url('/') }}">Retour à l'accueil</a>
            </div>
        </div>

        <!-- ========== ÉTAT RENVOI ========== -->
        <div id="resend-state" class="state-container" style="display: none;">
            <div class="icon">📨</div>
            <h1>Email renvoyé !</h1>
            <div class="success-box">
                <i>✅</i>
                <div>
                    <strong>Email envoyé avec succès !</strong><br>
                    Vérifiez votre boîte de réception (et vos spams).
                </div>
            </div>
            <p class="subtitle">Cliquez sur le lien dans l'email pour vérifier votre compte.</p>
            <button class="button" onclick="verifyEmail()">
                <i class="fas fa-sync"></i> Vérifier maintenant
            </button>
            <div class="footer">
                <button class="resend-link" onclick="showState('initial')">Retour</button>
            </div>
        </div>
    </div>

    <script>
        // ================= CONFIGURATION =================
        const VERIFY_URL = "{!! $verify_url ?? '' !!}";
        const API_URL = "{{ url('/api') }}";
        const IS_VERIFIED = {{ isset($success) ? 'true' : 'false' }};
        let countdownInterval = null;

        // ================= FONCTIONS UTILITAIRES =================

        // Afficher un toast
        function showToast(message, type = 'info', duration = 4000) {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            
            const icons = {
                success: '✅',
                error: '❌',
                warning: '⚠️',
                info: 'ℹ️'
            };
            
            toast.innerHTML = `
                <span>${icons[type] || 'ℹ️'}</span>
                <span>${message}</span>
                <button class="toast-close" onclick="this.parentElement.remove()">✕</button>
            `;
            
            container.appendChild(toast);
            
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateX(100%)';
                    setTimeout(() => toast.remove(), 300);
                }
            }, duration);
        }

        // Afficher un état
        function showState(state) {
            document.querySelectorAll('#app > .state-container').forEach(el => {
                el.style.display = 'none';
            });
            
            const target = document.getElementById(state + '-state');
            if (target) {
                target.style.display = 'block';
                target.style.animation = 'none';
                setTimeout(() => {
                    target.style.animation = 'fadeSlide 0.4s ease';
                }, 10);
            }
        }

        // ================= COMPTEUR À REBOURS =================
        function startCountdown() {
            let minutes = 60;
            let seconds = 0;
            
            if (countdownInterval) clearInterval(countdownInterval);
            
            countdownInterval = setInterval(() => {
                seconds--;
                if (seconds < 0) {
                    seconds = 59;
                    minutes--;
                }
                
                if (minutes < 0) {
                    clearInterval(countdownInterval);
                    document.getElementById('countdown').textContent = 'Expiré';
                    document.getElementById('verifyBtn').disabled = true;
                    document.getElementById('verifyBtn').textContent = '⏰ Lien expiré';
                    showToast('Le lien de vérification a expiré. Demandez un nouvel email.', 'warning');
                    return;
                }
                
                const timeStr = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
                document.getElementById('countdown').textContent = timeStr;
                
                // Avertir quand il reste moins de 5 minutes
                if (minutes < 5 && seconds === 0) {
                    showToast('⚠️ Le lien expire dans moins de 5 minutes !', 'warning');
                }
            }, 1000);
        }

        // ================= VÉRIFICATION EMAIL =================
        async function verifyEmail() {
            if (!VERIFY_URL) {
                document.getElementById('error-message').innerHTML = `
                    <i>⚠️</i>
                    <span>Lien de vérification manquant ou invalide.</span>
                `;
                showState('error');
                return;
            }

            const btn = document.getElementById('verifyBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="loader"></span> Vérification...';
            
            showState('loading');

            try {
                const response = await fetch(VERIFY_URL, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    credentials: 'include'
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Erreur HTTP:', response.status, errorText);
                    throw new Error(`Erreur ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();
                console.log('Réponse vérification:', data);

                if (data.status === 'success' || data.message === 'Email vérifié avec succès.') {
                    showToast('🎉 Email vérifié avec succès !', 'success');
                    showState('success');
                } else if (data.message && data.message.includes('déjà vérifié')) {
                    showToast('ℹ️ Cet email est déjà vérifié', 'info');
                    showState('already-verified');
                } else {
                    document.getElementById('error-message').innerHTML = `
                        <i>⚠️</i>
                        <span>${data.message || 'Erreur lors de la vérification.'}</span>
                    `;
                    showState('error');
                }
            } catch (error) {
                console.error('Erreur détaillée:', error);
                document.getElementById('error-message').innerHTML = `
                    <i>⚠️</i>
                    <span>${error.message || 'Erreur de connexion au serveur. Vérifiez votre connexion internet.'}</span>
                `;
                showState('error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-shield-alt"></i> Vérifier mon email';
            }
        }

        // ================= RENVOYER EMAIL =================
        async function resendVerification() {
            const token = localStorage.getItem('access_token');
            
            if (!token) {
                showToast('Vous devez être connecté pour renvoyer l\'email.', 'error');
                showState('error');
                return;
            }
            
            const btn = document.querySelector('#error-state .button-danger') || 
                        document.querySelector('#initial-state .resend-link');
            
            if (btn) {
                btn.disabled = true;
                btn.textContent = '⏳ Envoi...';
            }
            
            showState('loading');
            document.querySelector('#loading-state .subtitle').textContent = 
                'Renvoyé de l\'email de vérification en cours...';

            try {
                const response = await fetch(`${API_URL}/email/resend`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + token
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    showToast('📨 Email de vérification renvoyé avec succès !', 'success');
                    showState('resend');
                } else {
                    document.getElementById('error-message').innerHTML = `
                        <i>⚠️</i>
                        <span>${data.message || 'Erreur lors du renvoi.'}</span>
                    `;
                    showState('error');
                }
            } catch (error) {
                document.getElementById('error-message').innerHTML = `
                    <i>⚠️</i>
                    <span>${error.message || 'Erreur de connexion au serveur.'}</span>
                `;
                showState('error');
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.textContent = 'Renvoyer l\'email';
                }
            }
        }

        // ================= INITIALISATION =================
        document.addEventListener('DOMContentLoaded', function() {
            // Démarrer le compte à rebours
            startCountdown();
            
            // Vérifier l'état initial
            if (VERIFY_URL) {
                // Vérifier si l'email est déjà vérifié via le backend
                if (IS_VERIFIED) {
                    showState('already-verified');
                } else {
                    showState('initial');
                }
            } else {
                document.getElementById('error-message').innerHTML = `
                    <i>⚠️</i>
                    <span>Aucun lien de vérification fourni ou lien invalide.</span>
                `;
                showState('error');
            }
        });

        // Nettoyer l'intervalle si la page est fermée
        window.addEventListener('beforeunload', function() {
            if (countdownInterval) clearInterval(countdownInterval);
        });
    </script>
</body>
</html>
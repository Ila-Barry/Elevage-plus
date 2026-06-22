<!-- resources/views/verify-email.blade.php -->

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification d'email - Élevage+</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background: #ffffff;
            border-radius: 12px;
            padding: 40px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
        }
        .logo {
            font-size: 32px;
            font-weight: bold;
            color: #4F46E5;
            margin-bottom: 20px;
        }
        .icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
        h1 {
            color: #1f2937;
            margin-bottom: 10px;
        }
        p {
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .button {
            display: inline-block;
            background: #4F46E5;
            color: #ffffff;
            padding: 14px 40px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
            border: none;
            cursor: pointer;
            transition: background 0.3s;
        }
        .button:hover {
            background: #4338CA;
        }
        .button:disabled {
            background: #9ca3af;
            cursor: not-allowed;
        }
        .error {
            color: #ef4444;
            background: #fef2f2;
            padding: 12px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .success {
            color: #10b981;
            background: #f0fdf4;
            padding: 12px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .loader {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #ffffff;
            border-top: 3px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .footer {
            margin-top: 30px;
            color: #9ca3af;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container" id="app">
        <div class="logo">🐄 Élevage+</div>
        
        <div id="initial-state">
            <div class="icon">📧</div>
            <h1>Vérification de votre email</h1>
            <p>Cliquez sur le bouton ci-dessous pour vérifier votre adresse email.</p>
            <button class="button" onclick="verifyEmail()">Vérifier mon email</button>
            <div class="footer">Ce lien expirera dans 60 minutes.</div>
        </div>

        <div id="loading-state" style="display: none;">
            <div class="icon">⏳</div>
            <h1>Vérification en cours...</h1>
            <p>Veuillez patienter pendant que nous vérifions votre email.</p>
            <div class="loader"></div>
        </div>

        <div id="success-state" style="display: none;">
            <div class="icon">✅</div>
            <h1>Email vérifié avec succès !</h1>
            <p>Votre compte est maintenant actif. Vous pouvez vous connecter.</p>
            <a href="{{ url('/auth/login') }}" class="button">Se connecter</a>
        </div>

        <div id="error-state" style="display: none;">
            <div class="icon">❌</div>
            <h1>Erreur de vérification</h1>
            <div class="error" id="error-message"></div>
            <p>Veuillez réessayer ou demander un nouvel email de vérification.</p>
            <button class="button" onclick="resendVerification()">Renvoyer l'email</button>
        </div>

        <div id="already-verified-state" style="display: none;">
            <div class="icon">✅</div>
            <h1>Email déjà vérifié</h1>
            <p>Votre compte est déjà actif. Vous pouvez vous connecter.</p>
            <a href="{{ url('/login') }}" class="button">Se connecter</a>
        </div>
    </div>

    <script>
        // ================= CONFIGURATION =================
        const VERIFY_URL = "{!! $verify_url ?? '' !!}";
        const API_URL = "{{ url('/api') }}";
        const IS_VERIFIED = {{ isset($success) ? 'true' : 'false' }};

        console.log('URL de vérification (nettoyée):', VERIFY_URL);

        function showState(state) {
            document.querySelectorAll('#app > div[id$="-state"]').forEach(el => {
                el.style.display = 'none';
            });
            document.getElementById(state + '-state').style.display = 'block';
        }

        async function verifyEmail() {
            if (!VERIFY_URL) {
                document.getElementById('error-message').textContent = 'Lien de vérification manquant.';
                showState('error');
                return;
            }

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

                // Vérifier si la réponse est OK
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Erreur HTTP:', response.status, errorText);
                    throw new Error(`Erreur ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();
                console.log('Réponse vérification:', data);

                if (data.status === 'success') {
                    localStorage.setItem('email_verified', 'true');
                    showState('success');
                } else if (data.message === 'Email déjà vérifié.') {
                    showState('already-verified');
                } else {
                    document.getElementById('error-message').textContent = data.message || 'Erreur lors de la vérification.';
                    showState('error');
                }
            } catch (error) {
                console.error('Erreur détaillée:', error);
                document.getElementById('error-message').textContent = error.message || 'Erreur de connexion au serveur.';
                showState('error');
            }
        }

        async function resendVerification() {
            const token = localStorage.getItem('access_token');
            
            if (!token) {
                document.getElementById('error-message').textContent = 'Vous devez être connecté pour renvoyer l\'email.';
                showState('error');
                return;
            }
            
            showState('loading');
            document.querySelector('#loading-state p').textContent = 'Renvoyé de l\'email de vérification...';

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
                    alert('Email de vérification renvoyé avec succès !');
                    showState('initial');
                } else {
                    document.getElementById('error-message').textContent = data.message || 'Erreur lors du renvoi.';
                    showState('error');
                }
            } catch (error) {
                document.getElementById('error-message').textContent = 'Erreur de connexion au serveur.';
                showState('error');
            }
        }

        // Vérifier l'état initial réel
if (VERIFY_URL) {
    // On reste sur l'état initial pour que l'utilisateur clique sur "Vérifier mon email"
    showState('initial'); 
} else {
    document.getElementById('error-message').textContent = 'Aucun lien de vérification fourni ou lien invalide.';
    showState('error');
}
    </script>
</body>
</html>
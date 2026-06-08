
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Élevage+</title>

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
            <div class="logo">
                <i class="fas fa-tractor"></i>
                <span>ÉLEVAGE+</span>
            </div>
            <p class="login-subtitle">Pas de inscrit ? 
                <a href="#" class="signup-link">S'inscrire</a>,
            </p>
        </div>

        <!-- <img src="{{asset('images/img-elevage.jpeg')}}" > -->

        <div class="login-card">
            <!-- Titre du formulaire -->
            <div class="form-title">
                <i class="fas fa-sign-in-alt"></i>
                <span>se connecter à mon COMPTE</span>
            </div>

            <!-- Formulaire de connexion -->
            <form class="login-form" method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Champ Email / Téléphone -->
                <div class="input-group-custom">
                    <div class="input-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <input type="text" name="login" placeholder="Adress e-mail ou Numéro de téléphone" required autofocus>
                </div>

                <!-- Champ Mot de passe -->
                <div class="input-group-custom">
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <input type="password" name="password" placeholder="Mot de pass" required>
                </div>

                <!-- Options supplémentaires -->
                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember">
                        <span class="checkmark"></span>
                        se souvenir de moi
                    </label>
                    <a href="#" class="forgot-link">mot de pass oublier ?</a>
                </div>

                <!-- Bouton de connexion -->
                <button type="submit" class="login-btn">
                    Se connecter
                </button>
            </form>

            <!-- Séparateur -->
            <div class="divider">
                <span>Ou</span>
            </div>

            <!-- Connexion avec réseaux sociaux -->
            <div class="social-login">
                <button class="social-btn google">
                    <i class="fab fa-google"></i>
                    Google
                </button>
                <button class="social-btn facebook">
                    <i class="fab fa-facebook-f"></i>
                    facebook
                </button>
                <button class="social-btn instagram">
                    <i class="fab fa-instagram"></i>
                    Instagramme
                </button>
            </div>

            <!-- Lien d'inscription en bas -->
            <div class="signup-footer">
                Pas de compte: <a href="#">S'inscrire</a>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
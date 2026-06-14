<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Inscription | ÉLEVAGE+</title>

    <!-- Bootstrap 4.6 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome 6 (gratuit) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Google Fonts + style personnalisé -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/authCSS/register.css') }}">
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
    <div class="row no-gutters">
        <div class="col-12">
            <!-- Image d'arrière-plan -->
            <div class="bg-image" style="background-image: url('/images/elel.jpeg'); height: 100vh; background-size: cover; background-position: center;"></div>
        </div>
    </div>
</div>

<div class="container py-4 py-md-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-12">
            <!-- Carte principale (card) identique à la maquette -->
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-body p-4 p-md-5">

                    <!-- En-tête : Logo + lien connexion (Déja inscrit ?) -->
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2 logo-wrapper">
                            <i class="fas fa-paw text-success fs-2"></i>
                            <span class="logo-text">ÉLEVAGE+</span>
                        </div>
                        <div class="mt-2 mt-sm-0">
                            <a href="#" class="text-decoration-none fw-semibold small-link">
                                <i class="fas fa-sign-in-alt me-1"></i> Déja inscrit ? Se connecter
                            </a>
                        </div>
                    </div>

                    <!-- Titre central CRÉER UN COMPTE -->
                    <div class="text-center mb-4 mt-2">
                        <h3 class="fw-bold text-dark mb-1">CRÉER UN COMPTE</h3>
                        <div class="underline-title mx-auto"></div>
                    </div>

                    <!-- Formulaire d'inscription (champs + icônes exacts comme sur l'image) -->
                    <form action="#" method="POST" id="registerForm">
                        @csrf

                        <!-- 1. NOM (icône utilisateur) -->
                        <div class="form-group mb-4">
                            <label class="form-label fw-semibold">Nom</label>
                            <div class="input-group custom-input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-user text-muted"></i></span>
                                </div>
                                <input type="text" class="form-control border-start-0 ps-0" placeholder="complet" required>
                            </div>
                        </div>

                        <!-- 2. ADRESS E-MAIL (icône envelope) -->
                        <div class="form-group mb-4">
                            <label class="form-label fw-semibold">Adress e-mail</label>
                            <div class="input-group custom-input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                </div>
                                <input type="email" class="form-control border-start-0 ps-0" placeholder="e-mail" required>
                            </div>
                        </div>

                        <!-- 3. NUMÉRO TÉLÉPHONE (icône phone) -->
                        <div class="form-group mb-4">
                            <label class="form-label fw-semibold">Numéro téléphone</label>
                            <div class="input-group custom-input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-phone-alt text-muted"></i></span>
                                </div>
                                <input type="tel" class="form-control border-start-0 ps-0" placeholder="téléphone" required>
                            </div>
                        </div>

                        <!-- 4. MOT DE PASSE (icône cadenas) -->
                        <div class="form-group mb-4">
                            <label class="form-label fw-semibold">Mot de pass</label>
                            <div class="input-group custom-input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-lock text-muted"></i></span>
                                </div>
                                <input type="password" class="form-control border-start-0 ps-0" placeholder="mot de passe" id="password" required>
                            </div>
                        </div>

                        <!-- 5. CONFIRMER MOT DE PASSE -->
                        <div class="form-group mb-4">
                            <label class="form-label fw-semibold">Confirmer mot de pass</label>
                            <div class="input-group custom-input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-lock text-muted"></i></span>
                                </div>
                                <input type="password" class="form-control border-start-0 ps-0" placeholder="confirmer mot de passe" id="password_confirmation" required>
                            </div>
                            <small class="text-danger password-match-error d-none">Les mots de passe ne correspondent pas.</small>
                        </div>

                        <!-- 6. TYPE(S) ÉLEVAGE (icône tractor / élevage) -->
                        <div class="form-group mb-4">
                            <label class="form-label fw-semibold">Type(s) élevage</label>
                            <div class="input-group custom-input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-tractor text-muted"></i></span>
                                </div>
                                <input type="text" class="form-control border-start-0 ps-0" placeholder="élevage" required>
                            </div>
                        </div>

                        <!-- 7. ADRESS DOMICILE (icône maison) -->
                        <div class="form-group mb-4">
                            <label class="form-label fw-semibold">adress domicile</label>
                            <div class="input-group custom-input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-home text-muted"></i></span>
                                </div>
                                <input type="text" class="form-control border-start-0 ps-0" placeholder="domicile" required>
                            </div>
                        </div>

                        <!-- 8. Case à cocher conditions générales -->
                        <div class="form-group form-check mb-4 d-flex align-items-center">
                            <input class="form-check-input me-2" type="checkbox" id="acceptTerms" style="width: 18px; height: 18px;" required>
                            <label class="form-check-label text-dark" for="acceptTerms">
                                j'accepte les conditions d'utilisation
                            </label>
                        </div>

                        <!-- Bouton S'INSCRIRE -->
                        <button type="submit" class="btn btn-success btn-block rounded-pill py-3 fw-bold btn-submit w-100 shadow-sm">
                            S'INSCRIRE <i class="fas fa-arrow-right ms-2"></i>
                        </button>

                        <!-- Lien supplémentaire en bas: Déja un compte: Se connecter -->
                        <div class="text-center mt-4">
                            <span class="text-muted">Déja un compte :</span>
                            <a href="" class="fw-bold text-decoration-none ms-1 link-login">Se connecter</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<!-- jQuery + Bootstrap JS (nécessaire pour certains composants) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Petit script pour vérifier la correspondance des mots de passe (UX propre, sans dénaturer la maquette) -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('registerForm');
        const pwd = document.getElementById('password');
        const pwdConfirm = document.getElementById('password_confirmation');
        const errorSpan = document.querySelector('.password-match-error');

        function validatePasswordMatch() {
            if (pwd.value !== pwdConfirm.value && pwdConfirm.value !== '') {
                errorSpan.classList.remove('d-none');
                return false;
            } else {
                errorSpan.classList.add('d-none');
                return true;
            }
        }

        pwdConfirm.addEventListener('input', validatePasswordMatch);
        pwd.addEventListener('input', validatePasswordMatch);

        form.addEventListener('submit', function(e) {
            const isMatch = validatePasswordMatch();
            const termsAccepted = document.getElementById('acceptTerms').checked;
            if (!isMatch) {
                e.preventDefault();
                alert('Veuillez vérifier que les mots de passe correspondent.');
            } else if (!termsAccepted) {
                e.preventDefault();
                alert('Vous devez accepter les conditions d\'utilisation pour vous inscrire.');
            } else {
                // formulaire valide (simulation inscription)
                e.preventDefault();
                alert('Formulaire soumis avec succès (démo). Tous les champs sont valides.');
                // Vous pouvez supprimer cette ligne et laisser l'action réelle du formulaire.
            }
        });
    });
</script>
</body>
</html>
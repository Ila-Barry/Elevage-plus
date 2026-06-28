@extends('layouts.app')

@section('title', 'Vérification 2FA - Élevage+')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-shield-alt"></i> Authentification à deux facteurs</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted">Un code de vérification a été envoyé à votre adresse email.</p>
                    
                    <div id="errorMessage" class="alert alert-danger" style="display: none;">
                        <i class="fas fa-exclamation-circle"></i> <span id="errorText"></span>
                    </div>
                    
                    <form id="verify2faForm">
                        <div class="form-group">
                            <label>Code de vérification</label>
                            <input type="text" class="form-control form-control-lg text-center" id="code" 
                                   placeholder="123456" maxlength="6" autofocus>
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-block btn-lg" id="verifyBtn">
                            <span class="btn-text">Vérifier</span>
                            <span class="btn-loader" style="display: none;">
                                <i class="fas fa-spinner fa-spin"></i>
                            </span>
                        </button>
                        
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-link" id="resendCodeBtn">
                                <i class="fas fa-redo"></i> Renvoyer le code
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('verify2faForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const code = document.getElementById('code').value.trim();
    if (!code || code.length < 6) {
        showError('Veuillez entrer le code à 6 chiffres.');
        return;
    }
    
    const btn = document.getElementById('verifyBtn');
    btn.querySelector('.btn-text').style.display = 'none';
    btn.querySelector('.btn-loader').style.display = 'inline-block';
    btn.disabled = true;
    
    try {
        const userId = new URLSearchParams(window.location.search).get('user_id');
        const result = await window.API.verifyTwoFactor({
            two_factor_code: code,
            user_id: userId
        });
        
        if (result && result.status === 'success') {
            // Stocker le token
            if (result.data && result.data.access_token) {
                localStorage.setItem('access_token', result.data.access_token);
                localStorage.setItem('user', JSON.stringify(result.data.user));
            }
            window.location.href = '/dashboard';
        } else {
            showError(result.message || 'Code invalide. Veuillez réessayer.');
            document.getElementById('code').value = '';
            document.getElementById('code').focus();
        }
    } catch (error) {
        showError('Une erreur est survenue. Veuillez réessayer.');
    } finally {
        btn.querySelector('.btn-text').style.display = 'inline-block';
        btn.querySelector('.btn-loader').style.display = 'none';
        btn.disabled = false;
    }
});

function showError(message) {
    const errorDiv = document.getElementById('errorMessage');
    document.getElementById('errorText').textContent = message;
    errorDiv.style.display = 'block';
    setTimeout(() => {
        errorDiv.style.opacity = '0';
        setTimeout(() => {
            errorDiv.style.display = 'none';
            errorDiv.style.opacity = '1';
        }, 300);
    }, 5000);
}

document.getElementById('resendCodeBtn').addEventListener('click', async function() {
    this.disabled = true;
    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi...';
    
    // Simuler un renvoi de code
    setTimeout(() => {
        this.innerHTML = '<i class="fas fa-check"></i> Code renvoyé !';
        setTimeout(() => {
            this.innerHTML = '<i class="fas fa-redo"></i> Renvoyer le code';
            this.disabled = false;
        }, 2000);
    }, 1500);
});

// Auto-focus sur le champ code
document.getElementById('code').focus();

// Permettre seulement les chiffres
document.getElementById('code').addEventListener('input', function() {
    this.value = this.value.replace(/\D/g, '').slice(0, 6);
});
</script>
@endsection
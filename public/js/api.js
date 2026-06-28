// Configuration de l'API
const API_BASE_URL = '/api';

// Récupérer le token depuis localStorage
function getToken() {
    return localStorage.getItem('access_token');
}

// Récupérer l'utilisateur depuis localStorage
function getUser() {
    const userStr = localStorage.getItem('user');
    if (userStr) {
        try {
            return JSON.parse(userStr);
        } catch (e) {
            return null;
        }
    }
    return null;
}

// Définir le token dans les headers
function getAuthHeaders() {
    const token = getToken();
    const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    };
    
    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
    }
    
    // Ajouter le token CSRF
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        headers['X-CSRF-TOKEN'] = csrfToken.content;
    }
    
    return headers;
}

// Fonction d'appel API générique
async function apiCall(endpoint, method = 'GET', data = null, requiresAuth = true) {
    const url = `${API_BASE_URL}${endpoint}`;
    
    const options = {
        method: method,
        headers: requiresAuth ? getAuthHeaders() : {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    };
    
    if (data && (method === 'POST' || method === 'PUT' || method === 'PATCH')) {
        options.body = JSON.stringify(data);
    }
    
    try {
        const response = await fetch(url, options);
        const result = await response.json();
        
        // Si token expiré, rediriger vers login
        if (response.status === 401) {
            localStorage.removeItem('access_token');
            localStorage.removeItem('user');
            if (!window.location.pathname.includes('/auth/login') && 
                !window.location.pathname.includes('/auth/register')) {
                window.location.href = '/auth/login';
            }
            return null;
        }
        
        return result;
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}

// Fonction pour rediriger selon le rôle
function redirectBasedOnRole(user) {
    if (!user) {
        window.location.href = '/auth/login';
        return;
    }
    
    // Vérifier le rôle
    if (user.role === 'admin') {
        window.location.href = '/admin/dashboard';
    } else {
        window.location.href = '/dashboard';
    }
}

// Fonctions d'authentification
const API = {
    getToken,
    getUser,
    getAuthHeaders,
    apiCall,
    redirectBasedOnRole,
    
    async register(data) {
        const result = await apiCall('/auth/register', 'POST', data, false);
        if (result && result.status === 'success' && result.data) {
            // Stocker le token
            if (result.data.access_token) {
                localStorage.setItem('access_token', result.data.access_token);
            }
            // Stocker l'utilisateur
            if (result.data.user) {
                localStorage.setItem('user', JSON.stringify(result.data.user));
            }
        }
        return result;
    },
    
    async login(data) {
        const result = await apiCall('/auth/login', 'POST', data, false);
        if (result && result.status === 'success' && result.data) {
            // Stocker le token
            if (result.data.access_token) {
                localStorage.setItem('access_token', result.data.access_token);
            }
            // Stocker l'utilisateur
            if (result.data.user) {
                localStorage.setItem('user', JSON.stringify(result.data.user));
            }
        }
        return result;
    },
    
    async logout() {
        const result = await apiCall('/auth/logout', 'POST', null, true);
        localStorage.removeItem('access_token');
        localStorage.removeItem('user');
        return result;
    },
    
    async getCurrentUser() {
        return apiCall('/auth/me', 'GET', null, true);
    },
    
    async updateProfile(data) {
        return apiCall('/auth/profile', 'PUT', data, true);
    },
    
    async changePassword(data) {
        return apiCall('/auth/change-password', 'PUT', data, true);
    },
    
    async verifyTwoFactor(data) {
        return apiCall('/auth/verify-2fa', 'POST', data, false);
    },
    
    async refreshToken() {
        return apiCall('/auth/refresh', 'POST', null, true);
    }
};

// Exposer l'API globalement
window.API = API;

// Vérifier l'authentification au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    const token = getToken();
    const user = getUser();
    const currentPath = window.location.pathname;
    const authPages = ['/auth/login', '/auth/register', '/auth/verify-2fa'];
    const protectedPages = ['/dashboard', '/elevages', '/animaux', '/taches', '/stocks', '/messages', '/notification', '/auth/profile', '/auth/parametre'];
    const adminPages = ['/admin/dashboard', '/admin/utilisateur', '/admin/publication', '/admin/signale', '/admin/statistique'];
    
    // Si l'utilisateur est connecté
    if (token && user) {
        // Sur les pages d'auth, rediriger selon le rôle
        if (authPages.some(page => currentPath === page)) {
            redirectBasedOnRole(user);
            return;
        }
        
        // Vérifier si l'utilisateur essaie d'accéder à une page admin sans droits
        if (adminPages.some(page => currentPath.startsWith(page)) && user.role !== 'admin') {
            window.location.href = '/dashboard';
            return;
        }
    }
    
    // Si l'utilisateur n'est pas connecté et essaie d'accéder à une page protégée
    if (!token && protectedPages.some(page => currentPath.startsWith(page))) {
        window.location.href = '/auth/login';
        return;
    }
    
    // Si l'utilisateur n'est pas connecté et essaie d'accéder à une page admin
    if (!token && adminPages.some(page => currentPath.startsWith(page))) {
        window.location.href = '/auth/login';
        return;
    }
});

console.log('API JavaScript chargée avec succès');

// Vérifier l'authentification au chargement de chaque page
    document.addEventListener('DOMContentLoaded', function() {
        // Ne pas exécuter sur les pages d'auth
        const authPages = ['/auth/login', '/auth/register', '/auth/verify-2fa'];
        const currentPath = window.location.pathname;
        
        if (authPages.includes(currentPath)) {
            return;
        }
        
        // Vérifier si l'utilisateur est connecté
        const token = localStorage.getItem('access_token');
        const userStr = localStorage.getItem('user');
        
        if (!token || !userStr) {
            // Rediriger vers login si non connecté
            window.location.href = '/auth/login';
            return;
        }
        
        try {
            const user = JSON.parse(userStr);
            
            // Vérifier si l'utilisateur essaie d'accéder à une page admin sans droits
            const adminPages = ['/admin/dashboard', '/admin/utilisateur', '/admin/publication', '/admin/signale', '/admin/statistique'];
            if (adminPages.some(page => currentPath.startsWith(page)) && user.role !== 'admin') {
                window.location.href = '/dashboard';
                return;
            }
        } catch (e) {
            console.error('Erreur lors du parsing de l\'utilisateur:', e);
            window.location.href = '/auth/login';
        }
    });
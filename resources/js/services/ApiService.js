// resources/js/services/ApiService.js

/**
 * Service ApiService
 * 
 * Gère toutes les communications avec l'API backend
 * Inclut la gestion des tokens JWT, des erreurs et des retries
 */
class ApiService {
    constructor() {
        this.baseUrl = '/api';
        this.token = localStorage.getItem('access_token');
        this.refreshToken = localStorage.getItem('refresh_token');
        this.tokenExpiry = localStorage.getItem('token_expiry');
        this.isRefreshing = false;
        this.failedQueue = [];
    }

    /**
     * Récupère les headers par défaut
     */
    getHeaders() {
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        };

        // Ajouter le token CSRF si disponible
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            headers['X-CSRF-TOKEN'] = csrfToken.content;
        }

        // Ajouter le token JWT si disponible
        if (this.token) {
            headers['Authorization'] = `Bearer ${this.token}`;
        }

        return headers;
    }

    /**
     * Vérifie si le token est expiré
     */
    isTokenExpired() {
        if (!this.tokenExpiry) return true;
        const expiry = new Date(this.tokenExpiry);
        return expiry <= new Date();
    }

    /**
     * Rafraîchit le token JWT
     */
    async refreshAccessToken() {
        if (this.isRefreshing) {
            // Si déjà en cours, attendre la résolution
            return new Promise((resolve, reject) => {
                this.failedQueue.push({ resolve, reject });
            });
        }

        this.isRefreshing = true;

        try {
            const response = await fetch(`${this.baseUrl}/auth/refresh`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${this.refreshToken || this.token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Refresh token failed');
            }

            const data = await response.json();
            
            // Mettre à jour les tokens
            this.setTokens(data.data.access_token, data.data.expires_in);
            
            // Traiter la file d'attente
            this.failedQueue.forEach(({ resolve }) => resolve());
            this.failedQueue = [];

            return data;
        } catch (error) {
            // Échec du refresh - déconnecter l'utilisateur
            this.clearTokens();
            this.failedQueue.forEach(({ reject }) => reject(error));
            this.failedQueue = [];
            throw error;
        } finally {
            this.isRefreshing = false;
        }
    }

    /**
     * Stocke les tokens
     */
    setTokens(accessToken, expiresIn = 3600) {
        this.token = accessToken;
        localStorage.setItem('access_token', accessToken);
        
        const expiry = new Date();
        expiry.setSeconds(expiry.getSeconds() + expiresIn);
        this.tokenExpiry = expiry.toISOString();
        localStorage.setItem('token_expiry', this.tokenExpiry);
    }

    /**
     * Supprime les tokens
     */
    clearTokens() {
        this.token = null;
        this.refreshToken = null;
        this.tokenExpiry = null;
        localStorage.removeItem('access_token');
        localStorage.removeItem('refresh_token');
        localStorage.removeItem('token_expiry');
        localStorage.removeItem('user');
    }

    /**
     * Effectue une requête HTTP avec gestion automatique du token
     */
    async request(endpoint, options = {}) {
        const url = `${this.baseUrl}${endpoint}`;
        const config = {
            ...options,
            headers: {
                ...this.getHeaders(),
                ...options.headers
            }
        };

        try {
            const response = await fetch(url, config);
            
            // Si token expiré, tenter de le rafraîchir
            if (response.status === 401 && this.token) {
                await this.refreshAccessToken();
                // Réessayer la requête avec le nouveau token
                config.headers = {
                    ...this.getHeaders(),
                    ...options.headers
                };
                const retryResponse = await fetch(url, config);
                return this.handleResponse(retryResponse);
            }

            return this.handleResponse(response);
        } catch (error) {
            return this.handleError(error);
        }
    }

    /**
     * Traite la réponse HTTP
     */
    async handleResponse(response) {
        const contentType = response.headers.get('content-type');
        const isJson = contentType && contentType.includes('application/json');
        
        const data = isJson ? await response.json() : await response.text();

        if (!response.ok) {
            throw {
                status: response.status,
                statusText: response.statusText,
                data: data
            };
        }

        return data;
    }

    /**
     * Gère les erreurs
     */
    handleError(error) {
        console.error('API Error:', error);
        
        // Erreurs réseau
        if (!error.status) {
            return {
                success: false,
                message: 'Erreur de connexion au serveur. Vérifiez votre connexion Internet.',
                error: error
            };
        }

        // Erreurs HTTP
        const errorMessages = {
            400: 'Requête invalide. Veuillez vérifier vos données.',
            401: 'Session expirée. Veuillez vous reconnecter.',
            403: 'Accès interdit. Vous n\'avez pas les droits nécessaires.',
            404: 'Ressource non trouvée.',
            422: 'Erreur de validation. Veuillez corriger les champs.',
            429: 'Trop de requêtes. Veuillez patienter.',
            500: 'Erreur serveur. Veuillez réessayer plus tard.'
        };

        return {
            success: false,
            message: errorMessages[error.status] || 'Une erreur est survenue.',
            errors: error.data?.errors || null,
            status: error.status
        };
    }

    // ===== MÉTHODES HTTP =====

    get(endpoint, options = {}) {
        return this.request(endpoint, { ...options, method: 'GET' });
    }

    post(endpoint, data = {}, options = {}) {
        return this.request(endpoint, {
            ...options,
            method: 'POST',
            body: JSON.stringify(data)
        });
    }

    put(endpoint, data = {}, options = {}) {
        return this.request(endpoint, {
            ...options,
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }

    patch(endpoint, data = {}, options = {}) {
        return this.request(endpoint, {
            ...options,
            method: 'PATCH',
            body: JSON.stringify(data)
        });
    }

    delete(endpoint, options = {}) {
        return this.request(endpoint, { ...options, method: 'DELETE' });
    }

    /**
     * Upload de fichier avec FormData
     */
    upload(endpoint, formData, options = {}) {
        const headers = {
            ...this.getHeaders(),
            'Content-Type': 'multipart/form-data'
        };
        // Supprimer Content-Type pour que le navigateur le définisse automatiquement
        delete headers['Content-Type'];

        return this.request(endpoint, {
            ...options,
            method: 'POST',
            body: formData,
            headers
        });
    }
}

// Export d'une instance unique
export default new ApiService();
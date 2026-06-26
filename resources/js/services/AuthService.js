// resources/js/services/AuthService.js

import ApiService from './ApiService';
import NotificationService from './NotificationService';

/**
 * Service AuthService
 * 
 * Gère toutes les opérations d'authentification
 * Inscription, connexion, déconnexion, vérification email, etc.
 */
class AuthService {
    /**
     * Inscription d'un nouvel utilisateur
     */
    async register(userData) {
        try {
            const response = await ApiService.post('/auth/register', userData);
            
            if (response.success !== false) {
                // Stocker le token
                if (response.data?.access_token) {
                    ApiService.setTokens(
                        response.data.access_token,
                        response.data.expires_in || 3600
                    );
                    localStorage.setItem('user', JSON.stringify(response.data.user));
                }
                
                return {
                    success: true,
                    message: response.message || 'Inscription réussie !',
                    data: response.data
                };
            }
            
            return response;
        } catch (error) {
            return ApiService.handleError(error);
        }
    }

    /**
     * Connexion
     */
    async login(credentials) {
        try {
            const response = await ApiService.post('/auth/login', credentials);
            
            if (response.success !== false) {
                // Stocker le token
                if (response.data?.access_token) {
                    ApiService.setTokens(
                        response.data.access_token,
                        response.data.expires_in || 3600
                    );
                    localStorage.setItem('user', JSON.stringify(response.data.user));
                }
                
                return {
                    success: true,
                    message: response.message || 'Connexion réussie !',
                    data: response.data
                };
            }
            
            return response;
        } catch (error) {
            return ApiService.handleError(error);
        }
    }

    /**
     * Déconnexion
     */
    async logout() {
        try {
            await ApiService.post('/auth/logout');
        } catch (error) {
            // Ignorer les erreurs de déconnexion
        } finally {
            ApiService.clearTokens();
            localStorage.removeItem('user');
        }
        
        return { success: true, message: 'Déconnexion réussie.' };
    }

    /**
     * Récupérer l'utilisateur connecté
     */
    async getCurrentUser() {
        try {
            // Vérifier si l'utilisateur est dans localStorage
            const userStr = localStorage.getItem('user');
            if (userStr && !ApiService.isTokenExpired()) {
                const user = JSON.parse(userStr);
                return { success: true, data: { user } };
            }

            // Sinon, appeler l'API
            const response = await ApiService.get('/auth/me');
            
            if (response.success !== false && response.data) {
                localStorage.setItem('user', JSON.stringify(response.data.user));
                return response;
            }
            
            return response;
        } catch (error) {
            return ApiService.handleError(error);
        }
    }

    /**
     * Vérifier si l'utilisateur est authentifié
     */
    isAuthenticated() {
        return !!ApiService.token && !ApiService.isTokenExpired();
    }

    /**
     * Récupérer l'utilisateur depuis localStorage
     */
    getUserFromStorage() {
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

    /**
     * Vérifier si l'utilisateur est admin
     */
    isAdmin() {
        const user = this.getUserFromStorage();
        return user?.role === 'admin';
    }

    /**
     * Mettre à jour le profil
     */
    async updateProfile(data) {
        try {
            const response = await ApiService.put('/auth/profile', data);
            
            if (response.success !== false) {
                // Mettre à jour l'utilisateur dans localStorage
                if (response.data) {
                    const currentUser = this.getUserFromStorage() || {};
                    const updatedUser = { ...currentUser, ...response.data };
                    localStorage.setItem('user', JSON.stringify(updatedUser));
                }
                
                return {
                    success: true,
                    message: response.message || 'Profil mis à jour !',
                    data: response.data
                };
            }
            
            return response;
        } catch (error) {
            return ApiService.handleError(error);
        }
    }

    /**
     * Changer le mot de passe
     */
    async changePassword(data) {
        try {
            const response = await ApiService.put('/auth/change-password', data);
            return response;
        } catch (error) {
            return ApiService.handleError(error);
        }
    }

    /**
     * Envoyer l'email de vérification
     */
    async resendVerificationEmail() {
        try {
            const response = await ApiService.post('/email/resend');
            return response;
        } catch (error) {
            return ApiService.handleError(error);
        }
    }

    /**
     * Vérifier l'email via le lien
     */
    async verifyEmail(id, hash, signature) {
        try {
            const url = `/email/verify/${id}/${hash}?signature=${encodeURIComponent(signature)}`;
            const response = await ApiService.get(url);
            return response;
        } catch (error) {
            return ApiService.handleError(error);
        }
    }

    /**
     * Mettre à jour les préférences de notification
     */
    async updateNotificationPreferences(preferences) {
        try {
            const response = await ApiService.put('/auth/notification-preferences', preferences);
            return response;
        } catch (error) {
            return ApiService.handleError(error);
        }
    }

    /**
     * Mettre à jour la visibilité du profil
     */
    async updateProfileVisibility(visibility) {
        try {
            const response = await ApiService.put('/auth/profile-visibility', { profile_visibility: visibility });
            return response;
        } catch (error) {
            return ApiService.handleError(error);
        }
    }

    /**
     * Supprimer le compte
     */
    async deleteAccount(password, confirmation) {
        try {
            const response = await ApiService.delete('/auth/account', {
                body: JSON.stringify({ password, confirmation_text: confirmation })
            });
            
            if (response.success !== false) {
                ApiService.clearTokens();
                localStorage.removeItem('user');
            }
            
            return response;
        } catch (error) {
            return ApiService.handleError(error);
        }
    }

    /**
     * Activer/Désactiver 2FA
     */
    async toggleTwoFactor() {
        try {
            const response = await ApiService.post('/auth/toggle-2fa');
            return response;
        } catch (error) {
            return ApiService.handleError(error);
        }
    }
}

export default new AuthService();
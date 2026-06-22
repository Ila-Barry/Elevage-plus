// resources/js/stores/auth.js

import { defineStore } from 'pinia';
import AuthService from '../services/AuthService';
import NotificationService from '../services/NotificationService';

/**
 * Store Auth
 * 
 * Gère l'état de l'authentification
 * Compatible avec Vue 3 + Pinia
 */
export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: null,
        isAuthenticated: false,
        isLoading: false,
        error: null
    }),

    getters: {
        isAdmin: (state) => state.user?.role === 'admin',
        isEleveur: (state) => state.user?.role === 'user',
        userName: (state) => state.user?.name || '',
        userEmail: (state) => state.user?.email || '',
        userAvatar: (state) => state.user?.photo_url || null
    },

    actions: {
        /**
         * Initialise le store
         */
        init() {
            // Récupérer l'utilisateur depuis localStorage
            const userStr = localStorage.getItem('user');
            if (userStr) {
                try {
                    this.user = JSON.parse(userStr);
                    this.isAuthenticated = AuthService.isAuthenticated();
                } catch (e) {
                    this.user = null;
                    this.isAuthenticated = false;
                }
            }

            // Initialiser le service de notifications
            if (this.user && this.isAuthenticated) {
                NotificationService.init(this.user.id);
                NotificationService.requestPermission();
            }
        },

        /**
         * Inscription
         */
        async register(userData) {
            this.isLoading = true;
            this.error = null;

            try {
                const result = await AuthService.register(userData);
                
                if (result.success) {
                    this.user = result.data?.user || null;
                    this.isAuthenticated = true;
                    this.token = result.data?.access_token || null;
                    
                    if (this.user) {
                        NotificationService.init(this.user.id);
                        NotificationService.requestPermission();
                    }
                    
                    return result;
                } else {
                    this.error = result.message;
                    return result;
                }
            } catch (error) {
                this.error = error.message || 'Erreur lors de l\'inscription';
                return { success: false, message: this.error };
            } finally {
                this.isLoading = false;
            }
        },

        /**
         * Connexion
         */
        async login(credentials) {
            this.isLoading = true;
            this.error = null;

            try {
                const result = await AuthService.login(credentials);
                
                if (result.success) {
                    this.user = result.data?.user || null;
                    this.isAuthenticated = true;
                    this.token = result.data?.access_token || null;
                    
                    if (this.user) {
                        NotificationService.init(this.user.id);
                        NotificationService.requestPermission();
                    }
                    
                    return result;
                } else {
                    this.error = result.message;
                    return result;
                }
            } catch (error) {
                this.error = error.message || 'Erreur lors de la connexion';
                return { success: false, message: this.error };
            } finally {
                this.isLoading = false;
            }
        },

        /**
         * Déconnexion
         */
        async logout() {
            this.isLoading = true;

            try {
                await AuthService.logout();
                this.user = null;
                this.isAuthenticated = false;
                this.token = null;
                this.error = null;
                return { success: true };
            } catch (error) {
                return { success: false, message: error.message };
            } finally {
                this.isLoading = false;
            }
        },

        /**
         * Récupérer l'utilisateur actuel
         */
        async fetchUser() {
            this.isLoading = true;

            try {
                const result = await AuthService.getCurrentUser();
                
                if (result.success) {
                    this.user = result.data?.user || null;
                    this.isAuthenticated = true;
                    return result;
                } else {
                    this.user = null;
                    this.isAuthenticated = false;
                    return result;
                }
            } catch (error) {
                this.user = null;
                this.isAuthenticated = false;
                return { success: false, message: error.message };
            } finally {
                this.isLoading = false;
            }
        },

        /**
         * Mettre à jour le profil
         */
        async updateProfile(data) {
            this.isLoading = true;

            try {
                const result = await AuthService.updateProfile(data);
                
                if (result.success) {
                    this.user = { ...this.user, ...result.data };
                    return result;
                } else {
                    this.error = result.message;
                    return result;
                }
            } catch (error) {
                this.error = error.message;
                return { success: false, message: this.error };
            } finally {
                this.isLoading = false;
            }
        },

        /**
         * Changer le mot de passe
         */
        async changePassword(data) {
            this.isLoading = true;

            try {
                const result = await AuthService.changePassword(data);
                return result;
            } catch (error) {
                return { success: false, message: error.message };
            } finally {
                this.isLoading = false;
            }
        }
    }
});
<?php $__env->startSection('title', 'Notifications'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/eleveurCSS/notification.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<div class="dashboard-wrapper">

    <div class="notifications-container">

        <!-- En-tête de la page -->
        <div class="notifications-header">
            <div class="header-title">
                <i class="fas fa-bell"></i>
                <h1>Notifications</h1>
                <span class="notification-badge" id="totalNotifCount">12</span>
            </div>
            <div class="header-actions">
                <button class="btn-mark-all" id="markAllReadBtn">
                    <i class="fas fa-check-double"></i>
                    <span>Tout marquer comme lu</span>
                </button>
                <button class="btn-settings" id="notificationSettingsBtn">
                    <i class="fas fa-cog"></i>
                </button>
            </div>
        </div>

        <!-- Filtres -->
        <div class="notifications-filters">
            <button class="filter-btn active" data-filter="all">
                <i class="fas fa-list"></i>
                <span>Toutes</span>
                <span class="filter-count" id="allCount">12</span>
            </button>
            <button class="filter-btn" data-filter="unread">
                <i class="fas fa-envelope"></i>
                <span>Non lues</span>
                <span class="filter-count" id="unreadCount">5</span>
            </button>
            <button class="filter-btn" data-filter="read">
                <i class="fas fa-envelope-open"></i>
                <span>Lues</span>
                <span class="filter-count" id="readCount">7</span>
            </button>
            <div class="filter-divider"></div>
            <button class="filter-btn" data-filter="alert">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Alertes</span>
            </button>
            <button class="filter-btn" data-filter="message">
                <i class="fas fa-comment"></i>
                <span>Messages</span>
            </button>
            <button class="filter-btn" data-filter="task">
                <i class="fas fa-tasks"></i>
                <span>Tâches</span>
            </button>
            <button class="filter-btn" data-filter="stock">
                <i class="fas fa-box"></i>
                <span>Stocks</span>
            </button>
        </div>

        <!-- Liste des notifications -->
        <div class="notifications-list" id="notificationsList">

            <!-- Notification 1 - Alerte stock critique -->
            <div class="notification-item unread" data-type="stock" data-read="false">
                <div class="notification-icon stock-icon">
                    <i class="fas fa-box"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-header">
                        <h4>Stock critique - Aliment bovin</h4>
                        <span class="notification-time">Il y a 5 minutes</span>
                    </div>
                    <p class="notification-message">
                        Le stock d'aliment "Premium Vache" a atteint un niveau critique. 
                        Quantité restante : 45 kg (seuil : 50 kg). Pensez à réapprovisionner.
                    </p>
                    <div class="notification-footer">
                        <button class="action-btn view-btn">
                            <i class="fas fa-eye"></i>
                            Voir le stock
                        </button>
                        <button class="action-btn remind-later">
                            <i class="fas fa-clock"></i>
                            Rappeler plus tard
                        </button>
                    </div>
                </div>
                <button class="mark-read-btn" title="Marquer comme lu">
                    <i class="fas fa-circle"></i>
                </button>
            </div>

            <!-- Notification 2 - Rappel vaccination -->
            <div class="notification-item unread" data-type="task" data-read="false">
                <div class="notification-icon task-icon">
                    <i class="fas fa-syringe"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-header">
                        <h4>Rappel de vaccination - Marguerite</h4>
                        <span class="notification-time">Il y a 1 heure</span>
                    </div>
                    <p class="notification-message">
                        L'animal "Marguerite" doit être vacciné aujourd'hui. 
                        Ne pas oublier de vérifier son carnet de santé.
                    </p>
                    <div class="notification-footer">
                        <button class="action-btn view-btn">
                            <i class="fas fa-eye"></i>
                            Voir l'animal
                        </button>
                        <button class="action-btn complete-btn success">
                            <i class="fas fa-check"></i>
                            Marquer comme fait
                        </button>
                    </div>
                </div>
                <button class="mark-read-btn">
                    <i class="fas fa-circle"></i>
                </button>
            </div>

            <!-- Notification 3 - Nouveau message -->
            <div class="notification-item unread" data-type="message" data-read="false">
                <div class="notification-icon message-icon">
                    <i class="fas fa-comment-dots"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-header">
                        <h4>Nouveau message - Amadou Sy</h4>
                        <span class="notification-time">Il y a 2 heures</span>
                    </div>
                    <p class="notification-message">
                        "Bonjour, j'ai vu votre publication sur l'alimentation des bovins. 
                        Pouvez-vous me donner plus de détails sur la méthode que vous utilisez ?"
                    </p>
                    <div class="notification-footer">
                        <button class="action-btn reply-btn">
                            <i class="fas fa-reply"></i>
                            Répondre
                        </button>
                        <button class="action-btn view-btn">
                            <i class="fas fa-eye"></i>
                            Voir la conversation
                        </button>
                    </div>
                </div>
                <button class="mark-read-btn">
                    <i class="fas fa-circle"></i>
                </button>
            </div>

            <!-- Notification 4 - Alerte perte de poids -->
            <div class="notification-item" data-type="alert" data-read="true">
                <div class="notification-icon alert-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-header">
                        <h4>Alerte santé - Perte de poids suspecte</h4>
                        <span class="notification-time">Hier à 14h30</span>
                    </div>
                    <p class="notification-message">
                        L'animal "Blanchette" a perdu 12% de son poids en 15 jours 
                        (450kg → 396kg). Une consultation vétérinaire est recommandée.
                    </p>
                    <div class="notification-footer">
                        <button class="action-btn view-btn">
                            <i class="fas fa-eye"></i>
                            Voir l'animal
                        </button>
                        <button class="action-btn vet-btn">
                            <i class="fas fa-stethoscope"></i>
                            Contacter vétérinaire
                        </button>
                    </div>
                </div>
                <button class="mark-read-btn" style="opacity: 0.4;">
                    <i class="fas fa-circle"></i>
                </button>
            </div>

            <!-- Notification 5 - Nouveau commentaire -->
            <div class="notification-item" data-type="comment" data-read="true">
                <div class="notification-icon comment-icon">
                    <i class="fas fa-comment"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-header">
                        <h4>Nouveau commentaire sur votre publication</h4>
                        <span class="notification-time">Hier à 10h15</span>
                    </div>
                    <p class="notification-message">
                        Marie Diop a commenté votre publication "5 astuces pour l'hivernage" : 
                        "Merci pour ces conseils très utiles ! Je vais les appliquer dès demain."
                    </p>
                    <div class="notification-footer">
                        <button class="action-btn view-btn">
                            <i class="fas fa-eye"></i>
                            Voir la publication
                        </button>
                        <button class="action-btn reply-btn">
                            <i class="fas fa-reply"></i>
                            Répondre
                        </button>
                    </div>
                </div>
                <button class="mark-read-btn" style="opacity: 0.4;">
                    <i class="fas fa-circle"></i>
                </button>
            </div>

            <!-- Notification 6 - Like sur publication -->
            <div class="notification-item" data-type="like" data-read="true">
                <div class="notification-icon like-icon">
                    <i class="fas fa-heart"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-header">
                        <h4>Nouveau like - 45 likes</h4>
                        <span class="notification-time">Hier à 09h00</span>
                    </div>
                    <p class="notification-message">
                        Votre publication "Comment j'ai augmenté ma production laitière" 
                        a reçu 45 likes. Continuez à partager votre expérience !
                    </p>
                    <div class="notification-footer">
                        <button class="action-btn view-btn">
                            <i class="fas fa-eye"></i>
                            Voir la publication
                        </button>
                    </div>
                </div>
                <button class="mark-read-btn" style="opacity: 0.4;">
                    <i class="fas fa-circle"></i>
                </button>
            </div>

            <!-- Notification 7 - Nouveau partage -->
            <div class="notification-item" data-type="share" data-read="true">
                <div class="notification-icon share-icon">
                    <i class="fas fa-share-alt"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-header">
                        <h4>Nouveau partage - Facebook</h4>
                        <span class="notification-time">Hier à 08h30</span>
                    </div>
                    <p class="notification-message">
                        Ibrahima Fall a partagé votre publication "Alerte fièvre aphteuse" 
                        sur Facebook.
                    </p>
                    <div class="notification-footer">
                        <button class="action-btn view-btn">
                            <i class="fas fa-eye"></i>
                            Voir la publication
                        </button>
                    </div>
                </div>
                <button class="mark-read-btn" style="opacity: 0.4;">
                    <i class="fas fa-circle"></i>
                </button>
            </div>

            <!-- Notification 8 - Signalement publication (Admin visible) -->
            <div class="notification-item" data-type="report" data-read="false">
                <div class="notification-icon report-icon">
                    <i class="fas fa-flag"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-header">
                        <h4>Publication signalée</h4>
                        <span class="notification-time">Aujourd'hui à 07h45</span>
                    </div>
                    <p class="notification-message">
                        La publication "Publicité non autorisée" a été signalée par 3 utilisateurs 
                        pour motif "Spam". Veuillez modérer ce contenu.
                    </p>
                    <div class="notification-footer">
                        <button class="action-btn view-btn">
                            <i class="fas fa-eye"></i>
                            Voir la publication
                        </button>
                        <button class="action-btn moderate-btn warning">
                            <i class="fas fa-gavel"></i>
                            Modérer
                        </button>
                        <button class="action-btn ignore-btn">
                            <i class="fas fa-times"></i>
                            Ignorer
                        </button>
                    </div>
                </div>
                <button class="mark-read-btn">
                    <i class="fas fa-circle"></i>
                </button>
            </div>

            <!-- Notification 9 - Stock réapprovisionné -->
            <div class="notification-item" data-type="stock" data-read="true">
                <div class="notification-icon success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-header">
                        <h4>Réapprovisionnement effectué</h4>
                        <span class="notification-time">Hier à 17h00</span>
                    </div>
                    <p class="notification-message">
                        Le produit "Aliment Vache Premium" a été réapprovisionné. 
                        Nouvelle quantité : 500 kg.
                    </p>
                    <div class="notification-footer">
                        <button class="action-btn view-btn">
                            <i class="fas fa-eye"></i>
                            Voir le stock
                        </button>
                    </div>
                </div>
                <button class="mark-read-btn" style="opacity: 0.4;">
                    <i class="fas fa-circle"></i>
                </button>
            </div>

            <!-- Notification 10 - Bienvenue -->
            <div class="notification-item" data-type="welcome" data-read="true">
                <div class="notification-icon welcome-icon">
                    <i class="fas fa-hand-peace"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-header">
                        <h4>Bienvenue sur Élevage+ !</h4>
                        <span class="notification-time">15 mai 2026</span>
                    </div>
                    <p class="notification-message">
                        Merci d'avoir rejoint notre communauté ! Commencez dès maintenant 
                        à gérer votre élevage, partagez vos expériences et échangez avec 
                        d'autres éleveurs.
                    </p>
                    <div class="notification-footer">
                        <button class="action-btn get-started-btn">
                            <i class="fas fa-rocket"></i>
                            Commencer
                        </button>
                    </div>
                </div>
                <button class="mark-read-btn" style="opacity: 0.4;">
                    <i class="fas fa-circle"></i>
                </button>
            </div>

            <!-- Notification 11 - Rappel tâche alimentation -->
            <div class="notification-item" data-type="task" data-read="true">
                <div class="notification-icon task-icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-header">
                        <h4>Rappel alimentation - Matin</h4>
                        <span class="notification-time">Hier à 06h00</span>
                    </div>
                    <p class="notification-message">
                        N'oubliez pas de nourrir les animaux ce matin. 
                        Alimentation programmée pour 08h00.
                    </p>
                    <div class="notification-footer">
                        <button class="action-btn complete-btn success">
                            <i class="fas fa-check"></i>
                            Effectué
                        </button>
                        <button class="action-btn remind-later">
                            <i class="fas fa-clock"></i>
                            Rappeler plus tard
                        </button>
                    </div>
                </div>
                <button class="mark-read-btn" style="opacity: 0.4;">
                    <i class="fas fa-circle"></i>
                </button>
            </div>

            <!-- Notification 12 - Nouvel abonné (pour admin) -->
            <div class="notification-item" data-type="user" data-read="true">
                <div class="notification-icon user-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-header">
                        <h4>Nouvel utilisateur inscrit</h4>
                        <span class="notification-time">Le 14 mai 2026</span>
                    </div>
                    <p class="notification-message">
                        Un nouvel éleveur "Mamadou Diallo" a rejoint la plateforme. 
                        Bienvenue à lui dans la communauté !
                    </p>
                    <div class="notification-footer">
                        <button class="action-btn view-btn">
                            <i class="fas fa-eye"></i>
                            Voir le profil
                        </button>
                    </div>
                </div>
                <button class="mark-read-btn" style="opacity: 0.4;">
                    <i class="fas fa-circle"></i>
                </button>
            </div>

        </div>

        <!-- Message "Aucune notification" -->
        <div class="empty-notifications" id="emptyNotifications" style="display: none;">
            <div class="empty-icon">
                <i class="fas fa-bell-slash"></i>
            </div>
            <h3>Aucune notification</h3>
            <p>Vous n'avez aucune notification pour le moment.</p>
        </div>

        <!-- Pagination -->
        <div class="notifications-pagination">
            <button class="page-btn prev" disabled>
                <i class="fas fa-chevron-left"></i>
                Précédent
            </button>
            <div class="page-numbers">
                <button class="page-number active">1</button>
                <button class="page-number">2</button>
                <button class="page-number">3</button>
            </div>
            <button class="page-btn next">
                Suivant
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Éléments DOM
    const notificationsList = document.getElementById('notificationsList');
    const notificationItems = document.querySelectorAll('.notification-item');
    const filterBtns = document.querySelectorAll('.filter-btn');
    const markAllBtn = document.getElementById('markAllReadBtn');
    const emptyDiv = document.getElementById('emptyNotifications');
    
    // Compteurs
    const allCountSpan = document.getElementById('allCount');
    const unreadCountSpan = document.getElementById('unreadCount');
    const readCountSpan = document.getElementById('readCount');
    const totalNotifBadge = document.getElementById('totalNotifCount');

    // Fonction pour mettre à jour les compteurs
    function updateCounters() {
        const total = notificationItems.length;
        const unread = document.querySelectorAll('.notification-item.unread').length;
        const read = total - unread;
        
        if (allCountSpan) allCountSpan.textContent = total;
        if (unreadCountSpan) unreadCountSpan.textContent = unread;
        if (readCountSpan) readCountSpan.textContent = read;
        if (totalNotifBadge) totalNotifBadge.textContent = total;
    }

    // Fonction pour filtrer les notifications
    function filterNotifications(filter) {
        let visibleCount = 0;
        
        notificationItems.forEach(item => {
            const isUnread = item.classList.contains('unread');
            const type = item.dataset.type;
            
            let show = false;
            
            switch(filter) {
                case 'all':
                    show = true;
                    break;
                case 'unread':
                    show = isUnread;
                    break;
                case 'read':
                    show = !isUnread;
                    break;
                case 'alert':
                    show = type === 'alert';
                    break;
                case 'message':
                    show = type === 'message';
                    break;
                case 'task':
                    show = type === 'task';
                    break;
                case 'stock':
                    show = type === 'stock';
                    break;
                default:
                    show = true;
            }
            
            if (show) {
                item.style.display = 'flex';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });
        
        // Afficher le message "aucune notification" si nécessaire
        if (visibleCount === 0) {
            emptyDiv.style.display = 'flex';
            notificationsList.style.display = 'none';
        } else {
            emptyDiv.style.display = 'none';
            notificationsList.style.display = 'block';
        }
        
        // Mettre à jour l'état actif des filtres
        filterBtns.forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.filter === filter) {
                btn.classList.add('active');
            }
        });
        
        updateCounters();
    }

    // Fonction pour marquer une notification comme lue
    function markAsRead(notification) {
        if (notification.classList.contains('unread')) {
            notification.classList.remove('unread');
            const markBtn = notification.querySelector('.mark-read-btn');
            if (markBtn) markBtn.style.opacity = '0.4';
            updateCounters();
        }
    }

    // Fonction pour marquer toutes les notifications comme lues
    function markAllAsRead() {
        notificationItems.forEach(item => {
            if (item.classList.contains('unread')) {
                item.classList.remove('unread');
                const markBtn = item.querySelector('.mark-read-btn');
                if (markBtn) markBtn.style.opacity = '0.4';
            }
        });
        updateCounters();
        
        // Afficher un message de confirmation
        showToast('Toutes les notifications ont été marquées comme lues', 'success');
    }

    // Fonction pour afficher un toast de notification
    function showToast(message, type = 'info') {
        // Vérifier si un toast existe déjà
        let toast = document.querySelector('.custom-toast');
        if (toast) {
            toast.remove();
        }
        
        // Créer le toast
        toast = document.createElement('div');
        toast.className = `custom-toast ${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-info-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(toast);
        
        // Afficher le toast
        setTimeout(() => toast.classList.add('show'), 10);
        
        // Masquer et supprimer après 3 secondes
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Événements pour les boutons "Marquer comme lu"
    document.querySelectorAll('.mark-read-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const notification = this.closest('.notification-item');
            markAsRead(notification);
            showToast('Notification marquée comme lue', 'success');
        });
    });

    // Événement pour "Tout marquer comme lu"
    if (markAllBtn) {
        markAllBtn.addEventListener('click', () => markAllAsRead());
    }

    // Événements pour les filtres
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.dataset.filter;
            filterNotifications(filter);
        });
    });

    // Événements pour les actions dans les notifications
    document.querySelectorAll('.action-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const action = this.classList;
            const notification = this.closest('.notification-item');
            
            if (action.contains('complete-btn') || action.contains('success')) {
                showToast('Tâche marquée comme terminée', 'success');
                // Optionnel : masquer la notification
                // notification.style.display = 'none';
            } else if (action.contains('view-btn')) {
                showToast('Redirection en cours...', 'info');
            } else if (action.contains('reply-btn')) {
                showToast('Ouverture de la messagerie...', 'info');
            } else if (action.contains('remind-later')) {
                showToast('Rappel programmé pour plus tard', 'info');
            } else if (action.contains('moderate-btn')) {
                showToast('Ouverture de la modération...', 'info');
            } else if (action.contains('ignore-btn')) {
                notification.style.display = 'none';
                showToast('Signalement ignoré', 'info');
                updateCounters();
            } else if (action.contains('get-started-btn')) {
                window.location.href = '/dashboard';
            } else if (action.contains('vet-btn')) {
                showToast('Recherche de vétérinaires à proximité...', 'info');
            }
        });
    });

    // Cliquer sur une notification pour voir les détails
    notificationItems.forEach(item => {
        item.addEventListener('click', function(e) {
            // Ne pas déclencher si on clique sur un bouton
            if (e.target.tagName === 'BUTTON' || e.target.closest('button')) {
                return;
            }
            
            const title = this.querySelector('h4')?.textContent || 'Notification';
            showToast(`Ouverture: ${title}`, 'info');
            // Marquer comme lue au clic
            markAsRead(this);
        });
    });

    // Pagination (statique pour le moment)
    const pageNumbers = document.querySelectorAll('.page-number');
    const prevBtn = document.querySelector('.page-btn.prev');
    const nextBtn = document.querySelector('.page-btn.next');
    let currentPage = 1;
    const totalPages = 3;

    function updatePagination() {
        pageNumbers.forEach((btn, index) => {
            const pageNum = index + 1;
            if (pageNum === currentPage) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
        
        if (prevBtn) {
            prevBtn.disabled = currentPage === 1;
        }
        if (nextBtn) {
            nextBtn.disabled = currentPage === totalPages;
        }
    }

    pageNumbers.forEach(btn => {
        btn.addEventListener('click', function() {
            currentPage = parseInt(this.textContent);
            updatePagination();
            showToast(`Page ${currentPage} chargée`, 'info');
            // Ici, vous pourriez charger les notifications de la page via AJAX
        });
    });

    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            if (currentPage > 1) {
                currentPage--;
                updatePagination();
                showToast(`Page ${currentPage} chargée`, 'info');
            }
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            if (currentPage < totalPages) {
                currentPage++;
                updatePagination();
                showToast(`Page ${currentPage} chargée`, 'info');
            }
        });
    }

    // Bouton paramètres des notifications
    const settingsBtn = document.getElementById('notificationSettingsBtn');
    if (settingsBtn) {
        settingsBtn.addEventListener('click', () => {
            showToast('Paramètres des notifications (à venir)', 'info');
        });
    }

    // Initialiser les compteurs
    updateCounters();
    
    // Animation d'entrée pour les notifications
    notificationItems.forEach((item, index) => {
        item.style.animationDelay = `${index * 0.05}s`;
    });
});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\USER\Desktop\Projet\Elevage-plus\resources\views/notification.blade.php ENDPATH**/ ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo $__env->yieldContent('title', 'Élevage+'); ?></title>

    <!-- Bootstrap -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <!-- FontAwesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
          rel="stylesheet">

    <!-- CSS GLOBAL -->
    <link rel="stylesheet" href="<?php echo e(asset('css/layoutCSS/navbar.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/layoutCSS/sidebar.css')); ?>">

    <!-- CSS PAGE -->
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body>

    <!-- NAVBAR -->
    <?php echo $__env->make('layouts.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <div class="dashboard-layout" id="dashboardLayout">

        <!-- SIDEBAR -->
        <?php echo $__env->make('layouts.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <!-- CONTENU PRINCIPAL -->
        <main class="dashboard-content" id="dashboardContent">
            <?php echo $__env->yieldContent('content'); ?>
        </main>

    </div>

    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Ajuster la marge du contenu principal en fonction de la sidebar
            function adjustContentMargin() {
                var sidebar = $('#sidebar');
                var content = $('#dashboardContent');
                var windowWidth = $(window).width();
                
                if (windowWidth > 1024) {
                    // Grand écran: sidebar visible, contenu décalé
                    content.css('margin-left', '260px');
                } else if (windowWidth >= 769 && windowWidth <= 1024) {
                    // Écran moyen: sidebar compacte
                    content.css('margin-left', '80px');
                } else {
                    // Mobile: sidebar cachée par défaut
                    if (sidebar.hasClass('open')) {
                        content.css('margin-left', '0px');
                    } else {
                        content.css('margin-left', '0px');
                    }
                }
            }
            
            // Appeler au chargement
            adjustContentMargin();
            
            // Réajuster lors du redimensionnement
            $(window).on('resize', function() {
                adjustContentMargin();
            });
            
            // Réajuster quand la sidebar s'ouvre/ferme sur mobile
            $(document).on('click', '#menuToggle', function() {
                setTimeout(adjustContentMargin, 50);
            });
            
            $(document).on('click', '#closeSidebar', function() {
                setTimeout(adjustContentMargin, 50);
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
        // --- MODALE AJOUTER ---
        const addModal = document.getElementById('addAnimalModal');
        const editModal = document.getElementById('editAnimalModal');
        const btnAdd = document.querySelector('.btn-add-animal');
        const closeBtns = document.querySelectorAll('.modal-close');
        const cancelBtns = document.querySelectorAll('.btn-cancel');

        // Ouvrir modale Ajouter
        if(btnAdd) {
            btnAdd.addEventListener('click', function() {
                addModal.style.display = 'block';
                document.body.style.overflow = 'hidden';
            });
        }

        // Fermer les modales
        function closeModals() {
            addModal.style.display = 'none';
            editModal.style.display = 'none';
            document.body.style.overflow = '';
        }

        closeBtns.forEach(btn => btn.addEventListener('click', closeModals));
        cancelBtns.forEach(btn => btn.addEventListener('click', closeModals));

        window.addEventListener('click', function(e) {
            if(e.target === addModal) closeModals();
            if(e.target === editModal) closeModals();
        });

        // --- Gestion image Ajouter ---
        const chooseImgBtn = document.getElementById('chooseImageBtn');
        const deleteImgBtn = document.getElementById('deleteImageBtn');
        const imageInput = document.getElementById('animalImageInput');
        const photoPreview = document.getElementById('photoPreview');

        chooseImgBtn.addEventListener('click', () => imageInput.click());
        
        imageInput.addEventListener('change', function(e) {
            if(e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    photoPreview.innerHTML = `<img src="${ev.target.result}" style="width:100%; height:100%; object-fit:cover;">`;
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        });
        
        deleteImgBtn.addEventListener('click', function() {
            imageInput.value = '';
            photoPreview.innerHTML = `<i class="fas fa-camera-retro"></i><span>Aperçu photo</span>`;
        });

        // --- Gestion image Modifier ---
        const editChooseBtn = document.getElementById('editChooseImageBtn');
        const editDeleteBtn = document.getElementById('editDeleteImageBtn');
        const editImageInput = document.getElementById('editAnimalImageInput');
        const editPreview = document.getElementById('editPhotoPreview');
        const editPreviewImg = document.getElementById('editPreviewImage');
        const editPreviewPlaceholder = document.getElementById('editPreviewPlaceholder');

        editChooseBtn.addEventListener('click', () => editImageInput.click());
        
        editImageInput.addEventListener('change', function(e) {
            if(e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    editPreviewImg.src = ev.target.result;
                    editPreviewImg.style.display = 'block';
                    editPreviewPlaceholder.style.display = 'none';
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        });
        
        editDeleteBtn.addEventListener('click', function() {
            editImageInput.value = '';
            editPreviewImg.style.display = 'none';
            editPreviewPlaceholder.style.display = 'flex';
            editPreviewImg.src = '';
        });

        // --- Simulation : ouvrir modale Modifier avec les données de l'animal ---
        const editBtns = document.querySelectorAll('.btn-edit');
        
        editBtns.forEach((btn, index) => {
            btn.addEventListener('click', function() {
                // Récupérer les infos de l'animal concerné (exemple: depuis la carte)
                const card = this.closest('.animal-card');
                const nomElement = card.querySelector('.animal-info h5');
                const details = card.querySelectorAll('.animal-details div');
                
                let nom = nomElement ? nomElement.innerText.replace('NOM : ', '') : '';
                let race = '';
                let poids = '';
                
                details.forEach(detail => {
                    const text = detail.innerText;
                    if(text.includes('Race :')) race = text.replace('Race : ', '');
                    if(text.includes('Poids :')) poids = text.replace('Poids : ', '').replace(' kg', '');
                });
                
                // Remplir le formulaire de modification
                document.getElementById('editNom').value = nom;
                document.getElementById('editRace').value = race;
                document.getElementById('editPoids').value = poids;
                document.getElementById('editNote').value = '';
                
                // Réinitialiser l'aperçu photo
                editDeleteBtn.click();
                
                // Ouvrir la modale modification
                editModal.style.display = 'block';
                document.body.style.overflow = 'hidden';
            });
        });

        // --- Soumission formulaire Ajouter ---
        const addForm = document.getElementById('addAnimalForm');
        addForm.addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Animal ajouté avec succès !');
            closeModals();
            addForm.reset();
            deleteImgBtn.click(); // Reset photo
        });

        // --- Soumission formulaire Modifier ---
        const editForm = document.getElementById('editAnimalForm');
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Animal modifié avec succès !');
            closeModals();
        });
    });

     // Initialisation du graphique
     let weightChart = null;
    
    // Données de poids pour le graphique (exemple)
    const weightData = [120, 180, 250, 310, 370, 420, 450];
    const weightMonths = ['0', '6', '12', '18', '24', '30', '36'];
    
    function initWeightChart() {
        const ctx = document.getElementById('weightChart').getContext('2d');
        
        // Détruire le graphique existant s'il y en a un
        if (weightChart) {
            weightChart.destroy();
        }
        
        weightChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: weightMonths,
                datasets: [{
                    label: 'Poids (kg)',
                    data: weightData,
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: '#198754',
                    pointBorderColor: '#fff',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { size: 11 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw + ' kg';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Poids (kg)',
                            font: { size: 11 }
                        },
                        grid: {
                            color: '#e9ecef'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Âge (mois)',
                            font: { size: 11 }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
    
    // Fonction pour ouvrir la modale détail avec les données de l'animal
    function openDetailModal(animalData) {
        // Mettre à jour le titre et les informations
        document.getElementById('detailAnimalNom').textContent = animalData.nom.toUpperCase();
        document.getElementById('detailNom').textContent = animalData.nom;
        document.getElementById('detailEspece').textContent = animalData.espece || 'Bovin';
        document.getElementById('detailRace').textContent = animalData.race;
        document.getElementById('detailDateNaissance').innerHTML = animalData.dateNaissance + ' <span style="color:#888;">(âge : ' + animalData.age + ')</span>';
        document.getElementById('detailPoids').textContent = animalData.poids + ' kg';
        document.getElementById('detailStatutSanitaire').textContent = animalData.sante;
        document.getElementById('detailElevage').textContent = animalData.elevage;
        
        // Mettre à jour l'image
        if (animalData.image) {
            document.getElementById('detailAnimalImage').src = animalData.image;
        }
        
        // Mettre à jour l'historique des tâches
        const tasksContainer = document.getElementById('detailHistoriqueTaches');
        tasksContainer.innerHTML = '';
        
        if (animalData.historiqueTaches && animalData.historiqueTaches.length) {
            animalData.historiqueTaches.forEach(task => {
                const taskDiv = document.createElement('div');
                taskDiv.className = 'task-item';
                taskDiv.innerHTML = `
                    <span class="task-type">${task.type}</span>
                    <span class="task-date">${task.date}</span>
                `;
                tasksContainer.appendChild(taskDiv);
            });
        } else {
            // Données par défaut
            const defaultTasks = [
                { type: 'Vaccination', date: '10/03/2022' },
                { type: 'Poids', date: '430 kg' },
                { type: 'Vermifuge', date: '15/01/2022' },
                { type: 'Prochaine vaccination', date: '15/06/2022' }
            ];
            defaultTasks.forEach(task => {
                const taskDiv = document.createElement('div');
                taskDiv.className = 'task-item';
                taskDiv.innerHTML = `
                    <span class="task-type">${task.type}</span>
                    <span class="task-date">${task.date}</span>
                `;
                tasksContainer.appendChild(taskDiv);
            });
        }
        
        // Mettre à jour les labels de poids
        if (animalData.weightLabels) {
            const labelsContainer = document.getElementById('detailWeightLabels');
            labelsContainer.innerHTML = '';
            animalData.weightLabels.forEach(label => {
                const span = document.createElement('span');
                span.textContent = label;
                labelsContainer.appendChild(span);
            });
        }
        
        // Mettre à jour le graphique avec les données de poids
        if (animalData.weightData && animalData.weightMonths) {
            updateChartData(animalData.weightData, animalData.weightMonths);
        } else {
            // Données par défaut
            updateChartData([120, 180, 250, 310, 370, 420, 450], ['0', '6', '12', '18', '24', '30', '36']);
        }
        
        // Afficher la modale
        const modal = document.getElementById('detailAnimalModal');
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
    
    function updateChartData(data, labels) {
        if (weightChart) {
            weightChart.data.datasets[0].data = data;
            weightChart.data.labels = labels;
            weightChart.update();
        } else {
            initWeightChart();
        }
    }
    
    // Écouteurs pour les boutons Détail
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser le graphique avec des données par défaut
        initWeightChart();
        
        // Gestion de la modale détail
        const detailModal = document.getElementById('detailAnimalModal');
        const closeBtns = document.querySelectorAll('.modal-close');
        
        // Récupérer toutes les cartes d'animaux
        const animalCards = document.querySelectorAll('.animal-card');
        
        animalCards.forEach((card, index) => {
            const detailBtn = card.querySelector('.btn-detail');
            
            detailBtn.addEventListener('click', function() {
                // Extraire les données de l'animal depuis la carte
                const nomElement = card.querySelector('.animal-info h5');
                const details = card.querySelectorAll('.animal-details div');
                const imageElement = card.querySelector('.animal-image img');
                
                let nom = nomElement ? nomElement.innerText.replace('NOM : ', '') : 'Marguerite';
                let race = '';
                let age = '';
                let poids = '';
                let sante = '';
                
                details.forEach(detail => {
                    const text = detail.innerText;
                    if (text.includes('Race :')) race = text.replace('Race : ', '');
                    if (text.includes('Âge :')) age = text.replace('Âge : ', '');
                    if (text.includes('Poids :')) poids = text.replace('Poids : ', '').replace(' kg', '');
                    if (text.includes('Santé :')) sante = detail.querySelector('.badge-sante')?.innerText || 'Bonne';
                });
                
                // Calculer une date de naissance approximative à partir de l'âge
                let dateNaissance = '';
                let ageInYears = 0;
                if (age.includes('ans')) {
                    ageInYears = parseInt(age);
                } else if (age.includes('mois')) {
                    ageInYears = parseInt(age) / 12;
                }
                
                const today = new Date();
                const birthYear = today.getFullYear() - ageInYears;
                dateNaissance = `15/03/${birthYear}`;
                
                // Construire l'objet animal
                const animalData = {
                    nom: nom,
                    espece: 'Bovin',
                    race: race,
                    dateNaissance: dateNaissance,
                    age: age,
                    poids: poids,
                    sante: sante,
                    elevage: 'Drenage bovin - Thèse',
                    image: imageElement ? imageElement.src : 'https://images.unsplash.com/photo-1516467508483-a7212febe31a?w=400',
                    historiqueTaches: [
                        { type: 'Vaccination', date: '10/03/2022' },
                        { type: 'Poids', date: poids + ' kg' },
                        { type: 'Vermifuge', date: '15/01/2022' },
                        { type: 'Prochaine vaccination', date: '15/06/2022' }
                    ],
                    weightData: [120, 180, 250, 310, 370, parseInt(poids), parseInt(poids)],
                    weightMonths: ['0', '6', '12', '18', '24', '30', '36']
                };
                
                openDetailModal(animalData);
            });
        });
        
        // Fermeture de la modale
        closeBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                detailModal.style.display = 'none';
                document.body.style.overflow = '';
            });
        });
        
        // Fermer en cliquant à l'extérieur
        window.addEventListener('click', function(e) {
            if (e.target === detailModal) {
                detailModal.style.display = 'none';
                document.body.style.overflow = '';
            }
        });
    });

    // Gestion de l'upload et de l'aperçu
document.getElementById('photoInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            const preview = document.getElementById('imagePreview');
            const placeholder = document.querySelector('.image-preview-placeholder');
            
            preview.src = event.target.result;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
});

// Gestion de la suppression
document.getElementById('deletePhotoBtn').addEventListener('click', function() {
    const fileInput = document.getElementById('photoInput');
    const preview = document.getElementById('imagePreview');
    const placeholder = document.querySelector('.image-preview-placeholder');
    
    // Reset du formulaire
    fileInput.value = '';
    
    // Reset de l'aperçu
    preview.src = '';
    preview.style.display = 'none';
    placeholder.style.display = 'flex';
    
    // Optionnel : Message de confirmation
    showTemporaryMessage('Photo supprimée avec succès');
});

// Fonction utilitaire pour afficher un message temporaire
function showTemporaryMessage(message) {
    const infoDiv = document.querySelector('.photo-info');
    const originalContent = infoDiv.innerHTML;
    
    infoDiv.innerHTML = `
        <small class="text-success">
            <i class="fas fa-check-circle mr-1"></i> 
            ${message}
        </small>
    `;
    
    setTimeout(() => {
        infoDiv.innerHTML = originalContent;
    }, 2000);
}
    </script>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <?php echo $__env->yieldPushContent('scripts'); ?>

</body>
</html><?php /**PATH C:\Projets\Elevage-plus\resources\views/layouts/menu.blade.php ENDPATH**/ ?>
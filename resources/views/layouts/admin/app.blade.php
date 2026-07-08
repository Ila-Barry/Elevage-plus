<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Élevage+')</title>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="{{ asset('css/admin/navbar.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin/sidebar.css') }}">

        @stack('styles')
    </head>

    <body>

        @include('layouts.admin.navbar')

        <div class="dashboard-layout" id="dashboardLayout">

            @include('layouts.admin.sidebar')

            <main class="dashboard-content" id="dashboardContent">
                @yield('content')
            </main>

        </div>

        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        
        <script>
            // Variable globale pour le graphique
            let weightChart = null;
            const weightData = [120, 180, 250, 310, 370, 420, 450];
            const weightMonths = ['0', '6', '12', '18', '24', '30', '36'];

            function initWeightChart() {
                const canvas = document.getElementById('weightChart');
                if (!canvas) return; // Évite les crashs si le canvas n'est pas sur la page actuelle

                const ctx = canvas.getContext('2d');
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
                                labels: { font: { size: 11 } }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) { return context.raw + ' kg'; }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: { display: true, text: 'Poids (kg)', font: { size: 11 } },
                                grid: { color: '#e9ecef' }
                            },
                            x: {
                                title: { display: true, text: 'Âge (mois)', font: { size: 11 } },
                                grid: { display: false }
                            }
                        }
                    }
                });
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
            
            function openDetailModal(animalData) {
                document.getElementById('detailAnimalNom').textContent = animalData.nom.toUpperCase();
                document.getElementById('detailNom').textContent = animalData.nom;
                document.getElementById('detailEspece').textContent = animalData.espece || 'Bovin';
                document.getElementById('detailRace').textContent = animalData.race;
                document.getElementById('detailDateNaissance').innerHTML = animalData.dateNaissance + ' <span style="color:#888;">(âge : ' + animalData.age + ')</span>';
                document.getElementById('detailPoids').textContent = animalData.poids + ' kg';
                document.getElementById('detailStatutSanitaire').textContent = animalData.sante;
                document.getElementById('detailElevage').textContent = animalData.elevage;
                
                if (animalData.image) {
                    document.getElementById('detailAnimalImage').src = animalData.image;
                }
                
                const tasksContainer = document.getElementById('detailHistoriqueTaches');
                if (tasksContainer) {
                    tasksContainer.innerHTML = '';
                    const tasks = (animalData.historiqueTaches && animalData.historiqueTaches.length) ? animalData.historiqueTaches : [
                        { type: 'Vaccination', date: '10/03/2022' },
                        { type: 'Poids', date: animalData.poids + ' kg' },
                        { type: 'Vermifuge', date: '15/01/2022' },
                        { type: 'Prochaine vaccination', date: '15/06/2022' }
                    ];
                    
                    tasks.forEach(task => {
                        const taskDiv = document.createElement('div');
                        taskDiv.className = 'task-item';
                        taskDiv.innerHTML = `<span class="task-type">${task.type}</span><span class="task-date">${task.date}</span>`;
                        tasksContainer.appendChild(taskDiv);
                    });
                }
                
                if (animalData.weightData && animalData.weightMonths) {
                    updateChartData(animalData.weightData, animalData.weightMonths);
                }
                
                const modal = document.getElementById('detailAnimalModal');
                if (modal) {
                    modal.style.display = 'block';
                    document.body.style.overflow = 'hidden';
                }
            }

            function showTemporaryMessage(message) {
                const infoDiv = document.querySelector('.photo-info');
                if (!infoDiv) return;
                const originalContent = infoDiv.innerHTML;
                infoDiv.innerHTML = `<small class="text-success"><i class="fas fa-check-circle mr-1"></i> ${message}</small>`;
                setTimeout(() => { infoDiv.innerHTML = originalContent; }, 2000);
            }

            $(document).ready(function() {
                // === AJUSTEMENT MAQUETTE RESPONSIVE ===
                function adjustContentMargin() {
                    var sidebar = $('#sidebar');
                    var content = $('#dashboardContent');
                    var windowWidth = $(window).width();
                    
                    if (content.length) {
                        if (windowWidth > 1024) {
                            content.css('margin-left', '260px');
                        } else if (windowWidth >= 769 && windowWidth <= 1024) {
                            content.css('margin-left', '80px');
                        } else {
                            content.css('margin-left', '0px');
                        }
                    }
                }
                
                adjustContentMargin();
                $(window).on('resize', adjustContentMargin);
                $(document).on('click', '#menuToggle, #closeSidebar', function() {
                    setTimeout(adjustContentMargin, 50);
                });

                // === EXÉCUTION DU GRAPHIQUE ===
                initWeightChart();

                // === GESTION DES MODALES PRINCIPALES ===
                const addModal = document.getElementById('addAnimalModal');
                const editModal = document.getElementById('editAnimalModal');
                const detailModal = document.getElementById('detailAnimalModal');

                $(document).on('click', '.btn-add-animal', function() {
                    if(addModal) { addModal.style.display = 'block'; document.body.style.overflow = 'hidden'; }
                });

                $(document).on('click', '.modal-close, .btn-cancel', function() {
                    if(addModal) addModal.style.display = 'none';
                    if(editModal) editModal.style.display = 'none';
                    if(detailModal) detailModal.style.display = 'none';
                    document.body.style.overflow = '';
                });

                $(window).on('click', function(e) {
                    if(e.target === addModal || e.target === editModal || e.target === detailModal) {
                        if(addModal) addModal.style.display = 'none';
                        if(editModal) editModal.style.display = 'none';
                        if(detailModal) detailModal.style.display = 'none';
                        document.body.style.overflow = '';
                    }
                });

                // --- UPLOAD IMAGE AJOUTER ---
                const imageInput = document.getElementById('animalImageInput');
                $(document).on('click', '#chooseImageBtn', function() { if(imageInput) imageInput.click(); });
                
                if(imageInput) {
                    imageInput.addEventListener('change', function(e) {
                        const photoPreview = document.getElementById('photoPreview');
                        if(e.target.files && e.target.files[0] && photoPreview) {
                            const reader = new FileReader();
                            reader.onload = function(ev) {
                                photoPreview.innerHTML = `<img src="${ev.target.result}" style="width:100%; height:100%; object-fit:cover;">`;
                            };
                            reader.readAsDataURL(e.target.files[0]);
                        }
                    });
                }
                
                $(document).on('click', '#deleteImageBtn', function() {
                    if(imageInput) imageInput.value = '';
                    const photoPreview = document.getElementById('photoPreview');
                    if(photoPreview) photoPreview.innerHTML = `<i class="fas fa-camera-retro"></i><span>Aperçu photo</span>`;
                });

                // --- UPLOAD IMAGE MODIFIER ---
                const editImageInput = document.getElementById('editAnimalImageInput');
                $(document).on('click', '#editChooseImageBtn', function() { if(editImageInput) editImageInput.click(); });
                
                if(editImageInput) {
                    editImageInput.addEventListener('change', function(e) {
                        const editPreviewImg = document.getElementById('editPreviewImage');
                        const editPreviewPlaceholder = document.getElementById('editPreviewPlaceholder');
                        if(e.target.files && e.target.files[0] && editPreviewImg) {
                            const reader = new FileReader();
                            reader.onload = function(ev) {
                                editPreviewImg.src = ev.target.result;
                                editPreviewImg.style.display = 'block';
                                if(editPreviewPlaceholder) editPreviewPlaceholder.style.display = 'none';
                            };
                            reader.readAsDataURL(e.target.files[0]);
                        }
                    });
                }
                
                $(document).on('click', '#editDeleteImageBtn', function() {
                    if(editImageInput) editImageInput.value = '';
                    const editPreviewImg = document.getElementById('editPreviewImage');
                    const editPreviewPlaceholder = document.getElementById('editPreviewPlaceholder');
                    if(editPreviewImg) { editPreviewImg.style.display = 'none'; editPreviewImg.src = ''; }
                    if(editPreviewPlaceholder) editPreviewPlaceholder.style.display = 'flex';
                });

                // --- BOUTON MODIFIER (REMPLISSAGE) ---
                $(document).on('click', '.btn-edit', function() {
                    const card = this.closest('.animal-card');
                    if (!card) return;
                    const nomElement = card.querySelector('.animal-info h5');
                    const details = card.querySelectorAll('.animal-details div');
                    
                    let nom = nomElement ? nomElement.innerText.replace('NOM : ', '') : '';
                    let race = '', poids = '';
                    
                    details.forEach(detail => {
                        const text = detail.innerText;
                        if(text.includes('Race :')) race = text.replace('Race : ', '');
                        if(text.includes('Poids :')) poids = text.replace('Poids : ', '').replace(' kg', '');
                    });
                    
                    if(document.getElementById('editNom')) document.getElementById('editNom').value = nom;
                    if(document.getElementById('editRace')) document.getElementById('editRace').value = race;
                    if(document.getElementById('editPoids')) document.getElementById('editPoids').value = poids;
                    
                    $('#editDeleteImageBtn').click();
                    if(editModal) { editModal.style.display = 'block'; document.body.style.overflow = 'hidden'; }
                });

                // --- BOUTON DÉTAIL ANIMAL ---
                $(document).on('click', '.animal-card .btn-detail', function() {
                    const card = this.closest('.animal-card');
                    if(!card) return;
                    const nomElement = card.querySelector('.animal-info h5');
                    const details = card.querySelectorAll('.animal-details div');
                    const imageElement = card.querySelector('.animal-image img');
                    
                    let nom = nomElement ? nomElement.innerText.replace('NOM : ', '') : 'Marguerite';
                    let race = '', age = '', poids = '', sante = 'Bonne';
                    
                    details.forEach(detail => {
                        const text = detail.innerText;
                        if (text.includes('Race :')) race = text.replace('Race : ', '');
                        if (text.includes('Âge :')) age = text.replace('Âge : ', '');
                        if (text.includes('Poids :')) poids = text.replace('Poids : ', '').replace(' kg', '');
                        if (text.includes('Santé :')) sante = detail.querySelector('.badge-sante')?.innerText || 'Bonne';
                    });
                    
                    let ageInYears = age.includes('ans') ? parseInt(age) : (age.includes('mois') ? parseInt(age) / 12 : 2);
                    const birthYear = new Date().getFullYear() - ageInYears;
                    
                    const animalData = {
                        nom: nom, race: race, age: age, poids: poids, sante: sante,
                        dateNaissance: `15/03/${birthYear}`,
                        elevage: 'Elevage Bovin Général',
                        image: imageElement ? imageElement.src : 'https://images.unsplash.com/photo-1516467508483-a7212febe31a?w=400',
                        weightData: [120, 180, 250, 310, 370, parseInt(poids) || 400, parseInt(poids) || 400],
                        weightMonths: ['0', '6', '12', '18', '24', '30', '36']
                    };
                    
                    openDetailModal(animalData);
                });

                // --- SUBMITS FORMULAIRES ---
                $('#addAnimalForm').on('submit', function(e) {
                    e.preventDefault(); alert('Animal ajouté avec succès !');
                    if(addModal) addModal.style.display = 'none'; document.body.style.overflow = '';
                    this.reset(); $('#deleteImageBtn').click();
                });

                $('#editAnimalForm').on('submit', function(e) {
                    e.preventDefault(); alert('Animal modifié avec succès !');
                    if(editModal) editModal.style.display = 'none'; document.body.style.overflow = '';
                });

                // --- COMPORTEMENT PHOTO INPUT ALTERNATIF ---
                const photoInput = document.getElementById('photoInput');
                if(photoInput) {
                    photoInput.addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        const preview = document.getElementById('imagePreview');
                        const placeholder = document.querySelector('.image-preview-placeholder');
                        if (file && preview) {
                            const reader = new FileReader();
                            reader.onload = function(event) {
                                preview.src = event.target.result; preview.style.display = 'block';
                                if(placeholder) placeholder.style.display = 'none';
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                }

                $(document).on('click', '#deletePhotoBtn', function() {
                    if(photoInput) photoInput.value = '';
                    const preview = document.getElementById('imagePreview');
                    const placeholder = document.querySelector('.image-preview-placeholder');
                    if(preview) { preview.src = ''; preview.style.display = 'none'; }
                    if(placeholder) placeholder.style.display = 'flex';
                    showTemporaryMessage('Photo supprimée avec succès');
                });
            });
        </script>

        @stack('scripts')

    </body>
</html>
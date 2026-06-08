
@extends('layouts.menu')

@section('title', 'Gestion des animaux')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/eleveurCSS/animaux.css') }}">
@endpush

@section('content')

<div class="animals-page">

    <!-- HEADER -->
    <div class="page-header">
        <div>
            <h2>GESTION DES ANIMAUX</h2>
        </div>

        <div class="header-actions">
            <button class="btn-add-animal">
                <i class="fas fa-plus"></i>
                Ajouter un animal
            </button>

            <div class="search-box">
                <input type="text" placeholder="rechercher...">
                <button>
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- FILTRES -->
    <div class="filters-section">

        <span class="filter-label">Filtres :</span>

        <select>
            <option>Tous</option>
            <option>Bovin</option>
            <option>Ovin</option>
            <option>Caprin</option>
        </select>

        <select>
            <option>Espèce</option>
        </select>

        <select>
            <option>Âge</option>
        </select>

        <select>
            <option>Santé</option>
        </select>

    </div>

    <!-- LISTE DES ANIMAUX -->
    <div class="animals-list">

        {{-- Animal 1 --}}
        <div class="animal-card">

            <div class="animal-image">
                <img src="https://images.unsplash.com/photo-1516467508483-a7212febe31a?w=600"
                     alt="Animal">
            </div>

            <div class="animal-info">

                <h5>NOM : Marguerite</h5>

                <div class="animal-details">

                    <div>
                        <i class="fas fa-tag"></i>
                        Race : Brune
                    </div>

                    <div>
                        <i class="fas fa-birthday-cake"></i>
                        Âge : 4 ans
                    </div>

                    <div>
                        <i class="fas fa-weight-hanging"></i>
                        Poids : 450 kg
                    </div>

                    <div>
                        <i class="fas fa-heartbeat"></i>
                        Santé :
                        <span class="badge-sante">Bonne</span>
                    </div>

                </div>

            </div>

            <div class="animal-actions">

                <button class="btn-detail">
                    <i class="fas fa-eye"></i>
                    Détail
                </button>

                <button class="btn-edit">
                    <i class="fas fa-pen"></i>
                    Modifier
                </button>

                <button class="btn-delete">
                    <i class="fas fa-trash"></i>
                    Supprimer
                </button>

            </div>

        </div>

        {{-- Animal 2 --}}
        <div class="animal-card">

            <div class="animal-image">
                <img src="https://images.unsplash.com/photo-1516467508483-a7212febe31a?w=600"
                     alt="Animal">
            </div>

            <div class="animal-info">

                <h5>NOM : Blanchette</h5>

                <div class="animal-details">

                    <div>
                        <i class="fas fa-tag"></i>
                        Race : Brune
                    </div>

                    <div>
                        <i class="fas fa-birthday-cake"></i>
                        Âge : 1 ans 6 mois
                    </div>

                    <div>
                        <i class="fas fa-weight-hanging"></i>
                        Poids : 250 kg
                    </div>

                    <div>
                        <i class="fas fa-heartbeat"></i>
                        Santé :
                        <span class="badge-sante">Bonne</span>
                    </div>

                </div>

            </div>

            <div class="animal-actions">

                <button class="btn-detail">
                    <i class="fas fa-eye"></i>
                    Détail
                </button>

                <button class="btn-edit">
                    <i class="fas fa-pen"></i>
                    Modifier
                </button>

                <button class="btn-delete">
                    <i class="fas fa-trash"></i>
                    Supprimer
                </button>

            </div>

        </div>

        {{-- Animal 3 --}}
        <div class="animal-card">

            <div class="animal-image">
                <img src="https://images.unsplash.com/photo-1516467508483-a7212febe31a?w=600"
                     alt="Animal">
            </div>

            <div class="animal-info">

                <h5>NOM : Roussette</h5>

                <div class="animal-details">

                    <div>
                        <i class="fas fa-tag"></i>
                        Race : Brune
                    </div>

                    <div>
                        <i class="fas fa-birthday-cake"></i>
                        Âge :  32 mois
                    </div>

                    <div>
                        <i class="fas fa-weight-hanging"></i>
                        Poids : 400 kg
                    </div>

                    <div>
                        <i class="fas fa-heartbeat"></i>
                        Santé :
                        <span class="badge-sante">Bonne</span>
                    </div>

                </div>

            </div>

            <div class="animal-actions">

                <button class="btn-detail">
                    <i class="fas fa-eye"></i>
                    Détail
                </button>

                <button class="btn-edit">
                    <i class="fas fa-pen"></i>
                    Modifier
                </button>

                <button class="btn-delete">
                    <i class="fas fa-trash"></i>
                    Supprimer
                </button>

            </div>

        </div>

    </div>

    <!-- PAGINATION -->
    <div class="pagination-section">

        <div class="pagination-custom">

            <button>
                <i class="fas fa-angle-left"></i>
            </button>
            <button class="active">1</button>
                            <button>2</button>
                            <button>3</button>
                            <button>2</button>
                            <button>3</button>
                            <button>4</button>
                            <button>5</button>
                            <button>6</button>
                            <button>...</button>


            <button>
                <i class="fas fa-angle-right"></i>
            </button>

        </div>

        <div class="pagination-info">
            Affichage : 1/45
        </div>

    <!-- MODALE DÉTAIL DE L'ANIMAL -->
    <div id="detailAnimalModal" class="modal">
        <div class="modal-content modal-detail">
            <div class="modal-header">
                <h2>
                    <i class="fas fa-info-circle" style="color: #198754; margin-right: 10px;"></i>
                    DÉTAIL DE L'ANIMAL : <span id="detailAnimalNom">MARGUERITE</span>
                </h2>
                <span class="modal-close">&times;</span>
            </div>
            <div class="modal-body">
                <div class="detail-container">
                    <!-- Colonne gauche -->
                    <div class="detail-left">
                        <!-- Photo de l'animal -->
                        <div class="detail-photo">
                            <img id="detailAnimalImage" src="https://images.unsplash.com/photo-1516467508483-a7212febe31a?w=400" alt="Animal">
                        </div>

                        <!-- Informations générales -->
                        <div class="info-section">
                            <h3>
                                <i class="fas fa-info-circle"></i>
                                INFORMATIONS GÉNÉRALES
                            </h3>
                            <div class="info-grid">
                                <div class="info-item">
                                    <span class="info-label">Nom :</span>
                                    <span class="info-value" id="detailNom">Marguerite</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Espèce :</span>
                                    <span class="info-value" id="detailEspece">Bovin</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Race :</span>
                                    <span class="info-value" id="detailRace">Brune</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Date naissance :</span>
                                    <span class="info-value" id="detailDateNaissance">15/03/2022 <span style="color:#888;">(âge : 4 ans)</span></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Poids :</span>
                                    <span class="info-value" id="detailPoids">450 kg</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Statut sanitaire :</span>
                                    <span class="info-value">
                                        <span class="badge-sante-detail" id="detailStatutSanitaire">Bonne</span>
                                    </span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Élevage :</span>
                                    <span class="info-value" id="detailElevage">Drenage bovin - Thèse</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Colonne droite -->
                    <div class="detail-right">
                        <!-- Historique des tâches -->
                        <div class="tasks-section">
                            <h3>
                                <i class="fas fa-history"></i>
                                HISTORIQUE DES TÂCHES
                            </h3>
                            <div class="tasks-list" id="detailHistoriqueTaches">
                                <div class="task-item">
                                    <span class="task-type">Vaccination</span>
                                    <span class="task-date">10/03/2022</span>
                                </div>
                                <div class="task-item">
                                    <span class="task-type">Poids</span>
                                    <span class="task-date">430 kg</span>
                                </div>
                                <div class="task-item">
                                    <span class="task-type">Vermifuge</span>
                                    <span class="task-date">15/01/2022</span>
                                </div>
                                <div class="task-item">
                                    <span class="task-type">Prochaine vaccination</span>
                                    <span class="task-date">15/06/2022</span>
                                </div>
                            </div>
                        </div>

                        <!-- Courbe de poids -->
                        <div class="weight-chart-section">
                            <h3>
                                <i class="fas fa-chart-line"></i>
                                COURBE DE POIDS
                            </h3>
                            <div class="chart-container">
                                <canvas id="weightChart" width="400" height="250" style="max-width:100%; height:auto;"></canvas>
                            </div>
                            <div class="weight-labels" id="detailWeightLabels">
                                <span>0</span><span>50</span><span>100</span><span>150</span><span>200</span>
                                <span>250</span><span>300</span><span>350</span><span>400</span><span>450</span><span>500</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Applications mobiles -->
                <div class="mobile-apps-section">
                    <div class="mobile-apps-content">
                        <div class="apps-icons">
                            <div class="app-icon">
                                <i class="fas fa-mobile-alt"></i>
                                <span>Début solitaire</span>
                            </div>
                            <div class="app-icon">
                                <i class="fas fa-chart-simple"></i>
                                <span>Perturbation</span>
                            </div>
                            <div class="app-icon">
                                <i class="fas fa-shield-alt"></i>
                                <span>Sécurité</span>
                            </div>
                            <div class="app-icon">
                                <i class="fas fa-microchip"></i>
                                <span>Technologie</span>
                            </div>
                        </div>
                        <div class="app-pagination">
                            <span>Appuyez sur 1 sur 10</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODALE AJOUTER UN ANIMAL -->
<div id="addAnimalModal" class="modal">
    <div class="modal-content">
        <div class="modal-header mt-3">
            <h2>
                <i class="fas fa-paw" style="color: #198754; margin-right: 10px;"></i>
                AJOUTER UN ANIMAL
            </h2>
            <span class="modal-close">&times;</span>
        </div>

        <div class="modal-body">
            <!-- Logo ÉLEVAGE+ -->
            <div class="modal-logo">
                <span class="logo-text">ÉLEVAGE<span style="color: #198754;">+</span></span>
            </div>

            <!-- Photo section -->

<div class="modal-body px-4 pb-4">
    <form action="#" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-section-box p-3 mb-3 rounded">
            <label class="form-label font-weight-bold mb-2">
                <i class="far fa-image text-success mr-1"></i> Photo <span class="font-weight-normal text-muted text-lowercase">(optionnelle)</span>
            </label> 
            
            <div class="photo-actions-wrapper">
                <div class="image-preview-placeholder d-flex align-items-center justify-content-center rounded border border-dashed">
                    <i class="far fa-image fa-2x text-muted"></i>
                </div>
                
                <label class="btn btn-outline-success btn-photo-action mb-0 d-flex align-items-center justify-content-center cursor-pointer">
                    <i class="far fa-image mr-2"></i> Choisir une image
                    <input type="file" name="photo" class="d-none" accept="image/*">
                </label>
                
                <button type="button" class="btn btn-outline-danger btn-photo-action d-flex align-items-center justify-content-center">
                    <i class="fas fa-times mr-2"></i> Supprimer
                </button>
            </div>
        </div>
    </form>
</div>
            <!-- Formulaire -->
            <form id="addAnimalForm">
                <div class="form-group">
                    <label>NOM</label>
                    <input type="text" placeholder="nom animal" class="form-control">
                </div>

                <div class="form-group">
                    <label>RACE</label>
                    <input type="text" placeholder="race animal" class="form-control">
                </div>

                <div class="form-group">
                    <label>POIDS (kg)</label>
                    <input type="number" step="0.1" placeholder="poids animal" class="form-control">
                </div>

                <div class="form-group">
                    <label>ELEVAGE</label>
                    <select class="form-control">
                        <option value="">élevage</option>
                        <option>Ferme des Monts</option>
                        <option>Vallée Verte</option>
                        <option>Prairie Fleurie</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>NOTE <span class="optional">(optionnelle)</span></label>
                    <textarea rows="3" placeholder="description de l'animal" class="form-control"></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel">Annuler</button>
                    <button type="submit" class="btn-save">Enregistrer</button>
                </div>
            </form>

        </div>
    </div>



</div>

<!-- MODALE MODIFIER L'ANIMAL -->
<div id="editAnimalModal" class="modal">
    <div class="modal-content">
        <div class="modal-header mt-3">
            <h2>
                <i class="fas fa-edit" style="color: #198754; margin-right: 10px;"></i>
                MODIFIER L'ANIMAL
            </h2>
            <span class="modal-close">&times;</span>
        </div>

        <div class="modal-body">
            <!-- Logo ÉLEVAGE+ -->
            <div class="modal-logo">
                <span class="logo-text">ÉLEVAGE<span style="color: #198754;">+</span></span>
            </div>

            <!-- Photo section -->
            <div class="photo-section">
                <label class="photo-label">Photo <span class="optional">(optionnelle)</span></label>
                <div class="photo-preview" id="editPhotoPreview">
                    <img id="editPreviewImage" src="" alt="Aperçu" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                    <div id="editPreviewPlaceholder" style="display: flex; flex-direction: column; align-items: center; gap: 5px;">
                        <i class="fas fa-camera-retro"></i>
                        <span>Aperçu photo</span>
                    </div>
                </div>
                <div class="photo-actions">
                    <button type="button" class="btn-choose-img" id="editChooseImageBtn">
                        <i class="fas fa-folder-open"></i>
                        Choisir une image
                    </button>
                    <button type="button" class="btn-delete-img" id="editDeleteImageBtn">
                        <i class="fas fa-trash-alt"></i>
                        Supprimer
                    </button>
                </div>
                <input type="file" id="editAnimalImageInput" accept="image/*" style="display: none;">
            </div>

            <!-- Formulaire -->
            <form id="editAnimalForm">
                <input type="hidden" id="editAnimalId">
                <div class="form-group">
                    <label>NOM</label>
                    <input type="text" id="editNom" placeholder="nom animal" class="form-control">
                </div>

                <div class="form-group">
                    <label>RACE</label>
                    <input type="text" id="editRace" placeholder="race animal" class="form-control">
                </div>

                <div class="form-group">
                    <label>POIDS (kg)</label>
                    <input type="number" step="0.1" id="editPoids" placeholder="poids animal" class="form-control">
                </div>

                <div class="form-group">
                    <label>ELEVAGE</label>
                    <select id="editElevage" class="form-control">
                        <option value="">élevage</option>
                        <option>Ferme des Monts</option>
                        <option>Vallée Verte</option>
                        <option>Prairie Fleurie</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>NOTE <span class="optional">(optionnelle)</span></label>
                    <textarea id="editNote" rows="3" placeholder="description de l'animal" class="form-control"></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel">Annuler</button>
                    <button type="submit" class="btn-save">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>

</div>

@endsection


@extends('layouts.menu')

@section('title', 'Élevages')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/eleveurCSS/elevage.css') }}">
@endpush

@section('content')
<div class="dashboard-wrapper container-fluid py-4">
    

    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 gap-3">
        <button type="button" class="btn btn-create d-flex align-items-center" data-toggle="modal" data-target="#createElevageModal">
            <i class="fas fa-plus mr-2"></i> Créer un nouvel élevage
        </button>
    </div>

    <div class="elevages-list d-flex flex-column gap-2 mb-4">
        @forelse($elevages ?? [
        ['id' => 1, 'titre' => 'ÉLEVAGE BOVIN - THIÈS', 'type' => 'bovins', 'local' => 'Thiès, Sénégal', 'surface' => '5 hectares', 'animaux' => '45 bovins', 'date' => '15/03/2025'],
        ['id' => 2, 'titre' => 'ÉLEVAGE CAPRIN - DAKAR', 'type' => 'caprins', 'local' => 'Thiès, Sénégal', 'surface' => '5 hectares', 'animaux' => '45 bovins', 'date' => '10/03/2024'],
        ['id' => 3, 'titre' => 'ÉLEVAGE BOVIN - THIÈS', 'type' => 'bovins', 'local' => 'Thiès, Sénégal', 'surface' => '5 hectares', 'animaux' => '45 bovins', 'date' => '15/03/2025']
        ] as $elevage)

    <div class="elevage-card bg-white rounded shadow-sm border mb-3">
    <div class="row no-gutters align-items-stretch">

        <div class="col-12 col-md-4 col-lg-3 col-image-container">
            <div class="img-container-full">
                <img src="{{ asset('images/img-elevage.jpeg') }}" alt="Ferme Intégrée">
                <div class="img-overlay-text text-uppercase text-center">
                    Ferme Intégrée<br>
                    <small>"Union" - Dakar / Sénégal</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-5 col-lg-6 p-4 d-flex flex-column justify-content-center">
            <h2 class="elevage-card-title text-uppercase mb-3">{{ $elevage['titre'] }}</h2>

            <div class="elevage-info-grid d-flex flex-column gap-2">
                <div class="info-item-row pb-1">
                    <div class="info-label-side">
                        <i class="fas fa-map-marker-alt text-danger info-icon"></i>
                        <strong>Localisation :</strong>
                    </div>
                    <span class="text-muted">{{ $elevage['local'] }}</span>
                </div>

                <div class="info-item-row pb-1">
                    <div class="info-label-side">
                        <i class="fas fa-layer-group text-secondary info-icon"></i>
                        <strong>Superficie :</strong>
                    </div>
                    <span class="text-muted">{{ $elevage['surface'] }}</span>
                </div>

                <div class="info-item-row pb-1">
                    <div class="info-label-side">
                        <i class="fas fa-cow text-secondary info-icon"></i>
                        <strong>Animaux :</strong>
                    </div>
                    <span class="text-muted">{{ $elevage['animaux'] }}</span>
                </div>

                <div class="info-item-row pb-1">
                    <div class="info-label-side">
                        <i class="far fa-calendar-alt text-danger info-icon"></i>
                        <strong>Créé le :</strong>
                    </div>
                    <span class="text-muted">{{ $elevage['date'] }}</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-3 col-lg-3 p-4 border-top border-md-top-0 d-flex align-items-center justify-content-center">
            <div class="action-buttons-group d-flex flex-row flex-md-column align-items-stretch justify-content-center gap-2 w-100">
                <button type="button" class="btn btn-action btn-view d-flex align-items-center justify-content-center gap-2" data-toggle="modal" data-target="#voirElevageModal">
                    <i class="far fa-eye"></i>
                    <span>voir</span>
                </button>

                <button type="button" class="btn btn-action btn-edit d-flex align-items-center justify-content-center gap-2" data-toggle="modal" data-target="#voirElevageModal">
                    <i class="fas fa-pencil-alt"></i>
                    <span>modifier</span>
                </button>

                <button type="button" class="btn btn-action btn-delete d-flex align-items-center justify-content-center gap-2">
                    <i class="far fa-trash-alt"></i>
                    <span>Supprimer</span>
                </button>
            </div>
        </div>

    </div>
</div>

        @empty
        <div class="alert alert-info text-center">Aucun élevage enregistré pour le moment.</div>
        @endforelse
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4">
        <a href="#" class="btn btn-outline-dark btn-pagination d-flex align-items-center">
            <i class="fas fa-caret-left mr-2"></i> précédente
        </a>
        <a href="#" class="btn btn-outline-dark btn-pagination d-flex align-items-center">
            suivante <i class="fas fa-caret-right ml-2"></i>
        </a>
    </div>

</div>

<div class="modal fade" id="createElevageModal" tabindex="-1" aria-labelledby="createElevageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content create-elevage-modal">

            <div class="modal-header border-0 align-items-center pt-4 px-4 pb-2">
                <h5 class="modal-title font-weight-bold d-flex align-items-center" id="createElevageModalLabel">
                    <i class="fas fa-plus-circle mr-2 text-dark"></i> CRÉER UN ÉLEVAGE
                </h5>
                <button type="button" class="close-modal-btn" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times-circle"></i>
                </button>
            </div>

            <div class="modal-body px-4 pb-4">
                <form action="#" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-section-box p-3 mb-3 rounded">
                        <label class="form-label font-weight-bold mb-2">
                            <i class="far fa-image text-success mr-1"></i> Photo <span class="font-weight-normal text-muted text-lowercase">(optionnelle)</span>
                        </label>
                        <div class="d-flex align-items-center flex-wrap gap-3">
                            <div class="image-preview-placeholder d-flex align-items-center justify-content-center rounded border border-dashed">
                                <i class="far fa-image fa-2x text-muted"></i>
                            </div>
                            <div class="d-flex gap-2 flex-grow-1">
                                <label class="btn btn-outline-success btn-photo-action mb-0 d-flex align-items-center justify-content-center cursor-pointer">
                                    <i class="far fa-image mr-2"></i> Choisir une image
                                    <input type="file" name="photo" class="d-none" accept="image/*">
                                </label>
                                <button type="button" class="btn btn-outline-danger btn-photo-action d-flex align-items-center justify-content-center">
                                    <i class="fas fa-times mr-2"></i> Supprimer
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold">
                            <i class="far fa-sticky-note text-warning mr-1"></i> Nom de l'élevage <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nom_elevage" class="form-control custom-input" placeholder="Élevage bovin de Thiès" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold">
                            <i class="fas fa-cow text-secondary mr-1"></i> Type d'élevage <span class="text-danger">*</span>
                        </label>
                        <div class="select-wrapper">
                            <select name="type_elevage" class="form-control custom-input appearance-none" required>
                                <option value="bovins" selected>Bovins</option>
                                <option value="caprins">Caprins</option>
                                <option value="ovins">Ovins</option>
                                <option value="volailles">Volailles</option>
                            </select>
                            <i class="fas fa-caret-down select-arrow"></i>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold">
                            <i class="fas fa-map-marker-alt text-danger mr-1"></i> Localisation
                        </label>
                        <input type="text" name="localisation" class="form-control custom-input" placeholder="Thiès, Sénégal">
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold">Superficie (hectares)</label>
                        <input type="number" name="superficie" class="form-control custom-input" placeholder="5" min="0" step="any">
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label font-weight-bold">
                            <i class="fas fa-feather-alt text-secondary mr-1"></i> Description <span class="font-weight-normal text-muted text-lowercase">(optionnelle)</span>
                        </label>
                        <textarea name="description" class="form-control custom-textarea" rows="3" placeholder="Élevage spécialisé dans la production laitière"></textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2">
                        <button type="button" class="btn btn-modal-cancel d-flex align-items-center" data-dismiss="modal">
                            <i class="fas fa-times-circle mr-2 text-danger"></i> Annuler
                        </button>
                        <button type="submit" class="btn btn-modal-submit d-flex align-items-center">
                            <i class="fas fa-check-square mr-2 text-white"></i> Valider
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="voirElevageModal" tabindex="-1" role="dialog" aria-labelledby="voirElevageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">

            <div class="modal-header border-0 align-items-center pt-4 px-4 pb-2 d-flex justify-content-between">
                <h5 class="modal-title font-weight-bold d-flex align-items-center" id="voirElevageModalLabel" style="font-size: 16px; color: #1a1a1a;">
                    <i class="fas fa-users-cog mr-2 text-dark"></i> MODIFIER L'ÉLEVAGE
                </h5>
                <button type="button" class="close text-danger border-0 bg-transparent" data-dismiss="modal" aria-label="Close" style="font-size: 24px; line-height: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body px-4 pb-4">
                <form action="#" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="p-3 mb-3 border rounded" style="background-color: #fafafa;">
                        <label class="form-label fw-bold small text-muted">🖼️ Photo <span class="text-muted font-weight-normal">(optionnelle)</span></label>
                        <div class="d-flex align-items-center gap-3 mt-1">
                            <div class="border rounded d-flex align-items-center justify-content-center bg-white" style="width: 70px; height: 70px; border-style: dashed !important;">
                                🖼️
                            </div>
                            <label class="btn btn-sm btn-outline-success d-flex align-items-center gap-1 mb-0 cursor-pointer" style="border-radius: 6px;">
                                <i class="far fa-image"></i> Choisir une image
                                <input type="file" name="photo" class="d-none" accept="image/*">
                            </label>
                            <button type="button" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1" style="border-radius: 6px;">
                                ❌ Supprimer
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">🏷️ Nom de l'élevage <span class="text-danger">*</span></label>
                        <input type="text" name="nom_elevage" class="form-control custom-input" value="{{ $elevage['titre'] ?? 'Élevage bovin de Thiès' }}" style="border-radius: 6px; font-size: 14px;" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">🐄 Type d'élevage <span class="text-danger">*</span></label>
                        <div class="select-wrapper">
                            <select name="type_elevage" class="form-control form-select" style="border-radius: 6px; font-size: 14px;" required>
                                <option value="bovins" selected>Bovins</option>
                                <option value="caprins">Caprins</option>
                                <option value="ovins">Ovins</option>
                                <option value="volailles">Volailles</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">📍 Localisation</label>
                        <input type="text" name="localisation" class="form-control custom-input" value="{{ $elevage['local'] ?? 'Thiès, Sénégal' }}" style="border-radius: 6px; font-size: 14px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Superficie (hectares)</label>
                        <input type="number" name="superficie" class="form-control custom-input" value="5" min="0" step="any" style="border-radius: 6px; font-size: 14px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">📝 Description <span class="text-muted font-weight-normal">(optionnelle)</span></label>
                        <textarea name="description" class="form-control custom-textarea" rows="3" style="border-radius: 6px; font-size: 14px;">Élevage spécialisé dans la production laitière</textarea>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-outline-secondary d-flex align-items-center gap-1" data-dismiss="modal" style="border-radius: 6px; min-width: 100px; justify-content: center;">
                            ❎ Annuler
                        </button>
                        <button type="submit" class="btn btn-success d-flex align-items-center gap-1" style="background-color: #198754; border-radius: 6px; min-width: 100px; justify-content: center;">
                            ✅ Valider
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
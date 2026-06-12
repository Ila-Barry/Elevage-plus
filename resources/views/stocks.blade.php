@extends('layouts.menu')

@section('title', 'Stocks')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/eleveurCSS/stocks.css') }}">
@endpush

@section('content')

<div class="dashboard-wrapper">

<main>
  <div class="container">
    <!-- Titre -->
    <h1 class="page-title">GESTION DES STOCKS</h1>

    
    <!-- Boutons en bas -->
    <div class="stocks-bottom-actions">
      <button class="btn btn-add"><i class="fas fa-plus"></i> Ajouter un produit</button>
      <button class="btn btn-report"><i class="fas fa-chart-bar"></i> Rapport</button>
    </div>


    <!-- Barre recherche + filtre -->
    <div class="stocks-toolbar">
      <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="recher...">
      </div>
      <select class="filter-cat">
        <option>Catégorie ▼</option>
        <option>Aliments</option>
        <option>Médicaments</option>
        <option>Equipements</option>
      </select>
    </div>

    <!-- Tableau -->
    <div class="table-wrapper">
      <table class="stocks-table">
        <thead>
          <tr>
            <th>Produit</th>
            <th>Catégorie</th>
            <th>Quantité</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <div class="prod-cell">
               <span class="prod-icon"><i class="fas fa-cow"></i></span>
                Aliment vache premium
              </div>
            </td>
            <td>Aliments</td>
            <td>
              850kg<br>
              <span class="stock-status status-medium"><i class="fas fa-exclamation-triangle"></i> Stock moyen</span>
            </td>
            <td>
              <div class="actions">
                <button class="btn-action btn-plus"><i class="fas fa-plus"></i></button>
                <button class="btn-action btn-eye"><i class="far fa-eye"></i></button>
                <button class="btn-action btn-edit"><i class="fas fa-pen"></i></button>
                <button class="btn-action btn-del"><i class="fas fa-times"></i></button>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="prod-cell">
                <span class="prod-icon"><i class="fas fa-pills"></i></span>
                Vitamine B12
              </div>
            </td>
            <td>Médicaments</td>
            <td>
              12 boîtes<br>
              <span class="stock-status status-good"><i class="fas fa-check-circle"></i> Stock bon</span>
            </td>
            <td>
              <div class="actions">
                <button class="btn-action btn-plus"><i class="fas fa-plus"></i></button>
                <button class="btn-action btn-eye"><i class="far fa-eye"></i></button>
                <button class="btn-action btn-edit"><i class="fas fa-pen"></i></button>
                <button class="btn-action btn-del"><i class="fas fa-times"></i></button>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="prod-cell">
                <span class="prod-icon"><i class="fas fa-seedling"></i></span>
                Paille
              </div>
            </td>
            <td>Aliments</td>
            <td>
              120kg<br>
              <span class="stock-status status-critical"><i class="fas fa-circle"></i> Stock critique</span>
            </td>
            <td>
              <div class="actions">
                <button class="btn-action btn-plus"><i class="fas fa-plus"></i></button>
                <button class="btn-action btn-eye"><i class="far fa-eye"></i></button>
                <button class="btn-action btn-edit"><i class="fas fa-pen"></i></button>
                <button class="btn-action btn-del"><i class="fas fa-times"></i></button>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="prod-cell">
                <span class="prod-icon"><i class="fas fa-mitten"></i></span>
                Guants vétérina
              </div>
            </td>
            <td>Equipements</td>
            <td>
              45 paires<br>
              <span class="stock-status status-good"><i class="fas fa-check-circle"></i> Stock bon</span>
            </td>
            <td>
              <div class="actions">
                <button class="btn-action btn-plus"><i class="fas fa-plus"></i></button>
                <button class="btn-action btn-eye"><i class="far fa-eye"></i></button>
                <button class="btn-action btn-edit"><i class="fas fa-pen"></i></button>
                <button class="btn-action btn-del"><i class="fas fa-times"></i></button>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="prod-cell">
                <span class="prod-icon"><i class="fas fa-syringe"></i></span>
                Antibiotique
              </div>
            </td>
            <td>Médicaments</td>
            <td>
              850kg<br>
              <span class="stock-status status-low"><i class="fas fa-exclamation-triangle"></i> Stock faible</span>
            </td>
            <td>
              <div class="actions">
                <button class="btn-action btn-plus"><i class="fas fa-plus"></i></button>
                <button class="btn-action btn-eye"><i class="far fa-eye"></i></button>
                <button class="btn-action btn-edit"><i class="fas fa-pen"></i></button>
                <button class="btn-action btn-del"><i class="fas fa-times"></i></button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Historique -->
    <div class="historique">
      <h3><i class="fas fa-file-alt"></i> HISTORIQUE DES MOUVEMENTS (7 derniers jours)</h3>
      <div class="hist-list">
        <div class="hist-item">
          <span class="dot dot-green"></span>
          <span>12/05 - Entrée : +200 kg Aliment Vache Premium</span>
        </div>
        <div class="hist-item">
          <span class="dot dot-red"></span>
          <span>11/05 - Sortie : -50 kg Aliment Vache Premium</span>
        </div>
        <div class="hist-item">
          <span class="dot dot-red"></span>
          <span>10/05 - Sortie : -2 boites Vitamine B12</span>
        </div>
        <div class="hist-item">
          <span class="dot dot-green"></span>
          <span>09/05 - Entrée : +100 kg Paille</span>
        </div>
      </div>
    </div>
  </div>
</main>

</div>

@endsection
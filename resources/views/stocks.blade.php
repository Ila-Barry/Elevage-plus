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
      <button class="btn btn-add" id="openModalAdd"><i class="fas fa-plus"></i> Ajouter un produit</button>
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
  <tr data-id="1" data-nom="Aliment vache premium" data-cat="Aliments" data-qte="850" data-unite="kg" data-seuil="100" data-prix="12500" data-status="medium">
    <td data-label="Produit">
      <div class="prod-cell">
       <span class="prod-icon"><i class="fas fa-cow"></i></span>
        Aliment vache premium
      </div>
    </td>
    <td data-label="Catégorie">Aliments</td>
    <td data-label="Quantité">
      850kg<br>
      <span class="stock-status status-medium"><i class="fas fa-exclamation-triangle"></i> Stock moyen</span>
    </td>
    <td data-label="Actions">
      <div class="actions">
        <button class="btn-action btn-plus" data-id="1"><i class="fas fa-plus"></i></button>
        <button class="btn-action btn-eye" data-id="1"><i class="far fa-eye"></i></button>
        <button class="btn-action btn-edit" data-id="1"><i class="fas fa-pen"></i></button>
        <button class="btn-action btn-del" data-id="1"><i class="fas fa-times"></i></button>
      </div>
    </td>
  </tr>
  <tr data-id="2" data-nom="Vitamine B12" data-cat="Médicaments" data-qte="12" data-unite="boîtes" data-seuil="5" data-prix="8000" data-status="good">
    <td data-label="Produit">
      <div class="prod-cell">
        <span class="prod-icon"><i class="fas fa-pills"></i></span>
        Vitamine B12
      </div>
    </td>
    <td data-label="Catégorie">Médicaments</td>
    <td data-label="Quantité">
      12 boîtes<br>
      <span class="stock-status status-good"><i class="fas fa-check-circle"></i> Stock bon</span>
    </td>
    <td data-label="Actions">
      <div class="actions">
        <button class="btn-action btn-plus" data-id="2"><i class="fas fa-plus"></i></button>
        <button class="btn-action btn-eye" data-id="2"><i class="far fa-eye"></i></button>
        <button class="btn-action btn-edit" data-id="2"><i class="fas fa-pen"></i></button>
        <button class="btn-action btn-del" data-id="2"><i class="fas fa-times"></i></button>
      </div>
    </td>
  </tr>
  <tr data-id="3" data-nom="Paille" data-cat="Aliments" data-qte="120" data-unite="kg" data-seuil="200" data-prix="3000" data-status="critical">
    <td data-label="Produit">
      <div class="prod-cell">
        <span class="prod-icon"><i class="fas fa-seedling"></i></span>
        Paille
      </div>
    </td>
    <td data-label="Catégorie">Aliments</td>
    <td data-label="Quantité">
      120kg<br>
      <span class="stock-status status-critical"><i class="fas fa-circle"></i> Stock critique</span>
    </td>
    <td data-label="Actions">
      <div class="actions">
        <button class="btn-action btn-plus" data-id="3"><i class="fas fa-plus"></i></button>
        <button class="btn-action btn-eye" data-id="3"><i class="far fa-eye"></i></button>
        <button class="btn-action btn-edit" data-id="3"><i class="fas fa-pen"></i></button>
        <button class="btn-action btn-del" data-id="3"><i class="fas fa-times"></i></button>
      </div>
    </td>
  </tr>
  <tr data-id="4" data-nom="Guants vétérina" data-cat="Equipements" data-qte="45" data-unite="paires" data-seuil="20" data-prix="2500" data-status="good">
    <td data-label="Produit">
      <div class="prod-cell">
        <span class="prod-icon"><i class="fas fa-mitten"></i></span>
        Guants vétérina
      </div>
    </td>
    <td data-label="Catégorie">Equipements</td>
    <td data-label="Quantité">
      45 paires<br>
      <span class="stock-status status-good"><i class="fas fa-check-circle"></i> Stock bon</span>
    </td>
    <td data-label="Actions">
      <div class="actions">
        <button class="btn-action btn-plus" data-id="4"><i class="fas fa-plus"></i></button>
        <button class="btn-action btn-eye" data-id="4"><i class="far fa-eye"></i></button>
        <button class="btn-action btn-edit" data-id="4"><i class="fas fa-pen"></i></button>
        <button class="btn-action btn-del" data-id="4"><i class="fas fa-times"></i></button>
      </div>
    </td>
  </tr>
  <tr data-id="5" data-nom="Antibiotique" data-cat="Médicaments" data-qte="850" data-unite="kg" data-seuil="900" data-prix="15000" data-status="low">
    <td data-label="Produit">
      <div class="prod-cell">
        <span class="prod-icon"><i class="fas fa-syringe"></i></span>
        Antibiotique
      </div>
    </td>
    <td data-label="Catégorie">Médicaments</td>
    <td data-label="Quantité">
      850kg<br>
      <span class="stock-status status-low"><i class="fas fa-exclamation-triangle"></i> Stock faible</span>
    </td>
    <td data-label="Actions">
      <div class="actions">
        <button class="btn-action btn-plus" data-id="5"><i class="fas fa-plus"></i></button>
        <button class="btn-action btn-eye" data-id="5"><i class="far fa-eye"></i></button>
        <button class="btn-action btn-edit" data-id="5"><i class="fas fa-pen"></i></button>
        <button class="btn-action btn-del" data-id="5"><i class="fas fa-times"></i></button>
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

  <!-- MODALE AJOUTER PRODUIT -->
<div class="modal" id="modalAdd">
  <div class="modal-content">
    <div class="modal-header">
      <h3><i class="fas fa-plus"></i> Ajouter un produit</h3>
      <button class="modal-close" data-close="modalAdd"><i class="fas fa-times"></i></button>
    </div>
    <form class="modal-body" id="formAdd">
      <div class="form-group">
        <label>Nom du produit</label>
        <input type="text" name="nom" placeholder="Ex: Aliment vache premium" required>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Catégorie</label>
          <select name="categorie" required>
            <option value="">Choisir</option>
            <option value="Aliments">Aliments</option>
            <option value="Médicaments">Médicaments</option>
            <option value="Equipements">Equipements</option>
          </select>
        </div>
        <div class="form-group">
          <label>Quantité</label>
          <input type="number" name="quantite" placeholder="Ex: 850" required>
        </div>
      <div class="form-row">
        <div class="form-group">
          <label>Unité</label>
          <select name="unite" required>
            <option value="kg">kg</option>
            <option value="boîtes">boîtes</option>
            <option value="paires">paires</option>
            <option value="pièces">pièces</option>
          </select>
        </div>
        <div class="form-group">
          <label>Seuil minimum</label>
          <input type="number" name="seuil" placeholder="Ex: 100" required>
        </div>
      </div>
      <div class="form-group">
        <label>Prix unitaire FCFA</label>
        <input type="number" name="prix" placeholder="Ex: 12500">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-cancel" data-close="modalAdd">Annuler</button>
        <button type="submit" class="btn btn-add">Enregistrer</button>
      </div>
    </form>
  </div>
</div>

<!-- MODALE VOIR PRODUIT -->
<div class="modal" id="modalView">
  <div class="modal-content">
    <div class="modal-header">
      <h3><i class="far fa-eye"></i> Détails du produit</h3>
      <button class="modal-close" data-close="modalView"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <div class="view-row">
        <span class="view-label">Produit:</span>
        <span class="view-value" id="viewNom">Aliment vache premium</span>
      </div>
      <div class="view-row">
        <span class="view-label">Catégorie:</span>
        <span class="view-value" id="viewCat">Aliments</span>
      </div>
      <div class="view-row">
        <span class="view-label">Quantité:</span>
        <span class="view-value" id="viewQte">850 kg</span>
      </div>
      <div class="view-row">
        <span class="view-label">Seuil min:</span>
        <span class="view-value" id="viewSeuil">100 kg</span>
      </div>
      <div class="view-row">
        <span class="view-label">Prix unitaire:</span>
        <span class="view-value" id="viewPrix">12 500 FCFA</span>
      </div>
      <div class="view-row">
        <span class="view-label">Statut:</span>
        <span class="view-value" id="viewStatus"><span class="stock-status status-medium">Stock moyen</span></span>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-cancel" data-close="modalView">Fermer</button>
    </div>
  </div>
</div>

<!-- MODALE MODIFIER PRODUIT -->
<div class="modal" id="modalEdit">
  <div class="modal-content">
    <div class="modal-header">
      <h3><i class="fas fa-pen"></i> Modifier le produit</h3>
      <button class="modal-close" data-close="modalEdit"><i class="fas fa-times"></i></button>
    </div>
    <form class="modal-body" id="formEdit">
      <input type="hidden" name="id" id="editId">
      <div class="form-group">
        <label>Nom du produit</label>
        <input type="text" name="nom" id="editNom" required>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Catégorie</label>
          <select name="categorie" id="editCat" required>
            <option value="Aliments">Aliments</option>
            <option value="Médicaments">Médicaments</option>
            <option value="Equipements">Equipements</option>
          </select>
        </div>
        <div class="form-group">
          <label>Quantité</label>
          <input type="number" name="quantite" id="editQte" required>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Unité</label>
          <select name="unite" id="editUnite" required>
            <option value="kg">kg</option>
            <option value="boîtes">boîtes</option>
            <option value="paires">paires</option>
            <option value="pièces">pièces</option>
          </select>
        </div>
        <div class="form-group">
          <label>Seuil minimum</label>
          <input type="number" name="seuil" id="editSeuil" required>
        </div>
      </div>
      <div class="form-group">
        <label>Prix unitaire FCFA</label>
        <input type="number" name="prix" id="editPrix">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-cancel" data-close="modalEdit">Annuler</button>
        <button type="submit" class="btn btn-edit">Mettre à jour</button>
      </div>
    </form>
  </div>
</div>

<!-- MODALE SUPPRIMER PRODUIT -->
<div class="modal" id="modalDelete">
  <div class="modal-content modal-small">
    <div class="modal-header">
      <h3><i class="fas fa-trash"></i> Supprimer le produit</h3>
      <button class="modal-close" data-close="modalDelete"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <p class="delete-text">Tu es sûr de vouloir supprimer <strong id="deleteNom">ce produit</strong> ?</p>
      <p class="delete-warning">Cette action est irréversible.</p>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-cancel" data-close="modalDelete">Annuler</button>
      <button type="button" class="btn btn-del" id="confirmDelete">Supprimer</button>
    </div>
  </div>
</div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
  
  const getData = (id) => document.querySelector(`tr[data-id="${id}"]`)?.dataset;

  // OUVRIR MODALE AJOUTER
  document.getElementById('openModalAdd').addEventListener('click', () => {
    document.getElementById('modalAdd').classList.add('active');
  });

  // FERMER MODALES
  document.querySelectorAll('[data-close]').forEach(btn => {
    btn.addEventListener('click', function() {
      document.getElementById(this.dataset.close).classList.remove('active');
    });
  });

  document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', e => {
      if(e.target === modal) modal.classList.remove('active');
    });
  });

  // ACTION + : Ajouter stock
  document.querySelectorAll('.btn-plus').forEach(btn => {
    btn.addEventListener('click', function() {
      const d = getData(this.dataset.id);
      const qte = prompt(`Quantité à ajouter pour ${d.nom} :`);
      if(qte && !isNaN(qte)) alert(`+${qte} ${d.unite} ajouté`); // AJAX ici
    });
  });

  // ACTION OEIL : Voir
  document.querySelectorAll('.btn-eye').forEach(btn => {
    btn.addEventListener('click', function() {
      const d = getData(this.dataset.id);
      document.getElementById('viewNom').textContent = d.nom;
      document.getElementById('viewCat').textContent = d.cat;
      document.getElementById('viewQte').textContent = `${d.qte} ${d.unite}`;
      document.getElementById('viewSeuil').textContent = `${d.seuil} ${d.unite}`;
      document.getElementById('viewPrix').textContent = parseInt(d.prix).toLocaleString() + ' FCFA';
      
      const status = {
        good: '<span class="stock-status status-good"><i class="fas fa-check-circle"></i> Stock bon</span>',
        medium: '<span class="stock-status status-medium"><i class="fas fa-exclamation-triangle"></i> Stock moyen</span>',
        low: '<span class="stock-status status-low"><i class="fas fa-exclamation-triangle"></i> Stock faible</span>',
        critical: '<span class="stock-status status-critical"><i class="fas fa-circle"></i> Stock critique</span>'
      };
      document.getElementById('viewStatus').innerHTML = status[d.status];
      document.getElementById('modalView').classList.add('active');
    });
  });

  // ACTION CRAYON : Modifier
  document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', function() {
      const d = getData(this.dataset.id);
      document.getElementById('editId').value = this.dataset.id;
      document.getElementById('editNom').value = d.nom;
      document.getElementById('editCat').value = d.cat;
      document.getElementById('editQte').value = d.qte;
      document.getElementById('editUnite').value = d.unite;
      document.getElementById('editSeuil').value = d.seuil;
      document.getElementById('editPrix').value = d.prix;
      document.getElementById('modalEdit').classList.add('active');
    });
  });

  // ACTION X : Supprimer
  document.querySelectorAll('.btn-del').forEach(btn => {
    btn.addEventListener('click', function() {
      const d = getData(this.dataset.id);
      document.getElementById('deleteNom').textContent = d.nom;
      document.getElementById('confirmDelete').dataset.id = this.dataset.id;
      document.getElementById('modalDelete').classList.add('active');
    });
  });

  // SUBMIT FORMS
  document.getElementById('formAdd').onsubmit = e => {
    e.preventDefault();
    alert('Produit ajouté'); // fetch Laravel ici
    e.target.reset();
    document.getElementById('modalAdd').classList.remove('active');
  };

  document.getElementById('formEdit').onsubmit = e => {
    e.preventDefault();
    alert('Produit modifié'); // fetch Laravel ici
    document.getElementById('modalEdit').classList.remove('active');
  };

  document.getElementById('confirmDelete').onclick = function() {
    alert('Produit ' + this.dataset.id + ' supprimé'); // fetch Laravel ici
    document.getElementById('modalDelete').classList.remove('active');
  };
});
</script>

</div>




@endsection
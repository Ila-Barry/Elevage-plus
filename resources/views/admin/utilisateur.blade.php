go@extends('layouts.admin.app')

@section('title', 'Gestion utilisateurs')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/utilisateur.css') }}">
@endpush

@section('content')

<div class="users-page">

    <!-- ============================================
         EN-TÊTE DE LA PAGE
         ============================================ -->
    <div class="users-header">
        <h1>👥 GESTION DES UTILISATEURS</h1>

        <div class="users-actions">
            <!-- Bouton pour ouvrir la modale d'ajout -->
            <button class="btn-add" id="openModal">
                + Ajouter un utilisateur
            </button>

            <button class="btn-export hover-effect">
    📤 exporter csv
</button>

<button class="btn-export hover-effect">
    ✉️ envoyer newsletter
</button>
        </div>
    </div>

    <!-- ============================================
         FILTRES ET RECHERCHE
         ============================================ -->
    <div class="filter-card">
        <h3>🔍 FILTRES ET RECHERCHE</h3>

        <div class="filter-row">
            <input 
                type="text"
                placeholder="🔍 rechercher par nom, mail, élevage..."
            >
<button class="btn-search">
    🔍 Rechercher
</button>

            <button class="status active">
                🟢 Actifs
            </button>

            <button class="status banned">
                🔴 Bannis
            </button>
        </div>
    </div>

    <!-- ============================================
         STATISTIQUES UTILISATEURS
         ============================================ -->
    <div class="stats-users">
        <div class="stat-user total">
            👥
            <strong>132</strong>
            <span>Total</span>
        </div>

        <div class="stat-user actifs">
            🟢
            <strong>124</strong>
            <span>Actifs</span>
        </div>

        <div class="stat-user bannis">
            🔴
            <strong>5</strong>
            <span>Bannis</span>
        </div>

        <div class="stat-user admin">
            👤
            <strong>3</strong>
            <span>Administrateurs</span>
        </div>
    </div>

    <!-- ============================================
         TABLEAU DES UTILISATEURS
         ============================================ -->
    <div class="users-table-card">
        <h3>📋 LISTE DES UTILISATEURS</h3>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>👤 Nom</th>
                    <th>📧 Email</th>
                    <th>👑 Rôle</th>
                    <th>📅 Date</th>
                    <th>⚙️ Actions</th>
                </tr>
            </thead>

            <tbody>
                {{-- 
                    ATTENTION : Ces données sont statiques pour la démonstration.
                    En production, utilisez plutôt :
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td><span class="role {{ $user->role }}">{{ $user->role }}</span></td>
                            <td>{{ $user->created_at->format('d/m/y') }}</td>
                            <td class="actions">
                                <!-- Actions -->
                            </td>
                        </tr>
                    @endforeach
                --}}

                <!-- ==========================================
                     UTILISATEUR 1 - Jean Dupont
                     ========================================== -->
                <tr>
                    <td>1</td>
                    <td>Jean Dupont</td>
                    <td>jean@email.com</td>
                    <td>
                        <span class="role eleveur">Eleveur</span>
                    </td>
                    <td>15/03/25</td>
                    <td class="actions">
                        <!-- Voir les détails -->
                        <button
    class="btn-view"
    title="Voir les détails"
    onclick="openViewModal(
        1,
        'Jean Dupont',
        'jean@email.com',
        'Eleveur',
        '15/03/25',
        'Thiès, Sénégal',
        'Bovin'
    )">
    👁️
</button>

                        <!-- Modifier - Appelle la fonction avec les paramètres -->
                        <button 
                            class="btn-edit" 
                            title="Modifier l'utilisateur"
                            onclick="openEditModal('Jean Dupont', 'jean@email.com', 'Thiès, Sénégal', 'Bovin', 'Eleveur')">
                            ✏️
                        </button>

                        <!-- Bloquer l'utilisateur -->
                        <button class="btn-lock" title="Bloquer l'utilisateur">🔒</button>

                        <!-- Supprimer l'utilisateur -->
                        <button class="btn-delete" title="Supprimer l'utilisateur">🗑️</button>
                    </td>
                </tr>

                <!-- ==========================================
                     UTILISATEUR 2 - Marie Diop
                     ========================================== -->
                <tr>
                    <td>2</td>
                    <td>Marie Diop</td>
                    <td>marie@email.com</td>
                    <td>
                        <span class="role eleveur">Eleveur</span>
                    </td>
                    <td>10/01/26</td>
                    <td class="actions">
                       <button class="btn-view" onclick="openViewModal()">
                        👁️
                      </button>
                        <button 
                            class="btn-edit"
                            title="Modifier l'utilisateur" 
                            onclick="openEditModal('Marie Diop', 'marie@email.com', 'Dakar, Sénégal', '', 'Eleveur')">
                            ✏️
                        </button>
                        <button class="btn-lock" title="Bloquer l'utilisateur">🔒</button>
                        <button class="btn-delete" title="Supprimer l'utilisateur">🗑️</button>
                    </td>
                </tr>

                <!-- ==========================================
                     UTILISATEUR 3 - Astou Ndiaye
                     ========================================== -->
                <tr>
                    <td>3</td>
                    <td>Astou Ndiaye</td>
                    <td>astou@email.com</td>
                    <td>
                        <span class="role visiteur-role">Visiteur</span>
                    </td>
                    <td>01/01/25</td>
                    <td class="actions">
                       <button class="btn-view" onclick="openViewModal()">
                        👁️
                      </button>
                        <button 
                            class="btn-edit"
                            title="Modifier l'utilisateur"
                            onclick="openEditModal('Astou Ndiaye', 'astou@email.com', 'Kaolack, Sénégal', '', 'Visiteur')">
                            ✏️
                        </button>
                        <button class="btn-lock" title="Bloquer l'utilisateur">🔒</button>
                        <button class="btn-delete" title="Supprimer l'utilisateur">🗑️</button>
                    </td>
                </tr>

                <!-- ==========================================
                     UTILISATEUR 4 - Fatou Ndiaye
                     ========================================== -->
                <tr>
                    <td>4</td>
                    <td>Fatou Ndiaye</td>
                    <td>fatou@email.com</td>
                    <td>
                        <span class="role eleveur">Eleveur</span>
                    </td>
                    <td>20/02/26</td>
                    <td class="actions">
                        <button class="btn-view" onclick="openViewModal()">
                           👁️
                        </button>
                        <button 
                            class="btn-edit"
                            title="Modifier l'utilisateur"
                            onclick="openEditModal('Fatou Ndiaye', 'fatou@email.com', 'Tivaouane, Sénégal', '', 'Eleveur')">
                            ✏️
                        </button>
                        <button class="btn-lock" title="Bloquer l'utilisateur">🔒</button>
                        <button class="btn-delete" title="Supprimer l'utilisateur">🗑️</button>
                    </td>
                </tr>

                <!-- ==========================================
                     UTILISATEUR 5 - Moussa Fall
                     ========================================== -->
                <tr>
                    <td>5</td>
                    <td>Moussa Fall</td>
                    <td>moussa@email.com</td>
                    <td>
                        <span class="role eleveur">Eleveur</span>
                    </td>
                    <td>25/02/26</td>
                    <td class="actions">
                        <button class="btn-view" onclick="openViewModal()">
                           👁️
                        </button>
                        <button 
                            class="btn-edit"
                            title="Modifier l'utilisateur"
                            onclick="openEditModal('Moussa Fall', 'fallmoussa@email.com', 'Mbour, Sénégal', 'Bovin', 'Eleveur')">
                            ✏️
                        </button>
                        <button class="btn-lock" title="Bloquer l'utilisateur">🔒</button>
                        <button class="btn-delete" title="Supprimer l'utilisateur">🗑️</button>
                    </td>
                </tr>

                <!-- ==========================================
                     UTILISATEUR 6 - Administrateur
                     ========================================== -->
                <tr>
                    <td>6</td>
                    <td>Administrateur</td>
                    <td>admin@elevageplus.com</td>
                    <td>
                        <span class="role admin-role">Admin</span>
                    </td>
                    <td>01/01/26</td>
                    <td class="actions">
                        <button class="btn-view" onclick="openViewModal()">
                           👁️
                        </button>
                        <button 
                            class="btn-edit"
                            title="Modifier l'administrateur"
                            onclick="openEditModal('Administrateur', 'admin@elevageplus.com', 'Dakar, Sénégal', '', 'Admin')">
                            ✏️
                        </button>
                        <!-- L'admin n'a pas de boutons bloquer/supprimer -->
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- ============================================
         PAGINATION
         ============================================ -->
    <div class="pagination">
        <button>← Précédente</button>
        <button class="active">1</button>
        <button>2</button>
        <button>3</button>
        <button>...</button>
        <button>Suivante →</button>
    </div>
</div>

<!-- ==============================================
     MODALE D'AJOUT D'UTILISATEUR
     ============================================== -->

   <div class="modal" id="userModal">
    <div class="modal-content">

        <h2>👤 Ajouter un utilisateur</h2>

      <form id="addUserForm">  

            <!-- Nom complet -->
            <label for="nom">Nom complet</label>
            <input 
                type="text" 
                id="nom"
                name="nom"
                placeholder="Nom utilisateur"
                required
            >

            <!-- Email -->
            <label for="email">📧 Email</label>
            <input 
                type="email" 
                id="email"
                name="email"
                placeholder="Email utilisateur"
                required
            >

            <!-- Téléphone -->
            <label for="telephone">📞 Numéro de téléphone</label>
            <input 
                type="tel" 
                id="telephone"
                name="telephone"
                placeholder="Ex: 77 123 45 67"
            >

            <!-- Type d'élevage -->
            <label for="type_elevage">🐄 Type d'élevage</label>
            <select name="type_elevage" id="type_elevage">
                <option value="">-- Choisir un type d'élevage --</option>
                <option value="bovin">🐄 Élevage bovin (vaches)</option>
                <option value="ovin">🐑 Élevage ovin (moutons)</option>
                <option value="caprin">🐐 Élevage caprin (chèvres)</option>
                <option value="volaille">🐔 Élevage de volaille</option>
                <option value="porcin">🐖 Élevage porcin</option>
                <option value="pisciculture">🐟 Pisciculture (poissons)</option>
                <option value="autre">Autre</option>
            </select>

            <!-- Localisation -->
            <label for="localisation">📍 Localisation</label>
            <select name="localisation" id="localisation">
                <option value="">-- Choisir une ville --</option>

                <!-- Région de Dakar -->
                <optgroup label="🌆 Région de Dakar">
                    <option value="dakar">Dakar</option>
                    <option value="pikine">Pikine</option>
                    <option value="guediawaye">Guédiawaye</option>
                    <option value="rufisque">Rufisque</option>
                    <option value="keur-massar">Keur Massar</option>
                    <option value="bargny">Bargny</option>
                    <option value="diamniadio">Diamniadio</option>
                </optgroup>

                <!-- Région de Thiès -->
                <optgroup label="🏖️ Région de Thiès">
                    <option value="thies">Thiès</option>
                    <option value="mbour">Mbour</option>
                    <option value="tivaouane">Tivaouane</option>
                    <option value="joal">Joal-Fadiouth</option>
                    <option value="khombole">Khombole</option>
                </optgroup>

                <!-- Région de Diourbel -->
                <optgroup label="🕌 Région de Diourbel">
                    <option value="diourbel">Diourbel</option>
                    <option value="touba">Touba</option>
                    <option value="mbacke">Mbacké</option>
                    <option value="bambey">Bambey</option>
                </optgroup>

                <!-- Région de Saint-Louis -->
                <optgroup label="🌊 Région de Saint-Louis">
                    <option value="saint-louis">Saint-Louis</option>
                    <option value="podor">Podor</option>
                    <option value="dagana">Dagana</option>
                    <option value="richard-toll">Richard-Toll</option>
                    <option value="rosso">Rosso</option>
                </optgroup>

                <!-- Région de Kaolack -->
                <optgroup label="🌿 Région de Kaolack">
                    <option value="kaolack">Kaolack</option>
                    <option value="nioro">Nioro du Rip</option>
                    <option value="guinguineo">Guinguinéo</option>
                </optgroup>

                <!-- Région de Fatick -->
                <optgroup label="🌴 Région de Fatick">
                    <option value="fatick">Fatick</option>
                    <option value="foundiougne">Foundiougne</option>
                    <option value="gossas">Gossas</option>
                </optgroup>

                <!-- Région de Louga -->
                <optgroup label="🐪 Région de Louga">
                    <option value="louga">Louga</option>
                    <option value="kebemer">Kébémer</option>
                    <option value="linguere">Linguère</option>
                </optgroup>

                <!-- Région de Ziguinchor -->
                <optgroup label="🌴 Région de Ziguinchor">
                    <option value="ziguinchor">Ziguinchor</option>
                    <option value="bignona">Bignona</option>
                    <option value="oussouye">Oussouye</option>
                </optgroup>

                <!-- Région de Kolda -->
                <optgroup label="🌳 Région de Kolda">
                    <option value="kolda">Kolda</option>
                    <option value="velingara">Vélingara</option>
                </optgroup>

                <!-- Région de Matam -->
                <optgroup label="🐫 Région de Matam">
                    <option value="matam">Matam</option>
                    <option value="ourossogui">Ourossogui</option>
                    <option value="kanel">Kanel</option>
                </optgroup>

                <!-- Région de Tambacounda -->
                <optgroup label="🌍 Région de Tambacounda">
                    <option value="tambacounda">Tambacounda</option>
                    <option value="bake">Bakel</option>
                    <option value="koumpentoum">Koumpentoum</option>
                </optgroup>

                <!-- Région de Kaffrine -->
                <optgroup label="🌾 Région de Kaffrine">
                    <option value="kaffrine">Kaffrine</option>
                    <option value="koungheul">Koungheul</option>
                </optgroup>

                <!-- Région de Kédougou -->
                <optgroup label="⛰️ Région de Kédougou">
                    <option value="kedougou">Kédougou</option>
                </optgroup>

                <!-- Région de Sédhiou -->
                <optgroup label="🌿 Région de Sédhiou">
                    <option value="sedhiou">Sédhiou</option>
                    <option value="goudomp">Goudomp</option>
                    <option value="bounkiling">Bounkiling</option>
                </optgroup>
            </select>

            <!-- Mot de passe -->
            <label for="password">🔒 Mot de passe</label>
            <input 
                type="password" 
                id="password"
                name="password"
                placeholder="Mot de passe"
                required
            >

            <!-- Confirmation du mot de passe -->
            <label for="password_confirmation">🔒 Confirmer le mot de passe</label>
            <div class="password-wrapper">
                <input 
                    type="password" 
                    id="password_confirmation"
                    name="password_confirmation"
                    placeholder="Confirmer le mot de passe"
                    required
                >
                <button 
                    type="button" 
                    class="show-password" 
                    onclick="togglePasswordVisibility()"
                    title="Afficher/masquer le mot de passe"
                >
                    👁️‍🗨️
                </button>
            </div>

            <!-- Rôle -->
            <label for="role">👑 Rôle</label>
            <select name="role" id="role">
                <option value="Eleveur">Eleveur</option>
                <option value="Visiteur">Visiteur</option>
                <option value="Admin">Admin</option>
            </select>

            <!-- Boutons de la modale -->
            <div class="modal-buttons">
                <button 
                    type="button" 
                    class="btn-close" 
                    id="closeModal"
                >
                    Annuler
                </button>

                <button type="submit" class="btn-save">
                    Ajouter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ==============================================
     MODALE DE MODIFICATION D'UTILISATEUR
     ============================================== -->
<div class="modal" id="editModal">
    <div class="modal-content">
        <h2>
            ✏️ Modifier l'utilisateur : 
            <span id="editNameDisplay"></span>
        </h2>

        <form id="editUserForm" method="POST">
            @csrf
            @method('PUT')

            <!-- Nom complet -->
            <label for="editNom">👤 Nom complet</label>
            <input type="text" id="editNom" name="nom">

            <!-- Email -->
            <label for="editEmail">📧 Email</label>
            <input type="email" id="editEmail" name="email">

            <!-- Localisation -->
            <label for="editLocalisation">📍 Localisation</label>
            <input type="text" id="editLocalisation" name="localisation">

            <!-- Type d'élevage -->
            <label for="editElevage">🐄 Type d'élevage</label>
            <select id="editElevage" name="type_elevage">
                <option value="">Aucun</option>
                <option value="Bovin">Bovin</option>
                <option value="Volaille">Volaille</option>
                <option value="Ovin">Ovin</option>
                <option value="Caprin">Caprin</option>
                <option value="Autre">Autre</option>
            </select>

            <!-- Rôle -->
            <label for="editRole">👑 Rôle</label>
            <select id="editRole" name="role">
                <option value="Eleveur">Eleveur</option>
                <option value="Visiteur">Visiteur</option>
                <option value="Admin">Admin</option>
            </select>

<!-- Ajoutez ces champs dans votre formulaire, après le champ TYPE(s) ELEVAGE -->

<!-- Statut du compte -->
<div class="form-group">
  <label class="form-label">Statut du compte</label>
  <div class="status-group">
    <label class="status-option">
      <input type="radio" name="statut" value="actif" checked>
      <span class="status-badge actif">Actif</span>
    </label>
    <label class="status-option">
      <input type="radio" name="statut" value="banni">
      <span class="status-badge banni">Banni</span>
    </label>
  </div>
</div>

<!-- Motif du bannissement -->
<div class="form-group" id="motif-bannissement-group">
  <label class="form-label">Motif du bannissement (si banni)</label>
  <textarea 
    name="motif_bannissement" 
    class="form-control" 
    rows="2" 
    placeholder="Publications inappropriées répétées"
  >Publications inappropriées répétées</textarea>
  <small class="form-text text-muted">Ce champ est facultatif, sauf si le compte est banni.</small>
</div>

            <!-- Boutons de la modale -->
            <div class="modal-buttons">
                <button 
                    type="button" 
                    class="btn-close" 
                    onclick="closeEditModal()"
                >
                    Annuler
                </button>

                <button type="submit" class="btn-save">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ==============================================
     MODALE 3 : VOIR LES DÉTAILS DE L'UTILISATEUR
     ==============================================
     Cette modale s'ouvre quand on clique sur le bouton 👁️ 
     Elle affiche toutes les informations de l'utilisateur
-->
<div class="modal" id="viewModal">
    <div class="modal-content view-modal-content">
        
        <!-- En-tête de la modale -->
        <h2>👤 DÉTAILS DE L'UTILISATEUR</h2>
        
        <!-- Conteneur des informations -->
        <div class="user-details">
            
            <!-- Ligne : ID de l'utilisateur -->
            <div class="detail-row">
                <span class="detail-label">🆔 ID :</span>
                <span id="viewUserId" class="detail-value">1</span>
            </div>
            
            <!-- Ligne : Nom complet -->
            <div class="detail-row">
                <span class="detail-label">👤 Nom :</span>
                <span id="viewUserName" class="detail-value">Jean Dupont</span>
            </div>
            
            <!-- Ligne : Email -->
            <div class="detail-row">
                <span class="detail-label">📧 Email :</span>
                <span id="viewUserEmail" class="detail-value">jean@email.com</span>
            </div>
            
            <!-- Ligne : Rôle -->
            <div class="detail-row">
                <span class="detail-label">👑 Rôle :</span>
                <span id="viewUserRole" class="detail-value">Eleveur</span>
            </div>
            
            <!-- Ligne : Date d'inscription -->
            <div class="detail-row">
                <span class="detail-label">📅 Date d'inscription :</span>
                <span id="viewUserDate" class="detail-value">15/03/25</span>
            </div>
            
            <!-- Ligne : Localisation -->
            <div class="detail-row">
                <span class="detail-label">📍 Localisation :</span>
                <span id="viewUserLocation" class="detail-value">Thiès, Sénégal</span>
            </div>
            
            <!-- Ligne : Type d'élevage -->
            <div class="detail-row">
                <span class="detail-label">🐄 Type d'élevage :</span>
                <span id="viewUserElevage" class="detail-value">Bovin</span>
            </div>
            
            <!-- Ligne : Téléphone -->
            <div class="detail-row">
                <span class="detail-label">📱 Téléphone :</span>
                <span id="viewUserPhone" class="detail-value">77 123 45 67</span>
            </div>
            
            <!-- Ligne : Statut (Actif/Banni) -->
            <div class="detail-row">
                <span class="detail-label">🔒 Statut :</span>
                <span id="viewUserStatus" class="detail-value status-active">🟢 Actif</span>
            </div>
        </div>

        <!-- Bouton pour fermer la modale -->
        <div class="modal-buttons">
            <button type="button" class="btn-close" onclick="closeViewModal()">Fermer</button>
        </div>
    </div>
</div>

<!-- ==============================================
     MODALE 4 : CONFIRMER LE BLOCAGE
     ==============================================
     Cette modale s'ouvre quand on clique sur le bouton 🔒
     Elle demande une confirmation avant de bloquer l'utilisateur
-->
<div class="modal" id="blockModal">
    <div class="modal-content confirm-modal">
        
        <!-- Icône de blocage -->
        <div class="confirm-icon">🔒</div>
        
        <!-- Titre -->
        <h2>Confirmer le blocage</h2>
        
        <!-- Message de confirmation avec le nom de l'utilisateur -->
        <p id="blockMessage">
            Êtes-vous sûr de vouloir bloquer l'utilisateur 
            <strong id="blockUserName">Jean Dupont</strong> ?
        </p>
        
        <!-- Avertissement -->
        <p class="warning-text">
            ⚠️ L'utilisateur ne pourra plus se connecter à son compte.
        </p>
        
        <!-- Boutons d'action -->
        <div class="modal-buttons">
            <button type="button" class="btn-close" onclick="closeBlockModal()">Annuler</button>
            <button type="button" class="btn-danger" id="confirmBlockBtn" onclick="confirmBlock()">
                🔒 Bloquer
            </button>
        </div>
    </div>
</div>

<!-- ==============================================
     MODALE 5 : CONFIRMER LA SUPPRESSION
     ==============================================
     Cette modale s'ouvre quand on clique sur le bouton 🗑️
     Elle demande une confirmation avant de supprimer l'utilisateur
     ⚠️ Action irréversible !
-->
<div class="modal" id="deleteModal">
    <div class="modal-content confirm-modal">
        
        <!-- Icône de suppression -->
        <div class="confirm-icon">🗑️</div>
        
        <!-- Titre -->
        <h2>Confirmer la suppression</h2>
        
        <!-- Message de confirmation avec le nom de l'utilisateur -->
        <p id="deleteMessage">
            Êtes-vous sûr de vouloir supprimer l'utilisateur 
            <strong id="deleteUserName">Jean Dupont</strong> ?
        </p>
        
        <!-- Avertissement important -->
        <p class="warning-text">
            ⚠️ Cette action est irréversible ! Toutes les données 
            de l'utilisateur seront définitivement supprimées.
        </p>
        
        <!-- Boutons d'action -->
        <div class="modal-buttons">
            <button type="button" class="btn-close" onclick="closeDeleteModal()">Annuler</button>
            <button type="button" class="btn-danger" id="confirmDeleteBtn" onclick="confirmDelete()">
                🗑️ Supprimer
            </button>
        </div>
    </div>
</div>

<!-- ==============================================
     JAVASCRIPT
     ============================================== -->
<script>
    /**
     * =============================================
     * GESTION DE LA MODALE D'AJOUT
     * =============================================
     */
    
    // Récupération des éléments DOM
    const openModalBtn = document.getElementById('openModal');
    const closeModalBtn = document.getElementById('closeModal');
    const userModal = document.getElementById('userModal');

    // Ouvrir la modale d'ajout
    openModalBtn.addEventListener('click', function() {
        userModal.classList.add('show');
        document.body.classList.add('modal-open'); // Empêche le scroll
    });

    // Fermer la modale d'ajout
    closeModalBtn.addEventListener('click', function() {
        userModal.classList.remove('show');
        document.body.classList.remove('modal-open');
    });

    // Fermer en cliquant à l'extérieur de la modale
    window.addEventListener('click', function(event) {
        if (event.target === userModal) {
            userModal.classList.remove('show');
            document.body.classList.remove('modal-open');
        }
    });

    /**
     * =============================================
     * GESTION DE LA VISIBILITÉ DES MOTS DE PASSE
     * =============================================
     */
    function togglePasswordVisibility() {
        const password = document.getElementById('password');
        const confirmation = document.getElementById('password_confirmation');
        const button = document.querySelector('.show-password');

        // Si les champs sont en mode "password", on les passe en "text" pour afficher
        if (password.type === 'password') {
            password.type = 'text';
            confirmation.type = 'text';
            button.innerHTML = '🙈'; // Icône œil barré
        } else {
            // Sinon, on les remet en "password" pour masquer
            password.type = 'password';
            confirmation.type = 'password';
            button.innerHTML = '👁️‍🗨️'; // Icône œil
        }
    }

    /**
     * =============================================
     * GESTION DE LA MODALE D'ÉDITION
     * =============================================
     */

    /**
     * Fonction pour ouvrir la modale d'édition avec les données de l'utilisateur
     * @param {string} nom - Nom complet de l'utilisateur
     * @param {string} email - Email de l'utilisateur
     * @param {string} localisation - Localisation de l'utilisateur
     * @param {string} elevage - Type d'élevage
     * @param {string} role - Rôle de l'utilisateur
     */
    function openEditModal(nom, email, localisation, elevage, role) {
        // Récupération de la modale
        const editModal = document.getElementById('editModal');
        
        // Affichage de la modale
        editModal.classList.add('show');
        document.body.classList.add('modal-open');

        // Remplissage des champs avec les données
        document.getElementById('editNameDisplay').textContent = nom;
        document.getElementById('editNom').value = nom;
        document.getElementById('editEmail').value = email;
        document.getElementById('editLocalisation').value = localisation;
        document.getElementById('editElevage').value = elevage;
        document.getElementById('editRole').value = role;
    }

    /**
     * Fonction pour fermer la modale d'édition
     */
    function closeEditModal() {
        document.getElementById('editModal').classList.remove('show');
        document.body.classList.remove('modal-open');
    }

    // Fermeture de la modale d'édition en cliquant à l'extérieur
    window.addEventListener('click', function(event) {
        const editModal = document.getElementById('editModal');
        if (event.target === editModal) {
            closeEditModal();
        }
    });

    // JavaScript pour gérer l'affichage conditionnel du champ "Motif du bannissement"
document.addEventListener('DOMContentLoaded', function() {
  const radioActif = document.querySelector('input[name="statut"][value="actif"]');
  const radioBanni = document.querySelector('input[name="statut"][value="banni"]');
  const motifGroup = document.getElementById('motif-bannissement-group');
  const motifTextarea = motifGroup.querySelector('textarea');

  function toggleMotifField() {
    if (radioBanni.checked) {
      motifGroup.classList.remove('hidden');
      motifTextarea.disabled = false;
      motifTextarea.focus();
    } else {
      motifGroup.classList.add('hidden');
      motifTextarea.disabled = true;
      motifTextarea.value = ''; // optionnel : vider le champ
    }
  }

  // Ajouter les écouteurs
  radioActif.addEventListener('change', toggleMotifField);
  radioBanni.addEventListener('change', toggleMotifField);

  // État initial
  toggleMotifField();
});
    /**
     * =============================================
     * VALIDATION DU FORMULAIRE D'AJOUT
     * =============================================
     */
   const form = document.getElementById("addUserForm");

if (form) {
    form.addEventListener("submit", function (event) {

        const password = document.getElementById("password");
        const confirmation = document.getElementById("password_confirmation");

        if (!password || !confirmation) return;

        if (password.value !== confirmation.value) {
            event.preventDefault();
            alert("❌ Les mots de passe ne correspondent pas !");
            return;
        }

        if (password.value.length < 8) {
            event.preventDefault();
            alert("❌ Le mot de passe doit contenir au moins 8 caractères !");
            return;
        }
    });
}

    /**
     * =============================================
     * CONFIRMATION DE SUPPRESSION
     * =============================================
     */

let selectedUserName = "";
    let selectedDeleteRow = null;

document.querySelectorAll('.btn-delete').forEach(function(button) {
    button.addEventListener('click', function(e) {

        selectedDeleteRow = e.target.closest("tr");

        const name = selectedDeleteRow.querySelectorAll("td")[1].textContent;

        document.getElementById("deleteUserName").textContent = name;

        document.getElementById("deleteModal").classList.add("show");
        document.body.classList.add("modal-open");
    });
});

function confirmDelete() {

    if (!selectedDeleteRow) return;

    selectedDeleteRow.remove();

    alert("🗑️ Utilisateur supprimé avec succès !");

    closeDeleteModal();
}
function closeDeleteModal() {
    document.getElementById("deleteModal").classList.remove("show");
    document.body.classList.remove("modal-open");
}
    /**
     * =============================================
     * CONFIRMATION DE BLOCAGE
     * =============================================
     */
    document.querySelectorAll('.btn-lock').forEach(function(button) {
    button.addEventListener('click', function(e) {

        const row = e.target.closest("tr");
        selectedUserRow = row;

        const name = row.querySelectorAll("td")[1].textContent;
        selectedUserName = name;

        document.getElementById("blockUserName").textContent = name;

        document.getElementById("blockModal").classList.add("show");
        document.body.classList.add("modal-open");
    });
});

function closeBlockModal() {
    document.getElementById("blockModal").classList.remove("show");
    document.body.classList.remove("modal-open");
}

function confirmBlock() {

    if (!selectedUserRow) return;

    const roleEl = selectedUserRow.querySelector(".role");

    // Si déjà banni → on évite double blocage
    if (roleEl.classList.contains("status-banned")) {
        alert("⚠️ Cet utilisateur est déjà bloqué !");
        closeBlockModal();
        return;
    }

    // 🔥 Mise à jour visuelle
    roleEl.textContent = "Banni";
    roleEl.classList.remove("eleveur", "admin-role", "visiteur-role");
    roleEl.classList.add("status-banned");

    // 🔥 feedback utilisateur
    alert("🔒 Utilisateur bloqué avec succès !");

    closeBlockModal();
}
    /**
     * =============================================
     * GESTION DES FILTRES (DÉMONSTRATION)
     * =============================================
     */
    document.querySelectorAll('.status').forEach(function(button) {
        button.addEventListener('click', function() {
            // Retire la classe active de tous les boutons
            document.querySelectorAll('.status').forEach(function(btn) {
                btn.classList.remove('active');
            });
            
            // Ajoute la classe active au bouton cliqué
            this.classList.add('active');
            
            // Ici, vous pouvez ajouter la logique de filtrage
            console.log('Filtre : ' + this.textContent.trim());
        });
    });

function openViewModal(id, nom, email, role, date, localisation, elevage) {

    document.getElementById("viewUserId").textContent = id;
document.getElementById("viewUserName").textContent = nom;
document.getElementById("viewUserEmail").textContent = email;
document.getElementById("viewUserRole").textContent = role;
document.getElementById("viewUserDate").textContent = date;
document.getElementById("viewUserLocation").textContent = localisation;
document.getElementById("viewUserElevage").textContent = elevage;

    document.getElementById("viewModal").classList.add("show");
    document.body.classList.add("modal-open");
}

function closeViewModal() {
    document.getElementById("viewModal").classList.remove("show");
    document.body.classList.remove("modal-open");
}

window.addEventListener("click", function(e) {
    const modal = document.getElementById("viewModal");

    if (e.target === modal) {
        closeViewModal();
    }
});

window.addEventListener("click", function (e) {

    const modals = [
        document.getElementById("userModal"),
        document.getElementById("editModal"),
        document.getElementById("viewModal"),
        document.getElementById("blockModal"),
        document.getElementById("deleteModal")
    ];

    modals.forEach(modal => {

        if (!modal) return;

        // si on clique sur le fond (overlay)
        if (e.target === modal) {
            modal.classList.remove("show");
            document.body.classList.remove("modal-open");
        }
    });
});
document.addEventListener("keydown", function (e) {
    if (e.key !== "Escape") return;

    document.querySelectorAll(".modal.show").forEach(modal => {
        modal.classList.remove("show");
    });

    document.body.classList.remove("modal-open");
});

    // Afficher un message dans la console pour confirmer le chargement
    console.log('✅ Page Gestion des utilisateurs chargée avec succès !');
</script>

<style>
    /* =============================================
       STYLES DES MODALES DE VISUALISATION
       ============================================= */
    
    /* Largeur maximale de la modale de visualisation */
    .view-modal-content {
        max-width: 600px;
    }

    /* Conteneur des détails de l'utilisateur */
    .user-details {
        margin: 20px 0;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 8px;
    }

    /* Chaque ligne de détail */
    .detail-row {
        display: flex;
        padding: 10px 15px;
        border-bottom: 1px solid #e9ecef;
        align-items: center;
    }

    /* Dernière ligne sans bordure */
    .detail-row:last-child {
        border-bottom: none;
    }

    /* Étiquette du détail (gauche) */
    .detail-label {
        font-weight: 600;
        width: 180px;
        color: #495057;
        font-size: 14px;
    }

    /* Valeur du détail (droite) */
    .detail-value {
        color: #212529;
        font-size: 14px;
        flex: 1;
    }

    /* Statut actif - couleur verte */
    .status-active {
        color: #28a745;
        font-weight: 600;
    }

    /* Statut banni - couleur rouge */
    .status-banned {
        color: #dc3545;
        font-weight: 600;
    }

    .btn-export.hover-effect {
    transition: all 0.25s ease;
}

/* SURVOL BLEU */
.btn-export.hover-effect:hover {
    background-color: #0d6efd;   /* bleu bootstrap */
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(13, 110, 253, 0.35);
    border-color: #0d6efd;
}

/* clic */
.btn-export.hover-effect:active {
    transform: translateY(0px);
    box-shadow: 0 3px 8px rgba(13, 110, 253, 0.25);
}

.status {
    transition: all 0.25s ease;
    cursor: pointer;
}

/* 🔵 ACTIFS - survol vert */
.status.active:hover {
    background-color: #28a745;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(40, 167, 69, 0.3);
    border-color: #28a745;
}

/* 🔴 BANNIS - survol rouge */
.status.banned:hover {
    background-color: #dc3545;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(220, 53, 69, 0.3);
    border-color: #dc3545;
}

/* clic (effet pression) */
.status:active {
    transform: translateY(0px);
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
}

    /* =============================================
       STYLES DES MODALES DE CONFIRMATION
       ============================================= */
    
    /* Centrage du contenu et largeur réduite */
    .confirm-modal {
        text-align: center;
        max-width: 450px;
    }

    /* Grande icône au centre */
    .confirm-icon {
        font-size: 64px;
        margin: 10px 0;
        display: block;
    }

    /* Titre de la confirmation */
    .confirm-modal h2 {
        margin: 10px 0;
        color: #212529;
    }

    /* Texte du message */
    .confirm-modal p {
        font-size: 16px;
        color: #495057;
        margin: 15px 0;
        line-height: 1.6;
    }

    /* Avertissement en rouge avec fond */
    .confirm-modal .warning-text {
        color: #dc3545;
        font-size: 14px;
        background: #fff5f5;
        padding: 10px;
        border-radius: 6px;
        border-left: 4px solid #dc3545;
        margin: 15px 0;
    }

    /* Bouton danger (rouge) pour actions critiques */
    .btn-danger {
        background: #dc3545;
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 14px;
    }

    /* Effet au survol du bouton danger */
    .btn-danger:hover {
        background: #c82333;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
    }

    /* Conteneur du mot de passe avec le bouton oeil */
    .password-wrapper {
        display: flex;
        align-items: center;
        position: relative;
    }

    /* Input avec espace pour le bouton */
    .password-wrapper input {
        flex: 1;
        padding-right: 45px;
    }

    /* Bouton pour afficher/masquer le mot de passe */
    .show-password {
        position: absolute;
        right: 10px;
        background: none;
        border: none;
        cursor: pointer;
        font-size: 20px;
        padding: 0;
        color: #6c757d;
        transition: color 0.3s;
    }

    .show-password:hover {
        color: #212529;
    }

    /* =============================================
       ANIMATIONS DES MODALES
       ============================================= */
    
    /* Animation d'entrée de la modale */
    .modal.show .modal-content {
        animation: modalSlideIn 0.3s ease-out;
    }

    /* Keyframes de l'animation */
    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-30px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    .view-modal {
        max-width: 600px;
    }

.view-modal h2 {
    font-size: 20px;   /* Réduit la taille du titre */
    margin-bottom: 20px;
    text-align: center;
}

.view-modal p {
    margin: 12px 0;
    font-size: 16px;
}

.view-modal strong {
    display: inline-block;
    width: 150px;
}
</style>
@push('scripts')
<script>
$(document).ready(function() {
    const token = localStorage.getItem('access_token');

    if (!token) {
        window.location.href = '/auth/login';
        return;
    }

    // Charger les statistiques utilisateurs
    function loadStats() {
        $.ajax({
            url: '/api/admin/dashboard/stats',
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success) {
                    const users = response.data.utilisateurs;
                    $('.stat-user.total strong').text(users.total);
                    $('.stat-user.actifs strong').text(users.actifs);
                    $('.stat-user.bannis strong').text(users.bannis);
                }
            }
        });
    }

    // Charger la liste des utilisateurs
    function loadUsers(filters = {}) {
        $.ajax({
            url: '/api/admin/users',
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            },
            data: filters,
            success: function(response) {
                if (response.success) {
                    const users = response.data.data;
                    const tbody = $('table tbody');
                    tbody.empty();

                    users.forEach(function(user) {
                        const roleClass = user.role === 'admin' ? 'admin-role' : 'eleveur';
                        const roleLabel = user.role === 'admin' ? 'Admin' : 'Eleveur';
                        const date = new Date(user.created_at).toLocaleDateString('fr-FR');

                        let actions = `
                            <button class="btn-view" title="Voir les détails"
                                onclick="openViewModal(${user.id}, '${user.name}', '${user.email}', '${roleLabel}', '${date}', '', '')">
                                👁️
                            </button>
                            <button class="btn-edit" title="Modifier"
                                onclick="openEditModal('${user.name}', '${user.email}', '', '', '${roleLabel}')">
                                ✏️
                            </button>`;

                        if (user.role !== 'admin') {
                            actions += `
                            <button class="btn-lock" title="Bloquer" data-id="${user.id}">🔒</button>
                            <button class="btn-delete" title="Supprimer" data-id="${user.id}" data-name="${user.name}">🗑️</button>`;
                        }

                        tbody.append(`
                            <tr id="user-row-${user.id}">
                                <td>${user.id}</td>
                                <td>${user.name}</td>
                                <td>${user.email}</td>
                                <td><span class="role ${roleClass}">${roleLabel}</span></td>
                                <td>${date}</td>
                                <td class="actions">${actions}</td>
                            </tr>
                        `);
                    });

                    // Rebind events
                    bindDeleteButtons();
                    bindBlockButtons();
                }
            },
            error: function(xhr) {
                console.error('Erreur:', xhr.responseJSON);
                if (xhr.status === 401) window.location.href = '/auth/login';
            }
        });
    }

    // Supprimer un utilisateur via API
    function bindDeleteButtons() {
        $('table').on('click', '.btn-delete', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            document.getElementById('deleteUserName').textContent = name;
            document.getElementById('deleteModal').classList.add('show');
            document.body.classList.add('modal-open');

            document.getElementById('confirmDeleteBtn').onclick = function() {
                $.ajax({
                    url: `/api/admin/users/${id}`,
                    method: 'DELETE',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    },
                    success: function() {
                        $(`#user-row-${id}`).remove();
                        closeDeleteModal();
                        loadStats();
                        alert('🗑️ Utilisateur supprimé avec succès !');
                    },
                    error: function(xhr) {
                        console.error('Erreur suppression:', xhr.responseJSON);
                    }
                });
            };
        });
    }

    // Bloquer un utilisateur via API
    function bindBlockButtons() {
        $('table').on('click', '.btn-lock', function() {
            const id = $(this).data('id');
            const row = $(this).closest('tr');
            const name = row.find('td').eq(1).text();

            document.getElementById('blockUserName').textContent = name;
            document.getElementById('blockModal').classList.add('show');
            document.body.classList.add('modal-open');

            document.getElementById('confirmBlockBtn').onclick = function() {
                $.ajax({
                    url: `/api/admin/users/${id}/status`,
                    method: 'PATCH',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    data: JSON.stringify({ status: 'bannie' }),
                    success: function() {
                        const roleEl = row.find('.role');
                        roleEl.text('Banni').removeClass('eleveur admin-role').addClass('status-banned');
                        closeBlockModal();
                        loadStats();
                        alert('🔒 Utilisateur bloqué avec succès !');
                    },
                    error: function(xhr) {
                        console.error('Erreur blocage:', xhr.responseJSON);
                    }
                });
            };
        });
    }

    // Recherche
    $('.btn-search').on('click', function() {
        const search = $('input[type="text"]').val();
        loadUsers({ search: search });
    });

    // Chargement initial
    loadStats();
    loadUsers();
});
</script>
@endpush

@endsection
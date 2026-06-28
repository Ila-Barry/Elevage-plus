@extends('layouts.admin.app')

@section('title', 'Gestion utilisateurs')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/utilisateur.css') }}">
@endpush


@section('content')

<div class="users-page">


    <!-- HEADER -->
    <div class="users-header">

        <h1>👥 GESTION DES UTILISATEURS</h1>

        <div class="users-actions">

             <!-- Bouton ouverture modale -->
            <button class="btn-add" id="openModal">

               + Ajouter un utilisateur

            </button>

            <button class="btn-export">
                📤 exporter csv
            </button>

            <button class="btn-export">
                ✉️ envoyer newsletter
            </button>

        </div>

    </div>



    <!-- FILTRES -->

    <div class="filter-card">

        <h3>🔍 FILTRES ET RECHERCHE</h3>

        <div class="filter-row">

            <input 
            type="text"
            placeholder="🔍 rechercher par nom, mail, élevage..."
            >

            <button class="status active">
                🟢 Actifs
            </button>

            <button class="status banned">
                🔴 Bannis
            </button>

        </div>

    </div>




    <!-- STATISTIQUES -->


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





    <!-- TABLE -->


<div class="users-table-card">


<h3>
📋 LISTE DES UTILISATEURS
</h3>


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
    Chaque utilisateur ajouté plus tard aura automatiquement
    cette structure avec tous les boutons d'action.
    Plus tard cette partie viendra d'une boucle Laravel :
    @foreach($users as $user)
--}}


<tr>

    <!-- ID utilisateur -->
    <td>1</td>


    <!-- Nom utilisateur -->
    <td>
        Jean Dupont
    </td>


    <!-- Email utilisateur -->
    <td>
        jean@email.com
    </td>


    <!-- Rôle utilisateur -->
    <td>

        <span class="role eleveur">
            Eleveur
        </span>

    </td>


    <!-- Date de création -->
    <td>
        15/03/25
    </td>



    <!-- ACTIONS UTILISATEUR -->
    <td class="actions">


        <!-- Voir détails -->
        <button class="btn-view">
            👁️
        </button>



        <!-- Modifier utilisateur -->
                    <button 
            class="btn-edit"

            data-name="Jean Dupont"
            data-email="jean@email.com"
            data-localisation="Thiès, Sénégal"
            data-elevage="Bovin"
            data-role="Eleveur"

            >
            ✏️
            </button>



        <!-- Bloquer utilisateur -->
        <button class="btn-lock">
            🔒
        </button>



        <!-- Supprimer utilisateur -->
        <button class="btn-delete">
            🗑️
        </button>


    </td>


</tr>





<tr>

    <td>2</td>


    <td>
        Marie Diop
    </td>


    <td>
        marie@email.com
    </td>


    <td>

        <span class="role eleveur">
            Eleveur
        </span>

    </td>


    <td>
        10/01/26
    </td>



    <!-- Même bloc action pour tous les utilisateurs -->
    <td class="actions">


        <button class="btn-view">
            👁️
        </button>


                                <button 
            class="btn-edit"

            data-name="Marie Diop"
            data-email="marie@email.com"
            data-localisation="Dakar, Sénégal"
            data-elevage="Volaille"
            data-role="Eleveur">

            ✏️

            </button>


        <button class="btn-lock">
            🔒
        </button>


        <button class="btn-delete">
            🗑️
        </button>


    </td>


</tr>





<tr>


    <td>3</td>


    <td>
        Astou Ndiaye
    </td>


    <td>
        astou@email.com
    </td>


    <td>

        <span class="role visiteur-role">
            Visiteur
        </span>

    </td>


    <td>
        01/01/25
    </td>



    <!-- Même actions disponibles pour l'admin -->
    <td class="actions">


        <button class="btn-view">
            👁️
        </button>


                    <button 
            class="btn-edit"

            data-name="Jean Dupont"
            data-email="jean@email.com"
            data-localisation="Thiès, Sénégal"
            data-elevage="Bovin"
            data-role="Eleveur"

            >
            ✏️
            </button>


        <button class="btn-lock">
            🔒
        </button>


        <button class="btn-delete">
            🗑️
        </button>


    </td>


</tr>


{{-- Utilisateur 4 --}}

<tr>

    <!-- ID -->
    <td>4</td>


    <!-- Nom -->
    <td>
        Fatou Ndiaye
    </td>


    <!-- Email -->
    <td>
        fatou@email.com
    </td>


    <!-- Rôle -->
    <td>

        <span class="role eleveur">
            Eleveur
        </span>

    </td>


    <!-- Date -->
    <td>
        20/02/26
    </td>



    <!-- Actions -->
    <td class="actions">

        <button class="btn-view">
            👁️
        </button>

                    <button 
            class="btn-edit"

            data-name="Jean Dupont"
            data-email="jean@email.com"
            data-localisation="Thiès, Sénégal"
            data-elevage="Bovin"
            data-role="Eleveur"

            >
            ✏️
            </button>

        <button class="btn-lock">
            🔒
        </button>

        <button class="btn-delete">
            🗑️
        </button>

    </td>

</tr>





{{-- Utilisateur 5 --}}

<tr>

    <!-- ID -->
    <td>5</td>


    <!-- Nom -->
    <td>
        Moussa Fall
    </td>


    <!-- Email -->
    <td>
        moussa@email.com
    </td>


    <!-- Rôle -->
    <td>

        <span class="role eleveur">
            Eleveur
        </span>

    </td>


    <!-- Date -->
    <td>
        25/02/26
    </td>



    <!-- Actions -->
    <td class="actions">


        <button class="btn-view">
            👁️
        </button>


                    <button 
            class="btn-edit"

            data-name="Jean Dupont"
            data-email="jean@email.com"
            data-localisation="Thiès, Sénégal"
            data-elevage="Bovin"
            data-role="Eleveur"

            >
            ✏️
            </button>


        <button class="btn-lock">
            🔒
        </button>


        <button class="btn-delete">
            🗑️
        </button>


    </td>

</tr>






{{-- Utilisateur 6 : ADMIN --}}

{{-- Utilisateur 6 : Administrateur actuel --}}

<tr>

    <!-- ID -->
    <td>6</td>


    <!-- Nom administrateur -->
    <td>
        Administrateur
    </td>


    <!-- Email administrateur -->
    <td>
        admin@elevageplus.com
    </td>



    <!-- Rôle -->
    <td>

        <span class="role admin-role">
            Admin
        </span>

    </td>



    <!-- Date création -->
    <td>
        01/01/26
    </td>



    <!-- Actions administrateur -->

    <td class="actions">


        <!-- Voir les informations -->
        <button class="btn-view">
            👁️
        </button>



        <!-- Modifier le profil admin -->
                    <button 
            class="btn-edit"

            data-name="Jean Dupont"
            data-email="jean@email.com"
            data-localisation="Thiès, Sénégal"
            data-elevage="Bovin"
            data-role="Eleveur"

            >
            ✏️
            </button>



        <!-- Pas de bouton bloquer 🔒 -->
        <!-- Pas de bouton supprimer 🗑️ -->


    </td>



</tr>


{{-- 
    Plus tard avec la base de données Laravel,
    ça deviendra :

    @foreach($users as $user)

        <tr>
            ...
        </tr>

    @endforeach

--}}


</tbody>


</table>


</div>




<!-- PAGINATION -->

<div class="pagination">


<button>
← Precedente
</button>


<button>
1
</button>

<button>
2
</button>

<button>
3
</button>


<button>
...
</button>


<button>
Suivante →
</button>


</div>



</div>

<!-- =====================================
     MODALE AJOUT UTILISATEUR
===================================== -->


<div class="modal" id="userModal">


    <!-- Contenu de la fenêtre -->

    <div class="modal-content">


        <!-- Titre -->

        <h2>
            👤 Ajouter un utilisateur
        </h2>



        <!-- Formulaire -->

        <form>


            <!-- Nom -->

            <label>
                Nom complet
            </label>


            <input 
            type="text"
            placeholder="Nom utilisateur"
            >




            <!-- Email -->

               <label>
                  📧 Email
               </label>


               <input 
               type="email"
               name="email"
               placeholder="Email utilisateur"
               >




               <!-- Téléphone -->

               <label>
                  📞 Numéro de téléphone
               </label>


               <input 
               type="tel"
               name="telephone"
               placeholder="Ex: 77 123 45 67"
               >




               <!-- Type d'élevage -->

               <label>
                  🐄 Type d'élevage
               </label>


               <select name="type_elevage">


               <option value="">
                  -- Choisir un type d'élevage --
               </option>


               <option value="bovin">
                  🐄 Élevage bovin (vaches)
               </option>


               <option value="ovin">
                  🐑 Élevage ovin (moutons)
               </option>


               <option value="caprin">
                  🐐 Élevage caprin (chèvres)
               </option>


               <option value="volaille">
                  🐔 Élevage de volaille
               </option>


               <option value="porcin">
                  🐖 Élevage porcin
               </option>


               <option value="pisciculture">
                  🐟 Pisciculture (poissons)
               </option>


               <option value="autre">
                  Autre
               </option>


               </select>




            <!-- Localisation -->

                           <label>
                  📍 Localisation
               </label>


               <select name="localisation">


               <option value="">
                  -- Choisir une ville --
               </option>



               <!-- REGION DAKAR -->

               <optgroup label="🌆 Région de Dakar">

                  <option value="dakar">Dakar</option>
                  <option value="pikine">Pikine</option>
                  <option value="guediawaye">Guédiawaye</option>
                  <option value="rufisque">Rufisque</option>
                  <option value="keur-massar">Keur Massar</option>
                  <option value="bargny">Bargny</option>
                  <option value="diamniadio">Diamniadio</option>

               </optgroup>




               <!-- REGION THIES -->

               <optgroup label="🏖️ Région de Thiès">

                  <option value="thies">Thiès</option>
                  <option value="mbour">Mbour</option>
                  <option value="tivaouane">Tivaouane</option>
                  <option value="joal">Joal-Fadiouth</option>
                  <option value="khombole">Khombole</option>

               </optgroup>




               <!-- REGION DIOURBEL -->

               <optgroup label="🕌 Région de Diourbel">

                  <option value="diourbel">Diourbel</option>
                  <option value="touba">Touba</option>
                  <option value="mbacke">Mbacké</option>
                  <option value="bambey">Bambey</option>

               </optgroup>




               <!-- REGION SAINT-LOUIS -->

               <optgroup label="🌊 Région de Saint-Louis">

                  <option value="saint-louis">Saint-Louis</option>
                  <option value="podor">Podor</option>
                  <option value="dagana">Dagana</option>
                  <option value="richard-toll">Richard-Toll</option>
                  <option value="rosso">Rosso</option>

               </optgroup>




               <!-- REGION KAOLACK -->

               <optgroup label="🌿 Région de Kaolack">

                  <option value="kaolack">Kaolack</option>
                  <option value="nioro">Nioro du Rip</option>
                  <option value="guinguineo">Guinguinéo</option>

               </optgroup>




               <!-- REGION FATICK -->

               <optgroup label="🌴 Région de Fatick">

                  <option value="fatick">Fatick</option>
                  <option value="foundiougne">Foundiougne</option>
                  <option value="gossas">Gossas</option>

               </optgroup>




               <!-- REGION LOUGA -->

               <optgroup label="🐪 Région de Louga">

                  <option value="louga">Louga</option>
                  <option value="kebemer">Kébémer</option>
                  <option value="linguere">Linguère</option>

               </optgroup>




               <!-- REGION ZIGUINCHOR -->

               <optgroup label="🌴 Région de Ziguinchor">

                  <option value="ziguinchor">Ziguinchor</option>
                  <option value="bignona">Bignona</option>
                  <option value="oussouye">Oussouye</option>

               </optgroup>




               <!-- REGION KOLDA -->

               <optgroup label="🌳 Région de Kolda">

                  <option value="kolda">Kolda</option>
                  <option value="velingara">Vélingara</option>

               </optgroup>




               <!-- REGION MATAM -->

               <optgroup label="🐫 Région de Matam">

                  <option value="matam">Matam</option>
                  <option value="ourossogui">Ourossogui</option>
                  <option value="kanel">Kanel</option>

               </optgroup>




               <!-- REGION TAMBACOUNDA -->

               <optgroup label="🌍 Région de Tambacounda">

                  <option value="tambacounda">Tambacounda</option>
                  <option value="bake">Bakel</option>
                  <option value="koumpentoum">Koumpentoum</option>

               </optgroup>




               <!-- REGION KAFFRINE -->

               <optgroup label="🌾 Région de Kaffrine">

                  <option value="kaffrine">Kaffrine</option>
                  <option value="koungheul">Koungheul</option>

               </optgroup>




               <!-- REGION KEDOUGOU -->

               <optgroup label="⛰️ Région de Kédougou">

                  <option value="kedougou">Kédougou</option>

               </optgroup>




               <!-- REGION SEDHIOU -->

               <optgroup label="🌿 Région de Sédhiou">

                  <option value="sedhiou">Sédhiou</option>
                  <option value="goudomp">Goudomp</option>
                  <option value="bounkiling">Bounkiling</option>

               </optgroup>



               </select>




            <!-- Mot de passe -->

               <label>
                  🔒 Mot de passe
               </label>

               <input 
                  type="password"
                  id="password"
                  name="password"
                  placeholder="Mot de passe"
               >



               <!-- Confirmation -->

               <label>
                  🔒 Confirmer le mot de passe
               </label>

               <input 
                  type="password"
                  id="password_confirmation"
                  name="password_confirmation"
                  placeholder="Confirmer le mot de passe"
               >



               <!-- Bouton unique -->

                  <button 
                     type="button"
                     class="show-password"
                     onclick="togglePasswords()">

                        <i id="eyeIcon" class="fa-solid fa-eye-slash"></i>

                     </button>



            <!-- Role -->

            <label>
                Rôle
            </label>


            <select>

                <option>
                    Eleveur
                </option>

                <option>
                    Visiteur
                </option>

                <option>
                    Admin
                </option>

            </select>





            <!-- Boutons -->

            <div class="modal-buttons">


                <button 
                type="button"
                class="btn-close"
                id="closeModal">

                    Annuler

                </button>




                <button 
                type="submit"
                class="btn-save">

                    Ajouter

                </button>


            </div>


        </form>


    </div>


</div>

                <!-- ==========================================
                    MODALE MODIFIER UTILISATEUR
                ========================================== -->

                <!-- La fenêtre complète de la modale -->
                <div class="modal" id="editUserModal">


                    <!-- Contenu blanc de la fenêtre -->
                    <div class="modal-content edit-modal">



                        <!-- Titre dynamique :
                            Le nom sera remplacé automatiquement par JS -->
                        <h2>
                            ✏️ Modifier l'utilisateur :
                            <span id="editNameTitle"></span>
                        </h2>




                        <!-- Formulaire modification -->
                        <form>



                            <!-- =========================
                                NOM UTILISATEUR
                            ========================== -->

                            <label>
                                👤 Nom complet <span>*</span>
                            </label>


                            <!--
                                L'id permet au JavaScript
                                de récupérer et modifier cette valeur
                            -->
                            <input 
                            id="editName"
                            type="text">





                            <!-- =========================
                                EMAIL
                            ========================== -->

                            <label>
                                📧 Email <span>*</span>
                            </label>


                            <input 
                            id="editEmail"
                            type="email">






                            <!-- =========================
                                LOCALISATION
                            ========================== -->


                            <label>
                                📍 LOCALISATION <span>*</span>
                            </label>

 <select name="localisation">


               <option value="">
                  -- Choisir une ville --
               </option>



               <!-- REGION DAKAR -->

               <optgroup label="🌆 Région de Dakar">

                  <option value="dakar">Dakar</option>
                  <option value="pikine">Pikine</option>
                  <option value="guediawaye">Guédiawaye</option>
                  <option value="rufisque">Rufisque</option>
                  <option value="keur-massar">Keur Massar</option>
                  <option value="bargny">Bargny</option>
                  <option value="diamniadio">Diamniadio</option>

               </optgroup>




               <!-- REGION THIES -->

               <optgroup label="🏖️ Région de Thiès">

                  <option value="thies">Thiès</option>
                  <option value="mbour">Mbour</option>
                  <option value="tivaouane">Tivaouane</option>
                  <option value="joal">Joal-Fadiouth</option>
                  <option value="khombole">Khombole</option>

               </optgroup>




               <!-- REGION DIOURBEL -->

               <optgroup label="🕌 Région de Diourbel">

                  <option value="diourbel">Diourbel</option>
                  <option value="touba">Touba</option>
                  <option value="mbacke">Mbacké</option>
                  <option value="bambey">Bambey</option>

               </optgroup>




               <!-- REGION SAINT-LOUIS -->

               <optgroup label="🌊 Région de Saint-Louis">

                  <option value="saint-louis">Saint-Louis</option>
                  <option value="podor">Podor</option>
                  <option value="dagana">Dagana</option>
                  <option value="richard-toll">Richard-Toll</option>
                  <option value="rosso">Rosso</option>

               </optgroup>




               <!-- REGION KAOLACK -->

               <optgroup label="🌿 Région de Kaolack">

                  <option value="kaolack">Kaolack</option>
                  <option value="nioro">Nioro du Rip</option>
                  <option value="guinguineo">Guinguinéo</option>

               </optgroup>




               <!-- REGION FATICK -->

               <optgroup label="🌴 Région de Fatick">

                  <option value="fatick">Fatick</option>
                  <option value="foundiougne">Foundiougne</option>
                  <option value="gossas">Gossas</option>

               </optgroup>




               <!-- REGION LOUGA -->

               <optgroup label="🐪 Région de Louga">

                  <option value="louga">Louga</option>
                  <option value="kebemer">Kébémer</option>
                  <option value="linguere">Linguère</option>

               </optgroup>




               <!-- REGION ZIGUINCHOR -->

               <optgroup label="🌴 Région de Ziguinchor">

                  <option value="ziguinchor">Ziguinchor</option>
                  <option value="bignona">Bignona</option>
                  <option value="oussouye">Oussouye</option>

               </optgroup>




               <!-- REGION KOLDA -->

               <optgroup label="🌳 Région de Kolda">

                  <option value="kolda">Kolda</option>
                  <option value="velingara">Vélingara</option>

               </optgroup>




               <!-- REGION MATAM -->

               <optgroup label="🐫 Région de Matam">

                  <option value="matam">Matam</option>
                  <option value="ourossogui">Ourossogui</option>
                  <option value="kanel">Kanel</option>

               </optgroup>




               <!-- REGION TAMBACOUNDA -->

               <optgroup label="🌍 Région de Tambacounda">

                  <option value="tambacounda">Tambacounda</option>
                  <option value="bake">Bakel</option>
                  <option value="koumpentoum">Koumpentoum</option>

               </optgroup>




               <!-- REGION KAFFRINE -->

               <optgroup label="🌾 Région de Kaffrine">

                  <option value="kaffrine">Kaffrine</option>
                  <option value="koungheul">Koungheul</option>

               </optgroup>




               <!-- REGION KEDOUGOU -->

               <optgroup label="⛰️ Région de Kédougou">

                  <option value="kedougou">Kédougou</option>

               </optgroup>




               <!-- REGION SEDHIOU -->

               <optgroup label="🌿 Région de Sédhiou">

                  <option value="sedhiou">Sédhiou</option>
                  <option value="goudomp">Goudomp</option>
                  <option value="bounkiling">Bounkiling</option>

               </optgroup>



               </select>





                            <!-- =========================
                                TYPE ELEVAGE
                            ========================== -->


                            <label>
                                🐄 TYPE(s) ELEVAGE <span>*</span>
                            </label>



                            <select id="editElevage">


                                <option>Bovin</option>

                                <option>Volaille</option>

                                <option>Ovin</option>

                                <option>Caprin</option>

                                <option>Pisciculture</option>


                            </select>








                            <!-- =========================
                                ROLE UTILISATEUR
                            ========================== -->


                            <label>
                                👑 Rôle <span>*</span>
                            </label>



                            <select id="editRole">


                                <option>Eleveur</option>

                                <option>Visiteur</option>

                                <option>Admin</option>


                            </select>







                            <!-- Séparation visuelle -->
                            <hr>








                            <!-- =========================
                                STATUT DU COMPTE
                            ========================== -->


                            <label>
                                🔒 Statut du compte
                            </label>




                            <div class="status-choice">


                                <!-- Compte actif -->

                                <label>

                                    Actif


                                    <input 
                                    type="radio"
                                    name="status"
                                    value="actif"
                                    checked
                                    >

                                </label>




                                <!-- Compte banni -->

                                <label>

                                    Banni


                                    <input 
                                    type="radio"
                                    name="status"
                                    value="banni"
                                    >

                                </label>



                            </div>







                            <!-- =========================
                                MOTIF DU BANNISSEMENT
                            ========================== -->


                            <label>
                                📝 Motif du bannissement
                                (si banni)
                            </label>



                            <textarea 
                            id="banReason"
                            placeholder="Publications inappropriées répétées">
                            </textarea>








                            <!-- =========================
                                BOUTONS
                            ========================== -->


                            <div class="modal-buttons">


                                <!-- Fermer la fenêtre -->

                                <button 
                                type="button"
                                id="closeEditModal"
                                class="btn-close">

                                    ANNULER

                                </button>





                                <!-- Sauvegarder -->

                                <button 
                                type="submit"
                                class="btn-save">


                                    ENREGISTRER


                                </button>



                            </div>



                        </form>



                    </div>


                </div>

<script>

const openBtn = document.getElementById('openModal');
const closeBtn = document.getElementById('closeModal');
const modal = document.getElementById('userModal');


// Ouvrir la modale
openBtn.addEventListener('click', () => {

    modal.classList.add('show');

    // bloque le scroll arrière-plan
    document.body.classList.add('modal-open');

});



// Fermer la modale
closeBtn.addEventListener('click', () => {

    modal.classList.remove('show');

    // réactive le scroll
    document.body.classList.remove('modal-open');

});



// Fermer en cliquant sur le fond
window.addEventListener('click', (e) => {

    if(e.target === modal){

        modal.classList.remove('show');

        document.body.classList.remove('modal-open');

    }

});

function togglePasswords(){

    const password = document.getElementById('password');

    const confirmation = document.getElementById('password_confirmation');

    const icon = document.getElementById('eyeIcon');


    if(password.type === "password"){

        password.type = "text";

        confirmation.type = "text";


        // oeil ouvert
        icon.classList.remove('fa-eye-slash');

        icon.classList.add('fa-eye');


    }
    else{

        password.type = "password";

        confirmation.type = "password";


        // oeil barré
        icon.classList.remove('fa-eye');

        icon.classList.add('fa-eye-slash');

    }

}

</script>
<script>


const editModal = document.getElementById('editUserModal');


const editName = document.getElementById('editName');
const editEmail = document.getElementById('editEmail');
const editLocalisation = document.getElementById('editLocalisation');
const editElevage = document.getElementById('editElevage');
const editRole = document.getElementById('editRole');

const editTitle = document.getElementById('editNameTitle');



document.addEventListener('click', function(e){


    const button = e.target.closest('.btn-edit');


    if(button){


        editModal.classList.add('show');

        document.body.classList.add('modal-open');


        editName.value = button.dataset.name;

        editEmail.value = button.dataset.email;

        editLocalisation.value = button.dataset.localisation;

        editElevage.value = button.dataset.elevage;

        editRole.value = button.dataset.role;


        editTitle.textContent = button.dataset.name;


    }


});





document.getElementById('closeEditModal')
.addEventListener('click',()=>{


    editModal.classList.remove('show');

    document.body.classList.remove('modal-open');


});



window.addEventListener('click',(e)=>{


    if(e.target === editModal){


        editModal.classList.remove('show');

        document.body.classList.remove('modal-open');


    }


});


</script>

<!-- =====================================
     MODALE INFORMATIONS UTILISATEUR
===================================== -->

<div class="modal" id="viewUserModal">


    <div class="modal-content">


        <h2>
            👤 Informations utilisateur
        </h2>


        <p>
            <strong>Nom :</strong>
            <span id="viewName"></span>
        </p>


        <p>
            <strong>Email :</strong>
            <span id="viewEmail"></span>
        </p>


        <p>
            <strong>Rôle :</strong>
            <span id="viewRole"></span>
        </p>


        <p>
            <strong>Date inscription :</strong>
            <span id="viewDate"></span>
        </p>


        <div class="modal-buttons">


            <button 
            class="btn-close"
            id="closeViewModal">

                Fermer

            </button>


        </div>


    </div>


</div>
<script>


const viewModal = document.getElementById('viewUserModal');

const closeViewModal = document.getElementById('closeViewModal');



document.addEventListener('click', function(e){


    const btn = e.target.closest('.btn-view');


    if(btn){


        // récupérer la ligne du tableau

        const row = btn.closest('tr');


        // récupérer les colonnes

        const name = row.children[1].innerText;

        const email = row.children[2].innerText;

        const role = row.children[3].innerText;

        const date = row.children[4].innerText;



        // afficher dans la modale

        document.getElementById('viewName').textContent = name;

        document.getElementById('viewEmail').textContent = email;

        document.getElementById('viewRole').textContent = role;

        document.getElementById('viewDate').textContent = date;



        // ouvrir

        viewModal.classList.add('show');

        document.body.classList.add('modal-open');


    }


});




// fermer bouton

closeViewModal.addEventListener('click',()=>{


    viewModal.classList.remove('show');

    document.body.classList.remove('modal-open');


});





// fermer en cliquant dehors

window.addEventListener('click',(e)=>{


    if(e.target === viewModal){


        viewModal.classList.remove('show');

        document.body.classList.remove('modal-open');


    }


});



</script>
@endsection
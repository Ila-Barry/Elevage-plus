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
        <button class="btn-edit">
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


        <button class="btn-edit">
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


        <button class="btn-edit">
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

        <button class="btn-edit">
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


        <button class="btn-edit">
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
        <button class="btn-edit">
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
                Email
            </label>


            <input 
            type="email"
            placeholder="Email utilisateur"
            >




            <!-- Localisation -->

            <label>
               📍 Localisation
            </label>

            <select name="localisation">

               <option value="">-- Choisir une ville --</option>

               <option value="dakar">Dakar</option>
               <option value="pikine">Pikine</option>
               <option value="guediawaye">Guédiawaye</option>
               <option value="rufisque">Rufisque</option>
               <option value="thies">Thiès</option>
               <option value="saint-louis">Saint-Louis</option>
               <option value="kaolack">Kaolack</option>

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

                  👁️ Afficher les mots de passe

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


    if(password.type === "password"){

        password.type = "text";

        confirmation.type = "text";

    } 
    else {

        password.type = "password";

        confirmation.type = "password";

    }

}

</script>
@endsection
@extends('layouts.navbar')

@section('title', 'Paramètres - Élevage+')

@section('content')
<link rel="stylesheet" href="{{ asset('css/authCSS/parametre.css') }}">
    <div class="row">
        <div class="col-md-4">
            
            @include('layouts.sidebar')

        </div>

        <!-- espace de travail -->
        <div class="col-md-8">
            <h1>Paramètres</h1>
            <p>Gérez vos paramètres ici !</p>
        </div>
    </div>

@endsection
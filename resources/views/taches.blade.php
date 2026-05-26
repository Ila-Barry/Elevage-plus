@extends('layouts.navbar')

@section('title', 'Tâches - Élevage+')

@section('content')

    <!-- style_css -->
    <link rel="stylesheet" href="{{ asset('css/eleveurCSS/taches.css') }}">

    <!-- contenue de la page taches -->
    <div class="row">
        <div class="col-md-4">
            @include('layouts.sidebar')
        </div>

        <!-- space de travail -->
        <div class="col-md-8">
            <h1>Tâches</h1>
            <p>Gérez vos tâches ici !</p>
        </div>
    </div>
@endsection
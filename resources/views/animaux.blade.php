@extends('layouts.navbar')

@section('title', 'Animaux - Élevage+')

@section('content')

    <!-- style_css -->
    <link rel="stylesheet" href="{{ asset('css/eleveurCSS/animaux.css') }}">

    <!-- contenue de la page animaux -->
    <div class="row">
        <div class="col-md-4">
            @include('layouts.sidebar')
        </div>

        <!-- space de travail -->
        <div class="col-md-8">
            <h1>Animaux</h1>
            <p>Gérez vos animaux ici !</p>
        </div>
    </div>
@endsection
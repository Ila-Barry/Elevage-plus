@extends('layouts.navbar')

@section('title', 'Élevages - Élevage+')

@section('content')

    <!-- style_css -->
    <link rel="stylesheet" href="{{ asset('css/eleveurCSS/elevage.css') }}">

    <!-- contenue de la page elevages -->
    <div class="row">
        <div class="col-md-4">
            @include('layouts.sidebar')
        </div>

        <!-- space de travail -->
        <div class="col-md-8">
            <h1>Elevages</h1>
            <p>Gérez vos élevages ici !</p>
        </div>
    </div>
@endsection
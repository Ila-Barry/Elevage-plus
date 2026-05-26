@extends('layouts.navbar')

@section('title', 'Messages - Élevage+')

@section('content')

    <!-- style_css -->
    <link rel="stylesheet" href="{{ asset('css/eleveurCSS/messages.css') }}">

    <!-- contenue de la page messages -->
    <div class="row">
        <div class="col-md-4">
            @include('layouts.sidebar')
        </div>

        <!-- space de travail -->
        <div class="col-md-8">
            <h1>Messages</h1>
            <p>Gérez vos messages ici !</p>
        </div>
    </div>
@endsection
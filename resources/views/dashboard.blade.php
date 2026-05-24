@extends('layouts.navbar')

@section('title', 'Dashboard - Élevage+') 

@section('content')
    <div class="row">
        <div class="col-md-4">
            
            @include('layouts.sidebar')

        </div>

        <!-- space de travail -->
        <div class="col-md-8">
            <h1>Dashboard</h1>
            <p>Bienvenue sur votre dashboard !</p>
        </div>
    </div>
@endsection
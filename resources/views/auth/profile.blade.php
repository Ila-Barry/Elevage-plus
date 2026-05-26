@extends('layouts.navbar')

@section('title', 'Profile - Élevage+')

@section('content')
<link rel="stylesheet" href="{{ asset('css/authCSS/profile.css') }}">


    <div class="row">
        <div class="col-md-4">
            
            @include('layouts.sidebar')

        </div>

        <!-- espace de travail -->
        <div class="col-md-8">
            <h1>Profile</h1>
            <p>Gérez votre profile ici !</p>
        </div>
    </div>

@endsection
@extends('layouts.navbar')

@section('title', 'Tâches - Élevage+')

@section('content')
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
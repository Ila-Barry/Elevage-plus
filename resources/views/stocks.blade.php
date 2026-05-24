@extends('layouts.navbar')

@section('title', 'Stocks - Élevage+')

@section('content')
    <div class="row">
        <div class="col-md-4">
            
            @include('layouts.sidebar')

        </div>

        <!-- space de travail -->
        <div class="col-md-8">
            <h1>Stocks</h1>
            <p>Gérez vos stocks ici !</p>
        </div>
    </div>
@endsection
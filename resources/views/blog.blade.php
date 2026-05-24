@extends('layouts.navbar')

@section('title', 'Blog - Élevage+')

@section('content')
    <div class="row">
        <div class="col-md-4">
            
            @include('layouts.sidebar')

        </div>

        <!-- space de travail -->
        <div class="col-md-8">
            <h1>Blog</h1>
            <p>Consultez nos dernières actualités !</p>
        </div>
    </div>
@endsection
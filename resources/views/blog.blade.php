@extends('layouts.navbar')

@section('title', 'Blog - Élevage+')

@section('content')

    <!-- style_css -->
    <link rel="stylesheet" href="{{ asset('css/eleveurCSS/blog.css') }}">

    <!-- contenue de la page blog -->
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
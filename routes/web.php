<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('hom');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/elevages', function () {
    return view('elevages');
});

Route::get('/animaux', function () {
    return view('animaux');
});

Route::get('/taches', function () {
    return view('taches');
});

Route::get('/stocks', function () {
    return view('stocks');
});

Route::get('/blog', function () {
    return view('blog');
});

Route::get('/messagerie', function () {
    return view('messages');
});

// Route::get('/parametres', function () {
//     return view('parametres');
// });

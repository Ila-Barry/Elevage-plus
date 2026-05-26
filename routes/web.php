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
    return view('home');
});

Route::get('/profilEleveur', function () {
    return view('profilEleveur');
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

Route::get('/messages', function () {
    return view('messages');
});


Route::get('/auth/parametre', function () {
    return view('auth/parametre');
});

Route::get('/auth/profile', function () {
    return view('auth/profile');
});

Route::get('/auth/login', function () {
    return view('auth/login');
});

Route::get('/auth/register', function () {
    return view('auth/register');
});
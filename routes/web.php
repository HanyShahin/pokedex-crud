<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PokemonController;

// routes/web.php
Route::get('/', function () {
    return view('pokedex');
});

Route::get('/login', function () {
    return view('login');
});

// Adicione esta linha para as rotas do CRUD
Route::resource('pokemons', PokemonController::class);
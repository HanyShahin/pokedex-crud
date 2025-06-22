<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PokemonController; 


// 2. AGORA A ROTA PRINCIPAL REDIRECIONA PARA O LOGIN
Route::get('/', function () {
    return redirect()->route('login');
});

// 3. O DASHBOARD REDIRECIONA PARA A SUA POKEDEX APÓS O LOGIN
Route::get('/dashboard', function () {
    return redirect()->route('pokedex.index'); // O nome que daremos à rota da pokedex
})->middleware(['auth', 'verified'])->name('dashboard');

// Rotas de perfil que o Breeze cria (manter como está)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 4. GRUPO DE ROTAS PROTEGIDAS PARA A APLICAÇÃO POKÉDEX
Route::middleware('auth')->group(function () {
    
    // Rota para a view principal da Pokedex
    Route::get('/pokedex', function () {
        return view('pokedex');
    })->name('pokedex.index'); // Damos um nome para o redirect do dashboard funcionar

    // Rotas de Recurso para o CRUD (Create, Read, Update, Delete)
    // Elas também ficam aqui dentro para serem protegidas
    Route::resource('pokemons', PokemonController::class);
});


// Linha que carrega todas as rotas de autenticação (login, registro, etc.)
require __DIR__.'/auth.php';
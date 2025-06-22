<?php

// app/Http/Controllers/PokemonController.php
namespace App\Http\Controllers;

use App\Models\Pokemon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PokemonController extends Controller
{
    // READ: Retorna todos os Pokémon
    public function index()
    {
        $pokemons = Pokemon::orderBy('number', 'asc')->get();
        return response()->json($pokemons);
    }

    // CREATE: Salva um novo Pokémon
    public function store(Request $request)
    {
        // Validação completa para todos os campos
        $validator = Validator::make($request->all(), [
            'number'      => 'required|integer|unique:pokemons,number',
            'name'        => 'required|string|max:255',
            'type1'       => 'required|string|max:50',
            'type2'       => 'nullable|string|max:50', // Permite que seja nulo ou uma string
            'height'      => 'required|numeric|min:0',
            'weight'      => 'required|numeric|min:0',
            'description' => 'required|string',
            'image_url'   => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422); // 422 é mais específico para falha de validação
        }

        $pokemon = Pokemon::create($request->all());
        return response()->json($pokemon, 201);
    }

    // UPDATE: Atualiza um Pokémon existente
    public function update(Request $request, Pokemon $pokemon)
    {
        $validator = Validator::make($request->all(), [
            'number'      => 'required|integer|unique:pokemons,number,' . $pokemon->id,
            'name'        => 'required|string|max:255',
            'type1'       => 'required|string|max:50',
            'type2'       => 'nullable|string|max:50',
            'height'      => 'required|numeric|min:0',
            'weight'      => 'required|numeric|min:0',
            'description' => 'required|string',
            'image_url'   => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $pokemon->update($request->all());
        return response()->json($pokemon);
    }

    // DELETE: Remove um Pokémon
    public function destroy(Pokemon $pokemon)
    {
        $pokemon->delete();
        return response()->json(null, 204);
    }

    // READ (SINGLE): Retorna um único Pokémon
    public function show(Pokemon $pokemon)
    {
        return response()->json($pokemon);
    }
}
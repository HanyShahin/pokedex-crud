<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PokemonController extends Controller
{
    /**
     * READ: Retorna os Pokémon APENAS do usuário logado.
     */
    public function index()
    {
        $pokemons = Pokemon::where('user_id', Auth::id())
                           ->orderBy('number', 'asc')
                           ->get();

        return response()->json($pokemons);
    }

    /**
     * CREATE: Salva um novo Pokémon associado ao usuário logado.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'number'      => 'required|integer',
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

        $data = $request->all();
        $data['user_id'] = Auth::id(); // Adiciona o ID do usuário logado

        $pokemon = Pokemon::create($data); // Cria o Pokémon com o user_id

        return response()->json($pokemon, 201);
    }

    /**
     * READ (SINGLE): Retorna um único Pokémon, verificando se ele pertence ao usuário.
     */
    public function show(Pokemon $pokemon)
    {
        if ($pokemon->user_id !== Auth::id()) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        return response()->json($pokemon);
    }


    /**
     * UPDATE: Atualiza um Pokémon, verificando se ele pertence ao usuário.
     */
    public function update(Request $request, Pokemon $pokemon)
    {
        if ($pokemon->user_id !== Auth::id()) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'number'      => 'required|integer',
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

    /**
     * DELETE: Remove um Pokémon, verificando se ele pertence ao usuário.
     */
    public function destroy(Pokemon $pokemon)
    {
        if ($pokemon->user_id !== Auth::id()) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        $pokemon->delete();

        return response()->json(null, 204);
    }
}
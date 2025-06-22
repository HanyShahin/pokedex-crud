<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule; // Importante para a validação avançada

class PokemonController extends Controller
{
    /**
     * READ: Retorna os Pokémon do usuário logado.
     */
    public function index()
    {
        $pokemons = Pokemon::where('user_id', Auth::id())
                           ->orderBy('number', 'asc')
                           ->get();

        return response()->json($pokemons);
    }

    /**
     * CREATE: Salva um novo Pokémon.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'number' => [
                'required',
                'integer',
                // A regra agora é: o número deve ser único na tabela 'pokemons'
                // ONDE a coluna 'user_id' for igual ao ID do usuário logado.
                Rule::unique('pokemons')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                }),
            ],
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
        $data['user_id'] = Auth::id();

        $pokemon = Pokemon::create($data);

        return response()->json($pokemon, 201);
    }

    /**
     * READ (SINGLE): Mostra um Pokémon específico.
     */
    public function show(Pokemon $pokemon)
    {
        if ($pokemon->user_id !== Auth::id()) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        return response()->json($pokemon);
    }

    /**
     * UPDATE: Atualiza um Pokémon existente.
     */
    public function update(Request $request, Pokemon $pokemon)
    {
        if ($pokemon->user_id !== Auth::id()) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'number' => [
                'required',
                'integer',
                // A mesma regra de antes, mas ignorando o ID do próprio Pokémon que estamos editando
                Rule::unique('pokemons')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                })->ignore($pokemon->id),
            ],
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
     * DELETE: Remove um Pokémon.
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
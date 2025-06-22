<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pokemons', function (Blueprint $table) {
            // 1. Remove a regra antiga que só olhava para a coluna 'number'
            $table->dropUnique('pokemons_number_unique');

            // 2. Cria uma nova regra única para a COMBINAÇÃO de 'user_id' e 'number'
            $table->unique(['user_id', 'number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pokemons', function (Blueprint $table) {
            // Desfaz as alterações
            $table->dropUnique(['user_id', 'number']);
            $table->unique('number', 'pokemons_number_unique');
        });
    }
};

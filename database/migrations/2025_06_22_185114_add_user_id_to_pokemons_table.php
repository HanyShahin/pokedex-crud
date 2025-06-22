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
            // Adiciona a coluna 'user_id' depois da coluna 'id'
            // e a conecta com a tabela 'users'.
            $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pokemons', function (Blueprint $table) {
            // Este método desfaz o que o 'up' faz, caso necessário
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};

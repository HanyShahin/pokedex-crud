<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx..._create_pokemons_table.php
    public function up(): void
    {
        Schema::create('pokemons', function (Blueprint $table) {
            $table->id();
            $table->integer('number')->unique();
            $table->string('name');
            $table->string('type1');
            $table->string('type2')->nullable();
            $table->decimal('height', 5, 2);
            $table->decimal('weight', 6, 2);
            $table->text('description');
            $table->string('image_url');
            $table->timestamps(); // Cria created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pokemon');
    }
};

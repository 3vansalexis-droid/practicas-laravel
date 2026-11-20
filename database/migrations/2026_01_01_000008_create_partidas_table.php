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
        Schema::create('partidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jugador_x_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('jugador_o_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('turno_actual', 1)->default('X');
            $table->json('tablero');
            $table->string('estado')->default('esperando');
            $table->foreignId('ganador_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('es_empate')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partidas');
    }
};

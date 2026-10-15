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
        Schema::create('blocage_utilisateurs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('bloque_par')
                  ->constrained('utilisateurs')
                  ->cascadeOnDelete();

            $table->foreignId('utilisateur_bloque')
                  ->constrained('utilisateurs')
                  ->cascadeOnDelete();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocage_utilisateurs');
    }
};

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
                Schema::create('signalements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('signale_par')
                  ->constrained('utilisateurs')
                  ->cascadeOnDelete();

            // Objet signalé : annonce, utilisateur, etc.
            $table->morphs('signalable');

            $table->enum('motif', [
                'suspecte',
                'trompeuse',
                'photos_inappropriees',
                'comportement_abusif',
                'spam',
                'autre',
            ]);

            $table->text('details')->nullable();

            $table->enum('statut', [
                'en_attente',
                'en_examen',
                'resolu',
                'rejete',
            ])->default('en_attente');

            $table->foreignId('traite_par')
                  ->nullable()
                  ->constrained('utilisateurs')
                  ->nullOnDelete();

            $table->timestamp('traite_le')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signalements');
    }
};

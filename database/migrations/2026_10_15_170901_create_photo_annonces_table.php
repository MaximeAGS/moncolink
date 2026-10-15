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
          Schema::create('photo_annonces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('annonce_id')
                  ->constrained('annonces')
                  ->cascadeOnDelete();

            $table->string('chemin');
            $table->string('nom_original')->nullable();
            $table->string('type_mime')->nullable();
            $table->unsignedBigInteger('taille')->nullable();

            $table->boolean('est_principale')->default(false);
            $table->unsignedSmallInteger('ordre')->default(0);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photo_annonces');
    }
};

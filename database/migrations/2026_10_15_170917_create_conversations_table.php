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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('annonce_id')
                  ->nullable()
                  ->constrained('annonces')
                  ->nullOnDelete();

            $table->foreignId('initiateur_id')
                  ->constrained('utilisateurs')
                  ->cascadeOnDelete();

            $table->foreignId('destinataire_id')
                  ->constrained('utilisateurs')
                  ->cascadeOnDelete();

            $table->timestamp('dernier_message_le')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};

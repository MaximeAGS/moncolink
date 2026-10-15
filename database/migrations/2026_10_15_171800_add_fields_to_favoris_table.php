<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('favoris', function (Blueprint $table) {
            $table->foreignId('utilisateur_id')->constrained('utilisateurs')->cascadeOnDelete();
            $table->foreignId('annonce_id')->constrained('annonces')->cascadeOnDelete();
            $table->unique(['utilisateur_id', 'annonce_id']);
        });
    }

    public function down(): void
    {
        Schema::table('favoris', function (Blueprint $table) {
            $table->dropUnique(['utilisateur_id', 'annonce_id']);
            $table->dropConstrainedForeignId('utilisateur_id');
            $table->dropConstrainedForeignId('annonce_id');
        });
    }
};

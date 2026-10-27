<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->text('contenu')->nullable()->change();
            $table->string('fichier_chemin')->nullable();
            $table->string('fichier_nom')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['fichier_chemin', 'fichier_nom']);
            $table->text('contenu')->nullable(false)->change();
        });
    }
};

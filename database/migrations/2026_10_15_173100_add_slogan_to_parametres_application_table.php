<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parametres_application', function (Blueprint $table) {
            $table->string('slogan', 160)
                ->default('Un chez-vous, à plusieurs.')
                ->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('parametres_application', function (Blueprint $table) {
            $table->dropColumn('slogan');
        });
    }
};

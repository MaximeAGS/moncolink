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
        Schema::create('utilisateurs', function (Blueprint $table) {
            $table->id();

            // Identité
            $table->string('prenom');
            $table->string('nom');

            // Coordonnées
            $table->string('email')->unique();
            $table->string('telephone')->nullable()->unique();
            $table->timestamp('email_verifie_le')->nullable();
            $table->timestamp('telephone_verifie_le')->nullable();
            $table->string('mot_de_passe');

            // Profil
            $table->string('photo_profil')->nullable();
            $table->text('biographie')->nullable();
            $table->string('ville')->nullable();
            $table->string('commune')->nullable();
            $table->string('quartier')->nullable();
            $table->string('profession')->nullable();
            $table->boolean('est_etudiant')->default(false);

            // Confidentialité du téléphone
            $table->enum('visibilite_telephone', ['prive', 'public'])->default('prive');

            // Statut du compte (indépendant des rôles Spatie)
            $table->enum('statut', ['actif', 'suspendu', 'desactive'])->default('actif');

            $table->timestamp('derniere_connexion_le')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('utilisateurs');
    }

};

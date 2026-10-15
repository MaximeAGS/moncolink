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
              Schema::create('annonces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilisateur_id')
                  ->constrained('utilisateurs')
                  ->cascadeOnDelete();

            // Type d'annonce : offre ou recherche
            $table->enum('type', ['offre', 'recherche']);

            $table->string('titre');
            $table->text('description');

            // Localisation & type de bien
            $table->string('type_logement')->nullable();
            $table->string('ville')->nullable();
            $table->string('commune')->nullable();
            $table->string('quartier')->nullable();

            // Champs spécifiques à une offre
            $table->decimal('loyer_total', 10, 2)->nullable();
            $table->decimal('contribution_mensuelle', 10, 2)->nullable();
            $table->decimal('charges_supplementaires', 10, 2)->nullable();
            $table->date('disponible_a_partir_du')->nullable();
            $table->unsignedTinyInteger('capacite')->nullable();
            $table->json('equipements')->nullable();
            $table->text('conditions_particulieres')->nullable();

            // Champs spécifiques à une recherche
            $table->decimal('budget_max', 10, 2)->nullable();
            $table->date('date_emmenagement')->nullable();
            $table->string('type_logement_recherche')->nullable();
            $table->json('preferences_vie')->nullable();
            $table->text('informations_complementaires')->nullable();

            // Statut
            $table->enum('statut', [
                'en_attente',
                'publiee',
                'en_discussion',
                'pourvue',
                'desactivee',
                'refusee',
            ])->default('en_attente');

            $table->unsignedInteger('nombre_consultations')->default(0);
            $table->timestamp('publiee_le')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('annonces');
    }
};

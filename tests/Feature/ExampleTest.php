<?php

namespace Tests\Feature;

use App\Models\Annonce;
use App\Models\Utilisateur;
use App\Models\Ville;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_home_search_filters_published_annonces_by_city_type_and_budget(): void
    {
        $ville = Ville::create(['nom' => 'Cotonou']);
        $otherVille = Ville::create(['nom' => 'Parakou']);
        $owner = Utilisateur::create([
            'prenom' => 'Awa',
            'nom' => 'Mensah',
            'email' => 'home-search@example.com',
            'mot_de_passe' => 'Password@123',
        ]);

        Annonce::create([
            'utilisateur_id' => $owner->id,
            'ville_id' => $ville->id,
            'type' => 'offre',
            'titre' => 'Chambre dans le budget',
            'description' => 'Une chambre disponible.',
            'contribution_mensuelle' => 45000,
            'statut' => 'publiee',
        ]);

        Annonce::create([
            'utilisateur_id' => $owner->id,
            'ville_id' => $ville->id,
            'type' => 'offre',
            'titre' => 'Chambre hors budget',
            'description' => 'Une chambre plus chère.',
            'contribution_mensuelle' => 85000,
            'statut' => 'publiee',
        ]);

        Annonce::create([
            'utilisateur_id' => $owner->id,
            'ville_id' => $otherVille->id,
            'type' => 'offre',
            'titre' => 'Chambre autre ville',
            'description' => 'Une chambre à Parakou.',
            'contribution_mensuelle' => 35000,
            'statut' => 'publiee',
        ]);

        $this->get('/?'.http_build_query([
            'ville_id' => $ville->id,
            'type' => 'offre',
            'budget_max' => 50000,
        ]))
            ->assertOk()
            ->assertSee('Chambre dans le budget')
            ->assertDontSee('Chambre hors budget')
            ->assertDontSee('Chambre autre ville')
            ->assertSee('1 annonce');

        Annonce::create([
            'utilisateur_id' => $owner->id,
            'ville_id' => $ville->id,
            'type' => 'recherche',
            'titre' => 'Recherche avec montant mensuel',
            'description' => 'Budget indiqué dans le montant mensuel.',
            'contribution_mensuelle' => 30000,
            'statut' => 'publiee',
        ]);

        $this->get('/?'.http_build_query([
            'ville_id' => $ville->id,
            'type' => 'recherche',
            'budget_max' => 50000,
        ]))
            ->assertOk()
            ->assertSee('Recherche avec montant mensuel')
            ->assertSee('1 annonce');
    }
}

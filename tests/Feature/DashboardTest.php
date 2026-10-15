<?php

namespace Tests\Feature;

use App\Models\Annonce;
use App\Models\Commune;
use App\Models\Quartier;
use App\Models\User;
use App\Models\Utilisateur;
use App\Models\Ville;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_renders_for_a_breeze_user_without_colocation_relations(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Bonjour,')
            ->assertSee('Mes annonces')
            ->assertSee('Discussions')
            ->assertSee('Favoris');
    }

    public function test_dashboard_shows_annonces_for_a_colocation_user(): void
    {
        $ville = Ville::create(['nom' => 'Cotonou']);
        $commune = Commune::create(['ville_id' => $ville->id, 'nom' => 'Cotonou-centre']);
        $quartier = Quartier::create(['commune_id' => $commune->id, 'nom' => 'Dantokpa']);

        $user = Utilisateur::create([
            'prenom' => 'Awa',
            'nom' => 'Mensah',
            'email' => 'awa@example.com',
            'mot_de_passe' => 'Password@123',
            'ville_id' => $ville->id,
            'commune_id' => $commune->id,
            'quartier_id' => $quartier->id,
        ]);

        Annonce::create([
            'utilisateur_id' => $user->id,
            'type' => 'offre',
            'titre' => 'Chambre lumineuse',
            'description' => 'Une chambre près du centre.',
            'ville_id' => $ville->id,
            'commune_id' => $commune->id,
            'quartier_id' => $quartier->id,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Bonjour,')
            ->assertSee('Chambre lumineuse')
            ->assertSee('Dantokpa, Cotonou-centre, Cotonou');

        $this->assertSame($ville->id, $user->quartier->commune->ville->id);
        $this->assertDatabaseHas('utilisateurs', [
            'id' => $user->id,
            'ville_id' => $ville->id,
            'commune_id' => $commune->id,
            'quartier_id' => $quartier->id,
        ]);
    }
}

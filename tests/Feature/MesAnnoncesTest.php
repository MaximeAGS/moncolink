<?php

namespace Tests\Feature;

use App\Models\Annonce;
use App\Models\Utilisateur;
use App\Models\Ville;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MesAnnoncesTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_create_an_announcement_for_moderation(): void
    {
        $member = $this->createUtilisateur('listing-owner@example.com');
        $ville = Ville::create(['nom' => 'Cotonou']);

        $this->actingAs($member)
            ->post(route('mes-annonces.store'), [
                'type' => 'offre',
                'titre' => 'Chambre près du campus',
                'description' => 'Chambre disponible dans un appartement calme.',
                'ville_id' => $ville->id,
                'contribution_mensuelle' => 45000,
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('annonces', [
            'utilisateur_id' => $member->id,
            'titre' => 'Chambre près du campus',
            'statut' => 'en_attente',
        ]);
    }

    public function test_member_can_edit_and_delete_only_their_own_announcements(): void
    {
        $owner = $this->createUtilisateur('listing-owner@example.com');
        $otherMember = $this->createUtilisateur('other-member@example.com');
        $ownedAnnonce = $this->createAnnonce($owner, 'Mon annonce');
        $otherAnnonce = $this->createAnnonce($otherMember, 'Annonce d’un autre membre');

        $this->actingAs($owner)
            ->get(route('mes-annonces.edit', $ownedAnnonce))
            ->assertOk()
            ->assertSee('Modifier mon annonce');

        $this->put(route('mes-annonces.update', $ownedAnnonce), [
            'type' => 'offre',
            'titre' => 'Mon annonce modifiée',
            'description' => 'Description mise à jour.',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('annonces', [
            'id' => $ownedAnnonce->id,
            'titre' => 'Mon annonce modifiée',
            'statut' => 'en_attente',
        ]);

        $this->get(route('mes-annonces.edit', $otherAnnonce))->assertNotFound();
        $this->delete(route('mes-annonces.destroy', $otherAnnonce))->assertNotFound();

        $this->delete(route('mes-annonces.destroy', $ownedAnnonce))->assertRedirect(route('dashboard'));
        $this->assertSoftDeleted('annonces', ['id' => $ownedAnnonce->id]);
    }

    public function test_member_dashboard_exposes_listing_actions(): void
    {
        $member = $this->createUtilisateur('dashboard-listing@example.com');
        $annonce = $this->createAnnonce($member, 'Annonce du tableau de bord');

        $this->actingAs($member)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Publier une annonce')
            ->assertSee('Modifier')
            ->assertSee(route('mes-annonces.edit', $annonce), false)
            ->assertSee(route('mes-annonces.destroy', $annonce), false);
    }

    private function createUtilisateur(string $email): Utilisateur
    {
        return Utilisateur::create([
            'prenom' => 'Test',
            'nom' => 'Membre',
            'email' => $email,
            'mot_de_passe' => 'Password@123',
        ]);
    }

    private function createAnnonce(Utilisateur $owner, string $title): Annonce
    {
        return Annonce::create([
            'utilisateur_id' => $owner->id,
            'type' => 'offre',
            'titre' => $title,
            'description' => 'Description de test.',
            'statut' => 'publiee',
        ]);
    }
}

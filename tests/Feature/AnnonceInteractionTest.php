<?php

namespace Tests\Feature;

use App\Models\Annonce;
use App\Models\Signalement;
use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnonceInteractionTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_block_and_unblock_an_announcement_author(): void
    {
        $owner = $this->createUtilisateur('blocked-owner@example.com');
        $member = $this->createUtilisateur('blocker@example.com');
        $annonce = $this->createAnnonce($owner, 'Annonce à masquer');

        $this->actingAs($member)
            ->get('/')
            ->assertSee('Annonce à masquer');

        $this->post(route('utilisateurs.blocages.store', $owner))
            ->assertRedirect();

        $this->assertDatabaseHas('blocage_utilisateurs', [
            'bloque_par' => $member->id,
            'utilisateur_bloque' => $owner->id,
        ]);

        $this->get('/')
            ->assertDontSee('Annonce à masquer')
            ->assertDontSee('Bloquer ce membre');

        $this->get(route('membres-bloques.index'))
            ->assertOk()
            ->assertSee('Membres bloqués')
            ->assertSee('Débloquer');

        $this->delete(route('utilisateurs.blocages.destroy', $owner))
            ->assertRedirect();

        $this->assertDatabaseMissing('blocage_utilisateurs', [
            'bloque_par' => $member->id,
            'utilisateur_bloque' => $owner->id,
        ]);

        $this->get('/')->assertSee('Annonce à masquer');
    }

    public function test_member_can_report_an_announcement_only_once_while_its_report_is_open(): void
    {
        $owner = $this->createUtilisateur('reported-owner@example.com');
        $member = $this->createUtilisateur('reporter@example.com');
        $annonce = $this->createAnnonce($owner, 'Annonce signalable');

        $payload = [
            'motif' => 'trompeuse',
            'details' => 'Les informations ne correspondent pas.',
        ];

        $this->actingAs($member)
            ->post(route('annonces.signalements.store', $annonce), $payload)
            ->assertRedirect();

        $this->assertDatabaseHas('signalements', [
            'signale_par' => $member->id,
            'signalable_type' => Annonce::class,
            'signalable_id' => $annonce->id,
            'motif' => 'trompeuse',
        ]);

        $this->post(route('annonces.signalements.store', $annonce), [
            'motif' => 'spam',
        ])->assertRedirect();

        $this->assertSame(1, Signalement::query()->count());
        $this->get('/')->assertSee('Signalement envoyé');
    }

    public function test_member_cannot_block_themselves_or_report_their_own_announcement(): void
    {
        $member = $this->createUtilisateur('self-interaction@example.com');
        $annonce = $this->createAnnonce($member, 'Ma propre annonce');

        $this->actingAs($member)
            ->post(route('utilisateurs.blocages.store', $member))
            ->assertForbidden();

        $this->post(route('annonces.signalements.store', $annonce), [
            'motif' => 'autre',
        ])->assertForbidden();

        $this->assertDatabaseCount('blocage_utilisateurs', 0);
        $this->assertDatabaseCount('signalements', 0);
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
            'description' => 'Une chambre disponible.',
            'statut' => 'publiee',
        ]);
    }
}

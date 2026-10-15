<?php

namespace Tests\Feature;

use App\Models\Annonce;
use App\Models\Commune;
use App\Models\ParametreApplication;
use App\Models\Utilisateur;
use App\Models\Ville;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminResourcesTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_can_open_each_resource_and_create_a_favorite(): void
    {
        $role = Role::create(['name' => 'administrateur', 'guard_name' => 'web']);
        $administrator = $this->createUtilisateur('admin@example.com');
        $administrator->assignRole($role);
        $owner = $this->createUtilisateur('owner@example.com');

        $this->actingAs($administrator);

        foreach ([
            'admin.annonces.index',
            'admin.utilisateurs.index',
            'admin.users.index',
            'admin.photos-annonces.index',
            'admin.conversations.index',
            'admin.messages.index',
            'admin.signalements.index',
            'admin.favoris.index',
            'admin.notifications.index',
            'admin.blocages.index',
        ] as $route) {
            $this->get(route($route))->assertOk();
        }

        $annonce = Annonce::create([
            'utilisateur_id' => $owner->id,
            'type' => 'offre',
            'titre' => 'Chambre disponible',
            'description' => 'Une chambre à partager.',
        ]);

        $this->post(route('admin.favoris.store'), [
            'utilisateur_id' => $owner->id,
            'annonce_id' => $annonce->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('favoris', [
            'utilisateur_id' => $owner->id,
            'annonce_id' => $annonce->id,
        ]);
    }

    public function test_regular_user_cannot_access_administration(): void
    {
        $user = $this->createUtilisateur('user@example.com');

        $this->actingAs($user)
            ->get(route('admin.annonces.index'))
            ->assertForbidden();
    }

    public function test_administrator_can_view_the_admin_dashboard(): void
    {
        $role = Role::create(['name' => 'administrateur', 'guard_name' => 'web']);
        $administrator = $this->createUtilisateur('dashboard-admin@example.com');
        $administrator->assignRole($role);

        $this->actingAs($administrator)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Vue d’ensemble')
            ->assertSee('Annonces récentes')
            ->assertSee('Signalements');
    }

    public function test_administrator_can_change_project_name_description_and_logo(): void
    {
        Storage::fake('public');

        $role = Role::create(['name' => 'administrateur', 'guard_name' => 'web']);
        $administrator = $this->createUtilisateur('branding-admin@example.com');
        $administrator->assignRole($role);

        $this->actingAs($administrator)
            ->get(route('admin.parametres.edit'))
            ->assertOk()
            ->assertSee('Paramètres généraux')
            ->assertSee('Logo');

        $this->actingAs($administrator)
            ->put(route('admin.parametres.update'), [
                'nom' => 'Maison Partagée',
                'description' => 'Un lieu pour vivre ensemble.',
                'slogan' => 'La coloc qui vous ressemble.',
                'logo' => UploadedFile::fake()->image('logo.png'),
            ])
            ->assertRedirect(route('admin.parametres.edit'))
            ->assertSessionHasNoErrors();

        $parametres = ParametreApplication::findOrFail(1);
        $this->assertSame('Maison Partagée', $parametres->nom);
        $this->assertSame('Un lieu pour vivre ensemble.', $parametres->description);
        $this->assertSame('La coloc qui vous ressemble.', $parametres->slogan);
        Storage::disk('public')->assertExists($parametres->logo);

        $this->post(route('logout'));
        $this->get('/')
            ->assertOk()
            ->assertSee('Maison Partagée', false)
            ->assertSee('Un lieu pour vivre ensemble.', false)
            ->assertSee('La coloc qui vous ressemble.', false)
            ->assertSee('brand-logo-image', false);

        $this->actingAs($administrator)
            ->put(route('admin.parametres.update'), [
                'nom' => 'Maison Partagée',
                'description' => 'Un lieu pour vivre ensemble.',
                'slogan' => 'La coloc qui vous ressemble.',
                'supprimer_logo' => 1,
            ])
            ->assertRedirect(route('admin.parametres.edit'));

        Storage::disk('public')->assertMissing($parametres->logo);
        $this->get('/')->assertDontSee('brand-logo-image', false);
    }

    public function test_admin_cannot_assign_a_commune_from_another_city(): void
    {
        $role = Role::create(['name' => 'administrateur', 'guard_name' => 'web']);
        $administrator = $this->createUtilisateur('admin@example.com');
        $administrator->assignRole($role);
        $owner = $this->createUtilisateur('owner@example.com');
        $selectedCity = Ville::create(['nom' => 'Cotonou']);
        $otherCity = Ville::create(['nom' => 'Porto-Novo']);
        $commune = Commune::create([
            'ville_id' => $otherCity->id,
            'nom' => 'Avassa',
        ]);

        $this->actingAs($administrator)
            ->from(route('admin.annonces.create'))
            ->post(route('admin.annonces.store'), [
                'utilisateur_id' => $owner->id,
                'type' => 'offre',
                'titre' => 'Chambre disponible',
                'description' => 'Une chambre à partager.',
                'ville_id' => $selectedCity->id,
                'commune_id' => $commune->id,
                'statut' => 'en_attente',
                'nombre_consultations' => 0,
            ])
            ->assertSessionHasErrors('commune_id');
    }

    private function createUtilisateur(string $email): Utilisateur
    {
        return Utilisateur::create([
            'prenom' => 'Test',
            'nom' => 'Utilisateur',
            'email' => $email,
            'mot_de_passe' => 'Password@123',
        ]);
    }
}

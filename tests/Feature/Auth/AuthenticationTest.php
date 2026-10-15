<?php

namespace Tests\Feature\Auth;

use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = $this->createUtilisateur('login@example.com');

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_administrators_are_redirected_to_the_admin_dashboard(): void
    {
        $role = Role::create(['name' => 'administrateur', 'guard_name' => 'web']);
        $user = $this->createUtilisateur('admin-login@example.com');
        $user->assignRole($role);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = $this->createUtilisateur('invalid@example.com');

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = $this->createUtilisateur('logout@example.com');

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    private function createUtilisateur(string $email): Utilisateur
    {
        return Utilisateur::create([
            'prenom' => 'Test',
            'nom' => 'Utilisateur',
            'email' => $email,
            'mot_de_passe' => 'password',
        ]);
    }
}

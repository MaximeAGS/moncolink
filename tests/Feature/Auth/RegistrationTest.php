<?php

namespace Tests\Feature\Auth;

use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'prenom' => 'Test',
            'nom' => 'Utilisateur',
            'email' => 'test@example.com',
            'telephone' => '97000003',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $this->assertDatabaseHas('utilisateurs', [
            'email' => 'test@example.com',
            'prenom' => 'Test',
            'nom' => 'Utilisateur',
        ]);
        $this->assertInstanceOf(Utilisateur::class, auth()->user());
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}

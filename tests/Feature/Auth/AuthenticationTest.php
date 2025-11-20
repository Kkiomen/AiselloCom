<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Testy autoryzacji użytkowników.
 * User authentication tests.
 *
 * Testuje proces logowania i wylogowania użytkowników.
 * Tests the login and logout process for users.
 */
class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test wyświetlania ekranu logowania.
     * Test that the login screen can be rendered.
     */
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    /**
     * Test poprawnego logowania użytkownika.
     * Test that users can authenticate using the login screen.
     */
    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    /**
     * Test nieudanego logowania z błędnym hasłem.
     * Test that users cannot authenticate with invalid password.
     */
    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    /**
     * Test nieudanego logowania z nieistniejącym emailem.
     * Test that users cannot authenticate with non-existent email.
     */
    public function test_users_can_not_authenticate_with_non_existent_email(): void
    {
        $response = $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    /**
     * Test poprawnego wylogowania użytkownika.
     * Test that users can logout.
     */
    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    /**
     * Test funkcji "Zapamiętaj mnie".
     * Test remember me functionality.
     */
    public function test_users_can_login_with_remember_me(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
            'remember' => 'on',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        // Sprawdź czy token "remember" został ustawiony
        // Check if remember token was set
        $user->refresh();
        $this->assertNotNull($user->remember_token);
    }

    /**
     * Test walidacji wymaganego pola email.
     * Test that email field is required for login.
     */
    public function test_login_requires_email(): void
    {
        $response = $this->post('/login', [
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Test walidacji wymaganego pola hasło.
     * Test that password field is required for login.
     */
    public function test_login_requires_password(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    /**
     * Test przekierowania niezalogowanego użytkownika z chronionych stron.
     * Test that unauthenticated users are redirected from protected pages.
     */
    public function test_unauthenticated_users_are_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    /**
     * Test dostępu zalogowanego użytkownika do pulpitu.
     * Test that authenticated users can access dashboard.
     */
    public function test_authenticated_users_can_access_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }
}

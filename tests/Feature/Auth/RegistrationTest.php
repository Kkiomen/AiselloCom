<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Testy rejestracji użytkowników.
 * User registration tests.
 *
 * Testuje proces rejestracji nowych użytkowników w systemie Aisello.
 * Tests the registration process for new users in the Aisello system.
 */
class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test wyświetlania ekranu rejestracji.
     * Test that the registration screen can be rendered.
     */
    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    /**
     * Test poprawnej rejestracji nowego użytkownika.
     * Test that new users can register successfully.
     */
    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        // Sprawdź czy użytkownik został utworzony w bazie
        // Check if user was created in database
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }

    /**
     * Test rejestracji użytkownika z nazwą firmy.
     * Test that users can register with company name.
     */
    public function test_users_can_register_with_company_name(): void
    {
        $response = $this->post('/register', [
            'name' => 'Jan Kowalski',
            'company_name' => 'Aisello Sp. z o.o.',
            'email' => 'jan@aisello.com',
            'password' => 'securePassword123',
            'password_confirmation' => 'securePassword123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        // Sprawdź czy użytkownik został utworzony z nazwą firmy
        // Check if user was created with company name
        $this->assertDatabaseHas('users', [
            'name' => 'Jan Kowalski',
            'company_name' => 'Aisello Sp. z o.o.',
            'email' => 'jan@aisello.com',
        ]);
    }

    /**
     * Test rejestracji użytkownika bez nazwy firmy.
     * Test that users can register without company name.
     */
    public function test_users_can_register_without_company_name(): void
    {
        $response = $this->post('/register', [
            'name' => 'Anna Nowak',
            'email' => 'anna@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();

        // Sprawdź czy użytkownik ma null jako company_name
        // Check if user has null as company_name
        $user = User::where('email', 'anna@example.com')->first();
        $this->assertNull($user->company_name);
    }

    /**
     * Test walidacji wymaganego pola name.
     * Test that name field is required.
     */
    public function test_registration_requires_name(): void
    {
        $response = $this->post('/register', [
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertGuest();
    }

    /**
     * Test walidacji wymaganego pola email.
     * Test that email field is required.
     */
    public function test_registration_requires_email(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Test walidacji unikalności email.
     * Test that email must be unique.
     */
    public function test_registration_email_must_be_unique(): void
    {
        // Utwórz istniejącego użytkownika / Create existing user
        User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Test walidacji potwierdzenia hasła.
     * Test that password confirmation must match.
     */
    public function test_registration_password_must_be_confirmed(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    /**
     * Test walidacji minimalnej długości hasła.
     * Test that password must meet minimum length requirement.
     */
    public function test_registration_password_must_meet_minimum_length(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    /**
     * Test poprawnego formatu email.
     * Test that email must be valid format.
     */
    public function test_registration_email_must_be_valid(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}

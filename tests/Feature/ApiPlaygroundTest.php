<?php

namespace Tests\Feature;

use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Testy funkcjonalne dla API Playground
 * Feature tests for API Playground
 *
 * Testuje funkcjonalność interaktywnego testowania API
 * Tests interactive API testing functionality
 */
class ApiPlaygroundTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private ApiKey $apiKey;

    /**
     * Przygotuj dane testowe przed każdym testem
     * Set up test data before each test
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Utwórz użytkownika testowego
        // Create test user
        $this->user = User::factory()->create([
            'is_active' => true,
            'api_rate_limit' => 1000,
        ]);

        // Utwórz klucz API
        // Create API key
        $rawKey = 'aic_' . Str::random(60);
        $this->apiKey = $this->user->apiKeys()->create([
            'name' => 'Test API Key',
            'key' => hash('sha256', $rawKey),
            'is_active' => true,
            'expires_at' => null,
        ]);
    }

    /**
     * Test: Wyświetlanie strony playground
     * Test: Display playground page
     */
    public function test_displays_playground_page_for_authenticated_user(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('api.playground', ['slug' => 'product-description']));

        $response->assertStatus(200);
        $response->assertViewIs('api-playground.show');
        $response->assertViewHas('apiConfig');
        $response->assertViewHas('apiKeys');
        $response->assertSee('Product Description Generator');
    }

    /**
     * Test: Brak dostępu dla niezalogowanych użytkowników
     * Test: No access for unauthenticated users
     */
    public function test_redirects_unauthenticated_users(): void
    {
        $response = $this->get(route('api.playground', ['slug' => 'product-description']));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test: 404 dla nieistniejącego API
     * Test: 404 for non-existent API
     */
    public function test_returns_404_for_invalid_api_slug(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('api.playground', ['slug' => 'non-existent-api']));

        $response->assertStatus(404);
    }

    /**
     * Test: Brak API key ID w request
     * Test: Missing API key ID in request
     */
    public function test_fails_when_api_key_id_is_missing(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('api.playground.execute', ['slug' => 'product-description']), [
                'product_name' => 'Test Product',
            ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
        ]);
    }

    /**
     * Test: Nieważny API key ID
     * Test: Invalid API key ID
     */
    public function test_fails_with_invalid_api_key(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('api.playground.execute', ['slug' => 'product-description']), [
                'api_key_id' => 99999,
                'product_name' => 'Test Product',
            ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
        ]);
    }

    /**
     * Test: Nieaktywny API key
     * Test: Inactive API key
     */
    public function test_fails_with_inactive_api_key(): void
    {
        $this->apiKey->update(['is_active' => false]);

        $response = $this->actingAs($this->user)
            ->postJson(route('api.playground.execute', ['slug' => 'product-description']), [
                'api_key_id' => $this->apiKey->id,
                'product_name' => 'Test Product',
            ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
        ]);
    }

    /**
     * Test: Wygasły API key
     * Test: Expired API key
     */
    public function test_fails_with_expired_api_key(): void
    {
        $this->apiKey->update(['expires_at' => now()->subDay()]);

        $response = $this->actingAs($this->user)
            ->postJson(route('api.playground.execute', ['slug' => 'product-description']), [
                'api_key_id' => $this->apiKey->id,
                'product_name' => 'Test Product',
            ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
        ]);
    }

    /**
     * Test: Nie można użyć klucza innego użytkownika
     * Test: Cannot use another user API key
     */
    public function test_cannot_use_another_users_api_key(): void
    {
        $otherUser = User::factory()->create(['is_active' => true]);
        $rawKey = 'aic_' . Str::random(60);
        $otherApiKey = $otherUser->apiKeys()->create([
            'name' => 'Other User Key',
            'key' => hash('sha256', $rawKey),
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson(route('api.playground.execute', ['slug' => 'product-description']), [
                'api_key_id' => $otherApiKey->id,
                'product_name' => 'Test Product',
            ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
        ]);
    }
}

<?php

namespace Tests\Feature\Api;

use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductDescriptionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected string $apiKey;

    protected function setUp(): void
    {
        parent::setUp();

        // Utwórz użytkownika testowego
        $this->user = User::factory()->create([
            'api_rate_limit' => 100,
            'is_active' => true,
        ]);

        // Utwórz klucz API
        $rawKey = 'test_api_key_' . uniqid();
        $this->apiKey = $rawKey;

        ApiKey::create([
            'user_id' => $this->user->id,
            'key' => hash('sha256', $rawKey),
            'name' => 'Test Key',
            'is_active' => true,
        ]);
    }

    /**
     * Test generowania opisu produktu bez autentykacji.
     */
    public function test_generate_description_without_auth(): void
    {
        $response = $this->postJson('/api/v1/products/generate-description', [
            'name' => 'Test Product',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test generowania opisu z nieprawidłowym kluczem API.
     */
    public function test_generate_description_with_invalid_key(): void
    {
        $response = $this->postJson('/api/v1/products/generate-description', [
            'name' => 'Test Product',
        ], [
            'Authorization' => 'Bearer invalid_key',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test walidacji - brak wymaganych danych.
     */
    public function test_generate_description_validation(): void
    {
        $response = $this->postJson('/api/v1/products/generate-description', [
            // Puste dane - wszystkie pola opcjonalne, więc to powinno przejść
            'price' => 'invalid', // Ale to powinno failować walidację
        ], [
            'Authorization' => 'Bearer ' . $this->apiKey,
        ]);

        $response->assertStatus(422); // Validation error
    }

    /**
     * Test pobierania listy opisów.
     */
    public function test_get_descriptions_list(): void
    {
        $response = $this->getJson('/api/v1/products/descriptions', [
            'Authorization' => 'Bearer ' . $this->apiKey,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'meta' => [
                    'current_page',
                    'total',
                    'per_page',
                ],
            ]);
    }
}

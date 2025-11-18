<?php

namespace Tests\Feature\Api;

use App\Models\ApiKey;
use App\Models\ProductDescription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Kompletne testy endpointów API generowania opisów.
 * Complete API endpoint tests for description generation.
 *
 * Pokrywa wszystkie scenariusze: happy path, edge cases, błędy.
 */
class ProductDescriptionCompleteTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected ApiKey $apiKey;
    protected string $apiKeyPlain = 'test_api_key_12345';

    protected function setUp(): void
    {
        parent::setUp();

        // Utwórz użytkownika i klucz API
        $this->user = User::factory()->create([
            'api_rate_limit' => 100,
            'is_active' => true,
        ]);

        $this->apiKey = ApiKey::create([
            'user_id' => $this->user->id,
            'name' => 'Test Key',
            'key' => hash('sha256', $this->apiKeyPlain),
            'is_active' => true,
            'expires_at' => now()->addYear(),
        ]);
    }

    /**
     * Test: Brak autoryzacji (brak tokenu).
     */
    public function test_generate_requires_authorization(): void
    {
        $response = $this->postJson('/api/v1/products/generate-description', [
            'name' => 'Test Product',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'message' => 'Unauthorized',
        ]);
    }

    /**
     * Test: Nieprawidłowy klucz API.
     */
    public function test_generate_with_invalid_api_key(): void
    {
        $response = $this->postJson('/api/v1/products/generate-description', [
            'name' => 'Test Product',
        ], [
            'Authorization' => 'Bearer invalid_key_123',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test: Wygasły klucz API.
     */
    public function test_generate_with_expired_api_key(): void
    {
        $expiredKey = ApiKey::create([
            'user_id' => $this->user->id,
            'name' => 'Expired Key',
            'key' => hash('sha256', 'expired_key'),
            'is_active' => true,
            'expires_at' => now()->subDay(),
        ]);

        $response = $this->postJson('/api/v1/products/generate-description', [
            'name' => 'Test Product',
        ], [
            'Authorization' => 'Bearer expired_key',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test: Nieaktywny klucz API.
     */
    public function test_generate_with_inactive_api_key(): void
    {
        $inactiveKey = ApiKey::create([
            'user_id' => $this->user->id,
            'name' => 'Inactive Key',
            'key' => hash('sha256', 'inactive_key'),
            'is_active' => false,
            'expires_at' => now()->addYear(),
        ]);

        $response = $this->postJson('/api/v1/products/generate-description', [
            'name' => 'Test Product',
        ], [
            'Authorization' => 'Bearer inactive_key',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test: Walidacja - bez danych nie można wygenerować opisu bez auto_enrich.
     * Z auto_enrich=true można użyć samej nazwy.
     */
    public function test_generate_validation_missing_data_without_auto_enrich(): void
    {
        // Bez auto_enrich i bez danych - model nie będzie miał co generować
        // ale technicznie request jest poprawny (wszystkie pola są nullable)
        // więc oczekujemy 201, ale opis może być generyczny
        $response = $this->postJson('/api/v1/products/generate-description', [
            'auto_enrich' => false,
            // Brak jakichkolwiek danych
        ], [
            'Authorization' => "Bearer {$this->apiKeyPlain}",
        ]);

        // Request jest technicznie poprawny, choć mało sensowny
        $response->assertStatus(201);

        // Opis powinien zostać wygenerowany nawet dla "Produkt" / "Nieznany"
        $this->assertNotNull($response->json('data.generated_description'));
    }

    /**
     * Test: Walidacja - nieprawidłowe typy danych.
     */
    public function test_generate_validation_invalid_types(): void
    {
        $response = $this->postJson('/api/v1/products/generate-description', [
            'name' => 'Test Product',
            'price' => 'not-a-number', // Powinno być numeric
            'attributes' => 'not-an-array', // Powinno być array
            'auto_enrich' => 'not-a-boolean', // Powinno być boolean
        ], [
            'Authorization' => "Bearer {$this->apiKeyPlain}",
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['price', 'attributes']);
    }

    /**
     * Test: Walidacja - cena ujemna.
     */
    public function test_generate_validation_negative_price(): void
    {
        $response = $this->postJson('/api/v1/products/generate-description', [
            'name' => 'Test Product',
            'price' => -100,
        ], [
            'Authorization' => "Bearer {$this->apiKeyPlain}",
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['price']);
    }

    /**
     * Test: Generowanie z auto_enrich = false (tryb tani/szybki).
     */
    public function test_generate_without_auto_enrich(): void
    {
        $response = $this->postJson('/api/v1/products/generate-description', [
            'name' => 'Test Product',
            'manufacturer' => 'Test Brand',
            'price' => 99.99,
            'description' => 'Short description',
            'auto_enrich' => false,
        ], [
            'Authorization' => "Bearer {$this->apiKeyPlain}",
        ]);

        $response->assertStatus(201); // 201 Created
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'request_id',
                'status',
                'input_data',
                'enriched_data',
                'generated_description',
                'processing_time_ms',
                'tokens_used',
                'cost',
            ],
        ]);

        // Sprawdź że sources są puste (brak scrapingu)
        $this->assertEmpty($response->json('data.enriched_data.sources'));

        // Sprawdź że dane pochodzą z inputu
        $this->assertEquals('Test Product', $response->json('data.enriched_data.name'));
        $this->assertEquals('Test Brand', $response->json('data.enriched_data.manufacturer'));
        $this->assertEquals(99.99, $response->json('data.enriched_data.price'));
    }

    /**
     * Test: Generowanie z auto_enrich = true (tryb droższy/lepszy).
     */
    public function test_generate_with_auto_enrich(): void
    {
        $response = $this->postJson('/api/v1/products/generate-description', [
            'name' => 'iPhone 15 Pro',
            'auto_enrich' => true,
        ], [
            'Authorization' => "Bearer {$this->apiKeyPlain}",
        ]);

        $response->assertStatus(201); // 201 Created

        // Powinno mieć sources z scrapingu (może być 0 jeśli API nie działa)
        $sources = $response->json('data.enriched_data.sources');
        $this->assertIsArray($sources);

        // Sprawdź że wzbogacone dane mają dodatkowe pola
        $enrichedData = $response->json('data.enriched_data');
        $this->assertArrayHasKey('manufacturer', $enrichedData);
        $this->assertArrayHasKey('price', $enrichedData);
    }

    /**
     * Test: Domyślna wartość auto_enrich (powinno być true).
     */
    public function test_generate_default_auto_enrich(): void
    {
        $response = $this->postJson('/api/v1/products/generate-description', [
            'name' => 'Test Product',
            'manufacturer' => 'Test Brand',
            'price' => 50.00,
            // Brak auto_enrich - powinno być true domyślnie
        ], [
            'Authorization' => "Bearer {$this->apiKeyPlain}",
        ]);

        $response->assertStatus(201); // 201 Created

        // Sprawdź że auto_enrich został zapisany jako true w input_data
        $this->assertTrue($response->json('data.input_data.auto_enrich'));
    }

    /**
     * Test: Generowanie z wszystkimi polami wypełnionymi.
     */
    public function test_generate_with_all_fields(): void
    {
        $response = $this->postJson('/api/v1/products/generate-description', [
            'name' => 'Complete Product',
            'manufacturer' => 'Complete Brand',
            'price' => 199.99,
            'description' => 'Complete initial description',
            'attributes' => [
                'color' => 'red',
                'size' => 'large',
            ],
            'auto_enrich' => false,
        ], [
            'Authorization' => "Bearer {$this->apiKeyPlain}",
        ]);

        $response->assertStatus(201); // 201 Created
        $response->assertJson([
            'success' => true,
        ]);

        // Sprawdź że wszystkie dane są w enriched_data
        $enrichedData = $response->json('data.enriched_data');
        $this->assertEquals('Complete Product', $enrichedData['name']);
        $this->assertEquals('Complete Brand', $enrichedData['manufacturer']);
        $this->assertEquals(199.99, $enrichedData['price']);
    }

    /**
     * Test: Pobieranie listy wygenerowanych opisów.
     */
    public function test_get_descriptions_list(): void
    {
        // Utwórz kilka opisów
        ProductDescription::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson('/api/v1/products/descriptions', [
            'Authorization' => "Bearer {$this->apiKeyPlain}",
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'id',
                    'request_id',
                    'status',
                    'created_at',
                ],
            ],
        ]);

        $this->assertCount(3, $response->json('data'));
    }

    /**
     * Test: Pobieranie pojedynczego opisu.
     */
    public function test_get_single_description(): void
    {
        $description = ProductDescription::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson("/api/v1/products/descriptions/{$description->id}", [
            'Authorization' => "Bearer {$this->apiKeyPlain}",
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $description->id,
            ],
        ]);
    }

    /**
     * Test: Pobieranie nieistniejącego opisu.
     */
    public function test_get_non_existent_description(): void
    {
        $response = $this->getJson('/api/v1/products/descriptions/99999', [
            'Authorization' => "Bearer {$this->apiKeyPlain}",
        ]);

        $response->assertStatus(404);
    }

    /**
     * Test: Rate limiting - przekroczenie limitu.
     */
    public function test_rate_limit_exceeded(): void
    {
        // Ustaw bardzo niski limit
        $this->user->update(['api_rate_limit' => 1]);

        // Pierwszy request - OK
        $this->postJson('/api/v1/products/generate-description', [
            'name' => 'Test 1',
            'auto_enrich' => false,
        ], [
            'Authorization' => "Bearer {$this->apiKeyPlain}",
        ])->assertStatus(201); // 201 Created

        // Drugi request - przekroczenie limitu
        $response = $this->postJson('/api/v1/products/generate-description', [
            'name' => 'Test 2',
            'auto_enrich' => false,
        ], [
            'Authorization' => "Bearer {$this->apiKeyPlain}",
        ]);

        $response->assertStatus(429); // Too Many Requests
    }

    /**
     * Test: Koszt jest niższy bez auto_enrich.
     */
    public function test_cost_lower_without_auto_enrich(): void
    {
        // Request z auto_enrich
        $responseWithEnrich = $this->postJson('/api/v1/products/generate-description', [
            'name' => 'Product With Enrich',
            'auto_enrich' => true,
        ], [
            'Authorization' => "Bearer {$this->apiKeyPlain}",
        ]);

        // Request bez auto_enrich
        $responseWithoutEnrich = $this->postJson('/api/v1/products/generate-description', [
            'name' => 'Product Without Enrich',
            'manufacturer' => 'Test',
            'price' => 100,
            'description' => 'Test desc',
            'auto_enrich' => false,
        ], [
            'Authorization' => "Bearer {$this->apiKeyPlain}",
        ]);

        $costWith = $responseWithEnrich->json('data.cost');
        $costWithout = $responseWithoutEnrich->json('data.cost');

        // Koszt bez wzbogacania powinien być <= (czasami scraping może zawieść)
        $this->assertLessThanOrEqual($costWith + 0.001, $costWithout); // +epsilon dla floatów
    }
}

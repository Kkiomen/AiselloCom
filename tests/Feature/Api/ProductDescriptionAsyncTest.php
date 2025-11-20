<?php

namespace Tests\Feature\Api;

use App\Enums\ProductDescriptionStatus;
use App\Jobs\GenerateProductDescriptionJob;
use App\Models\ApiKey;
use App\Models\ProductDescription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

/**
 * Testy asynchronicznego generowania opisów produktów.
 * Tests for async product description generation.
 */
class ProductDescriptionAsyncTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected string $apiKey;
    protected ApiKey $apiKeyModel;

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

        $this->apiKeyModel = ApiKey::create([
            'user_id' => $this->user->id,
            'key' => hash('sha256', $rawKey),
            'name' => 'Test Key',
            'is_active' => true,
        ]);
    }

    /**
     * Test asynchronicznego generowania opisu produktu.
     * Tests async product description generation.
     */
    public function test_generate_description_async(): void
    {
        Queue::fake();

        $response = $this->postJson('/api/v1/products/generate-description-async', [
            'name' => 'Test Product',
            'manufacturer' => 'Test Manufacturer',
            'price' => 99.99,
            'external_product_id' => 'EXT-123',
        ], [
            'Authorization' => 'Bearer ' . $this->apiKey,
        ]);

        $response->assertStatus(202)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'request_id',
                    'external_product_id',
                    'status',
                    'status_label',
                ],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'external_product_id' => 'EXT-123',
                    'status' => 'pending',
                ],
            ]);

        // Sprawdź czy job został dodany do kolejki
        Queue::assertPushed(GenerateProductDescriptionJob::class);

        // Sprawdź czy rekord został utworzony w bazie
        $this->assertDatabaseHas('product_descriptions', [
            'user_id' => $this->user->id,
            'external_product_id' => 'EXT-123',
            'status' => ProductDescriptionStatus::PENDING->value,
        ]);
    }

    /**
     * Test asynchronicznego generowania bez autentykacji.
     * Tests async generation without authentication.
     */
    public function test_generate_async_without_auth(): void
    {
        $response = $this->postJson('/api/v1/products/generate-description-async', [
            'name' => 'Test Product',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test pobierania statusu asynchronicznego generowania.
     * Tests getting async generation status.
     */
    public function test_get_async_status(): void
    {
        // Utwórz rekord opisu
        $productDescription = ProductDescription::create([
            'user_id' => $this->user->id,
            'api_key_id' => $this->apiKeyModel->id,
            'request_id' => 'test-request-id-123',
            'external_product_id' => 'EXT-456',
            'input_data' => ['name' => 'Test'],
            'status' => ProductDescriptionStatus::PROCESSING,
        ]);

        $response = $this->getJson('/api/v1/products/async-status/test-request-id-123', [
            'Authorization' => 'Bearer ' . $this->apiKey,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'request_id',
                    'external_product_id',
                    'status',
                    'status_label',
                ],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'request_id' => 'test-request-id-123',
                    'external_product_id' => 'EXT-456',
                    'status' => 'processing',
                ],
            ]);
    }

    /**
     * Test pobierania statusu nieistniejącego requestu.
     * Tests getting status of non-existent request.
     */
    public function test_get_async_status_not_found(): void
    {
        $response = $this->getJson('/api/v1/products/async-status/non-existent-id', [
            'Authorization' => 'Bearer ' . $this->apiKey,
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Test pobierania opisów według external_product_id.
     * Tests getting descriptions by external product ID.
     */
    public function test_get_by_external_product_id(): void
    {
        // Utwórz kilka rekordów z tym samym external_product_id
        ProductDescription::create([
            'user_id' => $this->user->id,
            'api_key_id' => $this->apiKeyModel->id,
            'request_id' => 'req-1',
            'external_product_id' => 'EXT-789',
            'input_data' => ['name' => 'Test 1'],
            'status' => ProductDescriptionStatus::COMPLETED,
        ]);

        ProductDescription::create([
            'user_id' => $this->user->id,
            'api_key_id' => $this->apiKeyModel->id,
            'request_id' => 'req-2',
            'external_product_id' => 'EXT-789',
            'input_data' => ['name' => 'Test 2'],
            'status' => ProductDescriptionStatus::PENDING,
        ]);

        // Utwórz rekord z innym external_product_id
        ProductDescription::create([
            'user_id' => $this->user->id,
            'api_key_id' => $this->apiKeyModel->id,
            'request_id' => 'req-3',
            'external_product_id' => 'EXT-OTHER',
            'input_data' => ['name' => 'Test 3'],
            'status' => ProductDescriptionStatus::COMPLETED,
        ]);

        $response = $this->getJson('/api/v1/products/by-external-id/EXT-789', [
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
            ])
            ->assertJson([
                'success' => true,
                'meta' => [
                    'total' => 2,
                ],
            ]);
    }

    /**
     * Test że użytkownik nie widzi opisów innych użytkowników.
     * Tests that user cannot see other users' descriptions.
     */
    public function test_cannot_see_other_users_descriptions(): void
    {
        // Utwórz innego użytkownika z opisem
        $otherUser = User::factory()->create();
        $otherApiKey = ApiKey::create([
            'user_id' => $otherUser->id,
            'key' => hash('sha256', 'other_key'),
            'name' => 'Other Key',
            'is_active' => true,
        ]);

        ProductDescription::create([
            'user_id' => $otherUser->id,
            'api_key_id' => $otherApiKey->id,
            'request_id' => 'other-user-request',
            'external_product_id' => 'EXT-SECRET',
            'input_data' => ['name' => 'Secret Product'],
            'status' => ProductDescriptionStatus::COMPLETED,
        ]);

        // Próba pobrania statusu requestu innego użytkownika
        $response = $this->getJson('/api/v1/products/async-status/other-user-request', [
            'Authorization' => 'Bearer ' . $this->apiKey,
        ]);

        $response->assertStatus(404);

        // Próba pobrania po external_product_id innego użytkownika
        $response = $this->getJson('/api/v1/products/by-external-id/EXT-SECRET', [
            'Authorization' => 'Bearer ' . $this->apiKey,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'meta' => [
                    'total' => 0,
                ],
            ]);
    }

    /**
     * Test walidacji external_product_id.
     * Tests external_product_id validation.
     */
    public function test_external_product_id_validation(): void
    {
        Queue::fake();

        // Test z za długim external_product_id
        $response = $this->postJson('/api/v1/products/generate-description-async', [
            'name' => 'Test',
            'external_product_id' => str_repeat('a', 300), // Za długi
        ], [
            'Authorization' => 'Bearer ' . $this->apiKey,
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test że job jest dodawany do kolejki z poprawnymi danymi.
     * Tests that job is pushed to queue with correct data.
     */
    public function test_job_pushed_with_correct_data(): void
    {
        Queue::fake();

        $response = $this->postJson('/api/v1/products/generate-description-async', [
            'name' => 'Test Product',
            'external_product_id' => 'EXT-JOB-TEST',
        ], [
            'Authorization' => 'Bearer ' . $this->apiKey,
        ]);

        $response->assertStatus(202);

        $productDescriptionId = $response->json('data.id');

        Queue::assertPushed(GenerateProductDescriptionJob::class, function ($job) use ($productDescriptionId) {
            // Sprawdź czy job ma poprawne dane
            $reflection = new \ReflectionClass($job);
            $property = $reflection->getProperty('productDescriptionId');
            $property->setAccessible(true);

            return $property->getValue($job) === $productDescriptionId;
        });
    }

    /**
     * Test generowania bez external_product_id (opcjonalne).
     * Tests generation without external_product_id (optional field).
     */
    public function test_generate_async_without_external_product_id(): void
    {
        Queue::fake();

        $response = $this->postJson('/api/v1/products/generate-description-async', [
            'name' => 'Test Product Without External ID',
        ], [
            'Authorization' => 'Bearer ' . $this->apiKey,
        ]);

        $response->assertStatus(202)
            ->assertJson([
                'success' => true,
                'data' => [
                    'external_product_id' => null,
                ],
            ]);

        Queue::assertPushed(GenerateProductDescriptionJob::class);
    }
}

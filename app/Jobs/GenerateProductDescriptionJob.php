<?php

namespace App\Jobs;

use App\DTO\ProductInputDTO;
use App\Enums\ProductDescriptionStatus;
use App\Models\ApiKey;
use App\Models\ApiUsageLog;
use App\Models\ProductDescription;
use App\Models\User;
use App\Services\ProductDescription\DescriptionGeneratorService;
use App\Services\ProductDescription\ProductDescriptionService;
use App\Services\ProductDescription\ProductEnrichmentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Job do asynchronicznego generowania opisów produktów.
 * Job for asynchronous product description generation.
 *
 * Przetwarza opis produktu w kolejce, umożliwiając natychmiastową odpowiedź API.
 */
class GenerateProductDescriptionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Liczba prób wykonania joba.
     * Number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Timeout joba w sekundach.
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;

    /**
     * Konstruktor.
     *
     * @param int $productDescriptionId ID rekordu ProductDescription
     * @param int $userId ID użytkownika
     * @param int $apiKeyId ID klucza API
     */
    public function __construct(
        protected int $productDescriptionId,
        protected int $userId,
        protected int $apiKeyId
    ) {
    }

    /**
     * Wykonuje job.
     * Execute the job.
     *
     * @param ProductEnrichmentService $enrichmentService
     * @param DescriptionGeneratorService $generatorService
     * @return void
     */
    public function handle(
        ProductEnrichmentService $enrichmentService,
        DescriptionGeneratorService $generatorService
    ): void {
        $startTime = microtime(true);

        // Pobierz rekord opisu produktu
        $productDescription = ProductDescription::find($this->productDescriptionId);
        if (!$productDescription) {
            Log::error('GenerateProductDescriptionJob: ProductDescription not found', [
                'product_description_id' => $this->productDescriptionId,
            ]);
            return;
        }

        // Pobierz użytkownika i klucz API
        $user = User::find($this->userId);
        $apiKey = ApiKey::find($this->apiKeyId);

        if (!$user || !$apiKey) {
            Log::error('GenerateProductDescriptionJob: User or ApiKey not found', [
                'user_id' => $this->userId,
                'api_key_id' => $this->apiKeyId,
            ]);
            $productDescription->markAsFailed(__('api.user_or_key_not_found'));
            return;
        }

        try {
            // Oznacz jako przetwarzany
            $productDescription->markAsProcessing();

            // Utwórz DTO z zapisanych danych wejściowych
            $input = ProductInputDTO::fromArray($productDescription->input_data);

            // Wzbogać dane produktu
            $enrichmentStart = microtime(true);
            if ($input->autoEnrich) {
                $enrichedProduct = $enrichmentService->enrich($input);
            } else {
                $enrichedProduct = $this->createEnrichedFromInput($input);
            }
            $enrichmentTime = (int) ((microtime(true) - $enrichmentStart) * 1000);
            Log::info('GenerateProductDescriptionJob timing: Enrichment', [
                'product_description_id' => $this->productDescriptionId,
                'time_ms' => $enrichmentTime,
            ]);

            // Zapisz wzbogacone dane
            $productDescription->enriched_data = $enrichedProduct->toArray();
            $productDescription->save();

            // Generuj opis przez AI
            $aiStart = microtime(true);
            $generatedDescription = $generatorService->generate($enrichedProduct, $user, $input->language);
            $aiTime = (int) ((microtime(true) - $aiStart) * 1000);
            Log::info('GenerateProductDescriptionJob timing: AI Generation', [
                'product_description_id' => $this->productDescriptionId,
                'time_ms' => $aiTime,
            ]);

            // Zapisz wynik
            $processingTime = (int) ((microtime(true) - $startTime) * 1000);

            $productDescription->update([
                'generated_description' => $generatedDescription->description,
                'prompt_used' => 'Used custom or default prompt',
                'processing_time_ms' => $processingTime,
                'tokens_used' => $generatedDescription->getTotalTokens(),
                'cost' => $generatedDescription->cost,
                'status' => ProductDescriptionStatus::COMPLETED,
            ]);

            // Zaloguj użycie API
            $logData = [
                'user_id' => $user->id,
                'api_key_id' => $apiKey->id,
                'endpoint' => '/api/v1/products/generate-description-async',
                'tokens_used' => $generatedDescription->getTotalTokens(),
                'cost' => $generatedDescription->cost,
                'serper_cost' => $enrichedProduct->serperCost,
                'response_time_ms' => $processingTime,
            ];

            if (Schema::hasColumn('api_usage_logs', 'product_description_id')) {
                $logData['product_description_id'] = $productDescription->id;
            }

            ApiUsageLog::create($logData);

            Log::info('GenerateProductDescriptionJob completed successfully', [
                'product_description_id' => $this->productDescriptionId,
                'processing_time_ms' => $processingTime,
            ]);

        } catch (\Exception $e) {
            Log::error('GenerateProductDescriptionJob failed', [
                'product_description_id' => $this->productDescriptionId,
                'error' => $e->getMessage(),
            ]);

            $productDescription->markAsFailed($e->getMessage());

            throw $e;
        }
    }

    /**
     * Tworzy EnrichedProductDTO z danych wejściowych bez wzbogacania.
     * Creates EnrichedProductDTO from input data without enrichment.
     *
     * @param ProductInputDTO $input
     * @return \App\DTO\EnrichedProductDTO
     */
    protected function createEnrichedFromInput(ProductInputDTO $input): \App\DTO\EnrichedProductDTO
    {
        return new \App\DTO\EnrichedProductDTO(
            name: $input->name ?? 'Produkt',
            manufacturer: $input->manufacturer ?? 'Nieznany',
            price: $input->price,
            description: $input->description,
            attributes: $input->attributes ?? [],
            sources: [],
            originalInput: $input->toArray(),
            category: null,
            availability: null,
            images: [],
            sku: null,
            gtin: null,
            rating: null
        );
    }

    /**
     * Obsługa niepowodzenia joba.
     * Handle a job failure.
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('GenerateProductDescriptionJob failed permanently', [
            'product_description_id' => $this->productDescriptionId,
            'error' => $exception->getMessage(),
        ]);

        $productDescription = ProductDescription::find($this->productDescriptionId);
        if ($productDescription) {
            $productDescription->markAsFailed(__('api.async_generation_failed') . ': ' . $exception->getMessage());
        }
    }
}

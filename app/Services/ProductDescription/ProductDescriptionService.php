<?php

namespace App\Services\ProductDescription;

use App\DTO\EnrichedProductDTO;
use App\DTO\ProductInputDTO;
use App\Enums\ProductDescriptionStatus;
use App\Models\ApiKey;
use App\Models\ApiUsageLog;
use App\Models\ProductDescription;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Główny serwis orkiestracji procesu generowania opisów.
 * Main orchestration service for description generation process.
 *
 * Koordynuje cały proces: walidacja -> enrichment -> generowanie -> zapis.
 */
class ProductDescriptionService
{
    /**
     * Konstruktor.
     */
    public function __construct(
        protected ProductEnrichmentService $enrichmentService,
        protected DescriptionGeneratorService $generatorService
    ) {
    }

    /**
     * Generuje opis produktu (główny flow).
     * Generates product description (main flow).
     *
     * @param ProductInputDTO $input
     * @param User $user
     * @param ApiKey $apiKey
     * @return ProductDescription
     */
    public function generate(
        ProductInputDTO $input,
        User $user,
        ApiKey $apiKey
    ): ProductDescription {
        $startTime = microtime(true);

        // Rozpocznij transakcję
        return DB::transaction(function () use ($input, $user, $apiKey, $startTime) {
            // 1. Utwórz rekord opisu (status: pending)
            $productDescription = ProductDescription::create([
                'user_id' => $user->id,
                'api_key_id' => $apiKey->id,
                'request_id' => (string) Str::uuid(),
                'input_data' => $input->toArray(),
                'status' => ProductDescriptionStatus::PENDING,
            ]);

            try {
                // 2. Oznacz jako przetwarzany
                $productDescription->markAsProcessing();

                // 3. Wzbogać dane produktu (enrichment) lub użyj podanych danych
                $enrichmentStart = microtime(true);
                if ($input->autoEnrich) {
                    // Tryb auto-wzbogacania: szukaj w internecie (droższe, lepsze opisy)
                    $enrichedProduct = $this->enrichmentService->enrich($input);
                } else {
                    // Tryb bez wzbogacania: użyj tylko podanych danych (tańsze, szybsze)
                    $enrichedProduct = $this->createEnrichedFromInput($input);
                }
                $enrichmentTime = (int) ((microtime(true) - $enrichmentStart) * 1000);
                Log::info('ProductDescription timing: Enrichment', ['time_ms' => $enrichmentTime]);

                // Zapisz wzbogacone dane
                $productDescription->enriched_data = $enrichedProduct->toArray();
                $productDescription->save();

                // 4. Generuj opis przez AI
                $aiStart = microtime(true);
                $generatedDescription = $this->generatorService->generate($enrichedProduct, $user, $input->language);
                $aiTime = (int) ((microtime(true) - $aiStart) * 1000);
                Log::info('ProductDescription timing: AI Generation', ['time_ms' => $aiTime]);

                // 5. Zapisz wynik
                $processingTime = (int) ((microtime(true) - $startTime) * 1000);

                $productDescription->update([
                    'generated_description' => $generatedDescription->description,
                    'prompt_used' => 'Used custom or default prompt', // Możesz zapisać faktyczny prompt
                    'processing_time_ms' => $processingTime,
                    'tokens_used' => $generatedDescription->getTotalTokens(),
                    'cost' => $generatedDescription->cost,
                    'status' => ProductDescriptionStatus::COMPLETED,
                ]);

                // 6. Zaloguj użycie API
                $logData = [
                    'user_id' => $user->id,
                    'api_key_id' => $apiKey->id,
                    'endpoint' => '/api/v1/products/generate-description',
                    'tokens_used' => $generatedDescription->getTotalTokens(),
                    'cost' => $generatedDescription->cost,
                    'serper_cost' => $enrichedProduct->serperCost,
                    'response_time_ms' => $processingTime,
                ];

                // Dodaj product_description_id tylko jeśli kolumna istnieje
                if (Schema::hasColumn('api_usage_logs', 'product_description_id')) {
                    $logData['product_description_id'] = $productDescription->id;
                }

                ApiUsageLog::create($logData);

                return $productDescription->fresh();
            } catch (\Exception $e) {
                // Oznacz jako failed
                $productDescription->markAsFailed($e->getMessage());

                throw $e;
            }
        });
    }

    /**
     * Tworzy EnrichedProductDTO z danych wejściowych bez wzbogacania.
     * Creates EnrichedProductDTO from input data without enrichment.
     *
     * @param ProductInputDTO $input
     * @return EnrichedProductDTO
     */
    protected function createEnrichedFromInput(ProductInputDTO $input): EnrichedProductDTO
    {
        return new EnrichedProductDTO(
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
}

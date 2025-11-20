<?php

namespace App\Services\ProductDescription;

use App\DTO\EnrichedProductDTO;
use App\DTO\ProductInputDTO;
use App\Services\WebScraping\DataExtractorService;
use App\Services\WebScraping\SerperSearchService;
use App\Services\WebScraping\WebScraperService;
use Illuminate\Support\Facades\Log;

/**
 * Serwis wzbogacania danych produktu.
 * Service for product data enrichment.
 *
 * Uzupełnia brakujące informacje o produkcie używając web scraping.
 */
class ProductEnrichmentService
{
    /**
     * Konstruktor.
     */
    public function __construct(
        protected SerperSearchService $searchService,
        protected WebScraperService $scraperService,
        protected DataExtractorService $extractorService
    ) {
    }

    /**
     * Wzbogaca dane produktu.
     * Enriches product data.
     *
     * @param ProductInputDTO $input
     * @return EnrichedProductDTO
     */
    public function enrich(ProductInputDTO $input): EnrichedProductDTO
    {
        // Sprawdź jakie dane brakują
        $missingFields = $input->getMissingFields();

        // Przygotuj dane wyjściowe (rozpocznij od tego co mamy)
        $enrichedData = [
            'name' => $input->name,
            'manufacturer' => $input->manufacturer,
            'price' => $input->price,
            'description' => $input->description,
            'attributes' => $input->attributes ?? [],
        ];

        $sources = [];
        $serperCost = 0.0;

        // Jeśli brakuje danych, spróbuj je uzupełnić
        if (!empty($missingFields) && $input->name) {
            try {
                // Zbuduj query wyszukiwania
                $searchQuery = $this->buildSearchQuery($input);

                // Wyszukaj URLs
                $searchStart = microtime(true);
                $urls = $this->searchService->search(
                    $searchQuery,
                    config('api.processing.max_enrichment_urls', 3)
                );
                $searchTime = (int) ((microtime(true) - $searchStart) * 1000);
                Log::info('Enrichment timing: Serper Search', ['time_ms' => $searchTime, 'urls_found' => count($urls)]);

                // Oblicz koszt Serper API (1 token za zapytanie, $1 za 1000 tokenów = $0.001 za token)
                $serperTokens = 1; // Każde zapytanie zużywa 1 token
                $serperCost = $serperTokens * 0.001; // $0.001 za token

                // Scrapuj URLs
                if (!empty($urls)) {
                    $scrapeStart = microtime(true);
                    $scrapingResults = $this->scraperService->scrapeMultiple($urls);
                    $scrapeTime = (int) ((microtime(true) - $scrapeStart) * 1000);
                    Log::info('Enrichment timing: Web Scraping', ['time_ms' => $scrapeTime, 'urls_count' => count($urls)]);

                    // Ekstraktuj dane z każdego URL
                    $extractStart = microtime(true);
                    foreach ($scrapingResults as $result) {
                        if ($result->success) {
                            $html = $result->data['html'] ?? '';
                            if ($html) {
                                $extractedData = $this->extractorService->extract($html, $result->url);

                                // Merguj dane (tylko jeśli brakuje)
                                $enrichedData = $this->mergeData($enrichedData, $extractedData);
                                $sources[] = $result->url;
                            }
                        }
                    }
                    $extractTime = (int) ((microtime(true) - $extractStart) * 1000);
                    Log::info('Enrichment timing: Data Extraction', ['time_ms' => $extractTime]);
                }
            } catch (\Exception $e) {
                Log::warning('Product enrichment failed', [
                    'product' => $input->name,
                    'error' => $e->getMessage(),
                ]);
                // Kontynuuj z tym co mamy
            }
        }

        // Upewnij się że wymagane pola tekstowe są wypełnione (fallback)
        // Cena pozostaje null jeśli nie została podana
        $enrichedData['name'] = $enrichedData['name'] ?? 'Produkt';
        $enrichedData['manufacturer'] = $enrichedData['manufacturer'] ?? 'Nieznany';

        return new EnrichedProductDTO(
            name: $enrichedData['name'],
            manufacturer: $enrichedData['manufacturer'],
            price: $enrichedData['price'],
            description: $enrichedData['description'],
            attributes: $enrichedData['attributes'],
            sources: $sources,
            originalInput: $input->toArray(),
            category: $enrichedData['category'] ?? null,
            availability: $enrichedData['availability'] ?? null,
            images: $enrichedData['images'] ?? [],
            sku: $enrichedData['sku'] ?? null,
            gtin: $enrichedData['gtin'] ?? null,
            rating: $enrichedData['rating'] ?? null,
            serperCost: $serperCost
        );
    }

    /**
     * Buduje query wyszukiwania.
     * Builds search query.
     *
     * @param ProductInputDTO $input
     * @return string
     */
    protected function buildSearchQuery(ProductInputDTO $input): string
    {
        $parts = [];

        if ($input->name) {
            $parts[] = $input->name;
        }

        if ($input->manufacturer) {
            $parts[] = $input->manufacturer;
        }

        return implode(' ', $parts);
    }

    /**
     * Merguje dane (tylko pola które są puste).
     * Merges data (only empty fields).
     *
     * @param array $existing
     * @param array $new
     * @return array
     */
    protected function mergeData(array $existing, array $new): array
    {
        foreach ($new as $key => $value) {
            if (empty($existing[$key]) && !empty($value)) {
                $existing[$key] = $value;
            }
        }

        return $existing;
    }
}

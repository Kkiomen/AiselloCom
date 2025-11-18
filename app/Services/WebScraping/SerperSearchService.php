<?php

namespace App\Services\WebScraping;

use App\Exceptions\ScrapingException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

/**
 * Serwis wyszukiwania przez Serper.dev API.
 * Service for searching via Serper.dev API.
 *
 * Wyszukuje URLs produktów do scrapowania.
 */
class SerperSearchService
{
    /**
     * HTTP client.
     *
     * @var Client
     */
    protected Client $client;

    /**
     * Konstruktor.
     */
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('services.serper.base_url'),
            'timeout' => config('services.serper.timeout'),
            'headers' => [
                'X-API-KEY' => config('services.serper.api_key'),
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Wyszukuje URLs dla produktu.
     * Searches URLs for product.
     *
     * @param string $query Query wyszukiwania
     * @param int $limit Limit wyników
     * @return array URLs do scrapowania
     * @throws ScrapingException
     */
    public function search(string $query, int $limit = 5): array
    {
        try {
            $response = $this->client->post('/search', [
                'json' => [
                    'q' => $query,
                    'num' => $limit,
                    'gl' => 'pl', // Kraj: Polska
                    'hl' => 'pl', // Język: Polski
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            // Wyekstrahuj URLs z wyników organicznych
            $urls = [];
            if (isset($data['organic']) && is_array($data['organic'])) {
                foreach ($data['organic'] as $result) {
                    if (isset($result['link'])) {
                        $urls[] = $result['link'];
                    }
                }
            }

            return array_slice($urls, 0, $limit);
        } catch (\Exception $e) {
            Log::error('Serper search failed', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);

            throw new ScrapingException(
                'Search failed: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }
}

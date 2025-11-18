<?php

namespace App\Services\WebScraping;

use App\DTO\ScrapingResultDTO;
use App\Exceptions\ScrapingException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

/**
 * Serwis scrapowania stron WWW.
 * Service for web scraping.
 *
 * Pobiera HTML z URLs używając Guzzle.
 */
class WebScraperService
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
            'timeout' => config('services.scraping.timeout'),
            'verify' => false, // Dla prostoty MVP, w produkcji użyj true
            'allow_redirects' => true,
        ]);
    }

    /**
     * Scrapuje pojedynczy URL.
     * Scrapes single URL.
     *
     * @param string $url
     * @return ScrapingResultDTO
     */
    public function scrape(string $url): ScrapingResultDTO
    {
        try {
            // Losowy User-Agent z konfiguracji
            $userAgents = config('services.scraping.user_agents');
            $userAgent = $userAgents[array_rand($userAgents)];

            // Wykonaj request
            $response = $this->client->get($url, [
                'headers' => [
                    'User-Agent' => $userAgent,
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language' => 'pl,en-US;q=0.7,en;q=0.3',
                ],
            ]);

            $html = $response->getBody()->getContents();

            return new ScrapingResultDTO(
                url: $url,
                success: true,
                data: ['html' => $html],
                error: null
            );
        } catch (RequestException $e) {
            Log::warning('Web scraping failed', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return new ScrapingResultDTO(
                url: $url,
                success: false,
                data: [],
                error: $e->getMessage()
            );
        } catch (\Exception $e) {
            Log::error('Unexpected scraping error', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return new ScrapingResultDTO(
                url: $url,
                success: false,
                data: [],
                error: $e->getMessage()
            );
        }
    }

    /**
     * Scrapuje wiele URLs równolegle.
     * Scrapes multiple URLs in parallel.
     *
     * @param array $urls
     * @return array<ScrapingResultDTO>
     */
    public function scrapeMultiple(array $urls): array
    {
        $results = [];

        foreach ($urls as $url) {
            $results[] = $this->scrape($url);
        }

        return $results;
    }
}

<?php

namespace App\Services\WebScraping;

use Symfony\Component\DomCrawler\Crawler;

/**
 * Serwis ekstrakcji danych z HTML.
 * Service for data extraction from HTML.
 *
 * Wydobywa informacje o produkcie z HTML używając DomCrawler.
 */
class DataExtractorService
{
    /**
     * Ekstraktuje dane produktu z HTML.
     * Extracts product data from HTML.
     *
     * @param string $html HTML do przetworzenia
     * @param string $url URL źródłowy
     * @return array Wyekstrahowane dane
     */
    public function extract(string $html, string $url): array
    {
        $crawler = new Crawler($html);
        $data = [];

        // Próbuj wyekstrahować przez heurystyki (najniższy priorytet)
        $heuristic = $this->extractByHeuristics($crawler);
        if ($heuristic) {
            $data = array_merge($data, $heuristic);
        }

        // Próbuj wyekstrahować Open Graph (średni priorytet)
        $openGraph = $this->extractOpenGraph($crawler);
        if ($openGraph) {
            $data = array_merge($data, $openGraph);
        }

        // Próbuj wyekstrahować JSON-LD (najwyższy priorytet)
        $jsonLd = $this->extractJsonLd($crawler);
        if ($jsonLd) {
            $data = array_merge($data, $jsonLd);
        }

        return $data;
    }

    /**
     * Ekstraktuje JSON-LD z HTML.
     * Extracts JSON-LD from HTML.
     *
     * @param Crawler $crawler
     * @return array|null
     */
    protected function extractJsonLd(Crawler $crawler): ?array
    {
        try {
            $jsonLdNodes = $crawler->filter('script[type="application/ld+json"]');

            if ($jsonLdNodes->count() > 0) {
                $jsonText = $jsonLdNodes->first()->text();
                $data = json_decode($jsonText, true);

                if (isset($data['@type']) && $data['@type'] === 'Product') {
                    $result = [];

                    if (isset($data['name'])) {
                        $result['name'] = $data['name'];
                    }

                    if (isset($data['description'])) {
                        $result['description'] = $data['description'];
                    }

                    if (isset($data['offers']['price'])) {
                        $result['price'] = (float) $data['offers']['price'];
                    }

                    if (isset($data['brand']['name'])) {
                        $result['manufacturer'] = $data['brand']['name'];
                    } elseif (isset($data['brand']) && is_string($data['brand'])) {
                        $result['manufacturer'] = $data['brand'];
                    }

                    // Kategoria
                    if (isset($data['category'])) {
                        $result['category'] = $data['category'];
                    }

                    // Dostępność
                    if (isset($data['offers']['availability'])) {
                        $result['availability'] = $this->normalizeAvailability($data['offers']['availability']);
                    }

                    // Obrazy
                    if (isset($data['image'])) {
                        $result['images'] = is_array($data['image']) ? $data['image'] : [$data['image']];
                    }

                    // SKU
                    if (isset($data['sku'])) {
                        $result['sku'] = $data['sku'];
                    }

                    // GTIN/EAN
                    if (isset($data['gtin13'])) {
                        $result['gtin'] = $data['gtin13'];
                    } elseif (isset($data['gtin'])) {
                        $result['gtin'] = $data['gtin'];
                    }

                    // Ocena produktu
                    if (isset($data['aggregateRating']['ratingValue'])) {
                        $result['rating'] = (float) $data['aggregateRating']['ratingValue'];
                    }

                    return $result;
                }
            }
        } catch (\Exception $e) {
            // Ignoruj błędy parsowania JSON-LD
        }

        return null;
    }

    /**
     * Ekstraktuje Open Graph tags.
     * Extracts Open Graph tags.
     *
     * @param Crawler $crawler
     * @return array
     */
    protected function extractOpenGraph(Crawler $crawler): array
    {
        $data = [];

        try {
            // og:title
            $ogTitle = $crawler->filter('meta[property="og:title"]');
            if ($ogTitle->count() > 0) {
                $data['name'] = $ogTitle->attr('content');
            }

            // og:description
            $ogDesc = $crawler->filter('meta[property="og:description"]');
            if ($ogDesc->count() > 0) {
                $data['description'] = $ogDesc->attr('content');
            }

            // product:price:amount
            $ogPrice = $crawler->filter('meta[property="product:price:amount"]');
            if ($ogPrice->count() > 0) {
                $priceValue = $ogPrice->attr('content');
                if (is_numeric($priceValue)) {
                    $data['price'] = (float) $priceValue;
                }
            }

            // og:image
            $ogImage = $crawler->filter('meta[property="og:image"]');
            if ($ogImage->count() > 0) {
                $data['image'] = $ogImage->attr('content');
            }
        } catch (\Exception $e) {
            // Ignoruj błędy
        }

        return $data;
    }

    /**
     * Ekstraktuje dane używając heurystyk.
     * Extracts data using heuristics.
     *
     * @param Crawler $crawler
     * @return array
     */
    protected function extractByHeuristics(Crawler $crawler): array
    {
        $data = [];

        try {
            // Tytuł strony jako fallback dla nazwy
            if (empty($data['name'])) {
                $title = $crawler->filter('title');
                if ($title->count() > 0) {
                    $data['name'] = $title->text();
                }
            }

            // Meta description jako fallback
            if (empty($data['description'])) {
                $metaDesc = $crawler->filter('meta[name="description"]');
                if ($metaDesc->count() > 0) {
                    $data['description'] = $metaDesc->attr('content');
                }
            }

            // Szukaj ceny w typowych selektorach
            $priceSelectors = [
                '.price', '.product-price', '[itemprop="price"]',
                '.price-value', '#price'
            ];

            foreach ($priceSelectors as $selector) {
                try {
                    $priceNode = $crawler->filter($selector);
                    if ($priceNode->count() > 0) {
                        $priceText = $priceNode->first()->text();
                        // Wyciągnij tylko liczby i przecinek/kropkę
                        if (preg_match('/[\d\s,\.]+/', $priceText, $matches)) {
                            $priceValue = str_replace([' ', ','], ['', '.'], $matches[0]);
                            $data['price'] = (float) $priceValue;
                            break;
                        }
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        } catch (\Exception $e) {
            // Ignoruj błędy
        }

        return $data;
    }

    /**
     * Normalizuje status dostępności.
     * Normalizes availability status.
     *
     * @param string $availability
     * @return string
     */
    protected function normalizeAvailability(string $availability): string
    {
        // Mapowanie typowych statusów Schema.org
        $statusMap = [
            'InStock' => 'in_stock',
            'https://schema.org/InStock' => 'in_stock',
            'OutOfStock' => 'out_of_stock',
            'https://schema.org/OutOfStock' => 'out_of_stock',
            'PreOrder' => 'pre_order',
            'https://schema.org/PreOrder' => 'pre_order',
            'Discontinued' => 'discontinued',
            'https://schema.org/Discontinued' => 'discontinued',
        ];

        return $statusMap[$availability] ?? 'unknown';
    }
}

<?php

namespace Tests\Unit\Services;

use App\Services\WebScraping\DataExtractorService;
use Tests\TestCase;

/**
 * Testy ekstrakcji danych z realistycznych przykładów HTML.
 * Tests for data extraction from realistic HTML examples.
 *
 * Symuluje różne scenariusze stron sklepów internetowych.
 */
class DataExtractorRealWorldTest extends TestCase
{
    protected DataExtractorService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DataExtractorService();
    }

    /**
     * Test ekstrakcji z pełnego JSON-LD (idealny scenariusz).
     */
    public function test_extract_from_full_json_ld_schema(): void
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <title>Sony WH-1000XM5 - Sklep AudioMax</title>
            <script type="application/ld+json">
            {
                "@context": "https://schema.org/",
                "@type": "Product",
                "name": "Sony WH-1000XM5 Wireless Headphones",
                "image": [
                    "https://example.com/photos/1.jpg",
                    "https://example.com/photos/2.jpg"
                ],
                "description": "Najlepsze słuchawki bezprzewodowe z ANC",
                "sku": "WH1000XM5-B",
                "gtin13": "4548736123456",
                "brand": {
                    "@type": "Brand",
                    "name": "Sony"
                },
                "category": "Słuchawki",
                "offers": {
                    "@type": "Offer",
                    "url": "https://example.com/product",
                    "priceCurrency": "PLN",
                    "price": "1299.00",
                    "availability": "https://schema.org/InStock"
                },
                "aggregateRating": {
                    "@type": "AggregateRating",
                    "ratingValue": "4.8",
                    "reviewCount": "156"
                }
            }
            </script>
        </head>
        <body>
            <h1>Sony WH-1000XM5</h1>
        </body>
        </html>
        ';

        $data = $this->service->extract($html, 'https://example.com');

        // Sprawdź wszystkie pola
        $this->assertEquals('Sony WH-1000XM5 Wireless Headphones', $data['name']);
        $this->assertEquals('Sony', $data['manufacturer']);
        $this->assertEquals(1299.00, $data['price']);
        $this->assertEquals('Najlepsze słuchawki bezprzewodowe z ANC', $data['description']);
        $this->assertEquals('Słuchawki', $data['category']);
        $this->assertEquals('in_stock', $data['availability']);
        $this->assertEquals('WH1000XM5-B', $data['sku']);
        $this->assertEquals('4548736123456', $data['gtin']);
        $this->assertEquals(4.8, $data['rating']);
        $this->assertCount(2, $data['images']);
    }

    /**
     * Test ekstrakcji TYLKO z Open Graph (brak JSON-LD).
     */
    public function test_extract_from_open_graph_only(): void
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <title>iPhone 15 Pro - TechStore</title>
            <meta property="og:title" content="iPhone 15 Pro 256GB Titanium Blue">
            <meta property="og:description" content="Najnowszy iPhone z chipem A17 Pro i tytanową obudową">
            <meta property="og:image" content="https://example.com/iphone15pro.jpg">
            <meta property="product:price:amount" content="5999.99">
            <meta property="product:price:currency" content="PLN">
        </head>
        <body>
            <h1>iPhone 15 Pro</h1>
        </body>
        </html>
        ';

        $data = $this->service->extract($html, 'https://example.com');

        $this->assertEquals('iPhone 15 Pro 256GB Titanium Blue', $data['name']);
        $this->assertEquals('Najnowszy iPhone z chipem A17 Pro i tytanową obudową', $data['description']);
        $this->assertEquals(5999.99, $data['price']);
        $this->assertEquals('https://example.com/iphone15pro.jpg', $data['image']);
    }

    /**
     * Test ekstrakcji TYLKO z heurystyk (brak strukturyzowanych danych).
     */
    public function test_extract_from_heuristics_only(): void
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <title>Samsung Galaxy S24 Ultra - MobileShop</title>
            <meta name="description" content="Flagowy smartfon Samsung z aparatem 200MP">
        </head>
        <body>
            <div class="product">
                <h1>Samsung Galaxy S24 Ultra</h1>
                <p class="price">4599,99 zł</p>
                <div class="description">
                    Najnowszy flagowiec Samsung z ekranem AMOLED 6.8"
                </div>
            </div>
        </body>
        </html>
        ';

        $data = $this->service->extract($html, 'https://example.com');

        // Nazwa z <title>
        $this->assertEquals('Samsung Galaxy S24 Ultra - MobileShop', $data['name']);

        // Opis z meta description
        $this->assertEquals('Flagowy smartfon Samsung z aparatem 200MP', $data['description']);

        // Cena z klasy .price
        $this->assertEquals(4599.99, $data['price']);
    }

    /**
     * Test ekstrakcji z niepoprawnym JSON-LD (nie powinno wywołać błędu).
     */
    public function test_extract_with_malformed_json_ld(): void
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <title>Product</title>
            <script type="application/ld+json">
            {
                "@type": "Product",
                "name": "Test Product"
                // Missing closing brace - invalid JSON
            </script>
            <meta property="og:title" content="Fallback Product Name">
        </head>
        </html>
        ';

        $data = $this->service->extract($html, 'https://example.com');

        // Powinno użyć Open Graph jako fallback
        $this->assertEquals('Fallback Product Name', $data['name']);
    }

    /**
     * Test ekstrakcji z mieszanych źródeł (JSON-LD + Open Graph + heurystyki).
     * Priorytet: JSON-LD > Open Graph > heurystyki
     */
    public function test_extract_priority_mixed_sources(): void
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <title>HTML Title</title>
            <meta property="og:title" content="Open Graph Title">
            <meta property="og:description" content="OG Description">
            <script type="application/ld+json">
            {
                "@type": "Product",
                "name": "JSON-LD Title",
                "price": 99.99
            }
            </script>
        </head>
        </html>
        ';

        $data = $this->service->extract($html, 'https://example.com');

        // JSON-LD ma najwyższy priorytet dla name
        $this->assertEquals('JSON-LD Title', $data['name']);

        // Open Graph dla description (JSON-LD nie ma)
        $this->assertEquals('OG Description', $data['description']);

        // Price z JSON-LD
        $this->assertEquals(99.99, $data['price']);
    }

    /**
     * Test ekstrakcji dostępności w różnych formatach.
     */
    public function test_extract_availability_variants(): void
    {
        $scenarios = [
            ['InStock', 'in_stock'],
            ['https://schema.org/InStock', 'in_stock'],
            ['OutOfStock', 'out_of_stock'],
            ['https://schema.org/OutOfStock', 'out_of_stock'],
            ['PreOrder', 'pre_order'],
            ['Discontinued', 'discontinued'],
            ['UnknownStatus', 'unknown'],
        ];

        foreach ($scenarios as [$input, $expected]) {
            $html = '
            <script type="application/ld+json">
            {
                "@type": "Product",
                "offers": {
                    "availability": "' . $input . '"
                }
            }
            </script>
            ';

            $data = $this->service->extract($html, 'https://example.com');

            $this->assertEquals(
                $expected,
                $data['availability'],
                "Failed for availability: {$input}"
            );
        }
    }

    /**
     * Test ekstrakcji z pustą stroną.
     */
    public function test_extract_from_empty_page(): void
    {
        $html = '<!DOCTYPE html><html><head></head><body></body></html>';

        $data = $this->service->extract($html, 'https://example.com');

        $this->assertIsArray($data);
        // Może być pusta lub zawierać tylko fallbacki
    }

    /**
     * Test ekstrakcji ceny w różnych formatach.
     */
    public function test_extract_price_formats(): void
    {
        $scenarios = [
            '<span class="price">1 299,99 zł</span>' => 1299.99,
            '<div class="price-value">2499.50</div>' => 2499.50,
            '<p class="price">3 999</p>' => 3999.0,
            '<span id="price">49,90 PLN</span>' => 49.90,
        ];

        foreach ($scenarios as $priceHtml => $expectedPrice) {
            $html = "
            <!DOCTYPE html>
            <html>
            <body>
                {$priceHtml}
            </body>
            </html>
            ";

            $data = $this->service->extract($html, 'https://example.com');

            $this->assertEquals(
                $expectedPrice,
                $data['price'],
                "Failed to extract price from: {$priceHtml}"
            );
        }
    }
}

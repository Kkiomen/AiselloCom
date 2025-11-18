<?php

namespace Tests\Unit\Services;

use App\Services\WebScraping\DataExtractorService;
use Tests\TestCase;

/**
 * Test serwisu ekstrakcji danych z HTML.
 * Tests for data extraction service.
 */
class DataExtractorServiceTest extends TestCase
{
    protected DataExtractorService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DataExtractorService();
    }

    /**
     * Test ekstrakcji ceny z JSON-LD.
     */
    public function test_extract_price_from_json_ld(): void
    {
        $html = '
            <html>
                <script type="application/ld+json">
                {
                    "@type": "Product",
                    "name": "Test Product",
                    "offers": {
                        "price": "99.99",
                        "priceCurrency": "PLN"
                    }
                }
                </script>
            </html>
        ';

        $data = $this->service->extract($html, 'https://example.com');

        $this->assertEquals(99.99, $data['price']);
    }

    /**
     * Test ekstrakcji nazwy z JSON-LD.
     */
    public function test_extract_name_from_json_ld(): void
    {
        $html = '
            <html>
                <script type="application/ld+json">
                {
                    "@type": "Product",
                    "name": "Amazing Product",
                    "description": "Product description"
                }
                </script>
            </html>
        ';

        $data = $this->service->extract($html, 'https://example.com');

        $this->assertEquals('Amazing Product', $data['name']);
        $this->assertEquals('Product description', $data['description']);
    }

    /**
     * Test ekstrakcji z Open Graph meta tags.
     */
    public function test_extract_from_open_graph(): void
    {
        $html = '
            <html>
                <head>
                    <meta property="og:title" content="OG Product Title">
                    <meta property="og:description" content="OG Description">
                    <meta property="og:image" content="https://example.com/image.jpg">
                    <meta property="product:price:amount" content="199.99">
                </head>
            </html>
        ';

        $data = $this->service->extract($html, 'https://example.com');

        $this->assertEquals('OG Product Title', $data['name']);
        $this->assertEquals('OG Description', $data['description']);
        $this->assertEquals(199.99, $data['price']);
    }

    /**
     * Test ekstrakcji producenta.
     */
    public function test_extract_manufacturer(): void
    {
        $html = '
            <html>
                <script type="application/ld+json">
                {
                    "@type": "Product",
                    "brand": {
                        "name": "Sony"
                    }
                }
                </script>
            </html>
        ';

        $data = $this->service->extract($html, 'https://example.com');

        $this->assertEquals('Sony', $data['manufacturer']);
    }

    /**
     * Test ekstrakcji z pustego HTML.
     */
    public function test_extract_from_empty_html(): void
    {
        $data = $this->service->extract('', 'https://example.com');

        $this->assertIsArray($data);
        $this->assertEmpty($data);
    }

    /**
     * Test ekstrakcji z nieprawidłowego JSON-LD.
     */
    public function test_extract_with_invalid_json_ld(): void
    {
        $html = '
            <html>
                <script type="application/ld+json">
                {invalid json}
                </script>
            </html>
        ';

        $data = $this->service->extract($html, 'https://example.com');

        // Nie powinno wywołać błędu, tylko zwrócić puste dane
        $this->assertIsArray($data);
    }

    /**
     * Test priorytetyzacji źródeł danych.
     */
    public function test_data_source_priority(): void
    {
        $html = '
            <html>
                <head>
                    <meta property="og:title" content="OG Title">
                    <title>HTML Title</title>
                </head>
                <script type="application/ld+json">
                {
                    "@type": "Product",
                    "name": "JSON-LD Title"
                }
                </script>
            </html>
        ';

        $data = $this->service->extract($html, 'https://example.com');

        // JSON-LD ma wyższy priorytet niż Open Graph
        $this->assertEquals('JSON-LD Title', $data['name']);
    }

    /**
     * Test ekstrakcji dostępności.
     */
    public function test_extract_availability(): void
    {
        $html = '
            <html>
                <script type="application/ld+json">
                {
                    "@type": "Product",
                    "offers": {
                        "availability": "https://schema.org/InStock"
                    }
                }
                </script>
            </html>
        ';

        $data = $this->service->extract($html, 'https://example.com');

        $this->assertEquals('in_stock', $data['availability']);
    }

    /**
     * Test ekstrakcji obrazów.
     */
    public function test_extract_images(): void
    {
        $html = '
            <html>
                <script type="application/ld+json">
                {
                    "@type": "Product",
                    "image": ["https://example.com/image1.jpg", "https://example.com/image2.jpg"]
                }
                </script>
            </html>
        ';

        $data = $this->service->extract($html, 'https://example.com');

        $this->assertIsArray($data['images']);
        $this->assertCount(2, $data['images']);
        $this->assertEquals('https://example.com/image1.jpg', $data['images'][0]);
    }

    /**
     * Test ekstrakcji pojedynczego obrazu.
     */
    public function test_extract_single_image(): void
    {
        $html = '
            <html>
                <script type="application/ld+json">
                {
                    "@type": "Product",
                    "image": "https://example.com/image.jpg"
                }
                </script>
            </html>
        ';

        $data = $this->service->extract($html, 'https://example.com');

        $this->assertIsArray($data['images']);
        $this->assertCount(1, $data['images']);
        $this->assertEquals('https://example.com/image.jpg', $data['images'][0]);
    }

    /**
     * Test ekstrakcji kategorii.
     */
    public function test_extract_category(): void
    {
        $html = '
            <html>
                <script type="application/ld+json">
                {
                    "@type": "Product",
                    "category": "Electronics"
                }
                </script>
            </html>
        ';

        $data = $this->service->extract($html, 'https://example.com');

        $this->assertEquals('Electronics', $data['category']);
    }

    /**
     * Test ekstrakcji SKU i GTIN.
     */
    public function test_extract_sku_and_gtin(): void
    {
        $html = '
            <html>
                <script type="application/ld+json">
                {
                    "@type": "Product",
                    "sku": "ABC123",
                    "gtin13": "1234567890123"
                }
                </script>
            </html>
        ';

        $data = $this->service->extract($html, 'https://example.com');

        $this->assertEquals('ABC123', $data['sku']);
        $this->assertEquals('1234567890123', $data['gtin']);
    }

    /**
     * Test ekstrakcji oceny produktu.
     */
    public function test_extract_rating(): void
    {
        $html = '
            <html>
                <script type="application/ld+json">
                {
                    "@type": "Product",
                    "aggregateRating": {
                        "ratingValue": "4.5",
                        "reviewCount": "100"
                    }
                }
                </script>
            </html>
        ';

        $data = $this->service->extract($html, 'https://example.com');

        $this->assertEquals(4.5, $data['rating']);
    }

    /**
     * Test ekstrakcji obrazu z Open Graph.
     */
    public function test_extract_image_from_open_graph(): void
    {
        $html = '
            <html>
                <head>
                    <meta property="og:image" content="https://example.com/og-image.jpg">
                </head>
            </html>
        ';

        $data = $this->service->extract($html, 'https://example.com');

        $this->assertEquals('https://example.com/og-image.jpg', $data['image']);
    }
}

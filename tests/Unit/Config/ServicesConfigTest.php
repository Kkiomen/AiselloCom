<?php

namespace Tests\Unit\Config;

use Tests\TestCase;

/**
 * Test konfiguracji serwisów zewnętrznych.
 * Tests for external services configuration.
 */
class ServicesConfigTest extends TestCase
{
    /**
     * Test czy wartości OpenAI są poprawnie typowane.
     */
    public function test_openai_config_types(): void
    {
        $config = config('services.openai');

        $this->assertIsString($config['api_key'] ?? '');
        $this->assertIsString($config['model']);
        $this->assertIsInt($config['max_tokens']);
        $this->assertIsFloat($config['temperature']);
        $this->assertIsFloat($config['top_p']);
        $this->assertIsInt($config['timeout']);
    }

    /**
     * Test czy wartości Serper są poprawnie typowane.
     */
    public function test_serper_config_types(): void
    {
        $config = config('services.serper');

        $this->assertIsString($config['api_key'] ?? '');
        $this->assertIsString($config['base_url']);
        $this->assertIsInt($config['timeout']);
        $this->assertIsInt($config['results_limit']);
    }

    /**
     * Test czy wartości scraping są poprawnie typowane.
     */
    public function test_scraping_config_types(): void
    {
        $config = config('services.scraping');

        $this->assertIsInt($config['timeout']);
        $this->assertIsInt($config['max_retries']);
        $this->assertIsInt($config['retry_delay']);
        $this->assertIsArray($config['user_agents']);
        $this->assertNotEmpty($config['user_agents']);
    }

    /**
     * Test czy wartości numeryczne nie są stringami.
     */
    public function test_numeric_values_are_not_strings(): void
    {
        // OpenAI
        $this->assertNotSame('1500', config('services.openai.max_tokens'));
        $this->assertNotSame('0.7', config('services.openai.temperature'));

        // Serper
        $this->assertNotSame('10', config('services.serper.timeout'));
        $this->assertNotSame('5', config('services.serper.results_limit'));

        // Scraping
        $this->assertNotSame('15', config('services.scraping.timeout'));
    }

    /**
     * Test domyślnych wartości OpenAI.
     */
    public function test_openai_default_values(): void
    {
        $this->assertEquals('gpt-4o-mini', config('services.openai.model'));
        $this->assertEquals(1500, config('services.openai.max_tokens'));
        $this->assertEquals(0.7, config('services.openai.temperature'));
        $this->assertEquals(1.0, config('services.openai.top_p'));
    }

    /**
     * Test domyślnych wartości Serper.
     */
    public function test_serper_default_values(): void
    {
        $this->assertEquals('https://google.serper.dev', config('services.serper.base_url'));
        $this->assertEquals(10, config('services.serper.timeout'));
        $this->assertEquals(5, config('services.serper.results_limit'));
        $this->assertEquals('search', config('services.serper.search_type'));
    }
}

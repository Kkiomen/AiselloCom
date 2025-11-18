<?php

namespace Tests\Unit\Services;

use App\Services\AI\OpenAIService;
use App\Exceptions\AIServiceException;
use Tests\TestCase;

/**
 * Test serwisu OpenAI.
 * Tests for OpenAI service.
 */
class OpenAIServiceTest extends TestCase
{
    /**
     * Test czy serwis wymaga klucza API.
     */
    public function test_requires_api_key(): void
    {
        config(['services.openai.api_key' => null]);

        $this->expectException(AIServiceException::class);
        $this->expectExceptionMessage('OpenAI API key is not configured');

        new OpenAIService();
    }

    /**
     * Test czy konfiguracja zwraca poprawne typy.
     */
    public function test_config_returns_correct_types(): void
    {
        $service = new OpenAIService();

        // Sprawdź czy wartości z configu są właściwego typu
        $maxTokens = config('services.openai.max_tokens');
        $temperature = config('services.openai.temperature');
        $topP = config('services.openai.top_p');

        $this->assertIsInt($maxTokens, 'max_tokens powinno być integer, jest ' . gettype($maxTokens));
        $this->assertIsFloat($temperature, 'temperature powinno być float, jest ' . gettype($temperature));
        $this->assertIsFloat($topP, 'top_p powinno być float, jest ' . gettype($topP));
    }

    /**
     * Test szacowania tokenów.
     */
    public function test_estimate_tokens(): void
    {
        $service = new OpenAIService();

        // Test dla różnych długości tekstu
        $this->assertEquals(0, $service->estimateTokens(''));
        $this->assertEquals(1, $service->estimateTokens('test')); // 4 znaki = 1 token
        $this->assertEquals(6, $service->estimateTokens('To jest testowy tekst')); // 22 znaki = 6 tokenów
    }

    /**
     * Test szacowania dla długiego tekstu.
     */
    public function test_estimate_tokens_long_text(): void
    {
        $service = new OpenAIService();

        $longText = str_repeat('test ', 100); // 500 znaków
        $estimated = $service->estimateTokens($longText);

        $this->assertGreaterThan(100, $estimated);
        $this->assertLessThan(150, $estimated);
    }

    /**
     * Test szacowania dla tekstu wielobajtowego (UTF-8).
     */
    public function test_estimate_tokens_multibyte(): void
    {
        $service = new OpenAIService();

        $polishText = 'Zażółć gęślą jaźń'; // Polskie znaki
        $estimated = $service->estimateTokens($polishText);

        $this->assertGreaterThan(0, $estimated);
    }
}

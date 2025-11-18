<?php

namespace App\Services\AI;

use App\DTO\GeneratedDescriptionDTO;
use App\Exceptions\AIServiceException;
use App\Helpers\CostCalculator;
use OpenAI;

/**
 * Serwis komunikacji z OpenAI API.
 * Service for OpenAI API communication.
 *
 * Obsługuje generowanie tekstów przez modele GPT.
 */
class OpenAIService
{
    /**
     * Klient OpenAI.
     *
     * @var \OpenAI\Client
     */
    protected $client;

    /**
     * Konstruktor.
     */
    public function __construct()
    {
        $apiKey = config('services.openai.api_key');

        if (!$apiKey) {
            throw new AIServiceException('OpenAI API key is not configured');
        }

        $this->client = OpenAI::client($apiKey);
    }

    /**
     * Generuje completion (opis produktu).
     * Generates completion (product description).
     *
     * @param string $prompt Prompt do modelu
     * @param array $options Opcje requestu
     * @return GeneratedDescriptionDTO
     * @throws AIServiceException
     */
    public function complete(string $prompt, array $options = []): GeneratedDescriptionDTO
    {
        try {
            // Przygotuj parametry
            $params = [
                'model' => $options['model'] ?? config('services.openai.model'),
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'max_tokens' => $options['max_tokens'] ?? config('services.openai.max_tokens'),
                'temperature' => $options['temperature'] ?? config('services.openai.temperature'),
                'top_p' => $options['top_p'] ?? config('services.openai.top_p'),
            ];

            // Wykonaj request do OpenAI
            $response = $this->client->chat()->create($params);

            // Wyekstrahuj dane z odpowiedzi
            $description = $response->choices[0]->message->content;
            $inputTokens = $response->usage->promptTokens;
            $outputTokens = $response->usage->completionTokens;
            $model = $response->model;

            // Wyczyść opis z znaczników markdown i typowych znaków AI
            // Clean description from markdown markers and typical AI characters
            $description = $this->sanitizeDescription($description);

            // Oblicz koszt
            $cost = CostCalculator::calculate($inputTokens, $outputTokens, $model);

            return new GeneratedDescriptionDTO(
                description: $description,
                inputTokens: $inputTokens,
                outputTokens: $outputTokens,
                cost: $cost,
                model: $model
            );
        } catch (\Exception $e) {
            throw new AIServiceException(
                'Failed to generate description: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Szacuje liczbę tokenów dla tekstu.
     * Estimates token count for text.
     *
     * Używa aproksymacji, rzeczywista liczba może się różnić.
     *
     * @param string $text
     * @return int
     */
    public function estimateTokens(string $text): int
    {
        // Prosta heurystyka: ~4 znaki = 1 token
        return (int) ceil(mb_strlen($text) / 4);
    }

    /**
     * Czyści opis z znaczników markdown i typowych znaków AI.
     * Sanitizes description from markdown markers and typical AI characters.
     *
     * @param string $description
     * @return string
     */
    protected function sanitizeDescription(string $description): string
    {
        // Usuń bloki kodu markdown (```html ... ``` lub ``` ... ```)
        // Remove markdown code blocks
        $description = preg_replace('/```(?:html|HTML)?\s*\n?(.*?)\n?```/s', '$1', $description);

        // Zamień em-dash (—) na zwykły myślnik (-)
        // Replace em-dash with regular dash
        $description = str_replace('—', '-', $description);

        // Zamień en-dash (–) na zwykły myślnik (-)
        // Replace en-dash with regular dash
        $description = str_replace('–', '-', $description);

        // Usuń podwójne gwiazdki (bold markdown) jeśli zostały
        // Remove double asterisks (bold markdown) if present
        $description = preg_replace('/\*\*(.*?)\*\*/', '$1', $description);

        // Usuń pojedyncze gwiazdki (italic markdown) jeśli zostały
        // Remove single asterisks (italic markdown) if present
        $description = preg_replace('/\*(.*?)\*/', '$1', $description);

        // Usuń znaki # na początku linii (nagłówki markdown)
        // Remove # characters at line start (markdown headers)
        $description = preg_replace('/^#{1,6}\s+/m', '', $description);

        // Usuń nadmiarowe puste linie (więcej niż 2 z rzędu)
        // Remove excessive empty lines (more than 2 in a row)
        $description = preg_replace('/\n{3,}/', "\n\n", $description);

        // Przytnij białe znaki
        // Trim whitespace
        return trim($description);
    }
}

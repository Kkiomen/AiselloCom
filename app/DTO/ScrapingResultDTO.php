<?php

namespace App\DTO;

/**
 * DTO wyniku web scrapingu.
 * DTO for web scraping result.
 *
 * Reprezentuje dane wyekstrahowane z jednego URL.
 */
readonly class ScrapingResultDTO
{
    /**
     * Konstruktor.
     *
     * @param string $url URL ze scrapowanych danych
     * @param bool $success Czy scraping się powiódł
     * @param array $data Wyekstrahowane dane
     * @param string|null $error Komunikat błędu (jeśli niepowodzenie)
     */
    public function __construct(
        public string $url,
        public bool $success,
        public array $data,
        public ?string $error = null,
    ) {
    }

    /**
     * Konwertuje do tablicy.
     * Converts to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'success' => $this->success,
            'data' => $this->data,
            'error' => $this->error,
        ];
    }
}

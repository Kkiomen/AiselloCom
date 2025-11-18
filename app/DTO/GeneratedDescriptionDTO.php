<?php

namespace App\DTO;

/**
 * DTO wygenerowanego opisu.
 * DTO for generated description.
 *
 * Reprezentuje wynik generowania opisu przez AI.
 */
readonly class GeneratedDescriptionDTO
{
    /**
     * Konstruktor.
     *
     * @param string $description Wygenerowany opis
     * @param int $inputTokens Liczba tokenów wejściowych
     * @param int $outputTokens Liczba tokenów wyjściowych
     * @param float $cost Koszt w USD
     * @param string $model Użyty model AI
     */
    public function __construct(
        public string $description,
        public int $inputTokens,
        public int $outputTokens,
        public float $cost,
        public string $model,
    ) {
    }

    /**
     * Zwraca całkowitą liczbę tokenów.
     * Returns total token count.
     *
     * @return int
     */
    public function getTotalTokens(): int
    {
        return $this->inputTokens + $this->outputTokens;
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
            'description' => $this->description,
            'tokens' => [
                'input' => $this->inputTokens,
                'output' => $this->outputTokens,
                'total' => $this->getTotalTokens(),
            ],
            'cost' => $this->cost,
            'model' => $this->model,
        ];
    }
}

<?php

namespace App\Helpers;

/**
 * Helper do kalkulacji kosztów API.
 * Helper for API cost calculation.
 *
 * Oblicza koszty użycia modeli AI na podstawie liczby tokenów.
 */
class CostCalculator
{
    /**
     * Kalkuluje koszt na podstawie tokenów.
     * Calculates cost based on tokens.
     *
     * @param int $inputTokens Liczba tokenów wejściowych
     * @param int $outputTokens Liczba tokenów wyjściowych
     * @param string $model Nazwa modelu AI
     * @return float Koszt w USD
     */
    public static function calculate(
        int $inputTokens,
        int $outputTokens,
        string $model = 'gpt-4o-mini'
    ): float {
        // Pobierz ceny z konfiguracji
        $costs = config("api.costs.{$model}");

        if (!$costs) {
            // Jeśli model nie znaleziony, użyj domyślnych cen gpt-4o-mini
            $costs = config('api.costs.gpt4o_mini');
        }

        // Oblicz koszt (ceny są per 1K tokenów)
        $inputCost = ($inputTokens / 1000) * $costs['input'];
        $outputCost = ($outputTokens / 1000) * $costs['output'];

        return round($inputCost + $outputCost, 4);
    }

    /**
     * Kalkuluje koszt dla całkowitej liczby tokenów (uproszczona wersja).
     * Assumes 50/50 split between input and output tokens.
     *
     * @param int $totalTokens Całkowita liczba tokenów
     * @param string $model Nazwa modelu AI
     * @return float Koszt w USD
     */
    public static function calculateFromTotal(
        int $totalTokens,
        string $model = 'gpt-4o-mini'
    ): float {
        // Zakładamy podział 50/50 między input i output
        $halfTokens = (int) ($totalTokens / 2);

        return self::calculate($halfTokens, $halfTokens, $model);
    }

    /**
     * Formatuje koszt do wyświetlenia.
     * Formats cost for display.
     *
     * @param float $cost Koszt w USD
     * @param string $currency Waluta (USD, PLN, etc.)
     * @return string Sformatowany koszt
     */
    public static function format(float $cost, string $currency = 'USD'): string
    {
        return number_format($cost, 4) . ' ' . $currency;
    }
}

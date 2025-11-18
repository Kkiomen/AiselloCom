<?php

namespace App\Helpers;

/**
 * Helper do liczenia tokenów.
 * Helper for token counting.
 *
 * Aproksymacja liczby tokenów dla modeli OpenAI.
 * Rzeczywista liczba może się nieznacznie różnić.
 */
class TokenCounter
{
    /**
     * Aproksymuje liczbę tokenów dla tekstu.
     * Approximates token count for text.
     *
     * Reguła heurystyczna:
     * - Angielski: ~4 znaki = 1 token
     * - Polski: ~5 znaków = 1 token (więcej znaków diakrytycznych)
     *
     * @param string $text Tekst do policzenia
     * @param string $language Język tekstu (pl, en)
     * @return int Szacowana liczba tokenów
     */
    public static function estimate(string $text, string $language = 'pl'): int
    {
        if (empty($text)) {
            return 0;
        }

        $charCount = mb_strlen($text);

        // Różne współczynniki dla różnych języków
        $charsPerToken = match ($language) {
            'en' => 4.0,
            'pl' => 5.0,
            default => 4.5,
        };

        return (int) ceil($charCount / $charsPerToken);
    }

    /**
     * Szacuje liczbę tokenów dla tablicy stringów.
     * Estimates token count for array of strings.
     *
     * @param array $texts Tablica tekstów
     * @param string $language Język tekstów
     * @return int Szacowana liczba tokenów
     */
    public static function estimateMultiple(array $texts, string $language = 'pl'): int
    {
        $totalTokens = 0;

        foreach ($texts as $text) {
            if (is_string($text)) {
                $totalTokens += self::estimate($text, $language);
            }
        }

        return $totalTokens;
    }

    /**
     * Szacuje liczbę tokenów dla struktury (JSON/array).
     * Estimates token count for structure (JSON/array).
     *
     * @param array $data Dane do policzenia
     * @param string $language Język danych
     * @return int Szacowana liczba tokenów
     */
    public static function estimateStructure(array $data, string $language = 'pl'): int
    {
        // Konwertuj do JSON i policz tokeny
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);

        return self::estimate($json, $language);
    }
}

<?php

namespace App\Services\AI;

use App\DTO\EnrichedProductDTO;
use App\Models\User;

/**
 * Serwis budowania promptów.
 * Service for building prompts.
 *
 * Buduje prompty do generowania opisów na podstawie szablonów i danych.
 */
class PromptBuilderService
{
    /**
     * Buduje prompt z danych produktu.
     * Builds prompt from product data.
     *
     * @param EnrichedProductDTO $product Dane produktu
     * @param User $user Użytkownik
     * @param string $language Język docelowy opisu
     * @return string Zbudowany prompt
     */
    public function build(EnrichedProductDTO $product, User $user, string $language = 'pl'): string
    {
        // Pobierz szablon promptu
        $template = $this->getTemplate($user);

        // Mapuj kod języka na pełną nazwę
        // Map language code to full name
        $languageNames = [
            'pl' => 'polski',
            'en' => 'English',
            'de' => 'Deutsch',
            'fr' => 'français',
            'es' => 'español',
            'it' => 'italiano',
            'cs' => 'čeština',
            'sk' => 'slovenčina',
            'uk' => 'українська',
            'ru' => 'русский',
        ];
        $languageName = $languageNames[$language] ?? $language;

        // Przygotuj dane do interpolacji
        $placeholders = [
            '{name}' => $product->name,
            '{manufacturer}' => $product->manufacturer,
            '{price}' => number_format($product->price, 2, ',', ' '),
            '{description}' => $product->description ?? 'Brak dodatkowego opisu',
            '{attributes}' => $this->formatAttributes($product->attributes),
            '{language}' => $languageName,
        ];

        // Interpoluj placeholder w szablonie
        return str_replace(
            array_keys($placeholders),
            array_values($placeholders),
            $template
        );
    }

    /**
     * Pobiera szablon promptu dla użytkownika.
     * Gets prompt template for user.
     *
     * Jeśli użytkownik ma customowy prompt, użyj go.
     * W przeciwnym razie użyj domyślnego.
     *
     * @param User $user
     * @return string
     */
    protected function getTemplate(User $user): string
    {
        // Sprawdź czy użytkownik ma domyślny prompt
        $userPrompt = $user->defaultPrompt;

        if ($userPrompt) {
            return $userPrompt->prompt_template;
        }

        // Zwróć domyślny prompt systemowy
        return config('api.default_prompt');
    }

    /**
     * Formatuje atrybuty do tekstu.
     * Formats attributes to text.
     *
     * @param array $attributes
     * @return string
     */
    protected function formatAttributes(array $attributes): string
    {
        if (empty($attributes)) {
            return 'Brak dodatkowych atrybutów';
        }

        $formatted = [];
        foreach ($attributes as $key => $value) {
            $formatted[] = "- {$key}: {$value}";
        }

        return implode("\n", $formatted);
    }
}

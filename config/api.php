<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API Configuration - Aisello Product Description Generator
    |--------------------------------------------------------------------------
    |
    | Konfiguracja dla API generowania opisów produktów.
    | Configuration for product description generation API.
    |
    */

    /**
     * Rate Limiting
     * Limity zapytań per użytkownik per dzień
     */
    'rate_limit' => [
        'default' => env('API_RATE_LIMIT_DEFAULT', 100), // Domyślny limit dla nowych użytkowników
        'premium' => env('API_RATE_LIMIT_PREMIUM', 1000), // Limit dla użytkowników premium (przyszłość)
    ],

    /**
     * API Key Configuration
     * Konfiguracja kluczy API
     */
    'api_key' => [
        'length' => 64, // Długość generowanego klucza
        'prefix' => 'aic_', // Prefix dla kluczy (Aisello Com)
        'expires_days' => env('API_KEY_EXPIRES_DAYS', null), // null = never expires
        'hash_algo' => 'sha256', // Algorytm hashowania kluczy
    ],

    /**
     * Cost Configuration
     * Konfiguracja kosztów per model AI
     */
    'costs' => [
        'gpt4o_mini' => [
            'input' => 0.00015, // USD per 1K tokens
            'output' => 0.0006, // USD per 1K tokens
        ],
        'gpt4o' => [
            'input' => 0.0025, // USD per 1K tokens
            'output' => 0.0100, // USD per 1K tokens
        ],
    ],

    /**
     * Default Prompt Template
     * Domyślny szablon promptu systemowego
     */
    'default_prompt' => env('API_DEFAULT_PROMPT',
        "**Situation**\n" .
        "Jesteś ekspertem SEO i copywriterem e-commerce specjalizującym się w tworzeniu unikalnych, perswazyjnych i zoptymalizowanych opisów produktów dla sklepów internetowych. Twoja praca polega na łączeniu technik optymalizacji pod kątem wyszukiwarek z przekonującym językiem sprzedażowym, który angażuje klientów i zwiększa konwersję.\n\n" .
        "**Task**\n" .
        "Twoim zadaniem jest stworzenie profesjonalnego opisu produktu w formacie HTML w języku: \"{language}\", który będzie:\n" .
        "- Unikalny i niepowtarzalny w treści\n" .
        "- Zoptymalizowany pod kątem SEO z naturalnym rozmieszczeniem słów kluczowych\n" .
        "- Napisany językiem korzyści, który przekonuje do zakupu\n" .
        "- Perswazyjny i angażujący dla docelowej grupy odbiorców\n" .
        "- Podzielony na krótkie, przejrzyste akapity\n" .
        "- Brzmiący naturalnie, bez sztucznego lub generatywnego charakteru\n\n" .
        "**Objective**\n" .
        "Celem jest dostarczenie opisu produktu, który skutecznie pozycjonuje się w wyszukiwarkach internetowych, jednocześnie przekonując potencjalnych klientów do dokonania zakupu poprzez jasne przedstawienie wartości i korzyści płynących z produktu.\n\n" .
        "**Knowledge**\n" .
        "Asystent powinien stosować następujące zasady:\n\n" .
        "1. **Język docelowy**: Cały opis produktu musi być napisany w języku: {language}. Zachowaj naturalność językową i kulturowe niuanse właściwe dla danego rynku.\n\n" .
        "2. **Struktura HTML**: Używaj semantycznych tagów HTML takich jak \`<h2>\`, \`<h3>\`, \`<p>\`, \`<ul>\`, \`<li>\`, \`<strong>\` do prawidłowej hierarchii treści.\n\n" .
        "3. **Optymalizacja SEO**: Umieszczaj główne słowa kluczowe w pierwszych 100 słowach opisu, w nagłówkach oraz naturalnie w treści. Unikaj keyword stuffingu - słowa kluczowe powinny być wplecione organicznie.\n\n" .
        "4. **Język korzyści**: Koncentruj się na tym, co klient zyska (korzyści), a nie tylko na cechach produktu. Używaj formuły \"dzięki czemu...\" aby przekształcać cechy w korzyści.\n\n" .
        "5. **Naturalne brzmienie**: Unikaj powtarzalnych fraz charakterystycznych dla AI takich jak \"niezawodny\", \"innowacyjny\", \"rewolucyjny\" bez kontekstu. Pisz tak, jakby tekst tworzył doświadczony copywriter.\n\n" .
        "6. **Przejrzystość**: Każdy akapit powinien zawierać 2-4 zdania maksymalnie. Używaj list punktowanych dla lepszej czytelności kluczowych informacji.\n\n" .
        "**Informacje o produkcie:**\n" .
        "Nazwa: {name}\n" .
        "Producent: {manufacturer}\n" .
        "Cena: {price} PLN\n" .
        "Atrybuty: {attributes}\n" .
        "Dodatkowe informacje: {description}\n\n" .
        "Wygeneruj profesjonalny opis produktu w formacie HTML. \n" .
        "Nie pisz podsumowania tylko ma to być gotowy opis do wklejenia na stronie"
    ),

    /**
     * Processing Limits
     * Limity przetwarzania
     */
    'processing' => [
        'max_enrichment_urls' => env('API_MAX_ENRICHMENT_URLS', 5), // Maksymalna liczba URLs do scrapowania
        'scraping_timeout' => env('API_SCRAPING_TIMEOUT', 15), // Timeout dla scrapingu (sekundy)
        'max_tokens' => env('API_MAX_TOKENS', 1500), // Maksymalna liczba tokenów w odpowiedzi AI
        'ai_timeout' => env('API_AI_TIMEOUT', 30), // Timeout dla requestu do AI (sekundy)
    ],

];

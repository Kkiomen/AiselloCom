<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Language Lines - Polish
    |--------------------------------------------------------------------------
    |
    | Tłumaczenia komunikatów API na język polski.
    |
    */

    // Autentykacja
    'auth' => [
        'missing_api_key' => 'Brak klucza API. Wymagany nagłówek Authorization: Bearer {api_key}',
        'invalid_api_key' => 'Nieprawidłowy lub nieaktywny klucz API.',
        'inactive_or_expired_key' => 'Klucz API jest nieaktywny lub wygasł.',
        'user_inactive' => 'Konto użytkownika jest nieaktywne.',
        'unauthenticated' => 'Brak autentykacji.',
    ],

    // Rate limiting
    'rate_limit' => [
        'exceeded' => 'Przekroczono limit zapytań API. Limit: :limit zapytań dziennie.',
    ],

    // Statusy
    'status' => [
        'pending' => 'Oczekujący',
        'processing' => 'Przetwarzanie',
        'completed' => 'Zakończony',
        'failed' => 'Niepowodzenie',
    ],

    // Opisy produktów
    'description' => [
        'generated_successfully' => 'Opis produktu został pomyślnie wygenerowany.',
        'generation_failed' => 'Nie udało się wygenerować opisu produktu.',
        'not_found' => 'Nie znaleziono opisu produktu.',
        'queued_successfully' => 'Zadanie generowania opisu zostało dodane do kolejki.',
        'queue_failed' => 'Nie udało się dodać zadania do kolejki.',
    ],

    // Asynchroniczne generowanie
    'user_or_key_not_found' => 'Nie znaleziono użytkownika lub klucza API.',
    'async_generation_failed' => 'Asynchroniczne generowanie opisu nie powiodło się',

    // Walidacja
    'validation' => [
        'name_string' => 'Nazwa musi być tekstem.',
        'name_max' => 'Nazwa nie może być dłuższa niż 255 znaków.',
        'manufacturer_string' => 'Producent musi być tekstem.',
        'price_numeric' => 'Cena musi być liczbą.',
        'price_min' => 'Cena musi być większa lub równa 0.',
        'prompt_not_found' => 'Nie znaleziono wybranego promptu.',
        'external_product_id_string' => 'Zewnętrzny ID produktu musi być tekstem.',
        'external_product_id_max' => 'Zewnętrzny ID produktu nie może być dłuższy niż 255 znaków.',
    ],
];

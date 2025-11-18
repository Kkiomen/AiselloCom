<?php

/**
 * UI translations - Polish
 * Tłumaczenia UI - Polski
 *
 * Zawiera tłumaczenia elementów interfejsu użytkownika
 * Contains translations for user interface elements
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Navigation / Nawigacja
    |--------------------------------------------------------------------------
    */

    'nav' => [
        'dashboard' => 'Pulpit',
        'api_keys' => 'Klucze API',
        'api_explorer' => 'Eksplorator API',
        'usage' => 'Użycie i statystyki',
        'documentation' => 'Dokumentacja',
        'profile' => 'Profil',
        'logout' => 'Wyloguj',
        'settings' => 'Ustawienia',
    ],

    /*
    |--------------------------------------------------------------------------
    | Dashboard / Pulpit
    |--------------------------------------------------------------------------
    */

    'dashboard' => [
        'title' => 'Pulpit',
        'welcome' => 'Witaj ponownie, :name!',
        'quick_stats' => 'Szybkie statystyki',
        'api_keys_count' => 'Klucze API',
        'today_requests' => 'Dzisiejsze zapytania',
        'month_cost' => 'Koszt miesiąca',
        'recent_activity' => 'Ostatnia aktywność',
        'quick_actions' => 'Szybkie akcje',
        'generate_key' => 'Wygeneruj klucz API',
        'test_api' => 'Testuj API',
        'view_docs' => 'Zobacz dokumentację',
        'no_activity' => 'Brak aktywności. Zacznij od wygenerowania pierwszego klucza API!',
        'onboarding_title' => 'Rozpocznij z Aisello API',
        'onboarding_step_1' => 'Wygeneruj swój pierwszy klucz API',
        'onboarding_step_2' => 'Przetestuj API w playground',
        'onboarding_step_3' => 'Zintegruj ze swoją aplikacją',
    ],

    /*
    |--------------------------------------------------------------------------
    | API Keys / Klucze API
    |--------------------------------------------------------------------------
    */

    'api_keys' => [
        'title' => 'Klucze API',
        'subtitle' => 'Zarządzaj kluczami API do dostępu do usług Aisello',
        'generate' => 'Wygeneruj nowy klucz',
        'list_empty' => 'Brak kluczy API',
        'list_empty_description' => 'Wygeneruj swój pierwszy klucz API aby rozpocząć korzystanie z Aisello API',
        'name' => 'Nazwa klucza',
        'key' => 'Klucz API',
        'status' => 'Status',
        'last_used' => 'Ostatnio użyty',
        'expires_at' => 'Wygasa',
        'created_at' => 'Utworzony',
        'actions' => 'Akcje',
        'active' => 'Aktywny',
        'inactive' => 'Nieaktywny',
        'expired' => 'Wygasły',
        'never_used' => 'Nigdy nie użyty',
        'never_expires' => 'Nigdy nie wygasa',
        'view_details' => 'Zobacz szczegóły',
        'revoke' => 'Unieważnij',
        'copy' => 'Kopiuj',
        'copied' => 'Skopiowano!',

        // Generate form
        'generate_title' => 'Wygeneruj nowy klucz API',
        'generate_description' => 'Utwórz nowy klucz API aby uzyskać dostęp do usług Aisello',
        'name_label' => 'Nazwa klucza',
        'name_placeholder' => 'np. Produkcyjne API, Klucz deweloperski',
        'name_help' => 'Wybierz opisową nazwę aby zidentyfikować ten klucz',
        'expires_label' => 'Data wygaśnięcia (opcjonalnie)',
        'expires_help' => 'Pozostaw puste dla braku wygaśnięcia',
        'generate_button' => 'Wygeneruj klucz',
        'cancel' => 'Anuluj',

        // Generated key modal
        'generated_title' => 'Klucz API wygenerowany pomyślnie',
        'generated_warning' => 'Ważne: Zapisz ten klucz teraz!',
        'generated_description' => 'To jedyny moment, kiedy zobaczysz pełny klucz. Skopiuj go i przechowuj bezpiecznie.',
        'copy_button' => 'Kopiuj do schowka',
        'done_button' => 'Gotowe',

        // Details
        'details_title' => 'Szczegóły klucza API',
        'usage_stats' => 'Statystyki użycia',
        'total_requests' => 'Łączne zapytania',
        'total_tokens' => 'Łączne tokeny',
        'total_cost' => 'Łączny koszt',
        'recent_usage' => 'Ostatnie użycie',
        'endpoint' => 'Endpoint',
        'tokens' => 'Tokeny',
        'cost' => 'Koszt',
        'time' => 'Czas',
        'date' => 'Data',

        // Revoke confirmation
        'revoke_title' => 'Unieważnić klucz API?',
        'revoke_description' => 'Czy na pewno chcesz unieważnić ten klucz? Wszystkie aplikacje go używające przestaną działać natychmiast.',
        'revoke_button' => 'Tak, unieważnij',
        'revoke_success' => 'Klucz API unieważniony pomyślnie',
        'generate_success' => 'Klucz API wygenerowany pomyślnie',
    ],

    /*
    |--------------------------------------------------------------------------
    | API Explorer / Eksplorator API
    |--------------------------------------------------------------------------
    */

    'api_explorer' => [
        'title' => 'Eksplorator API',
        'subtitle' => 'Odkryj i testuj wszystkie dostępne API Aisello',
        'search_placeholder' => 'Szukaj API...',
        'category_all' => 'Wszystkie',
        'category_ai' => 'AI i ML',
        'category_web' => 'Web Scraping',
        'category_data' => 'Przetwarzanie danych',
        'try_it' => 'Wypróbuj',
        'view_docs' => 'Dokumentacja',
        'no_results' => 'Nie znaleziono API',
        'no_results_description' => 'Spróbuj dostosować kryteria wyszukiwania',
    ],

    /*
    |--------------------------------------------------------------------------
    | API Playground / Plac zabaw API
    |--------------------------------------------------------------------------
    */

    'playground' => [
        'title' => 'API Playground',
        'subtitle' => 'Testuj zapytania API w czasie rzeczywistym',
        'back_to_explorer' => 'Powrót do eksploratora',

        // Left panel
        'authentication' => 'Uwierzytelnianie',
        'parameters' => 'Parametry',
        'headers' => 'Nagłówki',
        'body' => 'Treść zapytania',
        'examples' => 'Przykłady',
        'select_key' => 'Wybierz klucz API',
        'select_key_placeholder' => 'Wybierz klucz API...',
        'no_keys' => 'Brak dostępnych kluczy API',
        'no_keys_description' => 'Najpierw wygeneruj klucz API',
        'send_request' => 'Wyślij zapytanie',
        'sending' => 'Wysyłanie...',

        // Right panel
        'response' => 'Odpowiedź',
        'logs' => 'Logi',
        'response_time' => 'Czas odpowiedzi',
        'status_code' => 'Kod statusu',
        'tokens_used' => 'Użyte tokeny',
        'cost_estimate' => 'Koszt',
        'copy_response' => 'Kopiuj odpowiedź',
        'no_response' => 'Brak odpowiedzi',
        'no_response_description' => 'Wyślij zapytanie aby zobaczyć odpowiedź tutaj',

        // Errors
        'error_network' => 'Błąd sieci. Sprawdź swoje połączenie.',
        'error_timeout' => 'Przekroczono limit czasu. Spróbuj ponownie.',
        'error_server' => 'Błąd serwera. Skontaktuj się z supportem.',

        // Progressive loading messages
        'loading' => [
            'analyzing' => 'Analizowanie danych...',
            'collecting' => 'Zbieranie informacji o produkcie...',
            'generating' => 'Generowanie opisu...',
            'polishing' => 'Dopieszczanie treści...',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Usage & Stats / Użycie i statystyki
    |--------------------------------------------------------------------------
    */

    'usage' => [
        'title' => 'Użycie i statystyki',
        'subtitle' => 'Monitoruj użycie API i koszty',
        'period' => 'Okres',
        'today' => 'Dzisiaj',
        'week' => 'Ten tydzień',
        'month' => 'Ten miesiąc',
        'custom' => 'Własny zakres',
        'export' => 'Eksportuj do CSV',

        // Stats cards
        'total_requests' => 'Łączne zapytania',
        'total_tokens' => 'Łączne tokeny',
        'total_cost' => 'Łączny koszt',
        'avg_response_time' => 'Śred. czas odpowiedzi',

        // Charts
        'requests_chart' => 'Zapytania w czasie',
        'cost_chart' => 'Koszty w czasie',
        'endpoints_chart' => 'Najpopularniejsze endpointy',

        // Table
        'logs_title' => 'Logi użycia API',
        'endpoint' => 'Endpoint',
        'api_key' => 'Klucz API',
        'tokens' => 'Tokeny',
        'cost' => 'Koszt',
        'response_time' => 'Czas odpowiedzi',
        'timestamp' => 'Znacznik czasu',
        'no_logs' => 'Brak logów użycia',
        'no_logs_description' => 'Zacznij używać API aby zobaczyć logi tutaj',

        // Filters
        'filter' => 'Filtruj',
        'filter_endpoint' => 'Filtruj po endpointcie',
        'filter_key' => 'Filtruj po kluczu API',
        'filter_status' => 'Filtruj po statusie',
        'clear_filters' => 'Wyczyść filtry',
    ],

    /*
    |--------------------------------------------------------------------------
    | User Prompts / Prompty użytkownika
    |--------------------------------------------------------------------------
    */

    'prompts' => [
        'title' => 'Własne prompty',
        'subtitle' => 'Twórz i zarządzaj własnymi promptami AI',
        'create' => 'Utwórz prompt',
        'create_title' => 'Utwórz nowy prompt',
        'create_description' => 'Zdefiniuj własny szablon promptu dla generacji AI',
        'edit_title' => 'Edytuj prompt',
        'empty' => 'Brak własnych promptów',
        'empty_description' => 'Utwórz swój pierwszy prompt, aby spersonalizować odpowiedzi AI',
        'create_first' => 'Utwórz pierwszy prompt',
        'default' => 'Domyślny',
        'set_default' => 'Ustaw jako domyślny',
        'name_label' => 'Nazwa promptu',
        'name_placeholder' => 'np. Opis produktu SEO, Tekst marketingowy',
        'template_label' => 'Szablon promptu',
        'template_help' => 'Napisz swój szablon promptu. Użyj zmiennych jak {name}, {description} itp.',
        'template_placeholder' => 'Napisz profesjonalny opis produktu dla {name}...',
        'available_variables' => 'Dostępne zmienne',
        'set_as_default' => 'Ustaw jako domyślny prompt',
        'default_help' => 'Ten prompt będzie automatycznie wybrany w playgroundzie API',
        'create_button' => 'Utwórz prompt',
        'update_button' => 'Zaktualizuj prompt',
        'confirm_delete' => 'Czy na pewno chcesz usunąć ten prompt?',
        'create_success' => 'Prompt utworzony pomyślnie',
        'update_success' => 'Prompt zaktualizowany pomyślnie',
        'delete_success' => 'Prompt usunięty pomyślnie',
        'set_default_success' => 'Prompt ustawiony jako domyślny',
    ],

    /*
    |--------------------------------------------------------------------------
    | Common / Wspólne
    |--------------------------------------------------------------------------
    */

    'common' => [
        'created' => 'Utworzono',
        'save' => 'Zapisz',
        'cancel' => 'Anuluj',
        'delete' => 'Usuń',
        'edit' => 'Edytuj',
        'view' => 'Zobacz',
        'close' => 'Zamknij',
        'confirm' => 'Potwierdź',
        'search' => 'Szukaj',
        'filter' => 'Filtruj',
        'export' => 'Eksportuj',
        'import' => 'Importuj',
        'loading' => 'Ładowanie...',
        'no_data' => 'Brak danych',
        'error' => 'Wystąpił błąd',
        'success' => 'Operacja zakończona sukcesem',
        'copied' => 'Skopiowano do schowka',
        'yes' => 'Tak',
        'no' => 'Nie',
        'back' => 'Wstecz',
        'next' => 'Dalej',
        'previous' => 'Poprzedni',
        'page' => 'Strona',
        'of' => 'z',
        'showing' => 'Pokazywanie',
        'to' => 'do',
        'results' => 'wyników',
    ],

];

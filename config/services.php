<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Aisello API Services
    |--------------------------------------------------------------------------
    |
    | Konfiguracja zewnętrznych serwisów używanych przez Aisello API.
    | Configuration for external services used by Aisello API.
    |
    */

    /**
     * Serper.dev - Search API
     * API do wyszukiwania informacji o produktach
     */
    'serper' => [
        'api_key' => env('SERPER_API_KEY'),
        'base_url' => env('SERPER_BASE_URL', 'https://google.serper.dev'),
        'timeout' => (int) env('SERPER_TIMEOUT', 10),
        'search_type' => env('SERPER_SEARCH_TYPE', 'search'), // search, images, news, etc.
        'results_limit' => (int) env('SERPER_RESULTS_LIMIT', 5),
    ],

    /**
     * OpenAI - AI Text Generation
     * API do generowania opisów produktów
     */
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'organization' => env('OPENAI_ORGANIZATION'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'max_tokens' => (int) env('OPENAI_MAX_TOKENS', 1500),
        'temperature' => (float) env('OPENAI_TEMPERATURE', 0.7),
        'top_p' => (float) env('OPENAI_TOP_P', 1.0),
        'frequency_penalty' => (float) env('OPENAI_FREQUENCY_PENALTY', 0.0),
        'presence_penalty' => (float) env('OPENAI_PRESENCE_PENALTY', 0.0),
        'timeout' => (int) env('OPENAI_TIMEOUT', 30),
    ],

    /**
     * Web Scraping Configuration
     * Konfiguracja dla web scrapingu
     */
    'scraping' => [
        'timeout' => (int) env('SCRAPING_TIMEOUT', 15),
        'max_retries' => (int) env('SCRAPING_MAX_RETRIES', 3),
        'retry_delay' => (int) env('SCRAPING_RETRY_DELAY', 1000), // milliseconds
        'user_agents' => [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        ],
    ],

];

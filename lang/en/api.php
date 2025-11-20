<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Language Lines - English
    |--------------------------------------------------------------------------
    |
    | API message translations in English.
    |
    */

    // Authentication
    'auth' => [
        'missing_api_key' => 'Missing API key. Required header: Authorization: Bearer {api_key}',
        'invalid_api_key' => 'Invalid or inactive API key.',
        'inactive_or_expired_key' => 'API key is inactive or expired.',
        'user_inactive' => 'User account is inactive.',
        'unauthenticated' => 'Unauthenticated.',
    ],

    // Rate limiting
    'rate_limit' => [
        'exceeded' => 'API rate limit exceeded. Limit: :limit requests per day.',
    ],

    // Statuses
    'status' => [
        'pending' => 'Pending',
        'processing' => 'Processing',
        'completed' => 'Completed',
        'failed' => 'Failed',
    ],

    // Product descriptions
    'description' => [
        'generated_successfully' => 'Product description generated successfully.',
        'generation_failed' => 'Failed to generate product description.',
        'not_found' => 'Product description not found.',
        'queued_successfully' => 'Description generation task has been queued.',
        'queue_failed' => 'Failed to add task to queue.',
    ],

    // Async generation
    'user_or_key_not_found' => 'User or API key not found.',
    'async_generation_failed' => 'Async description generation failed',

    // Validation
    'validation' => [
        'name_string' => 'Name must be a string.',
        'name_max' => 'Name cannot be longer than 255 characters.',
        'manufacturer_string' => 'Manufacturer must be a string.',
        'price_numeric' => 'Price must be a number.',
        'price_min' => 'Price must be greater than or equal to 0.',
        'prompt_not_found' => 'Selected prompt not found.',
        'external_product_id_string' => 'External product ID must be a string.',
        'external_product_id_max' => 'External product ID cannot be longer than 255 characters.',
    ],
];

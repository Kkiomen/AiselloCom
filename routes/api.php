<?php

use Illuminate\Support\Facades\Route;

/**
 * API Routes - Aisello Product Description Generator
 *
 * Wszystkie routy API są prefixowane przez /api i używają middleware 'api'.
 * Wersjonowanie: /api/v1/...
 */

// API Version 1
Route::prefix('v1')->name('api.v1.')->group(function () {

    /**
     * Publiczne endpointy - rejestracja i logowanie
     * Nie wymagają autentykacji API Key
     */
    // Route::post('auth/register', [AuthController::class, 'register'])
    //     ->name('auth.register');
    // Route::post('auth/login', [AuthController::class, 'login'])
    //     ->name('auth.login');

    /**
     * Chronione endpointy API
     * Wymagają middleware: auth.api.key (autentykacja przez API Key)
     * oraz api.rate.limit (rate limiting per user)
     */
    Route::middleware(['auth.api.key', 'api.rate.limit'])->group(function () {

        /**
         * Generowanie opisów produktów
         * Główna funkcjonalność API
         */
        Route::post('products/generate-description',
            [\App\Http\Controllers\Api\V1\ProductDescriptionController::class, 'generate'])
            ->name('products.generate');

        Route::get('products/descriptions',
            [\App\Http\Controllers\Api\V1\ProductDescriptionController::class, 'index'])
            ->name('products.index');

        Route::get('products/descriptions/{id}',
            [\App\Http\Controllers\Api\V1\ProductDescriptionController::class, 'show'])
            ->name('products.show');

        // Route::delete('products/descriptions/{id}',
        //     [ProductDescriptionController::class, 'destroy'])
        //     ->name('products.destroy');

        /**
         * Zarządzanie kluczami API
         * CRUD dla API keys użytkownika
         */
        // Route::get('api-keys', [ApiKeyController::class, 'index'])
        //     ->name('api-keys.index');

        // Route::post('api-keys', [ApiKeyController::class, 'store'])
        //     ->name('api-keys.store');

        // Route::get('api-keys/{id}', [ApiKeyController::class, 'show'])
        //     ->name('api-keys.show');

        // Route::delete('api-keys/{id}', [ApiKeyController::class, 'destroy'])
        //     ->name('api-keys.destroy');

        // Route::patch('api-keys/{id}/revoke', [ApiKeyController::class, 'revoke'])
        //     ->name('api-keys.revoke');

        /**
         * Zarządzanie customowymi promptami
         * CRUD dla promptów użytkownika
         */
        // Route::get('prompts', [UserPromptController::class, 'index'])
        //     ->name('prompts.index');

        // Route::post('prompts', [UserPromptController::class, 'store'])
        //     ->name('prompts.store');

        // Route::get('prompts/{id}', [UserPromptController::class, 'show'])
        //     ->name('prompts.show');

        // Route::put('prompts/{id}', [UserPromptController::class, 'update'])
        //     ->name('prompts.update');

        // Route::delete('prompts/{id}', [UserPromptController::class, 'destroy'])
        //     ->name('prompts.destroy');

        // Route::patch('prompts/{id}/set-default',
        //     [UserPromptController::class, 'setDefault'])
        //     ->name('prompts.set-default');

        /**
         * Statystyki i logi użycia API
         * Monitoring kosztów i wydajności
         */
        // Route::get('usage/stats', [UsageController::class, 'stats'])
        //     ->name('usage.stats');

        // Route::get('usage/logs', [UsageController::class, 'logs'])
        //     ->name('usage.logs');
    });
});

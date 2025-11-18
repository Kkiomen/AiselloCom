<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

/**
 * App Service Provider
 *
 * Główny dostawca usług aplikacji
 * Main application service provider
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * Rejestruje usługi aplikacji
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * Uruchamia usługi aplikacji
     */
    public function boot(): void
    {
        // Ustawienie locale z sesji / Set locale from session
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        }
    }
}

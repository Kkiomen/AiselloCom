<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware ustawiający locale aplikacji.
 *
 * Odczytuje zapisany język z sesji i ustawia go jako aktywny locale.
 * Reads saved language from session and sets it as active locale.
 */
class SetLocale
{
    /**
     * Obsługa przychodzącego żądania.
     * Handle an incoming request.
     *
     * @param Request $request Żądanie HTTP
     * @param Closure $next Następny middleware w łańcuchu
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Pobierz locale z sesji lub użyj domyślnego / Get locale from session or use default
        $locale = Session::get('locale', config('app.locale', 'en'));

        // Sprawdź czy locale jest obsługiwane / Check if locale is supported
        if (in_array($locale, ['en', 'pl'])) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}

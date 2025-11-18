<?php

namespace App\Http\Middleware;

use App\Models\ApiUsageLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware sprawdzania limitu zapytań API.
 *
 * Weryfikuje czy użytkownik nie przekroczył dziennego limitu zapytań.
 * Limit jest określony w polu user->api_rate_limit.
 *
 * Sprawdza liczbę zapytań wykonanych dzisiaj (od 00:00 do 23:59).
 * Jeśli limit jest przekroczony, zwraca błąd 429 Too Many Requests.
 *
 * Middleware powinien być użyty AFTER AuthenticateApiKey,
 * ponieważ wymaga zalogowanego użytkownika.
 */
class CheckApiRateLimit
{
    /**
     * Obsługuje przychodzące żądanie.
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Pobierz zalogowanego użytkownika
        $user = Auth::user();

        // Jeśli brak użytkownika, middleware auth.api.key nie zadziałał poprawnie
        if (!$user) {
            return response()->json([
                'message' => __('api.auth.unauthenticated'),
                'error' => 'unauthenticated',
            ], 401);
        }

        // Pobierz limit użytkownika
        $rateLimit = $user->api_rate_limit;

        // Policz zapytania dzisiaj (od 00:00)
        $todayRequestsCount = ApiUsageLog::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->count();

        // Sprawdź czy limit został przekroczony
        if ($todayRequestsCount >= $rateLimit) {
            return response()->json([
                'message' => __('api.rate_limit.exceeded', ['limit' => $rateLimit]),
                'error' => 'rate_limit_exceeded',
                'limit' => $rateLimit,
                'used' => $todayRequestsCount,
                'reset_at' => now()->endOfDay()->toIso8601String(),
            ], 429);
        }

        // Dodaj informacje o rate limit do nagłówków odpowiedzi
        $response = $next($request);

        $response->headers->set('X-RateLimit-Limit', $rateLimit);
        $response->headers->set('X-RateLimit-Remaining', max(0, $rateLimit - $todayRequestsCount - 1));
        $response->headers->set('X-RateLimit-Reset', now()->endOfDay()->timestamp);

        return $response;
    }
}

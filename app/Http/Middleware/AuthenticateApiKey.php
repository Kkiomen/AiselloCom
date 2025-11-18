<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware autentykacji przez klucz API.
 *
 * Weryfikuje poprawność klucza API przekazanego w nagłówku Authorization.
 * Format: Authorization: Bearer {api_key}
 *
 * Sprawdza:
 * - Czy klucz istnieje w bazie danych
 * - Czy klucz jest aktywny (is_active = true)
 * - Czy klucz nie wygasł (expires_at > now)
 * - Czy użytkownik jest aktywny (user->is_active = true)
 *
 * Po pomyślnej weryfikacji:
 * - Ustawia użytkownika w Auth::user()
 * - Oznacza klucz jako użyty (last_used_at)
 * - Dodaje klucz do requestu ($request->apiKey)
 */
class AuthenticateApiKey
{
    /**
     * Obsługuje przychodzące żądanie.
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Sprawdź czy to wewnętrzny request z playground (już ma ustawiony apiKey)
        // Check if this is internal request from playground (already has apiKey set)
        if ($request->attributes->has('apiKey')) {
            $apiKey = $request->attributes->get('apiKey');

            // Sprawdź czy klucz jest ważny
            if (!$apiKey->isValid() || !$apiKey->user->is_active) {
                return response()->json([
                    'message' => __('api.auth.invalid_api_key'),
                    'error' => 'invalid_api_key',
                ], 401);
            }

            // Ustaw użytkownika w Auth
            Auth::setUser($apiKey->user);

            // Oznacz klucz jako użyty
            $apiKey->markAsUsed();

            return $next($request);
        }

        // Pobierz klucz API z nagłówka Authorization
        $apiKeyValue = $request->bearerToken();

        // Sprawdź czy klucz został przekazany
        if (!$apiKeyValue) {
            return response()->json([
                'message' => __('api.auth.missing_api_key'),
                'error' => 'missing_api_key',
            ], 401);
        }

        // Znajdź klucz w bazie danych
        // Klucze są hashowane, więc musimy porównać hash
        $apiKey = ApiKey::where('key', hash('sha256', $apiKeyValue))
            ->with('user') // Eager load użytkownika
            ->first();

        // Sprawdź czy klucz istnieje
        if (!$apiKey) {
            return response()->json([
                'message' => __('api.auth.invalid_api_key'),
                'error' => 'invalid_api_key',
            ], 401);
        }

        // Sprawdź czy klucz jest ważny (aktywny i niewygasły)
        if (!$apiKey->isValid()) {
            return response()->json([
                'message' => __('api.auth.inactive_or_expired_key'),
                'error' => 'invalid_api_key',
            ], 401);
        }

        // Sprawdź czy użytkownik jest aktywny
        if (!$apiKey->user->is_active) {
            return response()->json([
                'message' => __('api.auth.user_inactive'),
                'error' => 'user_inactive',
            ], 403);
        }

        // Ustaw użytkownika w Auth
        Auth::setUser($apiKey->user);

        // Dodaj klucz API do requestu (do użycia w kontrolerach/serwisach)
        $request->attributes->set('apiKey', $apiKey);

        // Oznacz klucz jako użyty (aktualizuj last_used_at)
        $apiKey->markAsUsed();

        return $next($request);
    }
}

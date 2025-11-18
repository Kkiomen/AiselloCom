<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * API Key Controller (Web)
 *
 * Kontroler obsługujący zarządzanie kluczami API przez interfejs webowy
 * Controller handling API key management through web interface
 */
class ApiKeyController extends Controller
{
    /**
     * Wyświetla listę kluczy API użytkownika
     * Display user's API keys list
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $apiKeys = Auth::user()
            ->apiKeys()
            ->latest()
            ->paginate(20);

        return view('api-keys.index', compact('apiKeys'));
    }

    /**
     * Wyświetla formularz tworzenia nowego klucza API
     * Display form for creating new API key
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('api-keys.create');
    }

    /**
     * Tworzy nowy klucz API
     * Create new API key
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'expires_at' => 'nullable|date|after:today',
        ]);

        // Generuj klucz API / Generate API key
        $prefix = config('api.key.prefix', 'aic_');
        $length = config('api.key.length', 64);
        $rawKey = $prefix . Str::random($length - strlen($prefix));

        // Utwórz klucz API / Create API key
        $apiKey = Auth::user()->apiKeys()->create([
            'name' => $validated['name'],
            'key' => hash('sha256', $rawKey), // Hashuj klucz / Hash the key
            'expires_at' => $validated['expires_at'] ?? null,
            'is_active' => true,
        ]);

        // Przekieruj z raw key w sesji (pokazany tylko raz) / Redirect with raw key in session (shown only once)
        return redirect()
            ->route('api-keys.show', $apiKey)
            ->with('rawKey', $rawKey)
            ->with('success', __('ui.api_keys.generate_success'));
    }

    /**
     * Wyświetla szczegóły klucza API
     * Display API key details
     *
     * @param  \App\Models\ApiKey  $apiKey
     * @return \Illuminate\View\View
     */
    public function show(ApiKey $apiKey)
    {
        // Sprawdź czy użytkownik jest właścicielem / Check if user is owner
        if ($apiKey->user_id !== Auth::id()) {
            abort(403);
        }

        // Załaduj statystyki użycia / Load usage statistics
        $usageStats = [
            'total_requests' => $apiKey->apiUsageLogs()->count(),
            'total_tokens' => $apiKey->apiUsageLogs()->sum('tokens_used'),
            'total_cost' => $apiKey->apiUsageLogs()->sum('cost'),
        ];

        // Ostatnie użycie (10 ostatnich) / Recent usage (last 10)
        $recentUsage = $apiKey->apiUsageLogs()
            ->latest('created_at')
            ->take(10)
            ->get();

        // Sprawdź czy jest raw key w sesji (nowo utworzony klucz) / Check if there's raw key in session (newly created key)
        $rawKey = session('rawKey');

        return view('api-keys.show', compact('apiKey', 'usageStats', 'recentUsage', 'rawKey'));
    }

    /**
     * Unieważnia (deaktywuje) klucz API
     * Revoke (deactivate) API key
     *
     * @param  \App\Models\ApiKey  $apiKey
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ApiKey $apiKey)
    {
        // Sprawdź czy użytkownik jest właścicielem / Check if user is owner
        if ($apiKey->user_id !== Auth::id()) {
            abort(403);
        }

        // Unieważnij klucz / Revoke the key
        $apiKey->revoke();

        return redirect()
            ->route('api-keys.index')
            ->with('success', __('ui.api_keys.revoke_success'));
    }
}

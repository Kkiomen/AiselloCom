<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * Dashboard Controller
 *
 * Kontroler obsługujący widok pulpitu głównego
 * Controller handling main dashboard view
 */
class DashboardController extends Controller
{
    /**
     * Wyświetla pulpit główny
     * Display the main dashboard
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();

        // Pobierz statystyki / Get statistics
        $stats = [
            // Liczba kluczy API / Number of API keys
            'api_keys_count' => $user->apiKeys()->count(),
            'active_keys_count' => $user->activeApiKeys()->count(),

            // Dzisiejsze zapytania / Today's requests
            'today_requests' => $user->apiUsageLogs()
                ->whereDate('created_at', Carbon::today())
                ->count(),

            // Koszt miesiąca / Month cost
            'month_cost' => $user->apiUsageLogs()
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('cost'),

            // Łączna liczba zapytań / Total requests
            'total_requests' => $user->apiUsageLogs()->count(),

            // Łączne tokeny / Total tokens
            'total_tokens' => $user->apiUsageLogs()->sum('tokens_used'),
        ];

        // Ostatnia aktywność (10 ostatnich wywołań) / Recent activity (last 10 calls)
        $recentActivity = $user->apiUsageLogs()
            ->with('apiKey')
            ->latest('created_at')
            ->take(10)
            ->get();

        // Sprawdź czy to nowy użytkownik (onboarding) / Check if new user (onboarding)
        $isNewUser = $stats['api_keys_count'] === 0;

        return view('dashboard', compact('stats', 'recentActivity', 'isNewUser'));
    }
}

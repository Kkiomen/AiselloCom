<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * Usage Controller
 *
 * Kontroler obsługujący statystyki użycia API
 * Controller handling API usage statistics
 */
class UsageController extends Controller
{
    /**
     * Wyświetla statystyki użycia API
     * Display API usage statistics
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Okres filtrowania / Filter period
        $period = $request->get('period', 'today');
        
        // Ustaw daty w zależności od okresu / Set dates based on period
        $startDate = match($period) {
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            default => Carbon::today(),
        };
        $endDate = Carbon::now()->endOfDay();

        // Pobierz logi użycia z filtrem / Get usage logs with filter
        $logsQuery = $user->apiUsageLogs()
            ->with(['apiKey', 'productDescription'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        $logs = (clone $logsQuery)->latest('created_at')->paginate(50);

        // Statystyki dla wybranego okresu / Statistics for selected period
        $stats = [
            'total_requests' => $logsQuery->count(),
            'total_tokens' => $logsQuery->sum('tokens_used'),
            'total_cost' => $logsQuery->sum('cost'),
            'avg_response_time' => $logsQuery->avg('response_time_ms'),
        ];

        // Dane do wykresu - użycie API na dany dzień pogrupowane według endpointów
        // Chart data - API usage per day grouped by endpoints
        $chartData = $this->getChartData($user, $startDate, $endDate);

        return view('usage.index', compact('logs', 'stats', 'period', 'chartData'));
    }

    /**
     * Pobiera dane do wykresu użycia API
     * Get chart data for API usage
     *
     * @param \App\Models\User $user
     * @param \Carbon\Carbon $startDate
     * @param \Carbon\Carbon $endDate
     * @return array
     */
    private function getChartData($user, $startDate, $endDate)
    {
        // Pobierz wszystkie logi w danym okresie / Get all logs in the period
        $logs = $user->apiUsageLogs()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('created_at', 'endpoint')
            ->get();

        // Przygotuj dane do wykresu / Prepare chart data
        $dates = [];
        $endpoints = [];
        $dataByEndpoint = [];

        // Grupuj dane według daty i endpointu / Group data by date and endpoint
        foreach ($logs as $log) {
            $date = Carbon::parse($log->created_at)->format('Y-m-d');
            
            if (!in_array($date, $dates)) {
                $dates[] = $date;
            }

            if (!in_array($log->endpoint, $endpoints)) {
                $endpoints[] = $log->endpoint;
            }

            if (!isset($dataByEndpoint[$log->endpoint])) {
                $dataByEndpoint[$log->endpoint] = [];
            }

            if (!isset($dataByEndpoint[$log->endpoint][$date])) {
                $dataByEndpoint[$log->endpoint][$date] = 0;
            }

            $dataByEndpoint[$log->endpoint][$date]++;
        }
        
        // Sortuj daty / Sort dates
        sort($dates);

        // Upewnij się, że wszystkie daty są w każdym endpoincie / Ensure all dates are in each endpoint
        $datasets = [];
        $colors = [
            'rgb(99, 102, 241)', // indigo
            'rgb(139, 92, 246)', // purple
            'rgb(236, 72, 153)', // pink
            'rgb(239, 68, 68)',  // red
            'rgb(251, 146, 60)', // orange
            'rgb(250, 204, 21)', // yellow
            'rgb(34, 197, 94)',  // green
            'rgb(59, 130, 246)', // blue
        ];

        foreach ($endpoints as $index => $endpoint) {
            $data = [];
            foreach ($dates as $date) {
                $data[] = $dataByEndpoint[$endpoint][$date] ?? 0;
            }

            $datasets[] = [
                'label' => $endpoint,
                'data' => $data,
                'backgroundColor' => $colors[$index % count($colors)] . '80', // 50% opacity
                'borderColor' => $colors[$index % count($colors)],
                'borderWidth' => 2,
            ];
        }

        return [
            'labels' => array_map(function($date) {
                return Carbon::parse($date)->format('d.m');
            }, $dates),
            'datasets' => $datasets,
        ];
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiUsageLog;
use App\Models\ProductDescription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Kontroler panelu administracyjnego.
 * Admin dashboard controller.
 *
 * Zarządza widokami i danymi panelu administracyjnego.
 */
class AdminDashboardController extends Controller
{
    /**
     * Wyświetla dashboard admina z finansami.
     * Displays admin dashboard with financials.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Statystyki ogólne
        $totalUsers = User::count();
        $totalDescriptions = ProductDescription::count();
        $completedDescriptions = ProductDescription::where('status', 'completed')->count();

        // Koszty i przychody - dzisiaj
        $today = Carbon::today();
        $todayStats = $this->getFinancialStats($today, $today);

        // Koszty i przychody - ten miesiąc
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();
        $monthStats = $this->getFinancialStats($monthStart, $monthEnd);

        // Koszty i przychody - wszystko
        $allTimeStats = $this->getFinancialStats(null, null);

        // Ostatnie generacje (do weryfikacji)
        $recentDescriptions = ProductDescription::with(['user', 'apiKey'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Użycie API per endpoint
        $endpointStats = ApiUsageLog::select('endpoint', DB::raw('COUNT(*) as count'), DB::raw('SUM(cost) as total_cost'))
            ->groupBy('endpoint')
            ->orderBy('count', 'desc')
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalDescriptions',
            'completedDescriptions',
            'todayStats',
            'monthStats',
            'allTimeStats',
            'recentDescriptions',
            'endpointStats'
        ));
    }

    /**
     * Wyświetla listę wszystkich wygenerowanych opisów.
     * Displays list of all generated descriptions.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function descriptions(Request $request)
    {
        $query = ProductDescription::with(['user', 'apiKey', 'apiUsageLog']);

        // Filtrowanie po statusie
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filtrowanie po użytkowniku
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filtrowanie po dacie
        if ($request->has('date') && $request->date) {
            $query->whereDate('created_at', $request->date);
        }

        // Filtrowanie po zakresie dat
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Wyszukiwanie
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereRaw("JSON_EXTRACT(input_data, '$.name') LIKE ?", ["%{$search}%"])
                  ->orWhere('generated_description', 'like', "%{$search}%");
            });
        }

        $descriptions = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        $users = User::orderBy('name')->get();

        return view('admin.descriptions', compact('descriptions', 'users'));
    }

    /**
     * Wyświetla szczegóły pojedynczego opisu.
     * Displays single description details.
     *
     * @param ProductDescription $description
     * @return \Illuminate\View\View
     */
    public function showDescription(ProductDescription $description)
    {
        $description->load(['user', 'apiKey', 'apiUsageLog']);

        return view('admin.description-show', compact('description'));
    }

    /**
     * Wyświetla statystyki finansowe.
     * Displays financial statistics.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function financials(Request $request)
    {
        // Domyślny zakres - ostatnie 30 dni
        $startDate = $request->has('start_date')
            ? Carbon::parse($request->start_date)
            : Carbon::now()->subDays(30);
        $endDate = $request->has('end_date')
            ? Carbon::parse($request->end_date)
            : Carbon::now();

        // Statystyki dla zakresu
        $stats = $this->getFinancialStats($startDate, $endDate);

        // Dane do wykresu - dzienne
        $dailyStats = ApiUsageLog::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(cost) as openai_cost'),
            DB::raw('SUM(COALESCE(serper_cost, 0)) as serper_cost'),
            DB::raw('COUNT(*) as requests')
        )
        ->whereBetween('created_at', [$startDate, $endDate])
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        // Top użytkownicy z pełnymi danymi finansowymi
        $topUsers = ApiUsageLog::select(
            'user_id',
            DB::raw('SUM(cost) as openai_cost'),
            DB::raw('SUM(COALESCE(serper_cost, 0)) as serper_cost'),
            DB::raw('SUM(cost) + SUM(COALESCE(serper_cost, 0)) as total_cost'),
            DB::raw('(SUM(cost) + SUM(COALESCE(serper_cost, 0))) * 3 as revenue'),
            DB::raw('(SUM(cost) + SUM(COALESCE(serper_cost, 0))) * 2 as profit'),
            DB::raw('COUNT(*) as total_requests')
        )
        ->whereBetween('created_at', [$startDate, $endDate])
        ->groupBy('user_id')
        ->orderBy('total_cost', 'desc')
        ->take(10)
        ->with('user')
        ->get();

        return view('admin.financials', compact('stats', 'dailyStats', 'topUsers', 'startDate', 'endDate'));
    }

    /**
     * Pobiera statystyki finansowe dla zakresu dat.
     * Gets financial stats for date range.
     *
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return array
     */
    protected function getFinancialStats(?Carbon $startDate, ?Carbon $endDate): array
    {
        $query = ApiUsageLog::query();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
        }

        $openaiCost = (float) $query->sum('cost');
        $serperCost = (float) $query->sum('serper_cost');
        $totalCost = $openaiCost + $serperCost;
        $totalRequests = $query->count();

        // Przychód - zakładamy marżę 300% (płacimy X, pobieramy 3X)
        // To można później konfigurować
        $revenue = $totalCost * 3;
        $profit = $revenue - $totalCost;
        $margin = $totalCost > 0 ? ($profit / $revenue) * 100 : 0;

        return [
            'openai_cost' => $openaiCost,
            'serper_cost' => $serperCost,
            'total_cost' => $totalCost,
            'revenue' => $revenue,
            'profit' => $profit,
            'margin' => $margin,
            'total_requests' => $totalRequests,
        ];
    }

    /**
     * Lista użytkowników.
     * Users list.
     *
     * @return \Illuminate\View\View
     */
    public function users()
    {
        $users = User::withCount(['apiKeys', 'productDescriptions'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.users', compact('users'));
    }

    /**
     * Toggle admin status for user.
     * Przełącza status admina dla użytkownika.
     *
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleAdmin(User $user)
    {
        $user->is_admin = !$user->is_admin;
        $user->save();

        return back()->with('success', __('admin.user_updated'));
    }
}

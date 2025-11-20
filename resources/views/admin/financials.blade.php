<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ __('admin.detailed_financials') }}</h1>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Zakres dat -->
        <div class="card">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Od</label>
                    <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                        class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Do</label>
                    <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                        class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                </div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    {{ __('admin.filter') }}
                </button>
            </form>
        </div>

        <!-- Statystyki -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="card bg-red-50 dark:bg-red-900/20">
                <p class="text-sm text-red-600">OpenAI</p>
                <p class="text-xl font-bold text-red-700">-${{ number_format($stats['openai_cost'], 4) }}</p>
            </div>
            <div class="card bg-orange-50 dark:bg-orange-900/20">
                <p class="text-sm text-orange-600">Serper</p>
                <p class="text-xl font-bold text-orange-700">-${{ number_format($stats['serper_cost'], 4) }}</p>
            </div>
            <div class="card bg-blue-50 dark:bg-blue-900/20">
                <p class="text-sm text-blue-600">{{ __('admin.revenue') }}</p>
                <p class="text-xl font-bold text-blue-700">+${{ number_format($stats['revenue'], 4) }}</p>
            </div>
            <div class="card bg-green-50 dark:bg-green-900/20">
                <p class="text-sm text-green-600">{{ __('admin.net_profit') }}</p>
                <p class="text-xl font-bold text-green-700">${{ number_format($stats['profit'], 2) }}</p>
            </div>
        </div>

        <!-- Podsumowanie kosztów -->
        <div class="card">
            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Podsumowanie</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Łączny koszt</p>
                    <p class="font-bold text-red-600">-${{ number_format($stats['total_cost'], 4) }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Ilość requestów</p>
                    <p class="font-bold">{{ number_format($stats['total_requests']) }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Marża</p>
                    <p class="font-bold">{{ number_format($stats['margin'], 1) }}%</p>
                </div>
                <div>
                    <p class="text-gray-500">Średni koszt/request</p>
                    <p class="font-bold">${{ $stats['total_requests'] > 0 ? number_format($stats['total_cost'] / $stats['total_requests'], 4) : '0.0000' }}</p>
                </div>
            </div>
        </div>

        <!-- Top użytkownicy -->
        <div class="card">
            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Top {{ __('admin.users') }}</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-3 px-2">{{ __('admin.user') }}</th>
                            <th class="text-right py-3 px-2">Requests</th>
                            <th class="text-right py-3 px-2">OpenAI</th>
                            <th class="text-right py-3 px-2">Serper</th>
                            <th class="text-right py-3 px-2">Nasz koszt</th>
                            <th class="text-right py-3 px-2">Zapłacił</th>
                            <th class="text-right py-3 px-2">Zysk</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topUsers as $usage)
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="py-3 px-2">{{ $usage->user->name ?? 'N/A' }}</td>
                                <td class="py-3 px-2 text-right">{{ number_format($usage->total_requests) }}</td>
                                <td class="py-3 px-2 text-right text-red-600">-${{ number_format($usage->openai_cost, 4) }}</td>
                                <td class="py-3 px-2 text-right text-orange-600">-${{ number_format($usage->serper_cost, 4) }}</td>
                                <td class="py-3 px-2 text-right text-red-700 font-medium">-${{ number_format($usage->total_cost, 4) }}</td>
                                <td class="py-3 px-2 text-right text-blue-600 font-medium">+${{ number_format($usage->revenue, 4) }}</td>
                                <td class="py-3 px-2 text-right text-green-600 font-bold">+${{ number_format($usage->profit, 4) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Dzienne statystyki -->
        @if($dailyStats->count() > 0)
        <div class="card">
            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Dzienne statystyki</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-3 px-2">Data</th>
                            <th class="text-right py-3 px-2">Requests</th>
                            <th class="text-right py-3 px-2">OpenAI</th>
                            <th class="text-right py-3 px-2">Serper</th>
                            <th class="text-right py-3 px-2">Koszt</th>
                            <th class="text-right py-3 px-2">Przychód</th>
                            <th class="text-right py-3 px-2">Zysk</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dailyStats as $day)
                            @php
                                $dayCost = $day->openai_cost + $day->serper_cost;
                                $dayRevenue = $dayCost * 3;
                                $dayProfit = $dayCost * 2;
                            @endphp
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="py-3 px-2">{{ \Carbon\Carbon::parse($day->date)->format('d.m.Y') }}</td>
                                <td class="py-3 px-2 text-right">{{ number_format($day->requests) }}</td>
                                <td class="py-3 px-2 text-right text-red-600">-${{ number_format($day->openai_cost, 4) }}</td>
                                <td class="py-3 px-2 text-right text-orange-600">-${{ number_format($day->serper_cost, 4) }}</td>
                                <td class="py-3 px-2 text-right text-red-700">-${{ number_format($dayCost, 4) }}</td>
                                <td class="py-3 px-2 text-right text-blue-600">+${{ number_format($dayRevenue, 4) }}</td>
                                <td class="py-3 px-2 text-right text-green-600 font-medium">+${{ number_format($dayProfit, 4) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>

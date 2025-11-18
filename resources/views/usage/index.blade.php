<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('ui.usage.title') }}
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('ui.usage.subtitle') }}
            </p>
        </div>
    </x-slot>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="card">
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.usage.total_requests') }}</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ number_format($stats['total_requests']) }}</p>
        </div>
        <div class="card">
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.usage.total_tokens') }}</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ number_format($stats['total_tokens']) }}</p>
        </div>
        <div class="card">
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.usage.total_cost') }}</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">${{ number_format($stats['total_cost'], 4) }}</p>
        </div>
        <div class="card">
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.usage.avg_response_time') }}</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ number_format($stats['avg_response_time']) }}ms</p>
        </div>
    </div>

    <!-- Tabs for period selection -->
    <div class="mb-6">
        <div class="border-b border-gray-200 dark:border-gray-800">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <a href="{{ route('usage.index', ['period' => 'today']) }}" 
                   class="@if($period === 'today') border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    {{ __('ui.usage.today') }}
                </a>
                <a href="{{ route('usage.index', ['period' => 'week']) }}" 
                   class="@if($period === 'week') border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    {{ __('ui.usage.week') }}
                </a>
                <a href="{{ route('usage.index', ['period' => 'month']) }}" 
                   class="@if($period === 'month') border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    {{ __('ui.usage.month') }}
                </a>
            </nav>
        </div>
    </div>

    <!-- Chart -->
    <div class="card mb-8">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
            {{ __('ui.usage.endpoints_chart') }}
        </h3>
        <div class="h-96">
            <canvas id="usageChart"></canvas>
        </div>
    </div>

    <!-- Usage Logs Table -->
    <div class="card">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
            {{ __('ui.usage.logs_title') }}
        </h3>

        @if($logs->isEmpty())
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-gray-600 dark:text-gray-400">{{ __('ui.usage.no_logs') }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">{{ __('ui.usage.no_logs_description') }}</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('ui.usage.endpoint') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('ui.usage.api_key') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('ui.usage.product') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('ui.usage.generated_description') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('ui.usage.status') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('ui.usage.tokens') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('ui.usage.cost') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('ui.usage.response_time') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('ui.usage.timestamp') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                        @foreach($logs as $log)
                            @php
                                $productDescription = $log->productDescription;
                                $productName = $productDescription?->input_data['name'] ?? ($productDescription?->enriched_data['name'] ?? null);
                                $generatedDescription = $productDescription?->generated_description;
                                $status = $productDescription?->status;
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <code class="px-2 py-1 bg-gray-100 dark:bg-gray-800 rounded text-xs">{{ $log->endpoint }}</code>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                    {{ $log->apiKey->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                    @if($productDescription && $productName)
                                        <div class="max-w-xs">
                                            <span class="font-medium">{{ $productName }}</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">{{ __('ui.usage.no_product') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                    <div class="max-w-md">
                                        @if($productDescription && $generatedDescription)
                                            <button 
                                                x-data=""
                                                x-on:click="$dispatch('open-modal', 'description-modal-{{ $log->id }}')"
                                                class="text-left text-indigo-600 dark:text-indigo-400 hover:underline"
                                            >
                                                {{ Str::limit($generatedDescription, 80) }}
                                            </button>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">{{ __('ui.usage.no_description') }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($productDescription && $status)
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                                'processing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                                'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                                'failed' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                            ];
                                            $statusTranslations = [
                                                'pending' => __('ui.usage.status_pending'),
                                                'processing' => __('ui.usage.status_processing'),
                                                'completed' => __('ui.usage.status_completed'),
                                                'failed' => __('ui.usage.status_failed'),
                                            ];
                                            $color = $statusColors[$status->value] ?? $statusColors['pending'];
                                            $label = $statusTranslations[$status->value] ?? $status->value;
                                        @endphp
                                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $color }}">
                                            {{ $label }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">{{ __('ui.usage.no_product') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ number_format($log->tokens_used) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    ${{ number_format($log->cost, 4) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ number_format($log->response_time_ms) }}ms
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                    {{ $log->created_at->diffForHumans() }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="mt-4">
                    {{ $logs->links() }}
                </div>
            @endif
        @endif
    </div>

    <!-- Modals for descriptions -->
    @foreach($logs as $log)
        @php
            $productDescription = $log->productDescription;
            $generatedDescription = $productDescription?->generated_description;
            $productName = $productDescription?->input_data['name'] ?? ($productDescription?->enriched_data['name'] ?? null);
        @endphp
        @if($productDescription && $generatedDescription)
            <x-modal name="description-modal-{{ $log->id }}" maxWidth="4xl">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        {{ __('ui.usage.generated_description') }}
                        @if($productName)
                            <span class="text-sm text-gray-600 dark:text-gray-400"> - {{ $productName }}</span>
                        @endif
                    </h2>
                    <div class="mt-4 mb-6 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg max-h-96 overflow-y-auto">
                        <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-relaxed">{{ $generatedDescription }}</div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button 
                            x-on:click="$dispatch('close-modal', 'description-modal-{{ $log->id }}')"
                            class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors font-medium"
                        >
                            {{ __('ui.usage.close') }}
                        </button>
                    </div>
                </div>
            </x-modal>
        @endif
    @endforeach

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('usageChart');
            if (!ctx) return;

            const chartData = @json($chartData);
            
            // Sprawdź czy są dane do wyświetlenia
            if (!chartData.labels || chartData.labels.length === 0) {
                ctx.parentElement.innerHTML = '<div class="flex items-center justify-center h-96 text-gray-500 dark:text-gray-400">Brak danych do wyświetlenia</div>';
                return;
            }

            const isDarkMode = document.documentElement.classList.contains('dark');
            const textColor = isDarkMode ? 'rgb(209, 213, 219)' : 'rgb(55, 65, 81)';
            const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';

            new Chart(ctx, {
                type: 'line',
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: textColor,
                                usePointStyle: true,
                                padding: 15,
                            },
                        },
                        tooltip: {
                            backgroundColor: isDarkMode ? 'rgba(17, 24, 39, 0.9)' : 'rgba(255, 255, 255, 0.9)',
                            titleColor: textColor,
                            bodyColor: textColor,
                            borderColor: isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)',
                            borderWidth: 1,
                        },
                    },
                    scales: {
                        x: {
                            grid: {
                                color: gridColor,
                            },
                            ticks: {
                                color: textColor,
                            },
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: gridColor,
                            },
                            ticks: {
                                color: textColor,
                                precision: 0,
                            },
                        },
                    },
                },
            });
        });
    </script>
    @endpush
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
            {{ __('admin.dashboard') }}
        </h1>
    </x-slot>

    <div class="space-y-6">
        <!-- Statystyki ogolne -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="card">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('admin.total_users') }}</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalUsers) }}</p>
            </div>
            <div class="card">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('admin.total_descriptions') }}</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalDescriptions) }}</p>
            </div>
            <div class="card">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('admin.completed') }}</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($completedDescriptions) }}</p>
            </div>
        </div>

        <!-- Finanse -->
        <div class="card">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ __('admin.financials') }}</h2>
                <a href="{{ route('admin.financials') }}" class="text-sm text-indigo-600 hover:underline">Szczegoly →</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Dzisiaj -->
                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">{{ __('admin.today') }}</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm">Requestow:</span>
                            <span class="text-sm font-medium">{{ number_format($todayStats['total_requests']) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm">OpenAI:</span>
                            <span class="text-sm font-medium text-red-600">-${{ number_format($todayStats['openai_cost'], 4) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm">Serper:</span>
                            <span class="text-sm font-medium text-orange-600">-${{ number_format($todayStats['serper_cost'], 4) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm">Nasz koszt:</span>
                            <span class="text-sm font-medium text-red-700">-${{ number_format($todayStats['total_cost'], 4) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm">Zaplacili:</span>
                            <span class="text-sm font-medium text-blue-600">+${{ number_format($todayStats['revenue'], 4) }}</span>
                        </div>
                        <div class="flex justify-between border-t pt-2">
                            <span class="text-sm font-medium">Zysk:</span>
                            <span class="text-sm font-bold text-green-600">+${{ number_format($todayStats['profit'], 4) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Ten miesiac -->
                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">{{ __('admin.this_month') }}</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm">Requestow:</span>
                            <span class="text-sm font-medium">{{ number_format($monthStats['total_requests']) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm">OpenAI:</span>
                            <span class="text-sm font-medium text-red-600">-${{ number_format($monthStats['openai_cost'], 4) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm">Serper:</span>
                            <span class="text-sm font-medium text-orange-600">-${{ number_format($monthStats['serper_cost'], 4) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm">Nasz koszt:</span>
                            <span class="text-sm font-medium text-red-700">-${{ number_format($monthStats['total_cost'], 4) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm">Zaplacili:</span>
                            <span class="text-sm font-medium text-blue-600">+${{ number_format($monthStats['revenue'], 4) }}</span>
                        </div>
                        <div class="flex justify-between border-t pt-2">
                            <span class="text-sm font-medium">Zysk:</span>
                            <span class="text-sm font-bold text-green-600">+${{ number_format($monthStats['profit'], 4) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Calkowity -->
                <div class="p-4 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg">
                    <h3 class="text-sm font-medium text-indigo-600 dark:text-indigo-400 mb-3">{{ __('admin.all_time') }}</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm">Requestow:</span>
                            <span class="text-sm font-medium">{{ number_format($allTimeStats['total_requests']) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm">OpenAI:</span>
                            <span class="text-sm font-medium text-red-600">-${{ number_format($allTimeStats['openai_cost'], 4) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm">Serper:</span>
                            <span class="text-sm font-medium text-orange-600">-${{ number_format($allTimeStats['serper_cost'], 4) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm">Nasz koszt:</span>
                            <span class="text-sm font-medium text-red-700">-${{ number_format($allTimeStats['total_cost'], 4) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm">Zaplacili:</span>
                            <span class="text-sm font-medium text-blue-600">+${{ number_format($allTimeStats['revenue'], 4) }}</span>
                        </div>
                        <div class="flex justify-between border-t pt-2">
                            <span class="text-sm font-bold">Zysk netto:</span>
                            <span class="text-lg font-bold text-green-600">+${{ number_format($allTimeStats['profit'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ostatnie generacje -->
        <div class="card">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ __('admin.recent_generations') }}</h2>
                <a href="{{ route('admin.descriptions') }}" class="text-sm text-indigo-600 hover:underline">{{ __('admin.view_all') }}</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-3 px-2">{{ __('admin.user') }}</th>
                            <th class="text-left py-3 px-2">{{ __('admin.product') }}</th>
                            <th class="text-left py-3 px-2">{{ __('admin.status') }}</th>
                            <th class="text-right py-3 px-2">{{ __('admin.cost') }}</th>
                            <th class="text-left py-3 px-2">{{ __('admin.date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentDescriptions as $description)
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="py-3 px-2">{{ $description->user->name ?? 'N/A' }}</td>
                                <td class="py-3 px-2">{{ Str::limit($description->input_data['name'] ?? 'N/A', 30) }}</td>
                                <td class="py-3 px-2">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($description->status->value === 'completed') bg-green-100 text-green-800
                                        @elseif($description->status->value === 'failed') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        {{ $description->status->value }}
                                    </span>
                                </td>
                                <td class="py-3 px-2 text-right">${{ number_format($description->cost ?? 0, 4) }}</td>
                                <td class="py-3 px-2">{{ $description->created_at->format('d.m H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-500">{{ __('admin.no_data') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Nawigacja -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.descriptions') }}" class="card hover:bg-gray-50 dark:hover:bg-gray-800 transition flex items-center gap-3">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span class="font-medium">{{ __('admin.all_descriptions') }}</span>
            </a>
            <a href="{{ route('admin.financials') }}" class="card hover:bg-gray-50 dark:hover:bg-gray-800 transition flex items-center gap-3">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                </svg>
                <span class="font-medium">{{ __('admin.detailed_financials') }}</span>
            </a>
            <a href="{{ route('admin.users') }}" class="card hover:bg-gray-50 dark:hover:bg-gray-800 transition flex items-center gap-3">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197" />
                </svg>
                <span class="font-medium">{{ __('admin.manage_users') }}</span>
            </a>
        </div>
    </div>
</x-app-layout>

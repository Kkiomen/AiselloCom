<x-app-layout>
    {{-- Welcome header --}}
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
            {{ __('ui.dashboard.welcome', ['name' => Auth::user()->name]) }}
        </h1>
    </x-slot>

    {{-- Onboarding for new users --}}
    @if($isNewUser)
        <div class="card mb-8 border-l-4 border-primary-500">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-12 h-12 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                        {{ __('ui.dashboard.onboarding_title') }}
                    </h3>
                    <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300 mb-4">
                        <li>{{ __('ui.dashboard.onboarding_step_1') }}</li>
                        <li>{{ __('ui.dashboard.onboarding_step_2') }}</li>
                        <li>{{ __('ui.dashboard.onboarding_step_3') }}</li>
                    </ol>
                    <a href="{{ route('api-keys.create') }}" class="btn-gradient inline-block">
                        {{ __('ui.dashboard.generate_key') }}
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- Quick Stats --}}
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
            {{ __('ui.dashboard.quick_stats') }}
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- API Keys --}}
            <div class="card hover:scale-105 transition-transform">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.dashboard.api_keys_count') }}</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $stats['active_keys_count'] }}/{{ $stats['api_keys_count'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Today's Requests --}}
            <div class="card hover:scale-105 transition-transform">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.dashboard.today_requests') }}</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ number_format($stats['today_requests']) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Month Cost --}}
            <div class="card hover:scale-105 transition-transform">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.dashboard.month_cost') }}</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-1">${{ number_format($stats['month_cost'], 4) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Total Tokens --}}
            <div class="card hover:scale-105 transition-transform">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.usage.total_tokens') }}</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ number_format($stats['total_tokens']) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Quick Actions --}}
        <div class="lg:col-span-1">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                {{ __('ui.dashboard.quick_actions') }}
            </h2>
            <div class="space-y-3">
                <a href="{{ route('api-keys.create') }}" class="card hover:shadow-lg transition-all flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ __('ui.dashboard.generate_key') }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.api_keys.generate_description') }}</p>
                    </div>
                </a>

                <a href="{{ route('api.explorer') }}" class="card hover:shadow-lg transition-all flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-600 to-teal-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ __('ui.dashboard.test_api') }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.api_explorer.subtitle') }}</p>
                    </div>
                </a>

                <a href="#" class="card hover:shadow-lg transition-all flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-amber-600 to-orange-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ __('ui.dashboard.view_docs') }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.nav.documentation') }}</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    {{ __('ui.dashboard.recent_activity') }}
                </h2>
                <a href="{{ route('usage.index') }}" class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300">
                    {{ __('ui.common.view') }} {{ __('ui.nav.usage') }} →
                </a>
            </div>

            @if($recentActivity->isEmpty())
                <div class="card text-center py-12">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-gray-600 dark:text-gray-400">{{ __('ui.dashboard.no_activity') }}</p>
                </div>
            @else
                <div class="card overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('ui.usage.endpoint') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('ui.usage.api_key') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('ui.usage.tokens') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('ui.usage.cost') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('ui.usage.timestamp') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                                @foreach($recentActivity as $activity)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            <code class="px-2 py-1 bg-gray-100 dark:bg-gray-800 rounded text-xs">{{ $activity->endpoint }}</code>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                            {{ $activity->apiKey->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ number_format($activity->tokens_used) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            ${{ number_format($activity->cost, 4) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

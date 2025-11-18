<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('api-keys.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ $apiKey->name }}
                </h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('ui.api_keys.details_title') }}
                </p>
            </div>
        </div>
    </x-slot>

    <!-- Show RAW key if just generated (only once) -->
    @if($rawKey)
        <div x-data="{ copied: false }" class="mb-8">
            <div class="card border-l-4 border-primary-500">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-12 h-12 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                            {{ __('ui.api_keys.generated_title') }}
                        </h3>
                        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-3 mb-4">
                            <p class="text-sm text-amber-800 dark:text-amber-400 font-medium">
                                <svg class="w-5 h-5 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ __('ui.api_keys.generated_warning') }}
                            </p>
                            <p class="text-sm text-amber-700 dark:text-amber-500 mt-1">
                                {{ __('ui.api_keys.generated_description') }}
                            </p>
                        </div>

                        <div class="relative">
                            <div class="code-block bg-gray-900 dark:bg-black text-green-400 font-mono text-sm break-all pr-20">
                                {{ $rawKey }}
                            </div>
                            <button
                                @click="navigator.clipboard.writeText('{{ $rawKey }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                class="absolute top-3 right-3 btn-secondary">
                                <svg x-show="!copied" class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                <svg x-show="copied" class="w-5 h-5 inline text-green-600" fill="currentColor" viewBox="0 0 20 20" style="display: none;">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span x-text="copied ? '{{ __('ui.api_keys.copied') }}' : '{{ __('ui.api_keys.copy_button') }}'">{{ __('ui.api_keys.copy_button') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Key Info -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Key Details Card -->
            <div class="card">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    {{ __('ui.api_keys.details_title') }}
                </h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.api_keys.name') }}</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $apiKey->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.api_keys.status') }}</dt>
                        <dd class="mt-1">
                            @if($apiKey->isValid())
                                <span class="badge-success">{{ __('ui.api_keys.active') }}</span>
                            @elseif($apiKey->expires_at && $apiKey->expires_at->isPast())
                                <span class="badge-error">{{ __('ui.api_keys.expired') }}</span>
                            @else
                                <span class="badge-warning">{{ __('ui.api_keys.inactive') }}</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.api_keys.created_at') }}</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $apiKey->created_at->format('Y-m-d H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.api_keys.last_used') }}</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $apiKey->last_used_at ? $apiKey->last_used_at->diffForHumans() : __('ui.api_keys.never_used') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.api_keys.expires_at') }}</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $apiKey->expires_at ? $apiKey->expires_at->format('Y-m-d') : __('ui.api_keys.never_expires') }}
                        </dd>
                    </div>
                </dl>

                @if($apiKey->is_active)
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-800">
                        <form method="POST" action="{{ route('api-keys.destroy', $apiKey) }}" onsubmit="return confirm('{{ __('ui.api_keys.revoke_description') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full btn-secondary text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                                {{ __('ui.api_keys.revoke') }}
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Usage Stats Card -->
            <div class="card">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    {{ __('ui.api_keys.usage_stats') }}
                </h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.api_keys.total_requests') }}</span>
                            <span class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ number_format($usageStats['total_requests']) }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.api_keys.total_tokens') }}</span>
                            <span class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ number_format($usageStats['total_tokens']) }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.api_keys.total_cost') }}</span>
                            <span class="text-lg font-bold text-gray-900 dark:text-gray-100">${{ number_format($usageStats['total_cost'], 4) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Usage -->
        <div class="lg:col-span-2">
            <div class="card">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    {{ __('ui.api_keys.recent_usage') }}
                </h3>

                @if($recentUsage->isEmpty())
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-gray-600 dark:text-gray-400">{{ __('ui.api_keys.never_used') }}</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                        {{ __('ui.api_keys.endpoint') }}
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                        {{ __('ui.api_keys.tokens') }}
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                        {{ __('ui.api_keys.cost') }}
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                        {{ __('ui.api_keys.date') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                                @foreach($recentUsage as $usage)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                            <code class="px-2 py-1 bg-gray-100 dark:bg-gray-800 rounded text-xs">{{ $usage->endpoint }}</code>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                            {{ number_format($usage->tokens_used) }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                            ${{ number_format($usage->cost, 4) }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                            {{ $usage->created_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

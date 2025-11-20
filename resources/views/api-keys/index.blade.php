<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ __('ui.api_keys.title') }}
                </h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('ui.api_keys.subtitle') }}
                </p>
            </div>
            <a href="{{ route('api-keys.create') }}" class="btn-gradient mr-3">
                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('ui.api_keys.generate') }}
            </a>
        </div>
    </x-slot>

    @if($apiKeys->isEmpty())
        <!-- Empty state -->
        <div class="card text-center py-16">
            <svg class="w-20 h-20 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                {{ __('ui.api_keys.list_empty') }}
            </h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                {{ __('ui.api_keys.list_empty_description') }}
            </p>
            <a href="{{ route('api-keys.create') }}" class="btn-gradient inline-block">
                {{ __('ui.api_keys.generate') }}
            </a>
        </div>
    @else
        <!-- API Keys list -->
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('ui.api_keys.name') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('ui.api_keys.key') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('ui.api_keys.status') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('ui.api_keys.last_used') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('ui.api_keys.expires_at') }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('ui.api_keys.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                        @foreach($apiKeys as $key)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $key->name }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ __('ui.api_keys.created_at') }}: {{ $key->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <code class="px-2 py-1 bg-gray-100 dark:bg-gray-800 rounded text-xs text-gray-900 dark:text-gray-100">
                                        {{ config('api.key.prefix', 'aic_') }}••••••••••••••••
                                    </code>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($key->isValid())
                                        <span class="badge-success">
                                            {{ __('ui.api_keys.active') }}
                                        </span>
                                    @elseif($key->expires_at && $key->expires_at->isPast())
                                        <span class="badge-error">
                                            {{ __('ui.api_keys.expired') }}
                                        </span>
                                    @else
                                        <span class="badge-warning">
                                            {{ __('ui.api_keys.inactive') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                    {{ $key->last_used_at ? $key->last_used_at->diffForHumans() : __('ui.api_keys.never_used') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                    {{ $key->expires_at ? $key->expires_at->format('Y-m-d') : __('ui.api_keys.never_expires') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('api-keys.show', $key) }}" class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300">
                                            {{ __('ui.api_keys.view_details') }}
                                        </a>
                                        @if($key->is_active)
                                            <form method="POST" action="{{ route('api-keys.destroy', $key) }}" class="inline" onsubmit="return confirm('{{ __('ui.api_keys.revoke_description') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">
                                                    {{ __('ui.api_keys.revoke') }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($apiKeys->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800">
                    {{ $apiKeys->links() }}
                </div>
            @endif
        </div>
    @endif
</x-app-layout>

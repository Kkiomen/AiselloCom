<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('ui.api_explorer.title') }}
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('ui.api_explorer.subtitle') }}
            </p>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($apis as $api)
            <div class="card hover:shadow-xl transition-all cursor-pointer">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <span class="badge-success">
                        {{ ucfirst($api['status']) }}
                    </span>
                </div>

                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                    {{ app()->getLocale() === 'pl' ? $api['name_pl'] : $api['name'] }}
                </h3>

                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    {{ app()->getLocale() === 'pl' ? $api['description_pl'] : $api['description'] }}
                </p>

                <div class="mb-4">
                    <code class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded text-gray-900 dark:text-gray-100">
                        {{ $api['endpoint'] }}
                    </code>
                </div>

                <div class="flex items-center space-x-2">
                    <a href="{{ route('api.playground', $api['slug']) }}" class="btn-gradient flex-1 text-center">
                        {{ __('ui.api_explorer.try_it') }}
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>

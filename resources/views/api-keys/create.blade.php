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
                    {{ __('ui.api_keys.generate_title') }}
                </h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('ui.api_keys.generate_description') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <div class="card">
            <form method="POST" action="{{ route('api-keys.store') }}" class="space-y-6">
                @csrf

                <!-- Key Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">
                        {{ __('ui.api_keys.name_label') }}
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name') }}"
                        placeholder="{{ __('ui.api_keys.name_placeholder') }}"
                        class="input-modern @error('name') border-red-500 @enderror"
                        required
                        autofocus>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @else
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('ui.api_keys.name_help') }}</p>
                    @enderror
                </div>

                <!-- Expiration Date (Optional) -->
                <div>
                    <label for="expires_at" class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">
                        {{ __('ui.api_keys.expires_label') }}
                    </label>
                    <input
                        type="date"
                        name="expires_at"
                        id="expires_at"
                        value="{{ old('expires_at') }}"
                        min="{{ now()->addDay()->format('Y-m-d') }}"
                        class="input-modern @error('expires_at') border-red-500 @enderror">
                    @error('expires_at')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @else
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('ui.api_keys.expires_help') }}</p>
                    @enderror
                </div>

                <!-- Info box -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        <div class="ml-3 text-sm text-blue-800 dark:text-blue-400">
                            <p class="font-medium mb-1">{{ __('ui.api_keys.generated_warning') }}</p>
                            <p>{{ __('ui.api_keys.generated_description') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end space-x-3 pt-4">
                    <a href="{{ route('api-keys.index') }}" class="btn-secondary">
                        {{ __('ui.api_keys.cancel') }}
                    </a>
                    <button type="submit" class="btn-gradient">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('ui.api_keys.generate_button') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

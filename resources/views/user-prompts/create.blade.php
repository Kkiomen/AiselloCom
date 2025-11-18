<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('user-prompts.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ __('ui.prompts.create_title') }}
                </h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('ui.prompts.create_description') }}
                    <span class="inline-flex items-center px-2 py-0.5 ml-2 rounded text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                        {{ $apiType }}
                    </span>
                </p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-3xl">
        <form action="{{ route('user-prompts.store') }}" method="POST" class="card">
            @csrf
            <input type="hidden" name="api_type" value="{{ $apiType }}">
            @if(request('redirect_to'))
                <input type="hidden" name="redirect_to" value="{{ request('redirect_to') }}">
            @endif

            <div class="space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('ui.prompts.name_label') }} <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="input-modern @error('name') border-red-500 @enderror"
                        placeholder="{{ __('ui.prompts.name_placeholder') }}"
                        value="{{ old('name') }}"
                        required
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Prompt Template -->
                <div>
                    <label for="prompt_template" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('ui.prompts.template_label') }} <span class="text-red-500">*</span>
                    </label>
                    <p class="text-xs text-gray-500 mb-2">{{ __('ui.prompts.template_help') }}</p>
                    <textarea
                        name="prompt_template"
                        id="prompt_template"
                        rows="12"
                        class="input-modern font-mono text-sm @error('prompt_template') border-red-500 @enderror"
                        placeholder="{{ __('ui.prompts.template_placeholder') }}"
                        required
                    >{{ old('prompt_template') }}</textarea>
                    @error('prompt_template')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror

                    <!-- Variables Help -->
                    <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <p class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('ui.prompts.available_variables') }}:
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <code class="px-2 py-1 text-xs bg-white dark:bg-gray-900 rounded border border-gray-200 dark:border-gray-700">{{ '{name}' }}</code>
                            <code class="px-2 py-1 text-xs bg-white dark:bg-gray-900 rounded border border-gray-200 dark:border-gray-700">{{ '{manufacturer}' }}</code>
                            <code class="px-2 py-1 text-xs bg-white dark:bg-gray-900 rounded border border-gray-200 dark:border-gray-700">{{ '{price}' }}</code>
                            <code class="px-2 py-1 text-xs bg-white dark:bg-gray-900 rounded border border-gray-200 dark:border-gray-700">{{ '{description}' }}</code>
                            <code class="px-2 py-1 text-xs bg-white dark:bg-gray-900 rounded border border-gray-200 dark:border-gray-700">{{ '{attributes}' }}</code>
                        </div>
                    </div>
                </div>

                <!-- Is Default -->
                <div>
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input
                            type="checkbox"
                            name="is_default"
                            value="1"
                            class="w-4 h-4 text-indigo-600 rounded"
                            {{ old('is_default') ? 'checked' : '' }}
                        >
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            {{ __('ui.prompts.set_as_default') }}
                        </span>
                    </label>
                    <p class="text-xs text-gray-500 mt-1 ml-7">{{ __('ui.prompts.default_help') }}</p>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-800 flex justify-end gap-3">
                <a href="{{ route('user-prompts.index') }}" class="btn-secondary">
                    {{ __('ui.common.cancel') }}
                </a>
                <button type="submit" class="btn-gradient">
                    {{ __('ui.prompts.create_button') }}
                </button>
            </div>
        </form>
    </div>
</x-app-layout>

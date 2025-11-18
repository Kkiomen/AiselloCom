<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ __('ui.prompts.title') }}
                </h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('ui.prompts.subtitle') }}
                </p>
            </div>
            <a href="{{ route('user-prompts.create') }}" class="btn-gradient">
                <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('ui.prompts.create') }}
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if($prompts->isEmpty())
            <div class="card text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                    {{ __('ui.prompts.empty') }}
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    {{ __('ui.prompts.empty_description') }}
                </p>
                <a href="{{ route('user-prompts.create') }}" class="btn-gradient">
                    {{ __('ui.prompts.create_first') }}
                </a>
            </div>
        @else
            @foreach($promptsByApi as $apiType => $apiPrompts)
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                {{ $apiType }}
                            </span>
                            <span class="text-sm font-normal text-gray-500">({{ $apiPrompts->count() }})</span>
                        </h2>
                        <a href="{{ route('user-prompts.create', ['api_type' => $apiType]) }}" class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                            + {{ __('ui.prompts.create') }}
                        </a>
                    </div>
                    <div class="grid gap-4">
                        @foreach($apiPrompts as $prompt)
                            <div class="card hover:shadow-lg transition-shadow">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $prompt->name }}
                                            </h3>
                                            @if($prompt->is_default)
                                                <span class="px-2 py-0.5 text-xs font-semibold rounded bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400">
                                                    {{ __('ui.prompts.default') }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 font-mono">
                                            {{ Str::limit($prompt->prompt_template, 200) }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-2">
                                            {{ __('ui.common.created') }}: {{ $prompt->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2 ml-4">
                                        @if(!$prompt->is_default)
                                            <form action="{{ route('user-prompts.set-default', $prompt) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400" title="{{ __('ui.prompts.set_default') }}">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('user-prompts.edit', $prompt) }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400" title="{{ __('ui.common.edit') }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('user-prompts.destroy', $prompt) }}" method="POST" onsubmit="return confirm('{{ __('ui.prompts.confirm_delete') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400" title="{{ __('ui.common.delete') }}">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</x-app-layout>

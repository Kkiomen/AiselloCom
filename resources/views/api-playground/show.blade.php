<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('api.explorer') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-3">
                        <span class="text-3xl">{{ $apiConfig['icon'] ?? '🚀' }}</span>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                {{ app()->getLocale() === 'pl' ? $apiConfig['name_pl'] : $apiConfig['name'] }}
                            </h1>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ app()->getLocale() === 'pl' ? ($apiConfig['description_pl'] ?? $apiConfig['description']) : $apiConfig['description'] }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Panel -->
        <div class="space-y-6">
            <!-- API Key Card -->
            <div class="card">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('ui.playground.authentication') }}</h3>
                </div>

                @if($apiKeys->isEmpty())
                    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
                        <p class="text-sm text-amber-800 dark:text-amber-400">
                            <strong>{{ __('ui.playground.no_keys') }}</strong><br>
                            {{ __('ui.playground.no_keys_description') }}
                            <a href="{{ route('api-keys.create') }}" class="underline">{{ __('ui.api_keys.generate') }}</a>
                        </p>
                    </div>
                @else
                    <select id="apiKeySelect" class="input-modern" required>
                        <option value="">{{ __('ui.playground.select_key_placeholder') }}</option>
                        @foreach($apiKeys as $key)
                            <option value="{{ $key->id }}">{{ $key->name }}</option>
                        @endforeach
                    </select>
                @endif
            </div>

            @if(!$apiKeys->isEmpty())
            <!-- Parameters Card -->
            <form id="apiForm" class="card">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('ui.playground.parameters') }}</h3>

                <div class="space-y-4">
                    @foreach($apiConfig['fields'] as $field)
                        @php
                            $label = app()->getLocale() === 'pl' ? ($field['label_pl'] ?? $field['label']) : $field['label'];
                            $placeholder = app()->getLocale() === 'pl' ? ($field['placeholder_pl'] ?? $field['placeholder'] ?? '') : ($field['placeholder'] ?? '');
                            $help = app()->getLocale() === 'pl' ? ($field['help_pl'] ?? $field['help'] ?? '') : ($field['help'] ?? '');
                        @endphp

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ $label }}
                                @if($field['required'] ?? false)<span class="text-red-500">*</span>@endif
                            </label>
                            @if($help)<p class="text-xs text-gray-500 mb-2">{{ $help }}</p>@endif

                            @if($field['type'] === 'text')
                                <input type="text" name="{{ $field['name'] }}" class="input-modern" placeholder="{{ $placeholder }}">
                            @elseif($field['type'] === 'number')
                                <input type="number" name="{{ $field['name'] }}" class="input-modern" placeholder="{{ $placeholder }}" step="{{ $field['step'] ?? 'any' }}" min="{{ $field['min'] ?? '' }}">
                            @elseif($field['type'] === 'textarea')
                                <textarea name="{{ $field['name'] }}" class="input-modern" rows="{{ $field['rows'] ?? 4 }}" placeholder="{{ $placeholder }}"></textarea>
                            @elseif($field['type'] === 'tags')
                                <input type="text" name="{{ $field['name'] }}" class="input-modern" placeholder="{{ $placeholder }}">
                            @elseif($field['type'] === 'checkbox')
                                <label class="flex items-center space-x-3">
                                    <input type="checkbox" name="{{ $field['name'] }}" class="w-4 h-4 text-indigo-600 rounded" @if($field['default'] ?? false) checked @endif>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ app()->getLocale() === 'pl' ? 'Włącz' : 'Enable' }}</span>
                                </label>
                            @elseif($field['type'] === 'select')
                                <select name="{{ $field['name'] }}" class="input-modern">
                                    @foreach($field['options'] as $value => $optionLabel)
                                        <option value="{{ $value }}" @if(($field['default'] ?? '') === $value) selected @endif>
                                            {{ $optionLabel }}
                                        </option>
                                    @endforeach
                                </select>
                            @elseif($field['type'] === 'prompt_selector')
                                <div class="flex gap-2">
                                    <select name="{{ $field['name'] }}" id="promptSelector" class="input-modern flex-1">
                                        <option value="">{{ app()->getLocale() === 'pl' ? 'Użyj domyślnego promptu systemowego' : 'Use default system prompt' }}</option>
                                        @foreach($userPrompts as $prompt)
                                            <option value="{{ $prompt->id }}" @if($prompt->is_default) selected @endif>
                                                {{ $prompt->name }} @if($prompt->is_default)({{ app()->getLocale() === 'pl' ? 'Domyślny' : 'Default' }})@endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <a href="{{ route('user-prompts.create', ['api_type' => $slug, 'redirect_to' => request()->url()]) }}" class="btn-secondary px-3" title="{{ app()->getLocale() === 'pl' ? 'Utwórz nowy prompt' : 'Create new prompt' }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </a>
                                </div>
                                <div id="promptPreview" class="hidden mt-2 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <div class="flex justify-between mb-2">
                                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ app()->getLocale() === 'pl' ? 'Podgląd promptu' : 'Prompt Preview' }}</span>
                                        <button type="button" onclick="editPrompt()" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">{{ app()->getLocale() === 'pl' ? 'Edytuj' : 'Edit' }}</button>
                                    </div>
                                    <div id="promptContent" class="text-xs font-mono text-gray-600 dark:text-gray-400 max-h-32 overflow-y-auto whitespace-pre-wrap"></div>
                                </div>
                                @if($userPrompts->isEmpty())
                                    <p class="text-xs text-gray-500 mt-2">
                                        {{ app()->getLocale() === 'pl' ? 'Nie masz własnych promptów.' : "You don't have custom prompts." }}
                                        <a href="{{ route('user-prompts.create', ['api_type' => $slug, 'redirect_to' => request()->url()]) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                            {{ app()->getLocale() === 'pl' ? 'Utwórz pierwszy prompt' : 'Create your first prompt' }}
                                        </a>
                                    </p>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-800">
                    <button type="submit" class="btn-gradient w-full" id="sendButton">
                        <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        {{ __('ui.playground.send_request') }}
                    </button>
                </div>
            </form>
            @endif
        </div>

        <!-- Right Panel - Endpoint & Response -->
        <div class="space-y-6">
            @if(!$apiKeys->isEmpty())
            <!-- Endpoint Info -->
            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Endpoint</h4>
                    <a href="{{ route('api.docs', $slug) }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        {{ app()->getLocale() === 'pl' ? 'Dokumentacja' : 'Documentation' }}
                    </a>
                </div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="px-2 py-0.5 text-xs font-semibold rounded bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400">
                        {{ $apiConfig['method'] }}
                    </span>
                    <code class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded text-gray-900 dark:text-gray-100 flex-1">
                        {{ url($apiConfig['endpoint']) }}
                    </code>
                </div>

                <!-- Request Body (collapsible) -->
                <details class="border border-gray-200 dark:border-gray-700 rounded-lg">
                    <summary class="px-3 py-2 cursor-pointer text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/50 flex items-center justify-between">
                        <span>Request Body</span>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="event.stopPropagation(); copyRequestBody()" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                                {{ app()->getLocale() === 'pl' ? 'Kopiuj' : 'Copy' }}
                            </button>
                        </div>
                    </summary>
                    <div class="border-t border-gray-200 dark:border-gray-700">
                        <pre id="requestBodyPreview" class="text-xs p-3 overflow-x-auto max-h-64 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 font-mono">{}</pre>
                    </div>
                </details>
            </div>
            @endif

            <!-- Product Preview Card -->
            <div class="card" id="productPreviewCard">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        {{ app()->getLocale() === 'pl' ? 'Podgląd opisu' : 'Description Preview' }}
                    </h3>
                    <button id="copyDescriptionBtn" onclick="copyDescription()" class="text-xs bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 px-3 py-1.5 rounded-md hover:bg-indigo-200 dark:hover:bg-indigo-900/50 transition flex items-center gap-1.5 hidden">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        {{ app()->getLocale() === 'pl' ? 'Kopiuj HTML' : 'Copy HTML' }}
                    </button>
                </div>

                <!-- Product info bar -->
                <div class="flex items-center gap-4 mb-4 p-3 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-800/50 rounded-lg">
                    <div class="w-12 h-12 bg-white dark:bg-gray-700 rounded-lg shadow-sm flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 id="previewProductName" class="font-semibold text-gray-900 dark:text-gray-100 truncate">-</h4>
                        <p id="previewProductPrice" class="text-sm text-indigo-600 dark:text-indigo-400 font-bold">-</p>
                    </div>
                </div>

                <!-- Description content area -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900/50">
                    <div id="descriptionPreview" class="p-5 max-h-[500px] overflow-y-auto">
                        <!-- Skeleton loader - initial state -->
                        <div id="descriptionSkeleton">
                            <div class="text-center text-gray-400 dark:text-gray-500 py-12">
                                <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-sm font-medium">{{ app()->getLocale() === 'pl' ? 'Wyślij zapytanie aby wygenerować opis' : 'Send request to generate description' }}</p>
                            </div>
                        </div>
                        <!-- Loading skeleton z progressive messages -->
                        <div id="descriptionLoading" class="hidden">
                            <div class="text-center py-8">
                                <!-- Animated spinner with multiple rings -->
                                <div class="relative w-20 h-20 mx-auto mb-6">
                                    <!-- Outer pulsing ring -->
                                    <div class="absolute inset-0 border-4 border-indigo-200 dark:border-indigo-900/30 rounded-full animate-pulse"></div>
                                    <!-- Main spinning ring -->
                                    <div class="absolute inset-0 border-4 border-transparent border-t-indigo-600 border-r-indigo-600/30 dark:border-t-indigo-400 dark:border-r-indigo-400/30 rounded-full animate-spin"></div>
                                    <!-- Inner counter-spinning ring -->
                                    <div class="absolute inset-2 border-4 border-transparent border-b-indigo-400 border-l-indigo-400/30 dark:border-b-indigo-300 dark:border-l-indigo-300/30 rounded-full animate-spin" style="animation-duration: 1.5s; animation-direction: reverse;"></div>
                                    <!-- Center dot -->
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="w-3 h-3 bg-indigo-600 dark:bg-indigo-400 rounded-full animate-pulse"></div>
                                    </div>
                                </div>
                                <!-- Progressive message with fade transition -->
                                <p id="loadingMessage" class="text-base font-medium text-gray-700 dark:text-gray-300 mb-3 transition-all duration-300">
                                    {{ __('ui.playground.loading.analyzing') }}
                                </p>
                                <!-- Progress dots with bounce animation -->
                                <div class="flex justify-center gap-1 mb-6">
                                    <div id="progressDot1" class="w-2 h-2 rounded-full bg-indigo-600 dark:bg-indigo-400 transition-all duration-300"></div>
                                    <div id="progressDot2" class="w-2 h-2 rounded-full bg-gray-300 dark:bg-gray-600 transition-all duration-300"></div>
                                    <div id="progressDot3" class="w-2 h-2 rounded-full bg-gray-300 dark:bg-gray-600 transition-all duration-300"></div>
                                    <div id="progressDot4" class="w-2 h-2 rounded-full bg-gray-300 dark:bg-gray-600 transition-all duration-300"></div>
                                    <div id="progressDot5" class="w-2 h-2 rounded-full bg-gray-300 dark:bg-gray-600 transition-all duration-300"></div>
                                    <div id="progressDot6" class="w-2 h-2 rounded-full bg-gray-300 dark:bg-gray-600 transition-all duration-300"></div>
                                    <div id="progressDot7" class="w-2 h-2 rounded-full bg-gray-300 dark:bg-gray-600 transition-all duration-300"></div>
                                </div>
                                <!-- Animated skeleton preview -->
                                <div class="space-y-3 mt-4 text-left">
                                    <div class="h-5 bg-gradient-to-r from-gray-200 via-gray-100 to-gray-200 dark:from-gray-700 dark:via-gray-600 dark:to-gray-700 rounded w-3/4 animate-shimmer" style="background-size: 200% 100%;"></div>
                                    <div class="h-3 bg-gradient-to-r from-gray-200 via-gray-100 to-gray-200 dark:from-gray-700 dark:via-gray-600 dark:to-gray-700 rounded w-full animate-shimmer" style="background-size: 200% 100%; animation-delay: 0.1s;"></div>
                                    <div class="h-3 bg-gradient-to-r from-gray-200 via-gray-100 to-gray-200 dark:from-gray-700 dark:via-gray-600 dark:to-gray-700 rounded w-5/6 animate-shimmer" style="background-size: 200% 100%; animation-delay: 0.2s;"></div>
                                    <div class="h-3 bg-gradient-to-r from-gray-200 via-gray-100 to-gray-200 dark:from-gray-700 dark:via-gray-600 dark:to-gray-700 rounded w-full animate-shimmer" style="background-size: 200% 100%; animation-delay: 0.3s;"></div>
                                </div>
                            </div>
                        </div>

                        <style>
                            @keyframes shimmer {
                                0% { background-position: -200% 0; }
                                100% { background-position: 200% 0; }
                            }
                            .animate-shimmer {
                                animation: shimmer 1.5s ease-in-out infinite;
                            }
                        </style>
                        <!-- Actual content -->
                        <div id="descriptionContent" class="hidden prose prose-sm dark:prose-invert max-w-none prose-headings:text-gray-900 dark:prose-headings:text-gray-100 prose-p:text-gray-700 dark:prose-p:text-gray-300 prose-strong:text-gray-900 dark:prose-strong:text-gray-100 prose-ul:text-gray-700 dark:prose-ul:text-gray-300"></div>
                    </div>
                </div>
            </div>

            <!-- API Response Card (Swagger-like) -->
            <div class="card sticky top-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('ui.playground.response') }}</h3>
                <div id="responseArea" class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-gray-600 dark:text-gray-400">{{ __('ui.playground.no_response') }}</p>
                    <p class="text-sm text-gray-500 mt-2">{{ __('ui.playground.no_response_description') }}</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const promptsData = @json($userPrompts);

        // Progressive loading messages
        // Komunikaty progressive loading
        const loadingMessages = [
            '{{ __('ui.playground.loading.analyzing') }}',
            '{{ __('ui.playground.loading.collecting') }}',
            '{{ __('ui.playground.loading.researching') }}',
            '{{ __('ui.playground.loading.structuring') }}',
            '{{ __('ui.playground.loading.generating') }}',
            '{{ __('ui.playground.loading.optimizing') }}',
            '{{ __('ui.playground.loading.polishing') }}'
        ];
        let loadingInterval = null;
        let currentMessageIndex = 0;

        // Funkcja startująca progressive loading
        // Function to start progressive loading
        function startProgressiveLoading() {
            currentMessageIndex = 0;
            updateLoadingMessage();
            loadingInterval = setInterval(() => {
                currentMessageIndex = (currentMessageIndex + 1) % loadingMessages.length;
                updateLoadingMessage();
            }, 2500);
        }

        // Funkcja aktualizująca komunikat i kropki
        // Function to update message and dots
        function updateLoadingMessage() {
            const messageEl = document.getElementById('loadingMessage');
            if (messageEl) {
                // Fade out effect
                messageEl.style.opacity = '0';
                messageEl.style.transform = 'translateY(-5px)';

                setTimeout(() => {
                    messageEl.textContent = loadingMessages[currentMessageIndex];
                    // Fade in effect
                    messageEl.style.opacity = '1';
                    messageEl.style.transform = 'translateY(0)';
                }, 150);
            }
            // Update progress dots with scale animation
            for (let i = 1; i <= 7; i++) {
                const dot = document.getElementById('progressDot' + i);
                if (dot) {
                    if (i <= currentMessageIndex + 1) {
                        dot.classList.remove('bg-gray-300', 'dark:bg-gray-600');
                        dot.classList.add('bg-indigo-600', 'dark:bg-indigo-400');
                        // Scale up active dot
                        if (i === currentMessageIndex + 1) {
                            dot.style.transform = 'scale(1.4)';
                            setTimeout(() => { dot.style.transform = 'scale(1)'; }, 200);
                        }
                    } else {
                        dot.classList.remove('bg-indigo-600', 'dark:bg-indigo-400');
                        dot.classList.add('bg-gray-300', 'dark:bg-gray-600');
                    }
                }
            }
        }

        // Funkcja zatrzymująca progressive loading
        // Function to stop progressive loading
        function stopProgressiveLoading() {
            if (loadingInterval) {
                clearInterval(loadingInterval);
                loadingInterval = null;
            }
        }

        document.getElementById('promptSelector')?.addEventListener('change', function(e) {
            const promptId = e.target.value;
            const previewDiv = document.getElementById('promptPreview');
            const contentDiv = document.getElementById('promptContent');
            if (promptId) {
                const prompt = promptsData.find(p => p.id == promptId);
                if (prompt) {
                    contentDiv.textContent = prompt.prompt_template;
                    previewDiv.classList.remove('hidden');
                }
            } else {
                previewDiv.classList.add('hidden');
            }
        });

        function editPrompt() {
            const promptId = document.getElementById('promptSelector').value;
            if (promptId) {
                const redirectTo = encodeURIComponent(window.location.href);
                window.location.href = '/user-prompts/' + promptId + '/edit?redirect_to=' + redirectTo;
            }
        }

        document.getElementById('apiForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const responseArea = document.getElementById('responseArea');
            const sendButton = document.getElementById('sendButton');
            const apiKeyId = document.getElementById('apiKeySelect').value;

            if (!apiKeyId) {
                responseArea.innerHTML = '<div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4"><p class="text-sm text-red-800 dark:text-red-400">{{ app()->getLocale() === "pl" ? "Wybierz klucz API" : "Please select an API key" }}</p></div>';
                return;
            }

            sendButton.disabled = true;
            sendButton.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2 inline-block" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>{{ __("ui.playground.sending") }}';
            responseArea.innerHTML = '<div class="text-center py-12"><div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div><p class="mt-4 text-gray-600 dark:text-gray-400">{{ __("ui.playground.sending") }}</p></div>';

            // Show loading skeleton in product preview
            const productName = document.querySelector('input[name="product_name"]')?.value || '{{ app()->getLocale() === "pl" ? "Produkt" : "Product" }}';
            const productPrice = document.querySelector('input[name="price"]')?.value;
            document.getElementById('previewProductName').textContent = productName;
            document.getElementById('previewProductPrice').textContent = productPrice ? productPrice + ' PLN' : '-';
            document.getElementById('descriptionSkeleton').classList.add('hidden');
            document.getElementById('descriptionLoading').classList.remove('hidden');
            document.getElementById('descriptionContent').classList.add('hidden');
            document.getElementById('copyDescriptionBtn').classList.add('hidden');

            // Start progressive loading messages
            startProgressiveLoading();

            const formData = new FormData(e.target);
            const requestData = { api_key_id: apiKeyId };
            formData.forEach((value, key) => { if (value !== '') requestData[key] = value; });

            const startTime = Date.now();

            try {
                const response = await fetch('{{ route("api.playground.execute", $apiConfig["slug"]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(requestData)
                });

                const responseTime = Date.now() - startTime;
                const data = await response.json();

                // Stop progressive loading
                stopProgressiveLoading();

                // Show product preview if we have a description
                const descriptionSkeleton = document.getElementById('descriptionSkeleton');
                const descriptionLoading = document.getElementById('descriptionLoading');
                const descriptionContent = document.getElementById('descriptionContent');
                const copyDescriptionBtn = document.getElementById('copyDescriptionBtn');

                if (data.success && data.data?.generated_description) {
                    // Show actual content
                    descriptionContent.innerHTML = data.data.generated_description;
                    descriptionSkeleton.classList.add('hidden');
                    descriptionLoading.classList.add('hidden');
                    descriptionContent.classList.remove('hidden');
                    copyDescriptionBtn.classList.remove('hidden');

                    // Store for copy function
                    window.generatedDescription = data.data.generated_description;
                } else {
                    // Show error state or reset to initial
                    descriptionLoading.classList.add('hidden');
                    descriptionSkeleton.classList.remove('hidden');
                    descriptionContent.classList.add('hidden');
                    copyDescriptionBtn.classList.add('hidden');
                }

                // Swagger-like response format
                let statusClass = response.status >= 400 ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400' : 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400';
                let statusBorderClass = response.status >= 400 ? 'border-red-200 dark:border-red-800' : 'border-green-200 dark:border-green-800';

                let html = '<div class="space-y-3 text-left">';

                // Status header (always visible)
                html += '<div class="flex items-center justify-between">';
                html += '<div class="flex items-center gap-3">';
                html += '<span class="px-2.5 py-1 rounded text-sm font-medium ' + statusClass + '">' + response.status + '</span>';
                html += '<span class="text-sm text-gray-600 dark:text-gray-400">' + response.statusText + '</span>';
                html += '</div>';
                html += '<span class="text-sm text-gray-500">⚡ ' + responseTime + 'ms</span>';
                html += '</div>';

                // Stats section (collapsible)
                if (data.data?.tokens_used || data.tokens_used) {
                    html += '<details class="border border-gray-200 dark:border-gray-700 rounded-lg" open>';
                    html += '<summary class="px-4 py-3 cursor-pointer text-sm font-medium text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800/50 flex items-center justify-between">';
                    html += '<span>{{ app()->getLocale() === "pl" ? "Statystyki" : "Statistics" }}</span>';
                    html += '<svg class="w-4 h-4 text-gray-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>';
                    html += '</summary>';
                    html += '<div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 grid grid-cols-2 gap-4">';
                    html += '<div><p class="text-xs text-gray-500 mb-1">{{ __("ui.playground.tokens_used") }}</p><p class="text-sm font-semibold text-gray-900 dark:text-gray-100">' + (data.data?.tokens_used || data.tokens_used) + '</p></div>';
                    html += '<div><p class="text-xs text-gray-500 mb-1">{{ __("ui.playground.cost_estimate") }}</p><p class="text-sm font-semibold text-gray-900 dark:text-gray-100">$' + (data.data?.cost || data.cost || 0).toFixed(4) + '</p></div>';
                    html += '</div></details>';
                }

                // Response body (collapsible)
                html += '<details class="border border-gray-200 dark:border-gray-700 rounded-lg">';
                html += '<summary class="px-4 py-3 cursor-pointer text-sm font-medium text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800/50 flex items-center justify-between">';
                html += '<span>Response Body</span>';
                html += '<div class="flex items-center gap-2">';
                html += '<button onclick="event.stopPropagation(); copyToClipboard()" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">{{ __("ui.playground.copy_response") }}</button>';
                html += '<svg class="w-4 h-4 text-gray-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>';
                html += '</div></summary>';
                html += '<div class="border-t border-gray-200 dark:border-gray-700">';
                html += '<pre class="p-4 text-xs overflow-x-auto max-h-64 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200" id="responseBody">' + JSON.stringify(data, null, 2) + '</pre>';
                html += '</div></details>';

                html += '</div>';

                responseArea.innerHTML = html;

            } catch (error) {
                // Stop progressive loading on error
                stopProgressiveLoading();

                responseArea.innerHTML = '<div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4"><p class="text-sm text-red-800 dark:text-red-400"><strong>Error:</strong> ' + error.message + '</p></div>';
                // Reset skeleton on error
                document.getElementById('descriptionLoading').classList.add('hidden');
                document.getElementById('descriptionSkeleton').classList.remove('hidden');
                document.getElementById('descriptionContent').classList.add('hidden');
            } finally {
                sendButton.disabled = false;
                sendButton.innerHTML = '<svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>{{ __("ui.playground.send_request") }}';
            }
        });

        function copyToClipboard() {
            navigator.clipboard.writeText(document.getElementById('responseBody').textContent);
        }

        function copyDescription() {
            if (window.generatedDescription) {
                navigator.clipboard.writeText(window.generatedDescription);
                // Show feedback
                const btn = event.target.closest('button');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> {{ app()->getLocale() === "pl" ? "Skopiowano!" : "Copied!" }}';
                setTimeout(() => btn.innerHTML = originalText, 2000);
            }
        }

        // Funkcja aktualizująca podgląd Request Body
        // Function to update Request Body preview
        function updateRequestBodyPreview() {
            const form = document.getElementById('apiForm');
            if (!form) return;

            // Struktura z wszystkimi możliwymi polami (domyślnie null)
            // Structure with all possible fields (default null)
            const requestData = {
                name: null,
                manufacturer: null,
                price: null,
                description: null,
                attributes: null,
                language: null,
                auto_enrich: false,
                user_prompt_id: null
            };

            const formData = new FormData(form);

            // Aktualizuj wartości z formularza
            // Update values from form
            formData.forEach((value, key) => {
                // Mapuj nazwy pól playground na nazwy API
                // Map playground field names to API field names
                if (key === 'product_name') {
                    requestData['name'] = value || null;
                } else if (key === 'product_features') {
                    requestData['description'] = value || null;
                } else if (key === 'manufacturer') {
                    requestData['manufacturer'] = value || null;
                } else if (key === 'price') {
                    requestData['price'] = value ? parseFloat(value) : null;
                } else if (key === 'attributes') {
                    const attrs = value ? value.split(',').map(s => s.trim()).filter(s => s) : null;
                    requestData['attributes'] = attrs && attrs.length > 0 ? attrs : null;
                } else if (key === 'auto_enrich') {
                    requestData['auto_enrich'] = true;
                } else if (key === 'user_prompt_id') {
                    requestData['user_prompt_id'] = value ? parseInt(value) : null;
                } else if (key === 'language') {
                    requestData['language'] = value || null;
                }
            });

            // Sprawdź checkbox auto_enrich (FormData nie zawiera unchecked)
            const autoEnrichCheckbox = form.querySelector('input[name="auto_enrich"]');
            if (autoEnrichCheckbox) {
                requestData['auto_enrich'] = autoEnrichCheckbox.checked;
            }

            const preview = document.getElementById('requestBodyPreview');
            if (preview) {
                preview.textContent = JSON.stringify(requestData, null, 2);
            }
        }

        // Funkcja kopiująca Request Body
        // Function to copy Request Body
        function copyRequestBody() {
            const preview = document.getElementById('requestBodyPreview');
            if (preview) {
                navigator.clipboard.writeText(preview.textContent);
            }
        }

        window.addEventListener('load', function() {
            const sel = document.getElementById('promptSelector');
            if (sel && sel.value) sel.dispatchEvent(new Event('change'));

            // Inicjalizuj podgląd Request Body
            // Initialize Request Body preview
            updateRequestBodyPreview();

            // Dodaj event listenery do wszystkich pól formularza
            // Add event listeners to all form fields
            const form = document.getElementById('apiForm');
            if (form) {
                form.querySelectorAll('input, textarea, select').forEach(field => {
                    field.addEventListener('input', updateRequestBodyPreview);
                    field.addEventListener('change', updateRequestBodyPreview);
                });
            }
        });
    </script>
</x-app-layout>

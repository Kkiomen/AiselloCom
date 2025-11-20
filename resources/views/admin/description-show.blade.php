<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.descriptions') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ __('admin.view_details') }}</h1>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Informacje podstawowe -->
        <div class="card">
            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">{{ __('admin.product') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">ID</p>
                    <p class="font-medium">{{ $description->id }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Request ID</p>
                    <p class="font-medium font-mono text-xs">{{ $description->request_id }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">{{ __('admin.user') }}</p>
                    <p class="font-medium">{{ $description->user->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">{{ __('admin.status') }}</p>
                    <span class="px-2 py-1 text-xs rounded-full
                        @if($description->status->value === 'completed') bg-green-100 text-green-800
                        @elseif($description->status->value === 'failed') bg-red-100 text-red-800
                        @else bg-yellow-100 text-yellow-800
                        @endif">
                        {{ $description->status->value }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">{{ __('admin.date') }}</p>
                    <p class="font-medium">{{ $description->created_at->format('d.m.Y H:i:s') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">{{ __('admin.cost') }}</p>
                    <p class="font-medium">${{ number_format($description->cost ?? 0, 4) }}</p>
                </div>
            </div>
        </div>

        <!-- Dane wejściowe -->
        <div class="card">
            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Dane wejściowe</h2>
            <pre class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg text-sm overflow-x-auto">{{ json_encode($description->input_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>

        <!-- Wzbogacone dane -->
        @if($description->enriched_data)
        <div class="card">
            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Wzbogacone dane</h2>
            <pre class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg text-sm overflow-x-auto">{{ json_encode($description->enriched_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
        @endif

        <!-- Wygenerowany opis -->
        @if($description->generated_description)
        <div class="card">
            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Wygenerowany opis</h2>
            <div class="prose dark:prose-invert max-w-none">
                {!! nl2br(e($description->generated_description)) !!}
            </div>
        </div>
        @endif

        <!-- Błąd -->
        @if($description->error_message)
        <div class="card bg-red-50 dark:bg-red-900/20">
            <h2 class="text-lg font-bold text-red-900 dark:text-red-100 mb-4">Błąd</h2>
            <p class="text-red-700 dark:text-red-300">{{ $description->error_message }}</p>
        </div>
        @endif

        <!-- Metryki -->
        <div class="card">
            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Metryki</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Czas przetwarzania</p>
                    <p class="font-medium">{{ number_format($description->processing_time_ms ?? 0) }} ms</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Tokeny OpenAI</p>
                    <p class="font-medium">{{ number_format($description->tokens_used ?? 0) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Czas odpowiedzi</p>
                    <p class="font-medium">{{ number_format($description->apiUsageLog->response_time_ms ?? 0) }} ms</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Klucz API</p>
                    <p class="font-medium">{{ $description->apiKey->name ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Finanse -->
        @php
            $openaiCost = $description->cost ?? 0;
            $serperCost = $description->apiUsageLog->serper_cost ?? 0;
            $totalCost = $openaiCost + $serperCost;
            $revenue = $totalCost * 3;
            $profit = $totalCost * 2;
        @endphp
        <div class="card">
            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Finanse</h2>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Koszt OpenAI</p>
                    <p class="font-medium text-red-600">-${{ number_format($openaiCost, 4) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Koszt Serper</p>
                    <p class="font-medium text-orange-600">-${{ number_format($serperCost, 4) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Nasz koszt</p>
                    <p class="font-medium text-red-700">-${{ number_format($totalCost, 4) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Klient zapłacił</p>
                    <p class="font-medium text-blue-600">+${{ number_format($revenue, 4) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Nasz zysk</p>
                    <p class="font-bold text-green-600">+${{ number_format($profit, 4) }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

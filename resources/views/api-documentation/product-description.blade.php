<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('api.playground', 'product-description') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ app()->getLocale() === 'pl' ? 'Dokumentacja API' : 'API Documentation' }}
                </h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Product Description Generator
                </p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-12 mt-3">
        <!-- Wprowadzenie / Introduction -->
        <div class="card mt-3">
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                {{ app()->getLocale() === 'pl' ? 'Wprowadzenie' : 'Introduction' }}
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mb-4">
                @if(app()->getLocale() === 'pl')
                    API do generowania profesjonalnych opisów produktów przy użyciu sztucznej inteligencji.
                    Opisy są zoptymalizowane pod SEO i napisane w języku korzyści, który przekonuje do zakupu.
                @else
                    API for generating professional product descriptions using artificial intelligence.
                    Descriptions are SEO-optimized and written in benefit-driven language that converts.
                @endif
            </p>
            <div class="flex items-center gap-4 text-sm">
                <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400 rounded font-medium">
                    POST
                </span>
                <code class="bg-gray-100 dark:bg-gray-800 px-3 py-1 rounded text-gray-900 dark:text-gray-100">
                    /api/v1/products/generate-description
                </code>
            </div>
        </div>

        <!-- Autoryzacja / Authentication -->
        <div class="card mt-3">
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                {{ app()->getLocale() === 'pl' ? 'Autoryzacja' : 'Authentication' }}
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mb-4">
                @if(app()->getLocale() === 'pl')
                    Wszystkie żądania wymagają klucza API w nagłówku <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded">X-API-Key</code>.
                @else
                    All requests require an API key in the <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded">X-API-Key</code> header.
                @endif
            </p>
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                <code class="text-sm text-gray-800 dark:text-gray-200">
                    X-API-Key: aic_your_api_key_here
                </code>
            </div>
        </div>

        <!-- Parametry / Parameters -->
        <div class="card mt-3">
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                {{ app()->getLocale() === 'pl' ? 'Parametry żądania' : 'Request Parameters' }}
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-3 px-2 font-semibold text-gray-900 dark:text-gray-100">{{ app()->getLocale() === 'pl' ? 'Parametr' : 'Parameter' }}</th>
                            <th class="text-left py-3 px-2 font-semibold text-gray-900 dark:text-gray-100">{{ app()->getLocale() === 'pl' ? 'Typ' : 'Type' }}</th>
                            <th class="text-left py-3 px-2 font-semibold text-gray-900 dark:text-gray-100">{{ app()->getLocale() === 'pl' ? 'Wymagany' : 'Required' }}</th>
                            <th class="text-left py-3 px-2 font-semibold text-gray-900 dark:text-gray-100">{{ app()->getLocale() === 'pl' ? 'Opis' : 'Description' }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 dark:text-gray-400">
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="py-3 px-2"><code class="text-indigo-600 dark:text-indigo-400">name</code></td>
                            <td class="py-3 px-2">string</td>
                            <td class="py-3 px-2"><span class="text-gray-400">{{ app()->getLocale() === 'pl' ? 'Nie' : 'No' }}</span></td>
                            <td class="py-3 px-2">{{ app()->getLocale() === 'pl' ? 'Nazwa produktu' : 'Product name' }}</td>
                        </tr>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="py-3 px-2"><code class="text-indigo-600 dark:text-indigo-400">manufacturer</code></td>
                            <td class="py-3 px-2">string</td>
                            <td class="py-3 px-2"><span class="text-gray-400">{{ app()->getLocale() === 'pl' ? 'Nie' : 'No' }}</span></td>
                            <td class="py-3 px-2">{{ app()->getLocale() === 'pl' ? 'Producent lub marka' : 'Manufacturer or brand' }}</td>
                        </tr>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="py-3 px-2"><code class="text-indigo-600 dark:text-indigo-400">price</code></td>
                            <td class="py-3 px-2">number</td>
                            <td class="py-3 px-2"><span class="text-gray-400">{{ app()->getLocale() === 'pl' ? 'Nie' : 'No' }}</span></td>
                            <td class="py-3 px-2">{{ app()->getLocale() === 'pl' ? 'Cena produktu' : 'Product price' }}</td>
                        </tr>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="py-3 px-2"><code class="text-indigo-600 dark:text-indigo-400">description</code></td>
                            <td class="py-3 px-2">string</td>
                            <td class="py-3 px-2"><span class="text-gray-400">{{ app()->getLocale() === 'pl' ? 'Nie' : 'No' }}</span></td>
                            <td class="py-3 px-2">{{ app()->getLocale() === 'pl' ? 'Opis lub cechy produktu' : 'Product description or features' }}</td>
                        </tr>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="py-3 px-2"><code class="text-indigo-600 dark:text-indigo-400">attributes</code></td>
                            <td class="py-3 px-2">array</td>
                            <td class="py-3 px-2"><span class="text-gray-400">{{ app()->getLocale() === 'pl' ? 'Nie' : 'No' }}</span></td>
                            <td class="py-3 px-2">{{ app()->getLocale() === 'pl' ? 'Dodatkowe atrybuty (kolor, rozmiar, itp.)' : 'Additional attributes (color, size, etc.)' }}</td>
                        </tr>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="py-3 px-2"><code class="text-indigo-600 dark:text-indigo-400">language</code></td>
                            <td class="py-3 px-2">string</td>
                            <td class="py-3 px-2"><span class="text-gray-400">{{ app()->getLocale() === 'pl' ? 'Nie' : 'No' }}</span></td>
                            <td class="py-3 px-2">{{ app()->getLocale() === 'pl' ? 'Język opisu (pl, en, de, fr, es, it, cs, sk, uk, ru)' : 'Output language (pl, en, de, fr, es, it, cs, sk, uk, ru)' }}</td>
                        </tr>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="py-3 px-2"><code class="text-indigo-600 dark:text-indigo-400">auto_enrich</code></td>
                            <td class="py-3 px-2">boolean</td>
                            <td class="py-3 px-2"><span class="text-gray-400">{{ app()->getLocale() === 'pl' ? 'Nie' : 'No' }}</span></td>
                            <td class="py-3 px-2">{{ app()->getLocale() === 'pl' ? 'Automatyczne wzbogacenie danych przez AI (domyślnie: true)' : 'Auto-enrich data with AI (default: true)' }}</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-2"><code class="text-indigo-600 dark:text-indigo-400">user_prompt_id</code></td>
                            <td class="py-3 px-2">integer</td>
                            <td class="py-3 px-2"><span class="text-gray-400">{{ app()->getLocale() === 'pl' ? 'Nie' : 'No' }}</span></td>
                            <td class="py-3 px-2">{{ app()->getLocale() === 'pl' ? 'ID własnego promptu (opcjonalnie)' : 'Custom prompt ID (optional)' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Przykład żądania / Request Example -->
        <div class="card mt-3">
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                {{ app()->getLocale() === 'pl' ? 'Przykład żądania' : 'Request Example' }}
            </h2>

            <!-- Tabs -->
            <div x-data="{ tab: 'curl' }" class="space-y-4">
                <div class="flex gap-2 border-b border-gray-200 dark:border-gray-700">
                    <button @click="tab = 'curl'" :class="tab === 'curl' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-4 py-2 text-sm font-medium border-b-2 -mb-px">cURL</button>
                    <button @click="tab = 'php'" :class="tab === 'php' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-4 py-2 text-sm font-medium border-b-2 -mb-px">PHP</button>
                    <button @click="tab = 'js'" :class="tab === 'js' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-4 py-2 text-sm font-medium border-b-2 -mb-px">JavaScript</button>
                    <button @click="tab = 'python'" :class="tab === 'python' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-4 py-2 text-sm font-medium border-b-2 -mb-px">Python</button>
                </div>

                <!-- cURL -->
                <div x-show="tab === 'curl'" class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 overflow-x-auto">
                    <pre class="text-sm text-gray-900 dark:text-gray-100"><code>curl -X POST {{ url('/api/v1/products/generate-description') }} \
  -H "Content-Type: application/json" \
  -H "X-API-Key: aic_your_api_key_here" \
  -d '{
    "name": "Wireless Bluetooth Headphones",
    "manufacturer": "Sony",
    "price": 299.99,
    "description": "Premium noise-canceling headphones",
    "attributes": ["black", "over-ear", "30h battery"],
    "language": "en",
    "auto_enrich": true
  }'</code></pre>
                </div>

                <!-- PHP -->
                <div x-show="tab === 'php'" class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 overflow-x-auto">
                    <pre class="text-sm text-gray-900 dark:text-gray-100"><code>$response = Http::withHeaders([
    'X-API-Key' => 'aic_your_api_key_here',
])->post('{{ url('/api/v1/products/generate-description') }}', [
    'name' => 'Wireless Bluetooth Headphones',
    'manufacturer' => 'Sony',
    'price' => 299.99,
    'description' => 'Premium noise-canceling headphones',
    'attributes' => ['black', 'over-ear', '30h battery'],
    'language' => 'en',
    'auto_enrich' => true,
]);

$data = $response->json();</code></pre>
                </div>

                <!-- JavaScript -->
                <div x-show="tab === 'js'" class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 overflow-x-auto">
                    <pre class="text-sm text-gray-900 dark:text-gray-100"><code>const response = await fetch('{{ url('/api/v1/products/generate-description') }}', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-API-Key': 'aic_your_api_key_here'
  },
  body: JSON.stringify({
    name: 'Wireless Bluetooth Headphones',
    manufacturer: 'Sony',
    price: 299.99,
    description: 'Premium noise-canceling headphones',
    attributes: ['black', 'over-ear', '30h battery'],
    language: 'en',
    auto_enrich: true
  })
});

const data = await response.json();</code></pre>
                </div>

                <!-- Python -->
                <div x-show="tab === 'python'" class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 overflow-x-auto">
                    <pre class="text-sm text-gray-900 dark:text-gray-100"><code>import requests

response = requests.post(
    '{{ url('/api/v1/products/generate-description') }}',
    headers={
        'X-API-Key': 'aic_your_api_key_here'
    },
    json={
        'name': 'Wireless Bluetooth Headphones',
        'manufacturer': 'Sony',
        'price': 299.99,
        'description': 'Premium noise-canceling headphones',
        'attributes': ['black', 'over-ear', '30h battery'],
        'language': 'en',
        'auto_enrich': True
    }
)

data = response.json()</code></pre>
                </div>
            </div>
        </div>

        <!-- Przykład odpowiedzi / Response Example -->
        <div class="card mt-3">
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                {{ app()->getLocale() === 'pl' ? 'Przykład odpowiedzi' : 'Response Example' }}
            </h2>
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 overflow-x-auto">
                <pre class="text-sm text-gray-900 dark:text-gray-100"><code>{
  "success": true,
  "message": "Description generated successfully",
  "data": {
    "generated_description": "&lt;h2&gt;Sony Wireless Bluetooth Headphones&lt;/h2&gt;...",
    "tokens_used": 850,
    "cost": 0.0012
  }
}</code></pre>
            </div>
        </div>

        <!-- Czas przetwarzania / Processing Time -->
        <div class="card mt-3">
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                {{ app()->getLocale() === 'pl' ? 'Czas przetwarzania' : 'Processing Time' }}
            </h2>

            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-lg p-6 mb-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">
                            @if(app()->getLocale() === 'pl')
                                Dlaczego warto poczekać?
                            @else
                                Why is it worth the wait?
                            @endif
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">
                            @if(app()->getLocale() === 'pl')
                                Generowanie opisu trwa średnio <strong>10-20 sekund</strong>. W tym czasie nasz system wykonuje zaawansowane operacje,
                                które gwarantują najwyższą jakość treści - taką, jakiej nie osiągniesz w minutę ręcznego pisania.
                            @else
                                Description generation takes an average of <strong>10-20 seconds</strong>. During this time, our system performs advanced operations
                                that guarantee the highest quality content - quality you couldn't achieve in a minute of manual writing.
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100">
                    @if(app()->getLocale() === 'pl')
                        Co dzieje się w tle:
                    @else
                        What happens behind the scenes:
                    @endif
                </h3>

                <div class="grid gap-4 md:grid-cols-2">
                    <!-- Krok 1 -->
                    <div class="flex items-start gap-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="flex-shrink-0 w-8 h-8 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 rounded-full flex items-center justify-center font-bold text-sm">1</div>
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-gray-100 text-sm">
                                @if(app()->getLocale() === 'pl')
                                    Analiza rynku
                                @else
                                    Market Analysis
                                @endif
                            </h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                @if(app()->getLocale() === 'pl')
                                    Przeszukujemy internet w poszukiwaniu informacji o produkcie i konkurencji
                                @else
                                    We search the internet for product information and competition data
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Krok 2 -->
                    <div class="flex items-start gap-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="flex-shrink-0 w-8 h-8 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 rounded-full flex items-center justify-center font-bold text-sm">2</div>
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-gray-100 text-sm">
                                @if(app()->getLocale() === 'pl')
                                    Wzbogacanie danych
                                @else
                                    Data Enrichment
                                @endif
                            </h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                @if(app()->getLocale() === 'pl')
                                    Uzupełniamy brakujące informacje o specyfikacji i cechach produktu
                                @else
                                    We fill in missing specifications and product features
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Krok 3 -->
                    <div class="flex items-start gap-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="flex-shrink-0 w-8 h-8 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 rounded-full flex items-center justify-center font-bold text-sm">3</div>
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-gray-100 text-sm">
                                @if(app()->getLocale() === 'pl')
                                    Generowanie AI
                                @else
                                    AI Generation
                                @endif
                            </h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                @if(app()->getLocale() === 'pl')
                                    Zaawansowany model AI tworzy unikalny, perswazyjny opis
                                @else
                                    Advanced AI model creates unique, persuasive description
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Krok 4 -->
                    <div class="flex items-start gap-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="flex-shrink-0 w-8 h-8 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 rounded-full flex items-center justify-center font-bold text-sm">4</div>
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-gray-100 text-sm">
                                @if(app()->getLocale() === 'pl')
                                    Optymalizacja SEO
                                @else
                                    SEO Optimization
                                @endif
                            </h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                @if(app()->getLocale() === 'pl')
                                    Optymalizujemy treść pod kątem wyszukiwarek i konwersji
                                @else
                                    We optimize content for search engines and conversions
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                    <p class="text-sm text-green-800 dark:text-green-300">
                        <strong>💡 @if(app()->getLocale() === 'pl')Pro tip:@else Pro tip:@endif</strong>
                        @if(app()->getLocale() === 'pl')
                            Ustaw <code class="bg-green-100 dark:bg-green-900/50 px-1 rounded">auto_enrich: false</code> aby przyspieszyć generowanie do ~5 sekund,
                            jeśli masz już kompletne dane produktu.
                        @else
                            Set <code class="bg-green-100 dark:bg-green-900/50 px-1 rounded">auto_enrich: false</code> to speed up generation to ~5 seconds
                            if you already have complete product data.
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Kody błędów / Error Codes -->
        <div class="card mt-3">
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                {{ app()->getLocale() === 'pl' ? 'Kody błędów' : 'Error Codes' }}
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-3 px-2 font-semibold text-gray-900 dark:text-gray-100">{{ app()->getLocale() === 'pl' ? 'Kod' : 'Code' }}</th>
                            <th class="text-left py-3 px-2 font-semibold text-gray-900 dark:text-gray-100">{{ app()->getLocale() === 'pl' ? 'Opis' : 'Description' }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 dark:text-gray-400">
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="py-3 px-2"><span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400 rounded text-xs font-medium">401</span></td>
                            <td class="py-3 px-2">{{ app()->getLocale() === 'pl' ? 'Nieprawidłowy lub brakujący klucz API' : 'Invalid or missing API key' }}</td>
                        </tr>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="py-3 px-2"><span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400 rounded text-xs font-medium">422</span></td>
                            <td class="py-3 px-2">{{ app()->getLocale() === 'pl' ? 'Błąd walidacji danych' : 'Validation error' }}</td>
                        </tr>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="py-3 px-2"><span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400 rounded text-xs font-medium">429</span></td>
                            <td class="py-3 px-2">{{ app()->getLocale() === 'pl' ? 'Przekroczono limit zapytań' : 'Rate limit exceeded' }}</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-2"><span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400 rounded text-xs font-medium">500</span></td>
                            <td class="py-3 px-2">{{ app()->getLocale() === 'pl' ? 'Błąd serwera' : 'Server error' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Limity / Rate Limits -->
        <div class="card mt-3">
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                {{ app()->getLocale() === 'pl' ? 'Limity' : 'Rate Limits' }}
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mb-4">
                @if(app()->getLocale() === 'pl')
                    Domyślny limit to <strong>100 zapytań dziennie</strong> na klucz API.
                    Limit resetuje się o północy UTC.
                @else
                    Default limit is <strong>100 requests per day</strong> per API key.
                    The limit resets at midnight UTC.
                @endif
            </p>
            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
                <p class="text-sm text-amber-800 dark:text-amber-400">
                    @if(app()->getLocale() === 'pl')
                        <strong>Wskazówka:</strong> Sprawdzaj nagłówki <code>X-RateLimit-Remaining</code> w odpowiedzi aby monitorować pozostałe zapytania.
                    @else
                        <strong>Tip:</strong> Check <code>X-RateLimit-Remaining</code> header in the response to monitor remaining requests.
                    @endif
                </p>
            </div>
        </div>

        <!-- Przycisk do Playground -->
        <div class="text-center mt-3">
            <a href="{{ route('api.playground', 'product-description') }}" class="btn-gradient inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                {{ app()->getLocale() === 'pl' ? 'Wypróbuj w Playground' : 'Try in Playground' }}
            </a>
        </div>
    </div>
</x-app-layout>

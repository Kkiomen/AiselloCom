<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

/**
 * API Playground Controller
 *
 * Kontroler obsługujący playground do testowania API
 * Controller handling API testing playground
 */
class ApiPlaygroundController extends Controller
{
    /**
     * Wyświetla playground dla konkretnego API
     * Display playground for specific API
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function show(string $slug)
    {
        // Konfiguracja API (hardcoded na początek) / API configuration (hardcoded for now)
        $apiConfig = $this->getApiConfig($slug);

        if (!$apiConfig) {
            abort(404, 'API not found');
        }

        // Pobierz aktywne klucze API użytkownika / Get user's active API keys
        $apiKeys = Auth::user()->activeApiKeys()->get();

        // Pobierz prompty użytkownika dla tego API / Get user prompts for this API
        $userPrompts = Auth::user()->userPrompts()->forApi($slug)->get();
        $defaultPrompt = $userPrompts->where('is_default', true)->first();

        return view('api-playground.show', compact('apiConfig', 'apiKeys', 'userPrompts', 'defaultPrompt', 'slug'));
    }

    /**
     * Wykonuje request do API
     * Execute API request
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function execute(Request $request, string $slug)
    {
        // Pobierz konfigurację API / Get API configuration
        $apiConfig = $this->getApiConfig($slug);
        if (!$apiConfig) {
            return response()->json(['error' => 'API not found'], 404);
        }

        // Pobierz klucz API użytkownika / Get user's API key
        $apiKey = Auth::user()
            ->apiKeys()
            ->where('id', $request->api_key_id)
            ->first();

        if (!$apiKey || !$apiKey->isValid()) {
            return response()->json([
                'success' => false,
                'message' => __('api.auth.invalid_api_key')
            ], 401);
        }

        try {
            // Bezpośrednio wywołaj kontroler API z ustalonymi parametrami
            // Directly call API controller with set parameters

            // Mapuj pola z playground na właściwe pola API
            // Map playground fields to actual API fields
            $apiData = [];
            $playgroundData = $request->except('api_key_id');

            // Mapowanie pól / Field mapping
            if (isset($playgroundData['product_name'])) {
                $apiData['name'] = $playgroundData['product_name'];
            }
            if (isset($playgroundData['manufacturer'])) {
                $apiData['manufacturer'] = $playgroundData['manufacturer'];
            }
            if (isset($playgroundData['price'])) {
                $apiData['price'] = $playgroundData['price'];
            }
            if (isset($playgroundData['product_features'])) {
                $apiData['description'] = $playgroundData['product_features'];
            }
            if (isset($playgroundData['attributes'])) {
                // Konwertuj string na array jeśli potrzeba
                // Convert string to array if needed
                if (is_string($playgroundData['attributes'])) {
                    $apiData['attributes'] = array_filter(array_map('trim', explode(',', $playgroundData['attributes'])));
                } else {
                    $apiData['attributes'] = $playgroundData['attributes'];
                }
            }
            if (isset($playgroundData['user_prompt_id'])) {
                $apiData['user_prompt_id'] = $playgroundData['user_prompt_id'];
            }
            if (isset($playgroundData['auto_enrich'])) {
                $apiData['auto_enrich'] = filter_var($playgroundData['auto_enrich'], FILTER_VALIDATE_BOOLEAN);
            }
            if (isset($playgroundData['language'])) {
                $apiData['language'] = $playgroundData['language'];
            }

            // Utwórz request z prawidłowymi polami
            // Create request with correct fields
            $baseRequest = Request::create(
                $apiConfig['endpoint'],
                $apiConfig['method'],
                $apiData,
                [], // cookies
                [], // files
                $request->server->all(), // server
                null // content
            );

            // Utwórz Form Request i ustaw zależności
            // Create Form Request and set dependencies
            $apiRequest = \App\Http\Requests\Api\V1\GenerateDescriptionRequest::createFrom($baseRequest);
            $apiRequest->setContainer(app());
            $apiRequest->setRedirector(app('redirect'));

            // Ustaw użytkownika i klucz API w request
            // Set user and API key in request
            $apiRequest->attributes->set('apiKey', $apiKey);
            $apiRequest->setUserResolver(function () use ($apiKey) {
                return $apiKey->user;
            });
            Auth::setUser($apiKey->user);

            // Waliduj request / Validate request
            $validator = Validator::make($apiData, $apiRequest->rules(), $apiRequest->messages());

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => __('api.validation.failed'),
                    'errors' => $validator->errors()
                ], 422);
            }

            // Ustaw walidator na Form Request, żeby validated() działało
            // Set validator on Form Request so validated() works
            $apiRequest->setValidator($validator);

            // Wywołaj kontroler API
            // Call API controller
            $controller = app()->make(\App\Http\Controllers\Api\V1\ProductDescriptionController::class);
            $response = $controller->generate($apiRequest);

            // Zwróć response
            return $response;

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Obsługa błędów walidacji / Handle validation errors
            return response()->json([
                'success' => false,
                'message' => __('api.validation.failed'),
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('API Playground execution error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('api.description.generation_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Pobiera konfigurację API na podstawie slug
     * Get API configuration based on slug
     *
     * @param  string  $slug
     * @return array|null
     */
    private function getApiConfig(string $slug): ?array
    {
        $configs = [
            'product-description' => [
                'slug' => 'product-description',
                'name' => 'Product Description Generator',
                'name_pl' => 'Generator Opisów Produktów',
                'description' => 'Generate professional, SEO-optimized product descriptions using AI',
                'description_pl' => 'Generuj profesjonalne opisy produktów zoptymalizowane pod SEO przy użyciu AI',
                'endpoint' => '/api/v1/products/generate-description',
                'method' => 'POST',
                'icon' => '📝',
                'fields' => [
                    [
                        'name' => 'product_name',
                        'label' => 'Product Name',
                        'label_pl' => 'Nazwa produktu',
                        'type' => 'text',
                        'required' => false,
                        'placeholder' => 'e.g., Wireless Bluetooth Headphones',
                        'placeholder_pl' => 'np. Bezprzewodowe słuchawki Bluetooth',
                        'help' => 'The name of your product',
                        'help_pl' => 'Nazwa Twojego produktu',
                    ],
                    [
                        'name' => 'manufacturer',
                        'label' => 'Manufacturer / Brand',
                        'label_pl' => 'Producent / Marka',
                        'type' => 'text',
                        'required' => false,
                        'placeholder' => 'e.g., Sony, Apple, Samsung',
                        'placeholder_pl' => 'np. Sony, Apple, Samsung',
                        'help' => 'Product manufacturer or brand name',
                        'help_pl' => 'Producent lub marka produktu',
                    ],
                    [
                        'name' => 'price',
                        'label' => 'Price',
                        'label_pl' => 'Cena',
                        'type' => 'number',
                        'required' => false,
                        'placeholder' => '99.99',
                        'help' => 'Product price (optional, for context)',
                        'help_pl' => 'Cena produktu (opcjonalna, dla kontekstu)',
                        'step' => '0.01',
                        'min' => '0',
                    ],
                    [
                        'name' => 'product_features',
                        'label' => 'Product Description / Features',
                        'label_pl' => 'Opis / Cechy produktu',
                        'type' => 'textarea',
                        'required' => false,
                        'placeholder' => 'Describe key features, specifications, benefits...',
                        'placeholder_pl' => 'Opisz kluczowe cechy, specyfikacje, korzyści...',
                        'help' => 'Existing description or bullet points of features',
                        'help_pl' => 'Istniejący opis lub punkty z cechami',
                        'rows' => 4,
                    ],
                    [
                        'name' => 'attributes',
                        'label' => 'Additional Attributes',
                        'label_pl' => 'Dodatkowe atrybuty',
                        'type' => 'tags',
                        'required' => false,
                        'placeholder' => 'color, size, material, warranty',
                        'placeholder_pl' => 'kolor, rozmiar, materiał, gwarancja',
                        'help' => 'Key product attributes (comma-separated)',
                        'help_pl' => 'Kluczowe atrybuty produktu (oddzielone przecinkami)',
                    ],
                    [
                        'name' => 'user_prompt_id',
                        'label' => 'Custom Prompt',
                        'label_pl' => 'Własny prompt',
                        'type' => 'prompt_selector',
                        'required' => false,
                        'help' => 'Choose a custom prompt template or use default',
                        'help_pl' => 'Wybierz własny szablon promptu lub użyj domyślnego',
                    ],
                    [
                        'name' => 'auto_enrich',
                        'label' => 'Auto-enrich with AI',
                        'label_pl' => 'Automatyczne wzbogacenie AI',
                        'type' => 'checkbox',
                        'required' => false,
                        'help' => 'Let AI add relevant details and improvements',
                        'help_pl' => 'Pozwól AI dodać istotne szczegóły i ulepszenia',
                        'default' => true,
                    ],
                    [
                        'name' => 'language',
                        'label' => 'Output Language',
                        'label_pl' => 'Język opisu',
                        'type' => 'select',
                        'required' => false,
                        'help' => 'Language in which the description will be generated',
                        'help_pl' => 'Język w jakim zostanie wygenerowany opis',
                        'default' => 'pl',
                        'options' => [
                            'pl' => 'Polski',
                            'en' => 'English',
                            'de' => 'Deutsch',
                            'fr' => 'Français',
                            'es' => 'Español',
                            'it' => 'Italiano',
                            'cs' => 'Čeština',
                            'sk' => 'Slovenčina',
                            'uk' => 'Українська',
                            'ru' => 'Русский',
                        ],
                    ],
                ],
            ],
        ];

        return $configs[$slug] ?? null;
    }
}

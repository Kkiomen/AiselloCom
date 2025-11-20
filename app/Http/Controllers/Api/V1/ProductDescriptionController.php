<?php

namespace App\Http\Controllers\Api\V1;

use App\DTO\ProductInputDTO;
use App\Enums\ProductDescriptionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\GenerateDescriptionAsyncRequest;
use App\Http\Requests\Api\V1\GenerateDescriptionRequest;
use App\Http\Resources\Api\V1\ProductDescriptionResource;
use App\Jobs\GenerateProductDescriptionJob;
use App\Models\ProductDescription;
use App\Services\ProductDescription\ProductDescriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Kontroler opisów produktów.
 * Product descriptions controller.
 *
 * Obsługuje endpointy związane z generowaniem opisów produktów.
 */
class ProductDescriptionController extends Controller
{
    /**
     * Konstruktor.
     */
    public function __construct(
        protected ProductDescriptionService $productDescriptionService
    ) {
    }

    /**
     * Generuje opis produktu.
     * Generates product description.
     *
     * @param GenerateDescriptionRequest $request
     * @return JsonResponse
     */
    public function generate(GenerateDescriptionRequest $request): JsonResponse
    {
        try {
            // Pobierz użytkownika i API key z requestu
            $user = Auth::user();
            $apiKey = $request->attributes->get('apiKey');

            // Przygotuj DTO z danych wejściowych
            $inputDTO = ProductInputDTO::fromArray($request->validated());

            // Generuj opis
            $productDescription = $this->productDescriptionService->generate(
                $inputDTO,
                $user,
                $apiKey
            );

            // Zwróć odpowiedź
            return response()->json([
                'success' => true,
                'message' => __('api.description.generated_successfully'),
                'data' => new ProductDescriptionResource($productDescription),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('api.description.generation_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Lista wygenerowanych opisów użytkownika.
     * List of user's generated descriptions.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();

        $descriptions = $user->productDescriptions()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => ProductDescriptionResource::collection($descriptions),
            'meta' => [
                'current_page' => $descriptions->currentPage(),
                'total' => $descriptions->total(),
                'per_page' => $descriptions->perPage(),
            ],
        ]);
    }

    /**
     * Szczegóły pojedynczego opisu.
     * Single description details.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $user = Auth::user();

        $description = $user->productDescriptions()->find($id);

        if (!$description) {
            return response()->json([
                'success' => false,
                'message' => __('api.description.not_found'),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new ProductDescriptionResource($description),
        ]);
    }

    /**
     * Asynchronicznie generuje opis produktu.
     * Asynchronously generates product description.
     *
     * Tworzy rekord ze statusem PENDING i dodaje zadanie do kolejki.
     *
     * @param GenerateDescriptionAsyncRequest $request
     * @return JsonResponse
     */
    public function generateAsync(GenerateDescriptionAsyncRequest $request): JsonResponse
    {
        try {
            // Pobierz użytkownika i API key z requestu
            $user = Auth::user();
            $apiKey = $request->attributes->get('apiKey');

            // Przygotuj DTO z danych wejściowych
            $inputDTO = ProductInputDTO::fromArray($request->validated());

            // Utwórz rekord opisu ze statusem PENDING
            $productDescription = ProductDescription::create([
                'user_id' => $user->id,
                'api_key_id' => $apiKey->id,
                'request_id' => (string) Str::uuid(),
                'external_product_id' => $inputDTO->externalProductId,
                'input_data' => $inputDTO->toArray(),
                'status' => ProductDescriptionStatus::PENDING,
            ]);

            // Dodaj zadanie do kolejki
            GenerateProductDescriptionJob::dispatch(
                $productDescription->id,
                $user->id,
                $apiKey->id
            );

            // Zwróć odpowiedź z informacją o utworzeniu zadania
            return response()->json([
                'success' => true,
                'message' => __('api.description.queued_successfully'),
                'data' => [
                    'id' => $productDescription->id,
                    'request_id' => $productDescription->request_id,
                    'external_product_id' => $productDescription->external_product_id,
                    'status' => $productDescription->status->value,
                    'status_label' => $productDescription->status->label(),
                ],
            ], 202);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('api.description.queue_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Pobiera status asynchronicznego generowania.
     * Gets async generation status.
     *
     * @param string $requestId
     * @return JsonResponse
     */
    public function getAsyncStatus(string $requestId): JsonResponse
    {
        $user = Auth::user();

        $description = $user->productDescriptions()
            ->where('request_id', $requestId)
            ->first();

        if (!$description) {
            return response()->json([
                'success' => false,
                'message' => __('api.description.not_found'),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new ProductDescriptionResource($description),
        ]);
    }

    /**
     * Pobiera opisy według zewnętrznego ID produktu.
     * Gets descriptions by external product ID.
     *
     * @param string $externalProductId
     * @return JsonResponse
     */
    public function getByExternalProductId(string $externalProductId): JsonResponse
    {
        $user = Auth::user();

        $descriptions = $user->productDescriptions()
            ->byExternalProductId($externalProductId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => ProductDescriptionResource::collection($descriptions),
            'meta' => [
                'current_page' => $descriptions->currentPage(),
                'total' => $descriptions->total(),
                'per_page' => $descriptions->perPage(),
            ],
        ]);
    }
}

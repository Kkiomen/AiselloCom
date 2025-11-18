<?php

namespace App\Http\Controllers\Api\V1;

use App\DTO\ProductInputDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\GenerateDescriptionRequest;
use App\Http\Resources\Api\V1\ProductDescriptionResource;
use App\Services\ProductDescription\ProductDescriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

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
}

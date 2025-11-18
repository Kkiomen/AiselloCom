<?php

namespace App\Services\ProductDescription;

use App\DTO\EnrichedProductDTO;
use App\DTO\GeneratedDescriptionDTO;
use App\Models\User;
use App\Services\AI\OpenAIService;
use App\Services\AI\PromptBuilderService;

/**
 * Serwis generowania opisów.
 * Service for description generation.
 *
 * Generuje opisy produktów używając AI.
 */
class DescriptionGeneratorService
{
    /**
     * Konstruktor.
     */
    public function __construct(
        protected OpenAIService $openAIService,
        protected PromptBuilderService $promptBuilder
    ) {
    }

    /**
     * Generuje opis produktu.
     * Generates product description.
     *
     * @param EnrichedProductDTO $product
     * @param User $user
     * @param string $language Język docelowy opisu
     * @return GeneratedDescriptionDTO
     */
    public function generate(EnrichedProductDTO $product, User $user, string $language = 'pl'): GeneratedDescriptionDTO
    {
        // Zbuduj prompt
        $prompt = $this->promptBuilder->build($product, $user, $language);

        // Generuj opis przez OpenAI
        return $this->openAIService->complete($prompt);
    }
}

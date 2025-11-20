<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource dla opisu produktu.
 * Resource for product description.
 */
class ProductDescriptionResource extends JsonResource
{
    /**
     * Przekształca resource do tablicy.
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'request_id' => $this->request_id,
            'external_product_id' => $this->external_product_id,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),

            'input_data' => $this->input_data,
            'enriched_data' => $this->enriched_data,
            'generated_description' => $this->generated_description,

            'processing_time_ms' => $this->processing_time_ms,
            'tokens_used' => $this->tokens_used,
            'cost' => $this->cost ? (float) $this->cost : null,

            'error_message' => $this->error_message,

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

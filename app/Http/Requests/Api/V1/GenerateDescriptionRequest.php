<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request walidacji dla generowania opisu.
 * Validation request for description generation.
 */
class GenerateDescriptionRequest extends FormRequest
{
    /**
     * Określa czy użytkownik może wykonać ten request.
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Autoryzacja przez middleware
    }

    /**
     * Reguły walidacji.
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:5000',
            'attributes' => 'nullable|array',
            'attributes.*' => 'string',
            'user_prompt_id' => 'nullable|integer|exists:user_prompts,id',
            'auto_enrich' => 'nullable|boolean',
            'language' => 'nullable|string|max:50',
        ];
    }

    /**
     * Komunikaty walidacji.
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.string' => __('api.validation.name_string'),
            'name.max' => __('api.validation.name_max'),
            'manufacturer.string' => __('api.validation.manufacturer_string'),
            'price.numeric' => __('api.validation.price_numeric'),
            'price.min' => __('api.validation.price_min'),
            'user_prompt_id.exists' => __('api.validation.prompt_not_found'),
        ];
    }
}

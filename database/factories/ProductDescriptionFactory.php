<?php

namespace Database\Factories;

use App\Enums\ProductDescriptionStatus;
use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductDescription>
 */
class ProductDescriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'api_key_id' => function (array $attributes) {
                return ApiKey::factory()->create([
                    'user_id' => $attributes['user_id'],
                ]);
            },
            'request_id' => (string) Str::uuid(),
            'input_data' => [
                'name' => fake()->words(3, true),
                'manufacturer' => fake()->company(),
                'price' => fake()->randomFloat(2, 10, 1000),
            ],
            'enriched_data' => [
                'name' => fake()->words(3, true),
                'manufacturer' => fake()->company(),
                'price' => fake()->randomFloat(2, 10, 1000),
                'description' => fake()->sentence(),
                'sources' => [],
            ],
            'generated_description' => fake()->paragraph(),
            'prompt_used' => 'Default prompt',
            'status' => ProductDescriptionStatus::COMPLETED,
            'processing_time_ms' => fake()->numberBetween(1000, 5000),
            'tokens_used' => fake()->numberBetween(100, 1000),
            'cost' => fake()->randomFloat(4, 0.0001, 0.01),
            'error_message' => null,
        ];
    }

    /**
     * Indicate that the description is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProductDescriptionStatus::PENDING,
            'generated_description' => null,
            'processing_time_ms' => null,
            'tokens_used' => null,
            'cost' => null,
        ]);
    }

    /**
     * Indicate that the description failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProductDescriptionStatus::FAILED,
            'error_message' => fake()->sentence(),
        ]);
    }
}

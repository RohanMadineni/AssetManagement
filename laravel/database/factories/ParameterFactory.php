<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Parameter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Parameter>
 */
class ParameterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name'=> fake()->unique()->words(2, true),	
            'data_type'=> fake()->randomElement([
                'string',
                'number',
                'date',
                'boolean',
            ]),
            'is_required' => false,
        ];
    }
}

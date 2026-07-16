<?php

// database/factories/CategoryFactory.php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        // Believable IT and corporate hardware departments
        $categories = [
            'Laptops' => 'Portable workstations, company notebooks, and employee developer laptops.',
            'Monitors' => 'Desktop displays, dual screen layouts, and high-resolution presentation monitors.',
            'Headphones' => 'Keyboards, wireless mice, webcams, docking stations, and audio equipment.',
            'Mobile Devices' => 'Company-issued smartphones, testing tablets, and mobile cellular assets.',
        ];

        // Randomly selects a pairing to guarantee name-description alignment
        $chosenName = fake()->unique()->randomElement(array_keys($categories));

        return [
            'name' => $chosenName,
            'description' => $categories[$chosenName]??'Standard corporate asset group inventory.',
        ];
    }
}


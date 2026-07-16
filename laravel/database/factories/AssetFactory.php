<?php

// database/factories/AssetFactory.php
namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetFactory extends Factory
{
    public function definition(): array
    {
        // Realistic mapping of workplace assets
        $items = [
            ['brand' => 'Apple', 'name' => fake()->randomElement(['MacBook Pro M3', 'MacBook Air', 'iPad Pro', 'Studio Display'])],
            ['brand' => 'Dell', 'name' => fake()->randomElement(['XPS 15 Laptop', 'Latitude 7440', 'UltraSharp 27" Monitor'])],
            ['brand' => 'Lenovo', 'name' => fake()->randomElement(['ThinkPad X1 Carbon', 'ThinkCentre Desktop'])],
            ['brand' => 'Logitech', 'name' => fake()->randomElement(['MX Master 3S Mouse', 'MX Keys Keyboard', 'Brio 4K Webcam'])],
        ];

        $selectedItem = fake()->randomElement($items);

        return [
            'name' => $selectedItem['name'],
            'brand' => $selectedItem['brand'],
            'status' => 'available',
            'price' => fake()->randomFloat(2, 50, 3500), // Prices ranging from $50 accessories to $3500 computers
            
            // Recycles existing database records rather than creating new ones every single time
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            
            // Generates realistic corporate warranty dates (e.g., purchased within last 3 years)
            'Warranty' => fake()->dateTimeBetween('-3 years', '+2 years')->format('Y-m-d'),
        ];
    }
}

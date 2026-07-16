<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Asset;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()
            ->admin('Rohan', 'rohan123')
            ->create();
        User::factory()
            ->count(25)
            ->create();
        // 2. Create foundational corporate asset categories
        // $categories = ['Laptops', 'Monitors', 'Headphones', 'Mobile Devices'];

        // 2. Generate exactly 6 categories using the CategoryFactory
        // This guarantees both 'name' and 'description' are populated together
        Category::factory()
                ->count(4)
                ->create();

        // 3. Seed the asset records
        Asset::factory()
            ->count(10)
            ->create();
    }
}

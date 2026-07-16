<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('assets')->insert([
            'name' => Str::random(10),
            'brand' => Str::random(10).'@example.com',
            'status' => Str::random(10),
            'price' => fake()->randomNumber(4, true),
            'category_id' => fake()->numberBetween(0, 4), 
            'user_id' => fake() ->numberBetween(0, 4), 
            'Warranty' => fake()->date('Y-m-d', 'now'),
        ]);
    }
}

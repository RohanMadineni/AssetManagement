<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // public function run(): void
    // {
    //     //
    //     DB::table('users')->insert([
    //         'username' => Str::random(10),
    //         'email' => Str::random(10).'@example.com',
    //         'password' => Hash::make('password'),
    //         'role' => Str::random(10),
    //     ]);
    // }
     public function run(): void
    {
        // 1. Create the primary administrator account first
        // User::factory()
        //     ->admin('Rohan')
        //     ->create();

        // 2. Seed 25 additional believable system users
        User::factory()
            ->count(25)
            ->create();
    }
}

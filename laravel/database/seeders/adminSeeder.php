<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class adminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run() {
            User::firstOrCreate(
                ['email'=>'admin@example.com'],
                [
                    'name'=>'Admin User',
                    'password'=>Hash::make('password123'),
                    'role'=>'admin'
                ]
            );
        }
}

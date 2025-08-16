<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create an admin user
        User::create([
            'name'              => 'Ikram ullah',
            'email'             => 'ikramullah0024@gmail.com',
            'password'          => Hash::make('secretPassword@2025'),   // change this in production!
            'email_verified_at' => now(),
        ]);
    }
}

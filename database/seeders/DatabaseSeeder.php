<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Nova admin user from environment variables
        if (env('NOVA_USER_EMAIL') && env('NOVA_USER_PASSWORD')) {
            User::updateOrCreate(
                ['email' => env('NOVA_USER_EMAIL')],
                [
                    'name' => env('NOVA_USER_NAME', 'Admin'),
                    'email' => env('NOVA_USER_EMAIL'),
                    'password' => Hash::make(env('NOVA_USER_PASSWORD')),
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin par défaut
        User::create([
            'name' => 'Hasnreziga Admin',
            'email' => 'admin@automanager.com',
            'password' => Hash::make('#HasVehix2025!'),
            'role' => UserRole::ADMIN,
            'email_verified_at' => now(),
        ]);

        // Validateur par défaut
        User::create([
            'name' => 'Hasina ANDL',
            'email' => 'hasinaandritina538@gmail.com',
            'password' => Hash::make('#HasVehix2025!'),
            'role' => UserRole::VALIDATOR,
            'email_verified_at' => now(),
        ]);

        // Clients de démonstration
        /*$clients = [
            [
                'name' => 'Marie Dupont',
                'email' => 'marie@example.com',
                'password' => Hash::make('password'),
                'role' => UserRole::CLIENT,
                'email_verified_at' => now(),
            ],0.

            [
                'name' => 'Pierre Martin',
                'email' => 'pierre@example.com',
                'password' => Hash::make('password'),
                'role' => UserRole::CLIENT,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Sophie Bernard',
                'email' => 'sophie@example.com',
                'password' => Hash::make('password'),
                'role' => UserRole::CLIENT,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($clients as $client) {
            User::create($client);
        }*/
    }
}

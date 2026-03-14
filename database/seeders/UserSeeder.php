<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'demo@readon.app';

        if (User::where('email', $email)->exists()) {
            $this->command->info("UserSeeder: usuario {$email} ya existe, nada que hacer.");
            return;
        }

        User::create([
            'name'     => 'Demo User',
            'email'    => $email,
            'password' => Hash::make('password'),
        ]);

        $this->command->info("UserSeeder: usuario {$email} creado.");
    }
}

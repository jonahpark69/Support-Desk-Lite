<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'agent@demo.test'],
            [
                'name' => 'Agent Demo',
                'password' => Hash::make('password'),
                'role' => 'agent',
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@demo.test'],
            [
                'name' => 'User Demo',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );
    }
}

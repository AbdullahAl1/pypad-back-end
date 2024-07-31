<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'username' => 'amirhilal',
            'first_name' => 'Amir',
            'last_name' => 'Hilal',
            'email' => 'amiramirhilal@gmail.com',
            'password' => Hash::make('amir123'),
            'role' => 'superadmin',
            'email_verified_at' => now(),
        ]);
    }
}

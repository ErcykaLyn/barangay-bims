<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Resident;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ===== ADMIN (users table) =====
        User::updateOrCreate(
            ['email' => 'admin@bims.com'],
            [
                'first_name' => 'Bactad',
                'last_name'  => 'Admin',
                'email'      => 'admin@bims.com',
                'password'   => Hash::make('admin123'),
                'role'       => 'admin',
            ]
        );

        // ===== BHW (users table) =====
        User::updateOrCreate(
            ['email' => 'bhw@bactadeast.gov.ph'],
            [
                'first_name' => 'BHW',
                'last_name'  => 'User',
                'email'      => 'bhw@bactadeast.gov.ph',
                'password'   => Hash::make('bhw123'),
                'role'       => 'bhw',
            ]
        );

        // ===== RESIDENT (residents table) =====
        Resident::updateOrCreate(
            ['username' => 'juandelacruz'],
            [
                'username'       => 'juandelacruz',
                'first_name'     => 'Juan',
                'last_name'      => 'Dela Cruz',
                'zone_purok'     => 'Zone 2',
                'password'       => Hash::make('resident123'),
                'role'           => 'resident',
                'is_senior'      => false,
                'is_indigent'    => true,
                'is_pwd'         => true,
                'is_solo_parent' => false,
                'is_voter'       => true,
            ]
        );
    }
}
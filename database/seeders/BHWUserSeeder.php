<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class BHWUserSeeder extends Seeder
{
    public function run()
    {
        // Check if BHW user already exists using the new email
        $existing = DB::table('users')->where('email', 'bhwadmin@gmail.com')->first();
        if (!$existing) {
            DB::table('users')->insert([
                'first_name' => 'BHW',
                'last_name'  => 'Admin',
                'email'      => 'bhwadmin@gmail.com',
                'password'   => Hash::make('admin123'), // new password
                'role'       => 'bhw',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info('BHW user created successfully with email bhwadmin@gmail.com');
        } else {
            $this->command->info('BHW user already exists.');
        }
    }
}
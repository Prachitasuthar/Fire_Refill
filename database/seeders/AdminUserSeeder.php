<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'mobile_no' => '1234567890',
            'user_type' => 'admin',
            'email' => 'admin@admin.com',
            'address' => 'Surat',
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'), // password encryption
            'is_email_verified' => 1,
            'remember_token' => Str::random(10),
            'profile_image' => null,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);
    }
}

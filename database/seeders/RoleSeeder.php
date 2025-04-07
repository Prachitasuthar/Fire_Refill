<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Define roles
        $roles = ['admin', 'provider'];

        foreach ($roles as $role) {
            $createdRole = Role::firstOrCreate(['name' => $role]);
            
            if ($createdRole->wasRecentlyCreated) {
                echo "Role created: " . $role . "\n";
            } else {
                echo "Role already exists: " . $role . "\n";
            }
        }

        // Assign roles to users based on user_type
        $adminRole = Role::findByName('admin');
        $providerRole = Role::findByName('provider');
       

        // Assign 'admin' role to users with user_type 'admin'
        $admins = User::where('user_type', 'admin')->get();
        foreach ($admins as $admin) {
            if (!$admin->hasRole('admin')) {
                $admin->assignRole('admin');
            }
        }

        // Assign 'provider' role to all users with user_type 'provider'
        $providers = User::where('user_type', 'provider')->get();
        foreach ($providers as $provider) {
            if (!$provider->hasRole('provider')) {
                $provider->assignRole('provider');
            }
        }

      
    }
}

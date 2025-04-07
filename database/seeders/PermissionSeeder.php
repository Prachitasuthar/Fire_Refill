<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            // services
            'services',
            'create services',
            'view services',
            'edit services',
            'delete services',
            'show service request',
            'accept service request list',
            'accept service request',
            'show service resquest',
            'delete service request',
            'delete accepted service',
            // user
            'user list',
            'restore user',
            'edit user',
            'delete user',
            // accessories
            'view accessories',
            'create accessories',
            'edit accessories',
            'delete accessories',
            // fire extinguishers
            'view fire extinguisher',
            'create extinguisher',
            'edit extinguisher',
            'delete extinguisher',
            // fire suppression
            'view suppression',
            'create suppression',
            'edit suppression',
            'delete suppression',
            // watermist system
            'view watermist',
            'create watermist',
            'edit watermist',
            'delete watermist',
            // service providers
            'rejected provider',
            'provider request',
            'view providers',
            'edit service provider',
            // coupons
            'view coupon',
            'create coupon',
            // booking order
            'view order',
            'view delivered order',
            'track order',
            'delete order booking',
            
        ];

        // Create the permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $providerRole = Role::firstOrCreate(['name' => 'provider']);

        // Sync permissions for admin
        if ($adminRole) {
            $adminRole->syncPermissions($permissions);
        }

        // Sync limited permissions for provider
        if ($providerRole) {
            $providerRole->syncPermissions([
                'services', 
                'user list',
                'view services',
                'create services',
                'show service request',
                'accept service request list',
                'view order',
                'view delivered order',
                'user list',
                'view providers',
                'view accessories',
                'view fire extinguisher',
                'view suppression',
                'view watermist',
                'view coupon',
            ]);
        }

        // Assign roles to users based on their user_type, excluding the first provider
        $admins = User::where('user_type', 'admin')->get();
        foreach ($admins as $admin) {
            if (!$admin->hasRole('admin')) {
                $admin->assignRole('admin');
            }
        }

        // Fetch all providers excluding the first provider
        $providers = User::where('user_type', 'provider')->get();  // Skip the first provider
        foreach ($providers as $provider) {
            if (!$provider->hasRole('provider')) {
                $provider->assignRole('provider');
              
                $provider->givePermissionTo([
                    'services', 
                    'user list',
                    'view services',
                    'create services',
                    'show service request',
                    'accept service request list',
                    'view order',
                    'view delivered order',
                    'user list',
                    'view providers',
                    'view accessories',
                    'view fire extinguisher',
                    'view suppression',
                    'view watermist',
                    'view coupon',
                ]);
            }
        }
    }
}

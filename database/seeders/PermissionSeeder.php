<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        Permission::firstOrCreate(['name' => 'access waste bank module']);
        Permission::firstOrCreate(['name' => 'access annual collection module']);
        Permission::firstOrCreate(['name' => 'manage staff']); // For managing staff records
        Permission::firstOrCreate(['name' => 'manage users']); // For managing general users
        Permission::firstOrCreate(['name' => 'manage waste types']); // For managing waste types and prices

        // Optional: Create a 'Super Admin' role and assign all permissions
        // This is good for initial setup
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        // Optional: Create specific staff roles
        $wasteBankStaffRole = Role::firstOrCreate(['name' => 'Waste Bank Staff']);
        $wasteBankStaffRole->givePermissionTo('access waste bank module');

        $annualCollectionStaffRole = Role::firstOrCreate(['name' => 'Annual Collection Staff']);
        $annualCollectionStaffRole->givePermissionTo('access annual collection module');

        // Assign 'Super Admin' role to a specific user (e.g., user with ID 1)
        // Make sure you have at least one user in your 'users' table
        // $user = \App\Models\User::find(1);
        // if ($user) {
        //     $user->assignRole('Super Admin');
        // }
    }
}

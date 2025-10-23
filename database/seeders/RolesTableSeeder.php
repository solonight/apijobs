<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesTableSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $roles = ['user', 'admin', 'employer'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Create permissions
        $permissions = ['create jobs'];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign 'create jobs' permission to employer
        $employerRole = Role::where('name', 'employer')->first();
        $employerRole->givePermissionTo('create jobs');
    }
}


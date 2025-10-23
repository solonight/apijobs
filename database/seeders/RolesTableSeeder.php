<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

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
        $permissions = ['create jobs', 'update jobs', 'delete jobs'];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // Assign permissions AFTER they are created
        DB::commit();

        $employerRole = Role::where('name', 'employer')->first();

        foreach (['create jobs', 'update jobs', 'delete jobs'] as $perm) {
            $permission = Permission::where('name', $perm)->where('guard_name', 'web')->first();
            if ($permission) {
                $employerRole->givePermissionTo($permission);
            }
        }
    }
}


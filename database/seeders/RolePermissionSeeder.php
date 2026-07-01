<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view tasks',
            'create tasks',
            'update tasks',
            'delete tasks',
            'move tasks',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        $admin = Role::findOrCreate('admin');
        $supervisor = Role::findOrCreate('supervisor');
        $intern = Role::findOrCreate('intern');

        $admin->syncPermissions($permissions);

        $supervisor->syncPermissions([
            'view tasks',
            'create tasks',
            'update tasks',
            'delete tasks',
            'move tasks',
        ]);

        $intern->syncPermissions([
            'view tasks',
            'create tasks',
            'update tasks',
            'move tasks',
        ]);
    }
}
<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'Cliente']);
        $admin = Role::firstOrCreate(['name' => 'Administrador']);
        if ($admin->permissions()->count() === 0) {
            $permisos = Permission::get();
            $admin->permissions()->attach($permisos);
        }

        if (Role::count() > 0) return;

        Role::factory(25)->create();
    }
}

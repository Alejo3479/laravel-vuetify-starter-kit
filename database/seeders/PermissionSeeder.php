<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        if (Permission::count() > 0) return;

        $datos = [
            ['name' => 'listar usuarios', 'guard_name' => 'web'],
            ['name' => 'crear usuarios', 'guard_name' => 'web'],
            ['name' => 'editar usuarios', 'guard_name' => 'web'],
            ['name' => 'eliminar usuarios', 'guard_name' => 'web'],
            ['name' => 'ver roles', 'guard_name' => 'web'],
            ['name' => 'crear roles', 'guard_name' => 'web'],
            ['name' => 'editar roles', 'guard_name' => 'web'],
            ['name' => 'eliminar roles', 'guard_name' => 'web'],
        ];

        foreach ($datos as $dato) {
            Permission::create($dato);
        }
    }
}

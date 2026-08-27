<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $datos = [
            [
                'name' => 'Tablero',
                'permissions' => [
                    ['label' => 'Ver tablero', 'name' => 'ver.tablero', 'guard_name' => 'web'],
                ],
            ],
            [
                'name' => 'Usuarios',
                'permissions' => [
                    ['label' => 'Listar usuarios', 'name' => 'listar.usuarios', 'guard_name' => 'web'],
                    ['label' => 'Ver usuario', 'name' => 'ver.usuarios', 'guard_name' => 'web'],
                    ['label' => 'Crear usuario', 'name' => 'crear.usuarios', 'guard_name' => 'web'],
                    ['label' => 'Editar usuario', 'name' => 'editar.usuarios', 'guard_name' => 'web'],
                    ['label' => 'Eliminar usuario', 'name' => 'eliminar.usuarios', 'guard_name' => 'web'],
                ],
            ],
            [
                'name' => 'Roles',
                'permissions' => [
                    ['label' => 'Listar roles', 'name' => 'listar.roles', 'guard_name' => 'web'],
                    ['label' => 'Ver rol', 'name' => 'ver.roles', 'guard_name' => 'web'],
                    ['label' => 'Crear rol', 'name' => 'crear.roles', 'guard_name' => 'web'],
                    ['label' => 'Editar rol', 'name' => 'editar.roles', 'guard_name' => 'web'],
                    ['label' => 'Eliminar rol', 'name' => 'eliminar.roles', 'guard_name' => 'web'],
                ],
            ],
            [
                'name' => 'Clientes',
                'permissions' => [
                    ['label' => 'Listar clientes', 'name' => 'listar.clientes', 'guard_name' => 'web'],
                    ['label' => 'Ver cliente', 'name' => 'ver.clientes', 'guard_name' => 'web'],
                    ['label' => 'Crear cliente', 'name' => 'crear.clientes', 'guard_name' => 'web'],
                    ['label' => 'Editar cliente', 'name' => 'editar.clientes', 'guard_name' => 'web'],
                    ['label' => 'Eliminar cliente', 'name' => 'eliminar.clientes', 'guard_name' => 'web'],
                ],
            ],
        ];

        foreach ($datos as $dato) {
            $grupo = PermissionGroup::firstOrCreate([
                'name' => $dato['name'],
            ]);
            foreach ($dato['permissions'] as $item) {
                $grupo->permissions()->firstOrCreate([
                    'name' => $item['name'],
                ], [
                    'label' => $item['label'],
                    'guard_name' => $item['guard_name'],
                ]);
            }
        }

        $role = Role::whereName('Administrador')->first();
        if ($role) {
            $role->permissions()->attach(Permission::get());
        }
    }
}

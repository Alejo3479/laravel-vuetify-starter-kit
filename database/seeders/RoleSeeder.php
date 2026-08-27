<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $datos = [
            [
                'name' => 'Cliente'
            ],
            [
                'name' => 'Administrador'
            ],
        ];

        foreach ($datos as $dato) {
            Role::firstOrCreate(['name' => $dato['name']]);
        }

        if (app()->environment('production')) return;

        Role::factory(25)->create();
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        if (Role::count() > 0) return;

        Role::factory(25)->create();
    }
}

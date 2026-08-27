<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate([
            'email' => env('MAIL_FROM_ADDRESS', 'admin@test.local'),
        ], [
            'name' => 'Administrador',
            'password' => Hash::make(env('MAIL_FROM_ADDRESS', 'admin@test.local')),
        ]);
        $user->syncRoles(['Administrador']);

        if (app()->environment('production')) return;

        User::factory(50)->create();

        $role = Role::whereName('Cliente')->first();
        if ($role) {
            $role->users()->syncWithoutDetaching(User::factory(25)->create());
        }
    }
}

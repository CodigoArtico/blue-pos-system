<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear el rol administrador con todos los permisos
        $adminRole = Role::updateOrCreate(['name' => 'administrador']);
        $allPermissions = Permission::all();
        $adminRole->syncPermissions($allPermissions);

        // Crear usuario administrador
        $adminUser = User::updateOrCreate(
            ['email' => 'Administrador@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('12345678'),
            ]
        );

        // Asignar rol administrador
        $adminUser->assignRole('administrador');
    }
}

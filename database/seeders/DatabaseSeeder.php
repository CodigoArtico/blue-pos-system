<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'go@gmail.com',
        //     'password' => Hash::make('12345678'), // Hasheamos la contraseña
        // ]);

        $this->call([
            ConfiguracionBaseSeeder::class,
            EntidadesRelacionadasSeeder::class,
            PermissionSeeder::class,
            UserSeeder::class,
            TransaccionesSeeder::class,
            EmpresaSeeder::class,
        ]);
        
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'numero_documento' => '100000001',
            'user_nombre' => 'Admin Test',
            'user_correo' => 'admin@admin.com',
            'user_telefono' => '3000000001',
            'user_contrasena' => 'Admin12345',
            'rol_id' => 1, // Super Admin
            'email_verified_at' => now(),
        ]);
    }
}

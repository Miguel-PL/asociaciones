<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Crea el usuario con acceso al panel de Filament.
     *
     * Las credenciales se leen del entorno para no fijarlas en el repositorio.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@asociaciones.test');
        $password = env('ADMIN_PASSWORD', 'asociaciones');

        User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => env('ADMIN_NAME', 'Administrador'),
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ],
        );
    }
}

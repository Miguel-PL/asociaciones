<?php

namespace Database\Seeders;

use App\Models\User;
use App\Sites\SiteManager;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Crea una cuenta de administracion por sitio.
     *
     * Cada panel solo acepta cuentas de su asociacion, asi que cada sitio
     * necesita la suya. El correo sale de admin_email en el site.php de cada
     * sitio y las contrasenas se leen del entorno para no fijarlas en el
     * repositorio.
     */
    public function run(): void
    {
        $password = env('ADMIN_PASSWORD', 'asociaciones');
        $name = env('ADMIN_NAME', 'Administracion');

        $sites = app(SiteManager::class);

        foreach ($sites->enabled() as $slug => $site) {
            $email = $site->adminEmail ?? "{$slug}@asociaciones.test";

            User::query()->updateOrCreate(
                ['email' => $email],
                [
                    'site_id' => $slug,
                    'name' => "{$name} - {$site->name}",
                    'password' => Hash::make($password),
                    'email_verified_at' => now(),
                ],
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use App\Sites\SiteManager;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Crea las cuentas de administracion.
     *
     * Dos tipos:
     *
     * - Una por sitio. Cada panel solo acepta cuentas de su asociacion, asi
     *   que cada sitio necesita la suya. El correo sale de admin_email en el
     *   site.php de cada sitio.
     * - La de administracion de la plataforma, que entra en todos los paneles
     *   y no pertenece a ninguna asociacion. Se crea siempre, porque sin ella
     *   no habria forma de corregir una instalacion en la que se haya
     *   bloqueado el acceso.
     *
     * Las contrasenas se leen del entorno para no fijarlas en el repositorio.
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

        $this->superadmin($name);
    }

    /**
     * Administracion de toda la plataforma.
     *
     * site_id queda vacio a proposito: la cuenta no es de ninguna asociacion,
     * asi que dentro de cada panel ve el contenido de ese sitio. El acceso a
     * los paneles depende de is_super_admin y no de site_id.
     */
    protected function superadmin(string $name): void
    {
        User::query()->updateOrCreate(
            ['email' => env('SUPERADMIN_EMAIL', 'admin@asociaciones.test')],
            [
                'is_super_admin' => true,
                'site_id' => null,
                'name' => env('SUPERADMIN_NAME', "{$name} general"),
                'password' => Hash::make(env('SUPERADMIN_PASSWORD', 'asociaciones')),
                'email_verified_at' => now(),
            ],
        );
    }
}

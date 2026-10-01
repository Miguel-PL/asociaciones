<?php

namespace App\Sites\Commands;

use App\Sites\Site;
use Illuminate\Console\Command;

/**
 * Enlaza la carpeta sites/ dentro de public/ para que sus assets sean
 * servibles por el navegador, igual que hace storage:link con storage/app/public.
 */
class SitesLinkCommand extends Command
{
    protected $signature = 'sites:link
                            {--relative : Crear el enlace simbolico con rutas relativas}
                            {--force : Sustituir un enlace existente}';

    protected $description = 'Enlaza sites/ en public/sites para publicar los assets de los sitios';

    public function handle(): int
    {
        $target = Site::basePath();
        $link = public_path('sites');

        // PHP cachea realpath(): tras crear o borrar una junction hay que
        // limpiar la cache o isLink() leera valores obsoletos.
        clearstatcache(true);

        if (! is_dir($target)) {
            $this->components->error("No existe la carpeta de sitios [{$target}].");

            return self::FAILURE;
        }

        if (file_exists($link) || is_link($link)) {
            if (! $this->option('force')) {
                $this->components->error("El enlace [{$link}] ya existe. Usa --force para sustituirlo.");

                return self::FAILURE;
            }

            if (! $this->removeLink($link)) {
                $this->components->error("No se pudo eliminar el enlace existente [{$link}].");

                return self::FAILURE;
            }
        }

        $this->laravel->make('files')->link($target, $link);

        clearstatcache(true);

        if (! $this->isLink($link)) {
            $this->components->error("No se pudo crear el enlace [{$link}].");

            return self::FAILURE;
        }

        $this->components->info("El enlace [{$link}] apunta a [{$target}].");

        return self::SUCCESS;
    }

    protected function removeLink(string $link): bool
    {
        if (! $this->isLink($link)) {
            return false;
        }

        // rmdir quita la junction sin tocar el destino.
        return @rmdir($link) || @unlink($link);
    }

    /**
     * is_link() no reporta las junctions de Windows, pero realpath() las
     * resuelve a otra ruta. Asi detectamos ambas sin depender del idioma,
     * y sin is_dir(), que no es fiable con reparse points.
     */
    protected function isLink(string $path): bool
    {
        if (is_link($path)) {
            return true;
        }

        $real = realpath($path);

        if ($real === false) {
            return false;
        }

        return $this->normalize($real) !== $this->normalize($path);
    }

    protected function normalize(string $path): string
    {
        return strtolower(str_replace('\\', '/', rtrim($path, '\\/')));
    }
}

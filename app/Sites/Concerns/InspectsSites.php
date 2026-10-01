<?php

namespace App\Sites\Concerns;

use App\Sites\Site;
use App\Sites\SiteManager;
use Illuminate\Support\Facades\File;
use Throwable;

trait InspectsSites
{
    /**
     * Carpetas dentro de sites/, indexed por slug.
     *
     * @return array<string, string>
     */
    protected function siteDirectories(): array
    {
        $path = (string) config('sites.path', base_path('sites'));
        $directories = [];

        if (! File::isDirectory($path)) {
            return $directories;
        }

        foreach (File::directories($path) as $directory) {
            $directories[basename($directory)] = $directory;
        }

        ksort($directories);

        return $directories;
    }

    /**
     * Comprueba que un sitio sea coherente con su carpeta.
     *
     * @return array<int, string>
     */
    protected function problemsFor(string $slug, string $directory, SiteManager $sites): array
    {
        $problems = [];

        if (! File::exists("{$directory}/site.php")) {
            $problems[] = 'Falta site.php';

            return $problems;
        }

        try {
            $site = Site::load($slug);
        } catch (Throwable $exception) {
            $problems[] = 'site.php no se puede leer: '.$exception->getMessage();

            return $problems;
        }

        if ($site->slug !== $slug) {
            $problems[] = "El slug [{$site->slug}] no coincide con la carpeta [{$slug}]";
        }

        if (blank($site->domains)) {
            $problems[] = 'No declara ningún dominio';
        }

        foreach ($site->domains as $domain) {
            $owner = $sites->resolveByHost((string) $domain);

            if ($owner !== null && $owner->slug !== $slug && $owner->hasDomain((string) $domain)) {
                $problems[] = "El dominio [{$domain}] también lo declara [{$owner->slug}]";
            }
        }

        foreach (['logo', 'favicon'] as $asset) {
            $value = $site->{$asset};

            if (filled($value) && ! File::exists("{$directory}/{$value}")) {
                $problems[] = "El asset [{$asset}] apunta a [{$value}], que no existe";
            }
        }

        return $problems;
    }
}

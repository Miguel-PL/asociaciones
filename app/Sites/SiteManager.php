<?php

namespace App\Sites;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\File;
use Throwable;

/**
 * Registro de sitios y resolucion del sitio activo.
 *
 * El descubrimiento es automatico: cada carpeta dentro de sites/ que contenga
 * un site.php se convierte en un sitio disponible.
 */
class SiteManager
{
    /** @var array<string, Site>|null */
    protected ?array $sites = null;

    protected ?Site $current = null;

    public function __construct(protected Application $app) {}

    /**
     * Todos los sitios declarados, indexados por slug.
     *
     * @return array<string, Site>
     */
    public function all(): array
    {
        return $this->sites ??= $this->discover();
    }

    /**
     * @return array<int, string>
     */
    public function slugs(): array
    {
        return array_keys($this->all());
    }

    public function has(string $slug): bool
    {
        return array_key_exists($slug, $this->all());
    }

    public function get(string $slug): ?Site
    {
        return $this->all()[$slug] ?? null;
    }

    /**
     * Sitios que pueden servirse publicamente.
     *
     * @return array<string, Site>
     */
    public function enabled(): array
    {
        return array_filter($this->all(), fn (Site $site): bool => $site->enabled);
    }

    public function current(): ?Site
    {
        return $this->current;
    }

    public function set(?Site $site): void
    {
        $this->current = $site;
    }

    /**
     * Sitio por defecto, usado cuando nada mas identifica la peticion.
     */
    public function default(): ?Site
    {
        $slug = $this->app['config']->get('sites.default');

        if ($slug && $this->has($slug)) {
            return $this->get($slug);
        }

        $enabled = $this->enabled();

        return $enabled === [] ? null : reset($enabled);
    }

    /**
     * Sitio que corresponde a un dominio, si alguno lo declara.
     */
    public function resolveByHost(string $host): ?Site
    {
        foreach ($this->enabled() as $site) {
            if ($site->hasDomain($host)) {
                return $site;
            }
        }

        return null;
    }

    public function resolveBySlug(string $slug): ?Site
    {
        $site = $this->get($slug);

        return $site?->enabled ? $site : null;
    }

    /**
     * Fuerza el sitio activo, ignorando el dominio de la peticion.
     */
    public function forceSlug(): ?string
    {
        $slug = $this->app['config']->get('sites.override');

        return filled($slug) ? (string) $slug : null;
    }

    /**
     * Descarta los sitios cacheados, util tras crear uno nuevo.
     */
    public function flush(): void
    {
        $this->sites = null;
    }

    /**
     * @return array<string, Site>
     */
    protected function discover(): array
    {
        $path = Site::basePath();
        $sites = [];

        if (! File::isDirectory($path)) {
            return $sites;
        }

        foreach (File::directories($path) as $directory) {
            $slug = basename($directory);

            if (! File::exists("{$directory}/site.php")) {
                continue;
            }

            try {
                $sites[$slug] = Site::load($slug);
            } catch (Throwable) {
                // Un site.php invalido se reporta con sites:check, no al
                // arranquar la aplicacion.
                continue;
            }
        }

        ksort($sites);

        return $sites;
    }
}

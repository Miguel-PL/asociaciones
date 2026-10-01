<?php

namespace App\Sites;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\File;
use InvalidArgumentException;

/**
 * Configuración de un sitio, cargada desde sites/{slug}/site.php.
 *
 * @implements Arrayable<string, mixed>
 */
class Site implements Arrayable
{
    public function __construct(
        public readonly string $slug,
        public readonly string $name,
        public readonly string $tagline = '',
        public readonly string $description = '',
        public readonly array $domains = [],
        public readonly array $colors = [],
        public readonly ?string $logo = null,
        public readonly ?string $favicon = null,
        public readonly array $social = [],
        public readonly array $contact = [],
        public readonly array $footer = [],
        public readonly bool $enabled = true,
    ) {}

    /**
     * @param  array<string, mixed>  $config
     */
    public static function fromConfig(string $slug, array $config): self
    {
        foreach (['name'] as $required) {
            if (empty($config[$required])) {
                throw new InvalidArgumentException(
                    "El sitio [{$slug}] debe definir [{$required}] en su site.php."
                );
            }
        }

        return new self(
            slug: $slug,
            name: (string) $config['name'],
            tagline: (string) ($config['tagline'] ?? ''),
            description: (string) ($config['description'] ?? ''),
            domains: array_values((array) ($config['domains'] ?? [])),
            colors: (array) ($config['colors'] ?? []),
            logo: $config['logo'] ?? null,
            favicon: $config['favicon'] ?? null,
            social: (array) ($config['social'] ?? []),
            contact: (array) ($config['contact'] ?? []),
            footer: (array) ($config['footer'] ?? []),
            enabled: (bool) ($config['enabled'] ?? true),
        );
    }

    /**
     * Carga la configuración desde el directorio del sitio.
     */
    public static function load(string $slug): self
    {
        $path = static::configPath($slug);

        if (! File::exists($path)) {
            throw new InvalidArgumentException("El sitio [{$slug}] no tiene site.php en {$path}.");
        }

        $config = require $path;

        if (! is_array($config)) {
            throw new InvalidArgumentException("El site.php de [{$slug}] debe devolver un array.");
        }

        return static::fromConfig($slug, $config);
    }

    public static function configPath(string $slug): string
    {
        return static::basePath().DIRECTORY_SEPARATOR.$slug.DIRECTORY_SEPARATOR.'site.php';
    }

    /**
     * Carpeta que contiene los sitios.
     */
    public static function basePath(): string
    {
        return rtrim(
            (string) (config('sites.path') ?: base_path('sites')),
            '\\/',
        );
    }

    public function hasDomain(string $host): bool
    {
        $host = $this->normalizeHost($host);

        foreach ($this->domains as $domain) {
            if ($this->normalizeHost((string) $domain) === $host) {
                return true;
            }
        }

        return false;
    }

    /**
     * Carpeta de assets del sitio en disco.
     */
    public function assetsPath(): string
    {
        return static::basePath().DIRECTORY_SEPARATOR.$this->slug.DIRECTORY_SEPARATOR.'assets';
    }

    /**
     * Resuelve una ruta de asset del sitio, o null si el archivo no existe.
     */
    public function asset(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        $relative = str_replace('/', DIRECTORY_SEPARATOR, ltrim($path, '/'));

        if (! File::exists($this->assetsPath().DIRECTORY_SEPARATOR.$relative)) {
            return null;
        }

        return asset("sites/{$this->slug}/assets/{$relative}");
    }

    /**
     * Variable CSS con un color del sitio, por si existe.
     */
    public function color(string $key, ?string $fallback = null): ?string
    {
        $value = $this->colors[$key] ?? $fallback;

        return $value ? (string) $value : null;
    }

    /**
     * @return array<string, string>
     */
    public function colorVariables(): array
    {
        return array_map(
            fn (string $value): string => $value,
            array_filter($this->colors, 'is_string'),
        );
    }

    public function normalizeHost(string $host): string
    {
        $host = strtolower(trim($host));

        // Elimina el puerto, si lo hay.
        if (str_contains($host, ':')) {
            $host = str($host)->before(':')->toString();
        }

        return $host;
    }

    public function toArray(): array
    {
        return [
            'slug' => $this->slug,
            'name' => $this->name,
            'tagline' => $this->tagline,
            'description' => $this->description,
            'domains' => $this->domains,
            'colors' => $this->colors,
            'logo' => $this->logo,
            'favicon' => $this->favicon,
            'social' => $this->social,
            'contact' => $this->contact,
            'footer' => $this->footer,
            'enabled' => $this->enabled,
        ];
    }
}

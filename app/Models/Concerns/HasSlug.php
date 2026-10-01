<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Genera el slug a partir del titulo y garantiza que sea unico.
 *
 * La unicidad se comprueba a traves de la consulta del modelo, que ya lleva
 * el SiteScope del sitio activo. Por eso no depende de que site_id esté
 * asignado y no hay que fiarse del orden en que se registran los traits.
 */
trait HasSlug
{
    public static function bootHasSlug(): void
    {
        static::creating(function (self $model): void {
            if (blank($model->slug)) {
                $model->slug = $model->generateUniqueSlug();
            }
        });
    }

    protected function generateUniqueSlug(): string
    {
        $base = Str::slug($this->slugSource());

        if ($base === '') {
            $base = 'sin-titulo';
        }

        $slug = $base;
        $suffix = 1;

        while ($this->slugIsTaken($slug)) {
            $slug = $base.'-'.++$suffix;
        }

        return $slug;
    }

    /**
     * Texto del que se deriva el slug. Por defecto, el titulo.
     */
    protected function slugSource(): string
    {
        return (string) ($this->title ?? '');
    }

    protected function slugIsTaken(string $slug): bool
    {
        return self::query()
            ->where($this->qualifyColumn('slug'), $slug)
            ->when($this->exists, fn ($query) => $query->whereKeyNot($this->getKey()))
            ->exists();
    }
}

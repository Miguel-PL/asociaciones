<?php

namespace App\Models\Concerns;

use App\Models\Scopes\SiteScope;
use App\Sites\Site;
use App\Sites\SiteManager;
use Illuminate\Database\Eloquent\Builder;

/**
 * Asocia un modelo a un sitio mediante site_id, que guarda el slug.
 */
trait BelongsToSite
{
    public static function bootBelongsToSite(): void
    {
        static::addGlobalScope(new SiteScope);

        static::creating(function ($model): void {
            if (blank($model->site_id)) {
                $model->site_id = app(SiteManager::class)->current()?->slug;
            }
        });
    }

    /**
     * Configuracion del sitio propietario de la fila.
     */
    public function site(): ?Site
    {
        return $this->site_id
            ? app(SiteManager::class)->get($this->site_id)
            : null;
    }

    public function belongsToSite(string $slug): bool
    {
        return $this->site_id === $slug;
    }

    /**
     * Quita el filtro de sitio de la consulta.
     */
    public function scopeAllSites(Builder $query): Builder
    {
        return $query->withoutGlobalScope(SiteScope::class);
    }

    /**
     * Filtra por un sitio concreto, ignorando el sitio activo.
     */
    public function scopeForSite(Builder $query, string $slug): Builder
    {
        return $query
            ->withoutGlobalScope(SiteScope::class)
            ->where($this->qualifyColumn('site_id'), $slug);
    }
}

<?php

namespace App\Models\Scopes;

use App\Sites\SiteManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Restringe un modelo a las filas del sitio activo.
 *
 * Si no hay sitio activo (consola, colas, panel) no se aplica ninguna
 * restriccion, para que las herramientas de administracion vean todo.
 */
class SiteScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $slug = app(SiteManager::class)->current()?->slug;

        if ($slug === null) {
            return;
        }

        $builder->where($model->qualifyColumn('site_id'), $slug);
    }
}

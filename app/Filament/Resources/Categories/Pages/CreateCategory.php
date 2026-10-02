<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Resources\Pages\CreateRecord;

/**
 * site_id no aparece en el formulario: lo asigna BelongsToSite con el sitio
 * del panel, así que una categoría creada desde un panel nunca pertenece al otro.
 */
class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;
}

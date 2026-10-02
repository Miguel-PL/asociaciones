<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Resources\Pages\PageResource;
use Filament\Resources\Pages\CreateRecord;

/**
 * site_id no aparece en el formulario: lo asigna BelongsToSite con el sitio
 * del panel, así que una página creada desde un panel nunca pertenece al otro.
 */
class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;
}

<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use Filament\Resources\Pages\CreateRecord;

/**
 * site_id no aparece en el formulario: lo asigna BelongsToSite con el sitio
 * del panel, así que una noticia creada desde un panel nunca pertenece al otro.
 */
class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;
}

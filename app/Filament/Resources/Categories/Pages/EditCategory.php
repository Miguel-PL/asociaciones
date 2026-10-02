<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        // Sin RestoreAction: Category no usa SoftDeletes. Sin ViewAction: no hay
        // pagina de vista registrada en el recurso.
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

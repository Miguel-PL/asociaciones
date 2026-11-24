<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Carbon;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            Action::make('preview')
                ->label('Vista previa')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->tooltip('Guarda los cambios y abre la vista previa sin cambiar el estado de publicación.')
                ->action('saveForPreview'),
            $this->getCancelFormAction(),
        ];
    }

    public function saveForPreview(): void
    {
        if (! $this->recordIsPublic() && $this->formDataWouldBePublic()) {
            Notification::make()
                ->title('La vista previa no debe publicar la noticia')
                ->body('Desactiva «Publicada» para previsualizarla como borrador. Los datos del formulario se conservan.')
                ->warning()
                ->send();

            return;
        }

        $this->save(shouldRedirect: false);

        $this->redirect(route('posts.preview', ['post' => $this->getRecord()->slug]));
    }

    protected function recordIsPublic(): bool
    {
        return $this->record->is_published
            && $this->record->published_at !== null
            && $this->record->published_at->lte(now());
    }

    protected function formDataWouldBePublic(): bool
    {
        $publishedAt = data_get($this->data, 'published_at');

        return (bool) data_get($this->data, 'is_published')
            && filled($publishedAt)
            && Carbon::parse($publishedAt)->lte(now());
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

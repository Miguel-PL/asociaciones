<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Carbon;

/**
 * site_id no aparece en el formulario: lo asigna BelongsToSite con el sitio
 * del panel, así que una noticia creada desde un panel nunca pertenece al otro.
 */
class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected bool $redirectToPreviewAfterCreate = false;

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            Action::make('preview')
                ->label('Vista previa')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->tooltip('Guarda los cambios y abre la vista previa sin cambiar el estado de publicación.')
                ->action('createForPreview'),
            ...($this->canCreateAnother() ? [$this->getCreateAnotherFormAction()] : []),
            $this->getCancelFormAction(),
        ];
    }

    public function createForPreview(): void
    {
        if ($this->formDataWouldBePublic()) {
            Notification::make()
                ->title('La vista previa no debe publicar la noticia')
                ->body('Desactiva «Publicada» para previsualizarla como borrador. Los datos del formulario se conservan.')
                ->warning()
                ->send();

            return;
        }

        $this->redirectToPreviewAfterCreate = true;

        try {
            $this->create();
        } finally {
            $this->redirectToPreviewAfterCreate = false;
        }
    }

    protected function getRedirectUrl(): string
    {
        if ($this->redirectToPreviewAfterCreate) {
            return route('posts.preview', ['post' => $this->getRecord()->slug]);
        }

        return parent::getRedirectUrl();
    }

    protected function formDataWouldBePublic(): bool
    {
        $publishedAt = data_get($this->data, 'published_at');

        return (bool) data_get($this->data, 'is_published')
            && filled($publishedAt)
            && Carbon::parse($publishedAt)->lte(now());
    }
}

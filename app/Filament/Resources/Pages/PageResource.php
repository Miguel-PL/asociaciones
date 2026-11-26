<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\Pages\Pages\ListPages;
use App\Models\Page;
use App\Sites\SiteManager;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 2;

    protected static string|UnitEnum|null $navigationGroup = 'Contenido';

    protected static ?string $modelLabel = 'página';

    protected static ?string $pluralModelLabel = 'páginas';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Título')->searchable()->sortable()->wrap(),
                TextColumn::make('slug')->label('Slug')->searchable(),
                TextColumn::make('is_published')
                    ->label('Estado')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Publicada' : 'Oculta')
                    ->badge()
                    ->color(fn (Page $record): string => $record->is_published ? 'success' : 'gray'),
            ])
            ->recordTitleAttribute('title')
            ->actions([
                Action::make('toggleVisibility')
                    ->label(fn (Page $record): string => $record->is_published ? 'Ocultar' : 'Publicar')
                    ->icon(fn (Page $record): string => $record->is_published ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn (Page $record): string => $record->is_published ? 'danger' : 'success')
                    ->requiresConfirmation(fn (Page $record): bool => $record->is_published)
                    ->modalHeading(fn (Page $record): string => $record->is_published ? 'Ocultar página' : 'Publicar página')
                    ->modalDescription(fn (Page $record): string => $record->is_published
                        ? 'La página dejará de estar disponible en la web pública.'
                        : 'La página volverá a estar disponible en la web pública.')
                    ->modalSubmitActionLabel(fn (Page $record): string => $record->is_published ? 'Ocultar' : 'Publicar')
                    ->action(function (Page $record): void {
                        $site = app(SiteManager::class)->current();

                        abort_unless($site !== null && $record->site_id === $site->slug, 404);

                        $record->is_published = ! $record->is_published;
                        $record->save();

                        Notification::make()
                            ->title($record->is_published ? 'Página publicada' : 'Página ocultada')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('title');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
        ];
    }
}

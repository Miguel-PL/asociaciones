<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Pages\Pages\ListPages;
use App\Models\Page;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
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

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Contenido')->schema([
                TextInput::make('title')
                    ->label('Título')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->label('Slug')
                    ->maxLength(255)
                    ->helperText('Vacío = se genera a partir del título.'),
                RichEditor::make('body')
                    ->label('Contenido')
                    ->columnSpanFull(),
                TextInput::make('meta_description')
                    ->label('Meta descripción')
                    ->maxLength(500)
                    ->columnSpanFull(),
            ])->columns(1),

            Section::make('Publicación')->schema([
                Toggle::make('is_published')
                    ->label('Publicada')
                    ->helperText('Desactiva para dejar la página como borrador.'),
                DateTimePicker::make('published_at')
                    ->label('Fecha de publicación')
                    ->seconds(false)
                    ->default(now()),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Título')->searchable()->sortable()->wrap(),
                TextColumn::make('slug')->label('Slug')->searchable()->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_published')->label('Publicada')->boolean(),
                TextColumn::make('published_at')->label('Publicada el')->dateTime()->sortable(),
                TextColumn::make('updated_at')->label('Actualizada')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordTitleAttribute('title')
            ->filters([
                SelectFilter::make('is_published')->label('Estado')->options([
                    1 => 'Publicadas',
                    0 => 'Borradores',
                ]),
                TernaryFilter::make('is_published')->label('Publicada'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('title');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }
}

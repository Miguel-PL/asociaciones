<?php

namespace App\Filament\Resources\Categories;

use App\Filament\Resources\Categories\Pages\EditCategory;
use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Models\Category;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 3;

    protected static string|UnitEnum|null $navigationGroup = 'Contenido';

    protected static ?string $modelLabel = 'categoría';

    protected static ?string $pluralModelLabel = 'categorías';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Nombre')
                ->required()
                ->maxLength(255),
            TextInput::make('slug')
                ->label('Slug')
                ->maxLength(255)
                ->helperText('Si lo dejas vacío se genera a partir del nombre.'),
            Textarea::make('description')
                ->label('Descripción')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nombre')->searchable()->sortable(),
                TextColumn::make('slug')->label('Slug')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('posts_count')->label('Noticias')->counts('posts')->sortable(),
                TextColumn::make('created_at')->label('Creado')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordTitleAttribute('name')
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategories::route('/'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }
}

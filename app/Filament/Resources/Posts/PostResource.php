<?php

namespace App\Filament\Resources\Posts;

use App\Filament\Resources\Posts\Pages\EditPost;
use App\Filament\Resources\Posts\Pages\ListPosts;
use App\Models\Post;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use UnitEnum;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-newspaper';

    protected static ?int $navigationSort = 1;

    protected static string|UnitEnum|null $navigationGroup = 'Contenido';

    protected static ?string $modelLabel = 'noticia';

    protected static ?string $pluralModelLabel = 'noticias';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Noticia')->schema([
                TextInput::make('title')
                    ->label('Título')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->label('Slug')
                    ->maxLength(255)
                    ->helperText('Vacío = se genera a partir del título.'),
                Select::make('category_id')
                    ->label('Categoría')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    // Solo categorías del sitio activo: el SiteScope ya lo
                    // garantiza, pero lo dejamos explicito.
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(false),
                Textarea::make('excerpt')
                    ->label('Entradilla')
                    ->rows(3)
                    ->columnSpanFull(),
                RichEditor::make('body')
                    ->label('Contenido')
                    ->columnSpanFull(),
                FileUpload::make('cover_image')
                    ->label('Imagen de portada')
                    ->image()
                    ->directory('portadas')
                    ->maxSize(4096)
                    ->columnSpanFull(),
            ])->columns(1),

            Section::make('Publicación')->schema([
                Toggle::make('is_published')
                    ->label('Publicada')
                    ->helperText('Desactiva para dejar la noticia como borrador.'),
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
                ImageColumn::make('cover_image')
                    ->label('')
                    ->disk('public')
                    ->height(48)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('title')->label('Título')->searchable()->sortable()->wrap(),
                TextColumn::make('category.name')->label('Categoría')->badge()->sortable()->toggleable(),
                IconColumn::make('is_published')->label('Publicada')->boolean(),
                TextColumn::make('published_at')->label('Publicada el')->dateTime()->sortable(),
                TextColumn::make('updated_at')->label('Actualizada')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordTitleAttribute('title')
            ->filters([
                SelectFilter::make('category')->label('Categoría')->relationship('category', 'name'),
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
            ->defaultSort('published_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosts::route('/'),
            'edit' => EditPost::route('/{record}/edit'),
        ];
    }
}

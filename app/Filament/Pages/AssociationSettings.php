<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use App\Sites\SiteManager;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use UnitEnum;

class AssociationSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Datos';

    protected static string|UnitEnum|null $navigationGroup = 'Contenido';

    protected static ?string $title = 'Datos de la asociación';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.association-settings';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(SiteManager $sites): void
    {
        $site = $sites->current();
        abort_if($site === null, 404);

        $settings = SiteSetting::forSite($site);

        $this->form->fill([
            'footer_title' => $settings->footer_title,
            'address' => $settings->address,
            'email' => $settings->email,
            'phone' => $settings->phone,
            'facebook_url' => $settings->facebook_url,
            'instagram_url' => $settings->instagram_url,
            'x_url' => $settings->x_url,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contacto')
                    ->description('Estos datos aparecen en el pie de la web pública.')
                    ->schema([
                        TextInput::make('footer_title')
                            ->label('Título del bloque')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('address')
                            ->label('Dirección')
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Correo electrónico')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(50),
                    ])
                    ->columns(2),
                Section::make('Redes sociales')
                    ->description('Introduce la dirección de cada perfil. Deja un campo vacío para ocultar esa red en el pie.')
                    ->schema([
                        TextInput::make('facebook_url')
                            ->label('Facebook')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('instagram_url')
                            ->label('Instagram')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('x_url')
                            ->label('X')
                            ->url()
                            ->maxLength(255),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(SiteManager $sites): void
    {
        $site = $sites->current();
        abort_if($site === null, 404);

        $data = $this->form->getState();
        SiteSetting::query()->updateOrCreate(['site_id' => $site->slug], $data);

        Notification::make()
            ->success()
            ->title('Datos de la asociación guardados')
            ->send();
    }
}

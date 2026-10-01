<?php

namespace App\Filament\Pages;

use App\Filament\Middleware\IdentifyPanelSite;
use App\Sites\SiteManager;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Session;
use UnitEnum;

/**
 * Selector del sitio que se esta editando en el panel.
 */
class SwitchSite extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationLabel = 'Cambiar de sitio';

    protected static ?string $title = 'Sitio activo';

    protected static ?int $navigationSort = 1;

    protected static bool $shouldRegisterNavigation = true;

    public ?string $selected = null;

    public function mount(SiteManager $sites): void
    {
        $this->selected = $sites->current()?->slug;
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('selected')
                    ->label('Sitio')
                    ->options(fn (SiteManager $sites): array => collect($sites->enabled())
                        ->mapWithKeys(fn ($site): array => [$site->slug => $site->name])
                        ->all())
                    ->required()
                    ->live(),
            ]);
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('switch')
                ->label('Cambiar')
                ->disabled(fn (): bool => blank($this->selected))
                ->action(function (SiteManager $sites): void {
                    $site = $sites->resolveBySlug((string) $this->selected);

                    if (! $site) {
                        return;
                    }

                    Session::put(IdentifyPanelSite::SESSION_KEY, $site->slug);

                    // El contenido ya se cargo con otro sitio activo: hay que
                    // recargar para que las consultas vuelvan a filtrarse.
                    $this->redirect($this->getUrl(), navigate: true);
                }),
        ];
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return 'Sitios';
    }
}

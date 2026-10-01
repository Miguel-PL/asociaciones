<?php

namespace App\Providers\Filament;

use App\Filament\Auth\Login;
use App\Filament\Middleware\IdentifyPanelSite;
use App\Sites\Site;
use App\Sites\SiteManager;
use Filament\FilamentManager;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Middleware\ShareErrorsFromSession;

/**
 * Registra un panel de administracion por sitio.
 *
 * Los paneles se crean dinamicamente a partir de las carpetas de sites/, de
 * modo que anadir una web nueva da su panel automaticamente. Cada panel vive
 * en /{slug}/admin y el id del panel es el slug, que es lo que usa
 * User::canAccessPanel() para impedir que una cuenta entre en el panel de
 * otra asociacion.
 *
 * El codigo de los recursos, paginas y widgets es compartido: no se duplica
 * nada por sitio, solo cambia el panel al que pertenecen.
 */
class SitePanelsProvider extends ServiceProvider
{
    /**
     * Se registra en register() y no en boot() porque Filament construye las
     * rutas de los paneles al cargar el archivo de rutas del paquete, que
     * ocurre durante el arranque y no despues.
     */
    public function register(): void
    {
        $sites = $this->app->make(SiteManager::class);

        $default = $sites->default()?->slug;

        foreach ($sites->enabled() as $slug => $site) {
            $this->app->make(FilamentManager::class)->registerPanel(
                $this->panelFor($site, $slug === $default),
            );
        }
    }

    protected function panelFor(Site $site, bool $isDefault): Panel
    {
        $panel = Panel::make()
            ->id($site->slug)
            ->path($site->slug.'/admin')
            ->login(Login::class)
            ->colors($this->colorsFor($site))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                IdentifyPanelSite::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);

        return $isDefault ? $panel->default() : $panel;
    }

    /**
     * Usa el color principal del sitio y, si no lo declara, el de Filament.
     *
     * @return array<string, array<int|string, string>|string>
     */
    protected function colorsFor(Site $site): array
    {
        $primary = $site->color('primary');

        return $primary ? ['primary' => $primary] : ['primary' => Color::Amber];
    }
}

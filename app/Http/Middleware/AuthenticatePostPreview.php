<?php

namespace App\Http\Middleware;

use App\Sites\SiteManager;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate as FilamentAuthenticate;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Symfony\Component\HttpFoundation\Response;

/**
 * Selecciona el panel del sitio actual y aplica su autenticación/autorización
 * habitual antes de permitir una vista previa.
 */
class AuthenticatePostPreview extends FilamentAuthenticate
{
    public function __construct(AuthFactory $auth, protected SiteManager $sites)
    {
        parent::__construct($auth);
    }

    public function handle($request, $next, ...$guards): Response
    {
        // Route middleware can run before the IdentifySite middleware from the
        // web group. Resolve the same site context here before finding the
        // corresponding Filament panel.
        return app(IdentifySite::class)->handle($request, function ($request) use ($next, $guards): Response {
            $site = $this->sites->current();
            $panel = $site ? Filament::getPanel($site->slug, isStrict: false) : null;

            abort_if($panel === null, 404);

            Filament::setCurrentPanel($panel);

            return parent::handle($request, $next, ...$guards);
        });
    }
}

<?php

namespace App\Filament\Middleware;

use App\Sites\SiteManager;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fija el sitio que corresponde al panel que se esta sirviendo.
 *
 * El id del panel es el slug del sitio, asi que el sitio se deduce de la
 * propia URL y no hace falta guardarlo en sesion: cada asociacion tiene su
 * panel y no hay forma de cambiar de sitio desde dentro.
 *
 * Se aplica al panel entero, tambien al login, para que el nombre y los
 * colores del panel sean los del sitio correcto.
 */
class IdentifyPanelSite
{
    public function __construct(protected SiteManager $sites) {}

    public function handle(Request $request, Closure $next): Response
    {
        $slug = Filament::getCurrentPanel()?->getId();

        $site = $slug ? $this->sites->resolveBySlug($slug) : null;

        abort_if($site === null, 404, 'Este panel no corresponde a ningun sitio valido.');

        $this->sites->set($site);

        config(['app.name' => $site->name]);

        view()->share('site', $site);

        return $next($request);
    }
}

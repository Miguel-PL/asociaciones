<?php

namespace App\Filament\Middleware;

use App\Sites\Site;
use App\Sites\SiteManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fija el sitio que se esta editando en el panel.
 *
 * Usa una clave de sesion propia (site.panel) y no la del sitio publico
 * (site.active): si compartieran clave, cambiar de sitio en el panel haria
 * que la web publica se sirviera con el sitio equivocada al(admin) que la
 * acaba de cambiar.
 *
 * El panel nunca se queda sin sitio: si no hay seleccion se usa el que
 * declara sites.default. Asi SiteScope siempre filtra y el contenido de
 * una web nunca se mezcla con el de otra en la misma tabla.
 */
class IdentifyPanelSite
{
    public const SESSION_KEY = 'site.panel';

    public function __construct(protected SiteManager $sites) {}

    public function handle(Request $request, Closure $next): Response
    {
        $site = $this->resolve($request);

        abort_if($site === null, 404, 'No hay ningun sitio disponible para editar.');

        if ($request->hasSession() && $request->session()->get(self::SESSION_KEY) !== $site->slug) {
            $request->session()->put(self::SESSION_KEY, $site->slug);
        }

        $this->sites->set($site);

        config(['app.name' => $site->name]);

        view()->share('site', $site);

        return $next($request);
    }

    protected function resolve(Request $request): ?Site
    {
        if ($request->hasSession() && $slug = $request->session()->get(self::SESSION_KEY)) {
            if ($site = $this->sites->resolveBySlug((string) $slug)) {
                return $site;
            }
        }

        return $this->sites->default();
    }
}

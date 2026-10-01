<?php

namespace App\Http\Middleware;

use App\Sites\Site;
use App\Sites\SiteManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Determina que sitio se esta sirviendo y lo fija como sitio activo.
 *
 * Prioridad de resolucion:
 *   1. El selector del panel de administracion (sesion).
 *   2. La variable SITE del entorno.
 *   3. El dominio de la peticion.
 *   4. El sitio configurado por defecto.
 */
class IdentifySite
{
    /** Clave de sesion del selector del panel de administracion. */
    public const SESSION_KEY = 'site.active';

    public function __construct(protected SiteManager $sites) {}

    public function handle(Request $request, Closure $next): Response
    {
        $site = $this->resolve($request);

        abort_if($site === null, 404, 'Sitio no disponible.');

        $this->sites->set($site);

        // El nombre del sitio manda sobre APP_NAME, para que el panel y las
        // vistas muestren el nombre correcto.
        config(['app.name' => $site->name]);

        view()->share('site', $site);

        return $next($request);
    }

    protected function resolve(Request $request): ?Site
    {
        if ($request->hasSession() && $request->session()->has(self::SESSION_KEY)) {
            $selected = $this->sites->resolveBySlug(
                (string) $request->session()->get(self::SESSION_KEY)
            );

            if ($selected) {
                return $selected;
            }
        }

        if ($forced = $this->sites->forceSlug()) {
            return $this->sites->resolveBySlug($forced);
        }

        return $this->sites->resolveByHost($request->getHost())
            ?? $this->sites->default();
    }
}

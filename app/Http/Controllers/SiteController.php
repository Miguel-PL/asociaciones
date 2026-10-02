<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Sites\Site;
use App\Sites\SiteManager;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

/**
 * Base de las pantallas públicas de una web.
 *
 * Resuelve a qué asociación se está sirviendo la petición y deja en las vistas
 * lo que usan la cabecera y el pie: páginas de menú, redes y enlaces legales.
 *
 * Todo lo que llega de la base de datos ya viene filtrado por el sitio activo,
 * así que ninguna consulta puede mostrar contenido de otra asociación.
 */
abstract class SiteController extends Controller
{
    /**
     * Páginas de menú y enlaces legales, resueltos una vez por petición.
     *
     * @return array<string, mixed>
     */
    protected function viewData(Site $site, array $extra = []): array
    {
        return [
            'site' => $site,
            'navPages' => $this->navPages(),
            'socialLinks' => $site->social,
            'legalLinks' => $this->legalLinks($site),
            ...$extra,
        ];
    }

    /**
     * El sitio que se está sirviendo.
     *
     * IdentifySite ya lo ha fijado, así que solo hay que leerlo. Si no
     * hubiera sitio, el propio middleware habría cortado la petición.
     */
    protected function site(SiteManager $sites): Site
    {
        return $sites->current();
    }

    /**
     * Páginas de menú, indexadas por slug.
     *
     * @return Collection<string, Page>
     */
    protected function navPages(): Collection
    {
        return Page::query()
            ->published()
            ->whereIn('slug', $this->navSlugs())
            ->orderBy('title')
            ->get()
            ->keyBy('slug');
    }

    /**
     * Slugs de menú declarados por el sitio.
     *
     * @return array<int, string>
     */
    protected function navSlugs(): array
    {
        return app(SiteManager::class)->current()?->nav ?? [];
    }

    /**
     * Enlaces legales del pie, resueltos a su URL.
     *
     * @return array<string, string>
     */
    protected function legalLinks(Site $site): array
    {
        $labels = [
            'legal' => 'Aviso legal',
            'privacy' => 'Privacidad',
            'cookies' => 'Cookies',
        ];

        $pages = Page::query()
            ->published()
            ->whereIn('slug', array_values($site->footer))
            ->get()
            ->keyBy('slug');

        $links = [];

        foreach ($site->footer as $key => $slug) {
            if ($page = $pages->get($slug)) {
                $links[$labels[$key] ?? str($key)->title()->toString()] = route('pages.show', $page);
            }
        }

        return $links;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function view(string $view, array $data): View
    {
        return view($view, $data);
    }
}

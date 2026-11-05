<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\SiteSetting;
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
        $settings = SiteSetting::forSite($site);

        return [
            'site' => $site,
            'navPages' => $this->navPages(),
            'footerContact' => $settings->footerContact(),
            'socialLinks' => $settings->socialLinks(),
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
     * Páginas publicadas para el menú principal; las configuradas conservan
     * su orden y las nuevas se añaden por título.
     *
     * @return Collection<int, Page>
     */
    protected function navPages(): Collection
    {
        $site = app(SiteManager::class)->current();
        $configuredOrder = $site?->nav ?? [];
        $footerSlugs = array_values($site?->footer ?? []);

        return Page::query()
            ->published()
            // Los enlaces legales ya aparecen en el pie, no repetirlos arriba.
            ->whereNotIn('slug', $footerSlugs)
            ->get()
            // Mantener primero el orden configurado y añadir páginas nuevas
            // después, ordenadas por título.
            ->sort(function (Page $left, Page $right) use ($configuredOrder): int {
                $leftPosition = array_search($left->slug, $configuredOrder, true);
                $rightPosition = array_search($right->slug, $configuredOrder, true);

                if ($leftPosition !== false || $rightPosition !== false) {
                    if ($leftPosition === false) {
                        return 1;
                    }

                    if ($rightPosition === false) {
                        return -1;
                    }

                    return $leftPosition <=> $rightPosition;
                }

                return strcasecmp($left->title, $right->title);
            })
            ->values();
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

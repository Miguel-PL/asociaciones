<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\SiteController;
use App\Models\Page;
use App\Sites\SiteManager;
use Illuminate\Contracts\View\View;

/**
 * Páginas estáticas de cada asociación.
 */
class PageController extends SiteController
{
    /**
     * Página concreta.
     *
     * El slug es único dentro del sitio, y SiteScope ya impide que una
     * asociación sirva la página de la otra. Solo se alcanzan las publicadas.
     */
    public function show(string $page, SiteManager $sites): View
    {
        $site = $this->site($sites);

        $pagina = Page::query()
            ->published()
            ->where('slug', $page)
            ->firstOrFail();

        return $this->view('public.pages.show', $this->viewData($site, [
            'page' => $pagina,
        ]));
    }
}

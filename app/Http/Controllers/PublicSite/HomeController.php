<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\SiteController;
use App\Models\Post;
use App\Sites\SiteManager;
use Illuminate\Contracts\View\View;

/**
 * Portada de la web: las noticias más recientes y acceso a las páginas.
 */
class HomeController extends SiteController
{
    /**
     * Noticias que aparecen destacadas en la portada.
     */
    protected const HIGHLIGHTS = 3;

    public function __invoke(SiteManager $sites): View
    {
        $site = $this->site($sites);

        return $this->view('public.home', $this->viewData($site, [
            'posts' => Post::query()->published()->limit(self::HIGHLIGHTS)->get(),
        ]));
    }
}

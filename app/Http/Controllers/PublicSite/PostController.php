<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\SiteController;
use App\Models\Post;
use App\Sites\SiteManager;
use Illuminate\Contracts\View\View;

/**
 * Listado y detalle de noticias.
 */
class PostController extends SiteController
{
    /**
     * Noticias por página en el listado.
     */
    protected const PER_PAGE = 9;

    /**
     * Noticias sugeridas al pie del detalle.
     */
    protected const RELATED = 3;

    public function index(SiteManager $sites): View
    {
        $site = $this->site($sites);

        return $this->view('public.posts.index', $this->viewData($site, [
            'posts' => Post::query()->published()->paginate(self::PER_PAGE)->withQueryString(),
        ]));
    }

    /**
     * Noticia concreta.
     *
     * Se busca con el scope published() en lugar de por enlace de modelo, para
     * que respondan 404 con el mismo camino las noticias de otra asociación
     * (SiteScope), las no publicadas y las cuya fecha aún no ha llegado. Con
     * binding por modelo, una noticia oculta se abriría y luego se ocultaría
     * en la vista.
     */
    public function show(string $post, SiteManager $sites): View
    {
        $site = $this->site($sites);

        $noticia = Post::query()
            ->published()
            ->where('slug', $post)
            ->firstOrFail();

        return $this->view('public.posts.show', $this->viewData($site, [
            'post' => $noticia->load('category'),
            'more' => Post::query()
                ->published()
                ->where($noticia->getKeyName(), '!=', $noticia->getKey())
                ->limit(self::RELATED)
                ->get(),
        ]));
    }
}

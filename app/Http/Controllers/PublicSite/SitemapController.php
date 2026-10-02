<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\SiteController;
use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Sites\SiteManager;
use Illuminate\Http\Response;

/**
 * Mapa del sitio, en XML.
 *
 * Se genera por peticion y no como archivo estatico porque cada web vive en su
 * propio dominio: un unico sitemap en public/ solo podria describir el sitio
 * que se sirvio al generarlo. Como IdentifySite ya ha resuelto el sitio, las
 * consultas salen filtradas y cada dominio recibe su propio mapa.
 */
class SitemapController extends SiteController
{
    /**
     * URLs del sitio que se esta sirviendo.
     */
    public function __invoke(SiteManager $sites): Response
    {
        $site = $this->site($sites);

        $entradas = [
            ['url' => route('home'), 'lastmod' => null],
            ['url' => route('posts.index'), 'lastmod' => null],
        ];

        foreach (Post::query()->published()->get() as $post) {
            $entradas[] = [
                'url' => route('posts.show', $post),
                'lastmod' => $post->updated_at?->toAtomString(),
            ];
        }

        // Solo las categorias con noticias publicadas: una categoria vacia es
        // una pagina sin contenido que no conviene anunciar a los buscadores.
        $categorias = Category::query()
            ->whereHas('posts', fn ($query) => $query->published())
            ->orderBy('name')
            ->get();

        foreach ($categorias as $categoria) {
            $entradas[] = ['url' => route('categories.show', $categoria), 'lastmod' => null];
        }

        foreach (Page::query()->published()->orderBy('title')->get() as $page) {
            $entradas[] = [
                'url' => route('pages.show', $page),
                'lastmod' => $page->updated_at?->toAtomString(),
            ];
        }

        return response(view('public.sitemap', ['entradas' => $entradas]), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }
}

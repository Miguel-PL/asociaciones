<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\SiteController;
use App\Filament\Resources\Posts\PostResource;
use App\Models\Category;
use App\Models\Post;
use App\Sites\SiteManager;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Filament\Facades\Filament;

/**
 * Listado y detalle de noticias.
 */
class PostController extends SiteController
{
    /**
     * Noticias por página en el listado.
     */
    protected const PER_PAGE = 6;

    /**
     * Noticias sugeridas al pie del detalle.
     */
    protected const RELATED = 3;

    public function index(SiteManager $sites, Request $request): View
    {
        $site = $this->site($sites);
        $rawSearch = $request->query('q');
        $search = is_string($rawSearch) ? trim(mb_substr($rawSearch, 0, 255)) : '';
        $rawCategoryId = $request->query('category');
        $categoryId = is_string($rawCategoryId) || is_int($rawCategoryId)
            ? (string) $rawCategoryId
            : '';

        $posts = Post::query()
            ->published()
            ->with('category')
            ->when($search !== '', function ($query) use ($search): void {
                foreach (preg_split('/\s+/u', $search, -1, PREG_SPLIT_NO_EMPTY) ?: [] as $word) {
                    $query->where('title', 'like', '%'.$word.'%');
                }
            })
            ->when(filled($categoryId), fn ($query) => $query->where('category_id', $categoryId))
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return $this->view('public.posts.index', $this->viewData($site, [
            'posts' => $posts,
            'categories' => Category::query()->orderBy('name')->get(['id', 'name']),
            'search' => $search,
            'selectedCategory' => (string) $categoryId,
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

    /**
     * Muestra una noticia guardada aunque todavía no esté publicada.
     *
     * La ruta requiere sesión autenticada y la cuenta debe poder entrar al
     * panel correspondiente al sitio activo. Las consultas siguen aplicando
     * SiteScope para impedir el acceso a registros de otra asociación.
     */
    public function preview(string $post, SiteManager $sites, Request $request): Response
    {
        $site = $this->site($sites);
        $panel = Filament::getPanel($site->slug, isStrict: false);
        $user = $request->user();

        abort_unless($panel && $user && $user->canAccessPanel($panel), 403);

        $noticia = Post::query()
            ->where('slug', $post)
            ->firstOrFail()
            ->load('category');

        $editUrl = PostResource::getUrl(
            'edit',
            ['record' => $noticia],
            panel: $site->slug,
        );

        return response()
            ->view('public.posts.show', $this->viewData($site, [
                'post' => $noticia,
                'more' => Post::query()
                    ->published()
                    ->where($noticia->getKeyName(), '!=', $noticia->getKey())
                    ->limit(self::RELATED)
                    ->with('category')
                    ->get(),
                'isPreview' => true,
                'previewEditUrl' => $editUrl,
            ]))
            ->header('Cache-Control', 'private, no-store, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('X-Robots-Tag', 'noindex, nofollow, noarchive');
    }
}

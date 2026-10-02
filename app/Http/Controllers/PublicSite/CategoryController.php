<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\SiteController;
use App\Models\Category;
use App\Sites\SiteManager;
use Illuminate\Contracts\View\View;

/**
 * Noticias de una categoría.
 */
class CategoryController extends SiteController
{
    /**
     * Noticias por página.
     */
    protected const PER_PAGE = 9;

    /**
     * Listado de la categoría.
     *
     * La categoría y sus noticias salen ya filtradas por el sitio activo: una
     * categoría de otra asociación responde 404 en lugar de listar vacío.
     */
    public function show(string $category, SiteManager $sites): View
    {
        $site = $this->site($sites);

        $categoria = Category::query()
            ->where('slug', $category)
            ->firstOrFail();

        return $this->view('public.categories.show', $this->viewData($site, [
            'category' => $categoria,
            'posts' => $categoria->posts()
                ->published()
                ->paginate(self::PER_PAGE)
                ->withQueryString(),
        ]));
    }
}

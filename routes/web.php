<?php

use App\Http\Controllers\PublicSite\CategoryController;
use App\Http\Controllers\PublicSite\HomeController;
use App\Http\Controllers\PublicSite\PageController;
use App\Http\Controllers\PublicSite\PostController;
use Illuminate\Support\Facades\Route;

/*
| Rutas públicas.
|
| Las sirve IdentifySite, que ya ha determinado qué asociación se está
| viendo por el dominio. Un solo archivo de rutas vale para todas
| las webs y ninguna necesita conocer el nombre de la otra: el contenido que
| devuelve cada pantalla ya viene filtrado por sitio.
|
| La página por slug va la última a propósito: como ocupa un solo segmento, si
| se declarara antes se tragaría /noticias y /categorias.
*/

Route::get('/', HomeController::class)->name('home');

Route::get('/noticias', [PostController::class, 'index'])->name('posts.index');
Route::get('/noticias/{post}', [PostController::class, 'show'])->name('posts.show');

Route::get('/categorias/{category}', [CategoryController::class, 'show'])->name('categories.show');

/*
| Páginas estáticas, identificadas por su slug.
|
| Se excluyen a mano los segmentos que sirven rutas del panel, de salud o de
| archivos, para que /admin siga siendo un 404 y no se interprete como el
| slug de una página. El patrón ya rechaza los puntos, así que /sitemap.xml
| nunca colisiona; los dos nombres se excluyen igualmente por si algún día se
| acepta un slug con punto.
*/
Route::get('/{page}', [PageController::class, 'show'])
    ->where('page', '^(?!admin|up|storage|api|css|js|assets|sitemap|robots)[a-z0-9]+(?:-[a-z0-9]+)*$')
    ->name('pages.show');

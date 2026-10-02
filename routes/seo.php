<?php

use App\Http\Controllers\PublicSite\RobotsController;
use App\Http\Controllers\PublicSite\SitemapController;
use Illuminate\Support\Facades\Route;

/*
| Rutas para los buscadores.
|
| Van aparte de routes/web.php a propósito: no pasan por el grupo web, así que
| un rastreo no recibe cookies de sesión ni genera CSRF. Lo único que necesitan
| es saber qué sitio se está sirviendo, que es lo que hace el middleware site.
|
| Son rutas y no archivos de public/ porque cada web se sirve en su propio
| dominio: un único sitemap en disco solo describiría el sitio que se sirvió al
| generarlo.
|
| Ojo: no hay que volver a crear public/robots.txt. El servidor web sirve
| primero los archivos estáticos de public/ y ese archivo taparía esta ruta,
| dejando el mismo robots.txt para todas las asociaciones.
*/

Route::middleware('site')->group(function (): void {
    Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
    Route::get('/robots.txt', RobotsController::class)->name('robots');
});

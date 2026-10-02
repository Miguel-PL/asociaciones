<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\SiteController;
use App\Sites\SiteManager;
use Illuminate\Http\Response;

/**
 * robots.txt por dominio.
 *
 * Va como ruta y no como archivo de public/ por lo mismo que el sitemap: cada
 * web tiene su propio dominio y su propio panel que no debe indexarse.
 */
class RobotsController extends SiteController
{
    public function __invoke(SiteManager $sites): Response
    {
        $site = $this->site($sites);

        $lineas = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /'.$site->slug.'/admin/',
            '',
            'Sitemap: '.route('sitemap'),
        ];

        return response(implode("\n", $lineas)."\n", 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}

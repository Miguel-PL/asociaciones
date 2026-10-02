<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Sites\Site;
use App\Sites\SiteManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * La web pública se resuelve por dominio y solo muestra el contenido del
 * sitio que se está sirviendo.
 *
 * Las mismas rutas sirven las dos webs, así que estos tests comprueban sobre
 * todo la separación: que una asociación no llegue a ver nada de la otra ni
 * por una URL copiada de la otra.
 */
class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    protected SiteManager $sites;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sites = app(SiteManager::class);
    }

    /**
     * Fija el sitio activo para escribir contenido.
     *
     * Es como funciona el panel: el contenido se crea con un sitio ya elegido
     * y BelongsToSite le asigna site_id.
     */
    protected function activar(string $slug): void
    {
        $this->sites->set(Site::fromConfig($slug, ['name' => ucfirst($slug)]));
    }

    /**
     * Pide una URL como lo haría el dominio de esa web.
     *
     * IdentifySite resuelve el sitio en cada petición a partir del dominio. Si
     * se pide una ruta relativa, Symfony usa el host de APP_URL (localhost) y
     * todas las peticiones caerían en el sitio por defecto, así que aquí se
     * pide la URL completa: es lo que hace el navegador al entrar por el
     * dominio de cada asociación.
     */
    protected function visitar(string $slug, string $uri)
    {
        return $this->get("http://{$slug}.test{$uri}");
    }

    protected function noticia(string $titulo, array $atributos = []): Post
    {
        return Post::create([
            'title' => $titulo,
            'excerpt' => 'Entradilla de la noticia.',
            'body' => 'Cuerpo de la noticia.',
            'is_published' => true,
            'published_at' => now()->subDay(),
            ...$atributos,
        ]);
    }

    protected function pagina(string $titulo, array $atributos = []): Page
    {
        return Page::create([
            'title' => $titulo,
            'body' => 'Cuerpo de la página.',
            'is_published' => true,
            'published_at' => now()->subDay(),
            ...$atributos,
        ]);
    }

    public function test_the_home_page_renders_the_name_of_the_site(): void
    {
        $this->visitar('caudete-se-mueve', '/')
            ->assertOk()
            ->assertSee('Caudete Se Mueve');
    }

    public function test_each_domain_serves_its_own_site(): void
    {
        $this->visitar('caudete-se-mueve', '/')->assertSee('Caudete Se Mueve');
        $this->visitar('miradas-violetas', '/')->assertSee('Miradas Violetas');
    }

    public function test_the_home_page_shows_the_published_news(): void
    {
        $this->activar('caudete-se-mueve');
        $this->noticia('Noticia visible');

        $this->visitar('caudete-se-mueve', '/')
            ->assertOk()
            ->assertSee('Noticia visible');
    }

    public function test_the_home_page_does_not_list_unpublished_news(): void
    {
        $this->activar('caudete-se-mueve');
        $this->noticia('Borrador', ['is_published' => false]);

        $this->visitar('caudete-se-mueve', '/')
            ->assertOk()
            ->assertDontSee('Borrador');
    }

    public function test_the_news_list_only_shows_published_ones(): void
    {
        $this->activar('caudete-se-mueve');
        $this->noticia('Publicada');
        $this->noticia('Borrador', ['is_published' => false]);
        $this->noticia('Programada', ['published_at' => now()->addWeek()]);

        $this->visitar('caudete-se-mueve', '/noticias')
            ->assertOk()
            ->assertSee('Publicada')
            ->assertDontSee('Borrador')
            ->assertDontSee('Programada');
    }

    public function test_a_news_detail_renders(): void
    {
        $this->activar('caudete-se-mueve');
        $post = $this->noticia('Detalle de prueba');

        $this->visitar('caudete-se-mueve', '/noticias/'.$post->slug)
            ->assertOk()
            ->assertSee('Detalle de prueba')
            ->assertSee('Cuerpo de la noticia.');
    }

    public function test_an_unpublished_news_is_not_reachable(): void
    {
        $this->activar('caudete-se-mueve');
        $post = $this->noticia('Secreto', ['is_published' => false]);

        // 404 y no un 200 con la noticia escondida: la URL no debe revelar
        // siquiera que el contenido existe.
        $this->visitar('caudete-se-mueve', '/noticias/'.$post->slug)->assertNotFound();
    }

    public function test_a_page_renders(): void
    {
        $this->activar('caudete-se-mueve');
        $page = $this->pagina('Quiénes somos');

        $this->visitar('caudete-se-mueve', '/'.$page->slug)
            ->assertOk()
            ->assertSee('Quiénes somos')
            ->assertSee('Cuerpo de la página.');
    }

    public function test_an_unpublished_page_is_not_reachable(): void
    {
        $this->activar('caudete-se-mueve');
        $page = $this->pagina('Página oculta', ['is_published' => false]);

        $this->visitar('caudete-se-mueve', '/'.$page->slug)->assertNotFound();
    }

    public function test_a_category_lists_only_its_own_news(): void
    {
        $this->activar('caudete-se-mueve');
        $category = Category::create(['name' => 'Actividades']);
        $this->noticia('De actividades', ['category_id' => $category->id]);
        $this->noticia('De otra cosa');

        $this->visitar('caudete-se-mueve', '/categorias/'.$category->slug)
            ->assertOk()
            ->assertSee('De actividades')
            ->assertDontSee('De otra cosa');
    }

    public function test_a_site_never_shows_the_news_of_another_site(): void
    {
        $this->activar('caudete-se-mueve');
        $this->noticia('Noticia de Caudete');

        $this->activar('miradas-violetas');
        $this->noticia('Noticia de Miradas');

        $this->visitar('miradas-violetas', '/')
            ->assertOk()
            ->assertSee('Noticia de Miradas')
            ->assertDontSee('Noticia de Caudete');

        $this->visitar('caudete-se-mueve', '/')
            ->assertOk()
            ->assertSee('Noticia de Caudete')
            ->assertDontSee('Noticia de Miradas');
    }

    public function test_a_site_cannot_reach_the_news_of_another_site_by_url(): void
    {
        // La noticia se crea en Caudete y se pide su slug desde el dominio de
        // Miradas. Aunque la URL sea correcta, SiteScope debe impedir el acceso.
        $this->activar('caudete-se-mueve');
        $post = $this->noticia('Noticia de Caudete');

        $this->visitar('miradas-violetas', '/noticias/'.$post->slug)->assertNotFound();
        $this->visitar('miradas-violetas', '/noticias')->assertOk()->assertDontSee('Noticia de Caudete');
    }

    public function test_two_sites_can_have_a_page_with_the_same_slug(): void
    {
        $this->activar('caudete-se-mueve');
        $this->pagina('Quiénes somos', ['body' => 'Somos de Caudete.']);

        $this->activar('miradas-violetas');
        $this->pagina('Quiénes somos', ['body' => 'Somos de Miradas.']);

        $this->visitar('miradas-violetas', '/quienes-somos')
            ->assertOk()
            ->assertSee('Somos de Miradas.')
            ->assertDontSee('Somos de Caudete.');

        $this->visitar('caudete-se-mueve', '/quienes-somos')
            ->assertOk()
            ->assertSee('Somos de Caudete.')
            ->assertDontSee('Somos de Miradas.');
    }

    public function test_a_site_cannot_reach_the_category_of_another_site(): void
    {
        $this->activar('caudete-se-mueve');
        $category = Category::create(['name' => 'Actividades de Caudete']);

        $this->visitar('miradas-violetas', '/categorias/'.$category->slug)->assertNotFound();
    }

    public function test_the_page_uses_the_colors_of_the_site_being_served(): void
    {
        // Los colores viajan como variables CSS, así que las dos webs
        // comparten una sola hoja de estilos compilada.
        $this->visitar('caudete-se-mueve', '/')
            ->assertSee('--site-primary: #1d4ed8', false);

        $this->visitar('miradas-violetas', '/')
            ->assertSee('--site-primary: #7e22ce', false);
    }

    public function test_the_reserved_paths_are_not_mistaken_for_pages(): void
    {
        // El slug de página ocupa un solo segmento, así que sin excluir estos
        // nombres /admin se interpretaría como una página llamada "admin".
        $this->visitar('caudete-se-mueve', '/admin')->assertNotFound();
        $this->visitar('caudete-se-mueve', '/api')->assertNotFound();
    }

    public function test_the_sitemap_lists_only_the_content_of_the_site_being_served(): void
    {
        $this->activar('caudete-se-mueve');
        $this->noticia('Noticia de Caudete');
        $categoria = Category::create(['name' => 'Actividades de Caudete']);
        $this->pagina('Pagina de Caudete');

        $this->activar('miradas-violetas');
        $this->noticia('Noticia de Miradas');
        $this->pagina('Pagina de Miradas');

        $sitemap = $this->visitar('miradas-violetas', '/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

        $xml = $sitemap->getContent();

        $this->assertStringContainsString('<loc>http://miradas-violetas.test</loc>', $xml);
        $this->assertStringContainsString('<loc>http://miradas-violetas.test/noticias/noticia-de-miradas</loc>', $xml);
        $this->assertStringContainsString('<loc>http://miradas-violetas.test/pagina-de-miradas</loc>', $xml);

        $this->assertStringNotContainsString('caudete', $xml);
        $this->assertStringNotContainsString('Noticia de Caudete', $xml);
    }

    public function test_the_sitemap_skips_empty_categories_and_drafts(): void
    {
        $this->activar('caudete-se-mueve');
        $vacia = Category::create(['name' => 'Categoria vacia']);
        $conNoticias = Category::create(['name' => 'Actividades']);
        $this->noticia('Visible', ['category_id' => $conNoticias->id]);
        $this->noticia('Borrador', ['is_published' => false]);
        $this->pagina('Borrador', ['is_published' => false]);

        $xml = $this->visitar('caudete-se-mueve', '/sitemap.xml')->getContent();

        // Una categoría sin noticias publicadas sería una página vacía.
        $this->assertStringNotContainsString($vacia->slug, $xml);
        $this->assertStringContainsString('/categorias/'.$conNoticias->slug, $xml);
        $this->assertStringNotContainsString('/noticias/borrador', $xml);
        $this->assertStringNotContainsString('/borrador<', $xml);
    }

    public function test_each_site_serves_its_own_sitemap(): void
    {
        $this->visitar('caudete-se-mueve', '/sitemap.xml')
            ->assertOk()
            ->assertSee('http://caudete-se-mueve.test', false);

        $this->visitar('miradas-violetas', '/sitemap.xml')
            ->assertOk()
            ->assertSee('http://miradas-violetas.test', false);
    }

    public function test_robots_points_to_the_sitemap_and_hides_the_panel(): void
    {
        $robots = $this->visitar('caudete-se-mueve', '/robots.txt')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->getContent();

        $this->assertStringContainsString('Sitemap: http://caudete-se-mueve.test/sitemap.xml', $robots);
        $this->assertStringContainsString('Disallow: /caudete-se-mueve/admin/', $robots);
        $this->assertStringNotContainsString('miradas', $robots);
    }

    public function test_a_news_describes_itself_to_the_networks(): void
    {
        // Open Graph y Twitter Cards: lo que se comparte en redes.
        $this->activar('caudete-se-mueve');
        $post = $this->noticia('Noticia para redes', [
            'excerpt' => 'La entradilla que vera el navegador al compartir.',
            'cover_image' => 'portadas/noticia.jpg',
        ]);

        $html = $this->visitar('caudete-se-mueve', '/noticias/'.$post->slug)
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('<meta property="og:type" content="article">', $html);
        $this->assertStringContainsString('<meta property="og:title" content="Noticia para redes · Caudete Se Mueve">', $html);
        $this->assertStringContainsString('og:description" content="La entradilla que vera el navegador al compartir."', $html);
        $this->assertStringContainsString('og:image" content="http://caudete-se-mueve.test/storage/portadas/noticia.jpg"', $html);
        $this->assertStringContainsString('og:url" content="http://caudete-se-mueve.test/noticias/'.$post->slug.'"', $html);
        $this->assertStringContainsString('<meta name="twitter:card" content="summary_large_image">', $html);
        $this->assertStringContainsString('article:published_time', $html);
    }

    public function test_a_news_without_a_cover_image_falls_back_to_the_social_card_of_the_site(): void
    {
        $this->activar('caudete-se-mueve');
        $post = $this->noticia('Noticia sin portada');

        $html = $this->visitar('caudete-se-mueve', '/noticias/'.$post->slug)
            ->assertOk()
            ->getContent();

        // La tarjeta del sitio, y no el logo: un SVG no se dibuja en redes.
        $this->assertStringContainsString('<meta property="og:image" content="http://caudete-se-mueve.test/sites/caudete-se-mueve/assets/social.png">', $html);
    }

    public function test_the_home_page_describes_itself_with_the_data_of_the_site(): void
    {
        // Sin secciones propias, los metadatos salen del site.php de cada sitio.
        $this->visitar('caudete-se-mueve', '/')
            ->assertOk()
            ->assertSee('<meta property="og:site_name" content="Caudete Se Mueve">', false)
            ->assertSee('<meta property="og:type" content="website">', false);

        $this->visitar('miradas-violetas', '/')
            ->assertOk()
            ->assertSee('<meta property="og:site_name" content="Miradas Violetas">', false);
    }
}

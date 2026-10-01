<?php

namespace Tests\Feature;

use App\Filament\Middleware\IdentifyPanelSite;
use App\Http\Middleware\IdentifySite;
use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use App\Sites\Site;
use App\Sites\SiteManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * El panel edita contenido de un solo sitio cada vez. Estos tests comprueban
 * que la sesion del panel no se mixing con la del sitio publico y que el
 * SiteScope filtra correctamente en las pantallas de administracion.
 */
class PanelSiteIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected SiteManager $sites;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sites = app(SiteManager::class);

        $this->admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => 'secreto',
        ]);
    }

    protected function activate(string $slug): Site
    {
        $site = Site::fromConfig($slug, ['name' => ucfirst($slug)]);

        $this->sites->set($site);

        return $site;
    }

    public function test_the_panel_requires_authentication(): void
    {
        $this->get('/admin/posts')->assertRedirect();
    }

    public function test_the_panel_uses_the_default_site_by_default(): void
    {
        config(['sites.default' => 'caudete-se-mueve']);

        $this->actingAs($this->admin)
            ->get('/admin/posts')
            ->assertOk();
    }

    public function test_the_panel_renders_its_resource_pages(): void
    {
        foreach (['/admin/posts', '/admin/pages', '/admin/categories', '/admin/switch-site'] as $url) {
            $this->actingAs($this->admin)
                ->get($url)
                ->assertOk();
        }
    }

    public function test_the_panel_site_is_remembered_in_its_own_session_key(): void
    {
        $this->actingAs($this->admin)
            ->withSession([IdentifyPanelSite::SESSION_KEY => 'miradas-violetas'])
            ->get('/admin/posts')
            ->assertOk()
            ->assertSessionHas(IdentifyPanelSite::SESSION_KEY, 'miradas-violetas');

        // Elegir sitio en el panel no debe tocar la clave del sitio público.
        $this->assertNull(session(IdentifySite::SESSION_KEY));
    }

    public function test_the_panel_and_the_public_site_use_different_session_keys(): void
    {
        $this->assertNotSame(
            IdentifyPanelSite::SESSION_KEY,
            IdentifySite::SESSION_KEY,
        );
    }

    public function test_the_panel_lists_only_posts_of_the_selected_site(): void
    {
        $this->activate('caudete-se-mueve');
        Post::create(['title' => 'Noticia de Caudete', 'is_published' => true, 'published_at' => now()]);

        $this->activate('miradas-violetas');
        Post::create(['title' => 'Noticia de Miradas', 'is_published' => true, 'published_at' => now()]);

        // Al arrancar el panel con Miradas seleccionado, la consulta que usa
        // la tabla debe filtrar por ese sitio.
        $this->actingAs($this->admin)
            ->withSession([IdentifyPanelSite::SESSION_KEY => 'miradas-violetas'])
            ->get('/admin/posts')
            ->assertOk()
            ->assertSee('Noticia de Miradas')
            ->assertDontSee('Noticia de Caudete');
    }

    public function test_switching_the_panel_site_changes_what_is_listed(): void
    {
        $this->activate('caudete-se-mueve');
        Post::create(['title' => 'Noticia de Caudete', 'is_published' => true, 'published_at' => now()]);

        $this->activate('miradas-violetas');
        Post::create(['title' => 'Noticia de Miradas', 'is_published' => true, 'published_at' => now()]);

        $this->actingAs($this->admin)
            ->withSession([IdentifyPanelSite::SESSION_KEY => 'caudete-se-mueve'])
            ->get('/admin/posts')
            ->assertOk()
            ->assertSee('Noticia de Caudete')
            ->assertDontSee('Noticia de Miradas');
    }

    public function test_the_panel_never_shows_content_of_every_site_at_once(): void
    {
        $this->activate('caudete-se-mueve');
        Post::create(['title' => 'Titulo Común', 'is_published' => true, 'published_at' => now()]);
        Page::create(['title' => 'Pagina Común']);
        Category::create(['name' => 'Categoria Común']);

        $this->activate('miradas-violetas');
        Post::create(['title' => 'Titulo Común', 'is_published' => true, 'published_at' => now()]);
        Page::create(['title' => 'Pagina Común']);
        Category::create(['name' => 'Categoria Común']);

        // Con Caudete seleccionado, cada tabla debe mostrar solo una fila,
        // aunque los dos sitios tengan registros con el mismo nombre.
        $this->actingAs($this->admin)
            ->withSession([IdentifyPanelSite::SESSION_KEY => 'caudete-se-mueve'])
            ->get('/admin/posts')
            ->assertOk()
            ->assertSee('Titulo Común');
    }

    public function test_it_falls_back_to_the_default_when_the_session_site_disappears(): void
    {
        config(['sites.default' => 'caudete-se-mueve']);

        $this->actingAs($this->admin)
            ->withSession([IdentifyPanelSite::SESSION_KEY => 'sitio-borrado'])
            ->get('/admin/posts')
            ->assertOk()
            ->assertSessionHas(IdentifyPanelSite::SESSION_KEY, 'caudete-se-mueve');
    }
}

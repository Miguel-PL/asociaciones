<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Scopes\SiteScope;
use App\Sites\Site;
use App\Sites\SiteManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * El aislamiento por sitio es la garantia central del multisite: una web
 * nunca debe ver ni editar contenido de otra.
 */
class SiteContentIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected SiteManager $sites;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sites = app(SiteManager::class);
    }

    protected function activate(string $slug): ?Site
    {
        $site = Site::fromConfig($slug, ['name' => ucfirst($slug)]);

        $this->sites->set($site);

        return $site;
    }

    public function test_it_assigns_the_active_site_to_new_content(): void
    {
        $this->activate('caudete-se-mueve');

        $post = Post::create([
            'title' => 'Noticia de prueba',
            'body' => 'Cuerpo',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $this->assertSame('caudete-se-mueve', $post->site_id);
    }

    public function test_it_only_returns_content_of_the_active_site(): void
    {
        $this->activate('caudete-se-mueve');
        Post::create(['title' => 'De Caudete', 'is_published' => true, 'published_at' => now()]);

        $this->activate('miradas-violetas');
        Post::create(['title' => 'De Miradas', 'is_published' => true, 'published_at' => now()]);

        $this->activate('caudete-se-mueve');
        $this->assertSame(['De Caudete'], Post::pluck('title')->all());

        $this->activate('miradas-violetas');
        $this->assertSame(['De Miradas'], Post::pluck('title')->all());
    }

    public function test_it_keeps_the_same_slug_in_two_sites(): void
    {
        $this->activate('caudete-se-mueve');
        $first = Post::create(['title' => 'Mismo titulo', 'is_published' => true, 'published_at' => now()]);

        $this->activate('miradas-violetas');
        $second = Post::create(['title' => 'Mismo titulo', 'is_published' => true, 'published_at' => now()]);

        $this->assertSame('mismo-titulo', $first->slug);
        $this->assertSame($first->slug, $second->slug);
        $this->assertNotSame($first->id, $second->id);
    }

    public function test_it_generates_a_unique_slug_within_a_site(): void
    {
        $this->activate('caudete-se-mueve');

        $first = Post::create(['title' => 'Convocatoria', 'is_published' => true, 'published_at' => now()]);
        $second = Post::create(['title' => 'Convocatoria', 'is_published' => true, 'published_at' => now()]);

        $this->assertSame('convocatoria', $first->slug);
        $this->assertSame('convocatoria-2', $second->slug);
    }

    public function test_it_generates_the_slug_of_a_category_from_its_name(): void
    {
        $this->activate('caudete-se-mueve');

        $category = Category::create(['name' => 'Actividades Familiares']);

        $this->assertSame('actividades-familiares', $category->slug);
    }

    public function test_it_respects_an_explicit_slug(): void
    {
        $this->activate('caudete-se-mueve');

        $post = Post::create(['title' => 'Asamblea', 'slug' => 'mi-slug-a-medida']);

        $this->assertSame('mi-slug-a-medida', $post->slug);
    }

    public function test_it_does_not_apply_the_filter_without_an_active_site(): void
    {
        $this->activate('caudete-se-mueve');
        Post::create(['title' => 'De Caudete', 'is_published' => true, 'published_at' => now()]);

        $this->activate('miradas-violetas');
        Post::create(['title' => 'De Miradas', 'is_published' => true, 'published_at' => now()]);

        // Consola, colas y panel pueden no tener sitio activo.
        $this->sites->set(null);

        $this->assertCount(2, Post::all());
    }

    public function test_it_scopes_pages_and_categories_too(): void
    {
        $this->activate('caudete-se-mueve');
        Page::create(['title' => 'Pagina de Caudete']);
        Category::create(['name' => 'Categoria de Caudete']);

        $this->activate('miradas-violetas');

        $this->assertCount(0, Page::all());
        $this->assertCount(0, Category::all());
    }

    public function test_it_can_query_a_specific_site_ignoring_the_active_one(): void
    {
        $this->activate('caudete-se-mueve');
        Post::create(['title' => 'De Caudete', 'is_published' => true, 'published_at' => now()]);

        $this->activate('miradas-violetas');
        Post::create(['title' => 'De Miradas', 'is_published' => true, 'published_at' => now()]);

        $this->assertSame(
            ['De Caudete'],
            Post::forSite('caudete-se-mueve')->pluck('title')->all(),
        );

        $this->assertSame(
            ['De Caudete', 'De Miradas'],
            Post::withoutGlobalScope(SiteScope::class)
                ->orderBy('title')
                ->pluck('title')
                ->all(),
        );
    }

    public function test_published_scope_hides_drafts_and_future_posts(): void
    {
        $this->activate('caudete-se-mueve');

        Post::create(['title' => 'Visible', 'is_published' => true, 'published_at' => now()->subDay()]);
        Post::create(['title' => 'Borrador', 'is_published' => false, 'published_at' => null]);
        Post::create(['title' => 'Programada', 'is_published' => true, 'published_at' => now()->addWeek()]);

        $this->assertSame(['Visible'], Post::published()->pluck('title')->all());
    }

    public function test_it_resolves_the_owning_site_of_a_model(): void
    {
        $this->activate('caudete-se-mueve');
        $post = Post::create(['title' => 'Noticia']);

        $this->assertTrue($post->belongsToSite('caudete-se-mueve'));
        $this->assertFalse($post->belongsToSite('miradas-violetas'));

        // site() lo resuelve contra el registro real de la aplicacion.
        $this->assertSame('caudete-se-mueve', $post->site()?->slug);
    }
}

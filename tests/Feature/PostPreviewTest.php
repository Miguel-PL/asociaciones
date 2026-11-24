<?php

namespace Tests\Feature;

use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Filament\Resources\Posts\Pages\ListPosts;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use App\Sites\Site;
use App\Sites\SiteManager;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PostPreviewTest extends TestCase
{
    use RefreshDatabase;

    protected SiteManager $sites;

    protected User $caudeteUser;

    protected User $miradasUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sites = app(SiteManager::class);
        $this->activateSite('caudete-se-mueve');

        $this->caudeteUser = User::create([
            'site_id' => 'caudete-se-mueve',
            'name' => 'Administración Caudete',
            'email' => 'caudete-preview@example.test',
            'password' => 'secreto',
        ]);

        $this->miradasUser = User::create([
            'site_id' => 'miradas-violetas',
            'name' => 'Administración Miradas',
            'email' => 'miradas-preview@example.test',
            'password' => 'secreto',
        ]);
    }

    public function test_a_guest_cannot_preview_a_draft_and_public_routes_still_hide_it(): void
    {
        $post = $this->draft('Borrador privado');

        $this->get($this->previewUrl($post))->assertRedirect();
        $this->get($this->publicUrl($post))->assertNotFound();
    }

    public function test_preview_route_initializes_site_context_before_panel_authentication(): void
    {
        $post = $this->draft('Borrador sin contexto previo');
        $this->sites->set(null);

        $this->get($this->previewUrl($post))->assertRedirect();
    }

    public function test_an_authorized_site_admin_can_preview_a_draft_with_public_layout_and_content(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('portadas/vista-previa.jpg', 'fixture');

        $category = Category::create(['name' => 'Actividades']);
        $post = $this->draft('Borrador con formato', [
            'category_id' => $category->id,
            'cover_image' => 'portadas/vista-previa.jpg',
            'published_at' => now()->addDay(),
            'body' => '<h2>Sección principal</h2><p>Párrafo de prueba con <strong>negrita</strong> y <em>cursiva</em>.</p><h3>Subsección</h3><ul><li><p>Elemento con viñeta</p></li></ul><ol><li><p>Elemento numerado</p></li></ol><p><a href="https://example.org/">Enlace de prueba</a></p>',
        ]);

        $this->actingAs($this->caudeteUser)
            ->get($this->previewUrl($post))
            ->assertOk()
            ->assertSee('Vista previa')
            ->assertSee('esta noticia todavía no está publicada')
            ->assertSee('Volver a editar')
            ->assertSee('Actividades')
            ->assertSee('Borrador con formato')
            ->assertSee('<h2>Sección principal</h2>', false)
            ->assertSee('<p>Párrafo de prueba con ', false)
            ->assertSee('<h3>Subsección</h3>', false)
            ->assertSee('<strong>negrita</strong>', false)
            ->assertSee('<em>cursiva</em>', false)
            ->assertSee('<ul>', false)
            ->assertSee('<ol>', false)
            ->assertSee('<a href="https://example.org/"', false)
            ->assertSee('Enlace de prueba')
            ->assertSee('<time datetime="'.$post->published_at->toDateString().'">', false)
            ->assertSee('storage/portadas/vista-previa.jpg')
            ->assertHeader('Cache-Control', 'max-age=0, no-store, private')
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive');
    }

    public function test_an_admin_from_another_site_cannot_preview_the_post(): void
    {
        $post = $this->draft('Borrador de Caudete');

        $this->actingAs($this->miradasUser)
            ->get($this->previewUrl($post))
            ->assertForbidden();
    }

    public function test_admin_news_table_search_and_category_filter_include_drafts_and_published_posts(): void
    {
        $category = Category::create(['name' => 'Asambleas']);
        $otherCategory = Category::create(['name' => 'Actividades']);
        $draft = $this->draft('Asamblea en borrador', ['category_id' => $category->id]);
        $published = $this->draft('Asamblea publicada', [
            'category_id' => $category->id,
            'is_published' => true,
            'published_at' => now()->subMinute(),
        ]);
        $other = $this->draft('Asamblea de otra categoría', ['category_id' => $otherCategory->id]);

        $this->actingAs($this->caudeteUser);
        Filament::setCurrentPanel(Filament::getPanel('caudete-se-mueve'));

        Livewire::test(ListPosts::class)
            ->searchTable('Asamblea')
            ->assertCanSeeTableRecords([$draft, $published, $other])
            ->filterTable('category', $category->id)
            ->assertCanSeeTableRecords([$draft, $published])
            ->assertCanNotSeeTableRecords([$other])
            ->searchTable('borrador')
            ->assertCanSeeTableRecords([$draft])
            ->assertCanNotSeeTableRecords([$published]);
    }

    public function test_admin_news_table_keeps_search_and_category_when_moving_between_pages(): void
    {
        $category = Category::create(['name' => 'Boletines']);
        $otherCategory = Category::create(['name' => 'Actividades']);
        $posts = [];

        for ($number = 1; $number <= 12; $number++) {
            $posts[] = $this->draft('Noticias filtro '.str_pad((string) $number, 2, '0', STR_PAD_LEFT), [
                'category_id' => $category->id,
                'published_at' => now()->addDays($number),
            ]);
        }

        $this->draft('Noticias filtro en otra categoría', ['category_id' => $otherCategory->id]);
        $this->draft('Otro anuncio de la categoría', ['category_id' => $category->id]);
        $this->actingAs($this->caudeteUser);
        Filament::setCurrentPanel(Filament::getPanel('caudete-se-mueve'));

        Livewire::test(ListPosts::class)
            ->searchTable('Noticias filtro')
            ->filterTable('category', $category->id)
            ->assertCountTableRecords(12)
            ->call('gotoPage', 2, 'page')
            ->assertCountTableRecords(12)
            ->assertCanSeeTableRecords([$posts[1], $posts[0]])
            ->assertCanNotSeeTableRecords([$posts[11]]);
    }

    public function test_published_news_remains_available_through_its_public_route(): void
    {
        $post = $this->draft('Noticia publicada', [
            'is_published' => true,
            'published_at' => now()->subMinute(),
        ]);

        $this->get($this->publicUrl($post))
            ->assertOk()
            ->assertSee('Noticia publicada')
            ->assertDontSee('Vista previa');
    }

    public function test_create_preview_saves_the_form_without_publishing_the_post(): void
    {
        $this->actingAs($this->caudeteUser);
        Filament::setCurrentPanel(Filament::getPanel('caudete-se-mueve'));

        Livewire::test(CreatePost::class)
            ->fillForm([
                'title' => 'Nueva noticia en borrador',
                'slug' => 'nueva-noticia-en-borrador',
                'body' => '<h2>Contenido guardado</h2><p>Texto.</p>',
                'is_published' => false,
            ])
            ->assertSee('Vista previa')
            ->call('createForPreview')
            ->assertHasNoFormErrors()
            ->assertRedirect(route('posts.preview', ['post' => 'nueva-noticia-en-borrador']));

        $this->assertDatabaseHas('posts', [
            'slug' => 'nueva-noticia-en-borrador',
            'is_published' => false,
        ]);

        $this->actingAs($this->caudeteUser)
            ->get($this->previewUrl(Post::query()->whereSlug('nueva-noticia-en-borrador')->firstOrFail()))
            ->assertOk()
            ->assertSee('<h2>Contenido guardado</h2>', false);
    }

    public function test_create_preview_does_not_publish_when_the_form_is_marked_public(): void
    {
        $this->actingAs($this->caudeteUser);
        Filament::setCurrentPanel(Filament::getPanel('caudete-se-mueve'));

        Livewire::test(CreatePost::class)
            ->fillForm([
                'title' => 'No publicar al previsualizar',
                'slug' => 'no-publicar-al-previsualizar',
                'is_published' => true,
                'published_at' => now()->toDateTimeString(),
            ])
            ->call('createForPreview');

        $this->assertDatabaseMissing('posts', [
            'slug' => 'no-publicar-al-previsualizar',
        ]);
    }

    public function test_edit_preview_saves_changes_without_changing_publication_status(): void
    {
        $post = $this->draft('Noticia antes de editar');
        $this->actingAs($this->caudeteUser);
        Filament::setCurrentPanel(Filament::getPanel('caudete-se-mueve'));

        Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])
            ->fillForm([
                'title' => 'Noticia editada en borrador',
                'body' => '<h2>Cambio guardado</h2><p>Texto.</p>',
                'is_published' => false,
            ])
            ->assertSee('Vista previa')
            ->call('saveForPreview')
            ->assertHasNoFormErrors()
            ->assertRedirect(route('posts.preview', ['post' => $post->slug]));

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Noticia editada en borrador',
            'is_published' => false,
        ]);

        $this->actingAs($this->caudeteUser)
            ->get($this->previewUrl($post->refresh()))
            ->assertOk()
            ->assertSee('Cambio guardado')
            ->assertSee('Vista previa');
    }

    public function test_edit_preview_does_not_publish_a_draft_from_unsaved_form_values(): void
    {
        $post = $this->draft('Borrador que debe seguir privado');
        $this->actingAs($this->caudeteUser);
        Filament::setCurrentPanel(Filament::getPanel('caudete-se-mueve'));

        Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])
            ->fillForm([
                'title' => 'No publicar al previsualizar',
                'is_published' => true,
                'published_at' => now()->toDateTimeString(),
            ])
            ->call('saveForPreview');

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Borrador que debe seguir privado',
            'is_published' => false,
        ]);
    }

    protected function activateSite(string $slug): void
    {
        $this->sites->set(Site::load($slug));
    }

    /** @param array<string, mixed> $attributes */
    protected function draft(string $title, array $attributes = []): Post
    {
        $this->activateSite('caudete-se-mueve');

        return Post::create([
            'title' => $title,
            'body' => '<p>Contenido de prueba.</p>',
            'is_published' => false,
            ...$attributes,
        ]);
    }

    protected function previewUrl(Post $post): string
    {
        return 'http://caudete-se-mueve.test/noticias/'.$post->slug.'/vista-previa';
    }

    protected function publicUrl(Post $post): string
    {
        return 'http://caudete-se-mueve.test/noticias/'.$post->slug;
    }
}

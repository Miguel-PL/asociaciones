<?php

namespace Tests\Feature;

use App\Filament\Auth\Login;
use App\Filament\Resources\Categories\Pages\CreateCategory;
use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use App\Sites\Site;
use App\Sites\SiteManager;
use Database\Seeders\AdminUserSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Cada sitio tiene su propio panel en /{slug}/admin y sus propias cuentas.
 *
 * Estos tests comprueban lo que mas importa de esa separacion: que una cuenta
 * de una asociacion no entre, ni siquiera a ver, en el panel de la otra, y que
 * cada panel solo liste el contenido de su sitio.
 */
class PanelSiteIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected User $caudete;

    protected User $miradas;

    protected User $sinSitio;

    protected User $superadmin;

    protected SiteManager $sites;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sites = app(SiteManager::class);

        $this->caudete = $this->usuarioDe('caudete-se-mueve', 'caudete@example.test');
        $this->miradas = $this->usuarioDe('miradas-violetas', 'miradas@example.test');
        $this->sinSitio = User::create([
            'name' => 'Sin sitio',
            'email' => 'sinsitio@example.test',
            'password' => 'secreto',
        ]);
        $this->superadmin = User::create([
            'is_super_admin' => true,
            'name' => 'Superadmin',
            'email' => 'superadmin@example.test',
            'password' => 'secreto',
        ]);
    }

    protected function usuarioDe(string $slug, string $email): User
    {
        return User::create([
            'site_id' => $slug,
            'name' => ucfirst($slug),
            'email' => $email,
            'password' => 'secreto',
        ]);
    }

    /**
     * Crea contenido del sitio indicado como activo, que es lo que usa
     * SiteScope para completar site_id.
     */
    protected function activar(string $slug): void
    {
        $this->sites->set(Site::fromConfig($slug, ['name' => ucfirst($slug)]));
    }

    /**
     * Visita una pantalla del panel como esa cuenta, en sesion limpia.
     *
     * Cada asociacion entra desde su propio navegador, asi que al pasar de una
     * cuenta a otra hay que cambiar de sesion. Sin esto, el middleware
     * AuthenticateSession de Filament cierra la sesion anterior y la segunda
     * peticion acaba en el login en lugar de en el panel.
     */
    protected function verComo(User $usuario, string $url)
    {
        Auth::logout();

        $this->flushSession();

        return $this->actingAs($usuario)->get($url);
    }

    public function test_each_site_has_its_own_panel(): void
    {
        $this->verComo($this->caudete, '/caudete-se-mueve/admin')->assertOk();
        $this->verComo($this->miradas, '/miradas-violetas/admin')->assertOk();
    }

    public function test_the_panel_requires_authentication(): void
    {
        $this->get('/caudete-se-mueve/admin')->assertRedirect();
    }

    public function test_a_user_cannot_enter_the_panel_of_another_site(): void
    {
        $this->verComo($this->caudete, '/miradas-violetas/admin')->assertForbidden();

        $this->verComo($this->miradas, '/caudete-se-mueve/admin')->assertForbidden();
    }

    public function test_a_user_cannot_reach_the_content_screens_of_another_site(): void
    {
        // El 403 no debe depender de la pantalla: tampoco las tablas.
        $this->actingAs($this->caudete)
            ->get('/miradas-violetas/admin/posts')
            ->assertForbidden();
    }

    public function test_a_user_without_a_site_cannot_enter_any_panel(): void
    {
        $this->verComo($this->sinSitio, '/caudete-se-mueve/admin')->assertForbidden();

        $this->verComo($this->sinSitio, '/miradas-violetas/admin')->assertForbidden();
    }

    public function test_a_user_enters_only_its_own_panel(): void
    {
        $this->assertTrue($this->caudete->canAccessPanel(Filament::getPanel('caudete-se-mueve')));
        $this->assertFalse($this->caudete->canAccessPanel(Filament::getPanel('miradas-violetas')));
        $this->assertFalse($this->sinSitio->canAccessPanel(Filament::getPanel('caudete-se-mueve')));
    }

    public function test_the_login_of_a_site_rejects_the_accounts_of_another(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('miradas-violetas'));

        Livewire::test(Login::class)
            ->fillForm([
                'email' => $this->caudete->email,
                'password' => 'secreto',
            ])
            ->call('authenticate')
            ->assertHasFormErrors(['email']);

        $this->assertGuest();
    }

    public function test_the_login_of_a_site_accepts_its_own_accounts(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('caudete-se-mueve'));

        Livewire::test(Login::class)
            ->fillForm([
                'email' => $this->caudete->email,
                'password' => 'secreto',
            ])
            ->call('authenticate')
            ->assertHasNoFormErrors();

        $this->assertAuthenticatedAs($this->caudete);
    }

    public function test_the_superadmin_enters_every_panel(): void
    {
        $this->verComo($this->superadmin, '/caudete-se-mueve/admin')->assertOk();
        $this->verComo($this->superadmin, '/miradas-violetas/admin')->assertOk();

        $this->assertTrue($this->superadmin->canAccessPanel(Filament::getPanel('caudete-se-mueve')));
        $this->assertTrue($this->superadmin->canAccessPanel(Filament::getPanel('miradas-violetas')));
    }

    public function test_the_superadmin_can_log_in_on_any_panel(): void
    {
        // El login filtra por site_id, y el superadmin no pertenece a ninguna
        // asociacion: sin esta excepcion no podria entrar en ningun panel.
        foreach (['caudete-se-mueve', 'miradas-violetas'] as $slug) {
            Filament::setCurrentPanel(Filament::getPanel($slug));

            Livewire::test(Login::class)
                ->fillForm([
                    'email' => $this->superadmin->email,
                    'password' => 'secreto',
                ])
                ->call('authenticate')
                ->assertHasNoFormErrors();

            $this->assertAuthenticatedAs($this->superadmin);

            Auth::logout();
            $this->flushSession();
        }
    }

    public function test_the_superadmin_does_not_turn_the_other_accounts_into_admin(): void
    {
        // Que exista una cuenta que lo ve todo no debe relajar el aislamiento
        // del resto: estas dos siguen siendo cuentas de su asociacion.
        $this->assertFalse($this->caudete->isSuperAdmin());
        $this->assertFalse($this->sinSitio->isSuperAdmin());
        $this->assertNull($this->superadmin->site_id);

        $this->verComo($this->caudete, '/miradas-violetas/admin')->assertForbidden();
        $this->verComo($this->sinSitio, '/caudete-se-mueve/admin')->assertForbidden();
    }

    public function test_the_superadmin_only_sees_the_content_of_the_panel_it_is_in(): void
    {
        // Puede entrar en los dos paneles, pero dentro de cada uno sigue viendo
        // un solo sitio: entrar en un panel no le da acceso al contenido de los
        // demas.
        $this->activar('caudete-se-mueve');
        Post::create(['title' => 'Noticia de Caudete', 'is_published' => true, 'published_at' => now()]);

        $this->activar('miradas-violetas');
        Post::create(['title' => 'Noticia de Miradas', 'is_published' => true, 'published_at' => now()]);

        $this->verComo($this->superadmin, '/caudete-se-mueve/admin/posts')
            ->assertOk()
            ->assertSee('Noticia de Caudete')
            ->assertDontSee('Noticia de Miradas');

        $this->verComo($this->superadmin, '/miradas-violetas/admin/posts')
            ->assertOk()
            ->assertSee('Noticia de Miradas')
            ->assertDontSee('Noticia de Caudete');
    }

    public function test_content_created_by_the_superadmin_goes_to_the_site_of_the_panel(): void
    {
        // El superadmin no tiene site_id, asi que si el panel no lo fijara, el
        // contenido se crearia sin sitio y no apareceria en ninguna web.
        $this->activar('caudete-se-mueve');
        $this->verComo($this->superadmin, '/caudete-se-mueve/admin/categories/create')
            ->assertOk();

        Livewire::test(CreateCategory::class)
            ->fillForm(['name' => 'Categoria desde la superadmin'])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('categories', [
            'name' => 'Categoria desde la superadmin',
            'site_id' => 'caudete-se-mueve',
        ]);
    }

    public function test_the_seeder_creates_a_usable_superadmin(): void
    {
        // Es la cuenta con la que se trabaja a diario, asi que el seeder tiene
        // que dejarla lista y no duplicarla al re-ejecutarse.
        $this->seed(AdminUserSeeder::class);

        $superadmin = User::query()->where('email', 'admin@asociaciones.test')->sole();

        $this->assertSame('admin@asociaciones.test', $superadmin->email);
        $this->assertNull($superadmin->site_id);
        $this->assertTrue(Hash::check('asociaciones', $superadmin->password));

        $this->actingAs($superadmin)->get('/miradas-violetas/admin')->assertOk();

        $this->seed(AdminUserSeeder::class);

        $this->assertSame(1, User::query()->where('email', 'admin@asociaciones.test')->count());
    }

    public function test_each_panel_renders_its_resource_pages(): void
    {
        foreach (['/caudete-se-mueve/admin/posts', '/caudete-se-mueve/admin/pages', '/caudete-se-mueve/admin/categories'] as $url) {
            $this->actingAs($this->caudete)->get($url)->assertOk();
        }
    }

    public function test_each_panel_lists_only_the_content_of_its_own_site(): void
    {
        $this->activar('caudete-se-mueve');
        Post::create(['title' => 'Noticia de Caudete', 'is_published' => true, 'published_at' => now()]);

        $this->activar('miradas-violetas');
        Post::create(['title' => 'Noticia de Miradas', 'is_published' => true, 'published_at' => now()]);

        $this->verComo($this->caudete, '/caudete-se-mueve/admin/posts')
            ->assertOk()
            ->assertSee('Noticia de Caudete')
            ->assertDontSee('Noticia de Miradas');

        $this->verComo($this->miradas, '/miradas-violetas/admin/posts')
            ->assertOk()
            ->assertSee('Noticia de Miradas')
            ->assertDontSee('Noticia de Caudete');
    }

    public function test_each_panel_shows_the_name_of_its_own_site(): void
    {
        $this->actingAs($this->caudete)
            ->get('/caudete-se-mueve/admin')
            ->assertOk()
            ->assertSee('Caudete Se Mueve')
            ->assertDontSee('Miradas Violetas');
    }

    public function test_two_sites_can_hold_content_with_the_same_title(): void
    {
        $this->activar('caudete-se-mueve');
        Post::create(['title' => 'Titulo Común', 'is_published' => true, 'published_at' => now()]);
        Page::create(['title' => 'Pagina Común']);
        Category::create(['name' => 'Categoria Común']);

        $this->activar('miradas-violetas');
        Post::create(['title' => 'Titulo Común', 'is_published' => true, 'published_at' => now()]);
        Page::create(['title' => 'Pagina Común']);
        Category::create(['name' => 'Categoria Común']);

        // Cada panel ve solo su fila, aunque los dos sitios usen el mismo
        // titulo y el mismo slug.
        $this->verComo($this->caudete, '/caudete-se-mueve/admin/posts')
            ->assertOk()
            ->assertSee('Titulo Común');

        $this->verComo($this->miradas, '/miradas-violetas/admin/posts')
            ->assertOk()
            ->assertSee('Titulo Común');
    }

    public function test_there_is_no_shared_panel_and_no_site_switcher(): void
    {
        // El panel unico y el selector de sitio ya no existen: cada
        // asociacion entra por su propia ruta.
        $this->actingAs($this->caudete)->get('/admin')->assertNotFound();
        $this->actingAs($this->caudete)->get('/caudete-se-mueve/admin/switch-site')->assertNotFound();
    }

    public function test_each_panel_can_reach_the_create_and_edit_screens(): void
    {
        // El boton "Crear" de los listados tiene que llevar a una pantalla real:
        // sin la ruta create, el CRUD del panel esta roto.
        foreach (['posts', 'pages', 'categories'] as $recurso) {
            $this->verComo($this->caudete, "/caudete-se-mueve/admin/{$recurso}/create")
                ->assertOk();

            $this->verComo($this->miradas, "/miradas-violetas/admin/{$recurso}/create")
                ->assertOk();
        }

        $this->activar('caudete-se-mueve');
        $registro = Category::create(['name' => 'Categoria para editar']);

        $this->verComo($this->caudete, "/caudete-se-mueve/admin/categories/{$registro->id}/edit")
            ->assertOk()
            ->assertSee('Categoria para editar');
    }

    public function test_content_created_from_a_panel_belongs_to_that_site(): void
    {
        $this->activar('caudete-se-mueve');
        $this->verComo($this->caudete, '/caudete-se-mueve/admin/categories/create')
            ->assertOk();

        Livewire::test(CreateCategory::class)
            ->fillForm(['name' => 'Huerto comunitario'])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('categories', [
            'name' => 'Huerto comunitario',
            'site_id' => 'caudete-se-mueve',
        ]);
    }

    public function test_a_user_cannot_edit_the_content_of_another_site(): void
    {
        // El registro existe y la URL es correcta, pero el SiteScope impide que
        // el panel de la otra asociacion lo alcance.
        $this->activar('miradas-violetas');
        $registro = Category::create(['name' => 'Categoria de Miradas']);

        $this->verComo($this->caudete, "/caudete-se-mueve/admin/categories/{$registro->id}/edit")
            ->assertNotFound();
    }
}

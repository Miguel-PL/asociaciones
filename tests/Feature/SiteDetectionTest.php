<?php

namespace Tests\Feature;

use App\Sites\SiteManager;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SiteDetectionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // El .env de desarrollo fija SITE; los tests deben empezar limpios.
        config([
            'sites.path' => dirname(__DIR__).'/Fixtures/sites',
            'sites.default' => null,
            'sites.override' => null,
        ]);

        Route::get('/__sitio', function () {
            $site = app(SiteManager::class)->current();
            $shared = view()->shared('site');

            return [
                'slug' => $site?->slug,
                'name' => $site?->name,
                'app_name' => config('app.name'),
                'shared_slug' => $shared?->slug,
            ];
        })->middleware('web');
    }

    public function test_it_resolves_the_site_from_the_request_domain(): void
    {
        $this->get('http://unico.test/__sitio')
            ->assertOk()
            ->assertJsonPath('slug', 'sitio-de-prueba')
            ->assertJsonPath('name', 'Sitio de prueba');
    }

    public function test_it_falls_back_to_the_default_site_for_unknown_domains(): void
    {
        config(['sites.default' => 'sitio-de-prueba']);

        $this->get('http://no-registrado.test/__sitio')
            ->assertOk()
            ->assertJsonPath('slug', 'sitio-de-prueba');
    }

    public function test_the_environment_override_wins_over_the_domain(): void
    {
        config(['sites.override' => 'duplicado']);

        $this->get('http://unico.test/__sitio')
            ->assertOk()
            ->assertJsonPath('slug', 'duplicado');
    }

    public function test_the_environment_override_wins_over_the_default(): void
    {
        config(['sites.override' => 'duplicado', 'sites.default' => 'sitio-de-prueba']);

        $this->get('http://no-registrado.test/__sitio')
            ->assertOk()
            ->assertJsonPath('slug', 'duplicado');
    }

    public function test_it_returns_404_when_the_overridden_site_does_not_exist(): void
    {
        config(['sites.override' => 'no-existe']);

        $this->get('http://unico.test/__sitio')->assertNotFound();
    }

    public function test_it_returns_404_when_the_overridden_site_is_disabled(): void
    {
        config(['sites.override' => 'deshabilitado']);

        $this->get('http://unico.test/__sitio')->assertNotFound();
    }

    public function test_it_returns_404_when_there_is_no_site_at_all(): void
    {
        config(['sites.path' => dirname(__DIR__).'/Fixtures/vacio', 'sites.default' => null, 'sites.override' => null]);

        $this->get('http://unico.test/__sitio')->assertNotFound();
    }

    public function test_the_session_selection_wins_over_the_environment_override(): void
    {
        config(['sites.override' => 'duplicado']);

        $this->withSession(['site.active' => 'sitio-de-prueba'])
            ->get('http://duplicado.test/__sitio')
            ->assertOk()
            ->assertJsonPath('slug', 'sitio-de-prueba');
    }

    public function test_a_disabled_session_selection_is_ignored(): void
    {
        config(['sites.override' => 'duplicado']);

        $this->withSession(['site.active' => 'deshabilitado'])
            ->get('http://unico.test/__sitio')
            ->assertOk()
            ->assertJsonPath('slug', 'duplicado');
    }

    public function test_it_overrides_the_application_name_with_the_site_name(): void
    {
        $this->get('http://unico.test/__sitio')
            ->assertOk()
            ->assertJsonPath('app_name', 'Sitio de prueba');
    }

    public function test_it_shares_the_active_site_with_the_views(): void
    {
        $this->get('http://unico.test/__sitio')
            ->assertOk()
            ->assertJsonPath('shared_slug', 'sitio-de-prueba');
    }
}

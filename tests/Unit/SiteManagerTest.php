<?php

namespace Tests\Unit;

use App\Sites\SiteManager;
use Tests\TestCase;

class SiteManagerTest extends TestCase
{
    protected function useFixtures(): void
    {
        config(['sites.path' => $this->fixturePath()]);
    }

    protected function fixturePath(): string
    {
        return dirname(__DIR__).'/Fixtures/sites';
    }

    public function test_it_discovers_every_folder_with_a_site_php(): void
    {
        $this->useFixtures();

        $this->assertSame(
            ['deshabilitado', 'duplicado', 'sitio-de-prueba'],
            app(SiteManager::class)->slugs(),
        );
    }

    public function test_it_ignores_folders_without_a_site_php(): void
    {
        $this->useFixtures();

        $this->assertArrayNotHasKey('sin-config', app(SiteManager::class)->all());
    }

    public function test_it_skips_configuration_files_that_cannot_be_loaded(): void
    {
        $this->useFixtures();

        $this->assertArrayNotHasKey('invalido', app(SiteManager::class)->all());
    }

    public function test_it_resolves_a_site_by_domain(): void
    {
        $this->useFixtures();

        $this->assertSame(
            'sitio-de-prueba',
            app(SiteManager::class)->resolveByHost('unico.test')?->slug,
        );
    }

    public function test_it_ignores_case_when_resolving_by_domain(): void
    {
        $this->useFixtures();

        $this->assertSame(
            'sitio-de-prueba',
            app(SiteManager::class)->resolveByHost('UNICO.TEST')?->slug,
        );
    }

    public function test_the_first_site_alphabetical_wins_a_duplicated_domain(): void
    {
        $this->useFixtures();

        // 'duplicado' declara tambien sitio-de-prueba.test. El registro esta
        // ordenado por slug, asi que se resuelve de forma determinista.
        $this->assertSame(
            'duplicado',
            app(SiteManager::class)->resolveByHost('sitio-de-prueba.test')?->slug,
        );
    }

    public function test_it_returns_null_for_an_unknown_domain(): void
    {
        $this->useFixtures();

        $this->assertNull(app(SiteManager::class)->resolveByHost('desconocido.test'));
    }

    public function test_it_returns_null_for_an_unknown_slug(): void
    {
        $this->useFixtures();

        $this->assertNull(app(SiteManager::class)->resolveBySlug('no-existe'));
    }

    public function test_it_does_not_resolve_a_disabled_site(): void
    {
        $this->useFixtures();

        $this->assertNull(app(SiteManager::class)->resolveBySlug('deshabilitado'));
        $this->assertNull(app(SiteManager::class)->resolveByHost('deshabilitado.test'));
    }

    public function test_it_excludes_disabled_sites_from_the_enabled_list(): void
    {
        $this->useFixtures();

        $this->assertArrayNotHasKey('deshabilitado', app(SiteManager::class)->enabled());
    }

    public function test_it_falls_back_to_the_configured_default(): void
    {
        $this->useFixtures();
        config(['sites.default' => 'sitio-de-prueba']);

        $this->assertSame('sitio-de-prueba', app(SiteManager::class)->default()?->slug);
    }

    public function test_it_falls_back_to_the_first_enabled_site_without_a_default(): void
    {
        $this->useFixtures();
        config(['sites.default' => null]);

        $this->assertSame('duplicado', app(SiteManager::class)->default()?->slug);
    }

    public function test_it_flushes_the_cached_sites(): void
    {
        $this->useFixtures();

        $manager = app(SiteManager::class);
        $this->assertCount(3, $manager->all());

        $manager->flush();

        $this->assertCount(3, $manager->all());
    }
}

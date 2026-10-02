<?php

namespace Tests\Unit;

use App\Sites\Site;
use InvalidArgumentException;
use Tests\TestCase;

class SiteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['sites.path' => dirname(__DIR__).'/Fixtures/sites']);
    }

    public function test_it_loads_the_configuration_from_site_php(): void
    {
        $site = Site::load('sitio-de-prueba');

        $this->assertSame('sitio-de-prueba', $site->slug);
        $this->assertSame('Sitio de prueba', $site->name);
        $this->assertSame('Fixture para los tests', $site->tagline);
        $this->assertSame(['sitio-de-prueba.test', 'unico.test'], $site->domains);
        $this->assertSame('#123456', $site->color('primary'));
        $this->assertTrue($site->enabled);
    }

    public function test_it_fails_when_the_site_has_no_configuration_file(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('no tiene site.php');

        Site::load('no-existe');
    }

    public function test_it_fails_when_a_required_key_is_missing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('debe definir [name]');

        Site::load('invalido');
    }

    public function test_it_matches_domains_ignoring_case_and_port(): void
    {
        $site = Site::load('sitio-de-prueba');

        $this->assertTrue($site->hasDomain('sitio-de-prueba.test'));
        $this->assertTrue($site->hasDomain('SITIO-DE-PRUEBA.TEST'));
        $this->assertTrue($site->hasDomain('sitio-de-prueba.test:8080'));
        $this->assertTrue($site->hasDomain('  sitio-de-prueba.test  '));
        $this->assertFalse($site->hasDomain('otro.test'));
    }

    public function test_it_falls_back_to_the_default_colour(): void
    {
        $site = Site::load('sitio-de-prueba');

        $this->assertSame('#000000', $site->color('inexistente', '#000000'));
        $this->assertNull($site->color('inexistente'));
    }

    public function test_it_returns_null_for_assets_that_do_not_exist(): void
    {
        $site = Site::load('sitio-de-prueba');

        $this->assertNull($site->asset('no-existe.svg'));
        $this->assertNull($site->asset(null));
        $this->assertNotNull($site->asset('logo.svg'));
    }

    public function test_it_resolves_an_asset_written_from_the_site_directory(): void
    {
        // Los site.php guardan assets/logo.svg y las vistas pasan logo.svg: es
        // el mismo archivo y no debe depender de quien pregunta.
        $site = Site::load('sitio-de-prueba');

        $this->assertSame($site->asset('logo.svg'), $site->asset('assets/logo.svg'));
    }

    public function test_it_returns_null_when_the_site_has_no_social_card(): void
    {
        // Un sitio recien anadido puede no tener todavia su tarjeta generada, y
        // es preferible no anunciar imagen a anunciar una que no existe.
        $site = Site::fromConfig('sitio-de-prueba', ['name' => 'Sitio de prueba']);

        $this->assertNull($site->socialImage());
    }

    public function test_the_social_card_is_null_when_the_configured_file_is_missing(): void
    {
        $site = Site::fromConfig('sitio-de-prueba', [
            'name' => 'Sitio de prueba',
            'social_image' => 'assets/social.png',
        ]);

        $this->assertNull($site->socialImage());
    }
}

<?php

namespace App\Sites\Commands;

use App\Sites\Concerns\InspectsSites;
use App\Sites\SiteManager;
use Illuminate\Console\Command;

class SitesListCommand extends Command
{
    use InspectsSites;

    protected $signature = 'sites:list';

    protected $description = 'Muestra los sitios declarados en sites/';

    public function handle(SiteManager $sites): int
    {
        $all = $sites->all();

        if ($all === []) {
            $this->warn('No hay ningún sitio declarado en '.config('sites.path'));

            return self::SUCCESS;
        }

        $current = $sites->current();

        $this->table(
            ['Sitio', 'Dominios', 'Activo', 'Assets'],
            array_map(fn ($site): array => [
                $site->name.' ('.$site->slug.')',
                implode(', ', $site->domains) ?: '-',
                $site->enabled ? ($current?->slug === $site->slug ? 'sí (sirviendo)' : 'sí') : 'no',
                $this->countAssets($site->slug),
            ], $all),
        );

        if ($forced = $sites->forceSlug()) {
            $this->line('');
            $this->info("Sitio forzado por la variable SITE: {$forced}");
        }

        return self::SUCCESS;
    }

    protected function countAssets(string $slug): int
    {
        $directory = base_path("sites/{$slug}/assets");

        if (! is_dir($directory)) {
            return 0;
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
        );

        return iterator_count($files);
    }
}

<?php

namespace App\Sites\Commands;

use App\Sites\Concerns\InspectsSites;
use App\Sites\SiteManager;
use Illuminate\Console\Command;

class SitesCheckCommand extends Command
{
    use InspectsSites;

    protected $signature = 'sites:check';

    protected $description = 'Valida la configuración de cada sitio en sites/';

    public function handle(SiteManager $sites): int
    {
        $directories = $this->siteDirectories();
        $failures = 0;

        if ($directories === []) {
            $this->error('No hay carpetas de sitio en '.config('sites.path'));

            return self::FAILURE;
        }

        foreach ($directories as $slug => $directory) {
            $problems = $this->problemsFor($slug, $directory, $sites);

            if ($problems === []) {
                $this->components->twoColumnDetail($slug, '<fg=green>correcto</>');

                continue;
            }

            $failures++;

            $this->components->twoColumnDetail($slug, '<fg=red>con problemas</>');

            foreach ($problems as $problem) {
                $this->line("    <fg=red>-</> {$problem}");
            }
        }

        $this->line('');

        if ($failures > 0) {
            $this->error("{$failures} sitio(s) con problemas.");

            return self::FAILURE;
        }

        $this->info(count($directories).' sitio(s) correctos.');

        return self::SUCCESS;
    }
}

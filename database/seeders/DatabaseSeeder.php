<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * No se usa WithoutModelEvents: el contenido necesita los eventos de
     * modelo para que BelongsToSite asigne site_id y HasSlug genere el slug.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            SiteContentSeeder::class,
        ]);
    }
}

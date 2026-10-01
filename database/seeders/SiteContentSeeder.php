<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Scopes\SiteScope;
use App\Sites\SiteManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

/**
 * Contenido inicial de ejemplo para empezar a trabajar con datos reales.
 *
 * Los slugs de las páginas coinciden con los que declara el footer de
 * sites/caudete-se-mueve/site.php.
 */
class SiteContentSeeder extends Seeder
{
    public function run(SiteManager $sites): void
    {
        $slug = 'caudete-se-mueve';

        if (! $sites->has($slug)) {
            $this->command?->warn("El sitio [{$slug}] no existe, se omite el contenido.");

            return;
        }

        // El contenido se crea con el sitio activo puesto, para que
        // BelongsToSite lo asigne y SiteScope filtre como en producción.
        $site = $sites->get($slug);
        $previous = $sites->current();
        $sites->set($site);

        try {
            $categories = $this->seedCategories($slug);
            $this->seedPages($slug);
            $this->seedPosts($slug, $categories);
        } finally {
            $sites->set($previous);
        }
    }

    /**
     * @return array<string, Category>
     */
    protected function seedCategories(string $site): array
    {
        $definitions = [
            'actividades' => [
                'name' => 'Actividades',
                'description' => 'Encuentros, fiestas y actividades abiertas del barrio.',
            ],
            'participacion' => [
                'name' => 'Participación',
                'description' => 'Asambleas, propuestas y maneras de intervenir en Caudete.',
            ],
            'comunicacion' => [
                'name' => 'Comunicación',
                'description' => 'Avisos y noticias relevantes para la asociación.',
            ],
        ];

        $categories = [];

        foreach ($definitions as $slug => $data) {
            $categories[$slug] = $this->once(
                fn () => $this->create(Category::class, $site, ['slug' => $slug, ...$data]),
                fn () => $this->find(Category::class, $site, $slug),
            );
        }

        return $categories;
    }

    protected function seedPages(string $site): void
    {
        $pages = [
            [
                'slug' => 'quienes-somos',
                'title' => 'Quiénes somos',
                'meta_description' => 'Caudete Se Mueve es la asociación vecinal que trabaja por el barrio con actividades, propuestas y participación.',
                'body' => "Somos una asociación vecinal abierta a todo el barrio.\n\n"
                    .'Existimos para conocer los problemas concretos del entorno y para abrir canales de participación real, no solo consultiva.',
            ],
            [
                'slug' => 'junta-directiva',
                'title' => 'Junta directiva',
                'meta_description' => 'Quiénes forman la junta directiva de Caudete Se Mueve.',
                'body' => "La junta directiva se elige en asamblea y se renueva cada dos años.\n\n"
                    .'Cualquier vecino o vecina puede proponer su candidatura en la asamblea.',
            ],
            [
                'slug' => 'aviso-legal',
                'title' => 'Aviso legal',
                'meta_description' => 'Aviso legal de Caudete Se Mueve.',
                'body' => "Este sitio está mantenido por la asociación vecinal Caudete Se Mueve.\n\n"
                    .'Los contenidos publicados son responsabilidad de la asociación.',
            ],
            [
                'slug' => 'privacidad',
                'title' => 'Política de privacidad',
                'meta_description' => 'Qué datos tratamos en Caudete Se Mueve y con qué finalidad.',
                'body' => "No tratamos datos personales más allá de los imprescindibles para gestionar las actividades que organizamos.\n\n"
                    .'Puedes ejercer tus derechos de acceso, rectificación y supresión escribiendo a hola@caudetese-mueve.es.',
            ],
        ];

        foreach ($pages as $page) {
            $this->once(
                fn () => $this->create(Page::class, $site, [
                    ...$page,
                    'is_published' => true,
                    'published_at' => now()->subDays(30),
                ]),
                fn () => $this->find(Page::class, $site, $page['slug']),
            );
        }
    }

    /**
     * @param  array<string, Category>  $categories
     */
    protected function seedPosts(string $site, array $categories): void
    {
        $posts = [
            [
                'slug' => 'asamblea-vecinal-otono',
                'title' => 'Convocatoria de la asamblea vecinal de otoño',
                'category' => 'participacion',
                'excerpt' => 'Nos reunimos para revisar el estado del barrio y aprobar las propuestas del trimestre.',
            ],
            [
                'slug' => 'fiesta-otono-en-la-plaza',
                'title' => 'Fiesta de otoño en la plaza',
                'category' => 'actividades',
                'excerpt' => 'Música, juegos y merienda compartida. Abierto a todo el barrio.',
            ],
            [
                'slug' => 'nueva-web-de-la-asociacion',
                'title' => 'Ya puedes seguir la actividad de la asociación aquí',
                'category' => 'comunicacion',
                'excerpt' => 'Publicamos novedades, convocatorias y actas de asamblea en un único sitio.',
            ],
            [
                'slug' => 'propuesta-de-huerto-comunitario',
                'title' => 'Propuesta de huerto comunitario',
                'category' => 'participacion',
                'excerpt' => 'Buscamos un espacio municipal y personas voluntarias para sacarlo adelante.',
            ],
        ];

        foreach ($posts as $post) {
            $this->once(
                fn () => $this->create(Post::class, $site, [
                    'slug' => $post['slug'],
                    'title' => $post['title'],
                    'excerpt' => $post['excerpt'],
                    'body' => $post['excerpt']."\n\nOs invitamos a participar con vuestras propias propuestas.",
                    'category_id' => $categories[$post['category']]?->id,
                    'is_published' => true,
                    'published_at' => now()->subDays(random_int(1, 30)),
                ]),
                fn () => $this->find(Post::class, $site, $post['slug']),
            );
        }
    }

    /**
     * Crea un modelo asignando site_id de forma explícita.
     *
     * @param  class-string<Model>  $modelClass
     * @param  array<string, mixed>  $attributes
     */
    protected function create(string $modelClass, string $site, array $attributes): Model
    {
        /** @var Model $model */
        $model = new $modelClass;

        // site_id no es asignable en masa a propósito: se fija aquí.
        $model->site_id = $site;
        $model->fill($attributes);
        $model->save();

        return $model;
    }

    /**
     * Busca un modelo por sitio y slug, ignorando el filtro del sitio activo.
     *
     * @param  class-string<Model>  $modelClass
     */
    protected function find(string $modelClass, string $site, string $slug): ?Model
    {
        return $modelClass::query()
            ->withoutGlobalScope(SiteScope::class)
            ->where('site_id', $site)
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Ejecuta $create salvo que el registro ya exista.
     *
     * @param  callable(): Model  $create
     * @param  callable(): ?Model  $find
     */
    protected function once(callable $create, callable $find): Model
    {
        return $find() ?? $create();
    }
}

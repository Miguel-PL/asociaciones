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
    /**
     * Contenido de ejemplo de cada asociación.
     *
     * Se siembra para los dos sitios: tener contenido en ambos es lo que
     * permite comprobar que las mismas vistas sirven webs distintas y que
     * ninguna muestra lo de la otra.
     *
     * @var array<string, array{categories: array<string, array<string, string>>, pages: array<int, array<string, string>>, posts: array<int, array<string, string>>}>
     */
    protected array $content = [
        'caudete-se-mueve' => [
            'categories' => [
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
            ],
            'pages' => [
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
            ],
            'posts' => [
                [
                    'slug' => 'asamblea-vecinal-otono',
                    'title' => 'Convocatoria de la asamblea vecinal de otoño',
                    'category' => 'participacion',
                    'excerpt' => 'Nos reunimos para revisar el estado del barrio y aprobar las propuestas del trimestre.',
                    'days_ago' => 4,
                ],
                [
                    'slug' => 'fiesta-otono-en-la-plaza',
                    'title' => 'Fiesta de otoño en la plaza',
                    'category' => 'actividades',
                    'excerpt' => 'Música, juegos y merienda compartida. Abierto a todo el barrio.',
                    'days_ago' => 12,
                ],
                [
                    'slug' => 'nueva-web-de-la-asociacion',
                    'title' => 'Ya puedes seguir la actividad de la asociación aquí',
                    'category' => 'comunicacion',
                    'excerpt' => 'Publicamos novedades, convocatorias y actas de asamblea en un único sitio.',
                    'days_ago' => 21,
                ],
                [
                    'slug' => 'propuesta-de-huerto-comunitario',
                    'title' => 'Propuesta de huerto comunitario',
                    'category' => 'participacion',
                    'excerpt' => 'Buscamos un espacio municipal y personas voluntarias para sacarlo adelante.',
                    'days_ago' => 30,
                ],
            ],
        ],

        'miradas-violetas' => [
            'categories' => [
                'visibilidad' => [
                    'name' => 'Visibilidad',
                    'description' => 'Acciones para hacer visibles las desigualdades y las conductas machistas.',
                ],
                'formacion' => [
                    'name' => 'Formación',
                    'description' => 'Talleres y materiales para trabajar la igualdad en el barrio.',
                ],
                'comunidad' => [
                    'name' => 'Comunidad',
                    'description' => 'Encuentros y redes de apoyo entre vecinas y vecinos.',
                ],
            ],
            'pages' => [
                [
                    'slug' => 'quienes-somos',
                    'title' => 'Quiénes somos',
                    'meta_description' => 'Miradas Violetas es una red vecinal que trabaja por la igualdad y la visibilidad.',
                    'body' => "Somos una red vecinal que trabaja por la igualdad y la lucha contra la violencia de género.\n\n"
                        .'Nos organizamos alrededor del barrio y animamos a tomar la palabra y a cuidarnos.',
                ],
                [
                    'slug' => 'como-participar',
                    'title' => 'Cómo participar',
                    'meta_description' => 'Formas de participar en Miradas Violetas.',
                    'body' => "Puedes participar asistiendo a los encuentros, aplicando un taller o proponiendo una idea.\n\n"
                        .'Todas las actividades son abiertas.',
                ],
                [
                    'slug' => 'aviso-legal',
                    'title' => 'Aviso legal',
                    'meta_description' => 'Aviso legal de Miradas Violetas.',
                    'body' => "Este sitio está mantenido por la red vecinal Miradas Violetas.\n\n"
                        .'Los contenidos publicados son responsabilidad de la asociación.',
                ],
                [
                    'slug' => 'privacidad',
                    'title' => 'Política de privacidad',
                    'meta_description' => 'Qué datos tratamos en Miradas Violetas y con qué finalidad.',
                    'body' => "No tratamos datos personales más allá de los imprescindibles para organizar las actividades.\n\n"
                        .'Puedes ejercer tus derechos escribiendo a hola@miradasvioletas.es.',
                ],
            ],
            'posts' => [
                [
                    'slug' => 'taller-violeta-por-la-igualdad',
                    'title' => 'Taller Violeta por la igualdad',
                    'category' => 'formacion',
                    'excerpt' => 'Un taller práctico para reconocer y nombrar el silencio.',
                    'days_ago' => 6,
                ],
                [
                    'slug' => 'red-de-apoyo-en-el-barrio',
                    'title' => 'Red de apoyo en el barrio',
                    'category' => 'comunidad',
                    'excerpt' => 'Abrimos una red de acompañamiento entre vecinas y vecinos.',
                    'days_ago' => 15,
                ],
            ],
        ],
    ];

    public function run(SiteManager $sites): void
    {
        if (empty($this->content)) {
            return;
        }

        foreach ($this->content as $slug => $definitions) {
            if (! $sites->has($slug)) {
                $this->command?->warn("El sitio [{$slug}] no existe, se omite su contenido.");

                continue;
            }

            $this->seedSite($sites, $slug, $definitions);
        }
    }

    /**
     * @param  array{categories: array<string, array<string, string>>, pages: array<int, array<string, string>>, posts: array<int, array<string, string>>}  $definitions
     */
    protected function seedSite(SiteManager $sites, string $slug, array $definitions): void
    {
        // El contenido se crea con el sitio activo puesto, para que
        // BelongsToSite lo asigne y SiteScope filtre como en producción.
        $previous = $sites->current();
        $sites->set($sites->get($slug));

        try {
            $categories = $this->seedCategories($slug, $definitions['categories']);
            $this->seedPages($slug, $definitions['pages']);
            $this->seedPosts($slug, $definitions['posts'], $categories);
        } finally {
            $sites->set($previous);
        }
    }

    /**
     * @param  array<string, array<string, string>>  $definitions
     * @return array<string, Category>
     */
    protected function seedCategories(string $site, array $definitions): array
    {
        $categories = [];

        foreach ($definitions as $slug => $data) {
            $categories[$slug] = $this->once(
                fn () => $this->create(Category::class, $site, ['slug' => $slug, ...$data]),
                fn () => $this->find(Category::class, $site, $slug),
            );
        }

        return $categories;
    }

    /**
     * @param  array<int, array<string, string>>  $pages
     */
    protected function seedPages(string $site, array $pages): void
    {
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
     * @param  array<int, array<string, string>>  $posts
     * @param  array<string, Category>  $categories
     */
    protected function seedPosts(string $site, array $posts, array $categories): void
    {
        foreach ($posts as $post) {
            $this->once(
                fn () => $this->create(Post::class, $site, [
                    'slug' => $post['slug'],
                    'title' => $post['title'],
                    'excerpt' => $post['excerpt'],
                    'body' => $post['excerpt']."\n\nOs invitamos a participar con vuestras propias propuestas.",
                    'category_id' => $categories[$post['category']]?->id,
                    'is_published' => true,
                    // Fecha fija por noticia para que el contenido sea
                    // reproducible: dos seeds dan el mismo resultado.
                    'published_at' => now()->subDays((int) $post['days_ago']),
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

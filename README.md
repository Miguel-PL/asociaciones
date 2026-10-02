# Asociaciones - Web de Organizaciones Social

Proyecto base reutilizable para el desarrollo de webs de organizaciones sociales.

## Sitios

Este proyecto alberga múltiples webs con una misma base de código:

| Sitio | Descripción | Local |
|-------|-------------|-------|
| Caudete Se Mueve | [URL pendiente] | `caudete-se-mueve.test` |
| Miradas Violetas | [URL pendiente] | `miradas-violetas.test` |

## Arquitectura

- **Backend**: Laravel + Filament (panel de administración)
- **Frontend**: Laravel Blade
- **Base de datos**: MariaDB

La instalación de Laravel se encuentra en la raíz del proyecto. La configuración específica de cada sitio se encuentra en `sites/`.

## Estructura

```
asociaciones/
├── app/                # Código reutilizable
├── config/             # Configuración de Laravel
├── database/           # Migraciones y seeders
├── public/             # Archivos públicos
├── resources/          # Vistas Blade
├── routes/             # Rutas
├── sites/              # Configuración por sitio
│   ├── caudete-se-mueve/
│   └── miradas-violetas/
├── docs/               # Documentación
└── ...
```

## Añadir un nuevo sitio

Para añadir una tercera web, crear una nueva carpeta en `sites/` junto a un
archivo `site.php` con su configuración:

```bash
mkdir sites/nuevo-sitio/assets
mkdir sites/nuevo-sitio/content
```

## Entorno de desarrollo

Requisitos:

- PHP 8.3+
- Composer
- Node 20+
- MariaDB o MySQL

Puesta en marcha:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
```

En desarrollo, el sitio activo se fija con la variable `SITE` del `.env`, cuyo
valor debe coincidir con el nombre de la carpeta en `sites/`. En producción se
resuelve por dominio.

Cada sitio tiene su propia cuenta de administración, creada con
`php artisan db:seed`. El correo sale de `admin_email` en el `site.php` de cada
sitio y la contraseña se ajusta con `ADMIN_PASSWORD`.

## Web pública

Las dos webs comparten las mismas rutas y vistas: lo único que cambia es el
dominio por el que se entra. `IdentifySite` resuelve el sitio en cada petición y
el contenido se filtra siempre por `site_id`.

| Ruta | Contenido |
|------|-----------|
| `/` | Portada con las últimas noticias publicadas |
| `/noticias` | Listado de noticias |
| `/noticias/{slug}` | Noticia completa |
| `/categorias/{slug}` | Noticias de una categoría |
| `/{slug}` | Página estática |

Una URL copiada de otra asociación no funciona: pedir en un dominio el slug de
una noticia, categoría o página del otro sitio devuelve `404`, y la página
tampoco se resuelve aunque exista en el otro sitio.

La identidad visual de cada web sale de su `site.php` (`colors`, `logo`,
`favicon`, `social`, `contact`, `footer`). Los colores viajan como variables CSS,
así que todas las webs comparten una única hoja de estilos compilada.

### SEO

Cada página declara su título, descripción, canonical y sus datos de Open Graph
y Twitter Cards, con la identidad del sitio que se está sirviendo.

| Ruta | Contenido |
|------|-----------|
| `/sitemap.xml` | Noticias, categorías y páginas publicadas de ese sitio |
| `/robots.txt` | Apunta a su sitemap y oculta `/admin` de ese sitio |

Son rutas generadas, no archivos de `public/`, porque cada web se sirve en su
propio dominio: un único archivo en disco describiría solo el sitio que se
sirvió al generarlo. Por eso están en `routes/seo.php`, fuera del grupo `web`
para que un rastreo no reciba cookies de sesión.

> No hay que volver a crear `public/robots.txt`. El servidor web sirve primero
> los archivos estáticos de `public/`, así que ese archivo taparía la ruta y
> dejaría el mismo `robots.txt` para todas las asociaciones.

#### Imagen de reparto

`social_image` en el `site.php` es la imagen que se ve al compartir en redes, y
debe ser un PNG o JPG de 1200×630. **No puede ser el logo**: los SVG no se
dibujan en WhatsApp, Facebook ni X, y una tarjeta sin imagen se ve peor que una
con la imagen de la asociación. Si el sitio no la declara, la web no anuncia
ninguna imagen en lugar de anunciar una que no existe.

Las de los dos sitios actuales están en `sites/<slug>/assets/social.png`. Para
regenerar una tras cambiar el nombre o el lema, se compone un SVG con los colores
del `site.php` y se rasteriza con sharp:

```bash
npx sharp-cli -i tarjeta.svg -o sites/caudete-se-mueve/assets/social.png resize 1200 630 --format png
```

## Panel de administración

Cada sitio tiene su propio panel, en `/{slug}/admin`, por ejemplo
`/caudete-se-mueve/admin` y `/miradas-violetas/admin`. Los paneles se registran
sola a partir de las carpetas de `sites/`, así que añadir una web nueva da su
panel automáticamente.

Las cuentas no se comparten: una cuenta solo entra al panel de su asociación y
el login del otro sitio rechaza sus credenciales. El contenido de cada panel
está además filtrado por sitio, de modo que una asociación no ve ni puede
editar lo de la otra.

El código de recursos, páginas y widgets es común a todos los paneles; no se
duplica nada por sitio.

## Cuentas de administración

| Cuenta | Site | Paneles a los que entra |
|--------|------|-------------------------|
| `admin@asociaciones.test` | ninguno | todos (superadmin) |
| `caudete@asociaciones.test` | caudete-se-mueve | solo el de Caudete |
| `miradas@asociaciones.test` | miradas-violetas | solo el de Miradas |

Las contraseñas salen de `SUPERADMIN_PASSWORD` y `ADMIN_PASSWORD`, con
`asociaciones` como valor por defecto en local. Hay que cambiarlas antes de
publicar nada.

La cuenta de la plataforma se reconoce por `is_super_admin`, no por `site_id`:
entra en todos los paneles, pero dentro de cada uno sigue viendo solo el
contenido de ese sitio. Estar en todos los paneles no significa ver todo el
contenido de golpe; para eso hay que entrar en el panel del sitio que interesa.

`php artisan db:seed` crea las tres y es idempotente: se puede volver a lanzar
para recuperar el acceso si alguien se queda fuera.

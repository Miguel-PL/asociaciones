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

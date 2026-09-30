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

El usuario del panel de administración se crea con `php artisan db:seed`. Sus
credenciales se pueden ajustar con `ADMIN_EMAIL` y `ADMIN_PASSWORD`.

## Panel de administración

Disponible en `/admin`.

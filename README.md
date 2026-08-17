# Asociaciones - Web de Organizaciones Social

Proyecto base reutilizable para el desarrollo de webs de organizaciones sociales.

## Sitios

Este proyecto alberga múltiples webs con una misma base de código:

| Sitio | Descripción |
|-------|-------------|
| Caudete Se Mueve | [URL pendiente] |
| Miradas Violetas | [URL pendiente] |

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

Para añadir una tercera web, crear una nueva carpeta en `sites/`:

```bash
mkdir sites/nuevo-sitio/assets
mkdir sites/nuevo-sitio/content
```

## Requisitos

- PHP 8.1+
- Composer
- MariaDB

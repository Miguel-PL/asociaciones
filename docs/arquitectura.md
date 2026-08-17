# Arquitectura del Proyecto

## Visión general

Proyecto multi-sitio para desarrollar webs de organizaciones sociales, basado en Laravel + Filament. Una sola instalación de Laravel sirve como base reutilizable para múltiples webs.

## Por qué Laravel en la raíz

La instalación estándar de Laravel se mantiene en la raíz del proyecto para:

- Seguir la estructura convencional del framework
- Facilitar la instalación y actualización con Composer
- Mantener compatibilidad con herramientas de Laravel (Artisan, Tinker, etc.)
- Simplificar el despliegue en servidores estándar

## Por qué `sites/` para la configuración específica

La carpeta `sites/` contiene únicamente la configuración y activos propios de cada web:

```
sites/
├── caudete-se-mueve/
│   ├── site.php          # Configuración del sitio (nombre, dominio, colores...)
│   ├── assets/           # CSS, imágenes, favicon específicas
│   └── content/          # Contenido estático, traducciones
└── miradas-violetas/
    ├── site.php
    ├── assets/
    └── content/
```

Esta separación permite:

- **Añadir una tercera web** creando simplemente `sites/nueva-web/`
- **Mantener el core limpio** sin lógica específica de ningún sitio
- **Personalizar cada web** sin tocar el código reutilizable
- **Facilitar el mantenimiento** al tener todo lo específico en un solo lugar

## Contenido gestionable

El panel de administración (Filament) gestionará:

- Noticias y publicaciones
- Eventos
- Páginas estáticas
- Recursos y documentos descargables
- Galerías de imágenes

## Stack tecnológico

| Capa | Tecnología |
|------|------------|
| Backend | Laravel |
| Panel admin | Filament |
| Frontend | Laravel Blade |
| Base de datos | MariaDB |
| CSS | Tailwind CSS |

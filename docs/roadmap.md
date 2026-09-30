# Roadmap del Proyecto

## Fase 1: Estructura base

- [x] Definir arquitectura del proyecto
- [x] Crear estructura de carpetas inicial
- [x] Documentar decisión arquitectónica

## Fase 2: Instalación (completada)

- [x] Instalar Laravel 13 en la raíz del proyecto
- [x] Crear la base de datos y configurar `.env` con MariaDB/MySQL
- [x] Instalar Filament 5 y crear el panel `/admin`
- [x] Instalar dependencias de Node y compilar assets con Vite
- [x] Configurar locale (`es`) y zona horaria (`Europe/Madrid`)
- [x] Crear el seeder del usuario administrador
- [x] Aplicar las migraciones iniciales

## Fase 3: Core multi-sitio

- [ ] Crear `config/sites.php` con auto-descubrimiento de `sites/`
- [ ] Añadir `site.php` a cada sitio con su configuración
- [ ] Implementar `Site` y `SiteManager` (resolución por dominio, con override `SITE`)
- [ ] Crear el middleware `IdentifySite` y registrarlo
- [ ] Añadir `SiteScope` global para aislar el contenido por sitio
- [ ] Comandos `sites:list` y `sites:check`
- [ ] Tests de detección de sitio

## Fase 4: Contenido y panel de administración

- [ ] Migraciones de `news`, `news_categories` y `pages` con `site_id`
- [ ] Modelos con el trait de pertenencia a sitio
- [ ] Resources de Filament con selección y filtro de sitio
- [ ] Switcher de sitio en el panel
- [ ] Subida de imágenes con `FileUpload`

## Fase 5: Frontend

- [ ] Crear layout base con Blade y tema por sitio vía `@theme inline`
- [ ] Plantillas de portada, noticias y páginas
- [ ] Navegación generada desde las páginas marcadas en menú
- [ ] Responsive design
- [ ] SEO básico (metadatos, Open Graph, sitemap)

## Fase 6: Primer sitio (Caudete Se Mueve)

- [ ] Configurar el sitio en `sites/caudete-se-mueve/`
- [ ] Personalizar la identidad visual (logo, favicon, paleta)
- [ ] Crear páginas iniciales (Quiénes somos, Contacto, Aviso legal)
- [ ] Crear noticias de ejemplo
- [ ] Tests de aislamiento entre sitios

## Fase 7: Segundo sitio (Miradas Violetas)

- [ ] Configurar el sitio en `sites/miradas-violetas/`
- [ ] Personalizar la identidad visual
- [ ] Crear contenido inicial

## Fuera del MVP

Estos módulos quedan aplazados hasta que haya un sitio funcionando de punta a punta:

- [ ] Eventos
- [ ] Recursos y documentos descargables
- [ ] Galerías de imágenes
- [ ] Migración de contenido existente

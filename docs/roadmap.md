# Roadmap del Proyecto

El proyecto se organize por fases, pero el orden real de trabajo fue otro: el
núcleo multi-sitio, el contenido y los paneles se hicieron几乎 a la vez porque
el aislamiento por sitio solo se puede demostrar con las dos cosas puestas.

## Fase 1: Estructura base (completada)

- [x] Definir arquitectura del proyecto
- [x] Crear estructura de carpetas inicial
- [x] Documentar decisión arquitectónica

## Fase 2: Instalación (completada)

- [x] Instalar Laravel en la raíz del proyecto
- [x] Crear la base de datos y configurar `.env` con MariaDB/MySQL
- [x] Instalar Filament y compilar assets con Vite
- [x] Configurar locale (`es`) y zona horaria (`Europe/Madrid`)
- [x] Crear el seeder del usuario administrador
- [x] Aplicar las migraciones iniciales

## Fase 3: Core multi-sitio (completada)

- [x] `config/sites.php` con descubrimiento automático de `sites/`
- [x] `site.php` en cada sitio con su configuración
- [x] `Site` y `SiteManager` con resolución por dominio y override `SITE`
- [x] Middleware `IdentifySite` registrado
- [x] `SiteScope` global para aislar el contenido por sitio
- [x] Comandos `sites:list`, `sites:check` y `sites:link`
- [x] Tests de detección de sitio

## Fase 4: Contenido y panel de administración (completada)

- [x] Migraciones de `posts`, `categories` y `pages` con `site_id`
- [x] Modelos con el trait de pertenencia a sitio y slugs por sitio
- [x] Resources de Filament filtrados por sitio
- [x] Subida de imágenes con `FileUpload`
- [x] CRUD completo (listar, crear, editar, eliminar)

### Paneles y cuentas

- [x] Un panel por sitio en `/{slug}/admin`, registrado desde `sites/`
- [x] Cuentas separadas por asociación, con login acotado al sitio
- [x] Sin selector de sitio: se sustituyó por un panel por asociación
- [x] Cuenta de administración de la plataforma, con acceso a todos los paneles

## Fase 5: Frontend (completada)

- [x] Layout base con Blade y tema por sitio vía `@theme inline`
- [x] Plantillas de portada, noticias, categorías y páginas
- [x] Navegación generada desde las páginas marcadas en menú
- [x] Responsive design
- [x] SEO básico: metadatos, canonical, Open Graph, Twitter Cards
- [x] `sitemap.xml` y `robots.txt` por dominio

## Fase 6: Primer sitio (Caudete Se Mueve) (completada)

- [x] Configuración en `sites/caudete-se-mueve/`
- [x] Identidad visual (logo, favicon, paleta)
- [x] Páginas iniciales (Quiénes somos, Junta directiva, Aviso legal, Privacidad)
- [x] Noticias de ejemplo
- [x] Tests de aislamiento entre sitios

## Fase 7: Segundo sitio (Miradas Violetas) (completada)

- [x] Configuración en `sites/miradas-violetas/`
- [x] Identidad visual
- [x] Contenido inicial

## Pendiente

Antes de publicar:

- [ ] Cambiar `SUPERADMIN_PASSWORD` y `ADMIN_PASSWORD`, y los dominios `.es`
- [ ] Revisar los datos de contacto y redes, hoy son marcadores `PENDIENTE`
- [ ] Descomentar `HTTPS=true` en `Herd.php`
- [ ] Email real para avisos de publicación

## Fuera del MVP

- [ ] Eventos
- [ ] Recursos y documentos descargables
- [ ] Galerías de imágenes
- [ ] Migración de contenido existente

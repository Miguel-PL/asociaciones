# Imágenes de «Qué hacemos»

Esta carpeta contiene fotografías reales de Caudete Se Mueve utilizadas en la página «Qué hacemos»:

- `caudete-recogida-estufas-dana.webp`: imagen introductoria.
- `caudete-carga-ayuda-humanitaria.jpg`: área 01, ayuda humanitaria.
- `caudete-grupo-con-donacion.jpg`: área 02, acción social local.

Las áreas 03 a 06 muestran placeholders ilustrados porque aún no hay fotografías adecuadas. Para sustituirlos, copia una fotografía real en `assets/que-hacemos/` y reemplaza el placeholder de esa área en `resources/views/public/pages/show.blade.php` por una etiqueta `<img>` que use `$site->asset('que-hacemos/nombre-del-archivo.ext')`.

Fotografías pendientes recomendadas y rutas que se pueden usar:

- Área 03: `assets/que-hacemos/bicicletas-taller-solidario.webp` — taller o entrega de bicicletas solidarias.
- Área 04: `assets/que-hacemos/jornada-educativa.webp` — jornada o taller educativo en un centro de Caudete.
- Área 05: `assets/que-hacemos/movilizacion-por-la-paz.webp` — concentración o movilización ciudadana por la paz.
- Área 06: `assets/que-hacemos/actividad-cultural-comunitaria.webp` — actividad cultural o evento solidario comunitario.

El archivo `provisionales/logo_antiguo.svg` es un logotipo antiguo, no una fotografía; no se utiliza en esta página.

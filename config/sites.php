<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Directorio de sitios
    |--------------------------------------------------------------------------
    |
    | Carpeta que contiene un subdirectorio por sitio, cada uno con su propio
    | site.php. Añadir una web nueva equivale a crear esa carpeta.
    |
    */

    'path' => base_path('sites'),

    /*
    |--------------------------------------------------------------------------
    | Sitio por defecto
    |--------------------------------------------------------------------------
    |
    | Sitio que se sirve cuando la peticion no encaja con ningun dominio
    | declarado. Si se deja vacio se usa el primero disponible.
    |
    */

    'default' => env('SITE_DEFAULT', 'caudete-se-mueve'),

    /*
    |--------------------------------------------------------------------------
    | Sitio forzado
    |--------------------------------------------------------------------------
    |
    | Cuando se define, tiene prioridad sobre el dominio de la peticion. Es el
    | mecanismo de desarrollo: permite trabajar en un sitio sin depender de
    | los dominios .test. En produccion debe quedar vacio.
    |
    */

    'override' => env('SITE'),

];

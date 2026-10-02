<?php

return [

    'name' => 'Caudete Se Mueve',

    'tagline' => 'La asociación vecinal que mueve Caudete',

    'admin_email' => 'caudete@asociaciones.test',

    'description' => 'Caudete Se Mueve es la asociación vecinal de Caudete. '.
        'Trabajamos por el barrio con actividades, propuestas y participación.',

    /*
    | Dominios que sirven este sitio. El primero es el principal.
    | En desarrollo se usan los dominios .test de Herd.
    | PENDIENTE: caudetese-mueve.es es provisional hasta confirmar el real.
    */
    'domains' => [
        'caudete-se-mueve.test',
        'caudetese-mueve.es',
    ],

    'colors' => [
        'primary' => '#1d4ed8',
        'secondary' => '#0f766e',
        'accent' => '#f59e0b',
    ],

    'logo' => 'assets/logo.svg',

    'favicon' => 'assets/favicon.svg',

    // Tarjeta de reparto en redes, 1200x630. Las redes no aceptan SVG, asi que
    // no puede ser el logo. Ver README.md para como se genera.
    'social_image' => 'assets/social.png',

    // PENDIENTE: datos de contacto de relleno hasta confirmar los reales.
    'contact' => [
        'email' => 'hola@caudetese-mueve.es',
        'phone' => '+34 600 000 000',
        'address' => 'Plaza Mayor, Caudete',
    ],

    // PENDIENTE: enlaces a redes sociales reales.
    'social' => [
        'facebook' => 'https://facebook.com/',
        'instagram' => 'https://instagram.com/',
        'twitter' => 'https://twitter.com/',
    ],

    /*
    | Páginas que aparecen en el menú de la cabecera. Son slugs de páginas ya
    | publicadas: el enlace solo se muestra si la página existe.
    */
    'nav' => [
        'quienes-somos',
        'junta-directiva',
    ],

    'footer' => [
        'legal' => 'aviso-legal',
        'privacy' => 'privacidad',
    ],

];

{{--
    Layout común a todas las webs.

    Todo lo que cambia entre asociaciones (nombre, colores, contacto, enlaces,
    logo) sale de la variable $site, que IdentifySite comparte con las vistas.
    Las vistas son las mismas para todas las webs: lo único especifico de cada
    sitio son sus site.php y, si los necesita, sus assets.
--}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', $site->name)</title>
    <meta name="description" content="@yield('meta_description', $site->description)">

    <link rel="canonical" href="{{ $canonical ?? url()->current() }}">

    {{--
        Open Graph y Twitter Cards.

        Las vistas sobrescriben solo lo que aporta: una noticia cambia el
        título, la entradilla y la imagen; una página, el título y su resumen.
        Lo que no se declare cae al nombre y la descripción de la asociación,
        así que ninguna vista tiene que acordarse de rellenarlo todo.

        Los títulos y las descripciones se leen de las mismas secciones que
        ya rellenan el title y el meta description, para que ambos no puedan
        acabar diciendo cosas distintas.

        La imagen es la portada si la vista la aporta y, si no, la tarjeta del
        sitio. Nunca el logo: las redes no saben dibujar un SVG y una tarjeta
        sin imagen se ve peor que una con la imagen de la asociación.
    --}}
    @php
        $ogTitle = trim($__env->yieldContent('title', $site->name));
        $ogDescription = trim((string) preg_replace('/\s+/u', ' ', $__env->yieldContent('meta_description', $site->description)));
        $ogImage = $__env->yieldContent('og_image') ?: $site->socialImage();
    @endphp
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="{{ $site->name }}">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="og:url" content="{{ $canonical ?? url()->current() }}">
    <meta property="og:locale" content="es_ES">
    @if ($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
    @endif

    @if ($ogPublishedTime = $__env->yieldContent('og_published_time'))
        <meta property="article:published_time" content="{{ trim($ogPublishedTime) }}">
    @endif

    <meta name="twitter:card" content="{{ $ogImage ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $ogTitle }}">
    <meta name="twitter:description" content="{{ $ogDescription }}">
    @if ($ogImage)
        <meta name="twitter:image" content="{{ $ogImage }}">
    @endif

    @if ($site->asset($site->favicon))
        <link rel="icon" href="{{ $site->asset($site->favicon) }}" type="image/svg+xml">
    @endif

    {{-- Colores de esta web concreta, antes de cargar el CSS que los usa. --}}
    <style>
        :root {
            @foreach ($site->colorVariables() as $name => $value)
                --site-{{ $name }}: {{ $value }};
            @endforeach
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @if ($site->asset('site.css'))
        {{-- Ajustes propios de esta asociación, por encima de la base común. --}}
        <link rel="stylesheet" href="{{ $site->asset('site.css') }}">
    @endif
</head>
<body class="flex min-h-screen flex-col site-{{ $site->slug }}">
<a href="#contenido" class="sr-only focus:not-sr-only">Saltar al contenido</a>

<header class="site-header">
    <div class="site-header-inner">
        <a href="{{ route('home') }}" class="site-brand" aria-label="{{ $site->name }} — Inicio">
            @if ($site->asset($site->logo))
                <img src="{{ $site->asset($site->logo) }}" alt="" class="site-brand-logo">
            @endif
            <span>
                <span class="site-brand-name">{{ $site->name }}</span>
                @if ($site->tagline)
                    <span class="site-brand-tagline">{{ $site->tagline }}</span>
                @endif
            </span>
        </a>

        <button class="site-menu-toggle" type="button" aria-expanded="false" aria-controls="site-navigation" aria-label="Abrir menú">
            <span></span><span></span>
        </button>
        <nav id="site-navigation" aria-label="Principal" class="site-navigation">
            <a href="{{ route('home') }}"
               @class(['is-active' => request()->routeIs('home')])>Inicio</a>
            <a href="{{ route('posts.index') }}"
               @class(['is-active' => request()->routeIs('posts.*')])>Noticias</a>
            @foreach ($navPages as $page)
                <a href="{{ route('pages.show', $page) }}"
                   @class(['is-active' => request()->routeIs('pages.show', $page)])>{{ $page->title }}</a>
            @endforeach
        </nav>
    </div>
</header>

<main id="contenido" class="site-main flex-1">
    <div class="site-main-inner">
        @yield('content')
    </div>
</main>

<footer class="site-footer">
    <div class="site-footer-inner">
        <div class="site-footer-intro">
            @if ($site->asset($site->logo))
                <img src="{{ $site->asset($site->logo) }}" alt="" class="site-footer-logo">
            @endif
            <p class="site-footer-name">{{ $site->name }}</p>
            @if ($site->tagline)<p class="site-footer-tagline">{{ $site->tagline }}</p>@endif
        </div>
        <div class="site-footer-contact">
            <p class="site-footer-label">{{ $footerContact['title'] }}</p>
            @if ($footerContact['address'] ?? null)
                <p>{{ $footerContact['address'] }}</p>
            @endif
            @if ($footerContact['email'] ?? null)
                <p><a class="link" href="mailto:{{ $footerContact['email'] }}">{{ $footerContact['email'] }}</a></p>
            @endif
            @if ($footerContact['phone'] ?? null)
                <p><a href="tel:{{ preg_replace('/[^0-9+]/', '', $footerContact['phone']) }}">{{ $footerContact['phone'] }}</a></p>
            @endif
        </div>

        <div class="site-footer-links">
            <p class="site-footer-label">Explora</p>
            <div class="site-footer-link-list">
                <a href="{{ route('posts.index') }}">Noticias</a>
                @foreach ($navPages as $page)
                    <a href="{{ route('pages.show', $page) }}">{{ $page->title }}</a>
                @endforeach
            </div>

            @if ($socialLinks)
                <div class="site-footer-link-list site-social-links">
                    @foreach ($socialLinks as $network => $url)
                        <a href="{{ $url }}" rel="noopener">{{ ucfirst($network) }}</a>
                    @endforeach
                </div>
            @endif

            @if ($legalLinks)
                <div class="site-footer-link-list site-legal-links">
                    @foreach ($legalLinks as $label => $url)
                        <a href="{{ $url }}">{{ $label }}</a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    <div class="site-footer-bottom"><span>Comunidad, participación y dignidad humana.</span><span>Caudete · Albacete</span></div>
</footer>
</body>
</html>

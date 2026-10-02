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

    @if ($site->asset('favicon.svg'))
        <link rel="icon" href="{{ $site->asset('favicon.svg') }}" type="image/svg+xml">
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
<body class="flex min-h-screen flex-col">
<a href="#contenido" class="sr-only focus:not-sr-only">Saltar al contenido</a>

<header class="border-b border-stone-200 bg-white">
    <div class="container-site flex flex-wrap items-center justify-between gap-4 py-4">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            @if ($site->asset('logo.svg'))
                <img src="{{ $site->asset('logo.svg') }}" alt="" class="h-10 w-auto">
            @endif
            <span>
                <span class="block text-lg leading-tight font-semibold text-stone-900">{{ $site->name }}</span>
                @if ($site->tagline)
                    <span class="block text-sm text-stone-500">{{ $site->tagline }}</span>
                @endif
            </span>
        </a>

        <nav aria-label="Principal" class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm font-medium">
            <a href="{{ route('home') }}"
               @class(['text-stone-900', 'link' => ! request()->routeIs('home')])>Inicio</a>
            <a href="{{ route('posts.index') }}"
               @class(['text-stone-900', 'link' => ! request()->routeIs('posts.*')])>Noticias</a>
            @foreach ($navPages as $page)
                <a href="{{ route('pages.show', $page) }}"
                   @class(['text-stone-900', 'link' => ! request()->routeIs('pages.show', $page)])>{{ $page->title }}</a>
            @endforeach
        </nav>
    </div>
</header>

<main id="contenido" class="flex-1 py-10">
    <div class="container-site">
        @yield('content')
    </div>
</main>

<footer class="border-t border-stone-200 bg-white py-8 text-sm text-stone-600">
    <div class="container-site flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="space-y-1">
            <p class="font-medium text-stone-900">{{ $site->name }}</p>
            @if ($site->contact['address'] ?? null)
                <p>{{ $site->contact['address'] }}</p>
            @endif
            @if ($site->contact['email'] ?? null)
                <p><a class="link" href="mailto:{{ $site->contact['email'] }}">{{ $site->contact['email'] }}</a></p>
            @endif
            @if ($site->contact['phone'] ?? null)
                <p>{{ $site->contact['phone'] }}</p>
            @endif
        </div>

        <div class="space-y-1">
            <div class="flex flex-wrap gap-x-4 gap-y-1">
                <a class="link" href="{{ route('posts.index') }}">Noticias</a>
                @foreach ($navPages as $page)
                    <a class="link" href="{{ route('pages.show', $page) }}">{{ $page->title }}</a>
                @endforeach
            </div>

            @if ($socialLinks)
                <div class="flex flex-wrap gap-x-4 gap-y-1">
                    @foreach ($socialLinks as $network => $url)
                        <a class="link" href="{{ $url }}" rel="noopener">{{ ucfirst($network) }}</a>
                    @endforeach
                </div>
            @endif

            @if ($legalLinks)
                <div class="flex flex-wrap gap-x-4 gap-y-1">
                    @foreach ($legalLinks as $label => $url)
                        <a class="link" href="{{ $url }}">{{ $label }}</a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</footer>
</body>
</html>

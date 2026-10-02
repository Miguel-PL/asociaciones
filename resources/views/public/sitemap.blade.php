{{--
    Mapa del sitio en XML.

    Las URLs ya salen absolutas y con el dominio del sitio que se está
    sirviendo, y el contenido está filtrado por sitio: este archivo nunca
    describe dos webs a la vez.
--}}
<?php echo '<?xml version="1.0" encoding="UTF-8"?>'."\n"; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($entradas as $entrada)
    <url>
        <loc>{{ $entrada['url'] }}</loc>
@if (! empty($entrada['lastmod']))
        <lastmod>{{ $entrada['lastmod'] }}</lastmod>
@endif
    </url>
@endforeach
</urlset>

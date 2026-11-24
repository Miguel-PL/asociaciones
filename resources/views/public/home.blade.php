@extends('layouts.site')

@section('title', $site->name)
@section('meta_description', $site->description)

@section('content')
    <section class="home-hero" aria-labelledby="home-title">
        <div class="home-hero-copy">
            <p class="eyebrow"><span></span> Plataforma ciudadana · Caudete</p>
            <h1 id="home-title">Una comunidad<br>que <em>se mueve</em>.</h1>
            <p class="home-hero-description">Caudete Se Mueve es un colectivo vecinal que transforma la solidaridad de nuestro municipio en acción. Trabajamos por los Derechos Humanos, la justicia social y el apoyo a las personas en situación de vulnerabilidad, aquí y más allá de nuestras fronteras.</p>
            <div class="home-hero-actions">
                <a class="button button-dark" href="{{ route('pages.show', ['page' => 'que-hacemos']) }}">Qué hacemos</a>
                <a class="button home-button-secondary" href="{{ route('pages.show', ['page' => 'quienes-somos']) }}">Quiénes somos</a>
            </div>
        </div>
        <figure class="home-hero-art">
            <div class="home-art-orbit">
                <div class="home-art-image-frame">
                    <img src="{{ $site->asset($site->logo) }}" alt="Logotipo de Caudete Se Mueve" fetchpriority="high">
                </div>
            </div>
        </figure>
        <a class="hero-scroll" href="#actualidad"><span></span> Sigue explorando</a>
    </section>

    <section class="news-section home-news-section" id="actualidad" aria-labelledby="news-title">
        <div class="section-heading">
            <div><p class="eyebrow">Lo que pasa, lo que nos mueve</p><h2 id="news-title">Actualidad <span>de la asociación.</span></h2></div>
            <a class="text-link" href="{{ route('posts.index') }}">Todas las noticias</a>
        </div>

        @if ($posts->isEmpty())
            <div class="empty-state"><p class="eyebrow">Pronto, más noticias</p><p>Todavía no hay noticias publicadas. Vuelve pronto para conocer la actualidad de Caudete Se Mueve.</p></div>
        @else
            <div class="news-grid home-news-grid">
                @foreach ($posts as $post)
                    @include('public.partials.post-card', ['post' => $post])
                @endforeach
            </div>
        @endif
    </section>

    <section class="participation-band home-participation" aria-labelledby="home-participation-title">
        <div class="participation-symbol" aria-hidden="true">✳</div>
        <div><p class="eyebrow">Las cosas cambian cuando nos implicamos</p><h2 id="home-participation-title">La solidaridad se construye entre todos.</h2></div>
        <div class="participation-action"><p>Cada gesto cuenta. Súmate a nuestra comunidad y ayúdanos a transformar la solidaridad en acción.</p><a class="button button-light" href="{{ route('pages.show', ['page' => 'colabora']) }}">Quiero colaborar</a></div>
    </section>
@endsection

@extends('layouts.site')

@section('title', $site->name)
@section('meta_description', $site->description)

@section('content')
    <section class="home-hero" aria-labelledby="home-title">
        <div class="home-hero-copy">
            <p class="eyebrow"><span></span> Plataforma ciudadana · Caudete</p>
            <h1 id="home-title">Una comunidad<br>que <em>se mueve</em>.</h1>
            <p class="home-hero-description">{{ $site->description }}</p>
            <div class="home-hero-actions">
                <a class="button button-dark" href="{{ route('posts.index') }}">Descubre lo que hacemos</a>
                @foreach ($navPages->take(1) as $page)
                    <a class="text-link" href="{{ route('pages.show', $page) }}">{{ $page->title }}</a>
                @endforeach
            </div>
            <div class="home-hero-note"><span class="note-mark" aria-hidden="true">10</span><span><strong>Diez años moviéndonos</strong><br>por la dignidad humana</span></div>
        </div>
        <div class="home-hero-art" role="img" aria-label="Logotipo del décimo aniversario de Caudete Se Mueve">
            <div class="hero-art-ring"></div>
            <div class="hero-art-disc"><img src="{{ $site->asset($site->logo) }}" alt=""></div>
        </div>
        <a class="hero-scroll" href="#actualidad"><span></span> Sigue explorando</a>
    </section>

    <section class="news-section" id="actualidad" aria-labelledby="news-title">
        <div class="section-heading">
            <div><p class="eyebrow">Lo que pasa, lo que nos mueve</p><h2 id="news-title">Actualidad <span>de la asociación.</span></h2></div>
            @if ($posts->isNotEmpty())<a class="text-link" href="{{ route('posts.index') }}">Todas las noticias</a>@endif
        </div>

        @if ($posts->isEmpty())
            <div class="empty-state"><p class="eyebrow">Pronto, más noticias</p><p>Todavía no hay noticias publicadas. Vuelve pronto para conocer la actualidad de Caudete Se Mueve.</p></div>
        @else
            <div class="news-grid">
                @foreach ($posts as $post)
                    @include('public.partials.post-card', ['post' => $post])
                @endforeach
            </div>
        @endif
    </section>

    <section class="participation-band">
        <div class="participation-symbol" aria-hidden="true">✳</div>
        <div><p class="eyebrow">Las cosas cambian cuando nos implicamos</p><h2>Un mundo mejor lo <br>hacemos entre<br><em>todas y todos.</em></h2></div>
        <div class="participation-action"><p>Ideas, encuentros y un mundo más justo empiezan por participar.</p>@foreach ($navPages->take(1) as $page)<a class="button button-light" href="{{ route('pages.show', $page) }}">{{ $page->title }}</a>@endforeach</div>
    </section>
@endsection

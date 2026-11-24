@extends('layouts.site')

@section('title', $post->title.' · '.$site->name)
@section('meta_description', $post->excerpt ?? $site->description)

@section('og_type', 'article')
@section('og_image', $post->cover_image_url)
@section('og_published_time', $post->published_at?->toAtomString())

@section('content')
    @if ($isPreview ?? false)
        <aside class="mx-auto mb-6 flex flex-wrap items-center justify-between gap-3 rounded-md border border-stone-200 bg-white px-4 py-3 text-sm text-stone-600" role="status">
            <span>
                <strong class="font-semibold text-stone-800">Vista previa</strong>
                @if (! $post->is_published || $post->published_at?->isFuture())
                    — esta noticia todavía no está publicada.
                @else
                    — se muestran los cambios guardados de esta noticia publicada.
                @endif
            </span>
            <a href="{{ $previewEditUrl }}" class="link">Volver a editar</a>
        </aside>
    @endif

    <article class="editorial-page article-detail">
        <nav class="article-back" aria-label="Navegación de noticias">
            <a href="{{ route('posts.index') }}" class="link">Noticias</a>
        </nav>

        <header class="page-heading article-heading">
            <p class="eyebrow">Voces de Caudete</p>
            <h1>{{ $post->title }}</h1>

            <p class="article-meta">
            @if ($post->category)
                <a href="{{ route('categories.show', $post->category) }}" class="link">
                    {{ $post->category->name }}
                </a>
            @endif

            @if ($post->published_at)
                <time datetime="{{ $post->published_at->toDateString() }}">
                    {{ $post->published_at->translatedFormat('d \d\e F \d\e Y') }}
                </time>
            @endif
            </p>
        </header>

        @if ($post->cover_image_url)
            <figure class="article-cover"><img src="{{ $post->cover_image_url }}" alt=""></figure>
        @endif

        <div class="article-reading-column">
            <div class="prose-body article-body">
                @include('public.partials.rich-content', ['content' => $post->body])
            </div>
        </div>
    </article>

    @if ($more->isNotEmpty())
        <section class="article-more posts-archive" aria-labelledby="article-more-title">
            <header class="article-more-heading">
                <p class="eyebrow">Sigue leyendo</p>
                <h2 id="article-more-title">Más noticias</h2>
            </header>

            <div class="listing-grid posts-grid">
                @foreach ($more as $otra)
                    @include('public.partials.post-card', ['post' => $otra])
                @endforeach
            </div>
        </section>
    @endif
@endsection

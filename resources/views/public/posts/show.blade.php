@extends('layouts.site')

@section('title', $post->title.' · '.$site->name)
@section('meta_description', $post->excerpt ?? $site->description)

@section('og_type', 'article')
@section('og_image', $post->cover_image_url)
@section('og_published_time', $post->published_at?->toAtomString())

@section('content')
    <article class="editorial-page">
        <p class="text-sm">
            <a href="{{ route('posts.index') }}" class="link">Noticias</a>
        </p>

        <header class="page-heading article-heading"><p class="eyebrow">Voces de Caudete</p><h1>
            {{ $post->title }}
        </h1>

        <p class="mt-3 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-stone-500">
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

        <div class="prose-body mt-8 text-stone-700">
            @include('public.partials.rich-content', ['content' => $post->body])
        </div>
    </article>

    @if ($more->isNotEmpty())
        <section class="mx-auto mt-16 max-w-3xl border-t border-stone-200 pt-8">
            <h2 class="text-xl font-bold tracking-tight text-stone-900">Más noticias</h2>

            <div class="mt-6 grid gap-6 sm:grid-cols-3">
                @foreach ($more as $otra)
                    @include('public.partials.post-card', ['post' => $otra])
                @endforeach
            </div>
        </section>
    @endif
@endsection

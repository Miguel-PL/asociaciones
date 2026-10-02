@extends('layouts.site')

@section('title', $post->title.' · '.$site->name)
@section('meta_description', $post->excerpt ?? $site->description)

@section('content')
    <article class="mx-auto max-w-3xl">
        <p class="text-sm">
            <a href="{{ route('posts.index') }}" class="link">Noticias</a>
        </p>

        <h1 class="mt-3 text-3xl font-bold tracking-tight text-stone-900 sm:text-4xl">
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

        <div class="prose-body mt-8 text-stone-700">
            @foreach (preg_split('/\R{2,}/', trim($post->body ?? '')) as $paragraph)
                @if (trim($paragraph) !== '')
                    <p>{{ trim($paragraph) }}</p>
                @endif
            @endforeach
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

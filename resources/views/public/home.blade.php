@extends('layouts.site')

@section('title', $site->name)
@section('meta_description', $site->description)

@section('content')
    <section class="rounded-2xl bg-white p-8 shadow-sm sm:p-10">
        <h1 class="text-3xl font-bold tracking-tight text-stone-900 sm:text-4xl">{{ $site->name }}</h1>

        @if ($site->tagline)
            <p class="mt-2 text-lg text-stone-600">{{ $site->tagline }}</p>
        @endif

        @if ($site->description)
            <p class="mt-4 max-w-prose text-stone-700">{{ $site->description }}</p>
        @endif

        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('posts.index') }}"
               class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white transition hover:bg-primary-dark">
                Ver noticias
            </a>

            @foreach ($navPages as $page)
                <a href="{{ route('pages.show', $page) }}"
                   class="rounded-lg border border-stone-300 px-4 py-2 text-sm font-semibold text-stone-700 transition hover:border-stone-400">
                    {{ $page->title }}
                </a>
            @endforeach
        </div>
    </section>

    <section class="mt-12">
        <div class="flex items-baseline justify-between gap-4">
            <h2 class="text-2xl font-bold tracking-tight text-stone-900">Últimas noticias</h2>

            @if ($posts->isNotEmpty())
                <a href="{{ route('posts.index') }}" class="link text-sm">Ver todas</a>
            @endif
        </div>

        @if ($posts->isEmpty())
            <p class="mt-6 rounded-xl border border-dashed border-stone-300 p-8 text-center text-stone-500">
                Todavía no hay noticias publicadas.
            </p>
        @else
            <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($posts as $post)
                    @include('public.partials.post-card', ['post' => $post])
                @endforeach
            </div>
        @endif
    </section>
@endsection

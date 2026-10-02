@extends('layouts.site')

@section('title', $page->title.' · '.$site->name)
@section('meta_description', $page->meta_description ?? $site->description)

@section('content')
    <article class="mx-auto max-w-3xl">
        <h1 class="text-3xl font-bold tracking-tight text-stone-900 sm:text-4xl">
            {{ $page->title }}
        </h1>

        <div class="prose-body mt-8 text-stone-700">
            @foreach (preg_split('/\R{2,}/', trim($page->body ?? '')) as $paragraph)
                @if (trim($paragraph) !== '')
                    <p>{{ trim($paragraph) }}</p>
                @endif
            @endforeach
        </div>
    </article>
@endsection

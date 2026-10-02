@extends('layouts.site')

@section('title', $category->name.' · '.$site->name)
@section('meta_description', $category->description ?: 'Noticias de '.$category->name)

@section('content')
    <h1 class="text-3xl font-bold tracking-tight text-stone-900">{{ $category->name }}</h1>

    @if ($category->description)
        <p class="mt-2 max-w-prose text-stone-600">{{ $category->description }}</p>
    @endif

    @if ($posts->isEmpty())
        <p class="mt-6 rounded-xl border border-dashed border-stone-300 p-8 text-center text-stone-500">
            No hay noticias en esta categoría.
        </p>
    @else
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($posts as $post)
                @include('public.partials.post-card', ['post' => $post])
            @endforeach
        </div>

        <div class="mt-10">
            {{ $posts->links() }}
        </div>
    @endif
@endsection

@extends('layouts.site')

@section('title', $category->name.' · '.$site->name)
@section('meta_description', $category->description ?: 'Noticias de '.$category->name)

@section('content')
    <header class="page-heading"><p class="eyebrow">Noticias por tema</p><h1>{{ $category->name }}<em>.</em></h1>

    @if ($category->description)
        <p>{{ $category->description }}</p>
    @endif
    </header>

    @if ($posts->isEmpty())
        <p class="empty-state mt-6">
            No hay noticias en esta categoría.
        </p>
    @else
        <div class="listing-grid mt-8">
            @foreach ($posts as $post)
                @include('public.partials.post-card', ['post' => $post])
            @endforeach
        </div>

        <div class="mt-10">
            {{ $posts->links() }}
        </div>
    @endif
@endsection

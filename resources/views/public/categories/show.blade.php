@extends('layouts.site')

@section('title', $category->name.' · '.$site->name)
@section('meta_description', $category->description ?: 'Noticias de '.$category->name)

@section('content')
    <section class="posts-archive category-archive" aria-labelledby="category-title">
        <header class="page-heading posts-archive-heading category-archive-heading">
            <p class="eyebrow">Noticias por tema</p>
            <h1 id="category-title">{{ $category->name }}<em>.</em></h1>

            @if ($category->description)
                <p>{{ $category->description }}</p>
            @endif
        </header>

        @if ($posts->isEmpty())
            <p class="empty-state posts-empty-state">
                No hay noticias en esta categoría.
            </p>
        @else
            <div class="listing-grid posts-grid category-posts-grid">
                @foreach ($posts as $post)
                    @include('public.partials.post-card', ['post' => $post])
                @endforeach
            </div>

            <div class="posts-pagination-wrap">
                {{ $posts->links('public.posts.pagination') }}
            </div>
        @endif
    </section>
@endsection

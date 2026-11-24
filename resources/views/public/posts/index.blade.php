@extends('layouts.site')

@section('title', 'Noticias · '.$site->name)
@section('meta_description', 'Noticias publicadas por '.$site->name)

@section('content')
    <section class="posts-archive" aria-labelledby="posts-archive-title">
        <header class="page-heading posts-archive-heading">
            <p class="eyebrow">Caudete en movimiento</p>
            <h1 id="posts-archive-title">Noticias y actualidad.</h1>
            <p>Historias, propuestas y actividades de nuestra asociación.</p>
        </header>

        <form class="posts-filters" method="GET" action="{{ route('posts.index') }}" role="search" aria-label="Buscar noticias">
            <div class="posts-filters-field">
                <label for="posts-search">Buscar por título</label>
                <input id="posts-search" type="search" name="q" value="{{ $search }}" placeholder="Palabras del título…">
            </div>
            <div class="posts-filters-field">
                <label for="posts-category">Categoría</label>
                <select id="posts-category" name="category">
                    <option value="" @selected($selectedCategory === '')>Todas las categorías</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected($selectedCategory === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="posts-filters-actions">
                <button class="posts-filters-submit" type="submit">Buscar</button>
                <a class="posts-filters-clear" href="{{ route('posts.index') }}">Limpiar filtros</a>
            </div>
        </form>

        @if ($posts->isEmpty())
            <div class="empty-state posts-empty-state">
                @if ($search !== '' || filled($selectedCategory))
                    <p>No se han encontrado noticias con esos filtros.</p>
                    <p><a class="text-link" href="{{ route('posts.index') }}">Limpiar filtros y ver todas las noticias</a></p>
                @else
                    <p>Todavía no hay noticias publicadas.</p>
                @endif
            </div>
        @else
            <div class="listing-grid posts-grid">
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

@extends('layouts.site')

@section('title', 'Noticias · '.$site->name)
@section('meta_description', 'Noticias publicadas por '.$site->name)

@section('content')
    <header class="page-heading"><p class="eyebrow">Caudete en movimiento</p><h1>Noticias y actualidad.</h1><p>Historias, propuestas y actividades de nuestra asociación.</p></header>

    @if ($posts->isEmpty())
        <p class="empty-state mt-6">
            Todavía no hay noticias publicadas.
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

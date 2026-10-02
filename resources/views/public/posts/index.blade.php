@extends('layouts.site')

@section('title', 'Noticias · '.$site->name)
@section('meta_description', 'Noticias publicadas por '.$site->name)

@section('content')
    <h1 class="text-3xl font-bold tracking-tight text-stone-900">Noticias</h1>

    @if ($posts->isEmpty())
        <p class="mt-6 rounded-xl border border-dashed border-stone-300 p-8 text-center text-stone-500">
            Todavía no hay noticias publicadas.
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

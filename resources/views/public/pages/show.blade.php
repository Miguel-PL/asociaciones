@extends('layouts.site')

@section('title', $page->title.' · '.$site->name)
@section('meta_description', $page->meta_description ?? $site->description)

@section('content')
    <article class="editorial-page">
        <header class="page-heading">
        <p class="eyebrow">Caudete Se Mueve</p><h1>
            {{ $page->title }}
        </h1>
        </header>

        <div class="prose-body mt-8 text-stone-700">
            @include('public.partials.rich-content', ['content' => $page->body])
        </div>
    </article>
@endsection

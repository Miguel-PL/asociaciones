{{--
    Tarjeta de noticia, reutilizada en portada, listado, categoría y detalle.

    Espera una noticia ya cargada; $category solo se usa si viene informado.
--}}
<article class="group relative flex flex-col gap-2 rounded-xl bg-white p-5 shadow-sm transition hover:shadow-md">
    @if ($post->category)
        <p class="text-xs font-semibold tracking-wide text-primary uppercase">
            <a href="{{ route('categories.show', $post->category) }}"
               class="after:absolute after:inset-0">{{ $post->category->name }}</a>
        </p>
    @endif

    <h3 class="text-lg leading-snug font-semibold text-stone-900">
        <a href="{{ route('posts.show', $post) }}" class="after:absolute after:inset-0">
            {{ $post->title }}
        </a>
    </h3>

    @if ($post->excerpt)
        <p class="text-stone-600">{{ $post->excerpt }}</p>
    @endif

    @if ($post->published_at)
        <p class="mt-auto pt-2 text-sm text-stone-500">
            <time datetime="{{ $post->published_at->toDateString() }}">
                {{ $post->published_at->translatedFormat('d \d\e F \d\e Y') }}
            </time>
        </p>
    @endif
</article>

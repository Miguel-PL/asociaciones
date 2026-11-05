{{-- Tarjeta editorial compartida por portada, listado, categoría y noticias relacionadas. --}}
<article class="post-card">
    <a class="post-card-link" href="{{ route('posts.show', $post) }}" aria-label="Leer: {{ $post->title }}" title="{{ $post->title }}{{ $post->excerpt ? ' — '.$post->excerpt : '' }}">
        <div class="post-card-image">
            @if ($post->cover_image_url)
                <img src="{{ $post->cover_image_url }}" alt="" loading="lazy">
            @else
                <div class="post-image-placeholder" aria-hidden="true"><span>CSM</span><i></i></div>
            @endif
        </div>
        <div class="post-card-copy">
            <div class="post-card-meta">
                @if ($post->category)<span class="post-category">{{ $post->category->name }}</span>@endif
                @if ($post->published_at)<time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->translatedFormat('d M Y') }}</time>@endif
            </div>
            <h3>{{ $post->title }}</h3>
            @if ($post->excerpt)<p class="post-excerpt">{{ $post->excerpt }}</p>@endif
            <span class="post-read">Leer noticia</span>
        </div>
    </a>
</article>

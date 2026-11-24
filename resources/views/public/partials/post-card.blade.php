{{-- Tarjeta editorial compartida por portada, listado, categoría y noticias relacionadas. --}}
<article class="post-card">
    <a class="post-card-link" href="{{ route('posts.show', $post) }}" aria-label="Leer: {{ $post->title }}" title="{{ $post->title }}{{ $post->excerpt ? ' — '.$post->excerpt : '' }}">
        <div class="post-card-image">
            @if ($post->cover_image_url)
                <img src="{{ $post->cover_image_url }}" alt="" loading="lazy">
            @else
                <div class="post-image-placeholder post-image-placeholder--neutral" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2" />
                        <circle cx="8.5" cy="8.5" r="1.5" />
                        <path d="m21 15-5-5L5 21" />
                    </svg>
                    <span>Imagen no disponible</span>
                </div>
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

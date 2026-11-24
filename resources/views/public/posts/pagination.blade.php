@if ($paginator->hasPages())
    <nav class="posts-pagination" role="navigation" aria-label="Paginación de noticias">
        <p class="posts-pagination-summary">
            Mostrando
            <strong>{{ $paginator->firstItem() ?? 0 }}–{{ $paginator->lastItem() ?? 0 }}</strong>
            de <strong>{{ $paginator->total() }}</strong> noticias
        </p>

        <div class="posts-pagination-links">
            @if ($paginator->onFirstPage())
                <span class="posts-pagination-step is-disabled" aria-disabled="true">Anterior</span>
            @else
                <a class="posts-pagination-step" href="{{ $paginator->previousPageUrl() }}" rel="prev">Anterior</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="posts-pagination-ellipsis" aria-hidden="true">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="posts-pagination-page is-current" aria-current="page" aria-label="Página {{ $page }}">{{ $page }}</span>
                        @else
                            <a class="posts-pagination-page" href="{{ $url }}" aria-label="Ir a la página {{ $page }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a class="posts-pagination-step" href="{{ $paginator->nextPageUrl() }}" rel="next">Siguiente</a>
            @else
                <span class="posts-pagination-step is-disabled" aria-disabled="true">Siguiente</span>
            @endif
        </div>
    </nav>
@endif

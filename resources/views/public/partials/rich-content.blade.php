{{-- RichEditor stores HTML; older records may contain plain text with blank lines. --}}
@php
    $bodyContent = trim((string) $content);
    $containsHtml = preg_match('/<\/?[a-z][^>]*>/i', $bodyContent) === 1;
@endphp

@if ($containsHtml)
    {!! str($bodyContent)->sanitizeHtml() !!}
@else
    @foreach (preg_split('/\R{2,}/', $bodyContent) as $paragraph)
        @if (trim($paragraph) !== '')
            <p>{{ trim($paragraph) }}</p>
        @endif
    @endforeach
@endif

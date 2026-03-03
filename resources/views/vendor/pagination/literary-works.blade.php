@if ($paginator->hasPages())
    {{-- Previous --}}
    @if ($paginator->onFirstPage())
        <button class="clist-pagination__btn clist-pagination__btn--prev" disabled aria-label="Önceki sayfa">
            <i class="fa-solid fa-chevron-left"></i>
        </button>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="clist-pagination__btn clist-pagination__btn--prev" aria-label="Önceki sayfa">
            <i class="fa-solid fa-chevron-left"></i>
        </a>
    @endif

    {{-- Page Numbers --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="clist-pagination__dots">{{ $element }}</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <button class="clist-pagination__page clist-pagination__page--active" aria-current="page">{{ $page }}</button>
                @else
                    <a href="{{ $url }}" class="clist-pagination__page" aria-label="Sayfa {{ $page }}">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="clist-pagination__btn clist-pagination__btn--next" aria-label="Sonraki sayfa">
            <i class="fa-solid fa-chevron-right"></i>
        </a>
    @else
        <button class="clist-pagination__btn clist-pagination__btn--next" disabled aria-label="Sonraki sayfa">
            <i class="fa-solid fa-chevron-right"></i>
        </button>
    @endif
@endif

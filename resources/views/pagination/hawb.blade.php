@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="pagination">
        @if ($paginator->onFirstPage())
            <span class="active" aria-disabled="true">&laquo; Anterior</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo; Anterior</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span>{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="active" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next">Próxima &raquo;</a>
        @else
            <span aria-disabled="true">Próxima &raquo;</span>
        @endif
    </nav>
@endif

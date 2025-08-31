@if ($paginator->hasPages())
<div class="pagination" data-aos="fade-up" data-aos-easing="linear">
    @if ($paginator->onFirstPage())
        <a class="page-numbers btn-start"> <i class="b bi-chevron-double-left"></i></a>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="page-numbers btn-start"> <i class="b bi-chevron-double-left"></i></a>
    @endif
    @foreach ($elements as $element)
        @if (is_string($element))
            <li class="disabled"><span>{{ $element }}</span></li>
        @endif
        <span class="page-numbers pagination-space"> <i class="bi bi-three-dots"></i> </span>
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <a class="page-numbers current"><span>{{ $page }}</span></a>
                @else
                    <a href="{{ $url }}" class="page-numbers"><span>{{ $page }}</span></a>
                @endif
            @endforeach
        @endif
        <span class="page-numbers pagination-space"> <i class="bi bi-three-dots"></i> </span>
    @endforeach
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="page-numbers btn-end"><i class="bi bi-chevron-double-right"></i></a>
    @else
        <a class="page-numbers btn-end"><i class="bi bi-chevron-double-right"></i></a>
    @endif
</div>
@endif





@if($paginator->hasPages())
    <div class="pagination style-1 color-main justify-content-center mt-60">
{{--         Previous Page Link--}}
        @if ($paginator->onFirstPage())

        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
                <span class="text text-uppercase"> <i class="la la-angle-left"></i> </span>
            </a>

        @endif


{{--         Pagination Elements--}}
        @foreach ($elements as $element)
{{--             "Three Dots" Separator--}}
            @if (is_string($element))
                <li class="disabled" aria-disabled="true"><span>{{ $element }}</span></li>
            @endif

{{--             Array Of Links--}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <a aria-current="page" href="#" class="active">
                            <span>{{ $page }}</span>
                        </a>

                    @else
                        <a href="{{ $url }}">
                            <span>{{ $page }}</span>
                        </a>

                    @endif
                @endforeach
            @endif
        @endforeach


{{--         Next Page Link--}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
                <span class="text text-uppercase"> <i class="la la-angle-right"></i> </span>
            </a>


        @else

        @endif
    </div>

@endif






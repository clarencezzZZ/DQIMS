@if ($paginator->hasPages())
    <nav class="d-flex flex-column align-items-center justify-content-center py-4">
        {{-- Pagination Information Text --}}
        <div class="mb-3 px-4 py-2 pagination-info-pill">
            <p class="small mb-0">
                {!! __('Showing') !!}
                <span class="fw-bold text-success">{{ $paginator->firstItem() }}</span>
                {!! __('to') !!}
                <span class="fw-bold text-success">{{ $paginator->lastItem() }}</span>
                {!! __('of') !!}
                <span class="fw-bold text-success">{{ $paginator->total() }}</span>
                {!! __('results') !!}
            </p>
        </div>

        {{-- Pagination Links --}}
        <ul class="pagination pagination-sm mb-0 shadow-sm rounded-pill overflow-hidden border">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="page-link border-0 px-3" aria-hidden="true">&lsaquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link border-0 px-3" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true"><span class="page-link border-0">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page"><span class="page-link border-0 px-3">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link border-0 px-3" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link border-0 px-3" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-link border-0 px-3" aria-hidden="true">&rsaquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif

<style>
    /* Professional Center-Stacked Pagination Styling */
    .pagination-info-pill {
        background-color: #f8f9fa;
        border: 1px solid rgba(0,0,0,0.1);
        border-radius: 50px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        color: #6c757d;
        transition: all 0.3s ease;
    }

    .pagination .page-link {
        color: var(--denr-green);
        background-color: transparent;
        transition: all 0.2s ease;
    }

    .pagination .page-item.active .page-link {
        background-color: var(--denr-green) !important;
        color: white !important;
        font-weight: 600;
    }

    .pagination .page-item:not(.active) .page-link:hover {
        background-color: rgba(46, 125, 50, 0.05);
        color: var(--denr-dark);
    }

    [data-theme="dark"] .pagination-info-pill {
        background-color: var(--dark-surface-secondary) !important;
        border-color: var(--dark-border) !important;
        box-shadow: 0 2px 12px rgba(0,0,0,0.4) !important;
        color: #adb5bd !important;
    }

    [data-theme="dark"] .pagination {
        border-color: var(--dark-border) !important;
        background-color: var(--dark-surface-secondary) !important;
        box-shadow: 0 2px 10px rgba(0,0,0,0.3) !important;
    }

    [data-theme="dark"] .pagination .page-link {
        color: #adb5bd !important;
        background-color: transparent !important;
    }

    [data-theme="dark"] .pagination .page-item.active .page-link {
        background-color: var(--denr-green) !important;
        color: white !important;
    }

    [data-theme="dark"] .pagination .page-item.disabled .page-link {
        background-color: transparent !important;
        color: #495057 !important;
    }
</style>

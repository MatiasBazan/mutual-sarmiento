@if ($paginator->hasPages())
<nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">

    {{-- Contador --}}
    <p style="font-size:12px; color:#71717a; margin:0;">
        @if ($paginator->firstItem())
            {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} de {{ $paginator->total() }}
        @else
            {{ $paginator->count() }} resultados
        @endif
    </p>

    {{-- Botones --}}
    <div style="display:flex; align-items:center; gap:4px;">

        {{-- Anterior --}}
        @if ($paginator->onFirstPage())
            <span style="display:inline-flex; align-items:center; justify-content:center; width:30px; height:30px; border-radius:6px; border:1px solid #27272a; color:#3f3f46; cursor:not-allowed;">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
               style="display:inline-flex; align-items:center; justify-content:center; width:30px; height:30px; border-radius:6px; border:1px solid #3f3f46; color:#a1a1aa; transition:border-color 0.15s, color 0.15s;"
               onmouseover="this.style.borderColor='#71717a'; this.style.color='#fafafa'"
               onmouseout="this.style.borderColor='#3f3f46'; this.style.color='#a1a1aa'"
               aria-label="{{ __('pagination.previous') }}">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            </a>
        @endif

        {{-- Páginas --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span style="display:inline-flex; align-items:center; justify-content:center; width:30px; height:30px; font-size:12px; color:#3f3f46;">{{ $element }}</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page"
                              style="display:inline-flex; align-items:center; justify-content:center; width:30px; height:30px; border-radius:6px; border:1px solid #52525b; background:#27272a; color:#fafafa; font-size:12px; font-weight:600; cursor:default;">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                           style="display:inline-flex; align-items:center; justify-content:center; width:30px; height:30px; border-radius:6px; border:1px solid #3f3f46; color:#a1a1aa; font-size:12px; transition:border-color 0.15s, color 0.15s;"
                           onmouseover="this.style.borderColor='#71717a'; this.style.color='#fafafa'"
                           onmouseout="this.style.borderColor='#3f3f46'; this.style.color='#a1a1aa'"
                           aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Siguiente --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next"
               style="display:inline-flex; align-items:center; justify-content:center; width:30px; height:30px; border-radius:6px; border:1px solid #3f3f46; color:#a1a1aa; transition:border-color 0.15s, color 0.15s;"
               onmouseover="this.style.borderColor='#71717a'; this.style.color='#fafafa'"
               onmouseout="this.style.borderColor='#3f3f46'; this.style.color='#a1a1aa'"
               aria-label="{{ __('pagination.next') }}">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
            </a>
        @else
            <span style="display:inline-flex; align-items:center; justify-content:center; width:30px; height:30px; border-radius:6px; border:1px solid #27272a; color:#3f3f46; cursor:not-allowed;">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
            </span>
        @endif

    </div>
</nav>
@endif

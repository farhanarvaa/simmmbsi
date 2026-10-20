@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="simmm-pagination">
        {{-- Info Data di Kiri --}}
        <div class="simmm-pagination-info">
            Menampilkan <span class="font-medium">{{ $paginator->firstItem() }}</span>&ndash;<span class="font-medium">{{ $paginator->lastItem() }}</span> dari <span class="font-medium">{{ $paginator->total() }}</span> data
        </div>

        {{-- Navigasi Halaman di Kanan --}}
        <div class="simmm-pagination-nav">
            {{-- Tombol Sebelumnya (‹) --}}
            @if ($paginator->onFirstPage())
                <span class="simmm-page-btn disabled" aria-disabled="true" aria-label="Halaman Sebelumnya" title="Halaman Sebelumnya">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="simmm-page-btn" rel="prev" aria-label="Halaman Sebelumnya" title="Halaman Sebelumnya">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </a>
            @endif

            {{-- Nomor Halaman --}}
            @foreach ($elements as $element)
                {{-- Separator Titik-titik (...) --}}
                @if (is_string($element))
                    <span class="simmm-page-btn dots" aria-disabled="true">{{ $element }}</span>
                @endif

                {{-- Array Link Halaman --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="simmm-page-btn active" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="simmm-page-btn">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol Selanjutnya (›) --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="simmm-page-btn" rel="next" aria-label="Halaman Selanjutnya" title="Halaman Selanjutnya">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </a>
            @else
                <span class="simmm-page-btn disabled" aria-disabled="true" aria-label="Halaman Selanjutnya" title="Halaman Selanjutnya">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </span>
            @endif
        </div>
    </nav>
@endif

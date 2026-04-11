{{-- resources/views/vendor/pagination/tailwind.blade.php --}}
@if ($paginator->hasPages())
<nav class="lba-pagination" role="navigation" aria-label="Pagination">

    {{-- Info résultats --}}
    <p class="lba-pag-info">
        {{ __('Affichage de') }}
        <strong>{{ $paginator->firstItem() }}</strong>
        {{ __('à') }}
        <strong>{{ $paginator->lastItem() }}</strong>
        {{ __('sur') }}
        <strong>{{ $paginator->total() }}</strong>
        {{ __('résultats') }}
    </p>

    {{-- Boutons --}}
    <div class="lba-pag-links">

        {{-- Précédent --}}
        @if ($paginator->onFirstPage())
            <span class="lba-pag-btn lba-pag-disabled" aria-disabled="true">‹</span>
        @else
            <a class="lba-pag-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev">‹</a>
        @endif

        {{-- Numéros --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="lba-pag-btn lba-pag-dots">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="lba-pag-btn lba-pag-active" aria-current="page">{{ $page }}</span>
                    @else
                        <a class="lba-pag-btn" href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Suivant --}}
        @if ($paginator->hasMorePages())
            <a class="lba-pag-btn" href="{{ $paginator->nextPageUrl() }}" rel="next">›</a>
        @else
            <span class="lba-pag-btn lba-pag-disabled" aria-disabled="true">›</span>
        @endif

    </div>
</nav>
@endif

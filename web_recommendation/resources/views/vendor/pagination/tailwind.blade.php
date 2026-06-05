@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation"
        class="flex flex-wrap items-center justify-center gap-1 md:gap-2 mt-6 md:mt-10 mb-6 md:mb-10 font-semibold">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="px-2 md:px-3 py-1.5 md:py-2 bg-white/5 text-gray-400 rounded-lg cursor-not-allowed">
                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
                class="px-2 md:px-3 py-1.5 md:py-2 bg-white/10 hover:bg-white/20 text-black rounded-lg transition-colors">
                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="px-2 md:px-3 py-1.5 md:py-2 text-gray-400 text-xs md:text-sm">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-2.5 md:px-4 py-1.5 md:py-2 bg-primary-500 text-white text-xs md:text-sm font-semibold rounded-lg">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" class="px-2.5 md:px-4 py-1.5 md:py-2 bg-white/10 hover:bg-blue-500/20 text-black text-xs md:text-sm rounded-lg transition-colors">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
                class="px-2 md:px-3 py-1.5 md:py-2 bg-white/10 hover:bg-white/20 text-black rounded-lg transition-colors">
                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        @else
            <span class="px-2 md:px-3 py-1.5 md:py-2 bg-white/5 text-gray-400 rounded-lg cursor-not-allowed">
                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </span>
        @endif
    </nav>
@endif

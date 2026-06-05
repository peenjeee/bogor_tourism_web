<div>
    <!-- Search Bar -->
    <div class="mb-4 md:mb-6">
        <div class="max-w-md mx-auto">
            <div class="relative">
                <input type="text" 
                       wire:model.live.debounce.400ms="search"
                       placeholder="Cari tempat wisata..."
                       autocomplete="off"
                       class="input-modern pl-10 pr-10 text-sm py-3 border-2 border-primary-400/50 focus:border-primary-500 text-dark-800 dark:text-white w-full">
                
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 stat-label" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                
                <!-- Loading spinner -->
                <div wire:loading wire:target="search" class="absolute right-3 top-1/2 transform -translate-y-1/2">
                    <svg class="animate-spin w-4 h-4 text-primary-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                
                @if($search)
                    <button wire:click="clearSearch"
                       class="absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 flex items-center justify-center rounded-full bg-white/10 stat-label hover:bg-white/20 transition-all">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                @endif
            </div>
        </div>
        
        @if($search)
            <p class="text-center stat-label mt-3 text-xs">
               <span class="card-title font-semibold text-black">"{{ $search }}"</span>
                <span class="ml-2 px-2 py-0.5 bg-bogor-green-500/20 text-bogor-green-400 rounded text-xs font-semibold">{{ $places->total() }} ditemukan</span>
            </p>
        @endif
    </div>

    <!-- Category Filter -->
    <div class="mb-10 md:mb-20 flex flex-wrap items-center justify-center gap-1.5 md:gap-2">
        <button wire:click="setCategory('')" 
           class="text-black px-2.5 md:px-4 py-1 md:py-2 rounded-lg text-[10px] md:text-sm font-semibold transition-all duration-300 {{ !$category ? 'bg-bogor-blue-500 text-white' : 'bg-white/5 card-desc hover:bg-bogor-blue-500/10 border border-white/10' }}">
            Semua
        </button>
        
        @foreach($categories as $cat)
            <button wire:click="setCategory('{{ $cat }}')" 
               class="text-black px-2.5 md:px-4 py-1 md:py-2 rounded-lg text-[10px] md:text-sm font-semibold transition-all duration-300 {{ $category === $cat ? 'bg-bogor-blue-500 text-white' : 'bg-white/5 card-desc hover:bg-bogor-blue-500/10 border border-white/10' }}">
                {{ $cat }}
            </button>
        @endforeach
        
        <!-- Loading indicator for category -->
        <div wire:loading wire:target="setCategory" class="ml-2">
            <svg class="animate-spin w-4 h-4 text-primary-500" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    </div>

    @if($places->count() > 0)
        <!-- Places Grid - flex layout for automatic centering of remaining cards -->
        <div class="flex flex-wrap justify-center gap-2 md:gap-5 lg:gap-6 mb-6 md:mb-10" wire:loading.class="opacity-50" wire:target="search, setCategory">
            @foreach($places as $index => $place)
                <a href="{{ route('places.show', $place->id) }}" 
                   class="group card card-hover overflow-hidden !p-3 md:!p-6 flex flex-col w-[calc(50%-4px)] md:w-[calc(50%-10px)] lg:w-[calc(33.333%-16px)]"
                   wire:key="place-{{ $place->id }}">
                    <div class="relative h-28 sm:h-36 md:h-44 lg:h-52 img-container rounded-lg md:rounded-xl overflow-hidden mb-4 md:mb-6">
                        @if($place->url_gambar)
                            <img src="{{ $place->url_gambar }}" 
                                 alt="{{ $place->nama }}"
                                 class="w-full h-full object-cover"
                                 loading="lazy"
                                 onerror="this.src='https://via.placeholder.com/800x600/1e293b/3b82f6?text={{ urlencode($place->nama) }}'">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-bogor-blue-500 to-bogor-green-500 flex items-center justify-center">
                                <span class="text-3xl md:text-5xl font-bold text-white/30">{{ substr($place->nama, 0, 1) }}</span>
                            </div>
                        @endif
                        <div class="img-overlay"></div>
                        
                        <!-- Category Badge -->
                        <div class="absolute top-1.5 md:top-3 left-1.5 md:left-3">
                            <span class="px-1.5 md:px-3 py-0.5 md:py-1 bg-bogor-blue-500 text-white text-[10px] md:text-xs font-semibold rounded md:rounded-lg shadow">
                                {{ $place->kategori }}
                            </span>
                        </div>
                       
                        <!-- Likes -->
                        @if($place->likes > 0)
                            <div class="absolute top-1.5 md:top-3 right-1.5 md:right-3">
                                <span class="inline-flex items-center px-1.5 md:px-2.5 py-0.5 md:py-1 bg-white text-dark-800 text-[10px] md:text-xs font-semibold rounded md:rounded-lg shadow">
                                    <svg class="w-2.5 h-2.5 md:w-3.5 md:h-3.5 mr-0.5 md:mr-1 text-bogor-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                                    </svg>
                                    {{ number_format($place->likes) }}
                                </span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="p-1 md:p-0 flex flex-col flex-grow">
                        <!-- Place Name -->
                        <h3 class="text-xs sm:text-sm md:text-base lg:text-lg hero-title-accent font-bold mb-1 md:mb-2 card-title group-hover:text-bogor-blue-500 transition-colors line-clamp-1">
                            {{ $place->nama }}
                        </h3>
                        
                        <!-- Address (fixed height area) -->
                        <div class="h-4 md:h-5 mb-1 md:mb-2">
                            @if($place->alamat)
                                <p class="card-desc text-[10px] md:text-xs flex items-start line-clamp-1 text-gray-600">
                                    <svg class="w-2.5 h-2.5 md:w-3.5 md:h-3.5 mr-0.5 md:mr-1 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    </svg>
                                    {{ Str::limit($place->alamat, 25) }}
                                </p>
                            @endif
                        </div>
                        
                        <!-- Description - Fixed height on desktop, hidden on mobile -->
                        <div class="hidden md:block h-[52px] mb-3 overflow-hidden">
                            <p class="card-desc text-xs md:text-sm line-clamp-2 leading-relaxed text-gray-600 text-justify">
                                {{ $place->short_description }}
                            </p>
                        </div>
                        
                        <!-- Lihat Detail -->
                        <div class="flex items-center justify-end pt-1.5 md:pt-3 border-t border-white/10 mt-auto">
                            <span class="inline-flex items-center text-primary-500 text-[10px] md:text-sm font-bold group-hover:translate-x-1 transition-transform">
                                Lihat Detail
                                <svg class="w-2.5 h-2.5 md:w-4 md:h-4 ml-0.5 md:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="flex justify-center">
            {{ $places->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center mb-6 md:mb-6">
            <div class="card inline-block p-8 max-w-md">
                <div class="w-14 h-14 bg-primary-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold mb-2 card-title text-dark-800">Tidak Ada Wisata Ditemukan</h3>
                <p class="card-desc mb-4 text-sm text-dark-800 font-medium">Coba hapus filter atau pilih kategori lain</p>
                <button wire:click="$set('search', ''); $set('category', '')" class="btn-primary text-sm">
                    Lihat Semua Wisata
                </button>
            </div>
        </div>
    @endif
</div>

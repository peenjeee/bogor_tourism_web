@extends('layouts.app')

@section('title', 'BogorXplore - Jelajahi Destinasi Wisata Terbaik di Bogor')

@section('meta_description', 'BogorXplore adalah platform rekomendasi wisata Bogor terlengkap. Temukan destinasi wisata terbaik: air terjun, curug, taman, kebun raya, wisata alam, dan tempat wisata keluarga di Bogor.')

@section('meta_keywords', 'wisata bogor, tempat wisata bogor, rekomendasi wisata bogor, air terjun bogor, curug bogor, taman bogor, kebun raya bogor, destinasi bogor, liburan bogor, travel bogor, wisata alam bogor, wisata keluarga bogor, objek wisata bogor, BogorXplore')

@section('og_title', 'BogorXplore - Jelajahi Destinasi Wisata Terbaik di Bogor')
@section('og_description', 'Temukan destinasi wisata terbaik di Bogor. Air terjun, curug, taman, kebun raya, dan tempat wisata menarik lainnya.')

@section('twitter_title', 'BogorXplore - Platform Wisata Bogor Terlengkap')
@section('twitter_description', 'Jelajahi destinasi wisata terbaik di Bogor dengan rekomendasi terpercaya.')

@section('breadcrumb_schema')
    <script type="application/ld+json">
                    {
                        "@@context": "https://schema.org",
                        "@@type": "BreadcrumbList",
                        "itemListElement": [{
                            "@@type": "ListItem",
                            "position": 1,
                            "name": "Beranda",
                            "item": "{{ url('/') }}"
                        }]
                    }
                    </script>
@endsection

@section('structured_data')
    <script type="application/ld+json">
                    {
                        "@@context": "https://schema.org",
                        "@@type": "TravelAgency",
                        "name": "BogorXplore",
                        "description": "Platform rekomendasi wisata Bogor dengan destinasi wisata terbaik",
                        "url": "{{ url('/') }}",
                        "logo": "{{ asset('images/logo.svg') }}",
                        "areaServed": {
                            "@@type": "City",
                            "name": "Bogor",
                            "containedInPlace": {
                                "@@type": "State",
                                "name": "Jawa Barat",
                                "containedInPlace": {
                                    "@@type": "Country",
                                    "name": "Indonesia"
                                }
                            }
                        },
                        "hasOfferCatalog": {
                            "@@type": "OfferCatalog",
                            "name": "Destinasi Wisata Bogor",
                            "itemListElement": [
                                {
                                    "@@type": "OfferCatalog",
                                    "name": "Air Terjun & Curug"
                                },
                                {
                                    "@@type": "OfferCatalog",
                                    "name": "Taman & Kebun"
                                },
                                {
                                    "@@type": "OfferCatalog",
                                    "name": "Wisata Alam"
                                },
                                {
                                    "@@type": "OfferCatalog",
                                    "name": "Wisata Keluarga"
                                }
                            ]
                        }
                    }
                    </script>
@endsection

@section('content')
    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden pt-16 md:pt-20">
        <!-- Animated Background with Bogor Colors -->
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-gradient-to-br from-dark-950 via-dark-900 to-bogor-blue-900/50"></div>
            <div class="absolute inset-0 bg-grid opacity-30"></div>
            <div
                class="absolute top-1/4 -left-20 w-64 md:w-96 h-64 md:h-96 bg-bogor-blue-500/20 rounded-full blur-3xl animate-pulse">
            </div>
            <div class="absolute bottom-1/4 -right-20 w-64 md:w-96 h-64 md:h-96 bg-bogor-green-500/20 rounded-full blur-3xl animate-pulse"
                style="animation-delay: 1s"></div>
            <div class="absolute top-1/2 left-1/2 w-48 md:w-64 h-48 md:h-64 bg-bogor-gold-500/10 rounded-full blur-3xl animate-pulse"
                style="animation-delay: 2s"></div>
        </div>

        <div class="container mx-auto px-4 lg:px-8 relative z-10">
            <div class="text-center max-w-4xl mx-auto">
                <!-- Heading -->
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold mb-4 md:mb-6 leading-tight"
                    data-aos="fade-down">
                    <span class="hero-title">Jelajahi Keindahan</span>
                    <span class="block mt-2"><span class="text-white">Bogor</span><span
                            class="hero-title-accent">Xplore</span></span>
                </h1>

                <!-- Description -->
                <p class="text-sm sm:text-base md:text-lg hero-desc mb-6 md:mb-8 max-w-2xl mx-auto leading-relaxed font-semibold"
                    data-aos="fade-up">
                    Temukan <span class="hero-title-accent">{{ $places_count ?? '296' }}</span> rekomendasi destinasi
                    wisata menakjubkan di Bogor</span>
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3 md:gap-4" data-aos="fade-up">
                    <a href="{{ route('places.index') }}"
                        class="btn-primary text-sm md:text-base px-5 md:px-8 py-2.5 md:py-3 w-full sm:w-auto">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Mulai Jelajah
                    </a>
                    <a href="#featured"
                        class="btn-secondary text-sm md:text-base px-5 md:px-8 py-2.5 md:py-3 w-full sm:w-auto">
                        Lihat Populer
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                    </a>
                </div>

                <!-- Stats -->
                <div class="mt-10 md:mt-14" data-aos="zoom-in">
                    <div class="inline-block stat-box text-center px-8 md:px-12 py-4 md:py-6">
                        <div class="text-3xl md:text-5xl font-bold stat-number mb-1">{{ $places_count ?? '296' }}</div>
                        <div class="text-xs md:text-sm uppercase tracking-wider stat-label">Destinasi Wisata</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator - Much Lower Position -->
        <!-- <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 animate-bounce z-20" data-aos="fade-up" data-aos-delay="400">
                                                <a href="#featured" class="flex flex-col items-center scroll-indicator">
                                                    <span class="text-xs mb-1">Scroll</span>
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                                    </svg>
                                                </a>
                                            </div> -->
    </section>

    <!-- Featured Destinations Section -->
    <section id="featured" class="py-10 md:py-16 lg:py-20 relative scroll-mt-16">
        <div class="container mx-auto px-4 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-6 md:mb-10" data-aos="fade-down">
                <h2 class="text-xl md:text-3xl lg:text-4xl font-bold section-heading mb-2 md:mb-4 text-dark-950">
                    Destinasi <span class="text-bogor-blue-500">Populer</span>
                </h2>
                <!-- Bigger and Bold Subtitle -->
                <p
                    class="text-base md:text-xl lg:text-2xl section-subtitle font-semibold max-w-2xl mx-auto text-gray-950 mb-20">
                    Jelajahi tempat wisata paling favorit dan paling banyak dikunjungi di <span
                        class="text-bogor-blue-500">Bogor</span>
                </p>
            </div>

            @if($featuredPlaces->count() > 0)
                <!-- Places Grid - 3 columns desktop, 2 columns mobile, centers if 1-2 items -->
                <div class="flex flex-wrap justify-center gap-3 md:gap-5 lg:gap-6">
                    @foreach($featuredPlaces as $index => $place)
                        @php
                            $totalItems = $featuredPlaces->count();
                            $remainingInRow = $totalItems % 3; // For desktop (3 cols)
                            $remainingInMobileRow = $totalItems % 2; // For mobile (2 cols)
                            $isInLastRow = ($index >= $totalItems - $remainingInRow) && $remainingInRow > 0;
                            $isInLastMobileRow = ($index >= $totalItems - $remainingInMobileRow) && $remainingInMobileRow > 0;
                        @endphp
                        <a href="{{ route('places.show', $place->id) }}"
                            class="group card card-hover overflow-hidden !p-3 md:!p-6 flex flex-col w-[calc(50%-6px)] md:w-[calc(50%-10px)] lg:w-[calc(33.333%-16px)]"
                            wire:key="place-{{ $place->id }}">
                            <div
                                class="relative h-28 sm:h-36 md:h-44 lg:h-52 img-container rounded-lg md:rounded-xl overflow-hidden mb-4 md:mb-6">
                                @if($place->url_gambar)
                                    <img src="{{ $place->url_gambar }}" alt="{{ $place->nama }}" class="w-full h-full object-cover"
                                        loading="lazy"
                                        onerror="this.src='https://via.placeholder.com/800x600/1e293b/3b82f6?text={{ urlencode($place->nama) }}'">
                                @else
                                    <div
                                        class="w-full h-full bg-gradient-to-br from-bogor-blue-500 to-bogor-green-500 flex items-center justify-center">
                                        <span
                                            class="text-3xl md:text-5xl font-bold text-white/30">{{ substr($place->nama, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div class="img-overlay"></div>

                                <!-- Category Badge -->
                                <div class="absolute top-1.5 md:top-3 left-1.5 md:left-3">
                                    <span
                                        class="px-1.5 md:px-3 py-0.5 md:py-1 bg-bogor-blue-500 text-white text-[10px] md:text-xs font-semibold rounded md:rounded-lg shadow">
                                        {{ $place->kategori }}
                                    </span>
                                </div>

                                <!-- Likes -->
                                @if($place->likes > 0)
                                    <div class="absolute top-1.5 md:top-3 right-1.5 md:right-3">
                                        <span
                                            class="inline-flex items-center px-1.5 md:px-2.5 py-0.5 md:py-1 bg-white text-dark-800 text-[10px] md:text-xs font-semibold rounded md:rounded-lg shadow">
                                            <svg class="w-2.5 h-2.5 md:w-3.5 md:h-3.5 mr-0.5 md:mr-1 text-bogor-red-500"
                                                fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ number_format($place->likes) }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <div class="p-1 md:p-0 flex flex-col flex-grow">
                                <!-- Place Name -->
                                <h3
                                    class="text-xs sm:text-sm md:text-base lg:text-lg hero-title-accent font-bold mb-1 md:mb-2 card-title group-hover:text-bogor-blue-500 transition-colors line-clamp-1">
                                    {{ $place->nama }}
                                </h3>

                                <!-- Address (fixed height area) -->
                                <div class="h-4 md:h-5 mb-1 md:mb-2">
                                    @if($place->alamat)
                                        <p class="card-desc text-[10px] md:text-xs flex items-start line-clamp-1 text-gray-600">
                                            <svg class="w-2.5 h-2.5 md:w-3.5 md:h-3.5 mr-0.5 md:mr-1 mt-0.5 flex-shrink-0" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
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
                                    <span
                                        class="inline-flex items-center text-primary-500 text-[10px] md:text-sm font-bold group-hover:translate-x-1 transition-transform">
                                        Lihat Detail
                                        <svg class="w-2.5 h-2.5 md:w-4 md:h-4 ml-0.5 md:ml-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- View All Button -->
                <div class="text-center mt-10 md:mt-20" data-aos="zoom-in">
                    <a href="{{ route('places.index') }}" class="btn-primary inline-flex items-center text-sm md:text-base">
                        Lihat Semua Destinasi
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center">
                    <div class="card inline-block p-8 max-w-lg mb-5">
                        <!-- <div class="text-4xl mb-4"></div> -->
                        <h3 class="text-lg font-bold mb-3 card-title text-bogor-blue-500">Belum Ada Data Wisata</h3>
                        <p class="card-desc mb-4 text-sm text-gray-600">Data Wisata Belum Tersedia</p>
                        <!-- <code class="block bg-dark-800 px-4 py-2 rounded-lg text-primary-400 text-sm">
                                                                                                        php artisan db:seed
                                                                                                    </code> -->
                    </div>
                </div>
            @endif
        </div>
    </section>

@endsection

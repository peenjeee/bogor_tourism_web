@extends('layouts.app')

@section('title', $place->nama . ' - Wisata Bogor')

@section('meta_title', $place->nama . ' - Destinasi Wisata Bogor | BogorXplore')
@section('meta_description', Str::limit(strip_tags($place->deskripsi), 160) . ' Temukan info lengkap di BogorXplore.')
@section('meta_keywords', $place->nama . ', ' . $place->kategori . ', wisata bogor, ' . ($place->kecamatan ?? 'bogor') . ', tempat wisata')

@section('og_title', $place->nama . ' - Wisata ' . $place->kategori . ' di Bogor')
@section('og_description', Str::limit(strip_tags($place->deskripsi), 200))
@section('og_image', $place->url_gambar ?? asset('images/og-image.jpg'))

@section('twitter_title', $place->nama . ' - BogorXplore')
@section('twitter_description', Str::limit(strip_tags($place->deskripsi), 200))
@section('twitter_image', $place->url_gambar ?? asset('images/og-image.jpg'))

@section('breadcrumb_schema')
    <script type="application/ld+json">
                                                                        {
                                                                            "@@context": "https://schema.org",
                                                                            "@@type": "BreadcrumbList",
                                                                            "itemListElement": [
                                                                                {
                                                                                    "@@type": "ListItem",
                                                                                    "position": 1,
                                                                                    "name": "Beranda",
                                                                                    "item": "{{ url('/') }}"
                                                                                },
                                                                                {
                                                                                    "@@type": "ListItem",
                                                                                    "position": 2,
                                                                                    "name": "Daftar Wisata",
                                                                                    "item": "{{ url('/places') }}"
                                                                                },
                                                                                {
                                                                                    "@@type": "ListItem",
                                                                                    "position": 3,
                                                                                    "name": "{{ $place->nama }}",
                                                                                    "item": "{{ url()->current() }}"
                                                                                }
                                                                            ]
                                                                        }
                                                                        </script>
@endsection

@section('structured_data')
    <script type="application/ld+json">
                                                                    {
                                                                        "@@context": "https://schema.org",
                                                                        "@@type": "TouristAttraction",
                                                                        "name": {!! json_encode($place->nama) !!},
                                                                        "description": {!! json_encode(Str::limit(strip_tags(preg_replace('/\s+/', ' ', $place->deskripsi)), 500)) !!},
                                                                        "url": "{{ url()->current() }}",
                                                                        "image": "{{ $place->url_gambar ?? asset('images/og-image.jpg') }}",
                                                                        "address": {
                                                                            "@@type": "PostalAddress",
                                                                            "addressLocality": "Bogor",
                                                                            "addressRegion": "Jawa Barat",
                                                                            "addressCountry": "ID",
                                                                            "streetAddress": {!! json_encode($place->alamat ?? 'Bogor, Jawa Barat') !!}
                                                                        },
                                                                        "geo": {
                                                                            "@@type": "GeoCoordinates",
                                                                            "latitude": {{ $place->latitude ?? -6.5971 }},
                                                                            "longitude": {{ $place->longitude ?? 106.8060 }}
                                                                        },
                                                                        "priceRange": {!! json_encode($place->harga_tiket ?? 'Gratis - Berbayar') !!},
                                                                        "openingHours": {!! json_encode($place->jam_operasional ?? 'Setiap Hari') !!},
                                                                        "touristType": {!! json_encode($place->kategori) !!},
                                                                        "isAccessibleForFree": false,
                                                                        "publicAccess": true,
                                                                        "aggregateRating": {
                                                                            "@@type": "AggregateRating",
                                                                            "ratingValue": "4.5",
                                                                            "reviewCount": "{{ $place->likes ?? 1 }}",
                                                                            "bestRating": "5",
                                                                            "worstRating": "1"
                                                                        }
                                                                    }
                                                                    </script>
@endsection

@push('styles')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        .leaflet-container {
            border-radius: 0.75rem;
            z-index: 1;
        }

        .map-marker-popup .popup-title {
            font-weight: 600;
            color: #1e40af;
            margin-bottom: 4px;
        }

        .map-marker-popup .popup-address {
            font-size: 12px;
            color: #64748b;
        }

        .map-marker-popup .popup-link {
            display: inline-flex;
            align-items: center;
            margin-top: 8px;
            padding: 4px 12px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            border-radius: 6px;
            font-size: 11px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .map-marker-popup .popup-link:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
    </style>
@endpush

@section('content')
    <!-- Hero Section with Image -->
    <section class="relative pt-16 md:pt-20 pb-0">
        <div class="relative h-56 md:h-80 lg:h-96 overflow-hidden">
            @if($place->url_gambar)
                <img src="{{ $place->url_gambar }}" alt="{{ $place->nama }}" class="w-full h-full object-cover"
                    onerror="this.src='https://via.placeholder.com/1200x600/1e293b/3b82f6?text={{ urlencode($place->nama) }}'">
            @else
                <div
                    class="w-full h-full bg-gradient-to-br from-bogor-blue-500 via-bogor-green-500 to-bogor-gold-500 flex items-center justify-center">
                    <span class="text-6xl md:text-8xl font-bold text-white/20">{{ substr($place->nama, 0, 1) }}</span>
                </div>
            @endif

            <!-- Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-dark-900 via-dark-900/60 to-transparent"></div>

            <!-- Content Overlay -->
            <div class="absolute bottom-0 left-0 right-0 pb-4 md:pb-8">
                <div class="container mx-auto px-4 lg:px-8">
                    <!-- Breadcrumb -->
                    <nav class="flex items-center space-x-1 text-[10px] md:text-xs text-gray-300 mb-2 md:mb-3">
                        <a href="{{ route('home') }}" class="hover:text-white transition-colors">Beranda</a>
                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        <a href="{{ route('places.index') }}" class="hover:text-white transition-colors">Wisata</a>
                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        <span
                            class="text-white font-medium truncate max-w-[100px] md:max-w-none">{{ Str::limit($place->nama, 100) }}</span>
                    </nav>

                    <!-- Title & Category -->
                    <div class="flex flex-wrap items-center gap-2 mb-2 md:mb-3">
                        <h1 class="text-lg md:text-2xl lg:text-3xl font-bold text-white">{{ $place->nama }}</h1>
                        <span
                            class="px-2 py-0.5 md:px-3 md:py-1 bg-bogor-blue-500 text-white text-[10px] md:text-xs font-semibold rounded-lg">{{ $place->kategori }}</span>
                    </div>

                    <!-- Meta Info -->
                    <div class="flex flex-wrap items-center gap-3 text-gray-200 text-[10px] md:text-sm">
                        @if($place->likes > 0)
                            <div class="flex items-center">
                                <svg class="w-3 h-3 md:w-4 md:h-4 mr-1 text-bogor-red-400" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ number_format($place->likes) }} Likes
                            </div>
                        @endif

                        @if($place->alamat)
                            <!-- <div class="flex items-center">
                                                                                                                                                                                                                                                                                                                                                                                    <svg class="w-3         h-3 md:w-4 md:h-4 mr-1 text-bogor-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                                                                                                                                                                                                                                                                                                        <path stroke-lin        ecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                                                                                                                                                                                                                                                                                                                                                    </svg>        
                                                                                                                                                                                                                                                                                                                                                                                    <span class="tru        ncate max-w-[150px] md:max-w-none">{{ Str::limit($place->alamat, 1000) }}</span>
                                                                                                                                                                                                                                                                                                                                                                                </div> -->
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-6 md:py-12">
        <div class="container mx-auto px-4 lg:px-8">
            <!-- Single Column Layout - No Sidebar -->
            <div class="max-w-4xl mx-auto space-y-4 md:space-y-6">

                @php
                    // Parse isi content to format and extract tags
                    $content = $place->deskripsi ?? '';
                    $lines = explode("\n", $content);

                    $formattedContent = [];
                    $currentSection = 'deskripsi';
                    $sectionContent = [];
                    $tags = [];

                    foreach ($lines as $line) {
                        $line = trim($line);
                        if (empty($line))
                            continue;

                        $lineLower = strtolower($line);

                        // Extract Article Tags
                        if (preg_match('/^article tags[:\s]*/i', $line)) {
                            $tagLine = preg_replace('/^article tags[:\s]*/i', '', $line);
                            $tagItems = preg_split('/[·,]/', $tagLine);
                            foreach ($tagItems as $tag) {
                                $tag = trim($tag);
                                if (!empty($tag))
                                    $tags[] = $tag;
                            }
                            continue;
                        }

                        // Extract Article Categories (also add to tags)
                        if (preg_match('/^article categories[:\s]*/i', $line)) {
                            $catLine = preg_replace('/^article categories[:\s]*/i', '', $line);
                            $catItems = preg_split('/[·,]/', $catLine);
                            foreach ($catItems as $cat) {
                                $cat = trim($cat);
                                if (!empty($cat))
                                    $tags[] = $cat;
                            }
                            continue;
                        }

                        // Detect section headers
                        $isHeader = false;
                        if (preg_match('/^fasilitas/i', $lineLower)) {
                            if (!empty($sectionContent)) {
                                $formattedContent[$currentSection] = $sectionContent;
                            }
                            $currentSection = 'fasilitas';
                            $sectionContent = [];
                            $isHeader = true;
                        } elseif (preg_match('/^(harga|tiket masuk|htm)/i', $lineLower)) {
                            if (!empty($sectionContent)) {
                                $formattedContent[$currentSection] = $sectionContent;
                            }
                            $currentSection = 'harga';
                            $sectionContent = [];
                            $isHeader = true;
                        } elseif (preg_match('/^(jam operasional|jam buka)/i', $lineLower)) {
                            if (!empty($sectionContent)) {
                                $formattedContent[$currentSection] = $sectionContent;
                            }
                            $currentSection = 'jam';
                            $sectionContent = [];
                            $isHeader = true;
                        } elseif (preg_match('/^(lokasi|alamat)/i', $lineLower) && $currentSection !== 'lokasi') {
                            if (!empty($sectionContent)) {
                                $formattedContent[$currentSection] = $sectionContent;
                            }
                            $currentSection = 'lokasi';
                            $sectionContent = [];
                            $isHeader = true;
                        } elseif (preg_match('/^(sumber|instagram)/i', $lineLower)) {
                            if (!empty($sectionContent)) {
                                $formattedContent[$currentSection] = $sectionContent;
                            }
                            $currentSection = 'sumber';
                            $sectionContent = [];
                            $isHeader = true;
                        }

                        if (!$isHeader) {
                            $sectionContent[] = $line;
                        }
                    }

                    // Save last section
                    if (!empty($sectionContent)) {
                        $formattedContent[$currentSection] = $sectionContent;
                    }
                @endphp

                <!-- Informasi Section - Formatted Content -->
                <div class="card">
                    <div class="flex items-center mb-4">
                        <div
                            class="w-7 h-7 md:w-8 md:h-8 bg-bogor-blue-500/20 rounded-lg flex items-center justify-center mr-2">
                            <svg class="w-3.5 h-3.5 md:w-4 md:h-4 text-bogor-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h2 class="text-sm md:text-lg font-bold card-title text-blue-500">Informasi</h2>
                    </div>

                    <div class="card-desc text-xs md:text-sm leading-relaxed text-gray-500 space-y-4">
                        @if(!empty($formattedContent))
                            {{-- Deskripsi --}}
                            @if(isset($formattedContent['deskripsi']) && count($formattedContent['deskripsi']) > 0)
                                <div>
                                    <h3 class="font-bold text-gray-700 mb-1">Deskripsi</h3>
                                    <div class="text-justify">
                                        {!! nl2br(e(implode("\n", $formattedContent['deskripsi']))) !!}
                                    </div>
                                </div>
                            @endif

                            {{-- Fasilitas --}}
                            @if(isset($formattedContent['fasilitas']) && count($formattedContent['fasilitas']) > 0)
                                <div>
                                    <h3 class="font-bold text-gray-700 mb-1">Fasilitas</h3>
                                    <ul class="list-disc list-inside space-y-0.5">
                                        @foreach($formattedContent['fasilitas'] as $item)
                                            <li>{{ $item }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            {{-- Harga Tiket --}}
                            @if(isset($formattedContent['harga']) && count($formattedContent['harga']) > 0)
                                <div>
                                    <h3 class="font-bold text-gray-700 mb-1">Harga Tiket</h3>
                                    <ul class="list-disc list-inside space-y-0.5">
                                        @foreach($formattedContent['harga'] as $item)
                                            <li>{{ $item }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            {{-- Jam Operasional --}}
                            @if(isset($formattedContent['jam']) && count($formattedContent['jam']) > 0)
                                <div>
                                    <h3 class="font-bold text-gray-700 mb-1">Jam Operasional</h3>
                                    <ul class="list-disc list-inside space-y-0.5">
                                        @foreach($formattedContent['jam'] as $item)
                                            <li>{{ $item }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            {{-- Lokasi/Alamat --}}
                            @if(isset($formattedContent['lokasi']) && count($formattedContent['lokasi']) > 0)
                                <div>
                                    <h3 class="font-bold text-gray-700 mb-1">Alamat</h3>
                                    <p>{{ implode(' ', $formattedContent['lokasi']) }}</p>
                                </div>
                            @endif

                            {{-- Sumber --}}
                            @if(isset($formattedContent['sumber']) && count($formattedContent['sumber']) > 0)
                                <div>
                                    <h3 class="font-bold text-gray-700 mb-1">Sumber</h3>
                                    @foreach($formattedContent['sumber'] as $item)
                                        @if(filter_var($item, FILTER_VALIDATE_URL))
                                            <a href="{{ $item }}" target="_blank"
                                                class="text-bogor-blue-500 hover:underline break-all">{{ $item }}</a><br>
                                        @else
                                            <p>{{ $item }}</p>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <p class="text-gray-400 italic">Informasi belum tersedia untuk destinasi ini.</p>
                        @endif
                    </div>
                </div>

                <!-- Lokasi Section with Google Maps -->
                <div class="card">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center">
                            <div
                                class="w-7 h-7 md:w-8 md:h-8 bg-bogor-red-500/20 rounded-lg flex items-center justify-center mr-2">
                                <svg class="w-3.5 h-3.5 md:w-4 md:h-4 text-bogor-red-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <h2 class="text-sm md:text-lg font-bold card-title text-bogor-red-500">Lokasi</h2>
                        </div>
                    </div>

                    @php
                        // Use place name with full location for accurate Google Maps
                        $mapsQuery = $place->nama . ', Kabupaten Bogor, Jawa Barat';
                    @endphp

                    <!-- Google Maps Embed - Search by parsed address or place name -->
                    <div class="w-full h-48 md:h-64 rounded-xl overflow-hidden shadow-lg mb-4">
                        <iframe src="https://www.google.com/maps?q={{ urlencode($mapsQuery) }}&output=embed" width="100%"
                            height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>

                    <!-- Open in Maps Button -->
                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($mapsQuery) }}" target="_blank"
                        class="btn-primary w-full text-center text-xs md:text-sm py-2.5 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Buka di Maps
                    </a>
                </div>

                <!-- Tags Section (Below Maps) -->
                @if(count($tags) > 0)
                    <div class="flex flex-wrap justify-center gap-1.5 md:gap-2">
                        @foreach($tags as $tag)
                            <span
                                class="px-2 md:px-3 py-1 bg-bogor-gold-500/10 text-bogor-gold-500 text-[10px] md:text-xs rounded-lg border border-bogor-gold-500/20 font-semibold">
                                #{{ $tag }}
                            </span>
                        @endforeach
                    </div>
                @endif

            </div>
        </div>
    </section>

    <!-- Recommendations Section -->
    <section class="py-8 md:py-12">
        <div class="container mx-auto px-4 lg:px-8 relative z-10 pt-6 md:pt-6 mb-6 md:mb-6">
            <div class="text-center max-w-3xl mx-auto">
                <h2 class="text-xl md:text-3xl lg:text-4xl font-bold mb-2 md:mb-4 section-heading text-black"
                    data-aos="fade-down">
                    Rekomendasi <span class="hero-title-accent mt-2 text-bogor-blue-500">Wisata</span>
                </h2>
                @if($apiError)
                    <p class="section-subtitle text-sm md:text-base mb-14 text-black" data-aos="fade-up">
                        Wisata dengan kategori serupa</p>
                @else
                    <p class="section-subtitle text-sm md:text-base mb-14 text-black" data-aos="fade-up">
                        Ayo temukan tempat lain yang menarik!</p>
                @endif
            </div>

            @if($recommendations->count() > 0)
                <!-- Recommendations Grid - flex layout for automatic centering of remaining cards -->
                <div class="flex flex-wrap justify-center gap-2 sm:gap-3 md:gap-4">
                    @foreach($recommendations->take(6) as $index => $rec)
                        <a href="{{ route('places.show', $rec->id) }}"
                            class="group card card-hover overflow-hidden !p-3 md:!p-6 flex flex-col w-[calc(50%-4px)] md:w-[calc(50%-6px)] lg:w-[calc(33.333%-11px)]">
                            <div
                                class="relative h-28 sm:h-36 md:h-44 lg:h-52 img-container rounded-lg md:rounded-xl overflow-hidden mb-4 md:mb-6">
                                @if($rec->url_gambar)
                                    <img src="{{ $rec->url_gambar }}" alt="{{ $rec->nama }}" class="w-full h-full object-cover"
                                        loading="lazy"
                                        onerror="this.src='https://via.placeholder.com/800x600/1e293b/3b82f6?text={{ urlencode($rec->nama) }}'">
                                @else
                                    <div
                                        class="w-full h-full bg-gradient-to-br from-bogor-blue-500 to-bogor-green-500 flex items-center justify-center">
                                        <span class="text-3xl md:text-5xl font-bold text-white/30">{{ substr($rec->nama, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div class="img-overlay"></div>

                                <!-- Category Badge -->
                                <div class="absolute top-1.5 md:top-3 left-1.5 md:left-3">
                                    <span
                                        class="px-1.5 md:px-3 py-0.5 md:py-1 bg-bogor-blue-500 text-white text-[10px] md:text-xs font-semibold rounded md:rounded-lg shadow">
                                        {{ $rec->kategori }}
                                    </span>
                                </div>

                                <!-- Likes -->
                                @if($rec->likes > 0)
                                    <div class="absolute top-1.5 md:top-3 right-1.5 md:right-3">
                                        <span
                                            class="inline-flex items-center px-1.5 md:px-2.5 py-0.5 md:py-1 bg-white text-dark-800 text-[10px] md:text-xs font-semibold rounded md:rounded-lg shadow">
                                            <svg class="w-2.5 h-2.5 md:w-3.5 md:h-3.5 mr-0.5 md:mr-1 text-bogor-red-500"
                                                fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ number_format($rec->likes) }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <div class="p-1 md:p-0 flex flex-col flex-grow">
                                <!-- Place Name -->
                                <h3
                                    class="text-xs sm:text-sm md:text-base lg:text-lg hero-title-accent font-bold mb-1 md:mb-2 card-title group-hover:text-bogor-blue-500 transition-colors line-clamp-1">
                                    {{ $rec->nama }}
                                </h3>

                                <!-- Address (fixed height area) -->
                                <div class="h-4 md:h-5 mb-1 md:mb-2">
                                    @if($rec->alamat)
                                        <p class="card-desc text-[10px] md:text-xs flex items-start line-clamp-1 text-gray-600">
                                            <svg class="w-2.5 h-2.5 md:w-3.5 md:h-3.5 mr-0.5 md:mr-1 mt-0.5 flex-shrink-0" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            </svg>
                                            {{ Str::limit($rec->alamat, 25) }}
                                        </p>
                                    @endif
                                </div>

                                <!-- Description - Fixed height on desktop, hidden on mobile -->
                                <div class="hidden md:block h-[52px] mb-3 overflow-hidden">
                                    <p class="card-desc text-xs md:text-sm line-clamp-2 leading-relaxed text-gray-600 text-justify">
                                        {{ $rec->short_description }}
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

                <!-- Action Buttons - Always Side by Side -->
                <div class="flex flex-row items-stretch justify-center gap-2 sm:gap-3 mt-16 mb-8 px-4 sm:px-6">
                    <!-- Kembali ke Daftar -->
                    <a href="{{ route('places.index') }}"
                        class="flex-1 sm:flex-none inline-flex items-center justify-center px-3 sm:px-5 py-2.5 sm:py-3 bg-white border-2 border-gray-300 hover:border-bogor-blue-500 hover:bg-bogor-blue-50 text-gray-700 rounded-lg sm:rounded-xl transition-all text-xs sm:text-sm font-semibold group shadow-sm">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2 group-hover:-translate-x-1 transition-transform"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span class="whitespace-nowrap">Kembali</span>
                    </a>

                    <!-- Lihat Lebih Banyak -->
                    <a href="{{ route('places.index', ['category' => $place->kategori]) }}"
                        class="flex-1 sm:flex-none inline-flex items-center justify-center px-3 sm:px-5 py-2.5 sm:py-3 bg-gradient-to-r from-bogor-blue-500 to-bogor-blue-600 hover:from-bogor-blue-600 hover:to-bogor-blue-700 text-white rounded-lg sm:rounded-xl transition-all text-xs sm:text-sm font-semibold shadow-md hover:shadow-lg">
                        <span class="whitespace-nowrap">Lihat Lainnya</span>
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 ml-1 sm:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center mb-6 md:mb-6">
                    <div class="card inline-block p-8 max-w-md">
                        <div class="w-14 h-14 bg-primary-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-7 h-7 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold mb-2 card-title text-dark-800">Tidak ada data</h3>
                        <p class="card-desc mb-4 text-sm text-dark-800 font-medium">
                            Rekomendasi tidak ditemukan
                        </p>
                        <a href="{{ route('places.index') }}" class="btn-primary text-sm">
                            Lihat Semua Wisata
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
    {{-- Map removed --}}
@endpush

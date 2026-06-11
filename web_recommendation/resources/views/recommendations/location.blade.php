@extends('layouts.app')

@section('title', 'Rekomendasi Lokasi - Wisata Bogor | BogorXplore')

@section('meta_description', 'Temukan rekomendasi wisata Bogor terdekat berdasarkan destinasi pilihan atau lokasi Anda saat ini.')

@section('meta_keywords', 'rekomendasi lokasi wisata bogor, wisata terdekat bogor, jarak wisata bogor, rekomendasi wisata bogor')

@section('og_title', 'Rekomendasi Lokasi Wisata Bogor')
@section('og_description', 'Pilih destinasi awal atau gunakan lokasi Anda untuk menemukan wisata Bogor terdekat.')

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
                    "name": "Rekomendasi Lokasi",
                    "item": "{{ url('/recommendations') }}"
                }
            ]
        }
    </script>
@endsection

@section('structured_data')
    <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@@type": "CollectionPage",
            "name": "Rekomendasi Lokasi Wisata Bogor",
            "description": "Rekomendasi destinasi wisata Bogor berdasarkan jarak dari titik awal pilihan.",
            "url": "{{ url('/recommendations') }}",
            "isPartOf": {
                "@@type": "WebSite",
                "name": "BogorXplore",
                "url": "{{ url('/') }}"
            }
        }
    </script>
@endsection

@push('styles')
    <style>
        #location-recommendation-page {
            color: #1e293b;
        }

        #location-recommendation-page .card,
        #location-recommendation-page .stat-box,
        #location-recommendation-page .info-box {
            border: 1px solid rgba(148, 163, 184, 0.22) !important;
            background: rgba(255, 255, 255, 0.96) !important;
            color: #1e293b !important;
        }

        #location-recommendation-page .card-title,
        #location-recommendation-page .info-value,
        #location-recommendation-page .recommendation-title {
            color: #1e293b !important;
        }

        #location-recommendation-page .card-desc,
        #location-recommendation-page .stat-label,
        #location-recommendation-page .info-label,
        #location-recommendation-page .recommendation-muted {
            color: #475569 !important;
        }

        #location-recommendation-page .input-modern {
            border: 1px solid #bfdbfe !important;
            background: #ffffff !important;
            color: #1e293b !important;
        }

        #location-recommendation-page .input-modern::placeholder {
            color: #64748b !important;
            opacity: 1;
        }

        #location-recommendation-page .btn-secondary {
            border: 1px solid #cbd5e1 !important;
            background: #ffffff !important;
            color: #1e293b !important;
        }

        #location-recommendation-page .btn-secondary:hover {
            border-color: #3b82f6 !important;
            background: #eff6ff !important;
            color: #1d4ed8 !important;
        }

        #location-recommendation-page .badge {
            border-color: #bfdbfe !important;
            background: #dbeafe !important;
            color: #1d4ed8 !important;
        }

        #location-recommendation-page .destination-option {
            color: #1e293b !important;
        }

        #location-recommendation-page .destination-option:hover {
            background: transparent !important;
        }

        #location-recommendation-page .destination-option.destination-option-selected,
        #location-recommendation-page .destination-option.destination-option-selected:hover {
            background: #3b82f6 !important;
            color: #ffffff !important;
        }

        #location-recommendation-page .destination-option.destination-option-selected span {
            color: #ffffff !important;
        }

        #location-recommendation-page .origin-sidebar {
            z-index: 20;
        }

        @media (max-width: 1023px) {
            #location-recommendation-page .origin-sidebar {
                position: relative;
            }
        }

        @media (min-width: 1024px) {
            #location-recommendation-page .origin-sidebar {
                position: sticky;
                top: 7rem;
                align-self: flex-start;
            }
        }

        #location-recommendation-page .recommendation-content {
            position: relative;
            z-index: 1;
            min-width: 0;
        }

        #location-recommendation-page #destination-picker {
            position: relative;
            z-index: 30;
        }

        #location-recommendation-page #destination-panel {
            z-index: 40;
        }

        #location-recommendation-page .recommendation-line-clamp-1,
        #location-recommendation-page .recommendation-line-clamp-2,
        #location-recommendation-page .recommendation-line-clamp-3 {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        #location-recommendation-page .recommendation-line-clamp-1 {
            -webkit-line-clamp: 1;
        }

        #location-recommendation-page .recommendation-line-clamp-2 {
            -webkit-line-clamp: 2;
        }

        #location-recommendation-page .recommendation-line-clamp-3 {
            -webkit-line-clamp: 3;
        }
    </style>
@endpush

@section('content')
    <section class="pt-20 md:pt-24 pb-8 md:pb-12 relative overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-gradient-to-br from-dark-950 via-dark-900 to-bogor-blue-900/50"></div>
            <div class="absolute inset-0 bg-grid opacity-30"></div>
            <div class="absolute top-1/4 -left-20 w-64 md:w-96 h-64 md:h-96 bg-bogor-blue-500/20 rounded-full blur-3xl animate-pulse">
            </div>
            <div class="absolute bottom-1/4 -right-20 w-64 md:w-96 h-64 md:h-96 bg-bogor-green-500/20 rounded-full blur-3xl animate-pulse"
                style="animation-delay: 1s"></div>
            <div class="absolute top-1/2 left-1/2 w-48 md:w-64 h-48 md:h-64 bg-bogor-gold-500/10 rounded-full blur-3xl animate-pulse"
                style="animation-delay: 2s"></div>
        </div>

        <div class="container mx-auto px-4 lg:px-8 relative z-10 pt-6 md:pt-8">
            <div class="text-center max-w-3xl mx-auto">
                <h1 class="text-2xl md:text-4xl lg:text-5xl font-bold mb-3 md:mb-4 section-heading" data-aos="fade-up">
                    Temukan Wisata <span class="hero-title-accent">Terdekat</span>
                </h1>
                <p class="section-subtitle text-sm md:text-base max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">
                    Pilih titik awal dari destinasi wisata atau aktifkan lokasi Anda saat ini.
                </p>
            </div>
        </div>
    </section>

    <section class="py-6 md:py-12">
        <div class="container mx-auto px-4 lg:px-8">
            <div id="location-recommendation-page" class="grid gap-5 lg:grid-cols-[360px_minmax(0,1fr)] lg:gap-8"
                data-initial-place-id="{{ $initialPlaceId }}">
                <aside class="origin-sidebar grid gap-5 lg:sticky lg:top-28 lg:self-start">
                    <div class="card space-y-5">
                        <div>
                            <h2 class="text-lg md:text-2xl font-bold card-title">Titik Awal</h2>
                            <p class="card-desc text-xs md:text-sm mt-1">
                                Gunakan destinasi pilihan atau lokasi perangkat Anda.
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" id="selected-place-mode"
                                class="btn-primary px-3 py-2.5 text-xs md:text-sm">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Wisata
                            </button>
                            <button type="button" id="current-location-mode"
                                class="btn-secondary px-3 py-2.5 text-xs md:text-sm">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 2v2m0 16v2m10-10h-2M4 12H2m16.95-6.95l-1.414 1.414M6.464 17.536L5.05 18.95m13.9 0l-1.414-1.414M6.464 6.464L5.05 5.05" />
                                </svg>
                                Lokasi Saya
                            </button>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide stat-label mb-2">
                                Pilih Destinasi
                            </label>
                            <div class="relative" id="destination-picker">
                                <button type="button" id="destination-toggle"
                                    class="input-modern flex items-center justify-between gap-3 text-left text-sm py-3">
                                    <span id="destination-label" class="min-w-0 truncate">Pilih destinasi...</span>
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div id="destination-panel"
                                    class="hidden absolute left-0 right-0 top-[calc(100%+0.5rem)] card !p-2 shadow-elevated">
                                    <div class="relative mb-2">
                                        <input id="destination-search" type="search" autocomplete="off"
                                            class="input-modern py-2.5 pl-9 pr-3 text-sm"
                                            placeholder="Cari destinasi...">
                                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 stat-label" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <div id="destination-list" class="max-h-72 overflow-y-auto scrollbar-hide space-y-1 pr-1"></div>
                                    <p id="destination-count" class="stat-label text-[11px] mt-2 px-1"></p>
                                </div>
                            </div>
                        </div>

                        <div id="location-status" class="info-box p-3 text-xs md:text-sm card-desc">
                            Rekomendasi siap dihitung dari destinasi pilihan.
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div class="info-box p-3">
                                <p class="info-label text-[11px] uppercase tracking-wide">Latitude</p>
                                <p id="origin-latitude" class="info-value text-sm font-bold mt-1">-</p>
                            </div>
                            <div class="info-box p-3">
                                <p class="info-label text-[11px] uppercase tracking-wide">Longitude</p>
                                <p id="origin-longitude" class="info-value text-sm font-bold mt-1">-</p>
                            </div>
                        </div>
                    </div>
                </aside>

                <div class="recommendation-content">
                    <div class="card mb-5 md:mb-8">
                        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                            <div>
                                <span id="origin-source" class="badge mb-3">Titik awal</span>
                                <h2 class="text-xl md:text-3xl font-bold card-title">
                                    <span id="origin-title">Rekomendasi Terdekat</span>
                                </h2>
                                <p id="origin-description" class="card-desc text-sm mt-2">
                                    Pilih destinasi untuk melihat wisata terdekat.
                                </p>
                            </div>
                            <div class="stat-box px-4 py-3 text-center">
                                <p id="recommendation-count" class="stat-number text-2xl font-bold">0</p>
                                <p class="stat-label text-xs uppercase tracking-wide">Destinasi</p>
                            </div>
                        </div>
                    </div>

                    <div id="recommendation-grid" class="flex flex-wrap justify-center gap-2 sm:gap-3 md:gap-4"></div>

                    <div id="recommendation-empty" class="hidden text-center mb-6 md:mb-6">
                        <div class="card inline-block p-8 max-w-md">
                            <div class="w-14 h-14 bg-primary-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-7 h-7 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold mb-2 card-title text-dark-800">Belum ada rekomendasi</h3>
                            <p class="card-desc mb-4 text-sm text-dark-800 font-medium">
                                Pilih destinasi atau aktifkan lokasi Anda.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const places = {{ Illuminate\Support\Js::from($places) }};
            const root = document.getElementById('location-recommendation-page');

            if (!root || !places.length) {
                return;
            }

            const state = {
                mode: 'selected-place',
                selectedPlaceId: Number(root.dataset.initialPlaceId || places[0].id),
                currentLocation: null,
            };

            const destinationToggle = document.getElementById('destination-toggle');
            const destinationPanel = document.getElementById('destination-panel');
            const destinationSearch = document.getElementById('destination-search');
            const destinationList = document.getElementById('destination-list');
            const destinationLabel = document.getElementById('destination-label');
            const destinationCount = document.getElementById('destination-count');
            const selectedModeButton = document.getElementById('selected-place-mode');
            const currentLocationButton = document.getElementById('current-location-mode');
            const statusBox = document.getElementById('location-status');
            const originLatitude = document.getElementById('origin-latitude');
            const originLongitude = document.getElementById('origin-longitude');
            const originSource = document.getElementById('origin-source');
            const originTitle = document.getElementById('origin-title');
            const originDescription = document.getElementById('origin-description');
            const recommendationCount = document.getElementById('recommendation-count');
            const recommendationGrid = document.getElementById('recommendation-grid');
            const recommendationEmpty = document.getElementById('recommendation-empty');
            const placeUrl = @json(url('/places'));

            function escapeHtml(value) {
                return String(value ?? '').replace(/[&<>"']/g, (character) => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;',
                }[character]));
            }

            function normalizeText(value) {
                return String(value ?? '')
                    .toLowerCase()
                    .normalize('NFKD')
                    .replace(/[\u0300-\u036f]/g, '');
            }

            function toRadians(value) {
                return value * Math.PI / 180;
            }

            function haversineDistanceKm(start, end) {
                const startLat = toRadians(start.latitude);
                const endLat = toRadians(end.latitude);
                const deltaLat = toRadians(end.latitude - start.latitude);
                const deltaLng = toRadians(end.longitude - start.longitude);
                const a = Math.sin(deltaLat / 2) ** 2
                    + Math.cos(startLat) * Math.cos(endLat) * Math.sin(deltaLng / 2) ** 2;

                return 6371.0088 * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            }

            function formatDistanceKm(distanceKm) {
                if (distanceKm < 1) {
                    return `${Math.round(distanceKm * 1000).toLocaleString('id-ID')} m`;
                }

                return `${distanceKm.toLocaleString('id-ID', {
                    maximumFractionDigits: distanceKm < 10 ? 1 : 0,
                })} km`;
            }

            function selectedPlace() {
                return places.find((place) => Number(place.id) === Number(state.selectedPlaceId)) ?? places[0];
            }

            function setStatus(message, tone = 'info') {
                const toneClasses = {
                    info: 'info-box p-3 text-xs md:text-sm card-desc',
                    success: 'p-3 text-xs md:text-sm rounded-lg border border-bogor-green-500/30 bg-bogor-green-500/10 text-bogor-green-400 font-semibold',
                    error: 'p-3 text-xs md:text-sm rounded-lg border border-bogor-red-500/30 bg-bogor-red-500/10 text-bogor-red-400 font-semibold',
                };

                statusBox.className = toneClasses[tone] ?? toneClasses.info;
                statusBox.textContent = message;
            }

            function updateModeButtons() {
                selectedModeButton.className = state.mode === 'selected-place'
                    ? 'btn-primary px-3 py-2.5 text-xs md:text-sm'
                    : 'btn-secondary px-3 py-2.5 text-xs md:text-sm';

                currentLocationButton.className = state.mode === 'current-location'
                    ? 'btn-primary px-3 py-2.5 text-xs md:text-sm'
                    : 'btn-secondary px-3 py-2.5 text-xs md:text-sm';
            }

            function closeDestinationPanel() {
                destinationPanel.classList.add('hidden');
                destinationToggle.setAttribute('aria-expanded', 'false');
            }

            function renderDestinationList() {
                const query = normalizeText(destinationSearch.value.trim());
                const filteredPlaces = query
                    ? places.filter((place) => normalizeText(`${place.nama} ${place.kategori ?? ''}`).includes(query))
                    : places;

                destinationList.innerHTML = filteredPlaces.map((place) => {
                    const selected = Number(place.id) === Number(state.selectedPlaceId);

                    return `
                        <button type="button"
                            class="destination-option ${selected ? 'destination-option-selected' : ''} w-full flex items-center justify-between gap-3 px-3 py-2 rounded-lg text-left text-xs md:text-sm font-semibold"
                            data-place-id="${place.id}">
                            <span class="min-w-0 truncate">${escapeHtml(place.nama)}</span>
                            <span class="text-[10px] opacity-80">${escapeHtml(place.kategori ?? 'Wisata')}</span>
                        </button>
                    `;
                }).join('');

                destinationCount.textContent = `${filteredPlaces.length.toLocaleString('id-ID')} destinasi tersedia`;
            }

            function getNearbyPlaces(origin, excludePlaceId = null) {
                return places
                    .filter((place) => Number(place.id) !== Number(excludePlaceId))
                    .map((place) => ({
                        ...place,
                        distanceKm: haversineDistanceKm(origin, place),
                    }))
                    .sort((a, b) => {
                        if (a.distanceKm !== b.distanceKm) {
                            return a.distanceKm - b.distanceKm;
                        }

                        return Number(b.likes ?? 0) - Number(a.likes ?? 0);
                    })
                    .slice(0, 9);
            }

            function recommendationCard(place, index) {
                const imageUrl = place.url_gambar
                    ? escapeHtml(place.url_gambar)
                    : `https://via.placeholder.com/800x600/1e293b/3b82f6?text=${encodeURIComponent(place.nama)}`;
                const mapsQuery = encodeURIComponent(`${place.latitude},${place.longitude}`);

                return `
                    <article class="recommendation-card group card card-hover overflow-hidden !p-3 md:!p-6 flex flex-col w-[calc(50%-4px)] md:w-[calc(50%-6px)] lg:w-[calc(33.333%-11px)]">
                        <a href="${placeUrl}/${place.id}" class="block flex-1 min-w-0">
                            <div class="relative h-28 sm:h-36 md:h-44 lg:h-52 img-container rounded-lg md:rounded-xl overflow-hidden mb-4 md:mb-6">
                                <img src="${imageUrl}" alt="${escapeHtml(place.nama)}" class="w-full h-full object-cover" loading="lazy"
                                    onerror="this.src='https://via.placeholder.com/800x600/1e293b/3b82f6?text=${encodeURIComponent(place.nama)}'">
                                <div class="img-overlay"></div>

                                <div class="absolute top-1.5 md:top-3 left-1.5 md:left-3">
                                    <span class="px-1.5 md:px-3 py-0.5 md:py-1 bg-bogor-blue-500 text-white text-[10px] md:text-xs font-semibold rounded md:rounded-lg shadow">
                                        ${escapeHtml(place.kategori ?? 'Wisata')}
                                    </span>
                                </div>

                                <div class="absolute top-1.5 md:top-3 right-1.5 md:right-3">
                                    <span class="inline-flex items-center px-1.5 md:px-2.5 py-0.5 md:py-1 bg-white text-dark-800 text-[10px] md:text-xs font-semibold rounded md:rounded-lg shadow">
                                        #${index + 1}
                                    </span>
                                </div>

                                <div class="absolute bottom-1.5 md:bottom-3 left-1.5 md:left-3">
                                    <span class="inline-flex items-center px-1.5 md:px-2.5 py-0.5 md:py-1 bg-bogor-green-500 text-white text-[10px] md:text-xs font-semibold rounded md:rounded-lg shadow">
                                        ${formatDistanceKm(place.distanceKm)}
                                    </span>
                                </div>
                            </div>

                            <div class="p-1 md:p-0 flex flex-col flex-grow min-w-0">
                                <h3 class="recommendation-title text-xs sm:text-sm md:text-base lg:text-lg hero-title-accent font-bold mb-1 md:mb-2 card-title group-hover:text-bogor-blue-500 transition-colors recommendation-line-clamp-2 min-h-[2rem] md:min-h-[2.75rem]">
                                    ${escapeHtml(place.nama)}
                                </h3>

                                <div class="recommendation-address mb-2 min-h-[2.15rem] md:min-h-[2.5rem]">
                                    <p class="card-desc text-[10px] md:text-xs flex items-start gap-1.5 leading-snug text-gray-600">
                                        <svg class="w-2.5 h-2.5 md:w-3.5 md:h-3.5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        </svg>
                                        <span class="min-w-0 recommendation-line-clamp-2">
                                            ${escapeHtml(place.alamat ?? 'Bogor, Jawa Barat')}
                                        </span>
                                    </p>
                                </div>

                                <div class="recommendation-description hidden md:block min-h-[4.7rem] mb-3 overflow-hidden">
                                    <p class="card-desc text-xs md:text-sm recommendation-line-clamp-3 leading-relaxed text-gray-600 text-left">
                                        ${escapeHtml(place.deskripsi ?? 'Detail destinasi wisata Bogor.')}
                                    </p>
                                </div>
                            </div>
                        </a>

                        <div class="recommendation-footer flex min-h-8 items-center justify-between gap-2 pt-1.5 md:pt-3 border-t border-slate-200 mt-auto">
                            <span class="text-[10px] md:text-xs stat-label truncate max-w-[60%]">${escapeHtml(place.coordinate_source)}</span>
                            <a href="https://www.google.com/maps/search/?api=1&query=${mapsQuery}" target="_blank"
                                class="inline-flex shrink-0 items-center text-primary-500 text-[10px] md:text-sm font-bold hover:translate-x-1 transition-transform">
                                Maps
                                <svg class="w-2.5 h-2.5 md:w-4 md:h-4 ml-0.5 md:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </a>
                        </div>
                    </article>
                `;
            }

            function renderRecommendations(origin, label, source, excludePlaceId = null) {
                const recommendations = getNearbyPlaces(origin, excludePlaceId);

                updateModeButtons();
                originLatitude.textContent = Number(origin.latitude).toFixed(6);
                originLongitude.textContent = Number(origin.longitude).toFixed(6);
                originSource.textContent = source;
                originTitle.textContent = label;
                originDescription.textContent = recommendations.length
                    ? `Menampilkan ${recommendations.length} wisata terdekat dari titik awal ini.`
                    : 'Belum ada destinasi yang bisa dihitung dari titik awal ini.';
                recommendationCount.textContent = recommendations.length;

                recommendationGrid.innerHTML = recommendations.map(recommendationCard).join('');
                recommendationEmpty.classList.toggle('hidden', recommendations.length > 0);

                if (window.AOS) {
                    window.AOS.refresh();
                }
            }

            function useSelectedPlace() {
                const place = selectedPlace();

                state.mode = 'selected-place';
                destinationLabel.textContent = place.nama;
                setStatus('Rekomendasi dihitung dari destinasi pilihan.', 'info');
                renderRecommendations(place, `Dari ${place.nama}`, place.coordinate_source, place.id);
            }

            function requestCurrentLocation() {
                state.mode = 'current-location';
                updateModeButtons();

                if (!navigator.geolocation) {
                    setStatus('Browser ini belum mendukung akses lokasi.', 'error');
                    useSelectedPlace();
                    return;
                }

                setStatus('Meminta akses lokasi perangkat...', 'info');
                currentLocationButton.setAttribute('disabled', 'disabled');

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        currentLocationButton.removeAttribute('disabled');
                        state.currentLocation = {
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude,
                        };

                        setStatus('Lokasi perangkat aktif.', 'success');
                        renderRecommendations(state.currentLocation, 'Dari Lokasi Saya', 'GPS browser');
                    },
                    (error) => {
                        currentLocationButton.removeAttribute('disabled');
                        const message = error.code === error.PERMISSION_DENIED
                            ? 'Akses lokasi ditolak. Aktifkan izin lokasi di browser untuk memakai mode ini.'
                            : 'Lokasi belum bisa dibaca. Coba ulangi beberapa saat lagi.';

                        setStatus(message, 'error');
                        useSelectedPlace();
                    },
                    {
                        enableHighAccuracy: true,
                        maximumAge: 60000,
                        timeout: 12000,
                    },
                );
            }

            destinationToggle.addEventListener('click', () => {
                const isOpen = !destinationPanel.classList.contains('hidden');
                destinationPanel.classList.toggle('hidden', isOpen);
                destinationToggle.setAttribute('aria-expanded', String(!isOpen));

                if (!isOpen) {
                    destinationSearch.focus();
                    renderDestinationList();
                }
            });

            document.addEventListener('mousedown', (event) => {
                if (!document.getElementById('destination-picker').contains(event.target)) {
                    closeDestinationPanel();
                }
            });

            destinationSearch.addEventListener('input', renderDestinationList);

            destinationList.addEventListener('click', (event) => {
                const button = event.target.closest('[data-place-id]');

                if (!button) {
                    return;
                }

                state.selectedPlaceId = Number(button.dataset.placeId);
                destinationSearch.value = '';
                closeDestinationPanel();
                useSelectedPlace();
                renderDestinationList();
            });

            selectedModeButton.addEventListener('click', useSelectedPlace);
            currentLocationButton.addEventListener('click', requestCurrentLocation);

            renderDestinationList();
            useSelectedPlace();
        });
    </script>
@endpush

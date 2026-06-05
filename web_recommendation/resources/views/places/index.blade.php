@extends('layouts.app')

@section('title', 'Daftar Wisata Bogor - Destinasi Wisata Terbaik | BogorXplore')

@section('meta_description', 'Jelajahi destinasi wisata terbaik di Bogor. Temukan air terjun, curug, taman, kebun raya, wisata alam, dan tempat wisata keluarga dengan filter kategori dan pencarian.')

@section('meta_keywords', 'daftar wisata bogor, tempat wisata bogor, destinasi bogor, air terjun bogor, curug bogor, taman bogor, wisata alam bogor, wisata keluarga bogor, liburan bogor')

@section('og_title', 'Daftar Wisata Bogor - Destinasi Terbaik')
@section('og_description', 'Jelajahi semua destinasi wisata di Bogor. Filter berdasarkan kategori dan temukan tempat wisata favorit Anda.')

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
                "name": "Daftar Wisata Bogor",
                "description": "Koleksi lengkap destinasi wisata terbaik di Bogor",
                "url": "{{ url('/places') }}",
                "isPartOf": {
                    "@@type": "WebSite",
                    "name": "BogorXplore",
                    "url": "{{ url('/') }}"
                },
                "about": {
                    "@@type": "Thing",
                    "name": "Wisata Bogor",
                    "description": "Destinasi wisata di Kota Bogor, Jawa Barat, Indonesia"
                }
            }
            </script>
@endsection

@section('content')
    <!-- Page Header -->
    <section class="pt-20 md:pt-24 pb-6 md:pb-10 relative overflow-hidden">
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

        <div class="container mx-auto px- lg:px-8 relative z-10 pt-6 md:pt-6 mb-6 md:mb-6">
            <div class="text-center max-w-3xl mx-auto">
                <h1 class="text-xl md:text-3xl lg:text-4xl font-bold mb-2 md:mb-4 section-heading" data-aos="fade-up">
                    Daftar Wisata <span class="hero-title-accent mt-2">Bogor</span>
                </h1>
                <p class="section-subtitle text-sm md:text-base" data-aos="fade-up" data-aos-delay="100">
                    Jelajahi destinasi wisata menakjubkan di Bogor
                </p>
            </div>
        </div>
    </section>

    <!-- Filters & Places Grid -->
    <section class="py-4 md:py-8">
        <div class="container mx-auto px-4 lg:px-8">
            @livewire('places-list')
        </div>
    </section>

@endsection
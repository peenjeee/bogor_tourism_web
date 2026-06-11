<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Primary Meta Tags -->
    <title>@yield('title', 'BogorXplore - Jelajahi Destinasi Wisata Bogor')</title>
    <meta name="title" content="@yield('meta_title', 'BogorXplore - Jelajahi Destinasi Wisata Terbaik di Bogor')">
    <meta name="description" content="@yield('meta_description', 'BogorXplore adalah platform rekomendasi wisata Bogor dengan destinasi wisata. Temukan air terjun, taman, kebun, arena, dan tempat wisata menarik di Bogor.')">
    <meta name="keywords" content="@yield('meta_keywords', 'wisata bogor, tempat wisata bogor, rekomendasi wisata bogor, air terjun bogor, taman bogor, curug bogor, kebun raya bogor, destinasi bogor, liburan bogor, travel bogor, BogorXplore, wisata alam bogor, wisata keluarga bogor, objek wisata bogor')">
    <meta name="author" content="BogorXplore">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <meta name="bingbot" content="index, follow">
    <meta name="language" content="Indonesian">
    <meta name="revisit-after" content="1 days">
    <meta name="rating" content="general">
    <meta name="distribution" content="global">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">
    
    <!-- Alternate Languages -->
    <link rel="alternate" hreflang="id" href="{{ url()->current() }}">
    <link rel="alternate" hreflang="x-default" href="{{ url()->current() }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/logo.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logo.svg') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/logo.svg') }}">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('og_title', 'BogorXplore - Jelajahi Destinasi Wisata Terbaik di Bogor')">
    <meta property="og:description" content="@yield('og_description', 'Temukan destinasi wisata terbaik di Bogor. Air terjun, taman, kebun, dan tempat wisata menarik lainnya.')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-image.jpg'))">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="BogorXplore - Platform Wisata Bogor">
    <meta property="og:site_name" content="BogorXplore">
    <meta property="og:locale" content="id_ID">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="@yield('twitter_title', 'BogorXplore - Jelajahi Destinasi Wisata Terbaik di Bogor')">
    <meta name="twitter:description" content="@yield('twitter_description', 'Temukan destinasi wisata terbaik di Bogor dengan rekomendasi AI.')">
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/og-image.jpg'))">
    <meta name="twitter:image:alt" content="BogorXplore - Platform Wisata Bogor">
    
    <!-- Geo Tags for Local SEO -->
    <meta name="geo.region" content="ID-JB">
    <meta name="geo.placename" content="Bogor, Jawa Barat, Indonesia">
    <meta name="geo.position" content="-6.5971;106.8060">
    <meta name="ICBM" content="-6.5971, 106.8060">
    
    <!-- Dublin Core Metadata -->
    <meta name="DC.title" content="@yield('title', 'BogorXplore - Jelajahi Destinasi Wisata Bogor')">
    <meta name="DC.creator" content="BogorXplore">
    <meta name="DC.subject" content="Wisata Bogor, Pariwisata, Destinasi Wisata">
    <meta name="DC.description" content="@yield('meta_description', 'Platform rekomendasi wisata Bogor')">
    <meta name="DC.language" content="id">
    
    <!-- JSON-LD Structured Data - Website -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "WebSite",
        "name": "BogorXplore",
        "alternateName": ["Bogor Xplore", "Bogor Explorer", "Wisata Bogor"],
        "url": "{{ url('/') }}",
        "description": "Platform rekomendasi wisata Bogor dengan destinasi wisata terbaik. Temukan air terjun, taman, kebun raya, dan tempat wisata menarik di Bogor.",
        "inLanguage": "id-ID",
        "potentialAction": {
            "@@type": "SearchAction",
            "target": {
                "@@type": "EntryPoint",
                "urlTemplate": "{{ url('/places') }}?search={search_term_string}"
            },
            "query-input": "required name=search_term_string"
        }
    }
    </script>
    
    <!-- JSON-LD Structured Data - Organization -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "Organization",
        "name": "BogorXplore",
        "url": "{{ url('/') }}",
        "logo": "{{ asset('images/logo.svg') }}",
        "description": "Platform rekomendasi destinasi wisata terbaik di Kota Bogor",
        "address": {
            "@@type": "PostalAddress",
            "addressLocality": "Bogor",
            "addressRegion": "Jawa Barat",
            "addressCountry": "ID"
        },
        "areaServed": {
            "@@type": "City",
            "name": "Bogor",
            "@@id": "https://www.wikidata.org/wiki/Q3355"
        },
        "sameAs": []
    }
    </script>
    
    <!-- JSON-LD Structured Data - BreadcrumbList -->
    @yield('breadcrumb_schema')
    
    @yield('structured_data')

    <!-- Resource Hints for Performance -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="dns-prefetch" href="//unpkg.com">
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
    
    <!-- Preconnect to Critical Origins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Preload Critical Fonts -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap"></noscript>

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
    @livewireStyles
</head>


<body class="antialiased bg-grid">
    <!-- Navbar -->
    <nav class="navbar-glass transition-all duration-300">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between h-16 md:h-20">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center space-x-2 group">
                    <img src="{{ asset('images/logo.svg') }}" alt="BogorXplore" class="w-8 h-8 md:w-9 md:h-9 object-contain">
                    <span class="text-base md:text-lg font-bold nav-logo">Bogor<span class="hero-title-accent">Xplore</span></span>
                </a>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('home') }}" class="nav-link font-medium relative group">
                        Beranda
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-primary-500 group-hover:w-full transition-all duration-300"></span>
                    </a>
                    <a href="{{ route('places.index') }}" class="nav-link font-medium relative group">
                        Daftar Wisata
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-primary-500 group-hover:w-full transition-all duration-300"></span>
                    </a>
                    <a href="{{ route('recommendations.location') }}" class="nav-link font-medium relative group">
                        Rekomendasi Lokasi
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-primary-500 group-hover:w-full transition-all duration-300"></span>
                    </a>
                    
                    <!-- Search Inline -->
                    <form id="navbar-search-form" method="GET" action="{{ route('places.index') }}" class="search-container flex items-center gap-2">
                        <input type="text" 
                               id="navbar-search-input"
                               name="search" 
                               placeholder="Cari wisata..."
                               class="search-input-inline focus:outline-none">
                        <button type="submit" class="btn-primary py-2 px-4 flex items-center">
                            <svg class="w-4 h-4 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <span class="hidden lg:inline">Jelajahi</span>
                        </button>
                    </form>
                    
                    <!-- Theme Toggle -->
                    <!-- <button id="theme-toggle-desktop" class="theme-toggle" title="Toggle Theme">
                        <svg class="w-5 h-5 sun-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <svg class="w-5 h-5 moon-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button> -->
                </div>

                <!-- Mobile Navigation -->
                <div class="flex items-center gap-2 md:hidden">
                    <!-- Mobile Search Button - Always visible -->
                    <!-- <a href="{{ route('places.index') }}" class="p-2 rounded-xl bg-primary-500 text-white shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </a> -->
                    
                    <!-- Mobile Theme Toggle -->
                    <!-- <button id="theme-toggle-mobile" class="theme-toggle">
                        <svg class="w-5 h-5 sun-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <svg class="w-5 h-5 moon-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button> -->
                    
                    <!-- Mobile Menu Toggle -->
                    <button id="mobile-menu-toggle" type="button"
                            class="p-2 rounded-xl bg-white/10 nav-link hover:bg-white/20 transition-colors" 
                            onclick="toggleMobileMenu()" aria-label="Toggle menu" aria-controls="mobile-menu" aria-expanded="false">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-white/10">
            <div class="container mx-auto px-4 py-4 space-y-3">
                <a href="{{ route('home') }}" class="block nav-link font-medium py-2">
                    Beranda
                </a>
                <a href="{{ route('places.index') }}" class="block nav-link font-medium py-2">
                    Daftar Wisata
                </a>
                <a href="{{ route('recommendations.location') }}" class="block nav-link font-medium py-2">
                    Rekomendasi Lokasi
                </a>
                <!-- Mobile Search in Menu -->
                <form id="mobile-search-form" method="GET" action="{{ route('places.index') }}" class="pt-2">
                    <div class="flex gap-2">
                        <input type="text" 
                               id="mobile-search-input"
                               name="search" 
                               placeholder="Cari destinasi..."
                               class="flex-1 px-4 py-3 input-modern text-sm">
                        <button type="submit" class="px-4 py-3 bg-primary-500 text-white rounded-xl font-medium">
                            Cari
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer-section">
        <div class="container mx-auto px-4 lg:px-8 py-8 md:py-10">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 md:gap-6">
                <!-- Brand (Left) -->
                <div class="flex-1">
                    <div class="flex items-center space-x-2 mb-2 md:mb-3">
                        <img src="{{ asset('images/logo.svg') }}" alt="BogorXplore" class="w-8 h-8 md:w-9 md:h-9 object-contain">
                        <span class="text-base md:text-lg font-bold nav-logo">Bogor<span class="hero-title-accent">Xplore</span></span>
                    </div>
                    <p class="footer-text text-xs md:text-sm max-w-sm">
                        Jelajahi destinasi wisata terbaik di Kota Bogor dengan berbagai rekomendasi wisata.
                    </p>
                </div>

                <!-- Menu (Right Corner) -->
                <div class="flex items-center gap-4 md:gap-6 text-xs md:text-sm">
                    <a href="{{ route('home') }}" class="footer-link">
                        Beranda
                    </a>
                    <a href="{{ route('places.index') }}" class="footer-link">
                        Daftar Wisata
                    </a>
                    <a href="{{ route('recommendations.location') }}" class="footer-link">
                        Rekomendasi Lokasi
                    </a>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-white/10 mt-4 md:mt-6 pt-4 md:pt-6 text-center text-xs footer-text">
                <p>&copy; {{ date('Y') }} Bogor<span class="hero-title-accent">Xplore</span>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile Menu Toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const toggle = document.getElementById('mobile-menu-toggle');
            const isOpen = menu.classList.toggle('hidden') === false;

            document.body.classList.toggle('mobile-menu-open', isOpen);
            toggle?.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        }

        function closeMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const toggle = document.getElementById('mobile-menu-toggle');

            menu?.classList.add('hidden');
            document.body.classList.remove('mobile-menu-open');
            toggle?.setAttribute('aria-expanded', 'false');
        }

        window.addEventListener('resize', function () {
            if (window.innerWidth >= 768) {
                closeMobileMenu();
            }
        });

        // Theme Toggle Function
        // function toggleTheme() {
        //     const body = document.body;
        //     const isLight = body.classList.toggle('light-mode');
            
        //     // Update all sun/moon icons
        //     document.querySelectorAll('.sun-icon').forEach(icon => {
        //         icon.classList.toggle('hidden', isLight);
        //     });
        //     document.querySelectorAll('.moon-icon').forEach(icon => {
        //         icon.classList.toggle('hidden', !isLight);
        //     });
            
        //     // Save preference
        //     localStorage.setItem('theme', isLight ? 'light' : 'dark');
        // }

        // // Initialize theme on page load
        // document.addEventListener('DOMContentLoaded', function() {
        //     const savedTheme = localStorage.getItem('theme');
        //     const isLight = savedTheme === 'light';
            
        //     if (isLight) {
        //         document.body.classList.add('light-mode');
        //     }
            
        //     // Update icons based on saved theme
        //     document.querySelectorAll('.sun-icon').forEach(icon => {
        //         icon.classList.toggle('hidden', isLight);
        //     });
        //     document.querySelectorAll('.moon-icon').forEach(icon => {
        //         icon.classList.toggle('hidden', !isLight);
        //     });
            
        //     // Attach event listeners to theme toggle buttons
        //     document.getElementById('theme-toggle-desktop')?.addEventListener('click', toggleTheme);
        //     document.getElementById('theme-toggle-mobile')?.addEventListener('click', toggleTheme);
        // });
    </script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @livewireScripts
    
    <!-- SweetAlert2 Toast Configuration -->
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });
        
        // Handle navbar search form submission
        document.addEventListener('DOMContentLoaded', () => {
            // Desktop search form
            const searchForm = document.getElementById('navbar-search-form');
            const searchInput = document.getElementById('navbar-search-input');
            
            if (searchForm && searchInput) {
                searchForm.addEventListener('submit', function(e) {
                    const searchValue = searchInput.value.trim();
                    
                    if (searchValue) {
                        // Show loading toast
                        Toast.fire({
                            icon: 'info',
                            title: `Mencari "${searchValue}"...`
                        });
                    } else {
                        // Show error toast if empty search
                        e.preventDefault();
                        Toast.fire({
                            icon: 'error',
                            title: 'Masukkan kata kunci pencarian!'
                        });
                    }
                });
            }
            
            // Mobile search form
            const mobileSearchForm = document.getElementById('mobile-search-form');
            const mobileSearchInput = document.getElementById('mobile-search-input');
            
            if (mobileSearchForm && mobileSearchInput) {
                mobileSearchForm.addEventListener('submit', function(e) {
                    const searchValue = mobileSearchInput.value.trim();
                    
                    if (searchValue) {
                        // Show loading toast
                        Toast.fire({
                            icon: 'info',
                            title: `Mencari "${searchValue}"...`
                        });
                    } else {
                        // Show error toast if empty search
                        e.preventDefault();
                        Toast.fire({
                            icon: 'error',
                            title: 'Masukkan kata kunci pencarian!'
                        });
                    }
                });
            }
            
            // Check for search results on places page
            const urlParams = new URLSearchParams(window.location.search);
            const searchQuery = urlParams.get('search');
            
            if (searchQuery && window.location.pathname.includes('places')) {
                // Get count from page if available
                const countElement = document.querySelector('[data-places-count]');
                const resultsText = document.querySelector('.stat-label .px-2');
                
                if (resultsText) {
                    const count = resultsText.textContent.match(/\d+/);
                    if (count && parseInt(count[0]) > 0) {
                        Toast.fire({
                            icon: 'success',
                            title: `Ditemukan ${count[0]} destinasi wisata`
                        });
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: `Tidak ada hasil untuk "${searchQuery}"`
                        });
                    }
                }
            }
        });
    </script>
    @stack('scripts')
</body>

</html>

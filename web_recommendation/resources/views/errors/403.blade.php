@extends('layouts.app')

@section('title', '403 - Akses Ditolak')

@section('content')
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden pt-16 md:pt-20">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center max-w-2xl mx-auto">
                <!-- Title -->
                <h1 class="text-xl md:text-3xl lg:text-4xl font-bold mb-2 md:mb-4 text-dark-950">
                    Akses <span class="text-bogor-gold-500">Ditolak</span>
                </h1>
                <p class="text-base md:text-xl lg:text-2xl font-semibold text-dark-950 mb-8">
                    Error <span class="text-bogor-gold-500">403</span>
                </p>

                <!-- Icon -->
                <div class="w-16 h-16 mx-auto mb-6 bg-bogor-gold-500/10 rounded-xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-bogor-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>

                <!-- Description -->
                <p class="text-gray-600 text-sm md:text-base mb-8 leading-relaxed">
                    Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.<br>
                    Silakan kembali ke halaman utama atau hubungi administrator.
                </p>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="{{ route('home') }}" class="btn-primary text-sm md:text-base px-5 py-2.5 w-full sm:w-auto">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Kembali ke Beranda
                    </a>
                    <a href="{{ route('places.index') }}"
                        class="text-sm md:text-base px-5 py-2.5 w-full sm:w-auto bg-white border border-gray-300 rounded-xl font-medium text-gray-700 hover:bg-gray-50 transition-colors inline-flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Jelajahi Wisata
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
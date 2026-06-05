@extends('layouts.app')

@section('title', '500 - Kesalahan Server')

@section('content')
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden pt-16 md:pt-20">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center max-w-2xl mx-auto">
                <!-- Title -->
                <h1 class="text-xl md:text-3xl lg:text-4xl font-bold mb-2 md:mb-4 text-dark-950">
                    Kesalahan <span class="text-bogor-red-500">Server</span>
                </h1>
                <p class="text-base md:text-xl lg:text-2xl font-semibold text-dark-950 mb-8">
                    Error <span class="text-bogor-red-500">500</span>
                </p>

                <!-- Icon -->
                <div class="w-16 h-16 mx-auto mb-6 bg-bogor-red-500/10 rounded-xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-bogor-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>

                <!-- Description -->
                <p class="text-gray-600 text-sm md:text-base mb-8 leading-relaxed">
                    Maaf, terjadi kesalahan pada server kami.<br>
                    Tim kami sedang bekerja untuk memperbaikinya. Silakan coba lagi.
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
                    <button onclick="location.reload()"
                        class="text-sm md:text-base px-5 py-2.5 w-full sm:w-auto bg-white border border-gray-300 rounded-xl font-medium text-gray-700 hover:bg-gray-50 transition-colors inline-flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Coba Lagi
                    </button>
                </div>
            </div>
        </div>
    </section>
@endsection
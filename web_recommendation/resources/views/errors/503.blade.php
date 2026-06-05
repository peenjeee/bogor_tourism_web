@extends('layouts.app')

@section('title', '503 - Layanan Tidak Tersedia')

@section('content')
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden pt-16 md:pt-20">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center max-w-2xl mx-auto">
                <!-- Title -->
                <h1 class="text-xl md:text-3xl lg:text-4xl font-bold mb-2 md:mb-4 text-dark-950">
                    Dalam <span class="text-bogor-gold-500">Pemeliharaan</span>
                </h1>
                <p class="text-base md:text-xl lg:text-2xl font-semibold text-dark-950 mb-8">
                    Error <span class="text-bogor-gold-500">503</span>
                </p>

                <!-- Icon -->
                <div class="w-16 h-16 mx-auto mb-6 bg-bogor-gold-500/10 rounded-xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-bogor-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>

                <!-- Description -->
                <p class="text-gray-600 text-sm md:text-base mb-8 leading-relaxed">
                    Situs sedang dalam pemeliharaan untuk pengalaman yang lebih baik.<br>
                    Kami akan segera kembali. Terima kasih atas kesabaran Anda.
                </p>

                <!-- Actions -->
                <div class="flex items-center justify-center">
                    <button onclick="location.reload()" class="btn-primary text-sm md:text-base px-5 py-2.5">
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
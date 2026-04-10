<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $seoSettings['seo_title'] ?? ($landingPage->meta_title ?? config('app.name', 'Travel Agent') . ' — Premium Tour & Travel Indonesia') }}</title>
    <meta name="description" content="{{ $seoSettings['seo_description'] ?? ($landingPage->meta_description ?? 'Agen perjalanan terbaik untuk wisata domestik dan internasional: Bali, Singapore, Malaysia, Thailand dan lebih banyak lagi.') }}">
    <meta name="keywords" content="{{ $seoSettings['seo_keywords'] ?? ($landingPage->meta_keywords ?? 'travel, tour, liburan, wisata') }}">
    
    @if(!empty($seoSettings['seo_favicon']))
    <link rel="shortcut icon" href="{{ Storage::url($seoSettings['seo_favicon']) }}" type="image/x-icon">
    @endif

    <!-- Open Graph -->
    <meta property="og:title" content="{{ $seoSettings['seo_title'] ?? ($landingPage->meta_title ?? config('app.name')) }}">
    <meta property="og:description" content="{{ $seoSettings['seo_description'] ?? ($landingPage->meta_description ?? 'Agen perjalanan terbaik.') }}">
    @if(!empty($seoSettings['seo_og_image']))
    <meta property="og:image" content="{{ Storage::url($seoSettings['seo_og_image']) }}">
    @endif
    <meta property="og:type" content="website">

    <!-- Google Tag Manager -->
    @php
        $gtmId = isset($landingPage) && $landingPage->gtm_id ? $landingPage->gtm_id : env("GTM_ID","XXXXXXX");
    @endphp
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{{ $gtmId }}');</script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind Assets (Vite + Fallback CDN for absolute stability) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        primary: '#9B8EC7',
                        secondary: '#F45B26',
                        accent: '#FAE251',
                    },
                    animation: {
                        'bounce-slow': 'bounce 3s infinite',
                    },
                    borderRadius: {
                        '4xl': '2rem',
                        '5xl': '2.5rem',
                    }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* ===== AVRA Tour Brand Colors ===== */
        :root {
            --avra-primary:   #9B8EC7;
            --avra-primary2:  #BDA6CE;
            --avra-secondary: #F45B26;
            --avra-secondary2:#FAE251;
        }

        /* Tab buttons (modal) */
        .tab-btn.active { border-color: var(--avra-primary); background:#f4f2ff; color: var(--avra-primary); }

        /* ===== Modal Styles ===== */
        .modal-overlay {
            position: fixed; inset: 0; z-index: 9999;
            display: flex; align-items: center; justify-content: center;
            padding: 1rem; background: rgba(17,24,39,0.85); backdrop-filter: blur(8px);
        }
        .modal-panel {
            background: #fff; border-radius: 1.5rem; width: 100%; max-width: 68rem;
            max-height: 90vh; display: flex; flex-direction: column; overflow: hidden;
            box-shadow: 0 25px 60px rgba(0,0,0,0.35);
        }
        @media (min-width: 768px) {
            .modal-panel { flex-direction: row-reverse; }
            .modal-side { width: 38%; display: flex; flex-direction: column; position: relative; }
            .modal-main { width: 62%; display: flex; flex-direction: column; }
        }
        .modal-img { position: relative; height: 18rem; flex-shrink: 0; background: #000; }
        @media (min-width: 768px) { .modal-img { position: absolute; inset: 0; height: 100%; z-index: 0; } }
        .modal-img img { width:100%; height:100%; object-fit:cover; opacity: 0.85; }
        .modal-img-overlay {
            position:absolute; inset:0;
            background: linear-gradient(to top, rgba(10,15,30,0.9) 0%, transparent 60%);
        }
        .modal-img-content {
            position:absolute; bottom:0; left:0; right:0; padding:1.5rem 2rem;
        }
        .modal-img-content h3 { font-size:1.875rem; font-weight:800; color:#fff; margin:0 0 .25rem; }
        .modal-img-content p  { color:rgba(255,255,255,.75); font-weight:500; margin:0; }
        .modal-body { padding:1.5rem 2rem; overflow-y:auto; flex:1; }
        .modal-footer {
            padding:1.5rem 2rem; border-top:1px solid #f3f4f6;
            background:#fff; display:flex; justify-content:flex-end; gap:.75rem; flex-shrink:0;
        }
        .tab-btn {
            display:inline-flex; align-items:center; gap:.4rem;
            padding:.5rem 1rem; border-radius:9999px; font-size:.875rem; font-weight:600;
            cursor:pointer; border:2px solid #e5e7eb; background:#fff; color:#6b7280;
            transition:all .2s;
        }
        .itinerary-item {
            display:flex; gap:1rem; padding:.75rem 0; border-bottom:1px solid #f3f4f6;
        }
        .itinerary-day {
            flex-shrink:0; width:2.5rem; height:2.5rem; border-radius:.75rem;
            background:#f4f2ff; color: var(--avra-primary); display:flex; align-items:center;
            justify-content:center; font-weight:800; font-size:.875rem;
        }
        .accomm-item {
            display:flex; align-items:flex-start; gap:.75rem; padding:.625rem 0;
            border-bottom:1px dashed #f3f4f6;
        }
        .accomm-icon {
            flex-shrink:0; width:2rem; height:2rem; border-radius:.5rem;
            background:#f0fdf4; color:#16a34a; display:flex; align-items:center;
            justify-content:center;
        }
        /* Card hover effect */
        .trip-card { transition: transform .3s, box-shadow .3s; }
        .trip-card:hover { transform:translateY(-6px); box-shadow:0 24px 48px rgba(155,142,199,.18); }
        .trip-card img { transition: transform .5s ease; }
        .trip-card:hover img { transform: scale(1.08); }

        /* Gallery grid standard ratio */
        .gallery-item { position: relative; overflow: hidden; border-radius: 1rem; background: #e5e7eb; }
        .gallery-item::before { content: ''; display: block; padding-top: 75%; /* 4:3 */ }
        .gallery-item img {
            position: absolute; inset: 0; width: 100%; height: 100%;
            object-fit: cover; transition: transform .4s ease;
        }
        .gallery-item:hover img { transform: scale(1.07); }
    </style>
</head>
<body class="antialiased bg-gray-50 text-gray-900">

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtmId }}" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>

{{-- PREVIEW MODE BANNER — only visible on /preview-landing --}}
@if(isset($isPreviewMode) && $isPreviewMode)
<div style="position:fixed;top:0;left:0;right:0;z-index:99999;background:linear-gradient(135deg,#f59e0b,#d97706);color:white;padding:0.6rem 1.5rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;font-family:'Inter',-apple-system,sans-serif;box-shadow:0 2px 16px rgba(0,0,0,0.2);flex-wrap:wrap">
    <div style="display:flex;align-items:center;gap:0.75rem">
        <span style="display:inline-flex;align-items:center;gap:0.35rem;background:rgba(0,0,0,0.2);padding:0.25rem 0.75rem;border-radius:9999px;font-size:0.65rem;font-weight:900;letter-spacing:0.15em;text-transform:uppercase">
            <span style="width:0.5rem;height:0.5rem;border-radius:50%;background:#fff;animation:pulse 1.5s infinite"></span>
            PREVIEW MODE
        </span>
        <span style="font-size:0.78rem;font-weight:600">Anda sedang melihat versi <strong>DRAFT</strong> — belum ditayangkan ke publik.</span>
    </div>
    <div style="display:flex;align-items:center;gap:0.5rem">
        <a href="{{ url('/') }}" target="_blank" style="font-size:0.7rem;font-weight:700;color:white;background:rgba(0,0,0,0.2);padding:0.35rem 0.875rem;border-radius:0.5rem;text-decoration:none">
            Lihat Live Page
        </a>
        <a href="{{ route('filament.admin.pages.landing-page-cms') }}" style="font-size:0.7rem;font-weight:700;color:#92400e;background:white;padding:0.35rem 0.875rem;border-radius:0.5rem;text-decoration:none">
            Kembali ke Editor
        </a>
    </div>
</div>
<style>@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.3} }</style>
<div style="height:2.75rem"></div>{{-- spacer so content doesn't go under banner --}}
@endif

<!-- Navigation -->
<nav class="fixed w-full z-50 transition-all duration-300 bg-transparent" id="navbar" x-data="{ mobileMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20 items-center">

            <!-- Logo AVRA Tour -->
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0">
                <svg width="42" height="42" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M75 20 L95 10 L85 35 Z" fill="#9B8EC7"/>
                    <path d="M75 20 L95 10 L85 35 C80 30 75 25 75 20Z" fill="#7a6eb0"/>
                    <path d="M15 95 C25 60 50 40 75 20 C65 50 45 70 25 95 Z" fill="#9B8EC7" opacity="0.95"/>
                    <path d="M22 100 C35 68 58 50 80 30 C68 60 50 80 30 105 Z" fill="#BDA6CE" opacity="0.80"/>
                    <path d="M30 106 C45 78 65 62 85 45 C75 72 58 90 38 110 Z" fill="#D4C2E8" opacity="0.60"/>
                    <path d="M38 110 C55 88 72 74 90 60 C82 82 66 98 46 112 Z" fill="#F45B26" opacity="0.95"/>
                    <path d="M46 112 C62 95 78 83 95 72 C88 90 74 104 56 114 Z" fill="#FAE251" opacity="0.85"/>
                </svg>
                <span class="text-xl font-extrabold tracking-tight hidden sm:inline" style="color:#9B8EC7;">AVRA<span style="color:#F45B26;">TOUR</span></span>
                <span class="text-lg font-black tracking-tighter sm:hidden" style="color:#9B8EC7;">AVRA<span style="color:#F45B26;">T</span></span>
            </a>
            
            <div class="hidden lg:flex items-center space-x-0.5" id="nav-links">
                <a href="#tours"   class="text-white/90 hover:text-white px-3 py-2 rounded-full text-sm font-semibold transition-colors hover:bg-white/10">Destinasi</a>
                <a href="#process" class="text-white/90 hover:text-white px-3 py-2 rounded-full text-sm font-semibold transition-colors hover:bg-white/10">Cara Pesan</a>
                <a href="#about"   class="text-white/90 hover:text-white px-3 py-2 rounded-full text-sm font-semibold transition-colors hover:bg-white/10">Tentang</a>
                <a href="#gallery" class="text-white/90 hover:text-white px-3 py-2 rounded-full text-sm font-semibold transition-colors hover:bg-white/10">Galeri</a>
                <a href="{{ route('destinations') }}" class="text-white/90 hover:text-white px-3 py-2 rounded-full text-sm font-semibold transition-colors hover:bg-white/10">Semua Trip</a>
            </div>

            <div class="flex items-center gap-1.5 sm:gap-2">
                <!-- Desktop Buttons -->
                <a href="{{ route('booking.cek') }}" class="hidden sm:inline-flex items-center justify-center bg-white border border-gray-200 text-gray-600 px-4 py-2 rounded-full font-semibold hover:text-[#9B8EC7] hover:border-[#9B8EC7]/40 hover:bg-[#9B8EC7]/5 transition shadow-sm text-sm whitespace-nowrap">
                    🔍 Cek Pesanan
                </a>
                
                <!-- Compact Mobile Icon-only Button -->
                <a href="{{ route('booking.cek') }}" class="sm:hidden inline-flex items-center justify-center bg-white border border-gray-200 text-gray-600 w-10 h-10 rounded-full font-semibold transition shadow-sm hover:border-[#9B8EC7]/40">
                    🔍
                </a>

                <!-- Mobile Menu Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-white lg:hidden" id="mobileMenuBtn">
                    <svg class="w-6 h-6" x-show="!mobileMenuOpen" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                    <svg class="w-6 h-6" x-show="mobileMenuOpen" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>

                <!-- Action Button Desktop -->
                <a href="#tours" class="hidden md:inline-flex items-center justify-center text-white px-5 py-2.5 rounded-full font-bold hover:opacity-90 transition shadow-md text-sm whitespace-nowrap" style="background: linear-gradient(135deg, #9B8EC7, #F45B26);">
                    Pesan Trip
                </a>
            </div>
        </div>
    </div>

    <!-- Mobile Dropdown Menu -->
    <div x-show="mobileMenuOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="lg:hidden bg-white/95 backdrop-blur-xl border-b border-gray-100 shadow-2xl" id="mobile-menu" x-cloak>
        <div class="px-4 pt-4 pb-8 space-y-2">
            <a href="#tours"   @click="mobileMenuOpen = false" class="block px-4 py-3 rounded-2xl text-base font-bold text-gray-700 hover:bg-primary/5 hover:text-primary">Destinasi</a>
            <a href="#process" @click="mobileMenuOpen = false" class="block px-4 py-3 rounded-2xl text-base font-bold text-gray-700 hover:bg-primary/5 hover:text-primary">Cara Pesan</a>
            <a href="#gallery" @click="mobileMenuOpen = false" class="block px-4 py-3 rounded-2xl text-base font-bold text-gray-700 hover:bg-primary/5 hover:text-primary">Galeri</a>
            <a href="{{ route('destinations') }}" @click="mobileMenuOpen = false" class="block px-4 py-3 rounded-2xl text-base font-bold text-gray-700 hover:bg-primary/5 hover:text-primary">Semua Trip</a>
            <a href="{{ route('schedules.index') }}" @click="mobileMenuOpen = false" class="block px-4 py-3 rounded-2xl text-base font-bold text-gray-700 hover:bg-primary/5 hover:text-primary">Jadwal Trip</a>
            
            <div class="pt-4 mt-4 border-t border-gray-100">
                <a href="#tours" @click="mobileMenuOpen = false" class="flex items-center justify-center w-full py-4 rounded-3xl text-white font-black text-lg" style="background: linear-gradient(135deg, #9B8EC7, #F45B26);">
                    Pesan Paket Sekarang
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Parallax Hero -->
<div class="relative min-h-screen flex flex-col items-center justify-center bg-fixed bg-cover bg-center overflow-hidden"
     style="background-image: url('{{ $landingPage->hero_background_image ?? 'https://images.unsplash.com/photo-1508009603885-50cf7c579365?q=80&w=2350&auto=format&fit=crop' }}');">
    
    <!-- Dark overlay: lebih gelap agar teks terbaca -->
    <div class="absolute inset-0 z-0" style="background: linear-gradient(160deg, rgba(20,10,40,0.75) 0%, rgba(10,10,30,0.65) 60%, rgba(30,15,10,0.55) 100%);"></div>
    <!-- Professional bottom fade -->
    <div class="absolute bottom-0 w-full h-16 bg-gradient-to-t from-gray-50 to-transparent pointer-events-none z-10"></div>

    <div class="relative z-10 w-full max-w-4xl mx-auto px-4 flex flex-col items-center justify-center text-center mt-20">
        
        <!-- Headline -->
        <h1 class="text-4xl sm:text-6xl md:text-7xl font-black text-white mb-5 tracking-tight leading-[1.1] drop-shadow-lg">
            {{ $landingPage->hero_title ?? 'Perjalanan Impian,' }}
            <br>
            <span style="background: linear-gradient(135deg, #BDA6CE 0%, #FAE251 60%, #F45B26 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                {{ $landingPage->hero_subtitle ?? 'Kami yang Urus' }}
            </span>
        </h1>

        <!-- Sub: Condenced & High-Impact -->
        <p class="text-sm sm:text-base md:text-xl text-white font-medium max-w-2xl mx-auto leading-relaxed mb-10 drop-shadow-md">
            {!! strip_tags($landingPage->hero_text ?? 'Eksplorasi destinasi <span class="text-[#FAE251] font-black">internasional</span> & <span class="text-[#FAE251] font-black">domestik</span> tanpa ribet. Tiket, hotel, hingga itinerary lengkap—semua kami siapkan untuk liburan impian Anda.', '<span><br><b><strong><i><u>') !!}
        </p>

        <!-- CTA Buttons -->
        <div class="flex flex-wrap items-center justify-center gap-3 mt-8">
            <a href="#tours" class="inline-flex items-center justify-center text-white px-4 sm:px-6 py-2.5 rounded-full font-semibold hover:opacity-90 transition shadow-lg text-sm sm:text-base whitespace-nowrap" style="background: linear-gradient(135deg, #9B8EC7, #F45B26);">
                Pesan Sekarang
            </a>
            <a href="#tours" class="inline-flex items-center justify-center bg-white/10 backdrop-blur-sm border border-white/30 text-white px-4 sm:px-6 py-2.5 rounded-full font-semibold hover:bg-white/20 transition shadow-sm text-sm sm:text-base whitespace-nowrap">
                Pelajari Paket
            </a>
        </div>
    </div>
    
    <!-- Flash Messages -->
    <div class="absolute bottom-6 left-0 right-0 max-w-3xl mx-auto px-4 z-20">
        @if(request('booking')==='success')
        <div class="bg-green-50 border-l-4 border-green-500 p-3 rounded-r shadow-md flex items-center gap-3">
            <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <p class="font-bold text-green-800 text-sm">Pembayaran Berhasil!</p>
                <p class="text-xs text-green-700">Terima kasih telah memesan. Silakan periksa email Anda.</p>
            </div>
        </div>
        @elseif(request('booking')==='pending')
        <div class="bg-amber-50 border-l-4 border-amber-500 p-3 rounded-r shadow-md flex items-center gap-3">
            <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <p class="font-bold text-amber-800 text-sm">Pemesanan Diterima (Pending)</p>
                <p class="text-xs text-amber-700">Tim kami akan segera menghubungi Anda via WhatsApp.</p>
                @if(session('warning'))
                    <p class="mt-1 text-xs font-semibold text-amber-800 bg-amber-200/50 px-2 py-1 rounded">{{ session('warning') }}</p>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Featured Trips Section -->
<section id="tours" class="py-24 bg-gray-50 relative z-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block font-semibold tracking-wider uppercase text-xs px-4 py-1.5 rounded-full mb-3 text-white" style="background: linear-gradient(135deg,#9B8EC7,#BDA6CE);">{{ $landingPage->featured_trip_subtitle ?? 'Destinasi Pilihan' }}</span>
            <h2 class="text-4xl font-extrabold text-gray-900 tracking-tight">{{ $landingPage->featured_trip_title ?? 'Paket Trip Unggulan' }}</h2>
            <p class="mt-4 max-w-2xl text-xl text-gray-500 mx-auto">{{ $landingPage->featured_trip_subtitle ?? 'Paket wisata terpilih yang dirancang untuk memberikan pengalaman tak terlupakan.' }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($trips as $trip)
            <div class="trip-card bg-white rounded-3xl overflow-hidden shadow-sm cursor-pointer group"
                 onclick="openTripModal('{{ $trip->slug }}', this)"
                 data-slug="{{ $trip->slug }}">
                <div class="relative h-64 overflow-hidden">
                    <img src="{{ $trip->image_url }}" class="w-full h-full object-cover" alt="{{ $trip->title }}">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                    <div class="absolute top-4 right-4">
                        <span class="bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold {{ $trip->is_domestic ? 'text-blue-600' : 'text-indigo-600' }}">
                            {{ $trip->is_domestic ? '🏠 Domestik' : '✈️ Internasional' }}
                        </span>
                    </div>
                    @if($trip->duration_days)
                    <div class="absolute bottom-4 left-4">
                        <span class="bg-black/40 backdrop-blur-sm text-white text-xs font-semibold px-3 py-1 rounded-full">
                            {{ $trip->duration_days }} Hari
                        </span>
                    </div>
                    @endif
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $trip->title }}</h3>
                    <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $trip->description }}</p>
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-xs text-gray-400 uppercase tracking-wide">Mulai dari</span>
                            <div class="text-2xl font-extrabold trip-price transition-colors duration-300" style="color:#9B8EC7;">Rp {{ number_format($trip->price, 0, ',', '.') }}</div>
                        </div>
                        <div class="flex items-center gap-1.5 text-white px-5 py-2.5 rounded-full font-semibold transition text-sm" style="background:linear-gradient(135deg,#9B8EC7,#BDA6CE);">
                            Lihat Detail
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-12 text-center">
            <a href="{{ route('destinations') }}" class="inline-flex items-center gap-2 px-10 py-4 rounded-full font-bold text-white transition-all shadow-lg hover:opacity-90 hover:-translate-y-0.5" style="background: linear-gradient(135deg, #F45B26, #FAE251);">
                Lihat Semua Destinasi
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </a>
        </div>
    </div>
    <!-- Seamless short blur to Section 2 -->
    <div class="absolute bottom-0 w-full h-12 bg-gradient-to-t from-white to-transparent z-10 backdrop-blur-[1px] pointer-events-none"></div>
</section>

<!-- How It Works -->
<section id="process" class="py-28 bg-white relative overflow-hidden">
    <!-- Decorative bg blob -->
    <div class="absolute -top-32 -right-32 w-96 h-96 rounded-full opacity-10 blur-3xl pointer-events-none" style="background:radial-gradient(circle, #9B8EC7, transparent);"></div>
    <div class="absolute -bottom-20 -left-20 w-72 h-72 rounded-full opacity-10 blur-3xl pointer-events-none" style="background:radial-gradient(circle, #F45B26, transparent);"></div>

    <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
        <!-- Professional Header Design -->
        <div class="text-center mb-16">
            <div class="inline-flex items-center gap-2 px-6 py-2 bg-secondary/5 rounded-full border border-secondary/10 shadow-sm mb-6">
                <span class="text-[10px] font-black text-secondary tracking-[0.3em] uppercase">Experience The Journey</span>
                <span class="w-1.5 h-1.5 rounded-full bg-accent animate-pulse"></span>
            </div>
            <h2 class="text-3xl md:text-5xl font-black text-gray-900 mb-6 tracking-tight">Tiga Langkah Menuju <br><span class="text-primary">Liburan Impian Anda</span></h2>
            <p class="max-w-2xl text-gray-500 mx-auto text-lg leading-relaxed font-medium">Langkah cerdas untuk liburan yang berkesan — kami merancang proses yang efisien agar Anda bisa fokus sepenuhnya menikmati setiap jengkal destinasi tujuan.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-10 relative">
            <!-- connector line desktop -->
            <div class="hidden md:block absolute top-16 left-[calc(16.67%+1rem)] right-[calc(16.67%+1rem)] h-1 rounded-full overflow-hidden" style="background: #f3f4f6;">
                 <div class="h-full w-1/2 bg-gradient-to-r from-primary via-secondary to-accent"></div>
            </div>

            <!-- Step 1 -->
            <div class="relative bg-white rounded-[2.5rem] p-10 shadow-xl shadow-gray-100/50 border border-gray-50 hover:shadow-2xl transition-all duration-300 group hover:-translate-y-2">
                <div class="w-14 h-14 flex items-center justify-center rounded-2xl text-2xl font-black text-white mb-8 shadow-lg shadow-primary/20 group-hover:scale-110 transition-transform" style="background:linear-gradient(135deg,#9B8EC7,#BDA6CE);">01</div>
                <h3 class="text-xl font-extrabold text-gray-900 mb-4 tracking-tight">Pilih Destinasi</h3>
                <p class="text-gray-500 leading-relaxed text-sm font-medium">Jelajahi destinasi impianmu satu kali klik. Dari Bali sampai Barcelona — semua ada di satu tempat.</p>
                <div class="mt-6 inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Liburan Sat Set
                </div>
            </div>

            <!-- Step 2 -->
            <div class="relative bg-white rounded-[2.5rem] p-10 shadow-xl shadow-gray-100/50 border border-gray-50 hover:shadow-2xl transition-all duration-300 group hover:-translate-y-2">
                <div class="w-14 h-14 flex items-center justify-center rounded-2xl text-2xl font-black text-white mb-8 shadow-lg shadow-secondary/20 group-hover:scale-110 transition-transform" style="background:linear-gradient(135deg,#F45B26,#FAE251);">02</div>
                <h3 class="text-xl font-extrabold text-gray-900 mb-4 tracking-tight">Booking & Bayar</h3>
                <p class="text-gray-500 leading-relaxed text-sm font-medium">Konfirmasi pesananmu lewat gateway aman. Bukti pesanan lansung mendarat di email & WhatsApp.</p>
                <div class="mt-6 inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-secondary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Aman Terkendali
                </div>
            </div>

            <!-- Step 3 -->
            <div class="relative bg-white rounded-[2.5rem] p-10 shadow-xl shadow-gray-100/50 border border-gray-50 hover:shadow-2xl transition-all duration-300 group hover:-translate-y-2">
                <div class="w-14 h-14 flex items-center justify-center rounded-2xl text-2xl font-black text-white mb-8 shadow-lg shadow-accent/20 group-hover:scale-110 transition-transform" style="background:linear-gradient(135deg,#BDA6CE,#9B8EC7);">03</div>
                <h3 class="text-xl font-extrabold text-gray-900 mb-4 tracking-tight">Enjoy The Trip!</h3>
                <p class="text-gray-500 leading-relaxed text-sm font-medium">Semua kami handle, dari itinerary sampai pemandu lokal. Tinggal bawa koper & selamat menikmati.</p>
                <div class="mt-6 inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-[#9B8EC7]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Slay The Journey
                </div>
            </div>
        </div>
    </div>
    <!-- Seamless short blur to Section 3 -->
    <div class="absolute bottom-0 w-full h-12 bg-gradient-to-t from-gray-50 to-transparent z-10 backdrop-blur-[1px] pointer-events-none"></div>
</section>

<!-- About -->
<section id="about" class="py-24 bg-gray-50 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center gap-16">
        <div class="md:w-1/2">
            <img src="{{ $landingPage->about_image ?? 'https://images.unsplash.com/photo-1522199710521-72d69614c702?q=80&w=2072&auto=format&fit=crop' }}"
                 class="rounded-[2.5rem] shadow-2xl transition duration-500 hover:-translate-y-2" alt="Tim Kami">
        </div>
        <div class="md:w-1/2">
            <span class="inline-block font-black tracking-widest uppercase text-[10px] px-4 py-1.5 rounded-full mb-4 text-white" style="background:linear-gradient(135deg,#9B8EC7,#BDA6CE);">{{ $landingPage->about_subtitle ?? 'Tentang AVRA Tour' }}</span>
            <h2 class="text-4xl font-extrabold text-gray-900 mb-6 leading-tight">{!! strip_tags($landingPage->about_title ?? 'Mitra Perjalanan<br>Yang Bisa Dipercaya', '<span><br><b><strong><i><u>') !!}</h2>
            <p class="text-lg text-gray-600 mb-6 leading-relaxed font-medium">{{ $landingPage->about_text ?? 'Bebas overthinking, Bestie. Ribuan traveler sudah membuktikannya. Fokus kami cuma satu: bikin momen liburanmu epic & gak terlupakan.' }}</p>
            <a href="#" class="inline-flex items-center gap-2 font-black uppercase text-xs tracking-widest transition" style="color:#9B8EC7;">
                Baca Kisah Kami <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </a>
        </div>
    </div>
    <!-- Seamless short blur to Section 4 -->
    <div class="absolute bottom-0 w-full h-12 bg-gradient-to-t from-white to-transparent z-10 backdrop-blur-[1px] pointer-events-none"></div>
</section>

<!-- Documentation & Video -->
<section id="gallery" class="py-24 bg-white border-t border-gray-100 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="inline-block font-black tracking-widest uppercase text-[10px] px-4 py-1.5 rounded-full mb-3 text-white" style="background:linear-gradient(135deg,#9B8EC7,#BDA6CE);">Dokumentasi &amp; Galeri</span>
            <h2 class="text-4xl font-extrabold text-gray-900 tracking-tight">Keseruan Bersama AVRA Tour</h2>
            <p class="mt-4 max-w-2xl text-lg text-gray-500 mx-auto">Saksikan momen indah pelanggan kami dari berbagai penjuru dunia.</p>
        </div>

        <!-- YouTube Embed -->
        @if(isset($landingPage) && $landingPage->youtube_url)
        <div class="mb-6 max-w-4xl mx-auto rounded-3xl overflow-hidden shadow-2xl ring-1 ring-gray-100">
            <div class="relative w-full" style="padding-top: 56.25%;">
                <iframe
                    class="absolute top-0 left-0 w-full h-full"
                    src="{{ $landingPage->youtube_url }}"
                    title="AVRA Tour &mdash; Video Perjalanan"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    referrerpolicy="strict-origin-when-cross-origin"
                    allowfullscreen>
                </iframe>
            </div>
        </div>
        @endif

        <!-- Gallery: 4:3 standardized via CSS class -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-14">
            @php
                $displayGalleries = ($galleries && $galleries->count() > 0) 
                    ? $galleries->pluck('image')->toArray() 
                    : [
                        'https://images.unsplash.com/photo-1528127269322-539801943592?auto=format&fit=crop&q=80&w=800',
                        'https://images.unsplash.com/photo-1507608616759-54f48f0af0ee?auto=format&fit=crop&q=80&w=800',
                        'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?auto=format&fit=crop&q=80&w=800',
                        'https://images.unsplash.com/photo-1533587851505-d119e13bf0b7?auto=format&fit=crop&q=80&w=800',
                        'https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&q=80&w=800',
                        'https://images.unsplash.com/photo-1526778548025-fa2f459cd5c1?auto=format&fit=crop&q=80&w=800',
                        'https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&q=80&w=800',
                        'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?auto=format&fit=crop&q=80&w=800'
                    ];
            @endphp

            @foreach($displayGalleries as $img)
            <div class="gallery-item shadow-sm">
                <img src="{{ str_starts_with($img, 'http') ? $img : Storage::url($img) }}" alt="Dokumentasi AVRA Tour" loading="lazy">
            </div>
            @endforeach
        </div>
    </div>
    <!-- Seamless short blur to Section 5 -->
    <div class="absolute bottom-0 w-full h-12 bg-gradient-to-t from-gray-50 to-transparent z-10 backdrop-blur-[1px] pointer-events-none"></div>
</section>

<!-- Testimonials -->
<section class="py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="inline-block bg-orange-100 text-orange-700 font-semibold tracking-wider uppercase text-xs px-4 py-1.5 rounded-full mb-3">{{ $landingPage->testimonial_subtitle ?? 'Testimoni' }}</span>
            <h2 class="text-4xl font-extrabold text-gray-900">{{ $landingPage->testimonial_title ?? 'Yang Mereka Katakan' }}</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @php
                $testimonials = $landingPage->testimonials ?? [
                    ['name' => 'Sarah Jenkins', 'quote' => 'Trip ke Thailand kami berjalan sempurna. Pemandu luar biasa dan setiap detail sudah diurus.', 'stars' => 5],
                    ['name' => 'Budi Santoso', 'quote' => 'Bali sungguh magis! Berkat AVRA Tour, kami dapat harga terbaik tanpa khawatir apapun.', 'stars' => 5],
                    ['name' => 'Emily Wong', 'quote' => 'Sangat rekomendasikan paket Singapore-nya. Hotel yang dipilihkan tepat di sebelah Marina Bay!', 'stars' => 5]
                ];
            @endphp
            @foreach($testimonials as $testi)
            <div class="bg-white p-8 rounded-2xl border border-gray-100 hover:shadow-md transition" style="--tw-ring-color:#BDA6CE" onmouseover="this.style.borderColor='#E8E0F3'" onmouseout="this.style.borderColor='#f3f4f6'">
                <div class="flex gap-0.5 mb-4" style="color:#FAE251; text-shadow: 0 0 4px rgba(244,91,38,.4);">
                    @for($i=0; $i < ($testi['stars'] ?? 5); $i++) ★ @endfor
                </div>
                <p class="text-gray-600 italic mb-6 leading-relaxed text-sm">"{{ $testi['quote'] ?? '—' }}"</p>
                <div class="font-bold text-gray-900 text-sm">— {{ $testi['name'] ?? 'Guest' }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-24 relative overflow-hidden" style="background:linear-gradient(135deg,#9B8EC7 0%,#BDA6CE 40%, #F45B26 100%);">
    <div class="absolute inset-0 opacity-10">
        <svg class="h-full w-full" fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0 100 C 20 0 50 0 100 100 Z"></path></svg>
    </div>
    <div class="max-w-4xl mx-auto px-4 relative z-10 text-center">
        <h2 class="text-4xl font-extrabold text-white mb-6">{{ $landingPage->cta_title ?? 'Siap Memulai Petualangan?' }}</h2>
        <p class="text-xl text-white/80 mb-10">{{ $landingPage->cta_subtitle ?? 'Destinasi impian Anda hanya beberapa klik lagi.' }}</p>
        <a href="#tours" data-cta="footer_book_now" class="font-bold py-4 px-10 rounded-full shadow-2xl hover:-translate-y-1 transition transform text-lg inline-block" style="background:#fff; color:#9B8EC7;">
            Lihat Paket Sekarang
        </a>
    </div>
</section>

<!-- Footer -->
<footer class="bg-gray-950 text-gray-400 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-8">
        <div>
            <span class="text-2xl font-extrabold text-white mb-4 block">{{ config('app.name', 'AVRA Tour') }}</span>
            <p class="text-sm leading-relaxed">{{ $landingPage->footer_text ?? 'Menghubungkan Anda ke destinasi paling indah di dunia sejak 2011.' }}</p>
        </div>
        <div>
            <h4 class="text-white font-semibold mb-4">Destinasi</h4>
            <ul class="space-y-2 text-sm">
                @foreach(['Indonesia','Singapore','Malaysia','Thailand'] as $d)
                <li><a href="{{ route('destinations') }}" class="hover:text-white transition">{{ $d }}</a></li>
                @endforeach
            </ul>
        </div>
        <div>
            <h4 class="text-white font-semibold mb-4">Perusahaan</h4>
            <ul class="space-y-2 text-sm">
                @foreach(['Tentang Kami','Kontak','Syarat & Ketentuan'] as $m)
                <li><a href="#" class="hover:text-white transition">{{ $m }}</a></li>
                @endforeach
            </ul>
        </div>
        <div>
            <h4 class="text-white font-semibold mb-4">Hubungi Kami</h4>
            <ul class="space-y-2 text-sm">
                <li>{{ $landingPage->footer_email ?? 'info@avratour.com' }}</li>
                <li>{{ $landingPage->footer_phone ?? '+62 812 3456 7890' }}</li>
                <li>{{ $landingPage->footer_address ?? 'Jakarta, Indonesia' }}</li>
            </ul>
        </div>
    </div>
    <div class="max-w-7xl mx-auto px-4 mt-12 pt-8 border-t border-gray-800 text-center text-sm">
        &copy; {{ date('Y') }} {{ config('app.name', 'AVRA Tour') }}. All rights reserved.
    </div>
</footer>

<!-- WhatsApp Floating -->
<a href="https://wa.me/6281234567890" target="_blank"
   style="position:fixed;bottom:1.5rem;right:1.5rem;z-index:9998;background:#22c55e;color:#fff;padding:1rem;border-radius:9999px;box-shadow:0 8px 24px rgba(34,197,94,.4);display:flex;align-items:center;justify-content:center;transition:transform .2s;"
   onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
    <svg fill="currentColor" viewBox="0 0 24 24" style="width:2rem;height:2rem;"><path d="M12.031 0C5.385 0 0 5.385 0 12.031c0 2.222.585 4.316 1.624 6.136L.357 23.332l5.321-1.396a11.96 11.96 0 006.353 1.83h.005c6.643 0 12.03-5.385 12.03-12.031C24.066 5.385 18.682 0 12.031 0zm.005 21.603c-1.872 0-3.706-.503-5.312-1.455l-.38-.225-3.951 1.036 1.056-3.854-.247-.394a9.886 9.886 0 01-1.51-5.344C1.652 5.093 6.644.103 12.036.103s10.384 4.99 10.384 10.384-4.896 11.116-10.384 11.116zm5.7-7.792c-.312-.156-1.848-.912-2.134-1.018-.286-.104-.494-.156-.702.156-.208.312-.806 1.018-.988 1.226-.182.208-.364.234-.676.078-1.554-.775-2.73-1.636-3.77-3.376-.182-.312-.02-.482.136-.638.14-.14.312-.364.468-.546.156-.182.208-.312.312-.52.104-.208.052-.39-.026-.546-.078-.156-.702-1.69-.962-2.314-.254-.608-.512-.526-.702-.536-.182-.01-.39-.01-.598-.01-.208 0-.546.078-.832.39-.286.312-1.092 1.066-1.092 2.6 0 1.534 1.118 3.016 1.274 3.224.156.208 2.21 3.38 5.356 4.732 2.112.91 2.924.966 3.96.812.87-.13 1.848-.754 2.108-1.482.26-.728.26-1.352.182-1.482-.078-.13-.286-.208-.598-.364z"/></svg>
</a>

<!-- Trip Detail Modal -->
<div id="tripModal" style="display:none;" onclick="if(['tripModal','tripModalOverlay'].includes(event.target.id))closeTripModal()">
    <div class="modal-overlay" id="tripModalOverlay">
        <div class="modal-panel" onclick="event.stopPropagation()">
            <!-- Right Column: Full Bleed Image with Overlay Info & Footer -->
            <div class="modal-side">
                <div class="modal-img" id="modalImgContainer">
                    <img id="modalImg" src="" alt="">
                    <div class="modal-img-overlay" style="background: linear-gradient(to top, rgba(11, 15, 33, 0.98) 0%, rgba(11, 15, 33, 0.6) 35%, rgba(11, 15, 33, 0.2) 60%, transparent 100%);"></div>
                </div>

                <!-- Overlaid Content -->
                <div class="relative z-10 flex flex-col h-full grow p-8 justify-end md:pb-10 text-white">
                    <button onclick="closeTripModal()" style="position:absolute;top:1rem;right:1rem;background:rgba(255,255,255,.2);backdrop-filter:blur(8px);border:none;border-radius:9999px;padding:.5rem;cursor:pointer;color:#fff;z-index:20;display:flex;">
                        <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>

                    <div class="mb-8">
                        <h3 id="modalTitle" style="font-size: 1.75rem; font-weight: 900; line-height: 1.1; letter-spacing: -0.02em; margin-bottom: 0.5rem; text-shadow: 0 4px 12px rgba(0,0,0,0.5);"></h3>
                        <p id="modalSubtitle" style="font-size: 0.875rem; font-weight: 700; color: rgba(255,255,255,0.9); text-shadow: 0 2px 8px rgba(0,0,0,0.4);"></p>
                    </div>

                    <div class="flex items-center gap-4">
                        <button onclick="closeTripModal()" class="text-white/60 hover:text-white transition-colors text-[10px] font-black uppercase tracking-widest">Tutup</button>
                        <a id="modalBookBtn" href="#" style="flex:1; padding:.875rem; border-radius:1.25rem; font-weight:800; color:#111827; background:#fff; text-decoration:none; display:inline-flex; align-items:center; justify-content:center; gap:.5rem; box-shadow:0 10px 30px rgba(0,0,0,0.3); font-size: 0.875rem; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            Pesan Sekarang
                            <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Left Column: Tabs & Itineraries -->
            <div class="modal-main">
                <div style="padding: 2rem; padding-bottom: 0.5rem;">
                    <div id="modalTabs" style="display:flex;flex-wrap:wrap;gap:.5rem;"></div>
                </div>
                <!-- Brief Description: Top of Panel -->
                <div style="padding: 0 2rem 1.5rem 2rem;">
                    <!-- Compact Meta & Price Bar -->
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f3f4f6; padding-bottom: 1.25rem; margin-bottom: 1.25rem;">
                        <div>
                            <span style="font-size: 0.6rem; font-weight: 900; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.2em; display: block; margin-bottom: 0.2rem;">Info Perjalanan</span>
                            <div id="modalMainMeta" style="font-size: 0.813rem; font-weight: 700; color: #374151;"></div>
                        </div>
                        <div style="text-align: right;">
                            <span style="font-size: 0.6rem; font-weight: 900; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.2em; display: block; margin-bottom: 0.2rem;">Mulai Dari</span>
                            <div id="modalMainPrice" style="font-size: 1.25rem; font-weight: 900; color: var(--avra-primary);"></div>
                        </div>
                    </div>

                    <h4 style="font-size: 0.7rem; font-weight: 800; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.4rem;">Mengenai Paket Ini</h4>
                    <p id="modalDesc" style="font-size:0.875rem;color:#4b5563;line-height:1.6;"></p>
                </div>
                
                <div class="modal-body border-t border-gray-50" style="background: #fafbff;">
                    <div id="modalTabContent"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentSlug = null;

function openTripModal(slug, card) {
    currentSlug = slug;
    const modal = document.getElementById('tripModal');
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';

    // Show loading state
    document.getElementById('modalTitle').textContent = 'Memuat...';
    document.getElementById('modalDesc').textContent = '';
    document.getElementById('modalSubtitle').textContent = '';
    document.getElementById('modalTabs').innerHTML = '';
    document.getElementById('modalTabContent').innerHTML = '<div style="text-align:center;padding:2rem;color:#9ca3af;">Memuat detail trip...</div>';

    fetch(`/trip/${slug}/detail`)
        .then(r => r.json())
        .then(trip => {
            document.getElementById('modalImg').src = trip.image;
            document.getElementById('modalTitle').textContent = trip.title;
            
            // Subtitle on image removed for cleaner look or set to empty
            document.getElementById('modalSubtitle').textContent = '';
            
            // New Meta & Price Locations (Compact Bar)
            document.getElementById('modalMainMeta').textContent = 
                (trip.duration_days ? trip.duration_days + ' Hari' : '') +
                (trip.destination_country ? ' · ' + trip.destination_country : '');
            
            document.getElementById('modalMainPrice').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(trip.price);

            document.getElementById('modalDesc').textContent = trip.description;
            document.getElementById('modalBookBtn').href = '/trip/' + trip.slug;

            // Build tabs
            const tabsEl = document.getElementById('modalTabs');
            const contentEl = document.getElementById('modalTabContent');
            tabsEl.innerHTML = '';
            contentEl.innerHTML = '';

            if (!trip.locations || trip.locations.length === 0) {
                contentEl.innerHTML = '<p style="color:#9ca3af;text-align:center;padding:2rem;">Belum ada detail lokasi tersedia.</p>';
                return;
            }

            trip.locations.forEach((loc, idx) => {
                // Tab button
                const btn = document.createElement('button');
                btn.className = 'tab-btn' + (idx === 0 ? ' active' : '');
                btn.setAttribute('data-tab', idx);
                btn.innerHTML = (loc.flag_emoji ? loc.flag_emoji + ' ' : '') + loc.country + (loc.city ? ' <span style="font-weight:400;opacity:.7;font-size:.8rem;">— ' + loc.city + '</span>' : '');
                btn.onclick = () => switchTab(idx, trip.locations.length);
                tabsEl.appendChild(btn);

                // Tab panel
                const panel = document.createElement('div');
                panel.id = 'tab-panel-' + idx;
                panel.style.display = idx === 0 ? 'grid' : 'none';
                panel.style.gridTemplateColumns = '1fr 1fr';
                panel.style.gap = '2rem';

                // Itinerary column
                let itiHtml = `<div><h4 style="font-size:1rem;font-weight:700;color:#111827;margin-bottom:.75rem;display:flex;align-items:center;gap:.5rem;">
                    <svg style="width:1.125rem;height:1.125rem;color:#2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Itinerary</h4>`;
                if (loc.itineraries && loc.itineraries.length) {
                    loc.itineraries.forEach(it => {
                        itiHtml += `<div class="itinerary-item">
                            <div class="itinerary-day">D${it.day}</div>
                            <div><div style="font-weight:600;color:#111827;font-size:.875rem;">${it.title}</div>
                            ${it.description ? '<div style="font-size:.813rem;color:#6b7280;margin-top:.25rem;">' + it.description + '</div>' : ''}</div>
                        </div>`;
                    });
                } else {
                    itiHtml += '<p style="color:#9ca3af;font-size:.875rem;">Belum ada itinerary.</p>';
                }
                itiHtml += '</div>';

                // Accommodation column
                let accHtml = `<div><h4 style="font-size:1rem;font-weight:700;color:#111827;margin-bottom:.75rem;display:flex;align-items:center;gap:.5rem;">
                    <svg style="width:1.125rem;height:1.125rem;color:#7c3aed;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Akomodasi</h4>`;
                if (loc.accommodations && loc.accommodations.length) {
                    loc.accommodations.forEach(a => {
                        accHtml += `<div class="accomm-item">
                            <div class="accomm-icon">
                                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div><div style="font-weight:600;color:#111827;font-size:.875rem;">${a.name}</div>
                            ${a.type ? '<span style="font-size:.75rem;background:#f5f3ff;color:#7c3aed;padding:.125rem .5rem;border-radius:9999px;font-weight:600;">' + a.type + '</span>' : ''}
                            ${a.notes ? '<div style="font-size:.8rem;color:#9ca3af;margin-top:.25rem;">' + a.notes + '</div>' : ''}
                            </div>
                        </div>`;
                    });
                } else {
                    accHtml += '<p style="color:#9ca3af;font-size:.875rem;">Belum ada info akomodasi.</p>';
                }
                accHtml += '</div>';

                panel.innerHTML = itiHtml + accHtml;
                contentEl.appendChild(panel);
            });
        })
        .catch(() => {
            document.getElementById('modalTabContent').innerHTML =
                '<p style="color:#ef4444;text-align:center;padding:2rem;">Gagal memuat detail. Silakan coba lagi.</p>';
        });
}

function switchTab(idx, total) {
    for (let i = 0; i < total; i++) {
        const panel = document.getElementById('tab-panel-' + i);
        if (panel) panel.style.display = i === idx ? 'grid' : 'none';
    }
    document.querySelectorAll('.tab-btn').forEach((btn, i) => {
        btn.classList.toggle('active', i === idx);
    });
}

function closeTripModal() {
    document.getElementById('tripModal').style.display = 'none';
    document.body.style.overflow = '';
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeTripModal(); });

// Hover effect: harga trip berubah ke warna secondary (#F45B26) saat kartu di-hover
document.querySelectorAll('.trip-card').forEach(card => {
    const price = card.querySelector('.trip-price');
    if (!price) return;
    card.addEventListener('mouseenter', () => { price.style.color = '#F45B26'; });
    card.addEventListener('mouseleave', () => { price.style.color = '#9B8EC7'; });
});

// Scroll Navbar Effect
window.addEventListener('scroll', () => {
    const nav = document.getElementById('navbar');
    const navLinks = document.getElementById('nav-links');
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');

    if (window.scrollY > 40) {
        nav.classList.add('bg-white', 'shadow-md', 'border-b', 'border-gray-100');
        if(navLinks) {
            navLinks.querySelectorAll('a').forEach(link => {
                link.classList.remove('text-white/90', 'hover:bg-white/10');
                link.classList.add('text-gray-600', 'hover:bg-primary/10', 'hover:text-primary');
            });
        }
        if(mobileMenuBtn) {
            mobileMenuBtn.classList.remove('text-white', 'bg-white/20', 'border-white/30');
            mobileMenuBtn.classList.add('text-gray-600', 'bg-gray-100', 'border-gray-200');
        }
    } else {
        nav.classList.remove('bg-white', 'shadow-md', 'border-b', 'border-gray-100');
        if(navLinks) {
            navLinks.querySelectorAll('a').forEach(link => {
                link.classList.add('text-white/90', 'hover:bg-white/10');
                link.classList.remove('text-gray-600', 'hover:bg-primary/10', 'hover:text-primary');
            });
        }
        if(mobileMenuBtn) {
            mobileMenuBtn.classList.add('text-white', 'bg-white/20', 'border-white/30');
            mobileMenuBtn.classList.remove('text-gray-600', 'bg-gray-100', 'border-gray-200');
        }
    }
});

// Advanced Tracking
window.addEventListener('load', () => {
    // 1. Performance Tracking
    if (window.performance) {
        const perfData = window.performance.timing;
        const pageLoadTime = perfData.loadEventEnd - perfData.navigationStart;
        
        fetch('/api/analytics/event', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({
                event_type: 'page_perf',
                event_name: 'load_time',
                metadata: { load_time_ms: pageLoadTime, path: window.location.pathname }
            })
        });
    }

    // 2. Automated Event Tracking for all elements with data-cta
    document.querySelectorAll('[data-cta]').forEach(el => {
        el.addEventListener('click', function() {
            const ctaName = this.getAttribute('data-cta') || 'unnamed-cta';
            const ctaText = this.innerText.trim();
            
            fetch('/api/analytics/event', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({
                    event_type: 'cta_click',
                    event_name: ctaName,
                    metadata: { text: ctaText, path: window.location.pathname }
                })
            });
        });
    });
});
</script>

</body>
</html>

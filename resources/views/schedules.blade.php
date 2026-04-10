<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jadwal Keberangkatan — {{ config('app.name', 'AVRA Tour') }}</title>
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
                    borderRadius: {
                        '4xl': '2rem',
                        '5xl': '2.5rem',
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="antialiased bg-gray-50 text-gray-900">

    <!-- Navigation -->
    <nav class="fixed w-full z-50 bg-white shadow-sm border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <!-- Logo AVRA Tour -->
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0">
                    <svg width="36" height="36" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M75 20 L95 10 L85 35 Z" fill="#9B8EC7"/>
                        <path d="M75 20 L95 10 L85 35 C80 30 75 25 75 20Z" fill="#7a6eb0"/>
                        <path d="M15 95 C25 60 50 40 75 20 C65 50 45 70 25 95 Z" fill="#9B8EC7" opacity="0.95"/>
                        <path d="M22 100 C35 68 58 50 80 30 C68 60 50 80 30 105 Z" fill="#BDA6CE" opacity="0.80"/>
                        <path d="M30 106 C45 78 65 62 85 45 C75 72 58 90 38 110 Z" fill="#D4C2E8" opacity="0.60"/>
                        <path d="M38 110 C55 88 72 74 90 60 C82 82 66 98 46 112 Z" fill="#F45B26" opacity="0.95"/>
                        <path d="M46 112 C62 95 78 83 95 72 C88 90 74 104 56 114 Z" fill="#FAE251" opacity="0.85"/>
                    </svg>
                    <span class="text-xl font-extrabold tracking-tight" style="color:#9B8EC7;">AVRA<span style="color:#F45B26;">TOUR</span></span>
                </a>
                <div class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-primary px-4 py-2 rounded-full text-sm font-medium transition-colors hover:bg-primary/10">Beranda</a>
                    <a href="{{ route('destinations') }}" class="text-gray-600 hover:text-primary px-4 py-2 rounded-full text-sm font-medium transition-colors hover:bg-primary/10">Semua Destinasi</a>
                    <a href="{{ route('schedules.index') }}" class="text-primary bg-primary/10 px-4 py-2 rounded-full text-sm font-bold">Jadwal Trip</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="pt-32 pb-12 bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-3xl md:text-5xl font-black text-gray-950 mb-4 tracking-tight">Perencanaan Trip & Jadwal</h1>
            <p class="text-xl text-gray-500 max-w-2xl mx-auto">Pantau jadwal keberangkatan mendatang dan amankan kuota Anda.</p>
        </div>
    </div>

    <section class="py-16 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-5xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                <th class="px-8 py-6">Paket Trip</th>
                                <th class="px-8 py-6">Durasi</th>
                                <th class="px-8 py-6">Keberangkatan</th>
                                <th class="px-8 py-6">Kepulangan</th>
                                <th class="px-8 py-6">Kuota</th>
                                <th class="px-8 py-6 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($schedules as $sch)
                            <tr class="hover:bg-gray-50/30 transition-colors group">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-2xl overflow-hidden shadow-sm shrink-0">
                                            <img src="{{ $sch->trip->image_url }}" alt="" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 group-hover:text-primary transition-colors">{{ $sch->trip->title }}</div>
                                            <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">{{ $sch->trip->is_domestic ? 'Domestik' : 'Internasional' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="inline-flex items-center px-3 py-1 bg-gray-100 rounded-full text-xs font-bold text-gray-600">
                                        {{ $sch->trip->duration_days }} Hari
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span class="font-bold text-gray-900 text-sm italic">{{ \Carbon\Carbon::parse($sch->departure_date)->format('d M Y') }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span class="font-bold text-gray-500 text-sm">{{ \Carbon\Carbon::parse($sch->return_date)->format('d M Y') }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex flex-col gap-1">
                                        <div class="flex justify-between items-end w-32">
                                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">Sisa Kuota</span>
                                            <span class="text-xs font-black {{ $sch->quota > 5 ? 'text-green-600' : 'text-secondary' }}">{{ $sch->quota }}</span>
                                        </div>
                                        <div class="w-32 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                            @php 
                                                $totalCapacity = 50; // Manual fallback if capacity is not in DB
                                                $percentage = ($sch->quota / $totalCapacity) * 100;
                                            @endphp
                                            <div class="h-full rounded-full transition-all duration-500 {{ $sch->quota > 5 ? 'bg-green-500' : 'bg-secondary' }}" style="width: {{ $percentage }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    @if($sch->quota > 0)
                                    <a href="{{ route('trip.show', $sch->trip->slug) }}" class="inline-flex items-center justify-center bg-gray-900 text-white text-[10px] font-black uppercase tracking-widest px-6 py-3 rounded-xl hover:bg-primary hover:shadow-lg transition-all active:scale-95">
                                        Pesan
                                    </a>
                                    @else
                                    <span class="inline-flex items-center justify-center bg-gray-100 text-gray-400 text-[10px] font-black uppercase tracking-widest px-6 py-3 rounded-xl cursor-not-allowed">
                                        Full
                                    </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-8 py-24 text-center">
                                    <div class="text-4xl mb-4">🗓️</div>
                                    <h3 class="text-lg font-bold text-gray-700">Belum ada perencanaan keberangkatan</h3>
                                    <p class="text-gray-400 text-sm">Silakan hubungi kami via WhatsApp untuk request jadwal grup.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="mt-12 text-center text-gray-400 text-xs font-bold uppercase tracking-[0.3em] flex items-center justify-center gap-4">
                <span class="w-12 h-[1px] bg-gray-200"></span>
                Official Tour Itinerary & Planning System
                <span class="w-12 h-[1px] bg-gray-200"></span>
            </div>
        </div>
    </section>

    <!-- Footer Standardized -->
    <footer class="bg-gray-950 text-gray-400 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-8 text-left">
            <div>
                <span class="text-2xl font-extrabold text-white mb-4 block">AVRA Tour</span>
                <p class="text-sm leading-relaxed">Menghubungkan Anda ke destinasi paling indah di dunia sejak 2011.</p>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4 text-left">Destinasi</h4>
                <ul class="space-y-2 text-sm">
                    @foreach(['Indonesia','Singapore','Malaysia','Thailand'] as $d)
                    <li><a href="{{ route('destinations') }}" class="hover:text-white transition">{{ $d }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4 text-left">Perusahaan</h4>
                <ul class="space-y-2 text-sm">
                    @foreach(['Tentang Kami','Kontak','Syarat & Ketentuan'] as $m)
                    <li><a href="#" class="hover:text-white transition">{{ $m }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4 text-left">Hubungi Kami</h4>
                <ul class="space-y-2 text-sm">
                    <li>info@avratour.com</li>
                    <li>+62 812 3456 7890</li>
                    <li>Jakarta, Indonesia</li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 mt-12 pt-8 border-t border-gray-800 text-center text-sm">
            &copy; {{ date('Y') }} AVRA Tour. All rights reserved.
        </div>
    </footer>

</body>
</html>

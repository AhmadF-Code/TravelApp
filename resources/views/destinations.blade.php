<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Semua Destinasi — {{ config('app.name', 'Travel Agent') }}</title>
    <meta name="description" content="Temukan semua paket wisata domestik dan internasional terbaik kami.">
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
        .modal-overlay { position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;background:rgba(17,24,39,0.85);backdrop-filter:blur(8px); }
        .modal-panel { 
            background:#fff; border-radius:1.5rem; width:100%; max-width:68rem; max-height:90vh; 
            display:flex; flex-direction:column; overflow:hidden; box-shadow:0 25px 60px rgba(0,0,0,0.35); 
        }
        @media (min-width: 768px) {
            .modal-panel { flex-direction: row-reverse; }
            .modal-side { width: 38%; display: flex; flex-direction: column; position: relative; }
            .modal-main { width: 62%; display: flex; flex-direction: column; }
        }
        .modal-img { position: relative; height: 18rem; flex-shrink: 0; background: #000; }
        @media (min-width: 768px) { .modal-img { position: absolute; inset: 0; height: 100%; z-index: 0; } }
        .modal-img img { width:100%; height:100%; object-fit:cover; opacity: 0.85; }
        .modal-body { padding:1.5rem 2rem; overflow-y:auto; flex:1; }
        .tab-btn { display:inline-flex;align-items:center;gap:.4rem;padding:.5rem 1rem;border-radius:9999px;font-size:.875rem;font-weight:600;cursor:pointer;border:2px solid #e5e7eb;background:#fff;color:#6b7280;transition:all .2s; }
        .tab-btn.active { border-color:#9B8EC7;background:#f4f2ff;color:#9B8EC7; }
        .itinerary-item { display:flex;gap:1rem;padding:.75rem 0;border-bottom:1px solid #f3f4f6; }
        .itinerary-day { flex-shrink:0;width:2.5rem;height:2.5rem;border-radius:.75rem;background:#f4f2ff;color:#9B8EC7;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.875rem; }
        .accomm-item { display:flex;align-items:flex-start;gap:.75rem;padding:.625rem 0;border-bottom:1px dashed #f3f4f6; }
        .accomm-icon { flex-shrink:0;width:2rem;height:2rem;border-radius:.5rem;background:#f0fdf4;color:#16a34a;display:flex;align-items:center;justify-content:center; }
        .trip-card { transition:transform .3s,box-shadow .3s; }
        .trip-card:hover { transform:translateY(-6px);box-shadow:0 24px 48px rgba(155,142,199,.18); }
        .trip-card img { transition:transform .5s ease; }
        .trip-card:hover img { transform:scale(1.08); }
    </style>
</head>
<body class="antialiased bg-gray-50 text-gray-900">

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
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-[#9B8EC7] px-4 py-2 rounded-full text-sm font-medium transition-colors hover:bg-[#9B8EC7]/10">Beranda</a>
                    <a href="{{ route('destinations') }}" class="text-[#9B8EC7] bg-[#9B8EC7]/10 px-4 py-2 rounded-full text-sm font-bold">Semua Destinasi</a>
                    <a href="{{ route('schedules.index') }}" class="text-gray-600 hover:text-primary px-4 py-2 rounded-full text-sm font-medium transition-colors hover:bg-primary/10">Jadwal Trip</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="pt-32 pb-12 bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-4">Jelajahi Dunia Bersama Kami</h1>
            <p class="text-xl text-gray-500 max-w-2xl mx-auto">Koleksi lengkap paket wisata domestik dan internasional yang kami kurasi spesial untuk Anda.</p>
        </div>
    </div>

    <section class="py-16 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($trips->count())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($trips as $trip)
                <div class="trip-card bg-white rounded-3xl overflow-hidden shadow-sm cursor-pointer flex flex-col"
                     onclick="openTripModal('{{ $trip->slug }}')">
                    <div class="relative h-64 overflow-hidden shrink-0">
                        <img src="{{ $trip->image_url }}" class="w-full h-full object-cover" alt="{{ $trip->title }}">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                        <div class="absolute top-4 right-4">
                            <span class="bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold {{ $trip->is_domestic ? 'text-blue-600' : 'text-indigo-600' }}">
                                {{ $trip->is_domestic ? '🏠 Domestik' : '✈️ Internasional' }}
                            </span>
                        </div>
                        @if($trip->duration_days)
                        <div class="absolute bottom-4 left-4">
                            <span class="bg-black/40 backdrop-blur-sm text-white text-xs font-semibold px-3 py-1 rounded-full">{{ $trip->duration_days }} Hari</span>
                        </div>
                        @endif
                    </div>
                    <div class="p-6 flex flex-col flex-1">
                        <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $trip->title }}</h3>
                        <p class="text-sm text-gray-500 mb-4 flex-1">{{ $trip->description }}</p>
                        <div class="flex justify-between items-center pt-4 border-t border-gray-50">
                            <div>
                                <span class="text-xs text-gray-400 uppercase tracking-wide block">Mulai dari</span>
                                <div class="text-2xl font-extrabold" style="color:#9B8EC7;">Rp {{ number_format($trip->price, 0, ',', '.') }}</div>
                            </div>
                            <div class="flex items-center gap-1.5 text-white px-5 py-2.5 rounded-full font-semibold text-sm" style="background:linear-gradient(135deg, #9B8EC7, #BDA6CE);">
                                Lihat Detail
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-24">
                <div class="text-6xl mb-4">🗺️</div>
                <h3 class="text-xl font-bold text-gray-700 mb-2">Belum ada destinasi</h3>
                <p class="text-gray-500">Paket wisata akan segera hadir.</p>
            </div>
            @endif
        </div>
    </section>

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

    <!-- Trip Modal (Synchronized Design) -->
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

                <!-- Left Column: Details -->
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

function openTripModal(slug) {
    currentSlug = slug;
    document.getElementById('tripModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
    
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

            const tabsEl = document.getElementById('modalTabs');
            const contentEl = document.getElementById('modalTabContent');
            tabsEl.innerHTML = '';
            contentEl.innerHTML = '';

            if (!trip.locations || trip.locations.length === 0) {
                contentEl.innerHTML = '<p style="color:#9ca3af;text-align:center;padding:2rem;">Belum ada rincian lokasi.</p>';
                return;
            }

            trip.locations.forEach((loc, idx) => {
                const btn = document.createElement('button');
                btn.className = 'tab-btn' + (idx === 0 ? ' active' : '');
                btn.innerHTML = (loc.flag_emoji ? loc.flag_emoji + ' ' : '') + loc.country + (loc.city ? ' <span style="font-weight:400;opacity:.7;font-size:.8rem;">— ' + loc.city + '</span>' : '');
                btn.onclick = () => switchTab(idx, trip.locations.length);
                tabsEl.appendChild(btn);

                const panel = document.createElement('div');
                panel.id = 'tab-panel-' + idx;
                panel.style.display = idx === 0 ? 'grid' : 'none';
                panel.style.gridTemplateColumns = '1fr 1fr';
                panel.style.gap = '2rem';

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

                let accHtml = `<div><h4 style="font-size:1rem;font-weight:700;color:#111827;margin-bottom:.75rem;display:flex;align-items:center;gap:.5rem;">
                    <svg style="width:1.125rem;height:1.125rem;color:#7c3aed;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Akomodasi</h4>`;
                
                if (loc.accommodations && loc.accommodations.length) {
                    loc.accommodations.forEach(a => {
                        accHtml += `<div class="accomm-item">
                            <div class="accomm-icon"><svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                            <div><div style="font-weight:600;color:#111827;font-size:.875rem;">${a.name}</div>
                            ${a.type ? '<span style="font-size:.75rem;background:#f5f3ff;color:#7c3aed;padding:.125rem .5rem;border-radius:9999px;font-weight:600;">' + a.type + '</span>' : ''}
                            ${a.notes ? '<div style="font-size:.8rem;color:#9ca3af;margin-top:.25rem;">' + a.notes + '</div>' : ''}
                            </div>
                        </div>`;
                    });
                } else {
                    accHtml += '<p style="color:#9ca3af;font-size:.875rem;">Belum ada akomodasi.</p>';
                }
                accHtml += '</div>';

                panel.innerHTML = itiHtml + accHtml;
                contentEl.appendChild(panel);
            });
        });
}

function closeTripModal() {
    document.getElementById('tripModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

function switchTab(idx, total) {
    for (let i = 0; i < total; i++) {
        const p = document.getElementById('tab-panel-' + i);
        if (p) p.style.display = i === idx ? 'grid' : 'none';
    }
    document.querySelectorAll('.tab-btn').forEach((b, i) => b.classList.toggle('active', i === idx));
}
</script>

</body>
</html>

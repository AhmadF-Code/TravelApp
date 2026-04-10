<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Konfirmasi Pesanan — AVRA Tour</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
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
        body { font-family: 'Plus Jakarta Sans', sans-serif; -webkit-font-smoothing: antialiased; }
        .text-gradient {
            background: linear-gradient(135deg, #9B8EC7, #F45B26);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="{{ !isset($booking) ? 'bg-gray-50' : 'bg-[#F9FAFB]' }} text-gray-900">

<!-- Navigation -->
<nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
        <!-- Logo AVRA Tour -->
        <a href="{{ route('home') }}" class="flex items-center gap-3 shrink-0">
            <svg width="40" height="40" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M75 20 L95 10 L85 35 Z" fill="#9B8EC7"/>
                <path d="M75 20 L95 10 L85 35 C80 30 75 25 75 20Z" fill="#7a6eb0"/>
                <path d="M15 95 C25 60 50 40 75 20 C65 50 45 70 25 95 Z" fill="#9B8EC7" opacity="0.95"/>
                <path d="M22 100 C35 68 58 50 80 30 C68 60 50 80 30 105 Z" fill="#BDA6CE" opacity="0.80"/>
                <path d="M30 106 C45 78 65 62 85 45 C75 72 58 90 38 110 Z" fill="#D4C2E8" opacity="0.60"/>
                <path d="M38 110 C55 88 72 74 90 60 C82 82 66 98 46 112 Z" fill="#F45B26" opacity="0.95"/>
                <path d="M46 112 C62 95 78 83 95 72 C88 90 74 104 56 114 Z" fill="#FAE251" opacity="0.85"/>
            </svg>
            <span class="text-2xl font-black tracking-tighter" style="color:#9B8EC7;">AVRA<span style="color:#F45B26;">TOUR</span></span>
        </a>
        <a href="{{ route('home') }}" class="text-sm font-bold text-gray-500 hover:text-primary transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Beranda
        </a>
    </div>
</nav>

<div class="py-12 md:py-20">
    <div class="max-w-6xl mx-auto px-6">
        
        @if(session('message'))
            <div class="mb-10 p-5 bg-green-50 border border-green-100 rounded-3xl text-green-800 flex items-center gap-4 shadow-sm animate-fade-in">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center border border-green-200">
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
                </div>
                <span class="font-bold text-sm">{{ session('message') }}</span>
            </div>
        @endif

        @if(!isset($booking))
            <!-- Search Form: Premium Light Mode -->
            <div class="flex flex-col items-center justify-center min-h-[50vh]">
                <div class="text-center mb-10">
                    <h2 class="text-primary font-black uppercase tracking-[0.3em] text-xs mb-4">Official Verification</h2>
                    <h1 class="text-4xl font-black text-gray-950 tracking-tight mb-4">Cek Status Pesanan</h1>
                    <p class="text-gray-500 text-sm max-w-sm mx-auto leading-relaxed">Silakan masukkan kode booking dan email yang terdaftar untuk verifikasi e-ticket Anda.</p>
                </div>

                <div class="w-full max-w-md bg-white border border-gray-100 rounded-5xl shadow-2xl shadow-gray-200/50 p-12">
                    <form action="{{ route('booking.search') }}" method="POST" class="space-y-8">
                        @csrf
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Nomor Booking (Grup)</label>
                            <input type="text" name="code" required class="block w-full px-6 py-4 bg-gray-50 border-2 border-gray-50 rounded-2xl focus:bg-white focus:border-secondary outline-none transition-all text-gray-900 font-mono tracking-widest text-lg" placeholder="TRV-XXXX">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Email Pendaftaran</label>
                            <input type="email" name="email" required class="block w-full px-6 py-4 bg-gray-50 border-2 border-gray-50 rounded-2xl focus:bg-white focus:border-secondary outline-none transition-all text-gray-900 text-sm" placeholder="nama@email.com">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">ID Kursi / Tiket Individu</label>
                            <input type="text" name="traveler_code" required class="block w-full px-6 py-4 bg-gray-50 border-2 border-gray-50 rounded-2xl focus:bg-white focus:border-secondary outline-none transition-all text-gray-900 font-mono tracking-widest text-lg" placeholder="TKT-XXXX">
                            <p class="text-[9px] text-gray-400 font-bold mt-2 ml-1 italic">*Periksa ID Kursi di halaman checkout setelah melunasi pembayaran.</p>
                        </div>
                        
                        <div class="pt-4">
                            <button type="submit" class="w-full text-white font-black py-5 rounded-2xl shadow-xl shadow-secondary/20 hover:shadow-secondary/40 hover:-translate-y-1 active:translate-y-0 transition-all text-xs uppercase tracking-[0.25em]" style="background-color: #F45B26;">
                                Verifikasi Sekarang
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="mt-12 text-[10px] text-gray-400 font-bold uppercase tracking-[0.3em] flex items-center gap-3">
                    <span class="w-8 h-[1px] bg-gray-200"></span>
                    AVRA Tour Travel System &copy; {{ date('Y') }}
                    <span class="w-8 h-[1px] bg-gray-200"></span>
                </div>
            </div>
        @else
            <!-- Booking Details: Flipped Dashboard Layout (65/35 Right Summary) -->
            <div class="flex flex-col-reverse lg:flex-row-reverse gap-10 items-start">
                
                <!-- Sidebar (Summary): Now on the RIGHT -->
                <div class="lg:w-[35%] w-full sticky top-24">
                    <div class="bg-gray-900 rounded-5xl shadow-2xl p-10 text-white border border-gray-800">
                        <div class="mb-8">
                            <span class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] block mb-5">Selected Trip Package</span>
                            <div class="relative h-56 rounded-4xl overflow-hidden mb-8 shadow-2xl">
                                <img src="{{ $booking->trip->image_url }}" alt="{{ $booking->trip->title }}" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></div>
                                <div class="absolute bottom-6 left-8 right-8">
                                    <h3 class="font-black text-2xl leading-tight uppercase tracking-tight">{{ $booking->trip->title }}</h3>
                                </div>
                            </div>
                            
                            <div class="space-y-5 mb-10 px-2">
                                <div class="flex justify-between items-center text-xs font-bold">
                                    <span class="text-gray-500 uppercase tracking-widest">Harga / Orang</span>
                                    <span class="text-sm">Rp {{ number_format($booking->trip->price, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center text-xs font-bold pb-6 border-b border-gray-800 uppercase tracking-widest">
                                    <span class="text-gray-500">Jumlah Traveler</span>
                                    <span class="text-sm">{{ $booking->pax }} Pax</span>
                                </div>
                                <div class="pt-6 flex flex-col items-center">
                                    <span class="text-[11px] font-black uppercase text-gray-500 tracking-[0.3em] mb-2">Total Pembayaran</span>
                                    <div class="text-4xl font-black text-accent">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                @if(strtolower($booking->status) !== 'paid')
                                    @if($booking->midtrans_redirect_url)
                                        <a href="{{ $booking->midtrans_redirect_url }}" class="block w-full text-center text-white font-black py-5 rounded-3xl transition-all shadow-xl hover:opacity-90 active:scale-95 text-xs uppercase tracking-[0.3em]" style="background: linear-gradient(135deg, #9B8EC7, #F45B26);">
                                            Lanjut Pembayaran
                                        </a>
                                    @else
                                        <form action="{{ route('booking.repay', $booking->booking_code) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="w-full text-center bg-white text-gray-900 font-black py-5 rounded-3xl opacity-90 hover:opacity-100 transition active:scale-95 text-xs uppercase tracking-[0.3em]">
                                                Buat Invoice
                                            </button>
                                        </form>
                                    @endif
                                    <a href="https://wa.me/6281234567890?text=Konfirmasi Booking {{ $booking->booking_code }}" target="_blank" class="block w-full text-center border border-gray-800 text-gray-500 font-bold py-4 rounded-3xl hover:border-white hover:text-white transition text-[10px] tracking-widest uppercase">
                                         Customer Support
                                    </a>
                                @else
                                    <button class="w-full text-gray-900 font-black py-5 rounded-3xl transition-all shadow-xl hover:opacity-90 flex items-center justify-center gap-3 bg-white active:scale-95 text-xs uppercase tracking-[0.3em]" onclick="window.print()">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        Cetak E-Ticket
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Info Card: Now on the LEFT -->
                <div class="lg:w-[65%] w-full space-y-10">
                    <div class="bg-white rounded-5xl shadow-2xl shadow-gray-200/50 border border-gray-50 overflow-hidden">
                        <!-- Card Header -->
                        <div class="p-10 md:p-14 pb-0">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8 mb-16">
                                <div>
                                    <div class="text-[11px] font-black text-gray-400 uppercase tracking-[0.4em] mb-3">Booking Identifier</div>
                                    <h2 class="text-4xl md:text-6xl font-black text-gray-950 font-mono tracking-tighter leading-none">{{ $booking->booking_code }}</h2>
                                </div>
                                <div class="flex flex-col items-end gap-3 shrink-0">
                                    @if(strtolower($booking->status) === 'paid')
                                        <div class="bg-green-50 text-green-600 px-8 py-3 rounded-2xl font-black text-xs uppercase tracking-widest border border-green-100 flex items-center gap-3">
                                            <div class="w-2 h-2 rounded-full bg-green-500"></div> Lunas / Terbayar
                                        </div>
                                    @else
                                        <div class="bg-amber-50 text-amber-600 px-8 py-3 rounded-2xl font-black text-xs uppercase tracking-widest border border-amber-100 flex items-center gap-3">
                                            <div class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></div> Menunggu Bayar
                                        </div>
                                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">
                                            Batas: <span id="countdown-timer" class="text-secondary" data-expires="{{ $booking->created_at->addHour()->toIso8601String() }}">00:00:00</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-16 px-2">
                                <div class="space-y-4">
                                    <div class="text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-3">Data Pemesan</div>
                                    <div class="text-2xl font-black text-gray-950 uppercase tracking-tight leading-tight">{{ $booking->customer_name }}</div>
                                    <div class="flex flex-col gap-1.5 pt-1">
                                        <span class="text-sm text-gray-500 font-medium">{{ $booking->customer_email }}</span>
                                        <span class="text-sm text-gray-500 font-medium">{{ $booking->customer_phone }}</span>
                                        @if($booking->primary_passport)
                                            <span class="text-sm font-bold text-primary mt-1">Passport: {{ $booking->primary_passport }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="space-y-4">
                                    <div class="text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-3">Periode Trip</div>
                                    <div class="text-2xl font-black text-gray-950 uppercase tracking-tight leading-tight">
                                        {{ \Carbon\Carbon::parse($booking->schedule->departure_date)->format('d M') }} — {{ \Carbon\Carbon::parse($booking->schedule->return_date)->format('d M Y') }}
                                    </div>
                                    <div class="text-xs text-primary font-black uppercase tracking-widest pt-1">Konfirmasi Jadwal Tetap</div>
                                </div>
                            </div>
                            @php 
                                $additionals = $booking->travelers()->where('is_primary', false)->get(); 
                            @endphp
                            @if($additionals->count() > 0)
                                <div class="mt-12 px-2 border-t border-gray-50 pt-10">
                                    <div class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6">Manifest Peserta Tambahan</div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        @foreach($additionals as $trav)
                                            <div class="flex items-center gap-4 bg-gray-50/50 p-5 rounded-3xl border border-gray-100 group hover:bg-white hover:shadow-xl hover:shadow-primary/5 transition-all">
                                                <div class="w-12 h-12 bg-white rounded-2xl flex flex-col items-center justify-center font-black text-primary border border-gray-50">
                                                    <span class="text-[8px] uppercase tracking-tighter text-gray-400">ID</span>
                                                    <span class="leading-none text-sm">{{ $loop->iteration + 1 }}</span>
                                                </div>
                                                <div class="flex-grow">
                                                    <div class="font-bold text-gray-900 leading-none">{{ $trav->name }}</div>
                                                    <div class="flex items-center gap-2 mt-2">
                                                        <span class="text-[9px] font-black text-gray-400 font-mono tracking-tighter">{{ $trav->traveler_code }}</span>
                                                        @if($trav->passport_number)
                                                            <span class="text-[9px] font-bold text-primary/60 border-l border-gray-200 pl-2">Pass: {{ $trav->passport_number }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="text-right shrink-0">
                                                    <span class="text-[8px] font-black {{ $trav->status === 'paid' ? 'text-green-500' : 'text-amber-500' }} uppercase tracking-widest">{{ $trav->status }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Spacer / Section Divider -->
                        <div class="border-t border-gray-50 mx-10"></div>

                        <!-- Manifest Locations -->
                        <div class="p-10 md:p-14 pt-12">
                            <div class="flex items-center gap-4 mb-10">
                                <span class="text-xs font-black text-gray-400 uppercase tracking-[0.4em]">Detailed Destination Manifest</span>
                                <div class="flex-grow h-[1px] bg-gray-50"></div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                @foreach($booking->trip->locations as $loc)
                                    <div class="bg-gray-50/50 rounded-[2.5rem] p-10 border border-transparent hover:border-primary/20 hover:bg-white transition-all duration-500 group shadow-sm hover:shadow-xl hover:shadow-primary/5">
                                        <div class="flex items-center gap-4 mb-8">
                                            <div class="text-5xl transition-transform group-hover:scale-110 duration-500">{{ $loc->flag_emoji }}</div>
                                            <div>
                                                <div class="font-black text-gray-950 text-xl leading-none uppercase tracking-tighter">{{ $loc->country }}</div>
                                                <div class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.3em] mt-2">{{ $loc->city ?: 'Tujuan Utama' }}</div>
                                            </div>
                                        </div>
                                        <div class="space-y-4">
                                            @foreach($loc->itineraries as $itn)
                                                <div class="flex gap-4 items-start">
                                                    <span class="font-black text-secondary text-sm shrink-0 pt-0.5">D{{ $itn->day }}</span>
                                                    <p class="text-gray-600 font-medium text-sm leading-relaxed">{{ $itn->title }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @if(strtolower($booking->status) !== 'paid')
                        <div class="bg-amber-50 rounded-4xl p-10 border border-amber-100 flex items-start gap-8 shadow-sm">
                            <div class="w-14 h-14 bg-amber-500 text-white rounded-3xl flex items-center justify-center shrink-0 shadow-lg shadow-amber-500/30">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="pt-2">
                                <h4 class="text-amber-900 font-black uppercase text-xs tracking-widest mb-2">Instruksi Penting</h4>
                                <p class="text-amber-800 text-sm leading-relaxed font-medium">Sistem akan membatalkan pesanan secara otomatis jika pembayaran tidak diterima dalam kurun waktu 1 jam. Silakan lakukan pembayaran segera.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<footer class="py-12 border-t border-gray-100">
    <div class="max-w-6xl mx-auto px-6 text-center">
        <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.4em]">&copy; {{ date('Y') }} AVRA Tour • Premium Travel Management System</p>
    </div>
</footer>

<script>
    function updateCountdown() {
        const timerElement = document.getElementById('countdown-timer');
        if (!timerElement) return;

        const expiresAt = new Date(timerElement.getAttribute('data-expires')).getTime();
        const now = new Date().getTime();
        const distance = expiresAt - now;

        if (distance < 0) {
            timerElement.innerHTML = "EXPIRED";
            setTimeout(() => location.reload(), 2000); 
            return;
        }

        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        timerElement.innerHTML = 
            (hours > 0 ? String(hours).padStart(2, '0') + ":" : "") + 
            String(minutes).padStart(2, '0') + ":" + 
            String(seconds).padStart(2, '0');
    }
    setInterval(updateCountdown, 1000);
    updateCountdown();
</script>

</body>
</html>

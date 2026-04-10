<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Selesaikan Pembayaran — AVRA Tour</title>
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
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #F8FAFC; }
    </style>
</head>
<body class="antialiased text-gray-900">

    <div class="max-w-xl mx-auto px-4 py-12 md:py-20">
        
        <!-- Status Alert if Any -->
        @if(session('message'))
            <div class="mb-6 p-4 bg-green-50 border border-green-100 rounded-2xl text-green-700 text-sm font-bold shadow-sm">
                 {{ session('message') }}
            </div>
        @endif

        <!-- Countdown Header: Shopee Style -->
        <div class="bg-white rounded-4xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden mb-8">
            <div class="bg-[#F45B26]/5 p-8 text-center border-b border-[#F45B26]/10">
                <p class="text-[10px] font-black uppercase tracking-[0.4em] text-secondary mb-3">Selesaikan Pembayaran Dalam</p>
                <div class="text-4xl md:text-5xl font-black text-secondary tracking-tighter" id="checkout-timer" data-expires="{{ $booking->created_at->addHour()->toIso8601String() }}">
                    00:00:00
                </div>
            </div>
            <div class="p-4 bg-amber-50 flex items-center justify-center gap-2">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p class="text-[10px] font-bold text-amber-800 uppercase tracking-widest">Pesanan Otomatis Dibatalkan Jika Waktu Habis</p>
            </div>
        </div>

        <!-- Order Summary Card -->
        <div class="bg-white rounded-4xl shadow-xl shadow-gray-200/50 border border-gray-100 p-8 mb-8">
            <div class="flex items-start gap-5 mb-8">
                <div class="w-20 h-20 rounded-2xl overflow-hidden shadow-sm shrink-0">
                    <img src="{{ $booking->trip->image_url }}" alt="" class="w-full h-full object-cover">
                </div>
                <div>
                    <h3 class="font-black text-lg text-gray-900 leading-tight uppercase">{{ $booking->trip->title }}</h3>
                    <div class="flex items-center gap-2 text-xs font-bold text-gray-400 mt-2">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        {{ \Carbon\Carbon::parse($booking->schedule->departure_date)->format('d M') }} — {{ \Carbon\Carbon::parse($booking->schedule->return_date)->format('d M Y') }}
                    </div>
                </div>
            </div>

            <div class="space-y-4 border-t border-gray-50 pt-6">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-400 font-medium">Nomor Booking (Grup)</span>
                    <span class="font-black text-gray-950 font-mono tracking-widest">{{ $booking->booking_code }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-400 font-medium">Email Pemesan</span>
                    <span class="font-bold text-gray-700">{{ $booking->customer_email }}</span>
                </div>
                
                <!-- NEW: Participant List -->
                <div class="mt-8 pt-6 border-t border-gray-50">
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 mb-4">Daftar Kursi & Peserta</p>
                    <div class="space-y-3">
                        @foreach($booking->travelers as $t)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100">
                            <div class="flex items-center gap-3">
                                <div class="w-6 h-6 rounded-full {{ $t->is_primary ? 'bg-primary' : 'bg-gray-200' }} flex items-center justify-center text-[10px] text-white font-black">
                                    {{ $loop->iteration }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-gray-900">{{ $t->name }}</span>
                                    <span class="text-[9px] font-black text-primary/60 uppercase tracking-widest">{{ $t->is_primary ? 'Pemesan Utama' : 'Peserta Tambahan' }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-black text-gray-950 font-mono tabular-nums tracking-tighter">{{ $t->traveler_code }}</p>
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">ID Kursi</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-between items-center text-sm pt-4">
                    <span class="text-gray-400 font-medium">Total Peserta</span>
                    <span class="font-bold text-gray-700">{{ $booking->pax }} Orang</span>
                </div>
            </div>
        </div>

        <!-- Payment Portal: Embedded Midtrans Shopee Style -->
        <div class="bg-white rounded-5xl shadow-2xl shadow-secondary/5 border-2 border-secondary/10 overflow-hidden mb-12">
            <div class="p-8 border-b border-gray-50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-secondary/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    </div>
                    <span class="text-sm font-black uppercase tracking-widest text-gray-950">Portal Pembayaran Aman</span>
                </div>
                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total: Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</div>
            </div>

            <div class="relative" style="height: 600px;">
                @if($booking->midtrans_redirect_url)
                    <iframe src="{{ $booking->midtrans_redirect_url }}" 
                            class="w-full h-full border-none"
                            allow="payment">
                    </iframe>
                @else
                    <div class="w-full h-full flex flex-col items-center justify-center p-12 text-center">
                        <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div style="font-weight:700;color:#1e40af;font-size:.875rem;margin-bottom:.2rem;">Pembayaran Aman via Midtrans</div>
                        <p class="text-sm text-gray-500 mb-8 max-w-xs">Kami tidak dapat memuat rincian pembayaran. Silakan coba buat kembali.</p>
                        <form action="{{ route('booking.repay', $booking->booking_code) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-gray-950 text-white font-black px-8 py-4 rounded-2xl text-xs uppercase tracking-widest hover:bg-primary transition-all active:scale-95">
                                Perbarui Link Pembayaran
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <div class="p-4 bg-gray-50 text-center">
                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-[0.3em]">Pembayaran akan terintegrasi otomatis setelah transaksi berhasil</p>
            </div>
        </div>

        <div class="mt-8 flex flex-col items-center gap-6">
            <a href="{{ route('home') }}" class="text-xs font-black text-gray-400 hover:text-secondary uppercase tracking-[0.3em] transition-colors">
                Kembali ke Beranda
            </a>
            <div class="flex items-center gap-4 text-[9px] font-black text-gray-300 uppercase tracking-[0.4em]">
                <span class="h-[1px] w-8 bg-gray-200"></span>
                AVRA Tour Secure Checkout
                <span class="h-[1px] w-8 bg-gray-200"></span>
            </div>
        </div>
    </div>

    <script>
        function updateTimer() {
            const timerEl = document.getElementById('checkout-timer');
            if (!timerEl) return;

            const expiresAt = new Date(timerEl.getAttribute('data-expires')).getTime();
            const now = new Date().getTime();
            const diff = expiresAt - now;

            if (diff <= 0) {
                timerEl.innerHTML = "00:00:00";
                // Redirect ke Home jika Expired
                window.location.href = "{{ route('home') }}?warning=Waktu+pembayaran+habis";
                return;
            }

            const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const s = Math.floor((diff % (1000 * 60)) / 1000);

            timerEl.innerHTML = 
                String(h).padStart(2, '0') + ":" + 
                String(m).padStart(2, '0') + ":" + 
                String(s).padStart(2, '0');
        }

        setInterval(updateTimer, 1000);
        updateTimer();
    </script>
</body>
</html>

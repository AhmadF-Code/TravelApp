<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pesan Trip: {{ $trip->title }} — {{ config('app.name') }}</title>
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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background:#f8fafc; }
        .checkout-label { display:block;font-size:.875rem;font-weight:600;color:#374151;margin-bottom:.5rem; }
        .checkout-input {
            width:100%; padding:.875rem 1rem; border:1.5px solid #e5e7eb; border-radius:.875rem;
            font-size:.9375rem; color:#111827; outline:none; transition:border-color .2s, box-shadow .2s;
            background:#fff; box-sizing:border-box;
        }
        .checkout-input:focus { border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,.1); }
        .checkout-input.has-error { border-color:#ef4444; background:#fef2f2; }
        .phone-wrapper { position:relative; }
        .phone-prefix {
            position:absolute; left:1rem; top:50%; transform:translateY(-50%);
            font-weight:600; color:#6b7280; pointer-events:none;
        }
        .phone-wrapper .checkout-input { padding-left:3.5rem; }
        .select-input {
            width:100%; padding:.875rem 1rem; border:1.5px solid #e5e7eb; border-radius:.875rem;
            font-size:.9375rem; color:#111827; background:#fff; outline:none;
            transition:border-color .2s, box-shadow .2s; box-sizing:border-box;
        }
        .select-input:focus { border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,.1); }
        .summary-card {
            background:#fff; border-radius:1.5rem; overflow:hidden;
            box-shadow:0 4px 24px rgba(0,0,0,.07); position:sticky; top:2rem;
        }
        .badge-domestic { background:#eff6ff;color:#2563eb; }
        .badge-international { background:#eef2ff;color:#4f46e5; }
        .price-total-row { display:flex;justify-content:space-between;align-items:center;padding:.75rem 0;border-bottom:1px dashed #f3f4f6; }
        .submit-btn {
            width:100%;padding:1.125rem;border-radius:1rem;background:linear-gradient(135deg,#2563eb,#4f46e5);
            color:#fff;font-weight:700;font-size:1.05rem;border:none;cursor:pointer;
            display:flex;align-items:center;justify-content:center;gap:.625rem;
            box-shadow:0 8px 24px rgba(37,99,235,.35);transition:transform .2s,box-shadow .2s;
        }
        .submit-btn:hover { transform:translateY(-2px);box-shadow:0 12px 32px rgba(37,99,235,.45); }
        .submit-btn:disabled { opacity:.6;cursor:not-allowed;transform:none; }
        .itinerary-pill {
            display:inline-flex;align-items:center;gap:.375rem;
            background:#f0f9ff;color:#0369a1;font-size:.8rem;font-weight:600;
            padding:.3rem .75rem;border-radius:9999px;margin:.2rem;
        }
    </style>
</head>
<body>

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
                <a href="{{ route('destinations') }}" class="text-gray-600 hover:text-primary px-4 py-2 rounded-full text-sm font-medium transition-colors hover:bg-primary/10">Destinasi</a>
                <a href="{{ route('schedules.index') }}" class="text-gray-600 hover:text-primary px-4 py-2 rounded-full text-sm font-medium transition-colors hover:bg-primary/10">Jadwal</a>
                <a href="{{ route('booking.cek') }}" class="text-primary bg-primary/10 px-4 py-2 rounded-full text-sm font-bold">Cek Pesanan</a>
            </div>
        </div>
    </div>
</nav>

<div class="max-w-6xl mx-auto px-4 pt-32 pb-16">
    <!-- Status Banners -->
    @if($errors->any())
    <div style="background:#fef2f2;border:1.5px solid #fecaca;color:#b91c1c;border-radius:1rem;padding:1rem 1.25rem;margin-bottom:1.5rem;">
        <div style="font-weight:700;margin-bottom:.5rem;">⚠️ Mohon periksa kesalahan berikut:</div>
        <ul style="padding-left:1.25rem;margin:0;">
            @foreach($errors->all() as $e)<li style="font-size:.875rem;">{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    @if(request('booking')==='success')
    <div style="background:#f0fdf4;border:1.5px solid #86efac;color:#15803d;border-radius:1rem;padding:1rem 1.25rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:.75rem;">
        <svg style="width:1.5rem;min-width:1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <div><strong>Pembayaran Berhasil!</strong> Terima kasih telah memesan bersama kami. Konfirmasi dikirim ke email Anda.</div>
    </div>
    @elseif(request('booking')==='failed')
    <div style="background:#fffbeb;border:1.5px solid #fde68a;color:#92400e;border-radius:1rem;padding:1rem 1.25rem;margin-bottom:1.5rem;">
        ⚠️ Pembayaran gagal atau dibatalkan. Silakan coba lagi.
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 items-start" 
         x-data="{ 
            pax: {{ old('pax', 1) }},
            price: {{ $trip->price }},
            discount: 0,
            promoMsg: '',
            promoValid: null,
            async checkPromo() {
                const code = this.$refs.promoInput.value.trim().toUpperCase();
                if (!code) {
                    this.discount = 0;
                    this.promoMsg = '';
                    this.promoValid = null;
                    return;
                }
                this.promoMsg = 'Mengecek...';
                try {
                    const res = await fetch(`/api/check-promo/${code}`);
                    const data = await res.json();
                    if (data.valid) {
                        this.discount = data.discount;
                        this.promoMsg = data.message;
                        this.promoValid = true;
                    } else {
                        this.discount = 0;
                        this.promoMsg = data.message;
                        this.promoValid = false;
                    }
                } catch (e) {
                    this.promoMsg = 'Gagal memvalidasi.';
                    this.promoValid = false;
                }
            },
            formatIDR(num) {
               return 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
            }
         }">
        <!-- LEFT: Booking Form (3/5) -->
        <div class="lg:col-span-3">
            <div style="background:#fff;border-radius:1.5rem;padding:2rem;box-shadow:0 4px 24px rgba(0,0,0,.07);">
                <h1 style="font-size:1.5rem;font-weight:800;color:#111827;margin:0 0 .375rem;">Formulir Pemesanan</h1>
                <p style="color:#6b7280;font-size:.9rem;margin:0 0 2rem;">Lengkapi data Anda untuk melanjutkan ke pembayaran.</p>

                <form action="{{ route('trip.book', $trip->slug) }}" method="POST" id="bookingForm">
                    @csrf
                    {{-- Honeypot field --}}
                    <div style="display:none;">
                        <input type="text" name="website_url" value="">
                        <input type="hidden" name="form_timestamp" value="{{ time() }}">
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">
                        <div style="grid-column:1/-1;">
                            <label class="checkout-label">Pilih Cabang Pembelian AVRA</label>
                            <select name="branch_id" class="select-input" required>
                                <option value="" disabled selected>Pilih Cabang Terdekat...</option>
                                @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id')==$branch->id ? 'selected' : '' }}>
                                    Office AFRA: {{ $branch->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div style="grid-column:1/-1;">
                            <label class="checkout-label">Pilih Tanggal Keberangkatan</label>
                            <select name="schedule_id" class="select-input" required id="scheduleSelect">
                                @forelse($trip->schedules as $schedule)
                                <option value="{{ $schedule->id }}"
                                    data-departure="{{ \Carbon\Carbon::parse($schedule->departure_date)->format('d M Y') }}"
                                    data-return="{{ \Carbon\Carbon::parse($schedule->return_date)->format('d M Y') }}"
                                    {{ old('schedule_id')==$schedule->id ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::parse($schedule->departure_date)->format('d M Y') }}
                                    → {{ \Carbon\Carbon::parse($schedule->return_date)->format('d M Y') }}
                                    · Sisa {{ $schedule->quota }} slot
                                </option>
                                @empty
                                <option value="" disabled selected>Tidak ada jadwal tersedia.</option>
                                @endforelse
                            </select>
                        </div>

                        <div>
                            <label class="checkout-label" for="paxInput">Jumlah Peserta (Pax)</label>
                            <input type="number" name="pax" id="paxInput" min="1" max="100"
                                   x-model.number="pax"
                                   class="checkout-input {{ $errors->has('pax') ? 'has-error' : '' }}"
                                   required>
                        </div>

                        <div>
                            <label class="checkout-label" for="nameInput">Nama Lengkap (Primary)</label>
                            <input type="text" name="customer_name" id="nameInput"
                                   value="{{ old('customer_name') }}"
                                   class="checkout-input {{ $errors->has('customer_name') ? 'has-error' : '' }}"
                                   placeholder="Ahmad Fauzi" required>
                        </div>

                        @if(!$trip->is_domestic)
                        <div style="grid-column:1/-1;">
                            <label class="checkout-label" for="passportInput">Nomor Passport (Primary)</label>
                            <input type="text" name="primary_passport" id="passportInput"
                                   value="{{ old('primary_passport') }}"
                                   class="checkout-input {{ $errors->has('primary_passport') ? 'has-error' : '' }}"
                                   placeholder="A12345678" required>
                        </div>
                        @endif

                        <div style="grid-column:1/-1;">
                            <label class="checkout-label" for="emailInput">Alamat Email</label>
                            <input type="email" name="customer_email" id="emailInput"
                                   value="{{ old('customer_email') }}"
                                   class="checkout-input {{ $errors->has('customer_email') ? 'has-error' : '' }}"
                                   placeholder="ahmad@contoh.com" required>
                        </div>

                        <div style="grid-column:1/-1;">
                            <label class="checkout-label" for="phoneInput">Nomor HP / WhatsApp</label>
                            <div class="phone-wrapper">
                                <span class="phone-prefix">+62</span>
                                <input type="tel" name="customer_phone" id="phoneInput"
                                       value="{{ old('customer_phone') }}"
                                       class="checkout-input {{ $errors->has('customer_phone') ? 'has-error' : '' }}"
                                       placeholder="812 3456 7890" pattern="[0-9]{8,13}" required>
                            </div>
                        </div>

                        <div style="grid-column:1/-1;">
                            <label class="checkout-label" for="promoInput">Punya Kode Promo? (Opsional)</label>
                            <input type="text" name="promo_code_input" id="promoInput"
                                   x-ref="promoInput"
                                   value="{{ old('promo_code_input') }}"
                                   @blur="checkPromo()"
                                   @keyup.enter="checkPromo()"
                                   :class="{'has-error': promoValid === false, 'border-green-500': promoValid === true}"
                                   class="checkout-input"
                                   placeholder="Masukkan kode promo di sini..." style="text-transform:uppercase;">
                             <div x-text="promoMsg" :class="{'text-red-600': promoValid === false, 'text-green-600': promoValid === true}" class="text-xs mt-1 font-bold"></div>
                        </div>

                        <!-- Additional Travelers Section -->
                        <template x-if="pax > 1">
                            <div style="grid-column:1/-1; border-top: 1.5px solid #f3f4f6; padding-top: 1.25rem; margin-top: 1.5rem;" class="space-y-8">
                                <template x-for="i in parseInt(pax) - 1" :key="i">
                                    <div class="bg-gray-50/50 p-6 rounded-3xl border border-gray-100 mb-8 overflow-hidden relative">
                                        <div class="flex items-center gap-3 mb-6">
                                            <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white text-xs font-bold" x-text="i + 1"></div>
                                            <div class="text-sm font-black text-gray-800 uppercase tracking-widest">Data Peserta <span x-text="i + 1"></span></div>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label class="checkout-label">Nama Lengkap</label>
                                                <input type="text" :name="'additional_names[' + i + ']'" class="checkout-input" required placeholder="Sesuai KTP/Passport">
                                            </div>
                                            <div>
                                                <label class="checkout-label">Alamat Email</label>
                                                <input type="email" :name="'additional_emails[' + i + ']'" class="checkout-input" required placeholder="contoh@mail.com">
                                            </div>
                                            <div>
                                                <label class="checkout-label">Nomor WhatsApp</label>
                                                <div class="phone-wrapper">
                                                    <span class="phone-prefix">+62</span>
                                                    <input type="tel" :name="'additional_phones[' + i + ']'" class="checkout-input" required placeholder="8xx xxxx xxxx">
                                                </div>
                                            </div>
                                            @if(!$trip->is_domestic)
                                            <div>
                                                <label class="checkout-label">Nomor Passport</label>
                                                <input type="text" :name="'additional_passports[' + i + ']'" class="checkout-input" required placeholder="Contoh: A12345678">
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    <div style="background:linear-gradient(135deg,#eff6ff,#eef2ff);border-radius:1rem;padding:1rem 1.25rem;margin-bottom:1.5rem;display:flex;gap:.75rem;align-items:flex-start;">
                        <svg style="width:1.25rem;min-width:1.25rem;color:#3b82f6;margin-top:.1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        <div>
                            <div style="font-weight:700;color:#1e40af;font-size:.875rem;margin-bottom:.2rem;">Pembayaran Aman via Midtrans</div>
                            <div style="font-size:.8rem;color:#3730a3;">Mendukung Transfer Bank, GoPay, OVO, Dana, QRIS, Kartu Kredit, dan lebih banyak lagi.</div>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn" id="submitBtn" {{ $trip->schedules->isEmpty() ? 'disabled' : '' }}>
                        <span>Lanjutkan ke Pembayaran</span>
                        <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </form>
            </div>
        </div>

        <!-- RIGHT: Trip Summary (2/5) -->
        <div class="lg:col-span-2">
            <div class="summary-card">
                <div style="position:relative;height:14rem;overflow:hidden;">
                    <img src="{{ $trip->image_url }}" style="width:100%;height:100%;object-fit:cover;" alt="{{ $trip->title }}">
                    <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(10,15,30,.8),transparent 50%);"></div>
                    <div style="position:absolute;bottom:0;left:0;padding:1.25rem;">
                        <span style="display:inline-block;padding:.25rem .75rem;border-radius:9999px;font-size:.75rem;font-weight:700;{{ $trip->is_domestic ? 'background:#dbeafe;color:#1d4ed8;' : 'background:#e0e7ff;color:#4338ca;' }}margin-bottom:.5rem;">
                            {{ $trip->is_domestic ? '🏠 Domestik' : '✈️ Internasional' }}
                        </span>
                        <h2 style="font-size:1.25rem;font-weight:800;color:#fff;margin:0;">{{ $trip->title }}</h2>
                    </div>
                </div>

                <div style="padding:1.5rem;">
                    <!-- Destination info -->
                    @if($trip->destination_country)
                    <div style="display:flex;align-items:center;gap:.5rem;color:#6b7280;font-size:.875rem;margin-bottom:1rem;">
                        <svg style="width:1rem;height:1rem;color:#3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        {{ $trip->destination_country }}
                        @if($trip->duration_days)
                            · {{ $trip->duration_days }} Hari
                        @endif
                    </div>
                    @endif

                    <!-- Locations list -->
                    @if($trip->locations->count())
                    <div style="margin-bottom:1.25rem;">
                        <div style="font-size:.8rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#9ca3af;margin-bottom:.5rem;">Destinasi</div>
                        @foreach($trip->locations as $loc)
                        <div style="display:flex;align-items:center;gap:.5rem;padding:.375rem 0;font-size:.875rem;color:#374151;">
                            <span>{{ $loc->flag_emoji }}</span>
                            <span style="font-weight:600;">{{ $loc->country }}</span>
                            @if($loc->city)
                                <span style="color:#9ca3af;">— {{ $loc->city }}</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <!-- Price calc -->
                    <div style="border-top:1.5px solid #f3f4f6;padding-top:1rem;margin-top:.5rem;">
                        <div class="price-total-row">
                            <span style="font-size:.875rem;color:#6b7280;">Harga per Pax</span>
                            <span style="font-weight:700;color:#111827;" x-text="formatIDR(price)"></span>
                        </div>
                        <div class="price-total-row">
                            <span style="font-size:.875rem;color:#6b7280;">Jumlah Pax</span>
                            <span style="font-weight:700;color:#111827;" x-text="pax"></span>
                        </div>
                        <!-- Promo row -->
                        <div class="price-total-row" x-show="discount > 0" x-cloak style="color:#16a34a;">
                            <span style="font-size:.875rem;">Potongan Promo</span>
                            <span style="font-weight:700;" x-text="'- ' + formatIDR(discount)"></span>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;padding:1rem 0 0;">
                            <span style="font-weight:700;color:#111827;">Total Pembayaran</span>
                            <span style="font-size:1.5rem;font-weight:900;color:#2563eb;" x-text="formatIDR((pax * price) - discount)"></span>
                        </div>
                    </div>

                    <!-- Help -->
                    <a href="https://wa.me/6281234567890?text=Halo, saya ingin bertanya tentang paket {{ urlencode($trip->title) }}" target="_blank"
                       style="display:flex;align-items:center;justify-content:center;gap:.5rem;width:100%;padding:.75rem;border-radius:.875rem;background:#dcfce7;color:#15803d;font-weight:700;font-size:.875rem;text-decoration:none;margin-top:1rem;transition:background .2s;"
                       onmouseover="this.style.background='#bbf7d0'" onmouseout="this.style.background='#dcfce7'">
                        <svg style="width:1.25rem;" viewBox="0 0 24 24" fill="currentColor"><path d="M12.031 0C5.385 0 0 5.385 0 12.031c0 2.222.585 4.316 1.624 6.136L.357 23.332l5.321-1.396a11.96 11.96 0 006.353 1.83h.005c6.643 0 12.03-5.385 12.03-12.031C24.066 5.385 18.682 0 12.031 0z"/></svg>
                        Tanya via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style> [x-cloak] { display: none !important; } </style>
<script>
    document.getElementById('bookingForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<svg style="width:1.25rem;animation:spin 1s linear infinite;margin-right:8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg><span>Memproses...</span>';
    });
    const styleSpin = document.createElement('style');
    styleSpin.textContent = '@keyframes spin { from{transform:rotate(0deg);} to{transform:rotate(360deg);} }';
    document.head.appendChild(styleSpin);
</script>
</body>
</html>

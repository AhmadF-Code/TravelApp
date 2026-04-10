<x-filament-panels::page>
@php
    $kpi = $this->kpi;
    $visitor = $this->visitorStat;
@endphp

<div class="db-root-container">
    <style>
        .fi-page-header { display: none !important; }
        .fi-page { padding-top: 0 !important; }

        .db-wrap { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; padding-bottom: 2.5rem; display: flex; flex-direction: column; gap: 1.25rem; }

        /* HERO */
        .db-hero {
            position: relative; overflow: hidden; border-radius: 1.25rem;
            background: linear-gradient(135deg, #0f172a 0%, #1a2744 55%, #0f172a 100%);
            padding: 2rem 2.5rem; color: white;
        }
        .db-hero::before {
            content: ''; position: absolute; top: -5rem; right: -5rem; width: 22rem; height: 22rem;
            border-radius: 50%; background: radial-gradient(circle, rgba(245,158,11,0.15), transparent 70%); pointer-events: none;
        }
        .db-hero::after {
            content: ''; position: absolute; bottom: -4rem; left: -4rem; width: 18rem; height: 18rem;
            border-radius: 50%; background: radial-gradient(circle, rgba(59,130,246,0.12), transparent 70%); pointer-events: none;
        }
        .db-hero-inner { position: relative; z-index: 1; display: flex; flex-wrap: wrap; gap: 2rem; align-items: flex-start; justify-content: space-between; }
        .db-live-badge { display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.65rem; font-weight: 800; letter-spacing: 0.12em; text-transform: uppercase; color: #34d399; margin-bottom: 0.75rem; }
        .db-live-dot { width: 7px; height: 7px; border-radius: 50%; background: #34d399; animation: ping 1.5s ease-in-out infinite; display: inline-block; }
        @keyframes ping { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.4; transform: scale(1.4); } }
        .db-hero h1 { font-size: 2rem; font-weight: 900; margin: 0 0 0.4rem; line-height: 1.2; }
        .db-hero h1 span { background: linear-gradient(90deg, #fcd34d, #f59e0b); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .db-hero-sub { font-size: 0.8rem; color: #94a3b8; max-width: 36rem; margin-bottom: 1.25rem; }
        .db-date-form { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }
        .db-date-form input[type="date"] {
            background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);
            border-radius: 0.5rem; color: white; padding: 0.45rem 0.75rem; font-size: 0.8rem; outline: none; color-scheme: dark;
        }
        .db-date-sep { color: #64748b; font-size: 0.75rem; }
        .db-btn-apply {
            background: #f59e0b; color: #000; font-weight: 800; font-size: 0.7rem;
            letter-spacing: 0.05em; text-transform: uppercase; border: none; border-radius: 0.5rem;
            padding: 0.48rem 1rem; cursor: pointer; transition: background 0.2s;
        }
        .db-btn-apply:hover { background: #fbbf24; }
        .db-hero-stats { display: flex; gap: 0.75rem; flex-wrap: wrap; }
        .db-hero-stat {
            text-align: center; padding: 1rem 1.25rem; border-radius: 0.875rem; min-width: 90px;
            background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.12); backdrop-filter: blur(8px);
        }
        .db-hero-stat-num { font-size: 1.75rem; font-weight: 900; line-height: 1; }
        .db-hero-stat-lbl { font-size: 0.6rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; margin-top: 0.35rem; }

        /* GRID */
        .db-grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; }
        .db-grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
        .db-grid-4r { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem; }
        @media (max-width: 1024px) { .db-grid-4 { grid-template-columns: repeat(2, 1fr); } .db-grid-4r { grid-template-columns: repeat(2, 1fr); } .db-grid-2 { grid-template-columns: 1fr; } }
        @media (max-width: 640px)  { .db-grid-4 { grid-template-columns: 1fr; } .db-grid-4r { grid-template-columns: 1fr; } }

        /* CARD BASE */
        .db-card { background: #fff; border-radius: 1.125rem; border: 1px solid #f1f5f9; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden; }
        .db-card-header { display: flex; align-items: center; justify-content: space-between; padding: 1.1rem 1.5rem; border-bottom: 1px solid #f1f5f9; }
        .db-card-title { font-size: 0.875rem; font-weight: 900; color: #0f172a; margin: 0; }
        .db-card-sub { font-size: 0.7rem; color: #94a3b8; margin-top: 0.2rem; }
        .db-card-link { font-size: 0.7rem; font-weight: 700; color: #f59e0b; text-decoration: none; }
        .db-card-link:hover { color: #d97706; }

        /* KPI CARDS */
        .db-kpi { padding: 1.25rem; cursor: default; transition: box-shadow 0.2s; }
        .db-kpi-top { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1rem; }
        .db-kpi-icon { width: 2.5rem; height: 2.5rem; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; }
        .db-kpi-badge { font-size: 0.6rem; font-weight: 900; letter-spacing: 0.1em; text-transform: uppercase; padding: 0.2rem 0.6rem; border-radius: 9999px; }
        .db-kpi-lbl { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: #94a3b8; margin-bottom: 0.3rem; }
        .db-kpi-val { font-size: 1.25rem; font-weight: 900; color: #0f172a; line-height: 1.2; }
        .db-kpi-hint { font-size: 0.65rem; color: #94a3b8; margin-top: 0.25rem; }

        /* REPORT CARDS */
        .db-report-card { padding: 1.1rem; border-radius: 0.875rem; border: 2px solid; transition: box-shadow 0.2s, transform 0.15s; text-decoration: none; display: flex; flex-direction: column; gap: 0.6rem; cursor: pointer; }
        .db-report-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,0.1); transform: translateY(-2px); }
        .db-report-title { font-size: 0.8rem; font-weight: 900; }
        .db-report-sub { font-size: 0.65rem; color: #94a3b8; }
        .db-report-btn { display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.65rem; font-weight: 800; letter-spacing: 0.05em; text-transform: uppercase; color: white; padding: 0.4rem 0.85rem; border-radius: 0.4rem; margin-top: auto; text-decoration: none; }

        /* TABLE */
        .db-table { width: 100%; border-collapse: collapse; }
        .db-table thead th { padding: 0.7rem 1.5rem; text-align: left; font-size: 0.6rem; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; color: #94a3b8; background: #f8fafc; white-space: nowrap; }
        .db-table tbody td { padding: 0.875rem 1.5rem; font-size: 0.8rem; border-top: 1px solid #f8fafc; }
        .db-table tbody tr:hover { background: #fafbfc; }
        .db-cell-title { font-weight: 700; color: #0f172a; }
        .db-cell-sub { font-size: 0.65rem; color: #94a3b8; letter-spacing: 0.06em; text-transform: uppercase; margin-top: 0.1rem; }
        .db-badge { display: inline-block; font-size: 0.6rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; padding: 0.2rem 0.65rem; border-radius: 9999px; }
        .db-action-btn { display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.7rem; font-weight: 700; color: white; background: #1e293b; padding: 0.35rem 0.85rem; border-radius: 0.5rem; text-decoration: none; transition: background 0.2s; }
        .db-action-btn:hover { background: #334155; }
        .db-progress-wrap { display: flex; flex-direction: column; align-items: center; gap: 0.35rem; }
        .db-progress-bar { width: 5rem; height: 0.3rem; background: #e2e8f0; border-radius: 9999px; overflow: hidden; }
        .db-progress-fill { height: 100%; border-radius: 9999px; transition: width 0.5s; }

        /* LEADERBOARD */
        .db-rank-item { display: flex; align-items: center; justify-content: space-between; padding: 0.875rem 1.5rem; border-top: 1px solid #f8fafc; }
        .db-rank-item:hover { background: #fafbfc; }
        .db-rank-num { width: 1.6rem; height: 1.6rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.65rem; font-weight: 900; flex-shrink: 0; margin-right: 0.75rem; }
        .db-rank-name { font-size: 0.8rem; font-weight: 700; color: #0f172a; }
        .db-rank-sub { font-size: 0.65rem; color: #94a3b8; }
        .db-branch-item { padding: 0.875rem 1.5rem; border-top: 1px solid #f8fafc; }
        .db-branch-item:hover { background: #fafbfc; }
        .db-branch-bar { width: 100%; height: 0.25rem; background: #f1f5f9; border-radius: 9999px; overflow: hidden; margin-top: 0.4rem; }
        .db-branch-fill { height: 100%; background: linear-gradient(90deg, #3b82f6, #60a5fa); border-radius: 9999px; }
        .db-empty { padding: 3rem; text-align: center; font-size: 0.8rem; color: #cbd5e1; font-style: italic; }
    </style>

    <div class="db-wrap">

        {{-- ─── HERO ─────────────────────────────────────────────────── --}}
        <div class="db-hero">
            <div class="db-hero-inner">
                <div style="flex:1; min-width: 280px;">
                    <div class="db-live-badge">
                        <span class="db-live-dot"></span>
                        Operations Live
                    </div>
                    <h1>Welcome back, <span>{{ auth()->user()->name }}</span></h1>
                    <p class="db-hero-sub">Command center Travel Agent. Data operasional tersinkron dan siap untuk pengambilan keputusan terbaik.</p>

                    <form method="GET" action="{{ request()->url() }}" class="db-date-form">
                        <svg style="width:1rem;height:1rem;color:#f59e0b;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <input type="date" name="start" value="{{ $startDate }}">
                        <span class="db-date-sep">—</span>
                        <input type="date" name="end" value="{{ $endDate }}">
                        <button type="submit" class="db-btn-apply">Apply Filter</button>
                    </form>
                </div>

                <div class="db-hero-stats">
                    <div class="db-hero-stat">
                        <div class="db-hero-stat-num" style="color:#f87171">{{ $this->followupCount }}</div>
                        <div class="db-hero-stat-lbl" style="color:#f87171">Follow-up</div>
                    </div>
                    <div class="db-hero-stat">
                        <div class="db-hero-stat-num" style="color:#60a5fa">{{ $this->activeSchedulesCount }}</div>
                        <div class="db-hero-stat-lbl" style="color:#60a5fa">Jadwal Aktif</div>
                    </div>
                    <div class="db-hero-stat">
                        <div class="db-hero-stat-num" style="color:#34d399">{{ $visitor['today'] }}</div>
                        <div class="db-hero-stat-lbl" style="color:#34d399">Visitor Hari Ini</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── KPI ROW ──────────────────────────────────────────────── --}}
        <div class="db-grid-4">
            <div class="db-card db-kpi">
                <div class="db-kpi-top">
                    <div class="db-kpi-icon" style="background:linear-gradient(135deg,#d1fae5,#a7f3d0)">
                        <svg style="width:1.1rem;height:1.1rem;color:#059669" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                    </div>
                    <span class="db-kpi-badge" style="background:#d1fae5;color:#065f46">GROSS</span>
                </div>
                <div class="db-kpi-lbl">Omset Kotor</div>
                <div class="db-kpi-val">Rp {{ number_format($kpi['gross'], 0, ',', '.') }}</div>
                <div class="db-kpi-hint">{{ $kpi['paid_count'] }} booking lunas</div>
            </div>

            <div class="db-card db-kpi">
                <div class="db-kpi-top">
                    <div class="db-kpi-icon" style="background:linear-gradient(135deg,#dbeafe,#bfdbfe)">
                        <svg style="width:1.1rem;height:1.1rem;color:#1d4ed8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="db-kpi-badge" style="background:#dbeafe;color:#1e40af">NET</span>
                </div>
                <div class="db-kpi-lbl">Omset Bersih</div>
                <div class="db-kpi-val">Rp {{ number_format($kpi['net'], 0, ',', '.') }}</div>
                <div class="db-kpi-hint">Setelah refund dikurangi</div>
            </div>

            <div class="db-card db-kpi">
                <div class="db-kpi-top">
                    <div class="db-kpi-icon" style="background:linear-gradient(135deg,#fef3c7,#fde68a)">
                        <svg style="width:1.1rem;height:1.1rem;color:#b45309" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="db-kpi-badge" style="background:#fef3c7;color:#92400e">PENDING</span>
                </div>
                <div class="db-kpi-lbl">Menunggu Pembayaran</div>
                <div class="db-kpi-val">Rp {{ number_format($kpi['pending_val'], 0, ',', '.') }}</div>
                <div class="db-kpi-hint">{{ $kpi['pending_count'] }} pesanan aktif</div>
            </div>

            <div class="db-card db-kpi">
                <div class="db-kpi-top">
                    <div class="db-kpi-icon" style="background:linear-gradient(135deg,#fee2e2,#fecaca)">
                        <svg style="width:1.1rem;height:1.1rem;color:#b91c1c" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="db-kpi-badge" style="background:#fee2e2;color:#991b1b">BATAL</span>
                </div>
                <div class="db-kpi-lbl">Total Pembatalan</div>
                <div class="db-kpi-val">{{ $kpi['cancel_count'] }} <span style="font-size:0.85rem;font-weight:500;color:#94a3b8">Booking</span></div>
                <div class="db-kpi-hint">Refund: Rp {{ number_format($kpi['refund'], 0, ',', '.') }}</div>
            </div>
        </div>

        {{-- ─── REPORT CENTER ────────────────────────────────────────── --}}
        <div class="db-card">
            <div class="db-card-header">
                <div>
                    <div class="db-card-title">Management Report Center</div>
                    <div class="db-card-sub">Unduh data operasional dalam format CSV</div>
                </div>
            </div>
            <div style="padding:1.25rem;">
                <div class="db-grid-4r">
                    @php
                        $reports = [
                            ['label'=>'Sales Report', 'sub'=>'Rekap booking lunas', 'bg'=>'#f0fdf4', 'border'=>'#86efac', 'tc'=>'#166534', 'btnbg'=>'#16a34a', 'type'=>'sales'],
                            ['label'=>'Cancel Report', 'sub'=>'Pembatalan & alasan', 'bg'=>'#fff1f2', 'border'=>'#fca5a5', 'tc'=>'#9f1239', 'btnbg'=>'#dc2626', 'type'=>'cancel'],
                            ['label'=>'Refund Report', 'sub'=>'Audit dana keluar', 'bg'=>'#fffbeb', 'border'=>'#fcd34d', 'tc'=>'#92400e', 'btnbg'=>'#d97706', 'type'=>'refund'],
                            ['label'=>'Passenger Manifest', 'sub'=>'Daftar seluruh peserta', 'bg'=>'#eff6ff', 'border'=>'#93c5fd', 'tc'=>'#1e3a8a', 'btnbg'=>'#2563eb', 'type'=>'manifest'],
                        ];
                    @endphp
                    @foreach($reports as $r)
                    <a href="{{ route('admin.report.download', ['type' => $r['type']]) }}" target="_blank"
                       class="db-report-card"
                       style="background:{{ $r['bg'] }};border-color:{{ $r['border'] }};">
                        <div>
                            <div class="db-report-title" style="color:{{ $r['tc'] }}">{{ $r['label'] }}</div>
                            <div class="db-report-sub">{{ $r['sub'] }}</div>
                        </div>
                        <span class="db-report-btn" style="background:{{ $r['btnbg'] }}">
                            <svg style="width:0.75rem;height:0.75rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Download CSV
                        </span>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ─── SCHEDULE TABLE ───────────────────────────────────────── --}}
        <div class="db-card">
            <div class="db-card-header">
                <div>
                    <div class="db-card-title">Jadwal Keberangkatan Terdekat</div>
                    <div class="db-card-sub">Upcoming trips yang siap berangkat</div>
                </div>
                <a href="{{ \App\Filament\Resources\ScheduleResource::getUrl('index') }}" class="db-card-link">Lihat Semua</a>
            </div>
            <div style="overflow-x:auto">
                <table class="db-table">
                    <thead>
                        <tr>
                            <th>Trip Package</th>
                            <th>Keberangkatan</th>
                            <th style="text-align:center">Kapasitas</th>
                            <th>Status</th>
                            <th style="text-align:right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->upcomingSchedules as $s)
                        @php
                            $booked = $s->travelers_count;
                            $pct = $s->quota > 0 ? round(($booked / $s->quota) * 100) : 0;
                            $barColor = $pct >= 90 ? '#ef4444' : ($pct >= 60 ? '#f59e0b' : '#22c55e');
                            $days = now()->diffInDays($s->departure_date, false);
                        @endphp
                        <tr>
                            <td>
                                <div class="db-cell-title">{{ optional($s->trip)->title }}</div>
                                <div class="db-cell-sub">ID #{{ $s->id }}</div>
                            </td>
                            <td>
                                <div style="font-weight:700;color:#1e293b;font-size:0.8rem">{{ $s->departure_date->format('d M Y') }}</div>
                                @if($days <= 7 && $days >= 0)
                                    <div style="font-size:0.65rem;font-weight:800;color:#ef4444;margin-top:0.1rem">{{ $days }} hari lagi</div>
                                @else
                                    <div style="font-size:0.65rem;color:#94a3b8;margin-top:0.1rem">{{ max($days, 0) }} hari lagi</div>
                                @endif
                            </td>
                            <td>
                                <div class="db-progress-wrap">
                                    <div style="font-size:0.75rem;font-weight:700;color:#0f172a">{{ $booked }}/{{ $s->quota }}</div>
                                    <div class="db-progress-bar">
                                        <div class="db-progress-fill" style="width:{{ min($pct, 100) }}%;background:{{ $barColor }}"></div>
                                    </div>
                                    <div style="font-size:0.6rem;color:#94a3b8">{{ $pct }}%</div>
                                </div>
                            </td>
                            <td>
                                @if($pct >= 100)
                                    <span class="db-badge" style="background:#fee2e2;color:#991b1b">Full Booked</span>
                                @elseif($pct >= 80)
                                    <span class="db-badge" style="background:#fef3c7;color:#92400e">Hampir Penuh</span>
                                @else
                                    <span class="db-badge" style="background:#dcfce7;color:#166534">Tersedia</span>
                                @endif
                            </td>
                            <td style="text-align:right">
                                <a href="{{ \App\Filament\Resources\ScheduleResource::getUrl('manifest', ['record' => $s->id]) }}" class="db-action-btn">
                                    <svg style="width:0.75rem;height:0.75rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                                    Manifest
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="db-empty">Belum ada jadwal mendatang.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ─── TOP TRIPS + TOP BRANCHES ─────────────────────────────── --}}
        <div class="db-grid-2">
            <div class="db-card">
                <div class="db-card-header">
                    <div>
                        <div class="db-card-title">Top Paket Trip</div>
                        <div class="db-card-sub">Paling banyak terjual (range filter)</div>
                    </div>
                </div>
                @forelse($this->topTrips as $idx => $trip)
                @php
                    $medalBg  = $idx===0 ? '#fef3c7' : ($idx===1 ? '#f1f5f9' : ($idx===2 ? '#fff7ed' : '#f8fafc'));
                    $medalTxt = $idx===0 ? '#b45309' : ($idx===1 ? '#475569' : ($idx===2 ? '#9a3412' : '#64748b'));
                @endphp
                <div class="db-rank-item">
                    <div style="display:flex;align-items:center;gap:0.75rem;flex:1;min-width:0">
                        <div class="db-rank-num" style="background:{{ $medalBg }};color:{{ $medalTxt }}">{{ $idx+1 }}</div>
                        <div style="min-width:0">
                            <div class="db-rank-name" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:200px">{{ $trip->title }}</div>
                            <div class="db-rank-sub">{{ $trip->destination_country }}</div>
                        </div>
                    </div>
                    <div style="text-align:right;flex-shrink:0">
                        <span style="font-size:0.9rem;font-weight:900;color:#f59e0b">{{ $trip->paid_count }}</span>
                        <span style="font-size:0.65rem;color:#94a3b8;margin-left:2px">sold</span>
                    </div>
                </div>
                @empty
                <div class="db-empty">Belum ada data penjualan.</div>
                @endforelse
            </div>

            <div class="db-card">
                <div class="db-card-header">
                    <div>
                        <div class="db-card-title">Top Branch Revenue</div>
                        <div class="db-card-sub">Kontribusi pendapatan tertinggi</div>
                    </div>
                </div>
                @forelse($this->topBranches as $idx => $branch)
                @php $maxIncome = $this->topBranches->max('total_income') ?: 1; $pct = round(($branch->total_income / $maxIncome) * 100); @endphp
                <div class="db-branch-item">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.35rem">
                        <div style="display:flex;align-items:center;gap:0.6rem">
                            <div style="width:1.4rem;height:1.4rem;border-radius:50%;background:#eff6ff;color:#1d4ed8;display:flex;align-items:center;justify-content:center;font-size:0.6rem;font-weight:900;flex-shrink:0">{{ $idx+1 }}</div>
                            <span style="font-size:0.8rem;font-weight:700;color:#0f172a">{{ $branch->name }}</span>
                        </div>
                        <span style="font-size:0.8rem;font-weight:900;color:#16a34a">Rp {{ number_format($branch->total_income, 0, ',', '.') }}</span>
                    </div>
                    <div class="db-branch-bar" style="margin-left:2rem">
                        <div class="db-branch-fill" style="width:{{ $pct }}%"></div>
                    </div>
                </div>
                @empty
                <div class="db-empty">Belum ada data branch.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
</x-filament-panels::page>

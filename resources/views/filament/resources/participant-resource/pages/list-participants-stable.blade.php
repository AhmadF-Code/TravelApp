<x-filament-panels::page>

<style>
    .pa-wrap { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; padding-bottom: 2rem; }
    .pa-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.25rem; }
    .pa-search-wrap { position: relative; flex: 1; max-width: 26rem; }
    .pa-search-icon { position: absolute; left: 0.875rem; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; }
    .pa-search { width: 100%; padding: 0.6rem 0.875rem 0.6rem 2.5rem; border: 1.5px solid #e2e8f0; border-radius: 0.75rem; font-size: 0.82rem; outline: none; background: white; color: #0f172a; box-sizing: border-box; transition: border-color 0.2s; }
    .pa-search:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
    .pa-tabs { display: flex; align-items: center; gap: 0.25rem; background: #f1f5f9; border-radius: 0.75rem; padding: 0.25rem; flex-wrap: wrap; margin-bottom: 1rem; }
    .pa-tab { display: inline-block; padding: 0.4rem 1rem; font-size: 0.72rem; font-weight: 700; border-radius: 0.5rem; text-decoration: none; color: #64748b; transition: all 0.15s; white-space: nowrap; }
    .pa-tab:hover { color: #1e293b; }
    .pa-tab.active { background: white; color: #0f172a; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    .pa-card { background: white; border-radius: 1.125rem; border: 1px solid #f1f5f9; box-shadow: 0 1px 4px rgba(0,0,0,0.05); overflow: hidden; }
    .pa-table { width: 100%; border-collapse: collapse; }
    .pa-table thead th { padding: 0.75rem 1.5rem; text-align: left; font-size: 0.6rem; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; color: #94a3b8; background: #f8fafc; white-space: nowrap; }
    .pa-table tbody td { padding: 0.9rem 1.5rem; border-top: 1px solid #f8fafc; vertical-align: middle; }
    .pa-table tbody tr:hover { background: #fafbfc; }
    .pa-cell-main { font-size: 0.82rem; font-weight: 700; color: #0f172a; }
    .pa-cell-sub { font-size: 0.65rem; color: #94a3b8; margin-top: 0.1rem; }
    .pa-badge { display: inline-block; font-size: 0.6rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; padding: 0.22rem 0.65rem; border-radius: 9999px; }
    .pa-action { display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.7rem; font-weight: 700; color: white; padding: 0.35rem 0.75rem; border-radius: 0.5rem; text-decoration: none; transition: opacity 0.2s; white-space: nowrap; }
    .pa-action:hover { opacity: 0.85; }
    .pa-empty { padding: 4rem; text-align: center; font-size: 0.85rem; color: #cbd5e1; font-style: italic; }
    .pa-pagination { padding: 1rem 1.5rem; background: #fafbfc; border-top: 1px solid #f1f5f9; }
</style>

<div class="pa-wrap">
    <div class="pa-toolbar">
        <form action="{{ request()->url() }}" method="GET" style="flex:1;max-width:26rem">
            <div class="pa-search-wrap">
                <svg class="pa-search-icon" style="width:1rem;height:1rem" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, email, NIK, atau passport..." class="pa-search">
                <input type="hidden" name="status" value="{{ request()->query('status', 'all') }}">
            </div>
        </form>
        <div style="display:flex;align-items:center;padding:0.5rem 1rem;background:#eff6ff;border:1.5px solid #93c5fd;border-radius:0.75rem">
            <svg style="width:1rem;height:1rem;color:#2563eb;margin-right:0.4rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <span style="font-size:0.72rem;font-weight:700;color:#1e40af">Manajer Database Peserta</span>
        </div>
    </div>

    <!-- STATUS TABS -->
    <div class="pa-tabs">
        @php $currentStatus = request()->query('status', 'all'); @endphp
        @foreach(['all' => 'Semua Peserta', 'paid' => 'Sudah Bayar', 'pending' => 'Menunggu Pembayaran', 'cancelled' => 'Dibatalkan'] as $key => $label)
            <a href="?status={{ $key }}&search={{ $search }}"
               class="pa-tab {{ $currentStatus == $key ? 'active' : '' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="pa-card">
        <div style="overflow-x:auto">
            <table class="pa-table">
                <thead>
                    <tr>
                        <th>Identitas Peserta</th>
                        <th>KTP / NIK</th>
                        <th>Group Booking</th>
                        <th>Trip Detail</th>
                        <th>Status Booking</th>
                        <th style="text-align:right">Menu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->records as $traveler)
                    @php
                        $status = optional($traveler->booking)->status ?? 'unknown';
                        $sColor = match($status) {
                            'paid'      => ['bg'=>'#dcfce7','tc'=>'#166534'],
                            'pending'   => ['bg'=>'#fef3c7','tc'=>'#92400e'],
                            'cancelled' => ['bg'=>'#fee2e2','tc'=>'#991b1b'],
                            default     => ['bg'=>'#f1f5f9','tc'=>'#475569'],
                        };
                    @endphp
                    <tr>
                        <td>
                            <div class="pa-cell-main">{{ $traveler->name }}</div>
                            <div class="pa-cell-sub">{{ $traveler->email ?: '-' }} • {{ $traveler->phone ?: '-' }}</div>
                        </td>
                        <td>
                             <div class="pa-cell-main" style="letter-spacing:0.04em">{{ $traveler->ktp ?: 'Belum Diisi' }}</div>
                             <div class="pa-cell-sub">Passport: {{ $traveler->passport_number ?: '-' }}</div>
                        </td>
                        <td>
                            <div class="pa-cell-main" style="font-family:monospace;font-size:0.75rem;background:#f1f5f9;display:inline-block;padding:2px 6px;border-radius:4px">{{ optional($traveler->booking)->booking_code }}</div>
                            <div class="pa-cell-sub">{{ optional($traveler->booking)?->created_at?->format('d/m/Y') }}</div>
                        </td>
                        <td>
                            <div class="pa-cell-main" style="font-weight:600">{{ optional($traveler->booking?->schedule?->trip)->title }}</div>
                            <div class="pa-cell-sub">{{ optional($traveler->booking?->schedule)?->departure_date?->format('d M Y') }}</div>
                        </td>
                        <td>
                            <span class="pa-badge" style="background:{{ $sColor['bg'] }};color:{{ $sColor['tc'] }}">
                                {{ strtoupper($status) }}
                            </span>
                        </td>
                        <td style="text-align:right">
                             <a href="{{ App\Filament\Resources\BookingResource::getUrl('edit', ['record' => $traveler->booking_id]) }}"
                               class="pa-action" style="background:#1e293b">
                                <svg style="width:0.7rem;height:0.7rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Detail Booking
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="pa-empty">Tidak ada data peserta ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($this->records->hasPages())
        <div class="pa-pagination">{{ $this->records->links() }}</div>
        @endif
    </div>
</div>

</x-filament-panels::page>

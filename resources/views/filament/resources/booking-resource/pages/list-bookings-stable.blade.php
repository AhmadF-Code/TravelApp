<x-filament-panels::page>

<style>
    .bk-wrap { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; padding-bottom: 2rem; }
    .bk-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.25rem; }
    .bk-search-wrap { position: relative; flex: 1; max-width: 26rem; }
    .bk-search-icon { position: absolute; left: 0.875rem; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; }
    .bk-search { width: 100%; padding: 0.6rem 0.875rem 0.6rem 2.5rem; border: 1.5px solid #e2e8f0; border-radius: 0.75rem; font-size: 0.82rem; outline: none; background: white; color: #0f172a; box-sizing: border-box; transition: border-color 0.2s; }
    .bk-search:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,0.1); }
    .bk-tabs { display: flex; align-items: center; gap: 0.25rem; background: #f1f5f9; border-radius: 0.75rem; padding: 0.25rem; flex-wrap: wrap; }
    .bk-tab { display: inline-block; padding: 0.4rem 1rem; font-size: 0.72rem; font-weight: 700; border-radius: 0.5rem; text-decoration: none; color: #64748b; transition: all 0.15s; white-space: nowrap; }
    .bk-tab:hover { color: #1e293b; }
    .bk-tab.active { background: white; color: #0f172a; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    .bk-card { background: white; border-radius: 1.125rem; border: 1px solid #f1f5f9; box-shadow: 0 1px 4px rgba(0,0,0,0.05); overflow: hidden; }
    .bk-table { width: 100%; border-collapse: collapse; }
    .bk-table thead th { padding: 0.75rem 1.5rem; text-align: left; font-size: 0.6rem; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; color: #94a3b8; background: #f8fafc; white-space: nowrap; }
    .bk-table tbody td { padding: 0.9rem 1.5rem; border-top: 1px solid #f8fafc; vertical-align: middle; }
    .bk-table tbody tr:hover { background: #fafbfc; }
    .bk-cell-main { font-size: 0.82rem; font-weight: 700; color: #0f172a; }
    .bk-cell-sub { font-size: 0.65rem; color: #94a3b8; margin-top: 0.1rem; }
    .bk-badge { display: inline-block; font-size: 0.6rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; padding: 0.22rem 0.65rem; border-radius: 9999px; }
    .bk-price { font-size: 0.85rem; font-weight: 900; color: #0f172a; }
    .bk-action { display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.7rem; font-weight: 700; color: white; padding: 0.35rem 0.75rem; border-radius: 0.5rem; text-decoration: none; transition: opacity 0.2s; white-space: nowrap; }
    .bk-action:hover { opacity: 0.85; }
    .bk-actions { display: flex; align-items: center; justify-content: flex-end; gap: 0.35rem; }
    .bk-empty { padding: 4rem; text-align: center; font-size: 0.85rem; color: #cbd5e1; font-style: italic; }
    .bk-pagination { padding: 1rem 1.5rem; background: #fafbfc; border-top: 1px solid #f1f5f9; }
    .bk-top-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; flex-wrap: wrap; margin-bottom: 1rem; }
    .bk-expiry-bar { display: flex; align-items: center; gap: 0.75rem; padding: 0.7rem 1.25rem; border-radius: 0.875rem; margin-bottom: 1rem; font-size: 0.72rem; font-weight: 600; flex-wrap: wrap; }
    .bk-refresh-btn { display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.7rem; font-weight: 700; color: white; background: #0ea5e9; padding: 0.4rem 0.9rem; border-radius: 0.5rem; border: none; cursor: pointer; transition: opacity 0.2s; }
    .bk-refresh-btn:hover { opacity: 0.85; }
</style>

<div class="bk-wrap">
    <!-- TOOLBAR -->
    <div class="bk-top-row">
        <form action="{{ request()->url() }}" method="GET" style="flex:1;max-width:26rem;min-width:200px">
            <div class="bk-search-wrap">
                <svg class="bk-search-icon" style="width:1rem;height:1rem" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari kode booking, nama, email..." class="bk-search">
                <input type="hidden" name="status" value="{{ $statusFilter }}">
            </div>
        </form>
        <a href="{{ App\Filament\Resources\BookingResource::getUrl('create') }}"
           style="display:inline-flex;align-items:center;gap:0.5rem;font-size:0.78rem;font-weight:700;color:white;background:linear-gradient(135deg,#f59e0b,#d97706);padding:0.6rem 1.2rem;border-radius:0.75rem;text-decoration:none;white-space:nowrap">
            <svg style="width:0.9rem;height:0.9rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Booking Baru
        </a>
    </div>

    <!-- AUTO-EXPIRY STATUS BAR -->
    @if($expiredCount > 0)
    <div class="bk-expiry-bar" style="background:#f0fdf4;border:1px solid #86efac;color:#166534">
        <svg style="width:1rem;height:1rem;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span><strong>{{ $expiredCount }} booking</strong> otomatis di-expire karena melewati batas waktu 1 jam pembayaran.</span>
        <span style="color:#4ade80;margin-left:auto;flex-shrink:0">Dicek: {{ $checkedAt }}</span>
    </div>
    @else
    <div class="bk-expiry-bar" style="background:#f8fafc;border:1px solid #e2e8f0;color:#64748b">
        <svg style="width:0.9rem;height:0.9rem;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>Pengecekan otomatis berjalan. Tidak ada booking kedaluwarsa baru.</span>
        <span style="margin-left:auto;flex-shrink:0;font-size:0.65rem">{{ $checkedAt }}</span>
        <button wire:click="refreshBookings" class="bk-refresh-btn">
            <svg style="width:0.75rem;height:0.75rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Refresh Sekarang
        </button>
    </div>
    @endif

    <!-- STATUS TABS -->
    <div style="margin-bottom:1rem">
        <div class="bk-tabs">
            @foreach(['all' => 'Semua', 'pending' => 'Pending', 'paid' => 'Sudah Bayar', 'cancelled' => 'Dibatalkan'] as $key => $label)
                <a href="?status={{ $key }}&search={{ $search }}"
                   class="bk-tab {{ $statusFilter == $key ? 'active' : '' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- TABLE -->
    <div class="bk-card">
        <div style="overflow-x:auto">
            <table class="bk-table">
                <thead>
                    <tr>
                        <th>Booking Code</th>
                        <th>Pelanggan / Trip</th>
                        <th style="text-align:center">Pax</th>
                        <th>Total Biaya</th>
                        <th>Status</th>
                        <th style="text-align:right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->records as $booking)
                    @php
                        $statusColor = match($booking->status) {
                            'paid'      => ['bg'=>'#dcfce7','tc'=>'#166534'],
                            'pending'   => ['bg'=>'#fef3c7','tc'=>'#92400e'],
                            'cancelled' => ['bg'=>'#fee2e2','tc'=>'#991b1b'],
                            default     => ['bg'=>'#f1f5f9','tc'=>'#475569'],
                        };
                    @endphp
                    <tr>
                        <td>
                            <div class="bk-cell-main" style="font-family:monospace;font-size:0.8rem;letter-spacing:0.03em">{{ $booking->booking_code }}</div>
                        </td>
                        <td>
                            <div class="bk-cell-main">{{ $booking->customer_name }}</div>
                            <div class="bk-cell-sub">{{ optional($booking->schedule->trip)->title }}</div>
                        </td>
                        <td style="text-align:center">
                            <span style="font-size:0.85rem;font-weight:800;color:#1e293b">{{ $booking->pax }}</span>
                            <span style="font-size:0.65rem;color:#94a3b8"> pax</span>
                        </td>
                        <td>
                            <div class="bk-price">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</div>
                        </td>
                        <td>
                            <span class="bk-badge" style="background:{{ $statusColor['bg'] }};color:{{ $statusColor['tc'] }}">
                                {{ $booking->status }}
                            </span>
                        </td>
                        <td>
                            <div class="bk-actions">
                                <a href="{{ App\Filament\Resources\BookingResource::getUrl('view', ['record' => $booking->id]) }}"
                                   class="bk-action" style="background:#1e293b">
                                    <svg style="width:0.7rem;height:0.7rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Detail
                                </a>
                                <a href="{{ App\Filament\Resources\BookingResource::getUrl('edit', ['record' => $booking->id]) }}"
                                   class="bk-action" style="background:#3b82f6">
                                    <svg style="width:0.7rem;height:0.7rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="bk-empty">Tidak ada data yang ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($this->records->hasPages())
        <div class="bk-pagination">{{ $this->records->links() }}</div>
        @endif
    </div>
</div>

</x-filament-panels::page>

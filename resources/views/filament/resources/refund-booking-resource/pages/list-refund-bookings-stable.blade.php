<x-filament-panels::page>

<style>
    .rf-wrap { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; padding-bottom: 2rem; }
    .rf-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.25rem; }
    .rf-search-wrap { position: relative; flex: 1; max-width: 26rem; }
    .rf-search-icon { position: absolute; left: 0.875rem; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; }
    .rf-search { width: 100%; padding: 0.6rem 0.875rem 0.6rem 2.5rem; border: 1.5px solid #e2e8f0; border-radius: 0.75rem; font-size: 0.82rem; outline: none; background: white; color: #0f172a; box-sizing: border-box; transition: border-color 0.2s; }
    .rf-search:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,0.1); }
    .rf-card { background: white; border-radius: 1.125rem; border: 1px solid #f1f5f9; box-shadow: 0 1px 4px rgba(0,0,0,0.05); overflow: hidden; }
    .rf-table { width: 100%; border-collapse: collapse; }
    .rf-table thead th { padding: 0.75rem 1.5rem; text-align: left; font-size: 0.6rem; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; color: #94a3b8; background: #f8fafc; white-space: nowrap; }
    .rf-table tbody td { padding: 0.9rem 1.5rem; border-top: 1px solid #f8fafc; vertical-align: middle; }
    .rf-table tbody tr:hover { background: #fafbfc; }
    .rf-cell-main { font-size: 0.82rem; font-weight: 700; color: #0f172a; }
    .rf-cell-sub { font-size: 0.65rem; color: #94a3b8; margin-top: 0.1rem; }
    .rf-badge { display: inline-block; font-size: 0.6rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; padding: 0.22rem 0.65rem; border-radius: 9999px; }
    .rf-action { display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.7rem; font-weight: 700; color: white; padding: 0.35rem 0.75rem; border-radius: 0.5rem; text-decoration: none; transition: opacity 0.2s; white-space: nowrap; }
    .rf-action:hover { opacity: 0.85; }
    .rf-amount { font-size: 0.85rem; font-weight: 900; color: #dc2626; }
    .rf-empty { padding: 4rem; text-align: center; font-size: 0.85rem; color: #cbd5e1; font-style: italic; }
    .rf-pagination { padding: 1rem 1.5rem; background: #fafbfc; border-top: 1px solid #f1f5f9; }
</style>

<div class="rf-wrap">
    <div class="rf-toolbar">
        <form action="{{ request()->url() }}" method="GET" style="flex:1;max-width:26rem">
            <div class="rf-search-wrap">
                <svg class="rf-search-icon" style="width:1rem;height:1rem" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari kode booking atau nama pelanggan..." class="rf-search">
            </div>
        </form>
        <div style="display:flex;align-items:center;padding:0.5rem 1rem;background:#fff1f2;border:1.5px solid #fca5a5;border-radius:0.75rem">
            <svg style="width:1rem;height:1rem;color:#dc2626;margin-right:0.4rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            <span style="font-size:0.72rem;font-weight:700;color:#991b1b">Refund Pending Review</span>
        </div>
    </div>

    <div class="rf-card">
        <div style="overflow-x:auto">
            <table class="rf-table">
                <thead>
                    <tr>
                        <th>Booking Code</th>
                        <th>Pelanggan</th>
                        <th>Trip Package</th>
                        <th>Jumlah Refund</th>
                        <th>Status Refund</th>
                        <th style="text-align:right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->records as $booking)
                    @php
                        $refundStatus = $booking->refund_status ?? 'pending';
                        $rsColor = match($refundStatus) {
                            'done', 'approved'  => ['bg'=>'#dcfce7','tc'=>'#166534'],
                            'rejected'          => ['bg'=>'#fee2e2','tc'=>'#991b1b'],
                            default             => ['bg'=>'#fef3c7','tc'=>'#92400e'],
                        };
                    @endphp
                    <tr>
                        <td>
                            <div class="rf-cell-main" style="font-family:monospace;font-size:0.78rem;letter-spacing:0.03em">{{ $booking->booking_code }}</div>
                        </td>
                        <td>
                            <div class="rf-cell-main">{{ $booking->customer_name }}</div>
                            <div class="rf-cell-sub">{{ $booking->customer_phone }}</div>
                        </td>
                        <td>
                            <div class="rf-cell-main" style="font-weight:600">{{ optional($booking->schedule->trip)->title }}</div>
                            <div class="rf-cell-sub">{{ optional($booking->schedule)?->departure_date?->format('d M Y') }}</div>
                        </td>
                        <td>
                            <div class="rf-amount">Rp {{ number_format($booking->refund_amount ?? 0, 0, ',', '.') }}</div>
                        </td>
                        <td>
                            @php
                                $statusLabel = match($booking->follow_up_status) {
                                    'refund_processed' => 'Proses Transfer',
                                    'refund_completed' => 'Refund Selesai',
                                    default => $booking->follow_up_status,
                                };
                                $statusColor = match($booking->follow_up_status) {
                                    'refund_processed' => ['bg'=>'#fee2e2','tc'=>'#991b1b'],
                                    'refund_completed' => ['bg'=>'#dcfce7','tc'=>'#166534'],
                                    default => ['bg'=>'#f1f5f9','tc'=>'#64748b'],
                                };
                            @endphp
                            <span class="rf-badge" style="background:{{ $statusColor['bg'] }};color:{{ $statusColor['tc'] }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td style="text-align:right">
                            @if($booking->follow_up_status === 'refund_processed')
                                <a href="{{ App\Filament\Resources\RefundBookingResource::getUrl('proses', ['record' => $booking->id]) }}"
                                   class="rf-action" style="background:#1e293b">
                                    <svg style="width:0.7rem;height:0.7rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Proses Refund
                                </a>
                            @else
                                <a href="{{ App\Filament\Resources\RefundBookingResource::getUrl('view', ['record' => $booking->id]) }}"
                                   class="rf-action" style="background:#f59e0b">
                                    <svg style="width:0.7rem;height:0.7rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    View Transaction
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="rf-empty">Tidak ada data refund yang ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($this->records->hasPages())
        <div class="rf-pagination">{{ $this->records->links() }}</div>
        @endif
    </div>
</div>

</x-filament-panels::page>

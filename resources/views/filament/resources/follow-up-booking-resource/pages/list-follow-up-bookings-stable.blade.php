<x-filament-panels::page>

<style>
    .fu-wrap { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; padding-bottom: 2rem; }
    .fu-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.25rem; }
    .fu-search-wrap { position: relative; flex: 1; max-width: 26rem; }
    .fu-search-icon { position: absolute; left: 0.875rem; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; }
    .fu-search { width: 100%; padding: 0.6rem 10.875rem 0.6rem 2.5rem; border: 1.5px solid #e2e8f0; border-radius: 0.75rem; font-size: 0.82rem; outline: none; background: white; color: #0f172a; box-sizing: border-box; transition: border-color 0.2s; }
    .fu-search:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,0.1); }
    .fu-tabs { display: flex; align-items: center; gap: 0.25rem; background: #f1f5f9; border-radius: 0.75rem; padding: 0.25rem; flex-wrap: wrap; margin-bottom: 1rem; }
    .fu-tab { display: inline-block; padding: 0.4rem 1rem; font-size: 0.72rem; font-weight: 700; border-radius: 0.5rem; text-decoration: none; color: #64748b; transition: all 0.15s; white-space: nowrap; }
    .fu-tab:hover { color: #1e293b; }
    .fu-tab.active { background: white; color: #0f172a; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    .fu-card { background: white; border-radius: 1.125rem; border: 1px solid #f1f5f9; box-shadow: 0 1px 4px rgba(0,0,0,0.05); overflow: hidden; }
    .fu-table { width: 100%; border-collapse: collapse; }
    .fu-table thead th { padding: 0.75rem 1.5rem; text-align: left; font-size: 0.6rem; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; color: #94a3b8; background: #f8fafc; white-space: nowrap; }
    .fu-table tbody td { padding: 0.9rem 1.5rem; border-top: 1px solid #f8fafc; vertical-align: middle; }
    .fu-table tbody tr:hover { background: #fafbfc; }
    .fu-cell-main { font-size: 0.82rem; font-weight: 700; color: #0f172a; }
    .fu-cell-sub { font-size: 0.65rem; color: #94a3b8; margin-top: 0.1rem; }
    .fu-badge { display: inline-block; font-size: 0.6rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; padding: 0.22rem 0.65rem; border-radius: 9999px; }
    .fu-action-btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.35rem; font-size: 0.65rem; font-weight: 800; letter-spacing: 0.03em; color: white !important; padding: 0.4rem 0.75rem; border-radius: 0.5rem; border: none; cursor: pointer; transition: opacity 0.15s; text-decoration: none !important; }
    .fu-action-btn:hover { opacity: 0.85; }
    .fu-note { font-size: 0.72rem; color: #64748b; max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .fu-empty { padding: 4rem; text-align: center; font-size: 0.85rem; color: #cbd5e1; font-style: italic; }
</style>

<div class="fu-wrap">
    <div class="fu-toolbar">
        <form action="{{ request()->url() }}" method="GET" style="flex:1;max-width:26rem">
            <div class="fu-search-wrap">
                <svg class="fu-search-icon" style="width:1rem;height:1rem" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari kode booking atau nama pelanggan..." class="fu-search">
            </div>
        </form>
        <div style="display:flex;align-items:center;padding:0.5rem 1rem;background:#fffbeb;border:1.5px solid #fcd34d;border-radius:0.75rem">
            <svg style="width:1rem;height:1rem;color:#d97706;margin-right:0.4rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span style="font-size:0.72rem;font-weight:700;color:#92400e">Follow-up Task Manager</span>
        </div>
    </div>

    <!-- STATUS TABS -->
    <div class="fu-tabs">
        @php $currentStatus = request()->query('status', 'all'); @endphp
        @foreach(['all' => 'Perlu Ditindak (Active)', 'resolved_moved' => 'Sudah Reschedule', 'refund_processed' => 'Sudah Refund'] as $key => $label)
            <a href="?status={{ $key }}&search={{ $search }}"
               class="fu-tab {{ $currentStatus == $key ? 'active' : '' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="fu-card">
        <div style="overflow-x:auto">
            <table class="fu-table">
                <thead>
                    <tr>
                        <th>Booking Code</th>
                        <th>Pelanggan</th>
                        <th>Trip & Schedule</th>
                        <th>Status History</th>
                        <th style="text-align:right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->records as $booking)
                    @php
                        $fuStatus = $booking->follow_up_status ?? 'all';
                        $fuColor = match($fuStatus) {
                            'resolved_moved'   => ['bg'=>'#dcfce7','tc'=>'#166534'],
                            'refund_processed' => ['bg'=>'#fee2e2','tc'=>'#991b1b'],
                            default            => ['bg'=>'#fef3c7','tc'=>'#92400e'],
                        };
                    @endphp
                    <tr wire:key="fu-booking-{{ $booking->id }}">
                        <td>
                            <div class="fu-cell-main" style="font-family:monospace;font-size:0.78rem">{{ $booking->booking_code }}</div>
                            <div class="fu-cell-sub">{{ $booking->updated_at->format('d/m/Y') }}</div>
                        </td>
                        <td>
                            <div class="fu-cell-main">{{ $booking->customer_name }}</div>
                            <div class="fu-cell-sub">{{ $booking->customer_phone }}</div>
                        </td>
                        <td>
                            <div class="fu-cell-main">{{ optional($booking->schedule->trip)->title }}</div>
                            <div class="fu-cell-sub">{{ optional($booking->schedule)?->departure_date?->format('d M Y') }}</div>
                        </td>
                        <td>
                             <span class="fu-badge" style="background:{{ $fuColor['bg'] }};color:{{ $fuColor['tc'] }};margin-bottom:0.25rem">
                                {{ str_replace('_', ' ', $fuStatus) }}
                            </span>
                            <div class="fu-note" title="{{ $booking->follow_up_note }}">
                                {{ $booking->follow_up_note ?: 'Tanpa catatan.' }}
                            </div>
                        </td>
                        <td style="text-align:right;white-space:nowrap">
                            <div style="display:flex;gap:0.4rem;justify-content:flex-end">
                                <a href="{{ \App\Filament\Resources\FollowUpBookingResource::getUrl('reschedule', ['record' => $booking->id]) }}"
                                        class="fu-action-btn" style="background:#059669">
                                    <svg style="width:0.7rem;height:0.7rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                    Pindah
                                </a>
                                
                                <a href="{{ \App\Filament\Resources\FollowUpBookingResource::getUrl('cancel', ['record' => $booking->id]) }}"
                                        class="fu-action-btn" style="background:#dc2626">
                                    <svg style="width:0.7rem;height:0.7rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Batal
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="fu-empty">Tidak ada data follow-up yang perlu ditindak.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($this->records->hasPages())
        <div style="padding: 1.25rem; background:#f8fafc; border-top:1px solid #f1f5f9">
            {{ $this->records->links() }}
        </div>
        @endif
    </div>
</div>

</x-filament-panels::page>

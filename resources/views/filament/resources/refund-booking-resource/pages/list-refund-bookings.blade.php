<x-filament-panels::page>
<style>
    .rf-wrap { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; padding-bottom: 2rem; }
    .rf-card { background: white; border-radius: 1.125rem; border: 1px solid #f1f5f9; box-shadow: 0 1px 4px rgba(0,0,0,0.05); overflow: hidden; }
    .rf-table { width: 100%; border-collapse: collapse; }
    .rf-table thead th { padding: 0.75rem 1.5rem; text-align: left; font-size: 0.6rem; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; color: #94a3b8; background: #f8fafc; white-space: nowrap; }
    .rf-table tbody td { padding: 0.9rem 1.5rem; border-top: 1px solid #f8fafc; vertical-align: middle; }
    .rf-table tbody tr:hover { background: #fafbfc; }
    .rf-code { font-family: monospace; font-weight: 700; color: #0f172a; font-size: 0.82rem; }
    .rf-amount { font-weight: 900; color: #16a34a; font-size: 0.85rem; }
    .rf-badge { display: inline-block; font-size: 0.6rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; padding: 0.22rem 0.65rem; border-radius: 9999px; }
    .rf-action { display: inline-flex; align-items: center; justify-content: center; gap: 0.35rem; font-size: 0.65rem; font-weight: 800; letter-spacing: 0.03em; color: white !important; padding: 0.4rem 0.75rem; border-radius: 0.5rem; text-decoration: none !important; transition: opacity 0.15s; }
    .rf-action:hover { opacity: 0.85; }
</style>

<div class="rf-wrap">
    <div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 900; color: #0f172a">Financial Refund Audit</h2>
            <p style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem">Daftar seluruh transaksi yang memerlukan pengembalian dana.</p>
        </div>
        <div style="padding: 0.5rem 1rem; border: 1.5px solid #3b82f6; border-radius: 0.75rem; background: #eff6ff">
             <span style="font-size: 0.72rem; font-weight: 800; color: #1d4ed8">AUDIT MODE ACTIVE</span>
        </div>
    </div>

    <div class="rf-card">
        <div style="overflow-x:auto">
            <table class="rf-table">
                <thead>
                    <tr>
                        <th>Booking Code</th>
                        <th>Pelanggan</th>
                        <th>Alokasi Dana</th>
                        <th>Status Refund</th>
                        <th style="text-align:right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->getRecords() as $booking)
                    @php
                        $isComplete = ($booking->follow_up_status === 'refund_completed');
                        $statusColor = $isComplete ? ['bg'=>'#dcfce7','tc'=>'#166534','lbl'=>'COMPLETE'] : ['bg'=>'#fef3c7','tc'=>'#92400e','lbl'=>'PENDING PENYELAMATA'];
                        $btnColor = $isComplete ? '#1e293b' : '#3b82f6';
                        $btnLabel = $isComplete ? 'View Transaction' : 'Proses Refund';
                        $btnRoute = $isComplete ? \App\Filament\Resources\RefundBookingResource::getUrl('view', ['record' => $booking->id]) : \App\Filament\Resources\RefundBookingResource::getUrl('proses', ['record' => $booking->id]);
                    @endphp
                    <tr>
                        <td>
                            <div class="rf-code">{{ $booking->booking_code }}</div>
                            <div style="font-size: 0.65rem; color: #94a3b8; margin-top: 0.1rem">{{ $booking->updated_at->format('d M Y H:i') }}</div>
                        </td>
                        <td>
                            <div style="font-size: 0.82rem; font-weight: 700; color: #0f172a">{{ $booking->customer_name }}</div>
                            <div style="font-size: 0.65rem; color: #94a3b8">{{ $booking->customer_phone }}</div>
                        </td>
                        <td>
                            <div class="rf-amount">Rp {{ number_format($booking->refund_amount, 0, ',', '.') }}</div>
                            <div style="font-size: 0.6rem; color: #94a3b8">Source: System Cancellation</div>
                        </td>
                        <td>
                            <span class="rf-badge" style="background:{{ $statusColor['bg'] }};color:{{ $statusColor['tc'] }}">
                                {{ $statusColor['lbl'] }}
                            </span>
                             @if($isComplete)
                                <div style="font-size: 0.62rem; color: #64748b; margin-top: 0.2rem">By: {{ optional($booking->refundProcessor)->name ?? 'Admin' }}</div>
                             @endif
                        </td>
                        <td style="text-align:right">
                            <a href="{{ $btnRoute }}" class="rf-action" style="background:{{ $btnColor }}">
                                @if($isComplete)
                                    <svg style="width:0.75rem;height:0.75rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                @else
                                    <svg style="width:0.75rem;height:0.75rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @endif
                                {{ $btnLabel }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="padding: 4rem; text-align: center; font-size: 0.85rem; color: #cbd5e1; font-style: italic">Belum ada data refund yang perlu diproses.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</x-filament-panels::page>

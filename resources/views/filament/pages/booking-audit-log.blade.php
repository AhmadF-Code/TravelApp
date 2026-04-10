<x-filament-panels::page>

<div class="al-root-container">
    <style>
        .al-wrap { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; padding-bottom: 2rem; }

        /* KPI ROW */
        .al-kpi-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
        .al-kpi { background: white; border-radius: 1rem; border: 1px solid #f1f5f9; padding: 1.1rem 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
        .al-kpi-num { font-size: 1.75rem; font-weight: 900; line-height: 1; }
        .al-kpi-lbl { font-size: 0.62rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: #94a3b8; margin-top: 0.35rem; }

        /* TOOLBAR */
        .al-toolbar { display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 1.25rem; }
        .al-search-wrap { position: relative; flex: 1; min-width: 200px; max-width: 24rem; }
        .al-search-icon { position: absolute; left: 0.875rem; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; }
        .al-search { width: 100%; padding: 0.6rem 0.875rem 0.6rem 2.5rem; border: 1.5px solid #e2e8f0; border-radius: 0.75rem; font-size: 0.82rem; outline: none; background: white; color: #0f172a; box-sizing: border-box; transition: border-color 0.2s; }
        .al-search:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,0.1); }
        .al-filter-tabs { display: flex; align-items: center; gap: 0.25rem; background: #f1f5f9; border-radius: 0.75rem; padding: 0.2rem; }
        .al-tab { display: inline-block; padding: 0.38rem 0.9rem; font-size: 0.7rem; font-weight: 700; border-radius: 0.5rem; text-decoration: none; color: #64748b; white-space: nowrap; transition: all 0.15s; }
        .al-tab.active { background: white; color: #0f172a; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .al-tab:hover { color: #1e293b; }

        /* TABLE */
        .al-card { background: white; border-radius: 1.125rem; border: 1px solid #f1f5f9; box-shadow: 0 1px 4px rgba(0,0,0,0.05); overflow: hidden; }
        .al-table { width: 100%; border-collapse: collapse; }
        .al-table thead th { padding: 0.65rem 1.25rem; text-align: left; font-size: 0.58rem; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; color: #94a3b8; background: #f8fafc; white-space: nowrap; }
        .al-table tbody td { padding: 0.75rem 1.25rem; border-top: 1px solid #f8fafc; vertical-align: middle; }
        .al-table tbody tr:hover { background: #fafbfc; }
        .al-code { font-family: 'Courier New', monospace; font-size: 0.75rem; font-weight: 900; color: #0f172a; letter-spacing: 0.05em; }
        .al-badge { display: inline-block; font-size: 0.58rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; padding: 0.2rem 0.6rem; border-radius: 9999px; }
        .al-note { font-size: 0.68rem; color: #64748b; max-width: 260px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .al-ts { font-size: 0.65rem; color: #94a3b8; white-space: nowrap; }
        .al-triggerer { font-size: 0.7rem; font-weight: 600; color: #475569; }
        .al-empty { padding: 4rem; text-align: center; font-size: 0.85rem; color: #cbd5e1; font-style: italic; }
        .al-pagination { padding: 1rem 1.25rem; background: #fafbfc; border-top: 1px solid #f1f5f9; }
        @media(max-width:900px) { .al-kpi-row { grid-template-columns: repeat(2,1fr); } }
    </style>

    <div class="al-wrap">

        <!-- KPI HEADER -->
        <div class="al-kpi-row">
            <div class="al-kpi">
                <div class="al-kpi-num" style="color:#3b82f6">{{ number_format($this->stats['total']) }}</div>
                <div class="al-kpi-lbl">Total Log Tersimpan</div>
            </div>
            <div class="al-kpi">
                <div class="al-kpi-num" style="color:#f59e0b">{{ number_format($this->stats['today']) }}</div>
                <div class="al-kpi-lbl">Log Hari Ini</div>
            </div>
            <div class="al-kpi">
                <div class="al-kpi-num" style="color:#ef4444">{{ number_format($this->stats['auto_expired']) }}</div>
                <div class="al-kpi-lbl">Total Auto-Expired</div>
            </div>
            <div class="al-kpi">
                <div class="al-kpi-num" style="color:#dc2626">{{ number_format($this->stats['today_expired']) }}</div>
                <div class="al-kpi-lbl">Expired Hari Ini</div>
            </div>
        </div>

        <!-- TOOLBAR -->
        <div class="al-toolbar">
            <form action="{{ request()->url() }}" method="GET" style="flex:1;min-width:200px;max-width:24rem">
                <div class="al-search-wrap">
                    <svg class="al-search-icon" style="width:1rem;height:1rem" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari booking code atau trigger..." class="al-search">
                    <input type="hidden" name="action" value="{{ $actionFilter }}">
                </div>
            </form>

            <div class="al-filter-tabs">
                @foreach(['all' => 'Semua', 'auto_expired' => 'Auto Expired', 'manual_paid' => 'Manual Paid', 'cancel' => 'Cancel', 'refund' => 'Refund'] as $key => $label)
                    <a href="?action={{ $key }}&search={{ $search }}"
                       class="al-tab {{ $actionFilter == $key ? 'active' : '' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- TABLE -->
        <div class="al-card">
            <div style="overflow-x:auto">
                <table class="al-table">
                    <thead>
                        <tr>
                            <th>Booking Code</th>
                            <th>Aksi</th>
                            <th style="text-align:center">Status Perubahan</th>
                            <th>Jumlah</th>
                            <th>Oleh</th>
                            <th>Catatan</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->records as $log)
                        @php
                            $actionColor = match($log->action) {
                                'auto_expired'  => ['bg'=>'#fef3c7','tc'=>'#92400e'],
                                'manual_paid'   => ['bg'=>'#dcfce7','tc'=>'#166534'],
                                'cancel'        => ['bg'=>'#fee2e2','tc'=>'#991b1b'],
                                'refund'        => ['bg'=>'#eff6ff','tc'=>'#1e40af'],
                                default         => ['bg'=>'#f1f5f9','tc'=>'#475569'],
                            };
                        @endphp
                        <tr>
                            <td>
                                <span class="al-code">{{ $log->booking_code ?? '—' }}</span>
                            </td>
                            <td>
                                <span class="al-badge" style="background:{{ $actionColor['bg'] }};color:{{ $actionColor['tc'] }}">
                                    {{ str_replace('_', ' ', $log->action) }}
                                </span>
                            </td>
                            <td style="text-align:center">
                                @if($log->old_status || $log->new_status)
                                <div style="display:flex;align-items:center;justify-content:center;gap:0.35rem;font-size:0.65rem;font-weight:700">
                                    @if($log->old_status)
                                    <span style="background:#f1f5f9;color:#475569;padding:0.15rem 0.5rem;border-radius:9999px">{{ $log->old_status }}</span>
                                    @endif
                                    @if($log->new_status)
                                    <svg style="width:0.7rem;height:0.7rem;color:#94a3b8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                    <span style="background:{{ $actionColor['bg'] }};color:{{ $actionColor['tc'] }};padding:0.15rem 0.5rem;border-radius:9999px">{{ $log->new_status }}</span>
                                    @endif
                                </div>
                                @else
                                <span style="color:#cbd5e1;font-size:0.65rem">—</span>
                                @endif
                            </td>
                            <td>
                                @if($log->amount)
                                <span style="font-size:0.75rem;font-weight:800;color:#0f172a">Rp {{ number_format($log->amount, 0, ',', '.') }}</span>
                                @else
                                <span style="color:#cbd5e1;font-size:0.7rem">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="al-triggerer">{{ $log->triggered_by }}</span>
                                @if($log->ip_address)
                                <div style="font-size:0.6rem;color:#cbd5e1">{{ $log->ip_address }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="al-note" title="{{ $log->notes }}">{{ $log->notes ?? '—' }}</div>
                            </td>
                            <td>
                                <div class="al-ts">{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y') }}</div>
                                <div class="al-ts" style="font-weight:700;color:#475569">{{ \Carbon\Carbon::parse($log->created_at)->format('H:i:s') }}</div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="al-empty">Belum ada audit log tersimpan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($this->records->hasPages())
            <div class="al-pagination">{{ $this->records->links() }}</div>
            @endif
        </div>
    </div>
</div>

</x-filament-panels::page>

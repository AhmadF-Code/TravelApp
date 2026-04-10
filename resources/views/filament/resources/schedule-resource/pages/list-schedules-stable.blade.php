<x-filament-panels::page>

<style>
    .sc-wrap { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; padding-bottom: 2rem; }
    .sc-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.25rem; }
    .sc-search-wrap { position: relative; flex: 1; max-width: 26rem; }
    .sc-search-icon { position: absolute; left: 0.875rem; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; }
    .sc-search { width: 100%; padding: 0.6rem 0.875rem 0.6rem 2.5rem; border: 1.5px solid #e2e8f0; border-radius: 0.75rem; font-size: 0.82rem; outline: none; background: white; color: #0f172a; box-sizing: border-box; transition: border-color 0.2s; }
    .sc-search:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,0.1); }
    .sc-btn { display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.78rem; font-weight: 700; color: white; background: linear-gradient(135deg,#f59e0b,#d97706); padding: 0.6rem 1.2rem; border-radius: 0.75rem; text-decoration: none; white-space: nowrap; transition: opacity 0.2s; }
    .sc-btn:hover { opacity: 0.88; }
    .sc-card { background: white; border-radius: 1.125rem; border: 1px solid #f1f5f9; box-shadow: 0 1px 4px rgba(0,0,0,0.05); overflow: hidden; }
    .sc-table { width: 100%; border-collapse: collapse; }
    .sc-table thead th { padding: 0.75rem 1.5rem; text-align: left; font-size: 0.6rem; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; color: #94a3b8; background: #f8fafc; white-space: nowrap; }
    .sc-table tbody td { padding: 0.9rem 1.5rem; border-top: 1px solid #f8fafc; vertical-align: middle; }
    .sc-table tbody tr:hover { background: #fafbfc; }
    .sc-cell-main { font-size: 0.82rem; font-weight: 700; color: #0f172a; }
    .sc-cell-sub { font-size: 0.65rem; color: #94a3b8; margin-top: 0.1rem; }
    .sc-badge { display: inline-block; font-size: 0.6rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; padding: 0.22rem 0.65rem; border-radius: 9999px; }
    .sc-action { display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.7rem; font-weight: 700; color: white; padding: 0.35rem 0.75rem; border-radius: 0.5rem; text-decoration: none; transition: opacity 0.2s; white-space: nowrap; }
    .sc-action:hover { opacity: 0.85; }
    .sc-progress-wrap { display: flex; flex-direction: column; align-items: center; gap: 0.35rem; }
    .sc-progress-bar { width: 5rem; height: 0.3rem; background: #e2e8f0; border-radius: 9999px; overflow: hidden; }
    .sc-progress-fill { height: 100%; border-radius: 9999px; }
    .sc-empty { padding: 4rem; text-align: center; font-size: 0.85rem; color: #cbd5e1; font-style: italic; }
    .sc-pagination { padding: 1rem 1.5rem; background: #fafbfc; border-top: 1px solid #f1f5f9; }
</style>

<div class="sc-wrap">
    <div class="sc-toolbar">
        <form action="{{ request()->url() }}" method="GET" style="flex:1;max-width:26rem">
            <div class="sc-search-wrap">
                <svg class="sc-search-icon" style="width:1rem;height:1rem" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama paket trip..." class="sc-search">
            </div>
        </form>
        <a href="{{ App\Filament\Resources\ScheduleResource::getUrl('create') }}" class="sc-btn">
            <svg style="width:0.9rem;height:0.9rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Jadwal Baru
        </a>
    </div>

    <div class="sc-card">
        <div style="overflow-x:auto">
            <table class="sc-table">
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
                    @forelse($this->records as $schedule)
                    @php
                        $booked  = $schedule->travelers_count;
                        $quota   = $schedule->quota;
                        $pct     = $quota > 0 ? round(($booked / $quota) * 100) : 0;
                        $barClr  = $pct >= 100 ? '#ef4444' : ($pct >= 80 ? '#f59e0b' : '#22c55e');
                        $statusObj = match(true) {
                            $schedule->status === 'cancelled'                        => ['label'=>'Batal',       'bg'=>'#fee2e2','tc'=>'#991b1b'],
                            now()->startOfDay()->greaterThan($schedule->departure_date) => ['label'=>'Selesai',  'bg'=>'#dcfce7','tc'=>'#166534'],
                            default => ['label'=>'Akan Datang', 'bg'=>'#eff6ff','tc'=>'#1e40af'],
                        };
                    @endphp
                    <tr>
                        <td>
                            <div class="sc-cell-main">{{ optional($schedule->trip)->title }}</div>
                            <div class="sc-cell-sub">REF ID: {{ $schedule->id }}</div>
                        </td>
                        <td>
                            <div style="font-size:0.82rem;font-weight:700;color:#1e293b">{{ $schedule->departure_date->format('d M Y') }}</div>
                            <div class="sc-cell-sub">s/d {{ $schedule->return_date->format('d M Y') }}</div>
                        </td>
                        <td>
                            <div class="sc-progress-wrap">
                                <div style="font-size:0.75rem;font-weight:700;color:#0f172a">{{ $booked }}/{{ $quota }} Pax</div>
                                <div class="sc-progress-bar">
                                    <div class="sc-progress-fill" style="width:{{ min($pct,100) }}%;background:{{ $barClr }}"></div>
                                </div>
                                <div style="font-size:0.6rem;color:#94a3b8">{{ $pct }}%</div>
                            </div>
                        </td>
                        <td>
                            <span class="sc-badge" style="background:{{ $statusObj['bg'] }};color:{{ $statusObj['tc'] }}">{{ $statusObj['label'] }}</span>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;justify-content:flex-end;gap:0.35rem">
                                <a href="{{ App\Filament\Resources\ScheduleResource::getUrl('manifest', ['record' => $schedule->id]) }}"
                                   class="sc-action" style="background:#0ea5e9">
                                    <svg style="width:0.7rem;height:0.7rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                                    Manifest
                                </a>
                                <a href="{{ App\Filament\Resources\ScheduleResource::getUrl('edit', ['record' => $schedule->id]) }}"
                                   class="sc-action" style="background:#1e293b">
                                    <svg style="width:0.7rem;height:0.7rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="sc-empty">Belum ada jadwal yang direncanakan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($this->records->hasPages())
        <div class="sc-pagination">{{ $this->records->links() }}</div>
        @endif
    </div>
</div>

</x-filament-panels::page>

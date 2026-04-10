<x-filament-panels::page>

<style>
    .pc-wrap { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; padding-bottom: 2rem; }
    .pc-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.25rem; }
    .pc-search-wrap { position: relative; flex: 1; max-width: 26rem; }
    .pc-search-icon { position: absolute; left: 0.875rem; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; }
    .pc-search { width: 100%; padding: 0.6rem 0.875rem 0.6rem 2.5rem; border: 1.5px solid #e2e8f0; border-radius: 0.75rem; font-size: 0.82rem; outline: none; background: white; color: #0f172a; box-sizing: border-box; transition: border-color 0.2s; }
    .pc-search:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,0.1); }
    .pc-btn { display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.78rem; font-weight: 700; color: white; background: linear-gradient(135deg,#f59e0b,#d97706); padding: 0.6rem 1.2rem; border-radius: 0.75rem; text-decoration: none; white-space: nowrap; transition: opacity 0.2s; }
    .pc-btn:hover { opacity: 0.88; }
    .pc-card { background: white; border-radius: 1.125rem; border: 1px solid #f1f5f9; box-shadow: 0 1px 4px rgba(0,0,0,0.05); overflow: hidden; }
    .pc-table { width: 100%; border-collapse: collapse; }
    .pc-table thead th { padding: 0.75rem 1.5rem; text-align: left; font-size: 0.6rem; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; color: #94a3b8; background: #f8fafc; white-space: nowrap; }
    .pc-table tbody td { padding: 1rem 1.5rem; border-top: 1px solid #f8fafc; vertical-align: middle; }
    .pc-table tbody tr:hover { background: #fafbfc; }
    .pc-code { font-family: 'Courier New', monospace; font-size: 0.9rem; font-weight: 900; color: #0f172a; letter-spacing: 0.1em; background: #f8fafc; border: 1px solid #e2e8f0; padding: 0.3rem 0.7rem; border-radius: 0.5rem; display: inline-block; }
    .pc-price { font-size: 0.9rem; font-weight: 900; color: #16a34a; }
    .pc-badge { display: inline-block; font-size: 0.6rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; padding: 0.22rem 0.65rem; border-radius: 9999px; }
    .pc-date { font-size: 0.78rem; font-weight: 600; color: #475569; }
    .pc-action { display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.7rem; font-weight: 700; color: white; background: #1e293b; padding: 0.35rem 0.75rem; border-radius: 0.5rem; text-decoration: none; transition: opacity 0.2s; white-space: nowrap; }
    .pc-action:hover { opacity: 0.85; }
    .pc-empty { padding: 4rem; text-align: center; font-size: 0.85rem; color: #cbd5e1; font-style: italic; }
    .pc-pagination { padding: 1rem 1.5rem; background: #fafbfc; border-top: 1px solid #f1f5f9; }
</style>

<div class="pc-wrap">
    <div class="pc-toolbar">
        <form action="{{ request()->url() }}" method="GET" style="flex:1;max-width:26rem">
            <div class="pc-search-wrap">
                <svg class="pc-search-icon" style="width:1rem;height:1rem" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari kode promo..." class="pc-search">
            </div>
        </form>
        <a href="{{ App\Filament\Resources\PromoCodeResource::getUrl('create') }}" class="pc-btn">
            <svg style="width:0.9rem;height:0.9rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Kode Promo
        </a>
    </div>

    <div class="pc-card">
        <div style="overflow-x:auto">
            <table class="pc-table">
                <thead>
                    <tr>
                        <th>Kode Promo</th>
                        <th>Potongan</th>
                        <th>Masa Berlaku</th>
                        <th style="text-align:center">Status</th>
                        <th style="text-align:right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->records as $promo)
                    @php
                        // Fix: expires_at may be a string — always parse safely
                        $expiresAt = $promo->expires_at
                            ? (\Carbon\Carbon::hasFormat($promo->expires_at, 'Y-m-d H:i:s')
                                ? \Carbon\Carbon::parse($promo->expires_at)
                                : (is_object($promo->expires_at) ? $promo->expires_at : \Carbon\Carbon::parse($promo->expires_at)))
                            : null;
                        $isActive = $promo->is_active && ($expiresAt === null || $expiresAt->gt(now()));
                    @endphp
                    <tr>
                        <td>
                            <span class="pc-code">{{ strtoupper($promo->code) }}</span>
                        </td>
                        <td>
                            <div class="pc-price">Rp {{ number_format($promo->discount_amount, 0, ',', '.') }}</div>
                        </td>
                        <td>
                            <div class="pc-date">
                                @if($expiresAt)
                                    {{ $expiresAt->format('d M Y, H:i') }}
                                    @if($expiresAt->lt(now()))
                                        <span style="font-size:0.65rem;color:#ef4444;font-weight:700;margin-left:4px">(Expired)</span>
                                    @else
                                        <span style="font-size:0.65rem;color:#16a34a;font-weight:700;margin-left:4px">({{ $expiresAt->diffForHumans() }})</span>
                                    @endif
                                @else
                                    <span style="color:#94a3b8;font-style:italic">Tanpa batas waktu</span>
                                @endif
                            </div>
                        </td>
                        <td style="text-align:center">
                            @if($isActive)
                                <span class="pc-badge" style="background:#dcfce7;color:#166534">Aktif</span>
                            @else
                                <span class="pc-badge" style="background:#fee2e2;color:#991b1b">Non-Aktif</span>
                            @endif
                        </td>
                        <td style="text-align:right">
                            <a href="{{ App\Filament\Resources\PromoCodeResource::getUrl('edit', ['record' => $promo->id]) }}" class="pc-action">
                                <svg style="width:0.7rem;height:0.7rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="pc-empty">Belum ada kode promo terdaftar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($this->records->hasPages())
        <div class="pc-pagination">{{ $this->records->links() }}</div>
        @endif
    </div>
</div>

</x-filament-panels::page>

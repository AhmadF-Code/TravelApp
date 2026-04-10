<x-filament-panels::page>

<style>
    .tp-wrap { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; padding-bottom: 2rem; }

    /* TOOLBAR */
    .tp-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.25rem; }
    .tp-search-wrap { position: relative; flex: 1; max-width: 26rem; }
    .tp-search-icon { position: absolute; left: 0.875rem; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; }
    .tp-search { width: 100%; padding: 0.6rem 0.875rem 0.6rem 2.5rem; border: 1.5px solid #e2e8f0; border-radius: 0.75rem; font-size: 0.82rem; outline: none; background: white; color: #0f172a; transition: border-color 0.2s; box-sizing: border-box; }
    .tp-search:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,0.1); }
    .tp-btn-add { display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.78rem; font-weight: 700; color: white; background: linear-gradient(135deg,#f59e0b,#d97706); padding: 0.6rem 1.2rem; border-radius: 0.75rem; text-decoration: none; border: none; cursor: pointer; white-space: nowrap; transition: opacity 0.2s; }
    .tp-btn-add:hover { opacity: 0.88; }

    /* CARD TABLE */
    .tp-card { background: white; border-radius: 1.125rem; border: 1px solid #f1f5f9; box-shadow: 0 1px 4px rgba(0,0,0,0.05); overflow: hidden; }
    .tp-table { width: 100%; border-collapse: collapse; }
    .tp-table thead th { padding: 0.75rem 1.5rem; text-align: left; font-size: 0.6rem; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; color: #94a3b8; background: #f8fafc; white-space: nowrap; }
    .tp-table tbody td { padding: 1rem 1.5rem; border-top: 1px solid #f8fafc; vertical-align: middle; }
    .tp-table tbody tr:hover { background: #fafbfc; }
    .tp-cell-main { font-size: 0.85rem; font-weight: 700; color: #0f172a; }
    .tp-cell-sub { font-size: 0.65rem; color: #94a3b8; letter-spacing: 0.06em; margin-top: 0.15rem; }
    .tp-thumb { width: 3rem; height: 3rem; border-radius: 0.625rem; object-fit: cover; background: #f1f5f9; flex-shrink: 0; }
    .tp-thumb-placeholder { width: 3rem; height: 3rem; border-radius: 0.625rem; background: #f1f5f9; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .tp-badge { display: inline-block; font-size: 0.6rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; padding: 0.22rem 0.65rem; border-radius: 9999px; }
    .tp-price { font-size: 0.85rem; font-weight: 900; color: #16a34a; }
    .tp-action { display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.72rem; font-weight: 700; color: white; background: #1e293b; padding: 0.38rem 0.9rem; border-radius: 0.5rem; text-decoration: none; transition: background 0.2s; white-space: nowrap; }
    .tp-action:hover { background: #334155; }
    .tp-img-cell { display: flex; align-items: center; gap: 0.875rem; }
    .tp-badge-row { display: flex; align-items: center; gap: 0.35rem; flex-wrap: wrap; }
    .tp-empty { padding: 4rem; text-align: center; font-size: 0.85rem; color: #cbd5e1; font-style: italic; }
    .tp-pagination { padding: 1rem 1.5rem; background: #fafbfc; border-top: 1px solid #f1f5f9; }
</style>

<div class="tp-wrap">
    <!-- TOOLBAR -->
    <div class="tp-toolbar">
        <form action="{{ request()->url() }}" method="GET" style="flex:1;max-width:26rem">
            <div class="tp-search-wrap">
                <svg class="tp-search-icon" style="width:1rem;height:1rem" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama trip atau negara tujuan..." class="tp-search">
            </div>
        </form>

        <a href="{{ App\Filament\Resources\TripResource::getUrl('create') }}" class="tp-btn-add">
            <svg style="width:0.9rem;height:0.9rem" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Paket Baru
        </a>
    </div>

    <!-- TABLE CARD -->
    <div class="tp-card">
        <div style="overflow-x:auto">
            <table class="tp-table">
                <thead>
                    <tr>
                        <th>Trip Package</th>
                        <th>Destinasi</th>
                        <th>Durasi</th>
                        <th>Harga Base</th>
                        <th>Label</th>
                        <th style="text-align:right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->records as $trip)
                    <tr>
                        <td>
                            <div class="tp-img-cell">
                                @if($trip->image)
                                    <img src="{{ str_starts_with($trip->image, 'http') ? $trip->image : Storage::disk('public')->url($trip->image) }}"
                                         class="tp-thumb"
                                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                                    <div class="tp-thumb-placeholder" style="display:none">
                                        <svg style="width:1.2rem;height:1.2rem;color:#cbd5e1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @else
                                    <div class="tp-thumb-placeholder">
                                        <svg style="width:1.2rem;height:1.2rem;color:#cbd5e1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                                <div>
                                    <div class="tp-cell-main">{{ $trip->title }}</div>
                                    <div class="tp-cell-sub">{{ $trip->slug }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="tp-cell-main" style="font-weight:600">{{ $trip->destination_country ?? '—' }}</div>
                        </td>
                        <td>
                            <div style="font-size:0.85rem;font-weight:700;color:#1e293b">{{ $trip->duration_days }} Hari</div>
                        </td>
                        <td>
                            <div class="tp-price">Rp {{ number_format($trip->price, 0, ',', '.') }}</div>
                        </td>
                        <td>
                            <div class="tp-badge-row">
                                @if($trip->is_featured)
                                    <span class="tp-badge" style="background:#fef3c7;color:#92400e">Featured</span>
                                @else
                                    <span class="tp-badge" style="background:#f1f5f9;color:#475569">Standard</span>
                                @endif
                                @if($trip->is_domestic)
                                    <span class="tp-badge" style="background:#eff6ff;color:#1e40af">Domestik</span>
                                @else
                                    <span class="tp-badge" style="background:#f0fdf4;color:#166534">Internasional</span>
                                @endif
                            </div>
                        </td>
                        <td style="text-align:right">
                            <a href="{{ App\Filament\Resources\TripResource::getUrl('edit', ['record' => $trip->id]) }}" class="tp-action">
                                <svg style="width:0.75rem;height:0.75rem" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit Trip
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="tp-empty">Belum ada paket trip terdaftar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($this->records->hasPages())
        <div class="tp-pagination">
            {{ $this->records->links() }}
        </div>
        @endif
    </div>
</div>

</x-filament-panels::page>

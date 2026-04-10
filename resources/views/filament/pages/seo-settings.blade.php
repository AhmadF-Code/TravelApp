<x-filament-panels::page>

<div class="seo-root">
<style>
    .seo-root { font-family: 'Inter', sans-serif; }
    .audit-card { border-radius: 1.25rem; border: 1px solid #f1f5f9; padding: 1.5rem; background: white; margin-bottom: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
    .score-badge { font-size: 2.5rem; font-weight: 900; line-height: 1; }
    .score-lbl { font-size: 0.65rem; font-weight: 900; letter-spacing: 0.1em; text-transform: uppercase; color: #94a3b8; margin-top: 0.5rem; }
    .check-item { display: flex; items: center; gap: 0.75rem; font-size: 0.8rem; margin-top: 0.6rem; padding-bottom: 0.6rem; border-bottom: 1px solid #f8fafc; }
    .check-dot { width: 0.85rem; height: 0.85rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 0.5rem; color: white; }
    .seo-form-wrap { background: white; border-radius: 1.25rem; }
    .audit-grid { display: grid; grid-template-columns: 240px 1fr; gap: 2rem; }
    @media(max-width:800px) { .audit-grid { grid-template-columns: 1fr; } }
</style>

<div class="audit-card">
    <div class="audit-grid">
        <div style="text-align:center; padding: 1rem; border-right: 1px solid #f1f5f9">
            <div class="score-badge" style="color: {{ $this->seoAudit['score'] >= 80 ? '#16a34a' : ($this->seoAudit['score'] >= 50 ? '#f59e0b' : '#dc2626') }}">
                {{ $this->seoAudit['score'] }}
            </div>
            <div class="score-lbl">SEO Health Score</div>
            <div style="font-size:0.75rem;margin-top:1rem;color:#475569;font-weight:600">
                Landing Page Analysis
            </div>
        </div>
        <div>
            <h4 style="font-size:0.875rem;font-weight:800;color:#0f172a;margin-bottom:1rem">Rekomendasi SEO</h4>
            <div style="max-height: 180px; overflow-y: auto">
                @foreach($this->seoAudit['checks'] as $check)
                    <div class="check-item">
                        <div class="check-dot" style="background: {{ $check['lvl'] === 'ok' ? '#16a34a' : ($check['lvl'] === 'warn' ? '#f59e0b' : '#dc2626') }}">
                            @if($check['lvl'] === 'ok') ✓ @elseif($check['lvl'] === 'warn') ! @else × @endif
                        </div>
                        <span style="color:{{ $check['lvl'] === 'ok' ? '#475569' : '#0f172a' }}; font-weight:{{ $check['lvl'] === 'ok' ? '400' : '700' }}">
                            {{ $check['msg'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<form wire:submit.prevent="save">
    {{ $this->form }}
    
    <div style="margin-top: 1.5rem">
        <button type="submit" style="background:#0f172a;color:white;padding:0.75rem 2rem;border-radius:0.75rem;font-weight:800;font-size:0.875rem;cursor:pointer">
            Simpan Pengaturan SEO
        </button>
    </div>
</form>

</div>

</x-filament-panels::page>

<x-filament-panels::page>

<div class="cms-root-container">
    <style>
        .cms-wrap { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }

        /* STATUS BAR */
        .cms-status-bar { display: flex; align-items: center; gap: 1rem; padding: 1rem 1.5rem; border-radius: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; border: 1.5px solid; }
        .cms-status-live { background: #f0fdf4; border-color: #86efac; }
        .cms-status-draft { background: #fffbeb; border-color: #fcd34d; }
        .cms-status-none { background: #f8fafc; border-color: #e2e8f0; }
        .cms-version-pill { display: inline-block; font-size: 0.62rem; font-weight: 900; letter-spacing: 0.1em; text-transform: uppercase; padding: 0.2rem 0.7rem; border-radius: 9999px; }
        .cms-status-actions { display: flex; align-items: center; gap: 0.5rem; margin-left: auto; }

        /* LAYOUT */
        .cms-grid { display: grid; grid-template-columns: 1fr 360px; gap: 1.5rem; }
        @media(max-width:1100px) { .cms-grid { grid-template-columns: 1fr; } }

        /* FORM CARD */
        .cms-card { background: white; border-radius: 1.125rem; border: 1px solid #f1f5f9; box-shadow: 0 1px 4px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 1.25rem; }
        .cms-card-header { padding: 1rem 1.5rem; background: #f8fafc; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 0.625rem; }
        .cms-card-title { font-size: 0.75rem; font-weight: 900; letter-spacing: 0.08em; text-transform: uppercase; color: #64748b; }
        .cms-card-body { padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem; }

        /* FORM ELEMENTS */
        .cms-label { display: block; font-size: 0.7rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem; letter-spacing: 0.04em; }
        .cms-input, .cms-textarea { width: 100%; padding: 0.6rem 0.875rem; border: 1.5px solid #e2e8f0; border-radius: 0.625rem; font-size: 0.82rem; color: #0f172a; outline: none; background: white; transition: border-color 0.2s; box-sizing: border-box; font-family: inherit; }
        .cms-input:focus, .cms-textarea:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,0.1); }
        .cms-textarea { resize: vertical; min-height: 80px; }
        .cms-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        @media(max-width:640px) { .cms-row { grid-template-columns: 1fr; } }

        /* BUTTONS */
        .cms-btn { display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.78rem; font-weight: 700; padding: 0.6rem 1.2rem; border-radius: 0.75rem; border: none; cursor: pointer; transition: opacity 0.2s; text-decoration: none; white-space: nowrap; }
        .cms-btn:hover { opacity: 0.87; }
        .cms-btn-save { background: #f1f5f9; color: #1e293b; }
        .cms-btn-publish { background: linear-gradient(135deg,#16a34a,#15803d); color: white; }
        .cms-btn-preview { background: #eff6ff; color: #1e40af; }
        .cms-btn-rollback { background: #fef3c7; color: #92400e; font-size: 0.65rem; padding: 0.3rem 0.75rem; border-radius: 0.5rem; }
        .cms-btn-add { background: #f0fdf4; color: #166534; font-size: 0.65rem; }
        .cms-btn-remove { background: #fef2f2; color: #991b1b; font-size: 0.65rem; padding: 0.3rem 0.75rem; }

        /* REPEATER TESTIMONIAL */
        .testimonial-item { border: 1px dashed #e2e8f0; border-radius: 0.75rem; padding: 1rem; margin-bottom: 0.75rem; position: relative; }

        /* VERSION HISTORY SIDEBAR */
        .vh-item { padding: 0.875rem 1rem; border-top: 1px solid #f8fafc; display: flex; align-items: flex-start; gap: 0.75rem; }
        .vh-item:first-child { border-top: none; }
        .vh-dot { width: 0.5rem; height: 0.5rem; border-radius: 50%; flex-shrink: 0; margin-top: 0.35rem; }
        .vh-v { font-size: 0.65rem; font-weight: 800; color: #94a3b8; }
        .vh-change { font-size: 0.72rem; color: #475569; }
        .vh-date { font-size: 0.6rem; color: #cbd5e1; }
        .vh-meta { flex: 1; }
    </style>

    <div class="cms-wrap" wire:ignore.self>

        @php
            $active = $this->activeVersion;
            $draft  = $this->draftVersion;
        @endphp

        <div class="cms-status-bar {{ $active ? 'cms-status-live' : 'cms-status-none' }}">
            <div>
                @if($active)
                    <span class="cms-version-pill" style="background:#dcfce7;color:#166534">LIVE v{{ $active->version }}</span>
                    <span style="font-size:0.72rem;color:#166534;font-weight:600;margin-left:0.5rem">
                        Tayang sejak {{ $active->published_at?->format('d M Y H:i') ?? '—' }}
                    </span>
                @else
                    <span class="cms-version-pill" style="background:#f1f5f9;color:#64748b">BELUM ADA VERSI LIVE</span>
                @endif
            </div>

            @if($draft && $draft->status === 'draft')
            <div>
                <span class="cms-version-pill" style="background:#fef3c7;color:#92400e">DRAFT v{{ $draft->version }}</span>
            </div>
            @endif

            <div class="cms-status-actions">
                <a href="/preview-landing" target="_blank" class="cms-btn cms-btn-preview">
                    <svg style="width:0.85rem;height:0.85rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Preview Draft
                </a>
            </div>
        </div>

        <div class="cms-grid">
            <div>
                {{-- Section 1: Hero --}}
                <div class="cms-card">
                    <div class="cms-card-header">
                        <svg style="width:0.9rem;height:0.9rem;color:#f59e0b" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="cms-card-title">Hero Section</span>
                    </div>
                    <div class="cms-card-body">
                        <div>
                            <label class="cms-label">Internal Name</label>
                            <input type="text" class="cms-input" wire:model="title_field">
                        </div>
                        <div class="cms-row">
                            <div>
                                <label class="cms-label">Hero Title</label>
                                <input type="text" class="cms-input" wire:model="hero_title">
                            </div>
                            <div>
                                <label class="cms-label">Hero Subtitle</label>
                                <input type="text" class="cms-input" wire:model="hero_subtitle">
                            </div>
                        </div>
                        <div>
                            <label class="cms-label">Hero Text</label>
                            <textarea class="cms-textarea" wire:model="hero_text"></textarea>
                        </div>
                        <div>
                            <label class="cms-label">Hero Background URL</label>
                            <input type="text" class="cms-input" wire:model="hero_background_image">
                        </div>
                    </div>
                </div>

                {{-- Section 2: About --}}
                <div class="cms-card">
                    <div class="cms-card-header">
                        <svg style="width:0.9rem;height:0.9rem;color:#8b5cf6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                        <span class="cms-card-title">About Us</span>
                    </div>
                    <div class="cms-card-body">
                        <div class="cms-row">
                            <div>
                                <label class="cms-label">About Title</label>
                                <input type="text" class="cms-input" wire:model="about_title">
                            </div>
                            <div>
                                <label class="cms-label">About Subtitle</label>
                                <input type="text" class="cms-input" wire:model="about_subtitle">
                            </div>
                        </div>
                        <div>
                            <label class="cms-label">About Text</label>
                            <textarea class="cms-textarea" wire:model="about_text"></textarea>
                        </div>
                        <div>
                            <label class="cms-label">About Image URL</label>
                            <input type="text" class="cms-input" wire:model="about_image">
                        </div>
                    </div>
                </div>

                {{-- Section 3: Testimonials --}}
                <div class="cms-card">
                    <div class="cms-card-header">
                        <svg style="width:0.9rem;height:0.9rem;color:#ec4899" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        <span class="cms-card-title">Testimonials</span>
                        <button wire:click="addTestimonial" class="cms-btn cms-btn-add" style="margin-left:auto">+ Tambah</button>
                    </div>
                    <div class="cms-card-body">
                        <div>
                            <label class="cms-label">Testimonial Section Title</label>
                            <input type="text" class="cms-input" wire:model="testimonial_title">
                        </div>
                        @foreach($testimonials as $idx => $testi)
                        <div class="testimonial-item">
                            <button wire:click="removeTestimonial({{ $idx }})" class="cms-btn cms-btn-remove" style="position:absolute;top:0.5rem;right:0.5rem">Hapus</button>
                            <div class="cms-row">
                                <div>
                                    <label class="cms-label">Nama Pengunjung</label>
                                    <input type="text" class="cms-input" wire:model="testimonials.{{ $idx }}.name">
                                </div>
                                <div>
                                    <label class="cms-label">Rating (1-5)</label>
                                    <input type="number" class="cms-input" wire:model="testimonials.{{ $idx }}.stars" min="1" max="5">
                                </div>
                            </div>
                            <div style="margin-top:0.5rem">
                                <label class="cms-label">Komentar / Quote</label>
                                <textarea class="cms-textarea" wire:model="testimonials.{{ $idx }}.quote" rows="2"></textarea>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Section 4: CTA & Footer --}}
                <div class="cms-card">
                    <div class="cms-card-header">
                        <svg style="width:0.9rem;height:0.9rem;color:#3b82f6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span class="cms-card-title">CTA & Footer</span>
                    </div>
                    <div class="cms-card-body">
                        <div class="cms-row">
                            <div>
                                <label class="cms-label">CTA Title</label>
                                <input type="text" class="cms-input" wire:model="cta_title">
                            </div>
                            <div>
                                <label class="cms-label">CTA Subtitle</label>
                                <input type="text" class="cms-input" wire:model="cta_subtitle">
                            </div>
                        </div>
                        <hr style="border:none;border-top:1px solid #f1f5f9;margin:0.5rem 0">
                        <div>
                            <label class="cms-label">Footer Description</label>
                            <textarea class="cms-textarea" wire:model="footer_text" rows="2"></textarea>
                        </div>
                        <div class="cms-row">
                            <div>
                                <label class="cms-label">Footer Email</label>
                                <input type="email" class="cms-input" wire:model="footer_email">
                            </div>
                            <div>
                                <label class="cms-label">Footer Phone</label>
                                <input type="text" class="cms-input" wire:model="footer_phone">
                            </div>
                        </div>
                        <div>
                            <label class="cms-label">Footer Address</label>
                            <input type="text" class="cms-input" wire:model="footer_address">
                        </div>
                    </div>
                </div>

                {{-- Validation & Publish --}}
                <div class="cms-card">
                    <div class="cms-card-header">
                        <svg style="width:0.9rem;height:0.9rem;color:#16a34a" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="cms-card-title">Approval</span>
                    </div>
                    <div class="cms-card-body">
                        <label class="cms-label">Catatan Perubahan</label>
                        <input type="text" class="cms-input" wire:model="change_summary" placeholder="Apa yang diubah di versi ini?">
                        <div style="display:flex;gap:0.75rem;margin-top:1rem">
                            <button wire:click="saveDraft" class="cms-btn cms-btn-save">Simpan Draft</button>
                            <button wire:click="publishDraft" class="cms-btn cms-btn-publish">Publish ke Live</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar History --}}
            <div>
                <div class="cms-card" style="position:sticky;top:1.5rem">
                    <div class="cms-card-header">
                        <span class="cms-card-title">Riwayat Versi</span>
                    </div>
                    <div>
                        @foreach($this->versionHistory as $v)
                        <div class="vh-item">
                            <div class="vh-dot" style="background:{{ $v->status === 'active' ? '#16a34a' : '#cbd5e1' }}"></div>
                            <div class="vh-meta">
                                <span class="vh-v">v{{ $v->version }}</span>
                                <div class="vh-change">{{ $v->change_summary ?: 'Tanpa catatan' }}</div>
                                <div class="vh-date">{{ ($v->published_at ?? $v->created_at)->format('d M H:i') }}</div>
                                @if($v->status === 'archived')
                                <button wire:click="rollback({{ $v->id }})" class="cms-btn cms-btn-rollback" style="margin-top:0.3rem">Rollback</button>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</x-filament-panels::page>

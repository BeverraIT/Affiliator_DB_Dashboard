@extends('layouts.admin')

@section('title', 'Follow Up Chat')

@push('styles')
<style>
/* ── MD3 Design Tokens ─────────────────────────────────────── */

/* ── Type Scale ──────────────────────────────────────────────── */
.ts-headline-sm { font-size: 1.5rem;   font-weight: 600; line-height: 2rem;    color: var(--md-on-surface); }
.ts-title-sm    { font-size: .875rem;  font-weight: 500; line-height: 1.25rem; letter-spacing: .007em; color: var(--md-on-surface); }
.ts-body-sm     { font-size: .75rem;   font-weight: 400; line-height: 1rem;    letter-spacing: .025em; }
.ts-label-md    { font-size: .75rem;   font-weight: 500; letter-spacing: .05em; }

/* ── Surface Card ────────────────────────────────────────────── */
.surf-card { background: #fff; border-radius: var(--shape-lg); box-shadow: var(--md-elev-1); border: none; }

/* ── MD3 Table ───────────────────────────────────────────────── */
.md-table { width: 100%; border-collapse: collapse; }
.md-table thead th {
    padding: .75rem 1rem;
    font-size: .6875rem; font-weight: 700; letter-spacing: .07em; text-transform: uppercase;
    color: var(--md-on-surface-var); background: #F8F9FF;
    border-bottom: 1px solid var(--md-outline-var); white-space: nowrap;
}
.md-table thead th:first-child { border-radius: var(--shape-lg) 0 0 0; }
.md-table thead th:last-child  { border-radius: 0 var(--shape-lg) 0 0; }
.md-table tbody tr { border-bottom: 1px solid var(--md-outline-var); transition: background .12s; }
.md-table tbody tr:last-child { border-bottom: none; }
.md-table tbody tr:hover { background: rgba(21,101,192,.04); }
.md-table tbody td { padding: .875rem 1rem; vertical-align: middle; font-size: .8125rem; }

/* Table scroll wrapper */
.md-table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }

/* ── MD3 Chips ───────────────────────────────────────────────── */
.chip {
    display: inline-flex; align-items: center; gap: .25rem;
    padding: .2rem .625rem; border-radius: 8px;
    font-size: .6875rem; font-weight: 500; letter-spacing: .03em;
    white-space: nowrap; line-height: 1.4; user-select: none;
}
.chip-primary { background: var(--md-primary-cont); color: var(--md-on-primary-cont); }
.chip-surface { background: var(--md-surface-var);  color: var(--md-on-surface-var); }

/* ── MD3 Icon Buttons ────────────────────────────────────────── */
.btn-icon-md {
    display: inline-flex; align-items: center; justify-content: center;
    width: 40px; height: 40px; border-radius: 50%;
    border: none; cursor: pointer; flex-shrink: 0; text-decoration: none;
    transition: background-color .15s, color .15s;
    -webkit-tap-highlight-color: transparent; background: transparent;
}
.btn-icon-md:focus-visible { outline: 2px solid var(--md-primary); outline-offset: 2px; }
.btn-icon-md.sz-sm         { width: 28px; height: 28px; }

.btn-icon-md.flat          { color: var(--md-on-surface-var); }
.btn-icon-md.flat:hover    { background: rgba(26,27,31,.08); color: var(--md-on-surface); }

/* ── MD3 Label Buttons ───────────────────────────────────────── */
.btn-md {
    display: inline-flex; align-items: center; justify-content: center; gap: .375rem;
    padding: .5625rem 1.25rem; border-radius: 100px;
    font-size: .875rem; font-weight: 500; letter-spacing: .006em;
    border: none; cursor: pointer; text-decoration: none;
    transition: background-color .15s, box-shadow .15s, color .15s;
    white-space: nowrap; -webkit-tap-highlight-color: transparent; line-height: 1.25;
}
.btn-md:focus-visible        { outline: 2px solid var(--md-primary); outline-offset: 2px; }
.btn-md.sm                   { padding: .34375rem .875rem; font-size: .8125rem; }

.btn-md.filled-primary       { background: var(--md-primary);        color: var(--md-on-primary); }
.btn-md.filled-primary:hover { background: #1255A6; box-shadow: var(--md-elev-1); color: var(--md-on-primary); }

.btn-md.tonal-success        { background: var(--md-secondary-cont); color: var(--md-on-secondary-cont); }
.btn-md.tonal-success:hover  { filter: brightness(.96); box-shadow: var(--md-elev-1); }

.btn-md.outlined             { background: transparent; color: var(--md-on-surface-var); border: 1.5px solid var(--md-outline-var); }
.btn-md.outlined:hover       { background: rgba(26,27,31,.06); border-color: var(--md-outline); color: var(--md-on-surface); }

.btn-md.text-neutral         { background: transparent; color: var(--md-on-surface-var); }
.btn-md.text-neutral:hover   { background: rgba(26,27,31,.06); color: var(--md-on-surface); }

/* ── MD3 Banners ─────────────────────────────────────────────── */
.md-banner {
    display: flex; align-items: flex-start; gap: .75rem;
    padding: .875rem 1.125rem; border-radius: var(--shape-md);
    margin-bottom: 1rem; font-size: .875rem; line-height: 1.5;
}
.md-banner-success { background: var(--md-secondary-cont); color: var(--md-on-secondary-cont); border-left: 4px solid #1B5E20; }
.md-banner-error   { background: var(--md-error-cont);     color: var(--md-on-error-cont);     border-left: 4px solid var(--md-error); }

/* ── Filter Panel ────────────────────────────────────────────── */
.filter-panel {
    background: #fff; border-radius: var(--shape-lg);
    border: 1px solid var(--md-outline-var);
    padding: 1rem 1.125rem; margin-bottom: 1.25rem;
}
.filter-label {
    display: block; font-size: .6875rem; font-weight: 700;
    letter-spacing: .08em; text-transform: uppercase;
    color: var(--md-on-surface-var); margin-bottom: .3rem;
}

/* Search field with leading icon */
.search-field       { position: relative; }
.search-field__icon {
    position: absolute; left: .65rem; top: 50%; transform: translateY(-50%);
    color: var(--md-on-surface-var); display: flex; align-items: center;
    pointer-events: none; z-index: 1;
}
.search-field input.form-control { padding-left: 2.25rem; }

/* ── Page Header ─────────────────────────────────────────────── */
.md-page-hdr {
    display: flex; justify-content: space-between; align-items: flex-start;
    gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;
}
.md-page-hdr__actions { display: flex; align-items: center; gap: .5rem; flex-wrap: wrap; }

/* Counter pill */
.md-counter-pill {
    display: inline-flex; align-items: center; gap: .5rem;
    padding: .4375rem 1rem; border-radius: 100px;
    border: 1px solid var(--md-outline-var); background: #fff;
    font-size: .8125rem; font-weight: 500; color: var(--md-on-surface);
    box-shadow: var(--md-elev-1); white-space: nowrap;
}
.md-counter-pill__dot          { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.md-counter-pill__dot--warning { background: #BF360C; }

/* ── MD3 Empty State ─────────────────────────────────────────── */
.md-empty {
    display: flex; flex-direction: column; align-items: center;
    justify-content: center; padding: 3rem 1.5rem; gap: .75rem; text-align: center;
}
.md-empty__icon {
    width: 64px; height: 64px; border-radius: 50%;
    background: var(--md-surface-var);
    display: flex; align-items: center; justify-content: center;
    color: var(--md-on-surface-var); margin-bottom: .25rem; flex-shrink: 0;
}
.md-empty__title { font-size: .9375rem; font-weight: 500; color: var(--md-on-surface); margin: 0; }
.md-empty__body  { font-size: .8125rem; color: var(--md-on-surface-var); margin: 0; max-width: 320px; }

/* ── MD3 Pagination ──────────────────────────────────────────── */
.md-pagination {
    display: flex; justify-content: center;
    padding: 1rem; border-top: 1px solid var(--md-outline-var);
}
.md-pagination .pagination { gap: 2px; margin: 0; }
.md-pagination .page-link {
    border-radius: 50% !important; width: 36px; height: 36px;
    display: flex !important; align-items: center; justify-content: center;
    border: none !important; padding: 0;
    color: var(--md-on-surface-var); font-size: .875rem; font-weight: 500;
    background: transparent; transition: background .12s; box-shadow: none !important;
}
.md-pagination .page-link:hover           { background: rgba(26,27,31,.08); color: var(--md-on-surface); }
.md-pagination .page-item.active .page-link   { background: var(--md-primary) !important; color: #fff !important; }
.md-pagination .page-item.disabled .page-link { color: var(--md-on-surface-var); opacity: .38; }

/* ── Responsive ──────────────────────────────────────────────── */
@media (max-width: 767px) {
    .md-table tbody td { padding: .625rem .75rem; }
    .md-table thead th { padding: .625rem .75rem; }
    .md-page-hdr       { flex-direction: column; align-items: flex-start; }
}
</style>
@endpush

@section('content')

    {{-- ── Page Header ──────────────────────────────────────────── --}}
    <div class="md-page-hdr">
        <div>
            <h1 class="ts-headline-sm mb-1">Follow Up Chat</h1>
            <p class="ts-body-sm mb-0" style="color: var(--md-on-surface-var);">
                Pantau status komunikasi kreator yang perlu di-follow up via chat.
            </p>
        </div>

        <div class="md-page-hdr__actions">
            <div class="md-counter-pill">
                <span class="md-counter-pill__dot md-counter-pill__dot--warning"></span>
                <span>Perlu Follow Up</span>
                <strong style="color: var(--md-primary);">{{ $items->total() }}</strong>
            </div>
        </div>
    </div>

    {{-- ── Flash Banners ────────────────────────────────────────── --}}
    @if (session('success'))
        <div class="md-banner md-banner-success">
            <svg viewBox="0 0 20 20" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:.1rem;">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="md-banner md-banner-error">
            <svg viewBox="0 0 20 20" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:.1rem;">
                <circle cx="10" cy="10" r="9" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M10 6v4m0 4h.01" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- ── Filter Panel ─────────────────────────────────────────── --}}
    <div class="filter-panel">
        <form method="GET" action="{{ route('admin.followup.chat.index') }}">
            <div class="row g-2 align-items-end">

                {{-- Dari Tanggal --}}
                <div class="col-6 col-md-2">
                    <label class="filter-label">Dari Tanggal</label>
                    <input type="date"
                           name="start_date"
                           class="form-control form-control-sm"
                           value="{{ request('start_date') }}">
                </div>

                {{-- Sampai Tanggal --}}
                <div class="col-6 col-md-2">
                    <label class="filter-label">Sampai Tanggal</label>
                    <input type="date"
                           name="end_date"
                           class="form-control form-control-sm"
                           value="{{ request('end_date') }}">
                </div>

                {{-- SKU Utama --}}
                <div class="col-12 col-md-3">
                    <label class="filter-label">SKU Utama</label>
                    <select name="sku_utama" class="form-select form-select-sm select2-init" style="width:100%;">
                        <option value="">Semua SKU Utama</option>
                        @foreach($skuUtamaOptions as $prod)
                            <option value="{{ $prod->sku_utama }}" {{ $skuUtama == $prod->sku_utama ? 'selected' : '' }}>
                                {{ $prod->sku_utama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Pencarian --}}
                <div class="col-12 col-md">
                    <label class="filter-label">Pencarian</label>
                    <div class="search-field">
                        <span class="search-field__icon">
                            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                        </span>
                        <input type="text" name="q"
                               value="{{ $q ?? '' }}"
                               class="form-control form-control-sm"
                               placeholder="Cari Kreator, Toko, Produk...">
                    </div>
                </div>

                {{-- Aksi --}}
                <div class="col-auto d-flex gap-2">
                    <button type="submit" class="btn-md sm filled-primary">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        Filter
                    </button>
                    @if(!empty($q) || !empty($skuUtama) || request('start_date') || request('end_date'))
                        <a href="{{ route('admin.followup.chat.index') }}" class="btn-md sm outlined">Reset</a>
                    @endif
                </div>

            </div>
        </form>
    </div>

    {{-- ── Data Table ───────────────────────────────────────────── --}}
    <div class="surf-card">
        <div class="md-table-scroll">
            <table class="md-table">
                <thead>
                    <tr>
                        <th style="min-width: 170px;">Kreator</th>
                        <th style="min-width: 180px;">Toko & PIC</th>
                        <th style="min-width: 160px;">Produk</th>
                        <th style="min-width: 150px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($items as $item)
                    @php
                        $kreatorName = optional($item->creator)->username ?? optional($item->creator)->name ?? '-';
                    @endphp

                    <tr>
                        {{-- KREATOR --}}
                        <td>
                            <div class="d-flex flex-column gap-1">
                                <span class="ts-title-sm">{{ $kreatorName }}</span>

                                @if(optional($item->creator)->namawa)
                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                        <span class="chip chip-surface">
                                            <i class="bi bi-whatsapp" style="font-size:.65rem;"></i>
                                            {{ $item->creator->namawa }}
                                        </span>
                                        <button type="button"
                                                class="btn-icon-md sz-sm flat"
                                                onclick="copyToClipboard('{{ $item->creator->namawa }}', this)"
                                                title="Salin Nama WA"
                                                aria-label="Salin Nama WA">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
                                                <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </td>

                        {{-- TOKO & PIC --}}
                        <td>
                            <div class="d-flex flex-column gap-1">
                                <span class="ts-title-sm">
                                    {{ optional($item->store)->name ?? '-' }}
                                </span>

                                @if($item->user)
                                    <span class="chip chip-primary align-self-start">
                                        PIC: {{ $item->user->name }}
                                    </span>
                                @else
                                    <span class="chip chip-surface align-self-start">PIC: —</span>
                                @endif

                                <span class="ts-body-sm" style="color: var(--md-on-surface-var);">
                                    Tanggal:
                                    <strong style="color: var(--md-on-surface);">
                                        {{ $item->tanggal_aktual
                                            ? \Carbon\Carbon::parse($item->tanggal_aktual)->translatedFormat('d M Y')
                                            : '—' }}
                                    </strong>
                                </span>
                            </div>
                        </td>

                        {{-- PRODUK --}}
                        <td>
                            <div class="d-flex flex-column gap-1">
                                <span class="ts-title-sm">
                                    {{ optional($item->product)->sku ?? '-' }}
                                </span>
                                <span class="ts-body-sm" style="color: var(--md-on-surface-var);">
                                    Qty: <strong style="color: var(--md-on-surface);">{{ $item->qty ?? 0 }}</strong>
                                </span>
                            </div>
                        </td>

                        {{-- AKSI --}}
                        <td style="text-align: center;">
                            <form method="POST" action="{{ route('admin.followup.chat.chatStatus', $item->id) }}">
                                @csrf
                                <button type="submit" class="btn-md sm tonal-success">
                                    <svg viewBox="0 0 20 20" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 10l4 4L16 6"/>
                                    </svg>
                                    Sudah Dichat
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <div class="md-empty">
                                <div class="md-empty__icon">
                                    <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                        <path d="M9 10h.01M12 10h.01M15 10h.01"/>
                                    </svg>
                                </div>
                                <p class="md-empty__title">Tidak ada data</p>
                                <p class="md-empty__body">Semua kreator sudah di-follow up atau tidak ada yang sesuai filter.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($items->hasPages())
            <div class="md-pagination">
                {{ $items->links() }}
            </div>
        @endif
    </div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2-init').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Pilih SKU Utama...',
            allowClear: true
        });
    });

    function copyToClipboard(text, btnElement) {
        if (!text) return;
        navigator.clipboard.writeText(text).then(() => {
            const originalHtml = btnElement.innerHTML;
            btnElement.innerHTML = `<svg viewBox="0 0 16 16" width="13" height="13" fill="currentColor"><path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z" fill="#1B5E20"/></svg>`;
            setTimeout(() => { btnElement.innerHTML = originalHtml; }, 1200);
        }).catch(() => {
            alert('Gagal menyalin teks.');
        });
    }
</script>
@endpush

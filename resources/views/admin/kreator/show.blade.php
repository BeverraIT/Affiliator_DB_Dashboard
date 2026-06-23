@extends('layouts.admin')

@section('title', 'Detail Kreator')

@push('styles')
<style>
/* ─── MD3 Design Tokens ─────────────────────────────────────── */

/* ─── Type Scale ─────────────────────────────────────────────── */
.ts-headline-sm { font-size: 1.5rem;   font-weight: 600; line-height: 2rem; }
.ts-title-lg    { font-size: 1.125rem; font-weight: 500; line-height: 1.75rem; }
.ts-body-sm     { font-size: .75rem;   font-weight: 400; line-height: 1rem;   letter-spacing: .025em; }
.ts-label-sm    { font-size: .6875rem; font-weight: 500; letter-spacing: .06em; }

/* ─── Surface Card ───────────────────────────────────────────── */
.surf-card { background: #fff; border-radius: var(--shape-lg); box-shadow: var(--md-elev-1); border: none; }
.surf-card:hover { box-shadow: var(--md-elev-2); }

/* ─── Stat Cards (MD3 Filled Tonal) ──────────────────────────── */
.stat-card {
    border-radius: var(--shape-lg);
    padding: 1.125rem 1.25rem;
    position: relative; overflow: hidden;
    transition: box-shadow .2s ease, transform .2s ease;
    box-shadow: var(--md-elev-1);
}
.stat-card:hover { transform: translateY(-2px); box-shadow: var(--md-elev-2); }
.stat-card.sc-primary   { background: var(--md-primary-cont); }
.stat-card.sc-secondary { background: var(--md-secondary-cont); }
.stat-card.sc-tertiary  { background: var(--md-tertiary-cont); }
.stat-card .s-label {
    font-size: .6875rem; font-weight: 700; letter-spacing: .08em;
    text-transform: uppercase; margin-bottom: .25rem;
}
.stat-card.sc-primary   .s-label { color: var(--md-on-primary-cont);   opacity: .7; }
.stat-card.sc-secondary .s-label { color: var(--md-on-secondary-cont); opacity: .7; }
.stat-card.sc-tertiary  .s-label { color: var(--md-on-tertiary-cont);  opacity: .7; }
.stat-card .s-value { font-size: 1.375rem; font-weight: 700; line-height: 1.2; margin-bottom: .1875rem; }
.stat-card.sc-primary   .s-value { color: var(--md-on-primary-cont); }
.stat-card.sc-secondary .s-value { color: var(--md-on-secondary-cont); }
.stat-card.sc-tertiary  .s-value { color: var(--md-on-tertiary-cont); }
.stat-card .s-sub { font-size: .75rem; }
.stat-card.sc-primary   .s-sub { color: var(--md-on-primary-cont);   opacity: .6; }
.stat-card.sc-secondary .s-sub { color: var(--md-on-secondary-cont); opacity: .6; }
.stat-card.sc-tertiary  .s-sub { color: var(--md-on-tertiary-cont);  opacity: .6; }

/* ─── Chips ──────────────────────────────────────────────────── */
.chip {
    display: inline-flex; align-items: center; gap: .25rem;
    padding: .25rem .75rem; border-radius: 8px;
    font-size: .75rem; font-weight: 500; white-space: nowrap;
}
.chip-primary   { background: var(--md-primary-cont);   color: var(--md-on-primary-cont); }
.chip-secondary { background: var(--md-secondary-cont); color: var(--md-on-secondary-cont); }
.chip-tertiary  { background: var(--md-tertiary-cont);  color: var(--md-on-tertiary-cont); }
.chip-error     { background: var(--md-error-cont);     color: var(--md-on-error-cont); }
.chip-surface   { background: var(--md-surface-var);    color: var(--md-on-surface-var); }
.chip-warning   { background: #FFF3E0; color: #BF360C; }

/* ─── MD3 Table ──────────────────────────────────────────────── */
.md-table { width: 100%; border-collapse: collapse; }
.md-table thead th {
    padding: .75rem 1rem;
    font-size: .6875rem; font-weight: 700; letter-spacing: .07em; text-transform: uppercase;
    color: var(--md-on-surface-var); background: #F8F9FF;
    border-bottom: 1px solid var(--md-outline-var); white-space: nowrap;
}
.md-table tbody tr { border-bottom: 1px solid var(--md-outline-var); transition: background .12s; }
.md-table tbody tr:last-child { border-bottom: none; }
.md-table tbody tr:hover { background: rgba(21,101,192,.04); }
.md-table tbody td { padding: .8125rem 1rem; vertical-align: middle; font-size: .875rem; }

/* ─── MD3 Buttons ────────────────────────────────────────────── */
.btn-md {
    display: inline-flex; align-items: center; justify-content: center; gap: .375rem;
    padding: .5625rem 1.25rem; border-radius: 20px;
    font-size: .875rem; font-weight: 500;
    border: none; cursor: pointer; text-decoration: none; transition: all .15s;
}
.btn-md:focus-visible           { outline: 2px solid var(--md-primary); outline-offset: 2px; }
.btn-md.filled-primary          { background: var(--md-primary); color: var(--md-on-primary); }
.btn-md.filled-primary:hover    { background: #1255A6; box-shadow: 0 2px 8px rgba(21,101,192,.35); color: var(--md-on-primary); }
.btn-md.outlined                { background: transparent; color: var(--md-on-surface-var); border: 1.5px solid var(--md-outline-var); }
.btn-md.outlined:hover          { background: rgba(26,27,31,.06); border-color: var(--md-outline); color: var(--md-on-surface); }

.btn-icon-md {
    display: inline-flex; align-items: center; justify-content: center;
    width: 32px; height: 32px; border-radius: 50%;
    border: none; cursor: pointer; text-decoration: none; transition: all .15s;
}
.btn-icon-md.ghost       { background: transparent; color: var(--md-on-surface-var); border: 1.5px solid var(--md-outline-var); }
.btn-icon-md.ghost:hover { background: rgba(26,27,31,.06); border-color: var(--md-outline); }

/* ─── Section Title ──────────────────────────────────────────── */
.ks-section-title {
    display: flex; align-items: center; gap: .5rem;
    font-size: .75rem; font-weight: 700; letter-spacing: .08em;
    text-transform: uppercase; color: var(--md-primary);
    margin-bottom: 1rem; padding-bottom: .625rem;
    border-bottom: 1px solid var(--md-outline-var);
}
.ks-section-title::before {
    content: ''; display: inline-block; width: 8px; height: 8px;
    border-radius: 50%; background: var(--md-primary); flex-shrink: 0;
}
</style>
@endpush

@section('content')
@php
    $genderLabel = match($creator->genderpengikut) {
        'perempuan' => 'Perempuan',
        'laki-laki' => 'Laki-laki',
        'seimbang'  => 'Seimbang',
        default     => '-',
    };
    $channelLabel = match($creator->channel) {
        'video'        => 'Video',
        'live'         => 'Live',
        'kartu produk' => 'Kartu Produk',
        default        => '-',
    };
    $genderClass = match($creator->genderpengikut) {
        'perempuan' => 'chip chip-error',
        'laki-laki' => 'chip chip-primary',
        'seimbang'  => 'chip chip-secondary',
        default     => 'chip chip-surface',
    };
    $channelClass = match($creator->channel) {
        'video'        => 'chip chip-primary',
        'live'         => 'chip chip-warning',
        'kartu produk' => 'chip chip-surface',
        default        => 'chip chip-surface',
    };
    $waRaw = preg_replace('/\D+/', '', (string) ($creator->nomerwa ?? ''));
    $waNumber = $waRaw
        ? (str_starts_with($waRaw, '0') ? '62'.substr($waRaw, 1) : $waRaw)
        : null;
    $tiktokUrl = 'https://www.tiktok.com/@' . rawurlencode(ltrim((string) $creator->username, '@'));
@endphp

<div style="max-width: 860px;">

    {{-- ── Page Header ─────────────────────────────────────────── --}}
    <header class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="ts-headline-sm mb-0" style="color: var(--md-on-surface);">Detail Kreator</h1>
        <div class="d-flex gap-2">
            <a href="{{ url()->previous() }}" class="btn-md outlined">Kembali</a>
            <a href="{{ route('admin.kreator.edit', $creator->id) }}" class="btn-md filled-primary">Edit</a>
        </div>
    </header>

    {{-- ── Identity Card ────────────────────────────────────────── --}}
    <div class="surf-card p-4 mb-3">
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                    <a href="{{ $tiktokUrl }}" target="_blank" rel="noopener noreferrer"
                        class="ts-title-lg text-decoration-none fw-semibold"
                        style="color: var(--md-on-surface);">
                        {{ $creator->username }}
                    </a>
                    @if((int)$creator->top_creator === 1)
                        <span class="chip chip-warning" title="Top Creator">★ Top</span>
                    @endif
                    @if((int)$creator->is_blocked === 1)
                        <span class="chip chip-error">Diblokir</span>
                    @endif
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="{{ $channelClass }}">{{ $channelLabel }}</span>
                    <span class="{{ $genderClass }}">{{ $genderLabel }}</span>
                </div>
            </div>
            <div class="ts-body-sm" style="color: var(--md-on-surface-var);">
                Diinput oleh: <span class="fw-medium">{{ $creator->user->name ?? '—' }}</span>
            </div>
        </div>
    </div>

    {{-- ── Stat Cards ───────────────────────────────────────────── --}}
    <div class="row g-3 mb-3">
        <div class="col-12 col-md-4">
            <div class="stat-card sc-primary">
                <div class="s-label">GMV</div>
                <div class="s-value">Rp {{ number_format((int) $creator->gmv, 0, ',', '.') }}</div>
                <div class="s-sub">Total nilai penjualan</div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="stat-card sc-secondary">
                <div class="s-label">Item Terjual</div>
                <div class="s-value">{{ number_format((int) $creator->itemsterjual, 0, ',', '.') }}</div>
                <div class="s-sub">Jumlah produk</div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="stat-card sc-tertiary">
                <div class="s-label">Kontak</div>
                <div class="s-value" style="font-size: 1rem;">{{ $creator->namawa ?: '—' }}</div>
                <div class="s-sub d-flex align-items-center gap-1">
                    <span>{{ $creator->nomerwa ?: '—' }}</span>
                    @if($waNumber)
                        <button type="button"
                            class="btn-icon-md ghost"
                            style="width:22px;height:22px;"
                            data-copy="{{ $creator->nomerwa }}"
                            onclick="copyIdPesanan(this)"
                            aria-label="Salin Nomor WA" title="Salin Nomor WA">
                            <svg viewBox="0 0 20 20" width="12" height="12" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M8 3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1V3z"/>
                                <path d="M6 3H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1h-2"/>
                            </svg>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── Umur ─────────────────────────────────────────────────── --}}
    <div class="surf-card p-4 mb-3">
        <div class="ks-section-title">Umur Pengikut</div>
        <div class="d-flex flex-wrap gap-2">
            @forelse($creator->ages as $age)
                <span class="chip chip-surface">{{ $age->name }}</span>
            @empty
                <span class="chip chip-surface" style="opacity:.6;">Belum diisi</span>
            @endforelse
        </div>
    </div>

    {{-- ── Kategori ─────────────────────────────────────────────── --}}
    <div class="surf-card p-4 mb-3">
        <div class="ks-section-title">Kategori</div>
        <div class="d-flex flex-wrap gap-2">
            @forelse($creator->categories as $cat)
                <span class="chip chip-surface">{{ $cat->name }}</span>
            @empty
                <span class="chip chip-surface" style="opacity:.6;">Belum ada kategori</span>
            @endforelse
        </div>
    </div>

    {{-- ── Konten Style ─────────────────────────────────────────── --}}
    <div class="surf-card p-4 mb-3">
        <div class="ks-section-title">Konten Style</div>
        <div class="d-flex flex-wrap gap-2">
            @forelse($creator->contentStyles as $style)
                <span class="chip chip-surface">{{ $style->name }}</span>
            @empty
                <span class="chip chip-surface" style="opacity:.6;">Belum ada konten style</span>
            @endforelse
        </div>
    </div>

    {{-- ── Histori Sampel ───────────────────────────────────────── --}}
    <div class="surf-card mb-4">
        <div class="p-4 pb-0">
            <div class="ks-section-title">Sampel Dikirim</div>
        </div>
        <div class="table-responsive">
            <table class="md-table">
                <thead>
                    <tr>
                        <th>Tanggal Deal</th>
                        <th>SKU Utama</th>
                        <th>Toko</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($creator->sendSamples as $sample)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($sample->deal_date)->format('d M Y') }}</td>
                            <td>{{ $sample->product->sku_utama ?? 'N/A' }}</td>
                            <td>{{ $sample->store->name ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align:center; color: var(--md-on-surface-var); padding: 2rem 1rem;">
                                Belum ada sampel dikirim
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ── Toast Copy ───────────────────────────────────────────── --}}
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
    <div id="copyToast" class="toast align-items-center text-bg-dark border-0" role="alert" aria-live="polite" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="copyToastText">Tersalin.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin/kirim-sampel-index.js') }}"></script>
@endpush

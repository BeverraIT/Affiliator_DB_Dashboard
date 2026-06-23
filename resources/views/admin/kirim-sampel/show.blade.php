@extends('layouts.admin')

@section('title', 'Detail Pengiriman')

@push('styles')
<style>
/* ── MD3 Design Tokens ───────────────────────────────────── */

/* ── Type Scale ──────────────────────────────────────────── */
.ts-headline-sm { font-size: 1.5rem;  font-weight: 600; line-height: 2rem; }
.ts-body-sm     { font-size: .75rem;  font-weight: 400; line-height: 1rem;   letter-spacing: .025em; }
.ts-label-sm    { font-size: .6875rem; font-weight: 500; letter-spacing: .06em; }

/* ── Surface Card ────────────────────────────────────────── */
.surf-card { background: #fff; border-radius: var(--shape-lg); box-shadow: var(--md-elev-1); border: none; }

/* ── MD3 Buttons ─────────────────────────────────────────── */
.btn-md {
    display: inline-flex; align-items: center; justify-content: center; gap: .375rem;
    padding: .5625rem 1.25rem; border-radius: 20px;
    font-size: .875rem; font-weight: 500;
    border: none; cursor: pointer; text-decoration: none; transition: all .15s;
}
.btn-md:focus-visible { outline: 2px solid var(--md-primary); outline-offset: 2px; }
.btn-md.filled-primary       { background: var(--md-primary); color: var(--md-on-primary); }
.btn-md.filled-primary:hover { background: #1255A6; box-shadow: 0 2px 8px rgba(21,101,192,.35); color: var(--md-on-primary); }
.btn-md.outlined             { background: transparent; color: var(--md-on-surface-var); border: 1.5px solid var(--md-outline-var); }
.btn-md.outlined:hover       { background: rgba(26,27,31,.06); border-color: var(--md-outline); color: var(--md-on-surface); }

/* ── MD3 Tonal Chips ─────────────────────────────────────── */
.chip {
    display: inline-flex; align-items: center; gap: .25rem;
    padding: .25rem .75rem; border-radius: 8px;
    font-size: .75rem; font-weight: 500; white-space: nowrap;
}
.chip-primary   { background: var(--md-primary-cont);   color: var(--md-on-primary-cont); }
.chip-secondary { background: var(--md-secondary-cont); color: var(--md-on-secondary-cont); }
.chip-error     { background: var(--md-error-cont);     color: var(--md-on-error-cont); }
.chip-tertiary  { background: var(--md-tertiary-cont);  color: var(--md-on-tertiary-cont); }
.chip-surface   { background: var(--md-surface-var);    color: var(--md-on-surface-var); }
.chip-warning   { background: #FFF3E0; color: #BF360C; }

/* ── Stat Cards (MD3 Filled Tonal) ──────────────────────── */
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
.stat-card.sc-error     { background: var(--md-error-cont); }
.stat-card .s-label { font-size: .6875rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; margin-bottom: .25rem; }
.stat-card.sc-primary   .s-label { color: var(--md-on-primary-cont); opacity: .7; }
.stat-card.sc-secondary .s-label { color: var(--md-on-secondary-cont); opacity: .7; }
.stat-card.sc-tertiary  .s-label { color: var(--md-on-tertiary-cont); opacity: .7; }
.stat-card.sc-error     .s-label { color: var(--md-on-error-cont); opacity: .7; }
.stat-card .s-value { font-size: 1.375rem; font-weight: 700; line-height: 1.2; margin-bottom: .1875rem; }
.stat-card.sc-primary   .s-value { color: var(--md-on-primary-cont); }
.stat-card.sc-secondary .s-value { color: var(--md-on-secondary-cont); }
.stat-card.sc-tertiary  .s-value { color: var(--md-on-tertiary-cont); }
.stat-card.sc-error     .s-value { color: var(--md-on-error-cont); }
.stat-card .s-sub { font-size: .75rem; }
.stat-card.sc-primary   .s-sub { color: var(--md-on-primary-cont); opacity: .6; }
.stat-card.sc-secondary .s-sub { color: var(--md-on-secondary-cont); opacity: .6; }
.stat-card.sc-tertiary  .s-sub { color: var(--md-on-tertiary-cont); opacity: .6; }

/* ── Detail Card ─────────────────────────────────────────── */
.detail-card { background: #fff; border-radius: var(--shape-lg); border: 1px solid var(--md-outline-var); }
.detail-card-title {
    display: flex; align-items: center; gap: .5rem;
    font-size: .6875rem; font-weight: 700; letter-spacing: .08em;
    text-transform: uppercase; color: var(--md-on-surface-var);
    padding: .875rem 1.125rem; border-bottom: 1px solid var(--md-outline-var);
}
.detail-card-title::before {
    content: ''; width: 8px; height: 8px; border-radius: 50%;
    background: var(--md-primary); flex-shrink: 0;
}
.detail-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: .625rem 1.125rem;
    border-bottom: 1px solid var(--md-outline-var);
    font-size: .875rem;
}
.detail-row:last-child { border-bottom: none; }
.detail-row .d-key { color: var(--md-on-surface-var); font-size: .8125rem; }
.detail-row .d-val { font-weight: 500; color: var(--md-on-surface); text-align: right; }

/* ── Creator Hero ────────────────────────────────────────── */
.creator-hero {
    display: flex; align-items: center; gap: 1rem;
    padding: 1.125rem 1.25rem;
    background: var(--md-surface-var);
    border-radius: var(--shape-md);
    margin-bottom: 1.25rem;
}
.creator-avatar {
    width: 48px; height: 48px; border-radius: 50%;
    background: var(--md-primary);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    box-shadow: var(--md-elev-1);
}

/* ── Icon Button (copy) ──────────────────────────────────── */
.btn-icon-md {
    display: inline-flex; align-items: center; justify-content: center;
    width: 30px; height: 30px; border-radius: 50%;
    background: transparent; color: var(--md-on-surface-var);
    border: 1.5px solid var(--md-outline-var); cursor: pointer; transition: all .15s;
}
.btn-icon-md:hover { background: rgba(26,27,31,.06); border-color: var(--md-outline); color: var(--md-on-surface); }
</style>
@endpush

@section('content')
    <div style="max-width: 1000px;">

        {{-- ── Page Header ─────────────────────────────────────── --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="ts-headline-sm mb-0" style="color: var(--md-on-surface);">Detail Pengiriman</h1>
            <div class="d-flex gap-2">
                <a href="{{ url()->previous() }}" class="btn-md outlined">
                    <svg viewBox="0 0 20 20" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M12 16l-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Kembali
                </a>
                <a href="{{ route('admin.kirim-sampel.edit', $sendSample->id) }}" class="btn-md filled-primary">
                    <svg viewBox="0 0 20 20" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M4 13.5V16h2.5L14 8.5 11.5 6 4 13.5z"/>
                        <path d="M12 5.5 13.5 4 16 6.5 14.5 8"/>
                    </svg>
                    Edit Data
                </a>
            </div>
        </div>

        {{-- ── Main Card ────────────────────────────────────────── --}}
        <div class="surf-card" style="overflow: hidden;">
            {{-- Top accent bar --}}
            <div style="height: 4px; background: linear-gradient(90deg, var(--md-primary), var(--md-tertiary), var(--md-secondary));"></div>

            <div class="p-4">

                {{-- ── Creator Hero ─────────────────────────────── --}}
                <div class="creator-hero">
                    <div class="creator-avatar">
                        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" width="22" height="22">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                    <div class="flex-grow-1">
                        <div class="ts-label-sm mb-1" style="color:var(--md-on-surface-var); text-transform:uppercase;">Kreator</div>
                        <div style="font-size:1.125rem; font-weight:700; color:var(--md-on-surface); margin-bottom:.375rem;">
                            {{ $sendSample->creator->username ?? '-' }}
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="chip chip-warning">
                                {{ $sendSample->product->sku ?? 'Produk Dihapus' }}
                            </span>
                            @if($sendSample->status_chat > 0)
                                <span class="chip chip-secondary">Sudah Chat</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ── Stat Cards ───────────────────────────────── --}}
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-4">
                        <div class="stat-card sc-primary">
                            <div class="s-label">Jumlah Sampel</div>
                            <div class="s-value">{{ number_format($sendSample->qty) }} Unit</div>
                            <div class="s-sub">@ Rp {{ number_format($sendSample->product->harga ?? 0, 0, ',', '.') }}</div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="stat-card sc-tertiary">
                            <div class="s-label">Total Nilai</div>
                            <div class="s-value">Rp {{ number_format($sendSample->total_nilai, 0, ',', '.') }}</div>
                            <div class="s-sub">&nbsp;</div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="stat-card sc-secondary" style="position:relative;">
                            <div class="s-label">ID Pesanan</div>
                            @if($sendSample->id_pesanan)
                                <div class="d-flex align-items-center gap-2">
                                    <div class="s-value" style="font-size:1rem; word-break:break-all;">{{ $sendSample->id_pesanan }}</div>
                                    <button type="button"
                                            class="btn-icon-md"
                                            style="flex-shrink:0; border-color:rgba(0,33,7,.2); color:var(--md-on-secondary-cont);"
                                            data-copy="{{ $sendSample->id_pesanan }}"
                                            onclick="copyIdPesanan(this)"
                                            aria-label="Salin ID Pesanan" title="Salin ID Pesanan">
                                        <svg viewBox="0 0 20 20" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M8 3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1V3z"/>
                                            <path d="M6 3H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1h-2"/>
                                        </svg>
                                    </button>
                                </div>
                            @else
                                <div class="s-value">-</div>
                                <div class="s-sub">Belum diisi</div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ── Detail Cards ─────────────────────────────── --}}
                <div class="row g-3">
                    {{-- Toko, Approve, PIC & Perjanjian --}}
                    <div class="col-12 col-lg-6">
                        <div class="detail-card h-100">
                            <div class="detail-card-title">Toko &amp; PIC</div>

                            <div class="detail-row">
                                <span class="d-key">Toko</span>
                                <span class="d-val">{{ $sendSample->store->name ?? '-' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="d-key">Approve</span>
                                <span class="d-val">{{ $sendSample->approver->name ?? '-' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="d-key">PIC</span>
                                <span class="d-val">{{ $sendSample->user->name ?? '-' }}</span>
                            </div>

                            <div class="detail-card-title" style="border-top: 1px solid var(--md-outline-var); border-bottom:none; margin-top:-.5px;">
                                Perjanjian Konten
                            </div>
                            <div class="px-3 py-3 d-flex gap-2 flex-wrap">
                                <span class="chip chip-primary" style="padding:.35rem .875rem; font-size:.8125rem;">
                                    Live {{ $sendSample->live_dibuat ?? '0' }} / {{ $sendSample->perjanjian_livestream ?? '0' }}
                                </span>
                                <span class="chip chip-warning" style="padding:.35rem .875rem; font-size:.8125rem;">
                                    Video {{ $sendSample->video_dibuat ?? '0' }} / {{ $sendSample->perjanjian_video ?? '0' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Timeline --}}
                    <div class="col-12 col-lg-6">
                        <div class="detail-card h-100">
                            <div class="detail-card-title">Timeline</div>

                            <div class="detail-row">
                                <span class="d-key">Tanggal Deal</span>
                                <span class="d-val">
                                    {{ optional($sendSample->deal_date)->translatedFormat('d F Y') ?? '-' }}
                                </span>
                            </div>
                            <div class="detail-row">
                                <span class="d-key">Tanggal Kirim</span>
                                <span class="d-val">
                                    {{ optional($sendSample->tanggal_kirim)->translatedFormat('d F Y') ?? '-' }}
                                </span>
                            </div>
                            <div class="detail-row">
                                <span class="d-key">Estimasi Sampai</span>
                                <span class="d-val">
                                    {{ optional($sendSample->estimasi_sampai)->translatedFormat('d F Y') ?? '-' }}
                                </span>
                            </div>
                            <div class="detail-row">
                                <span class="d-key">Tanggal Aktual</span>
                                <span class="d-val" style="{{ $sendSample->tanggal_aktual ? 'color:var(--md-secondary); font-weight:600;' : '' }}">
                                    {{ optional($sendSample->tanggal_aktual)->translatedFormat('d F Y') ?? '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ── MD3 Snackbar ─────────────────────────────────────── --}}
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
        <div id="copyToast" class="toast align-items-center border-0" role="alert" aria-live="polite" aria-atomic="true"
             style="background:#313033; color:#E6E1E5; border-radius:var(--shape-sm);">
            <div class="d-flex">
                <div class="toast-body" id="copyToastText" style="font-size:.875rem;">Tersalin.</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin/kirim-sampel-index.js') }}"></script>
@endpush

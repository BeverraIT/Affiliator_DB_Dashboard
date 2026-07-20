@extends('layouts.admin')

@section('title', 'Kirim Sampel')

@push('styles')
<style>
/* ── MD3 Design Tokens ─────────────────────────────────────── */

/* ── Type Scale ──────────────────────────────────────────────── */
.ts-headline-sm { font-size: 1.5rem;   font-weight: 600; line-height: 2rem; }
.ts-title-sm    { font-size: .875rem;  font-weight: 500; line-height: 1.25rem; letter-spacing: .007em; }
.ts-body-sm     { font-size: .75rem;   font-weight: 400; line-height: 1rem;   letter-spacing: .025em; }
.ts-label-md    { font-size: .75rem;   font-weight: 500; letter-spacing: .05em; }
.ts-label-sm    { font-size: .6875rem; font-weight: 500; letter-spacing: .06em; }

/* ── Surface Card ────────────────────────────────────────────── */
.surf-card { background: #fff; border-radius: var(--shape-lg); box-shadow: var(--md-elev-1); border: none; }

/* ── MD3 Table ───────────────────────────────────────────────── */
.md-table { width: 100%; border-collapse: collapse; }
.md-table thead th {
    padding: .75rem 1rem;
    font-size: .6875rem; font-weight: 700; letter-spacing: .07em; text-transform: uppercase;
    color: var(--md-on-surface-var);
    background: #F8F9FF;
    border-bottom: 1px solid var(--md-outline-var);
    white-space: nowrap;
}
.md-table tbody tr { border-bottom: 1px solid var(--md-outline-var); transition: background .12s; }
.md-table tbody tr:last-child { border-bottom: none; }
.md-table tbody tr:hover { background: rgba(21,101,192,.04); }
.md-table tbody td { padding: .8125rem 1rem; vertical-align: middle; font-size: .8125rem; }

/* ── MD3 Chips ───────────────────────────────────────────────── */
.chip {
    display: inline-flex; align-items: center; gap: .25rem;
    padding: .2rem .625rem; border-radius: 8px;
    font-size: .6875rem; font-weight: 500; letter-spacing: .03em; white-space: nowrap;
}
.chip-primary   { background: var(--md-primary-cont);   color: var(--md-on-primary-cont); }
.chip-secondary { background: var(--md-secondary-cont); color: var(--md-on-secondary-cont); }
.chip-tertiary  { background: var(--md-tertiary-cont);  color: var(--md-on-tertiary-cont); }
.chip-error     { background: var(--md-error-cont);     color: var(--md-on-error-cont); }
.chip-surface   { background: var(--md-surface-var);    color: var(--md-on-surface-var); }
.chip-warning   { background: #FFF3E0; color: #BF360C; }

/* ── MD3 Icon Buttons ────────────────────────────────────────── */
.btn-icon-md {
    display: inline-flex; align-items: center; justify-content: center;
    width: 40px; height: 40px; border-radius: 50%;
    border: none; cursor: pointer; flex-shrink: 0; text-decoration: none;
    transition: all .15s;
}
.btn-icon-md:focus-visible { outline: 2px solid var(--md-primary); outline-offset: 2px; }
.btn-icon-md.sz-sm { width: 28px; height: 28px; }

.btn-icon-md.filled       { background: var(--md-primary); color: var(--md-on-primary); }
.btn-icon-md.filled:hover { background: #1255A6; box-shadow: 0 2px 8px rgba(21,101,192,.35); color: var(--md-on-primary); }

.btn-icon-md.t-warning       { background: #FFF3E0; color: #BF360C; }
.btn-icon-md.t-warning:hover { background: #FFE0B2; }

.btn-icon-md.t-secondary       { background: var(--md-secondary-cont); color: var(--md-secondary); }
.btn-icon-md.t-secondary:hover { background: #B5D9B7; }

.btn-icon-md.ghost       { background: transparent; color: var(--md-on-surface-var); border: 1.5px solid var(--md-outline-var); }
.btn-icon-md.ghost:hover { background: rgba(26,27,31,.06); border-color: var(--md-outline); color: var(--md-on-surface); }

.btn-icon-md.ghost-primary       { background: transparent; color: var(--md-primary); border: 1.5px solid rgba(21,101,192,.3); }
.btn-icon-md.ghost-primary:hover { background: var(--md-primary-cont); border-color: var(--md-primary); }

.btn-icon-md.ghost-danger       { background: transparent; color: var(--md-error); border: 1.5px solid rgba(179,38,30,.25); }
.btn-icon-md.ghost-danger:hover { background: var(--md-error-cont); border-color: var(--md-error); }

.btn-icon-md.flat       { background: transparent; color: var(--md-on-surface-var); border: none; }
.btn-icon-md.flat:hover { background: rgba(26,27,31,.08); color: var(--md-on-surface); }

/* ── MD3 Label Buttons ───────────────────────────────────────── */
.btn-md {
    display: inline-flex; align-items: center; justify-content: center; gap: .375rem;
    padding: .5625rem 1.25rem; border-radius: 20px;
    font-size: .875rem; font-weight: 500;
    border: none; cursor: pointer; text-decoration: none;
    transition: all .15s;
}
.btn-md:focus-visible { outline: 2px solid var(--md-primary); outline-offset: 2px; }

.btn-md.filled-primary       { background: var(--md-primary); color: var(--md-on-primary); }
.btn-md.filled-primary:hover { background: #1255A6; box-shadow: 0 2px 8px rgba(21,101,192,.35); color: var(--md-on-primary); }

.btn-md.filled-error       { background: var(--md-error); color: var(--md-on-error); }
.btn-md.filled-error:hover { background: #99201A; color: var(--md-on-error); }

.btn-md.tonal-warning       { background: #FFF3E0; color: #BF360C; }
.btn-md.tonal-warning:hover { background: #FFE0B2; }

.btn-md.outlined       { background: transparent; color: var(--md-on-surface-var); border: 1.5px solid var(--md-outline-var); }
.btn-md.outlined:hover { background: rgba(26,27,31,.06); border-color: var(--md-outline); color: var(--md-on-surface); }

.btn-md.text-neutral       { background: transparent; color: var(--md-on-surface-var); }
.btn-md.text-neutral:hover { background: rgba(26,27,31,.06); }

/* ── Banners ─────────────────────────────────────────────────── */
.md-banner {
    display: flex; align-items: flex-start; gap: .75rem;
    padding: .875rem 1.125rem; border-radius: var(--shape-md);
    margin-bottom: 1rem; font-size: .875rem;
}
.md-banner-success { background: var(--md-secondary-cont); color: var(--md-on-secondary-cont); border-left: 4px solid var(--md-secondary); }
.md-banner-error   { background: var(--md-error-cont);     color: var(--md-on-error-cont);     border-left: 4px solid var(--md-error); }

/* ── Filter Panel ────────────────────────────────────────────── */
.filter-panel {
    background: #fff; border-radius: var(--shape-lg);
    box-shadow: var(--md-elev-1); padding: .875rem 1.125rem;
    margin-bottom: 1.25rem;
}
.filter-label {
    display: block; font-size: .6875rem; font-weight: 700;
    letter-spacing: .08em; text-transform: uppercase;
    color: var(--md-on-surface-var); margin-bottom: .25rem;
}

/* ── Page header ─────────────────────────────────────────────── */
.ks-page-header {
    display: flex; justify-content: space-between; align-items: center;
    gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;
}
.ks-page-header .actions { display: flex; align-items: center; gap: .5rem; flex-wrap: wrap; }
</style>
@endpush

@section('content')
    @php
        use Illuminate\Support\Facades\Route;

        $hasShow    = Route::has('admin.kirim-sampel.show');
        $hasEdit    = Route::has('admin.kirim-sampel.edit');
        $hasDestroy = Route::has('admin.kirim-sampel.destroy');
        $q    = $q    ?? request('q');
        $sort = $sort ?? request('sort', 'deal_date');
        $dir  = $dir  ?? request('dir', 'desc');

        if (! function_exists('formatRupiahFull')) {
            function formatRupiahFull($value) {
                return 'Rp' . number_format((int) $value, 0, ',', '.');
            }
        }
    @endphp

    {{-- ── Page Header ─────────────────────────────────────────── --}}
    <div class="ks-page-header">
        <h1 class="ts-headline-sm mb-0" style="color: var(--md-on-surface);">Kirim Sampel</h1>

        <div class="actions">
            {{-- UPDATE PIC MASSAL --}}
            <button type="button"
                class="btn-icon-md t-warning"
                data-bs-toggle="modal"
                data-bs-target="#updatePicModal"
                aria-label="Update PIC Massal"
                title="Update PIC Massal">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm.256 7a4.5 4.5 0 0 1-.229-1.004H3c.001-.246.154-.986.832-1.664C4.484 10.68 5.711 10 8 10q.39 0 .74.025c.226-.341.496-.65.804-.918Q8.844 9.002 8 9c-5 0-6 3-6 4s1 1 1 1h5.256Zm3.63-4.54c.18-.613 1.048-.613 1.229 0l.043.148a.64.64 0 0 0 .921.382l.136-.074c.561-.306 1.175.308.87.869l-.075.136a.64.64 0 0 0 .382.92l.149.045c.612.18.612 1.048 0 1.229l-.15.043a.64.64 0 0 0-.38.921l.074.136c.305.561-.309 1.175-.87.87l-.136-.075a.64.64 0 0 0-.92.382l-.045.149c-.18.612-1.048.612-1.229 0l-.043-.15a.64.64 0 0 0-.921-.38l-.136.074c-.561.305-1.175-.309-.87-.87l.075-.136a.64.64 0 0 0-.382-.92l-.148-.045c-.613-.18-.613-1.048 0-1.229l.148-.043a.64.64 0 0 0 .382-.921l-.074-.136c-.306-.561.308-1.175.869-.87l.136.075a.64.64 0 0 0 .92-.382zM14 12.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0z"/>
                </svg>
            </button>

            {{-- BULK DELETE --}}
            <button type="button"
                    id="btnBulkDelete"
                    class="btn-md filled-error d-none align-items-center"
                    onclick="submitBulkDelete()">
                <svg viewBox="0 0 20 20" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 6h12M8 6V4h4v2M7 6v9a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V6" />
                </svg>
                Hapus (<span id="countSelected">0</span>)
            </button>

            {{-- EXPORT --}}
            <a href="{{ route('admin.kirim-sampel.export', request()->all()) }}"
                class="btn-icon-md t-secondary"
                title="Export Excel"
                data-bs-toggle="tooltip">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0M9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1M5.884 6.68 8 9.219l2.116-2.54a.5.5 0 1 1 .768.641L8.651 10l2.233 2.68a.5.5 0 0 1-.768.64L8 10.781l-2.116 2.54a.5.5 0 0 1-.768-.641L7.349 10 5.116 7.32a.5.5 0 1 1 .768-.64"/>
                </svg>
            </a>

            {{-- IMPORT MASSAL --}}
            <button type="button"
                class="btn-icon-md ghost"
                data-bs-toggle="modal"
                data-bs-target="#importModal"
                aria-label="Import Massal"
                title="Kirim Sampel Massal">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
                </svg>
            </button>

            {{-- TAMBAH --}}
            <a href="{{ route('admin.kirim-sampel.create') }}"
                class="btn-icon-md filled"
                aria-label="Tambah kirim sampel"
                title="Tambah">
                <svg viewBox="0 0 20 20" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M10 4v12M4 10h12" stroke-linecap="round"/>
                </svg>
            </a>
        </div>
    </div>

    {{-- ── Flash Messages ────────────────────────────────────── --}}
    @if (session('success'))
        <div class="md-banner md-banner-success">
            <svg viewBox="0 0 20 20" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:.1rem;">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error') || session('delete_error'))
        <div class="md-banner md-banner-error">
            <svg viewBox="0 0 20 20" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:.1rem;">
                <circle cx="10" cy="10" r="8"/><path d="M10 7v4M10 13.5v.5" stroke-linecap="round"/>
            </svg>
            <span>{{ session('error') ?? session('delete_error') }}</span>
        </div>
    @endif

    {{-- ── Filter & Search ──────────────────────────────────── --}}
    <form method="GET" action="{{ route('admin.kirim-sampel.index') }}">
        <div class="filter-panel d-flex align-items-center gap-2 flex-wrap">

            {{-- Search --}}
            <div style="flex: 1 1 220px; max-width: 400px;">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                    </span>
                    <input type="text" name="q" value="{{ $q }}" class="form-control border-start-0 ps-0"
                        placeholder="Cari Kreator, Toko, PIC, Produk...">
                </div>
            </div>

            <input type="hidden" name="sort" value="{{ $sort }}">
            <input type="hidden" name="dir"  value="{{ $dir }}">

            {{-- Filter Dropdown --}}
            <div class="dropdown">
                <button type="button"
                        class="btn-md outlined dropdown-toggle"
                        data-bs-toggle="dropdown"
                        data-bs-auto-close="outside"
                        aria-expanded="false">
                    <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                    </svg>
                    Filter
                </button>

                <div class="dropdown-menu dropdown-menu-end shadow border-0 p-0 mt-2"
                     style="min-width:320px; border-radius:var(--shape-lg)!important; overflow:hidden;">
                    <div class="px-3 py-2 border-bottom" style="background:#F8F9FF;">
                        <span class="ts-label-sm" style="color:var(--md-on-surface-var);text-transform:uppercase;letter-spacing:.08em;">Filter Data</span>
                    </div>
                    <div class="p-3">
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="filter-label">Dari Tanggal</label>
                                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-6">
                                <label class="filter-label">Sampai Tanggal</label>
                                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-6">
                                <label class="filter-label">Status ID Pesanan</label>
                                <select class="form-select form-select-sm" name="filter_id_pesanan">
                                    <option value="">Semua</option>
                                    <option value="filled" {{ request('filter_id_pesanan') == 'filled' ? 'selected' : '' }}>Sudah Input ID</option>
                                    <option value="empty"  {{ request('filter_id_pesanan') == 'empty'  ? 'selected' : '' }}>Belum Input ID</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="filter-label">Status Chat</label>
                                <select class="form-select form-select-sm" name="filter_status_chat">
                                    <option value="">Semua</option>
                                    <option value="filled" {{ request('filter_status_chat') == 'filled' ? 'selected' : '' }}>Sudah Chat</option>
                                    <option value="empty"  {{ request('filter_status_chat') == 'empty'  ? 'selected' : '' }}>Belum Chat</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="filter-label">Toko</label>
                                <select class="form-select form-select-sm select2-init" name="filter_stores[]" multiple="multiple" style="width:100%">
                                    @foreach($optStores as $s)
                                        <option value="{{ $s->id }}" {{ in_array($s->id, $filterStores) ? 'selected' : '' }}>{{ $s->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="filter-label">PIC</label>
                                <select class="form-select form-select-sm select2-init" name="filter_pics[]" multiple="multiple" style="width:100%">
                                    @foreach($optPics as $p)
                                        <option value="{{ $p->id }}" {{ in_array($p->id, $filterPics) ? 'selected' : '' }}>{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="filter-label">SKU Utama</label>
                                <select class="form-select form-select-sm select2-init" name="filter_sku[]" multiple="multiple" style="width:100%">
                                    @foreach($optSku as $item)
                                        <option value="{{ $item->sku_utama }}" {{ in_array($item->sku_utama, $filterSku) ? 'selected' : '' }}>{{ $item->sku_utama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 px-3 py-2 border-top" style="background:#F8F9FF;">
                        <a href="{{ route('admin.kirim-sampel.index', ['sort' => $sort, 'dir' => $dir]) }}"
                           class="btn-md text-neutral">Reset</a>
                        <button type="submit" class="btn-md filled-primary">Terapkan</button>
                    </div>
                </div>
            </div>

            {{-- Reset All --}}
            @if(!empty($q) || !empty($filterCreators) || !empty($filterStores) || !empty($filterPics) || !empty($filterSku))
                <a class="chip chip-error"
                   href="{{ route('admin.kirim-sampel.index', ['sort' => $sort, 'dir' => $dir]) }}"
                   style="text-decoration:none; padding:.35rem .75rem; border-radius:20px;">
                    <svg viewBox="0 0 20 20" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M4 4l12 12M16 4l-12 12" stroke-linecap="round"/>
                    </svg>
                    Reset Filter
                </a>
            @endif
        </div>
    </form>

    {{-- ── Data Table ────────────────────────────────────────── --}}
    <div class="surf-card" style="overflow:hidden;">
        <div class="table-responsive">
            <table class="md-table">
                <thead>
                    <tr>
                        <th style="width:40px; text-align:center;">
                            <input class="form-check-input" type="checkbox" id="checkAll">
                        </th>
                        <th style="width:140px;">Tanggal</th>
                        <th style="width:200px;">Kreator / Toko / PIC</th>
                        <th style="width:140px;">Produk</th>
                        <th style="width:110px;">Perjanjian</th>
                        <th style="width:52px; text-align:center;">Qty</th>
                        <th style="width:160px; text-align:center;">ID Pesanan</th>
                        <th style="width:110px; text-align:center;">Aksi</th>
                    </tr>
                </thead>

                <tbody id="sampleTableBody">
                @forelse($sendSamples as $i => $row)
                    @php
                        $hargaProduk     = (int) ($row->product->harga ?? 0);
                        $qty             = (int) ($row->qty ?? 0);
                        $totalHarga      = $hargaProduk * $qty;

                        $tglDealShow     = optional($row->deal_date)->format('d M Y') ?? null;
                        $tglKirimShow    = optional($row->tanggal_kirim)->format('d M Y') ?? null;
                        $tglEstimasiShow = optional($row->estimasi_sampai)->format('d M Y') ?? null;
                        $tglAktualShow   = optional($row->tanggal_aktual)->format('d M Y') ?? null;

                        $perjLive        = (int) ($row->perjanjian_livestream ?? 0);
                        $perjVideo       = (int) ($row->perjanjian_video ?? 0);
                        $hasIdPesanan    = !empty($row->id_pesanan);
                        $idPesananVal    = (string) ($row->id_pesanan ?? '');

                        $usernameVal     = $row->creator->username ?? '-';
                    @endphp

                    <tr>
                        {{-- CHECKBOX --}}
                        <td style="text-align:center;">
                            <input class="form-check-input check-item" type="checkbox" value="{{ $row->id }}">
                        </td>

                        {{-- TANGGAL --}}
                        <td>
                            <div style="font-size:.8125rem; font-weight:600; color:var(--md-on-surface);">
                                {{ $tglDealShow ?: '-' }}
                            </div>
                            <div class="mt-1" style="font-size:.7rem; color:var(--md-on-surface-var); line-height:1.65;">
                                Kirim: {{ $tglKirimShow ?: '-' }}<br>
                                Est.: {{ $tglEstimasiShow ?: '-' }}<br>
                                Aktual: {{ $tglAktualShow ?: '-' }}
                            </div>
                        </td>

                        {{-- KREATOR / TOKO / PIC --}}
                        <td>
                            <div class="d-flex align-items-center gap-1 mb-1">
                                <span style="font-size:.8125rem; font-weight:600; color:var(--md-on-surface); letter-spacing:.04em;">
                                    {{ $usernameVal }}
                                </span>
                                @if($usernameVal !== '-')
                                    <button type="button"
                                            class="btn-icon-md flat"
                                            style="width:22px; height:22px;"
                                            data-copy="{{ $usernameVal }}"
                                            onclick="copyToClipboard(this)"
                                            aria-label="Salin Username" title="Salin Username">
                                        <svg viewBox="0 0 20 20" width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M8 3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1V3z"/>
                                            <path d="M6 3H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1h-2"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                            <div class="ts-body-sm" style="color:var(--md-on-surface-var);">{{ $row->store->name ?? '-' }}</div>
                            @if($row->user)
                                <div class="d-flex flex-wrap gap-1 mt-1">
                                    <span class="chip chip-primary">PIC: {{ $row->user->name }}</span>
                                    @if($row->status_chat > 0)
                                        <span class="chip chip-secondary">Sudah Chat</span>
                                    @endif
                                </div>
                            @endif
                        </td>

                        {{-- PRODUK --}}
                        <td>
                            @if($row->product)
                                <div style="font-size:.8125rem; font-weight:600; color:var(--md-on-surface);">{{ $row->product->sku ?? '-' }}</div>
                                <div class="ts-body-sm mt-1" style="color:var(--md-on-surface-var);">ID: {{ $row->seller_product_id ?? '-' }}</div>
                                <div class="ts-body-sm" style="color:var(--md-on-surface-var);">{{ formatRupiahFull($hargaProduk) }}</div>
                            @else
                                <span style="color:var(--md-on-surface-var);">-</span>
                            @endif
                        </td>

                        {{-- PERJANJIAN --}}
                        <td>
                            <div class="d-flex flex-column gap-1">
                                <span class="chip chip-primary">Live: {{ $perjLive ?: '-' }}</span>
                                <span class="chip chip-warning">Video: {{ $perjVideo ?: '-' }}</span>
                            </div>
                        </td>

                        {{-- QTY --}}
                        <td style="text-align:center;">
                            <span style="font-size:.875rem; font-weight:600; color:var(--md-on-surface);">{{ $qty }}</span>
                        </td>

                        {{-- ID PESANAN --}}
                        <td style="text-align:center;">
                            @if($hasIdPesanan)
                                <div class="d-inline-flex align-items-center gap-1">
                                    <span class="chip chip-secondary">{{ $idPesananVal }}</span>
                                    <button type="button"
                                            class="btn-icon-md flat"
                                            style="width:24px; height:24px;"
                                            data-copy="{{ $idPesananVal }}"
                                            onclick="copyToClipboard(this)"
                                            aria-label="Salin ID Pesanan" title="Salin ID Pesanan">
                                        <svg viewBox="0 0 20 20" width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M8 3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1V3z"/>
                                            <path d="M6 3H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1h-2"/>
                                        </svg>
                                    </button>
                                </div>
                            @else
                                <span class="chip chip-surface">Belum ada</span>
                            @endif
                        </td>

                        {{-- AKSI --}}
                        <td style="text-align:center;">
                            <div class="d-inline-flex gap-1">
                                @if($hasShow)
                                    <a href="{{ route('admin.kirim-sampel.show', $row) }}"
                                       class="btn-icon-md ghost-primary sz-sm"
                                       aria-label="Detail" title="Detail">
                                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </a>
                                @endif

                                @if($hasEdit)
                                    <a href="{{ route('admin.kirim-sampel.edit', $row) }}"
                                       class="btn-icon-md ghost sz-sm"
                                       aria-label="Edit" title="Edit">
                                        <svg viewBox="0 0 20 20" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M4 13.5V16h2.5L14 8.5 11.5 6 4 13.5z"/>
                                            <path d="M12 5.5 13.5 4 16 6.5 14.5 8"/>
                                        </svg>
                                    </a>
                                @endif

                                @if($hasDestroy)
                                    <button type="button"
                                            class="btn-icon-md ghost-danger sz-sm"
                                            data-url="{{ route('admin.kirim-sampel.destroy', $row->id) }}"
                                            data-name="Kirim sampel #{{ $row->id }}"
                                            onclick="openDeleteModal(this)"
                                            aria-label="Hapus" title="Hapus">
                                        <svg viewBox="0 0 20 20" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M4 6h12M8 6V4h4v2M7 6v9a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V6"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center; padding:3.5rem 1rem; color:var(--md-on-surface-var);">
                            <div style="font-size:2rem; margin-bottom:.5rem;">📭</div>
                            <div style="font-size:.875rem; font-weight:500;">Belum ada data kirim sampel</div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION --}}
    <div class="mt-3">
        @if(method_exists($sendSamples, 'links'))
            {{ $sendSamples->links() }}
        @endif
    </div>

    {{-- ── Modal: Hapus ─────────────────────────────────────── --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius:var(--shape-xl);">
                <div class="modal-header border-0 pt-4 pb-2 px-4">
                    <h5 class="modal-title ts-title-sm fw-semibold" style="font-size:1.125rem; color:var(--md-on-surface);">Hapus Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body px-4 py-2" style="font-size:.875rem; color:var(--md-on-surface-var);">
                    Yakin ingin menghapus <strong style="color:var(--md-on-surface);" id="modalDeleteName"></strong>?
                    <div class="mt-1 ts-body-sm">Tindakan ini tidak dapat dibatalkan.</div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-2 gap-2">
                    <button type="button" class="btn-md text-neutral" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-md filled-error">Ya, hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- FORM BULK DELETE --}}
    <form id="formBulkDelete" action="{{ route('admin.kirim-sampel.bulk_destroy') }}" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    {{-- ── Modal: Import Massal ─────────────────────────────── --}}
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius:var(--shape-xl);">
                <div class="modal-header border-0 pt-4 pb-1 px-4">
                    <h5 class="modal-title" id="importModalLabel"
                        style="font-size:1.125rem; font-weight:600; color:var(--md-on-surface);">Upload Kirim Sampel Massal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.kirim-sampel.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body px-4 py-3">
                        <div class="p-3 mb-2 ts-body-sm"
                             style="background:var(--md-primary-cont); color:var(--md-on-primary-cont); border-radius:var(--shape-md); line-height:1.6;">
                            Pastikan format Excel memiliki header kolom:<br>
                            <strong>deal_date, pic, approve, toko, kreator, perjanjian_livestream, perjanjian_video, sku, qty, id_pesanan, tanggal_kirim, tanggal_aktual, video_dibuat, live_dibuat</strong>
                        </div>
                        <div class="mb-3">
                            <a href="{{ route('admin.kirim-sampel.template') }}"
                               class="btn-md sm outlined"
                               style="text-decoration:none; width:fit-content;">
                                <svg viewBox="0 0 16 16" width="13" height="13" fill="currentColor">
                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                                    <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 1-.708.708l3 3z"/>
                                </svg>
                                Download Template Excel
                            </a>
                        </div>
                        <label class="filter-label">Pilih File Excel (.xlsx / .csv)</label>
                        <input class="form-control" type="file" id="fileImport" name="file" accept=".xlsx,.xls,.csv" required>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4 pt-2 gap-2">
                        <button type="button" class="btn-md text-neutral" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-md filled-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                                <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
                            </svg>
                            Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Modal: Update PIC Massal ─────────────────────────── --}}
    <div class="modal fade" id="updatePicModal" tabindex="-1" aria-labelledby="updatePicModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius:var(--shape-xl);">
                <div class="modal-header border-0 pt-4 pb-1 px-4">
                    <h5 class="modal-title" id="updatePicModalLabel"
                        style="font-size:1.125rem; font-weight:600; color:var(--md-on-surface);">Update PIC Massal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formUpdatePicMassal">
                    @csrf
                    <div class="modal-body px-4 py-3">
                        <div class="mb-3">
                            <label class="filter-label">PIC Lama</label>
                            <select class="form-select select2-update-pic" name="old_pic" required style="width:100%;">
                                <option value=""></option>
                                @foreach($optPics as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="filter-label">Toko</label>
                            <select class="form-select select2-update-pic" name="store_id" required style="width:100%;">
                                <option value=""></option>
                                @foreach($optStores as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <hr style="border-color:var(--md-outline-var); margin:.75rem 0;">
                        <div class="mb-1">
                            <label class="filter-label" style="color:var(--md-primary);">PIC Baru</label>
                            <select class="form-select select2-update-pic" name="new_pic_id" required style="width:100%;">
                                <option value=""></option>
                                @foreach($optPics as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4 pt-2 gap-2">
                        <button type="button" class="btn-md text-neutral" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-md tonal-warning" id="btnSubmitUpdatePic">Update PIC</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── MD3 Snackbar ─────────────────────────────────────── --}}
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index:1080;">
        <div id="copyToast" class="toast align-items-center border-0" role="alert" aria-live="polite" aria-atomic="true"
             style="background:#313033; color:#E6E1E5; border-radius:var(--shape-sm);">
            <div class="d-flex">
                <div class="toast-body ts-body-sm" id="copyToastText">Tersalin.</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkAll    = document.getElementById('checkAll');
            const checkItems  = document.querySelectorAll('.check-item');
            const btnBulkDelete = document.getElementById('btnBulkDelete');
            const countSelected = document.getElementById('countSelected');

            function toggleBulkButton() {
                const selectedCount = document.querySelectorAll('.check-item:checked').length;
                if (countSelected) countSelected.innerText = selectedCount;
                if (selectedCount > 0) {
                    btnBulkDelete.classList.remove('d-none');
                    btnBulkDelete.classList.add('d-inline-flex');
                } else {
                    btnBulkDelete.classList.add('d-none');
                    btnBulkDelete.classList.remove('d-inline-flex');
                }
            }

            if (checkAll) {
                checkAll.addEventListener('change', function() {
                    checkItems.forEach(cb => { cb.checked = this.checked; });
                    toggleBulkButton();
                });
            }

            checkItems.forEach(cb => {
                cb.addEventListener('change', function() {
                    if (!this.checked && checkAll) checkAll.checked = false;
                    if (document.querySelectorAll('.check-item:checked').length === checkItems.length && checkAll)
                        checkAll.checked = true;
                    toggleBulkButton();
                });
            });

            window.submitBulkDelete = function() {
                const selectedIds = Array.from(document.querySelectorAll('.check-item:checked')).map(cb => cb.value);
                if (selectedIds.length === 0) return;
                if (confirm('Apakah Anda yakin ingin menghapus ' + selectedIds.length + ' data terpilih?')) {
                    const form = document.getElementById('formBulkDelete');
                    form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
                    selectedIds.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden'; input.name = 'ids[]'; input.value = id;
                        form.appendChild(input);
                    });
                    form.submit();
                }
            };
        });

        window.copyToClipboard = function(btn) {
            const textToCopy = btn.getAttribute('data-copy');
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(textToCopy).then(() => { showToast('Berhasil disalin: ' + textToCopy); });
            } else {
                let ta = document.createElement('textarea');
                ta.value = textToCopy; ta.style.position = 'fixed'; ta.style.left = '-999999px';
                document.body.appendChild(ta); ta.focus(); ta.select();
                try { document.execCommand('copy'); showToast('Berhasil disalin: ' + textToCopy); }
                catch (err) { console.error('Gagal menyalin text', err); }
                document.body.removeChild(ta);
            }
        };

        window.showToast = function(message) {
            const toastEl = document.getElementById('copyToast');
            document.getElementById('copyToastText').innerText = message;
            new bootstrap.Toast(toastEl, { delay: 3000 }).show();
        };

        $(document).ready(function() {
            $('.select2-init').each(function() {
                $(this).select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: 'Pilih Opsi...',
                    allowClear: true,
                    closeOnSelect: false,
                    dropdownParent: $(this).closest('.dropdown-menu')
                });
            });

            $('.select2-container').on('click', function(e) { e.stopPropagation(); });

            $('#updatePicModal').on('shown.bs.modal', function() {
                $('.select2-update-pic').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#updatePicModal'),
                    placeholder: 'Pilih...',
                    allowClear: true
                });
            });

            document.getElementById('formUpdatePicMassal').addEventListener('submit', function(e) {
                e.preventDefault();
                const form = this;
                const submitBtn = document.getElementById('btnSubmitUpdatePic');
                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Memproses...';
                const formData = new FormData(form);
                const actionUrl = "{{ route('admin.kirim-sampel.update-pic-massal') }}";
                fetch(actionUrl, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': formData.get('_token'), 'Accept': 'application/json' },
                    body: formData
                })
                .then(r => r.json())
                .then(data => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Update PIC';
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('updatePicModal')).hide();
                        Swal.fire({
                            icon: 'success', title: 'Berhasil!',
                            text: `${data.message} (${data.jumlah_diupdate} data diupdate)`,
                            showConfirmButton: false, timer: 1500, timerProgressBar: true
                        }).then(() => { window.location.reload(); });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: data.message, confirmButtonColor: '#3085d6' });
                    }
                })
                .catch(error => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Update PIC';
                    Swal.fire({ icon: 'error', title: 'Kesalahan Sistem', text: 'Terjadi kesalahan pada server (Error 500). Silakan cek console atau log Laravel.', confirmButtonColor: '#d33' });
                    console.error(error);
                });
            });
        });
    </script>
    <script src="{{ asset('js/admin/kirim-sampel-index.js') }}"></script>
@endpush

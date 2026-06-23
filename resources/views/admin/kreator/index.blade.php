@extends('layouts.admin')

@section('title', 'Kreator')

@push('styles')
<style>
/* ─── MD3 Design Tokens ─────────────────────────────────────── */

/* ─── Type Scale ─────────────────────────────────────────────── */
.ts-headline-sm { font-size: 1.5rem;   font-weight: 600; line-height: 2rem; }
.ts-body-sm     { font-size: .75rem;   font-weight: 400; line-height: 1rem; letter-spacing: .025em; }
.ts-label-md    { font-size: .75rem;   font-weight: 500; letter-spacing: .05em; }

/* ─── Surface Card ───────────────────────────────────────────── */
.surf-card { background: #fff; border-radius: var(--shape-lg); box-shadow: var(--md-elev-1); border: none; }

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
.md-table tbody td { padding: .75rem 1rem; vertical-align: middle; font-size: .8125rem; }

/* ─── Chips ──────────────────────────────────────────────────── */
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

/* ─── Icon Buttons ───────────────────────────────────────────── */
.btn-icon-md {
    display: inline-flex; align-items: center; justify-content: center;
    width: 40px; height: 40px; border-radius: 50%;
    border: none; cursor: pointer; flex-shrink: 0; text-decoration: none;
    transition: all .15s;
}
.btn-icon-md.sz-sm                { width: 28px; height: 28px; }
.btn-icon-md:focus-visible        { outline: 2px solid var(--md-primary); outline-offset: 2px; }
.btn-icon-md.filled               { background: var(--md-primary); color: var(--md-on-primary); }
.btn-icon-md.filled:hover         { background: #1255A6; box-shadow: 0 2px 8px rgba(21,101,192,.35); color: var(--md-on-primary); }
.btn-icon-md.t-secondary          { background: var(--md-secondary-cont); color: var(--md-secondary); }
.btn-icon-md.t-secondary:hover    { background: #B5D9B7; }
.btn-icon-md.ghost                { background: transparent; color: var(--md-on-surface-var); border: 1.5px solid var(--md-outline-var); }
.btn-icon-md.ghost:hover          { background: rgba(26,27,31,.06); border-color: var(--md-outline); color: var(--md-on-surface); }
.btn-icon-md.ghost-primary        { background: transparent; color: var(--md-primary); border: 1.5px solid rgba(21,101,192,.3); }
.btn-icon-md.ghost-primary:hover  { background: var(--md-primary-cont); border-color: var(--md-primary); }
.btn-icon-md.ghost-danger         { background: transparent; color: var(--md-error); border: 1.5px solid rgba(179,38,30,.25); }
.btn-icon-md.ghost-danger:hover   { background: var(--md-error-cont); border-color: var(--md-error); }
.btn-icon-md.flat                 { background: transparent; color: var(--md-on-surface-var); border: none; }
.btn-icon-md.flat:hover           { background: rgba(26,27,31,.08); color: var(--md-on-surface); }

/* ─── Label Buttons ──────────────────────────────────────────── */
.btn-md {
    display: inline-flex; align-items: center; justify-content: center; gap: .375rem;
    padding: .5625rem 1.25rem; border-radius: 20px;
    font-size: .875rem; font-weight: 500;
    border: none; cursor: pointer; text-decoration: none; transition: all .15s;
}
.btn-md:focus-visible           { outline: 2px solid var(--md-primary); outline-offset: 2px; }
.btn-md.filled-primary          { background: var(--md-primary); color: var(--md-on-primary); }
.btn-md.filled-primary:hover    { background: #1255A6; box-shadow: 0 2px 8px rgba(21,101,192,.35); color: var(--md-on-primary); }
.btn-md.filled-error            { background: var(--md-error); color: var(--md-on-error); }
.btn-md.filled-error:hover      { background: #99201A; color: var(--md-on-error); }
.btn-md.outlined                { background: transparent; color: var(--md-on-surface-var); border: 1.5px solid var(--md-outline-var); }
.btn-md.outlined:hover          { background: rgba(26,27,31,.06); border-color: var(--md-outline); color: var(--md-on-surface); }
.btn-md.text-neutral            { background: transparent; color: var(--md-on-surface-var); }
.btn-md.text-neutral:hover      { background: rgba(26,27,31,.06); }

/* ─── Banners ────────────────────────────────────────────────── */
.md-banner {
    display: flex; align-items: flex-start; gap: .75rem;
    padding: .875rem 1.125rem; border-radius: var(--shape-md);
    margin-bottom: 1rem; font-size: .875rem;
}
.md-banner-success { background: var(--md-secondary-cont); color: var(--md-on-secondary-cont); border-left: 4px solid var(--md-secondary); }
.md-banner-error   { background: var(--md-error-cont);     color: var(--md-on-error-cont);     border-left: 4px solid var(--md-error); }

/* ─── Filter Panel ───────────────────────────────────────────── */
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
.filter-dropdown-menu {
    border-radius: var(--shape-lg) !important;
    box-shadow: var(--md-elev-2) !important;
    border: 1px solid var(--md-outline-var) !important;
    padding: 0 !important; overflow: hidden; min-width: 340px !important;
}

/* ─── Page Header ────────────────────────────────────────────── */
.kr-page-header {
    display: flex; justify-content: space-between; align-items: center;
    gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;
}
.kr-page-header .actions { display: flex; align-items: center; gap: .5rem; flex-wrap: wrap; }

/* ─── MD3 Dialog ─────────────────────────────────────────────── */
.md-dialog .modal-content {
    border-radius: var(--shape-xl); border: none;
    box-shadow: 0 4px 24px rgba(0,0,0,.12);
}
.md-dialog .modal-header { padding: 1.25rem 1.5rem .75rem; border-bottom: none; }
.md-dialog .modal-title  { font-size: 1.125rem; font-weight: 600; color: var(--md-on-surface); }
.md-dialog .modal-body   { padding: .375rem 1.5rem 1rem; color: var(--md-on-surface-var); font-size: .9375rem; line-height: 1.5; }
.md-dialog .modal-footer { padding: .5rem 1.5rem 1.25rem; border-top: none; }
</style>
@endpush

@section('content')
    {{-- ── Page Header ──────────────────────────────────────────── --}}
    <div class="kr-page-header">
        <h1 class="ts-headline-sm mb-0" style="color: var(--md-on-surface);">Kreator</h1>

        <div class="actions">
            {{-- BULK DELETE --}}
            <button type="button"
                    id="btnBulkDelete"
                    class="btn-md filled-error d-none align-items-center"
                    onclick="submitBulkDelete()">
                <svg viewBox="0 0 20 20" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 6h12M8 6V4h4v2M7 6v9a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V6"/>
                </svg>
                Hapus (<span id="countSelected">0</span>)
            </button>

            {{-- EXPORT --}}
            <a href="{{ route('admin.kreator.export', request()->all()) }}"
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
                aria-label="Tambah Kreator Massal"
                title="Kreator Massal">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
                </svg>
            </button>

            {{-- TAMBAH --}}
            <a href="{{ route('admin.kreator.create') }}"
                class="btn-icon-md filled"
                aria-label="Tambah kreator" title="Tambah">
                <svg viewBox="0 0 20 20" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M10 4v12M4 10h12" stroke-linecap="round"/>
                </svg>
            </a>
        </div>
    </div>

    {{-- ── Flash Messages ────────────────────────────────────────── --}}
    @if (session('success'))
        <div class="md-banner md-banner-success">
            <svg viewBox="0 0 20 20" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:.1rem;">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- ── Search & Filter ──────────────────────────────────────── --}}
    <form method="GET" action="{{ route('admin.kreator.index') }}">
        <div class="filter-panel d-flex align-items-center gap-2 flex-wrap">

            {{-- Search input --}}
            <div style="flex: 1 1 220px; max-width: 380px;">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                    </span>
                    <input type="text" name="q" value="{{ $q ?? '' }}"
                        class="form-control border-start-0 ps-0"
                        placeholder="Cari Kreator..." autocomplete="off">
                </div>
            </div>

            <input type="hidden" name="sort" value="{{ $sort ?? 'id' }}">
            <input type="hidden" name="dir"  value="{{ $dir ?? 'desc' }}">

            {{-- Submit Cari --}}
            <button type="submit" class="btn-icon-md ghost" title="Cari">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
            </button>

            {{-- Filter Dropdown --}}
            <div class="dropdown">
                <button type="button"
                    class="btn-icon-md ghost"
                    data-bs-toggle="dropdown"
                    data-bs-auto-close="outside"
                    aria-expanded="false"
                    title="Filter Pencarian">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                    </svg>
                </button>

                <div class="dropdown-menu filter-dropdown-menu dropdown-menu-end mt-2">
                    {{-- Header --}}
                    <div style="padding: .875rem 1rem .75rem; border-bottom: 1px solid var(--md-outline-var);">
                        <div style="font-size: .6875rem; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; color: var(--md-primary);">Opsi Filter</div>
                    </div>

                    {{-- Body --}}
                    <div style="padding: .875rem 1rem;">
                        <div class="row g-2">

                            {{-- GMV Range --}}
                            <div class="col-12">
                                <span class="filter-label">Rentang GMV</span>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light">Rp</span>
                                    <input type="number" name="min_gmv" class="form-control" placeholder="Min" value="{{ request('min_gmv') }}">
                                    <span class="input-group-text bg-white border-start-0 border-end-0">–</span>
                                    <input type="number" name="max_gmv" class="form-control" placeholder="Max" value="{{ request('max_gmv') }}">
                                </div>
                            </div>

                            <div class="col-12"><hr class="my-1" style="border-color: var(--md-outline-var);"></div>

                            {{-- Umur --}}
                            <div class="col-6">
                                <span class="filter-label">Umur</span>
                                <select class="form-select form-select-sm select2-init" id="filterUmur" name="filter_umur[]" multiple="multiple" style="width:100%">
                                    @foreach($ageOptions as $age)
                                        <option value="{{ $age->id }}" {{ in_array($age->id, $filterUmur ?? []) ? 'selected' : '' }}>{{ $age->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Kategori --}}
                            <div class="col-6">
                                <span class="filter-label">Kategori</span>
                                <select class="form-select form-select-sm select2-init" id="filterKategori" name="filter_kategori[]" multiple="multiple" style="width:100%">
                                    @foreach($categoryOptions as $cat)
                                        <option value="{{ $cat->id }}" {{ in_array($cat->id, $filterKategori ?? []) ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Konten Style --}}
                            <div class="col-6">
                                <span class="filter-label">Konten Style</span>
                                <select class="form-select form-select-sm select2-init" id="filterStyle" name="filter_style[]" multiple="multiple" style="width:100%">
                                    @foreach($styleOptions as $style)
                                        <option value="{{ $style->id }}" {{ in_array($style->id, $filterStyle ?? []) ? 'selected' : '' }}>{{ $style->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Input Oleh --}}
                            <div class="col-6">
                                <span class="filter-label">Input Oleh</span>
                                <select class="form-select form-select-sm select2-init" id="filterUser" name="filter_user[]" multiple="multiple" style="width:100%">
                                    @foreach($userOptions as $user)
                                        <option value="{{ $user->id }}" {{ in_array($user->id, $filterUser ?? []) ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Toggle filters --}}
                            <div class="col-12">
                                <div class="d-flex flex-wrap gap-3 pt-1">
                                    <div class="form-check form-switch mb-0 d-flex align-items-center gap-2">
                                        <input class="form-check-input" type="checkbox" role="switch" id="filterTopCreator" name="filter_top_creator" value="1" {{ request('filter_top_creator') ? 'checked' : '' }}>
                                        <label class="form-check-label ts-body-sm" for="filterTopCreator" style="color: var(--md-on-surface-var);">Top Kreator ★</label>
                                    </div>
                                    <div class="form-check form-switch mb-0 d-flex align-items-center gap-2">
                                        <input class="form-check-input" type="checkbox" role="switch" id="filterBlokir" name="filter_is_blocked" value="1" {{ request('filter_is_blocked') ? 'checked' : '' }}>
                                        <label class="form-check-label ts-body-sm" for="filterBlokir" style="color: var(--md-on-surface-var);">Diblokir</label>
                                    </div>
                                    <div class="form-check form-switch mb-0 d-flex align-items-center gap-2">
                                        <input class="form-check-input" type="checkbox" role="switch" id="filterNoWa" name="filter_no_wa" value="1" {{ request('filter_no_wa') ? 'checked' : '' }}>
                                        <label class="form-check-label ts-body-sm" for="filterNoWa" style="color: var(--md-on-surface-var);">Belum Ada WA</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div style="padding: .625rem 1rem; border-top: 1px solid var(--md-outline-var); background: #F8F9FF; display: flex; justify-content: flex-end; gap: .5rem;">
                        <a href="{{ route('admin.kreator.index', ['q' => $q, 'sort' => $sort, 'dir' => $dir]) }}"
                           class="btn-md text-neutral">Reset</a>
                        <button type="submit" class="btn-md filled-primary">Terapkan</button>
                    </div>
                </div>
            </div>

            {{-- Reset active search --}}
            @if(!empty($q))
                <a class="btn-md text-neutral"
                    href="{{ route('admin.kreator.index', ['sort' => $sort ?? 'id', 'dir' => $dir ?? 'desc']) }}">
                    Reset
                </a>
            @endif
        </div>
    </form>

    {{-- ── Data Table ───────────────────────────────────────────── --}}
    <div class="surf-card">
        <div class="table-responsive">
            <table class="md-table">
                <thead>
                    <tr>
                        <th style="width:40px;">
                            <div class="d-flex justify-content-center">
                                <input class="form-check-input" type="checkbox" id="checkAll">
                            </div>
                        </th>
                        <th style="width:160px;">Kreator</th>
                        <th style="width:130px; text-align:right;">GMV</th>
                        <th style="width:70px; text-align:right;">Item</th>
                        <th style="width:130px;">Umur</th>
                        <th style="width:200px;">Kategori</th>
                        <th style="width:180px;">Konten Style</th>
                        <th style="width:110px; text-align:center;">Aksi</th>
                    </tr>
                </thead>

                <tbody id="creatorTableBody">
                @foreach($creators as $creator)
                    @php
                        $gender = $creator->genderpengikut;
                        $genderLabel = $gender ? \Illuminate\Support\Str::title(str_replace('-', ' ', $gender)) : null;
                        $genderClass = match ($gender) {
                            'perempuan' => 'chip chip-error',
                            'laki-laki' => 'chip chip-primary',
                            'seimbang'  => 'chip chip-secondary',
                            default     => 'chip chip-surface',
                        };

                        $channel = $creator->channel;
                        $channelLabel = $channel ? \Illuminate\Support\Str::title(str_replace('-', ' ', $channel)) : null;
                        $channelClass = match ($channel) {
                            'video'        => 'chip chip-primary',
                            'live'         => 'chip chip-warning',
                            'kartu produk' => 'chip chip-surface',
                            default        => 'chip chip-surface',
                        };
                    @endphp

                    <tr class="row-data"
                        data-gmv="{{ $creator->gmv }}"
                        data-items="{{ $creator->itemsterjual }}"
                        data-gender="{{ $creator->genderpengikut }}"
                        data-channel="{{ $creator->channel }}">

                        <td style="text-align:center;">
                            <div class="d-flex justify-content-center">
                                <input class="form-check-input check-item" type="checkbox" value="{{ $creator->id }}">
                            </div>
                        </td>

                        {{-- KREATOR --}}
                        <td>
                            <div class="d-flex flex-column gap-1">
                                @php
                                    $tiktokUrl = 'https://www.tiktok.com/@' . rawurlencode(ltrim((string) $creator->username, '@'));
                                @endphp

                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                    <a href="{{ $tiktokUrl }}" target="_blank" rel="noopener noreferrer"
                                        class="text-decoration-none fw-medium"
                                        style="color: var(--md-on-surface); font-size: .8125rem;">
                                        {{ $creator->username }}
                                    </a>

                                    <button type="button"
                                            class="btn-icon-md sz-sm flat"
                                            data-copy="{{ $creator->username }}"
                                            onclick="copyIdPesanan(this)"
                                            aria-label="Salin Username" title="Salin Username">
                                        <svg viewBox="0 0 20 20" width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M8 3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1V3z"/>
                                            <path d="M6 3H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1h-2"/>
                                        </svg>
                                    </button>

                                    @if((int)$creator->top_creator === 1)
                                        <span class="chip chip-warning" title="Top Creator">★</span>
                                    @endif

                                    @if((int)$creator->is_blocked === 1)
                                        <span class="chip chip-error">Diblokir</span>
                                    @endif
                                </div>

                                <div class="ts-body-sm" style="color: var(--md-on-surface-var);">
                                    {{ $creator->user->name ?? '—' }}
                                </div>

                                <div class="d-flex flex-wrap gap-1">
                                    @if($channelLabel)
                                        <span class="{{ $channelClass }}">{{ $channelLabel }}</span>
                                    @endif
                                    @if($genderLabel)
                                        <span class="{{ $genderClass }}">{{ $genderLabel }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- GMV --}}
                        <td style="text-align:right; font-weight:600;">
                            {{ number_format((int) $creator->gmv, 0, ',', '.') }}
                        </td>

                        {{-- ITEM TERJUAL --}}
                        <td style="text-align:right;">
                            {{ number_format((int) $creator->itemsterjual, 0, ',', '.') }}
                        </td>

                        {{-- UMUR --}}
                        <td>
                            <div class="d-flex flex-column gap-1">
                                @forelse($creator->ages as $age)
                                    <span class="chip chip-surface align-self-start">{{ $age->name }}</span>
                                @empty
                                    <span style="color: var(--md-on-surface-var); font-size:.75rem;">—</span>
                                @endforelse
                            </div>
                        </td>

                        {{-- KATEGORI --}}
                        <td>
                            <div class="d-flex flex-column gap-1">
                                @forelse($creator->categories as $category)
                                    <span class="chip chip-surface align-self-start">{{ $category->name }}</span>
                                @empty
                                    <span style="color: var(--md-on-surface-var); font-size:.75rem;">—</span>
                                @endforelse
                            </div>
                        </td>

                        {{-- KONTEN STYLE --}}
                        <td>
                            <div class="d-flex flex-column gap-1">
                                @forelse($creator->contentStyles as $cs)
                                    <span class="chip chip-surface align-self-start">{{ $cs->name }}</span>
                                @empty
                                    <span style="color: var(--md-on-surface-var); font-size:.75rem;">—</span>
                                @endforelse
                            </div>
                        </td>

                        {{-- AKSI --}}
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('admin.kreator.show', $creator->id) }}"
                                    class="btn-icon-md sz-sm ghost-primary"
                                    aria-label="Detail" title="Detail">
                                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </a>

                                <a href="{{ route('admin.kreator.edit', $creator->id) }}"
                                    class="btn-icon-md sz-sm ghost"
                                    aria-label="Edit" title="Edit">
                                    <svg viewBox="0 0 20 20" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M4 13.5V16h2.5L14 8.5 11.5 6 4 13.5z"/>
                                        <path d="M12 5.5 13.5 4 16 6.5 14.5 8"/>
                                    </svg>
                                </a>

                                @if(auth()->user()->role === 'super admin')
                                    <button type="button"
                                        class="btn-icon-md sz-sm ghost-danger"
                                        data-url="{{ route('admin.kreator.destroy', $creator->id) }}"
                                        data-name="{{ $creator->username }}"
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
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $creators->links() }}
    </div>

    {{-- ── Modal Delete ─────────────────────────────────────────── --}}
    <div class="modal fade md-dialog" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hapus Kreator</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    Yakin ingin menghapus <strong id="modalCreatorName"></strong>?
                </div>
                <div class="modal-footer d-flex gap-2 justify-content-end">
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

    {{-- ── Toast Copy ───────────────────────────────────────────── --}}
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
        <div id="copyToast" class="toast align-items-center text-bg-dark border-0" role="alert" aria-live="polite" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="copyToastText">Tersalin.</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
            </div>
        </div>
    </div>

    {{-- ── Form Bulk Delete ─────────────────────────────────────── --}}
    <form id="formBulkDelete" action="{{ route('admin.kreator.bulk_destroy') }}" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    {{-- ── Modal Import ─────────────────────────────────────────── --}}
    <div class="modal fade md-dialog" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Upload Kreator Massal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form action="{{ route('admin.kreator.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="md-banner md-banner-success mb-2">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:.1rem;">
                                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <span style="font-size:.8125rem;">Format Excel harus memiliki header kolom:<br>
                            <strong>username, channel, gmv, itemsterjual, genderpengikut, namawa, nomerwa, kategori, umur, konten_style</strong></span>
                        </div>
                        <div class="mb-3">
                            <a href="{{ route('admin.kreator.template') }}"
                               class="btn-md sm outlined"
                               style="text-decoration:none; width:fit-content;">
                                <svg viewBox="0 0 16 16" width="13" height="13" fill="currentColor">
                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                                    <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 1-.708.708l3 3z"/>
                                </svg>
                                Download Template Excel
                            </a>
                        </div>
                        <div>
                            <label for="fileImport" class="filter-label mb-1">Pilih File Excel (.xlsx / .csv)</label>
                            <input class="form-control" type="file" id="fileImport" name="file" accept=".xlsx,.xls,.csv" required>
                        </div>
                    </div>
                    <div class="modal-footer d-flex gap-2 justify-content-end">
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
@endsection

@push('scripts')
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
                    checkItems.forEach(checkbox => { checkbox.checked = this.checked; });
                    toggleBulkButton();
                });
            }

            checkItems.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    if (!this.checked && checkAll) checkAll.checked = false;
                    if (document.querySelectorAll('.check-item:checked').length === checkItems.length && checkAll) {
                        checkAll.checked = true;
                    }
                    toggleBulkButton();
                });
            });

            window.submitBulkDelete = function() {
                const selectedIds = Array.from(document.querySelectorAll('.check-item:checked')).map(cb => cb.value);
                if (selectedIds.length === 0) return;
                if (confirm('Apakah Anda yakin ingin menghapus ' + selectedIds.length + ' data terpilih?')) {
                    const form = document.getElementById('formBulkDelete');
                    form.querySelectorAll('input[name="ids[]"]').forEach(input => input.remove());
                    selectedIds.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden'; input.name = 'ids[]'; input.value = id;
                        form.appendChild(input);
                    });
                    form.submit();
                }
            };
        });

        function openDeleteModal(button) {
            var url  = button.getAttribute('data-url');
            var name = button.getAttribute('data-name');
            var form = document.getElementById('deleteForm');
            var modalName = document.getElementById('modalCreatorName');
            if (form) form.action = url;
            if (modalName) modalName.innerText = name;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        $(document).ready(function() {
            $('.select2-init').each(function() {
                $(this).select2({
                    theme: "bootstrap-5",
                    width: '100%',
                    placeholder: 'Pilih Opsi...',
                    allowClear: true,
                    closeOnSelect: false,
                    dropdownParent: $(this).closest('.dropdown-menu')
                });
            });
            $('.select2-container').on('click', function(e) { e.stopPropagation(); });
        });
    </script>
    <script src="{{ asset('js/admin/kirim-sampel-index.js') }}"></script>
@endpush

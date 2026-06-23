@extends('layouts.admin')

@section('title', 'Dashboard')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* =========================================================
           MATERIAL DESIGN 3 — COLOR TOKENS
        ========================================================= */

        body {
            font-family: 'Inter', sans-serif;
            background-color: #F0F2F5;
            color: var(--md-on-surface);
        }

        /* =========================================================
           MD3 TYPE SCALE
        ========================================================= */
        .ts-headline-sm  { font-size: 1.5rem;    font-weight: 600; line-height: 2rem;    letter-spacing: 0; }
        .ts-title-lg     { font-size: 1.125rem;  font-weight: 500; line-height: 1.75rem; letter-spacing: 0; }
        .ts-title-md     { font-size: 1rem;      font-weight: 500; line-height: 1.5rem;  letter-spacing: .009em; }
        .ts-title-sm     { font-size: .875rem;   font-weight: 500; line-height: 1.25rem; letter-spacing: .007em; }
        .ts-body-md      { font-size: .875rem;   font-weight: 400; line-height: 1.25rem; letter-spacing: .016em; }
        .ts-body-sm      { font-size: .75rem;    font-weight: 400; line-height: 1rem;    letter-spacing: .025em; }
        .ts-label-lg     { font-size: .875rem;   font-weight: 500; letter-spacing: .007em; }
        .ts-label-md     { font-size: .75rem;    font-weight: 500; letter-spacing: .05em; }
        .ts-label-sm     { font-size: .6875rem;  font-weight: 500; letter-spacing: .06em; }

        /* =========================================================
           SURFACE CARD
        ========================================================= */
        .surf-card {
            background: #FFFFFF;
            border-radius: var(--shape-lg);
            box-shadow: var(--md-elev-1);
            border: none;
            transition: box-shadow .2s ease;
        }
        .surf-card:hover { box-shadow: var(--md-elev-2); }

        /* =========================================================
           STAT CARDS — MD3 "Filled Tonal" style
        ========================================================= */
        .stat-card {
            border-radius: var(--shape-xl);
            padding: 1.375rem 1.5rem 1.25rem;
            position: relative;
            overflow: hidden;
            transition: box-shadow .2s ease, transform .2s ease;
            box-shadow: var(--md-elev-1);
        }
        .stat-card::after {
            /* MD3 state layer (hover) */
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: transparent;
            transition: background .15s;
        }
        .stat-card:hover::after { background: rgba(0,0,0,.04); }
        .stat-card:hover { transform: translateY(-2px); box-shadow: var(--md-elev-2); }

        /* colour variants */
        .stat-card.sc-primary   { background: var(--md-primary-container); }
        .stat-card.sc-secondary { background: var(--md-secondary-cont); }
        .stat-card.sc-tertiary  { background: var(--md-tertiary-cont); }
        .stat-card.sc-error     { background: var(--md-error-cont); }

        .stat-icon {
            width: 48px; height: 48px;
            border-radius: var(--shape-md);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }
        .stat-card.sc-primary   .stat-icon { background: rgba(21,101,192,.14);  color: var(--md-primary); }
        .stat-card.sc-secondary .stat-icon { background: rgba(27,94,32,.14);    color: var(--md-secondary); }
        .stat-card.sc-tertiary  .stat-icon { background: rgba(106,27,154,.14);  color: var(--md-tertiary); }
        .stat-card.sc-error     .stat-icon { background: rgba(179,38,30,.14);   color: var(--md-error); }

        .stat-label { font-size: .6875rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; margin-bottom: .25rem; }
        .stat-card.sc-primary   .stat-label { color: var(--md-on-primary-cont); opacity: .75; }
        .stat-card.sc-secondary .stat-label { color: var(--md-on-secondary-cont); opacity: .75; }
        .stat-card.sc-tertiary  .stat-label { color: var(--md-on-tertiary-cont); opacity: .75; }
        .stat-card.sc-error     .stat-label { color: var(--md-on-error-cont); opacity: .75; }

        .stat-value {
            font-size: 2rem; font-weight: 700; line-height: 1.15;
            letter-spacing: -.025em; margin-bottom: .25rem;
        }
        .stat-card.sc-primary   .stat-value { color: var(--md-on-primary-cont); }
        .stat-card.sc-secondary .stat-value { color: var(--md-on-secondary-cont); }
        .stat-card.sc-tertiary  .stat-value { color: var(--md-on-tertiary-cont); }
        .stat-card.sc-error     .stat-value { color: var(--md-on-error-cont); }

        .stat-sub { font-size: .75rem; }
        .stat-card.sc-primary   .stat-sub { color: var(--md-on-primary-cont); opacity: .6; }
        .stat-card.sc-secondary .stat-sub { color: var(--md-on-secondary-cont); opacity: .6; }
        .stat-card.sc-tertiary  .stat-sub { color: var(--md-on-tertiary-cont); opacity: .6; }
        .stat-card.sc-error     .stat-sub { color: var(--md-on-error-cont); opacity: .6; }

        /* =========================================================
           CUSTOM DATE RANGE PICKER
        ========================================================= */
        .drp-trigger {
            display: flex; align-items: center; gap: 6px;
            padding: .375rem .875rem;
            border: 1.5px solid #C4C6D0;
            border-radius: 8px; background: #fff;
            cursor: pointer; transition: border-color .12s;
            white-space: nowrap; user-select: none;
        }
        .drp-trigger:hover { border-color: #74777F; }
        .drp-trigger.open  { border: 2px solid #1565C0; padding: calc(.375rem - 0.5px) calc(.875rem - 0.5px); }

        .drp-prefix {
            font-size: .75rem; font-weight: 600;
            color: #44464F; white-space: nowrap; flex-shrink: 0;
        }
        .drp-input-group { display: flex; align-items: center; gap: 3px; }
        .drp-inp {
            border: none; outline: none; background: transparent;
            font-size: .8125rem; color: #1A1B1F;
            cursor: pointer; width: 88px; text-align: center;
            font-family: 'Inter', sans-serif;
        }
        .drp-inp::placeholder { color: #74777F; }
        .drp-sep { font-size: .8125rem; color: #74777F; }
        .drp-cal-icon { color: #74777F; display: flex; align-items: center; flex-shrink: 0; font-size: .875rem; }

        .drp-panel {
            position: absolute; right: 0; top: calc(100% + 6px);
            background: #fff;
            border: 1px solid #C4C6D0;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,.14);
            z-index: 1050; display: flex; align-items: stretch;
            overflow: hidden;
        }
        .drp-presets {
            display: flex; flex-direction: column;
            padding: 14px 10px; gap: 8px;
            border-right: 1px solid #C4C6D0; flex-shrink: 0;
        }
        .drp-preset {
            text-align: center; padding: 7px 14px;
            font-size: .8125rem; font-weight: 500;
            border: 1.5px solid #C4C6D0; background: #fff; color: #1A1B1F;
            border-radius: 8px; cursor: pointer;
            transition: all .12s; white-space: nowrap;
            min-width: 148px; font-family: 'Inter', sans-serif;
        }
        .drp-preset:hover  { border-color: #1565C0; color: #1565C0; }
        .drp-preset.active { border-color: #1565C0; color: #1565C0; background: rgba(21,101,192,.06); }

        .drp-cal-area { padding: 14px 16px; min-width: 560px; }
        .drp-cal-top  { display: flex; justify-content: center; margin-bottom: 12px; }
        .drp-kustom {
            padding: 5px 16px; font-size: .8125rem; font-weight: 500;
            border: 1.5px solid #1565C0; background: #fff; color: #1565C0;
            border-radius: 8px; cursor: pointer;
            font-family: 'Inter', sans-serif; transition: all .12s;
        }
        .drp-kustom:hover  { background: rgba(21,101,192,.06); }
        .drp-kustom.active { background: rgba(21,101,192,.10); }
        .drp-hint { font-size: .75rem; color: #44464F; margin-left: 10px; display: flex; align-items: center; }

        .drp-cal-nav { display: flex; align-items: center; gap: 4px; margin-bottom: 10px; }
        .drp-cal-title {
            flex: 1; text-align: center;
            font-size: .9375rem; font-weight: 500; color: #1A1B1F;
        }
        .drp-cal-spacer { width: 56px; flex-shrink: 0; }
        .drp-nav-btn {
            width: 28px; height: 28px; border: none; background: transparent;
            border-radius: 50%; cursor: pointer; font-size: 15px; line-height: 1;
            color: #44464F; display: flex; align-items: center; justify-content: center;
            transition: background .1s; flex-shrink: 0;
        }
        .drp-nav-btn:hover { background: #E1E2EC; color: #1A1B1F; }

        .drp-months { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        .drp-month-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px 0; }
        .drp-day-head {
            font-size: .6875rem; font-weight: 600; color: #44464F;
            text-align: center; padding: 3px 0 7px;
        }
        .drp-day {
            font-size: .8125rem; cursor: pointer; color: #1A1B1F;
            height: 34px; display: flex; align-items: center; justify-content: center;
            position: relative;
        }
        .drp-day-inner {
            width: 32px; height: 32px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 50%; position: relative; transition: background .1s;
        }
        .drp-day:hover:not(.other-month):not(.disabled) .drp-day-inner { background: #E1E2EC; }
        .drp-day.other-month { color: #C4C6D0; cursor: default; }
        .drp-day.disabled    { color: #C4C6D0; cursor: default; }

        .drp-day.in-range    { background: rgba(21,101,192,.10); }
        .drp-day.range-start { background: linear-gradient(to right, transparent 50%, rgba(21,101,192,.10) 50%); }
        .drp-day.range-end   { background: linear-gradient(to left,  transparent 50%, rgba(21,101,192,.10) 50%); }
        .drp-day.range-start.range-end { background: transparent; }

        .drp-day.selected .drp-day-inner { background: #1565C0; color: #fff; font-weight: 600; }
        .drp-day.selected:hover .drp-day-inner { background: #1565C0; }
        .drp-day.today:not(.selected) .drp-day-inner::after {
            content: ''; position: absolute; bottom: 2px; left: 50%;
            transform: translateX(-50%); width: 4px; height: 4px;
            border-radius: 50%; background: #1565C0;
        }
        .drp-day.selected.today .drp-day-inner::after {
            content: ''; position: absolute; bottom: 2px; left: 50%;
            transform: translateX(-50%); width: 4px; height: 4px;
            border-radius: 50%; background: #fff;
        }

        /* =========================================================
           ACTION BUTTONS
        ========================================================= */
        .btn-filled {
            display: inline-flex; align-items: center; gap: .375rem;
            padding: .5rem 1.25rem;
            border-radius: 20px;
            font-size: .8125rem; font-weight: 500;
            border: none; cursor: pointer; font-family: 'Inter', sans-serif;
            transition: all .15s;
        }
        .btn-filled.primary {
            background: var(--md-primary); color: var(--md-on-primary);
        }
        .btn-filled.primary:hover {
            background: #1255A6;
            box-shadow: 0 2px 8px rgba(21,101,192,.35);
        }

        .btn-tonal {
            display: inline-flex; align-items: center; gap: .375rem;
            padding: .5rem 1.125rem;
            border-radius: 20px;
            font-size: .8125rem; font-weight: 500;
            border: none; cursor: pointer; font-family: 'Inter', sans-serif;
            transition: background .15s;
        }
        .btn-tonal.secondary {
            background: var(--md-secondary-cont); color: var(--md-secondary);
        }
        .btn-tonal.secondary:hover { background: #B5D9B7; }

        .btn-text {
            display: inline-flex; align-items: center; gap: .375rem;
            padding: .5rem .875rem;
            border-radius: 20px;
            font-size: .8125rem; font-weight: 500;
            border: none; cursor: pointer; font-family: 'Inter', sans-serif;
            background: transparent;
            transition: background .15s;
        }
        .btn-text.primary { color: var(--md-primary); }
        .btn-text.primary:hover { background: rgba(21,101,192,.08); }

        .btn-icon-ghost {
            display: inline-flex; align-items: center; justify-content: center;
            width: 36px; height: 36px; border-radius: 50%;
            border: 1.5px solid var(--md-outline-var);
            background: transparent; cursor: pointer;
            color: var(--md-on-surface-var);
            transition: all .15s; font-family: 'Inter', sans-serif;
            font-size: .875rem;
        }
        .btn-icon-ghost:hover {
            background: rgba(26,27,31,.06);
            border-color: var(--md-outline);
            color: var(--md-on-surface);
        }

        /* =========================================================
           SECTION HEADER
        ========================================================= */
        .sec-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; margin-bottom: 1.25rem; }
        .sec-title   { font-size: 1rem; font-weight: 600; color: var(--md-on-surface); margin-bottom: .1875rem; }
        .sec-sub     { font-size: .75rem; color: var(--md-on-surface-var); }

        .sec-icon {
            width: 36px; height: 36px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; flex-shrink: 0;
        }

        /* =========================================================
           MD3 DATA TABLE
        ========================================================= */
        .md-table { width: 100%; border-collapse: collapse; }
        .md-table thead th {
            padding: .625rem 1rem;
            font-size: .6875rem; font-weight: 700; letter-spacing: .07em; text-transform: uppercase;
            color: var(--md-on-surface-var);
            background: #F8F9FF;
            border-bottom: 1px solid var(--md-outline-var);
        }
        .md-table thead th:first-child { border-radius: var(--shape-sm) 0 0 0; }
        .md-table thead th:last-child  { border-radius: 0 var(--shape-sm) 0 0; }
        .md-table tbody tr { border-bottom: 1px solid var(--md-outline-var); transition: background .12s; }
        .md-table tbody tr:last-child { border-bottom: none; }
        .md-table tbody tr:hover { background: rgba(21,101,192,.04); }
        .md-table tbody td { padding: .8125rem 1rem; font-size: .875rem; vertical-align: middle; }

        /* =========================================================
           RANK BADGES
        ========================================================= */
        .rank {
            display: inline-flex; align-items: center; justify-content: center;
            width: 28px; height: 28px; border-radius: 50%;
            font-size: .6875rem; font-weight: 700;
        }
        .rank-1 { background: #FFF3CD; color: #856404; }
        .rank-2 { background: #E9ECEF; color: #495057; }
        .rank-3 { background: #FFE8D6; color: #7D4F1E; }
        .rank-n { background: var(--md-surface-var); color: var(--md-on-surface-var); }

        /* =========================================================
           PROGRESS BAR (inline relative indicator)
        ========================================================= */
        .prog-wrap {
            height: 6px; border-radius: 3px;
            background: var(--md-surface-var);
            overflow: hidden; margin-top: .3125rem;
        }
        .prog-bar { height: 100%; border-radius: 3px; transition: width .5s cubic-bezier(.4,0,.2,1); }
        .prog-bar.primary   { background: var(--md-primary); }
        .prog-bar.secondary { background: var(--md-secondary); }
        .prog-bar.tertiary  { background: var(--md-tertiary); }
        .prog-bar.warning   { background: #E65100; }
        .prog-bar.danger    { background: var(--md-error); }

        /* MD3 Linear Progress — thick variant for metric summary */
        .prog-lg { height: 10px !important; border-radius: 5px !important; margin-top: 0 !important; }

        /* =========================================================
           CONTENT COMPLETION — Metric Summary Cards
        ========================================================= */
        .metric-card {
            background: #FFFFFF;
            border-radius: var(--shape-xl);
            padding: 1.5rem;
            box-shadow: var(--md-elev-1);
            transition: box-shadow .2s ease, transform .2s ease;
        }
        .metric-card:hover { box-shadow: var(--md-elev-2); transform: translateY(-2px); }

        .metric-rate {
            font-size: 3rem; font-weight: 700; line-height: 1;
            letter-spacing: -.04em;
        }
        .metric-rate.good    { color: var(--md-secondary); }
        .metric-rate.average { color: #E65100; }
        .metric-rate.low     { color: var(--md-error); }

        /* =========================================================
           STATUS CHIPS — MD3 Suggestion Chip variant
        ========================================================= */
        .status-chip {
            display: inline-flex; align-items: center; gap: .3125rem;
            padding: .25rem .6875rem;
            border-radius: 8px;
            font-size: .6875rem; font-weight: 600;
            letter-spacing: .03em; white-space: nowrap;
        }
        .status-chip .dot {
            width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0;
        }
        .status-chip.done    { background: var(--md-secondary-cont); color: var(--md-secondary); }
        .status-chip.done    .dot { background: var(--md-secondary); }
        .status-chip.ongoing { background: #FFF3E0; color: #E65100; }
        .status-chip.ongoing .dot { background: #E65100; }
        .status-chip.pending { background: var(--md-error-cont); color: var(--md-error); }
        .status-chip.pending .dot { background: var(--md-error); }

        /* =========================================================
           RATE BADGE (inline percentage in table)
        ========================================================= */
        .rate-badge {
            display: inline-flex; align-items: center;
            padding: .125rem .4375rem;
            border-radius: 6px;
            font-size: .6875rem; font-weight: 700;
            letter-spacing: .02em;
        }
        .rate-badge.good    { background: var(--md-secondary-cont); color: var(--md-secondary); }
        .rate-badge.average { background: #FFF3E0; color: #E65100; }
        .rate-badge.low     { background: var(--md-error-cont); color: var(--md-error); }
        .rate-badge.none    { background: var(--md-surface-var); color: var(--md-on-surface-var); }

        /* =========================================================
           AVATAR ICON (for table rows)
        ========================================================= */
        .av-icon {
            flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
        }

        /* =========================================================
           EMPTY STATE
        ========================================================= */
        .empty-state {
            text-align: center; padding: 2.5rem 1rem;
            color: var(--md-on-surface-var);
        }
        .empty-state .bi { font-size: 2rem; display: block; margin-bottom: .5rem; opacity: .4; }

        /* =========================================================
           SCROLLBAR
        ========================================================= */
        .table-responsive::-webkit-scrollbar { height: 4px; }
        .table-responsive::-webkit-scrollbar-track { background: transparent; }
        .table-responsive::-webkit-scrollbar-thumb { background: var(--md-outline-var); border-radius: 2px; }

        /* =========================================================
           MODAL OVERRIDES
        ========================================================= */
        .modal-content {
            border-radius: var(--shape-xl) !important;
            border: none !important;
            box-shadow: 0 8px 32px rgba(0,0,0,.16);
        }
        .modal-header { border-bottom: 1px solid var(--md-outline-var) !important; }
        .modal-footer { border-top:  1px solid var(--md-outline-var) !important; }
    </style>
@endpush

@section('content')
<div class="py-4">

    @if(($stats->totalSampel ?? 0) == 0)
        <div class="d-flex align-items-start gap-2 mb-4" role="status"
             style="padding:.875rem 1.125rem;border-radius:var(--shape-md);background:var(--md-tertiary-cont);color:var(--md-on-tertiary-cont);border-left:4px solid var(--md-tertiary);">
            <i class="bi bi-info-circle-fill" style="margin-top:.1rem;"></i>
            <div style="font-size:.8125rem;line-height:1.5;">
                Tidak ada data sampel untuk periode <strong>{{ $periodLabel }}</strong>.
                Coba pilih rentang tanggal yang lebih luas atau periode lain.
            </div>
        </div>
    @endif

    {{-- ============================================================
         PAGE HEADER + FILTERS
    ============================================================ --}}
    <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-end mb-4 gap-3">

        {{-- Title --}}
        <div>
            <p class="ts-label-sm mb-1" style="color:var(--md-on-surface-var);letter-spacing:.1em;text-transform:uppercase;">Overview</p>
            <h1 class="ts-headline-sm mb-1" style="color:var(--md-on-surface);">Dashboard</h1>
            <p class="ts-body-sm" style="color:var(--md-on-surface-var);">
                Pantau performa kreator, distribusi sampel, dan pengeluaran Anda.
            </p>
        </div>

        {{-- Date Range Picker --}}
        <div style="position:relative" id="drp-wrap">
            {{-- Trigger --}}
            <div id="drp-trigger" class="drp-trigger" onclick="drpToggle()">
                <span class="drp-prefix" id="drp-prefix">{{ $currentPeriod === 'custom' ? 'Kustom' : $periodLabel }}:</span>
                <div class="drp-input-group">
                    <input id="drp-from" class="drp-inp" readonly
                           value="{{ $curStart->format('M d, Y') }}"
                           placeholder="Tanggal mulai">
                    <span class="drp-sep">–</span>
                    <input id="drp-to" class="drp-inp" readonly
                           value="{{ $curEnd->format('M d, Y') }}"
                           placeholder="Tanggal akhir">
                </div>
                <span class="drp-cal-icon">
                    <i class="bi bi-calendar4-range"></i>
                </span>
            </div>

            {{-- Panel --}}
            <div id="drp-panel" class="drp-panel" style="display:none">
                {{-- Kiri: presets --}}
                <div class="drp-presets">
                    <button type="button" class="drp-preset {{ $currentPeriod === 'kemarin' ? 'active' : '' }}"
                            onclick="drpPreset('kemarin')">Kemarin</button>
                    <button type="button" class="drp-preset {{ $currentPeriod === '7d' ? 'active' : '' }}"
                            onclick="drpPreset('7d')">7 Hari Terakhir</button>
                    <button type="button" class="drp-preset {{ $currentPeriod === '30d' ? 'active' : '' }}"
                            onclick="drpPreset('30d')">30 Hari Terakhir</button>
                </div>

                {{-- Kanan: kalender --}}
                <div class="drp-cal-area">
                    <div class="drp-cal-top">
                        <button type="button" id="drp-kustom-btn"
                                class="drp-kustom {{ $currentPeriod === 'custom' ? 'active' : '' }}"
                                onclick="drpClearPick()">Kustom</button>
                        <span class="drp-hint" id="drp-hint"></span>
                    </div>
                    <div class="drp-cal-nav">
                        <button type="button" onclick="drpNavMonth(-12)" class="drp-nav-btn" title="Tahun sebelumnya">&#171;</button>
                        <button type="button" onclick="drpNavMonth(-1)"  class="drp-nav-btn" title="Bulan sebelumnya">&#8249;</button>
                        <span id="drp-cal-lbl-l" class="drp-cal-title"></span>
                        <span class="drp-cal-spacer"></span>
                        <span id="drp-cal-lbl-r" class="drp-cal-title"></span>
                        <button type="button" onclick="drpNavMonth(1)"   class="drp-nav-btn" title="Bulan berikutnya">&#8250;</button>
                        <button type="button" onclick="drpNavMonth(12)"  class="drp-nav-btn" title="Tahun berikutnya">&#187;</button>
                    </div>
                    <div class="drp-months">
                        <div id="drp-month-l"></div>
                        <div id="drp-month-r"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ============================================================
         STAT CARDS
    ============================================================ --}}
    @php
        $maxQty       = $top10Products->max('total_qty')        ?: 1;
        $maxPengeluaran = $allProducts->max('total_pengeluaran') ?: 1;
        $maxPic       = $topUser->max('total_dikirim')           ?: 1;
        $maxCreator   = $topCreator->max('total_dikirim')        ?: 1;
        $totalSKU     = $allProducts->count();
    @endphp

    <div class="row g-3 mb-4">
        {{-- TOTAL KREATOR --}}
        <div class="col-6 col-lg-3">
            <div class="stat-card sc-primary h-100">
                <div class="stat-icon"><i class="bi bi-person-video2"></i></div>
                <div class="stat-label">Total Kreator</div>
                <div class="stat-value">{{ number_format($totalCreator ?? 0) }}</div>
                <div class="stat-sub">kreator terdaftar</div>
            </div>
        </div>

        {{-- SAMPEL DIKIRIM --}}
        <div class="col-6 col-lg-3">
            <div class="stat-card sc-secondary h-100">
                <div class="stat-icon"><i class="bi bi-box-seam"></i></div>
                <div class="stat-label">Sampel Dikirim</div>
                <div class="stat-value">{{ number_format($stats->totalSampel ?? 0) }}</div>
                <div class="stat-sub">total pengiriman</div>
            </div>
        </div>

        {{-- PENGELUARAN --}}
        <div class="col-6 col-lg-3">
            <div class="stat-card sc-error h-100">
                <div class="stat-icon"><i class="bi bi-wallet2"></i></div>
                <div class="stat-label">Pengeluaran</div>
                <div class="stat-value" style="font-size:1.45rem;letter-spacing:-.015em;">
                    Rp {{ number_format($stats->totalPengeluaran ?? 0, 0, ',', '.') }}
                </div>
                <div class="stat-sub">total nilai sampel</div>
            </div>
        </div>

        {{-- PRODUK AKTIF (SKU) --}}
        <div class="col-6 col-lg-3">
            <div class="stat-card sc-tertiary h-100">
                <div class="stat-icon"><i class="bi bi-tags-fill"></i></div>
                <div class="stat-label">Produk Aktif</div>
                <div class="stat-value">{{ number_format($totalSKU) }}</div>
                <div class="stat-sub">varian SKU berbeda</div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         CREATOR TREND CHART
    ============================================================ --}}
    <div class="row g-3 mb-3">
        <div class="col-12">
            <div class="surf-card p-4">
                <div class="sec-header">
                    <div>
                        <div class="sec-title">Tren Kreator Baru</div>
                        <div class="sec-sub">
                            {{ $periodLabel }}
                        </div>
                    </div>
                    <div class="sec-icon" style="background:var(--md-primary-container);color:var(--md-primary);">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                </div>
                <div style="position:relative;height:275px;width:100%;">
                    <canvas id="creatorTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         TOP 10 SKU TABLE
    ============================================================ --}}
    <div class="row g-3 mb-3">
        <div class="col-12">
            <div class="surf-card p-4">
                <div class="sec-header">
                    <div>
                        <div class="sec-title">Top 10 SKU Utama</div>
                        <div class="sec-sub">Produk dengan volume pengiriman & pengeluaran tertinggi</div>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <a href="{{ request()->fullUrlWithQuery(['export' => 'sku']) }}"
                           class="btn-tonal secondary">
                            <i class="bi bi-file-earmark-excel"></i> Export
                        </a>
                        <button type="button"
                                class="btn-text primary"
                                data-bs-toggle="modal" data-bs-target="#modalAllSKU">
                            <i class="bi bi-arrows-fullscreen"></i> Lihat Semua
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="md-table">
                        <thead>
                            <tr>
                                <th width="56" class="text-center">Rank</th>
                                <th>Nama SKU</th>
                                <th class="text-end" style="white-space:nowrap;">Harga Satuan</th>
                                <th style="min-width:200px;">Total Qty</th>
                                <th class="text-end" style="white-space:nowrap;">Total Pengeluaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($top10Products as $index => $product)
                                @php $pct = $maxQty > 0 ? ($product->total_qty / $maxQty * 100) : 0; @endphp
                                <tr>
                                    <td class="text-center">
                                        @if($index === 0)
                                            <span class="rank rank-1"><i class="bi bi-trophy-fill" style="font-size:.5rem;"></i></span>
                                        @elseif($index === 1)
                                            <span class="rank rank-2">2</span>
                                        @elseif($index === 2)
                                            <span class="rank rank-3">3</span>
                                        @else
                                            <span class="rank rank-n">{{ $index + 1 }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="av-icon" style="width:32px;height:32px;border-radius:8px;background:var(--md-surface-var);color:var(--md-on-surface-var);font-size:.875rem;">
                                                <i class="bi bi-box2"></i>
                                            </div>
                                            <span class="ts-title-sm" style="color:var(--md-on-surface);">{{ $product->sku_utama }}</span>
                                        </div>
                                    </td>
                                    <td class="text-end ts-body-md" style="color:var(--md-on-surface-var);">
                                        Rp {{ number_format($product->harga, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span style="font-weight:600;color:var(--md-primary);min-width:44px;font-size:.875rem;">{{ number_format($product->total_qty) }}</span>
                                            <div class="flex-grow-1">
                                                <div class="prog-wrap"><div class="prog-bar primary" style="width:{{ $pct }}%;"></div></div>
                                            </div>
                                            <span class="ts-label-sm" style="color:var(--md-on-surface-var);">pcs</span>
                                        </div>
                                    </td>
                                    <td class="text-end" style="font-weight:600;color:var(--md-error);font-size:.875rem;">
                                        Rp {{ number_format($product->total_pengeluaran, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="empty-state">
                                            <i class="bi bi-inbox"></i>
                                            Belum ada data produk untuk ditampilkan.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         PIC + CREATOR (2-column)
    ============================================================ --}}
    <div class="row g-3">

        {{-- TOP PIC --}}
        <div class="col-12 col-lg-6">
            <div class="surf-card p-4 h-100">
                <div class="sec-header">
                    <div>
                        <div class="sec-title">Performa PIC</div>
                        <div class="sec-sub">PIC paling aktif mengirimkan sampel</div>
                    </div>
                    <div class="sec-icon" style="background:var(--md-primary-container);color:var(--md-primary);">
                        <i class="bi bi-person-badge-fill"></i>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="md-table">
                        <thead>
                            <tr>
                                <th width="48" class="text-center">#</th>
                                <th>Nama PIC</th>
                                <th style="min-width:150px;">Total Dikirim</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topUser as $index => $data)
                                @php $picPct = $maxPic > 0 ? ($data->total_dikirim / $maxPic * 100) : 0; @endphp
                                <tr>
                                    <td class="text-center">
                                        @if($index === 0)
                                            <span class="rank rank-1"><i class="bi bi-trophy-fill" style="font-size:.5rem;"></i></span>
                                        @elseif($index === 1)
                                            <span class="rank rank-2">2</span>
                                        @elseif($index === 2)
                                            <span class="rank rank-3">3</span>
                                        @else
                                            <span class="rank rank-n">{{ $index + 1 }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="av-icon" style="width:32px;height:32px;border-radius:50%;background:var(--md-primary-container);color:var(--md-primary);font-size:.875rem;">
                                                <i class="bi bi-person-fill"></i>
                                            </div>
                                            <span class="ts-title-sm" style="color:var(--md-on-surface);">{{ $data->user->name ?? 'User Tidak Ditemukan' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span style="font-weight:600;color:var(--md-primary);min-width:36px;font-size:.875rem;">{{ number_format($data->total_dikirim) }}</span>
                                            <div class="flex-grow-1">
                                                <div class="prog-wrap"><div class="prog-bar primary" style="width:{{ $picPct }}%;"></div></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3">
                                        <div class="empty-state">
                                            <i class="bi bi-inbox"></i>
                                            Tidak ada data PIC dalam periode ini.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TOP CREATOR --}}
        <div class="col-12 col-lg-6">
            <div class="surf-card p-4 h-100">
                <div class="sec-header">
                    <div>
                        <div class="sec-title">Top Kreator</div>
                        <div class="sec-sub">Kreator dengan penerimaan sampel terbanyak</div>
                    </div>
                    <div class="sec-icon" style="background:var(--md-tertiary-cont);color:var(--md-tertiary);">
                        <i class="bi bi-camera-reels-fill"></i>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="md-table">
                        <thead>
                            <tr>
                                <th width="48" class="text-center">#</th>
                                <th>Username Kreator</th>
                                <th style="min-width:150px;">Total Sampel</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topCreator as $index => $creatorData)
                                @php $crPct = $maxCreator > 0 ? ($creatorData->total_dikirim / $maxCreator * 100) : 0; @endphp
                                <tr>
                                    <td class="text-center">
                                        @if($index === 0)
                                            <span class="rank rank-1"><i class="bi bi-star-fill" style="font-size:.5rem;"></i></span>
                                        @elseif($index === 1)
                                            <span class="rank rank-2">2</span>
                                        @elseif($index === 2)
                                            <span class="rank rank-3">3</span>
                                        @else
                                            <span class="rank rank-n">{{ $index + 1 }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="av-icon" style="width:32px;height:32px;border-radius:50%;background:var(--md-tertiary-cont);color:var(--md-tertiary);font-size:.875rem;">
                                                <i class="bi bi-camera-reels-fill"></i>
                                            </div>
                                            <span class="ts-title-sm" style="color:var(--md-on-surface);">{{ $creatorData->creator->username ?? 'Unknown Creator' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span style="font-weight:600;color:var(--md-tertiary);min-width:36px;font-size:.875rem;">{{ number_format($creatorData->total_dikirim) }}</span>
                                            <div class="flex-grow-1">
                                                <div class="prog-wrap"><div class="prog-bar tertiary" style="width:{{ $crPct }}%;"></div></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3">
                                        <div class="empty-state">
                                            <i class="bi bi-inbox"></i>
                                            Belum ada data kreator.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    {{-- /row PIC + Creator --}}

</div>
{{-- /py-4 --}}

{{-- ============================================================
     MODAL — DAFTAR LENGKAP SKU
============================================================ --}}
<div class="modal fade" id="modalAllSKU" tabindex="-1" aria-labelledby="modalAllSKULabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header px-4 py-3">
                <h5 class="ts-title-md mb-0" id="modalAllSKULabel">Daftar Lengkap SKU Sampel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="md-table">
                        <thead>
                            <tr>
                                <th width="56" class="text-center">No.</th>
                                <th>Nama SKU</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Total Qty</th>
                                <th class="text-end">Total Pengeluaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allProducts as $index => $product)
                                <tr>
                                    <td class="text-center">
                                        <span class="rank rank-n">{{ $index + 1 }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="av-icon" style="width:28px;height:28px;border-radius:8px;background:var(--md-surface-var);color:var(--md-on-surface-var);font-size:.8rem;">
                                                <i class="bi bi-box2"></i>
                                            </div>
                                            <span class="ts-title-sm" style="color:var(--md-on-surface);">{{ $product->sku_utama }}</span>
                                        </div>
                                    </td>
                                    <td class="text-end ts-body-md" style="color:var(--md-on-surface-var);">
                                        Rp {{ number_format($product->harga, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end" style="font-weight:600;color:var(--md-primary);">
                                        {{ number_format($product->total_qty) }} pcs
                                    </td>
                                    <td class="text-end" style="font-weight:600;color:var(--md-error);">
                                        Rp {{ number_format($product->total_pengeluaran, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="empty-state">
                                            <i class="bi bi-inbox"></i>
                                            Belum ada data produk.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer px-4 py-3">
                <button type="button" class="btn-filled primary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            /* --------------------------------------------------
               DATE RANGE PICKER
            -------------------------------------------------- */
            (function () {
                const DAYS      = ['Sen','Sel','Rab','Kam','Jum','Sab','Min'];
                const INIT_START = '{{ $curStart->format('Y-m-d') }}';
                const INIT_END   = '{{ $curEnd->format('Y-m-d') }}';

                function parseLocal(iso) {
                    const [y, m, d] = iso.split('-').map(Number);
                    return new Date(y, m - 1, d);
                }
                const TODAY = new Date(); TODAY.setHours(0, 0, 0, 0);

                let calLeft   = new Date(parseLocal(INIT_START)); calLeft.setDate(1);
                let calRight  = new Date(calLeft.getFullYear(), calLeft.getMonth() + 1, 1);
                let pickStart = null, pickEnd = null, hovDay = null;

                const trigger = document.getElementById('drp-trigger');
                const panel   = document.getElementById('drp-panel');
                const lblL    = document.getElementById('drp-cal-lbl-l');
                const lblR    = document.getElementById('drp-cal-lbl-r');
                const kustom  = document.getElementById('drp-kustom-btn');

                function fmtISO(d) {
                    return d.getFullYear() + '-' +
                           String(d.getMonth() + 1).padStart(2, '0') + '-' +
                           String(d.getDate()).padStart(2, '0');
                }
                function fmtDisplay(d) {
                    const M = ['Jan','Feb','Mar','Apr','May','Jun',
                               'Jul','Aug','Sep','Oct','Nov','Dec'];
                    return M[d.getMonth()] + ' ' + String(d.getDate()).padStart(2,'0') + ', ' + d.getFullYear();
                }

                function openPanel() {
                    if (!(pickStart && !pickEnd)) {
                        pickStart = parseLocal(INIT_START);
                        pickEnd   = parseLocal(INIT_END);
                    }
                    hovDay = null;

                    const startRef = pickStart || parseLocal(INIT_START);
                    calLeft = new Date(startRef.getFullYear(), startRef.getMonth(), 1);

                    if (pickStart && pickEnd) {
                        const endMonth = new Date(pickEnd.getFullYear(), pickEnd.getMonth(), 1);
                        calRight = endMonth.getTime() > calLeft.getTime()
                            ? endMonth
                            : new Date(calLeft.getFullYear(), calLeft.getMonth() + 1, 1);
                    } else {
                        calRight = new Date(calLeft.getFullYear(), calLeft.getMonth() + 1, 1);
                    }

                    renderCalendars();
                    const hint = document.getElementById('drp-hint');
                    if (hint) hint.textContent = (pickStart && !pickEnd) ? 'Pilih tanggal akhir' : '';
                    panel.style.display = 'flex';
                    trigger.classList.add('open');
                }

                function closePanel() {
                    panel.style.display = 'none';
                    trigger.classList.remove('open');
                }

                window.drpToggle = function () {
                    panel.style.display === 'none' ? openPanel() : closePanel();
                };

                window.drpPreset = function (key) {
                    kustom.classList.remove('active');
                    closePanel();
                    const params = new URLSearchParams(window.location.search);
                    params.set('period', key);
                    params.delete('date_from');
                    params.delete('date_to');
                    window.location.href = '{{ route("admin.dashboard.index") }}?' + params.toString();
                };

                window.drpClearPick = function () {
                    pickStart = null; pickEnd = null; hovDay = null;
                    kustom.classList.add('active');
                    const hint = document.getElementById('drp-hint');
                    if (hint) hint.textContent = 'Pilih tanggal mulai';
                    renderCalendars();
                };

                window.drpNavMonth = function (delta) {
                    calLeft  = new Date(calLeft.getFullYear(), calLeft.getMonth() + delta, 1);
                    calRight = new Date(calLeft.getFullYear(), calLeft.getMonth() + 1, 1);
                    renderCalendars();
                };

                function apply(start, end) {
                    const s = start <= end ? start : end;
                    const e = start <= end ? end   : start;
                    document.getElementById('drp-prefix').textContent = 'Kustom:';
                    document.getElementById('drp-from').value         = fmtDisplay(s);
                    document.getElementById('drp-to').value           = fmtDisplay(e);
                    const params = new URLSearchParams(window.location.search);
                    params.set('period',    'custom');
                    params.set('date_from', fmtISO(s));
                    params.set('date_to',   fmtISO(e));
                    window.location.href = '{{ route("admin.dashboard.index") }}?' + params.toString();
                }

                function renderCalendars() {
                    lblL.textContent = String(calLeft.getMonth() + 1).padStart(2,'0') + ' / ' + calLeft.getFullYear();
                    lblR.textContent = String(calRight.getMonth() + 1).padStart(2,'0') + ' / ' + calRight.getFullYear();
                    renderMonth(document.getElementById('drp-month-l'), calLeft);
                    renderMonth(document.getElementById('drp-month-r'), calRight);
                }

                function renderMonth(el, monthDate) {
                    const year  = monthDate.getFullYear();
                    const month = monthDate.getMonth();
                    const first = new Date(year, month, 1);
                    const dow   = (first.getDay() + 6) % 7;
                    const start = new Date(first); start.setDate(1 - dow);

                    let html = '<div class="drp-month-grid">';
                    DAYS.forEach(d => { html += `<div class="drp-day-head">${d}</div>`; });

                    for (let i = 0; i < 42; i++) {
                        const d = new Date(start); d.setDate(start.getDate() + i);
                        const isOther    = d.getMonth() !== month;
                        const isToday    = d.getTime() === TODAY.getTime();
                        const isFuture   = d > TODAY;
                        const isDisabled = isOther || isFuture;

                        let a = pickStart, b = pickEnd || hovDay;
                        if (a && b && a > b) { const t = a; a = b; b = t; }

                        const isSel     = (pickStart && d.getTime() === pickStart.getTime()) ||
                                          (pickEnd   && d.getTime() === pickEnd.getTime());
                        const isInRange = a && b && d > a && d < b;
                        const isRangeS  = a && b && d.getTime() === a.getTime();
                        const isRangeE  = a && b && d.getTime() === b.getTime();

                        let cls = 'drp-day';
                        if (isOther)              cls += ' other-month';
                        if (isFuture && !isOther) cls += ' disabled';
                        if (isToday)              cls += ' today';
                        if (isInRange && !isSel)  cls += ' in-range';
                        if (isRangeS  && !isSel)  cls += ' range-start';
                        if (isRangeE  && !isSel)  cls += ' range-end';
                        if (isSel)                cls += ' selected';

                        const iso   = fmtISO(d);
                        const guard = isDisabled ? '' :
                            `onclick="drpDayClick('${iso}')" onmouseenter="drpDayHover('${iso}')" onmouseleave="drpDayLeave()"`;
                        html += `<div class="${cls}" ${guard}><span class="drp-day-inner">${d.getDate()}</span></div>`;
                    }
                    html += '</div>';
                    el.innerHTML = html;
                }

                window.drpDayClick = function (iso) {
                    const d    = parseLocal(iso);
                    const hint = document.getElementById('drp-hint');
                    if (!pickStart || (pickStart && pickEnd)) {
                        pickStart = d; pickEnd = null; hovDay = null;
                        kustom.classList.add('active');
                        if (hint) hint.textContent = 'Pilih tanggal akhir';
                        renderCalendars();
                    } else {
                        pickEnd = d;
                        if (hint) hint.textContent = '';
                        renderCalendars();
                        apply(pickStart, pickEnd);
                    }
                };
                window.drpDayHover = function (iso) {
                    if (pickStart && !pickEnd) { hovDay = parseLocal(iso); renderCalendars(); }
                };
                window.drpDayLeave = function () {
                    if (pickStart && !pickEnd) { hovDay = null; renderCalendars(); }
                };

                document.addEventListener('click', function (e) {
                    if (!document.getElementById('drp-wrap').contains(e.target)) closePanel();
                });
            })();

            /* --------------------------------------------------
               CREATOR TREND LINE CHART
            -------------------------------------------------- */
            const raw    = @json($creatorPerDay);
            const labels = raw.map(item => new Date(item.date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }));
            const values = raw.map(item => item.total_creator);

            const canvas = document.getElementById('creatorTrendChart');
            const ctx    = canvas.getContext('2d');

            // MD3 primary gradient fill
            const grad = ctx.createLinearGradient(0, 0, 0, 275);
            grad.addColorStop(0, 'rgba(21, 101, 192, 0.20)');
            grad.addColorStop(1, 'rgba(21, 101, 192, 0.01)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Kreator Baru',
                        data: values,
                        backgroundColor: grad,
                        borderColor: '#1565C0',
                        borderWidth: 2.5,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#1565C0',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 7,
                        pointHoverBackgroundColor: '#1565C0',
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            intersect: false,
                            mode: 'index',
                            // MD3 tooltip: dark surface
                            backgroundColor: '#1A1B1F',
                            titleColor: '#E1E2EC',
                            bodyColor: '#C4C6D0',
                            padding: 12,
                            cornerRadius: 8,
                            borderColor: 'rgba(255,255,255,.08)',
                            borderWidth: 1,
                            callbacks: {
                                label: ctx => ` ${ctx.parsed.y} kreator`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                color: '#74777F',
                                font: { size: 11, family: 'Inter' }
                            },
                            grid: {
                                color: '#E1E2EC',
                                lineWidth: 1
                            },
                            border: { display: false, dash: [4, 4] }
                        },
                        x: {
                            grid: { display: false },
                            ticks: {
                                color: '#74777F',
                                font: { size: 11, family: 'Inter' }
                            },
                            border: { display: false }
                        }
                    }
                }
            });
        });
    </script>
@endpush

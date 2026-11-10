{{-- Gaya bersama halaman KORA (index & Kreator Fit). --}}
<style>
    .ts-headline-sm { font-size: 1.5rem; font-weight: 600; line-height: 2rem; }
    .ts-label-sm    { font-size: .6875rem; font-weight: 500; letter-spacing: .06em; }
    .ts-body-sm     { font-size: .75rem; line-height: 1rem; }

    .kora-tabs { display: flex; gap: .25rem; border-bottom: 1px solid var(--md-outline-var); margin-bottom: 1.25rem; flex-wrap: wrap; }
    .kora-tab {
        display: inline-flex; align-items: center; gap: .4rem; padding: .625rem 1rem;
        font-size: .8125rem; font-weight: 500; color: var(--md-on-surface-var); text-decoration: none;
        border-bottom: 3px solid transparent; margin-bottom: -1px;
    }
    .kora-tab:hover { color: var(--md-on-surface); background: rgba(0,0,0,.03); }
    .kora-tab.active { color: var(--md-primary); border-bottom-color: var(--md-primary); }

    .kora-filter select, .kora-filter input[type=date] {
        border: 1.5px solid var(--md-outline-var); border-radius: 8px; padding: .3rem .6rem;
        font-size: .8125rem; background: #fff; color: var(--md-on-surface);
    }
    .kora-chip {
        display: inline-block; padding: .3rem .75rem; border-radius: 8px; font-size: .75rem; font-weight: 500;
        border: 1.5px solid var(--md-outline-var); color: var(--md-on-surface-var); text-decoration: none; background: #fff;
    }
    .kora-chip.active { background: var(--md-primary-container); border-color: var(--md-primary-container); color: var(--md-primary); }

    .kora-kpi { background: #fff; border-radius: var(--shape-lg); box-shadow: var(--md-elev-1); padding: 1.125rem 1.25rem; height: 100%; }
    .kora-kpi .v { font-size: 1.75rem; font-weight: 700; letter-spacing: -.02em; color: var(--md-on-surface); line-height: 1.15; }
    .kora-kpi .l { font-size: .75rem; color: var(--md-on-surface-var); margin-top: .25rem; }
    .kora-kpi .i { font-size: 1.1rem; color: var(--md-primary); margin-bottom: .5rem; }

    .sec-title { font-size: 1rem; font-weight: 600; color: var(--md-on-surface); }
    .sec-sub   { font-size: .75rem; color: var(--md-on-surface-var); }

    .funnel { display: grid; grid-template-columns: minmax(150px, 220px) 1fr 70px; gap: .55rem .9rem; align-items: center; font-size: .8125rem; }
    .funnel .bar { height: 20px; background: var(--md-primary-container); border-radius: 4px; overflow: hidden; }
    .funnel .bar i { display: block; height: 100%; background: var(--md-primary); border-radius: 4px; }
    .funnel .n { text-align: right; font-weight: 600; font-variant-numeric: tabular-nums; }

    .kora-table { width: 100%; font-size: .8125rem; }
    .kora-table th { font-size: .6875rem; font-weight: 600; letter-spacing: .06em; text-transform: uppercase; color: var(--md-on-surface-var);
                     padding: .5rem .5rem; border-bottom: 1px solid var(--md-outline-var); white-space: nowrap; }
    .kora-table td { padding: .6rem .5rem; border-bottom: 1px solid #EEF0F3; vertical-align: top; }
    .kora-table tr:last-child td { border-bottom: 0; }
    .kora-table .num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
    .kora-table tr.sorot td { background: #F3F7FF; }
    .kora-table.th-lipat th { white-space: normal; vertical-align: bottom; }
    .kora-table .kelompok { min-width: 170px; }

    .kora-badge { display: inline-block; font-size: .6875rem; font-weight: 600; padding: .15rem .5rem; border-radius: 6px; white-space: nowrap; }
    .b-tim     { background: #FFF4E5; color: #8A4B00; }
    .b-deal    { background: #E6F4EA; color: #1B5E20; }
    .b-kreator { background: #EEF0F3; color: #44464F; }
    .b-sampel  { background: var(--md-primary-container); color: var(--md-primary); }

    .kora-note { font-size: .75rem; color: var(--md-on-surface-var); line-height: 1.5; }
    .kora-alert { padding: .875rem 1.125rem; border-radius: var(--shape-md); background: #FDECEA; color: #8C1D18; border-left: 4px solid #B3261E; font-size: .8125rem; }
    .produk-judul { max-width: 360px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
</style>

@extends('layouts.admin')

@section('title', 'KORA')

@php
    $rp = function ($n) {
        $n = (float) $n;
        if ($n >= 1e9) return 'Rp' . number_format($n / 1e9, 2, ',', '.') . ' M';
        if ($n >= 1e6) return 'Rp' . number_format($n / 1e6, 1, ',', '.') . ' jt';
        if ($n >= 1e3) return 'Rp' . number_format($n / 1e3, 0, ',', '.') . ' rb';
        return 'Rp' . number_format($n, 0, ',', '.');
    };
    $angka = fn ($n) => number_format((float) $n, 0, ',', '.');
    $persen = fn ($n) => $n === null ? '–' : $n . '%';
    $tautan = fn (array $ubah) => route('admin.kora.index', array_filter(array_merge(
        ['tab' => $tab, 'toko' => $toko, 'period' => $period,
         'date_from' => $period === 'custom' ? $dari->format('Y-m-d') : null,
         'date_to'   => $period === 'custom' ? $sampai->format('Y-m-d') : null],
        $ubah), fn ($v) => $v !== null && $v !== ''));
    $tabs = [
        'ringkasan' => ['Ringkasan', 'bi-speedometer2'],
        'kinerja'   => ['Kinerja Sampel', 'bi-bar-chart-line'],
        'antrian'   => ['Antrian', 'bi-hourglass-split'],
        'penjualan' => ['Penjualan Video', 'bi-graph-up-arrow'],
    ];
@endphp

@push('styles')
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
@endpush

@section('content')
<div class="py-4">

    {{-- Header + filter toko --}}
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end mb-3 gap-3">
        <div>
            <p class="ts-label-sm mb-1" style="color:var(--md-on-surface-var);text-transform:uppercase;">Creator Agent</p>
            <h1 class="ts-headline-sm mb-1" style="color:var(--md-on-surface);">KORA</h1>
            <p class="ts-body-sm mb-0" style="color:var(--md-on-surface-var);">
                Screening, negosiasi, dan hasil sampel yang ditangani KORA. Hanya baca, data dari database KORA.
            </p>
        </div>
        <form method="GET" action="{{ route('admin.kora.index') }}" class="kora-filter d-flex align-items-center gap-2">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <input type="hidden" name="period" value="{{ $period }}">
            @if($period === 'custom')
                <input type="hidden" name="date_from" value="{{ $dari->format('Y-m-d') }}">
                <input type="hidden" name="date_to" value="{{ $sampai->format('Y-m-d') }}">
            @endif
            <label for="kora-toko" class="ts-body-sm" style="color:var(--md-on-surface-var);">Toko</label>
            <select id="kora-toko" name="toko" onchange="this.form.submit()">
                <option value="">Semua toko</option>
                @foreach($daftarToko as $slug => $nama)
                    <option value="{{ $slug }}" @selected($toko === $slug)>{{ $nama }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <nav class="kora-tabs" aria-label="Bagian KORA">
        @foreach($tabs as $kunci => [$label, $ikon])
            <a href="{{ $tautan(['tab' => $kunci, 'state' => null, 'page' => null]) }}"
               class="kora-tab{{ $tab === $kunci ? ' active' : '' }}" @if($tab === $kunci) aria-current="page" @endif>
                <i class="bi {{ $ikon }}"></i>{{ $label }}
            </a>
        @endforeach
    </nav>

    @if($galat)
        <div class="kora-alert mb-4" role="alert"><i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $galat }}</div>
    @elseif($tab === 'ringkasan')
        @php $r = $ringkasan; $maks = max(1, $r['dicek']); @endphp

        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
            @foreach(['7d' => '7 hari', '30d' => '30 hari', 'semua' => 'Sejak KORA mulai'] as $kunci => $label)
                <a href="{{ $tautan(['period' => $kunci, 'date_from' => null, 'date_to' => null]) }}"
                   class="kora-chip{{ $period === $kunci ? ' active' : '' }}">{{ $label }}</a>
            @endforeach
            <form method="GET" action="{{ route('admin.kora.index') }}" class="kora-filter d-flex align-items-center gap-1">
                <input type="hidden" name="tab" value="ringkasan">
                <input type="hidden" name="period" value="custom">
                @if($toko)<input type="hidden" name="toko" value="{{ $toko }}">@endif
                <input type="date" name="date_from" value="{{ $dari->format('Y-m-d') }}" aria-label="Tanggal mulai">
                <span class="ts-body-sm">–</span>
                <input type="date" name="date_to" value="{{ $sampai->format('Y-m-d') }}" aria-label="Tanggal akhir">
                <button type="submit" class="kora-chip{{ $period === 'custom' ? ' active' : '' }}">Terapkan</button>
            </form>
            <span class="ts-body-sm ms-1" style="color:var(--md-on-surface-var);">{{ $periodLabel }} · pengajuan yang masuk di periode ini</span>
        </div>

        <div class="row g-3 mb-3">
            @foreach([
                ['bi-funnel', $angka($r['dicek']), 'pengajuan dicek otomatis (' . $angka($r['kreator']) . ' kreator)'],
                ['bi-chat-dots', $angka($r['chat']), 'chat ke kreator terkirim otomatis'],
                ['bi-hand-thumbs-up', $angka($r['deal']), 'kreator deal lewat negosiasi KORA'],
                ['bi-people', $angka($r['kartu']), 'kartu keputusan ke tim' . ($r['median_putus'] !== null ? ' · median ' . $r['median_putus'] . ' menit' : '')],
            ] as [$ikon, $nilai, $label])
                <div class="col-6 col-lg-3">
                    <div class="kora-kpi"><div class="i"><i class="bi {{ $ikon }}"></i></div><div class="v">{{ $nilai }}</div><div class="l">{{ $label }}</div></div>
                </div>
            @endforeach
        </div>

        <div class="surf-card p-4 mb-3">
            <div class="sec-title mb-1">Dari pengajuan sampai sampel</div>
            <div class="sec-sub mb-3">{{ $toko ? $daftarToko[$toko] : 'Semua toko' }} · {{ $periodLabel }}</div>
            <div class="funnel">
                @foreach([
                    ['Pengajuan dicek', $r['dicek']],
                    ['Ditolak otomatis (tidak layak)', $r['ditolak']],
                    ['Lolos / diteruskan ke tim', $r['lolos']],
                    ['Ditawari 5 video', $r['ditawari']],
                    ['Kreator deal', $r['deal']],
                    ['Sampel di-approve', $r['approve']],
                ] as [$label, $n])
                    <div>{{ $label }}</div>
                    <div class="bar" role="img" aria-label="{{ $label }}: {{ $angka($n) }}"><i style="width:{{ round(100 * $n / $maks, 1) }}%"></i></div>
                    <div class="n">{{ $angka($n) }}</div>
                @endforeach
            </div>
            <p class="kora-note mt-3 mb-0">
                @if($r['ditawari'])
                    {{ round(100 * $r['deal'] / $r['ditawari']) }}% kreator yang ditawari akhirnya deal.
                @endif
                Tidak semua yang lolos ditawari: sebagian diambil alih tim, mengajukan banyak produk, atau berada di toko yang negosiasinya belum aktif.
            </p>
        </div>

        <div class="surf-card p-4 mb-3">
            <div class="sec-title mb-1">Sampel di-approve per jalur</div>
            <div class="sec-sub mb-3">Dihitung dari tanggal approve, {{ $periodLabel }}</div>
            @php
                $labelJalur = ['kora' => 'Di-approve KORA (setelah DEAL tim)', 'kora_tambahan' => 'KORA, permintaan tambahan (OK tim)',
                               'approve_manual_wa' => 'Tim approve manual (lapor di WA)', 'sinkron_tiktok' => 'Tim approve di TikTok (terbaca sinkron)',
                               'data_lama' => 'Data lama'];
            @endphp
            <table class="kora-table">
                <thead><tr><th>Jalur</th><th class="num">Produk sampel</th></tr></thead>
                <tbody>
                @foreach($labelJalur as $k => $label)
                    @if(($r['jalur'][$k] ?? 0) > 0)
                        <tr><td>{{ $label }}</td><td class="num">{{ $angka($r['jalur'][$k]) }}</td></tr>
                    @endif
                @endforeach
                @if(!array_sum($r['jalur']))
                    <tr><td colspan="2" class="kora-note">Belum ada sampel di-approve pada periode ini.</td></tr>
                @endif
                </tbody>
            </table>
        </div>

    @elseif($tab === 'kinerja')
        @php $kora = $kinerja['kora_penuh']['matang']; @endphp
        <div class="surf-card p-4 mb-3">
            <div class="sec-title mb-1">Kinerja sampel per kelompok</div>
            <div class="sec-sub mb-3">
                Sampel "matang": mulai {{ \App\Services\KoraStats::UMUR_MIN }}–{{ \App\Services\KoraStats::UMUR_MAKS }} hari lalu,
                supaya sampel baru (belum sampai / belum sempat posting) tidak terlihat buruk. {{ $toko ? $daftarToko[$toko] : 'Semua toko' }}.
            </div>
            <div class="table-responsive">
            <table class="kora-table th-lipat">
                <thead><tr>
                    <th class="kelompok">Kelompok</th><th class="num">Sampel matang</th><th class="num">Selesai di TikTok</th>
                    <th class="num">Video lengkap</th><th class="num">Sampel laku</th><th class="num">Views</th>
                    <th class="num">GMV</th><th class="num">GMV / sampel</th><th class="num">GMV / nilai sampel</th><th class="num">Approve 7 hari</th>
                </tr></thead>
                <tbody>
                @foreach($kinerja as $k => $x)
                    <tr class="{{ in_array($k, ['kora_penuh', 'nego_kora']) ? 'sorot' : '' }}">
                        <td class="kelompok"><strong>{{ $x['label'] }}</strong></td>
                        <td class="num">{{ $angka($x['matang']) }}</td>
                        <td class="num">{{ $persen($x['selesai']) }}</td>
                        <td class="num">{{ $x['lengkap'] === null ? ($x['matang'] ? 'belum dinilai' : '–') : $x['lengkap'] . '%' }}</td>
                        <td class="num">{{ $persen($x['laku']) }}</td>
                        <td class="num">{{ $x['matang'] ? $angka($x['views']) : '–' }}</td>
                        <td class="num">{{ $x['matang'] ? $rp($x['gmv']) : '–' }}</td>
                        <td class="num">{{ $x['per_sampel'] === null ? '–' : $rp($x['per_sampel']) }}</td>
                        <td class="num">{{ $x['rasio'] === null ? '–' : number_format($x['rasio'], 1, ',', '.') . '×' }}</td>
                        <td class="num">{{ $angka($x['baru']) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            </div>
            <ul class="kora-note mt-3 mb-0 ps-3">
                @if($kora < 10)
                    <li><strong>Sampel matang KORA penuh baru {{ $kora }} (&lt; 10): perbandingan dengan tim belum adil.</strong></li>
                @endif
                <li><strong>KORA penuh</strong>: screening, negosiasi, dan approve oleh KORA (setelah DEAL tim). <strong>Nego KORA</strong>: KORA negosiasi, tim yang approve.
                    <strong>Ditangani tim</strong>: lolos screening KORA, diproses tim. <strong>Di luar KORA</strong>: sampel yang tidak lewat KORA.</li>
                <li>Video lengkap hanya dinilai untuk sampel mulai {{ \App\Services\KoraStats::UMUR_LENGKAP }}–{{ \App\Services\KoraStats::UMUR_MAKS }} hari lalu (batas video 30 hari) yang punya janji video.</li>
                <li>Sampel laku = ada video kreator untuk produk itu yang menghasilkan penjualan. GMV &amp; views dihitung sejak sampel mulai.</li>
            </ul>
        </div>

    @elseif($tab === 'antrian')
        @php $a = $antrian; $hal = max(1, (int) request('page', 1)); $halAkhir = max(1, (int) ceil($a['total'] / 10)); @endphp
        <div class="d-flex flex-wrap gap-2 mb-3">
            <a href="{{ $tautan(['state' => null, 'page' => null]) }}" class="kora-chip{{ !$state ? ' active' : '' }}">Semua · {{ array_sum($a['jumlah']) }}</a>
            @foreach(\App\Services\KoraStats::ANTRIAN as $k => $label)
                <a href="{{ $tautan(['state' => $k, 'page' => null]) }}" class="kora-chip{{ $state === $k ? ' active' : '' }}">{{ $label }} · {{ $a['jumlah'][$k] ?? 0 }}</a>
            @endforeach
        </div>
        <div class="surf-card p-4 mb-3">
            <div class="sec-title mb-1">Pengajuan yang sedang berjalan</div>
            <div class="sec-sub mb-3">Belum lewat batas waktu TikTok, batas terdekat di atas. Keputusan tetap lewat WA grup.</div>
            <div class="table-responsive">
            <table class="kora-table">
                <thead><tr><th>#</th><th>Toko</th><th>Kreator</th><th>Produk</th><th>Status</th><th class="num">Sisa waktu</th></tr></thead>
                <tbody>
                @forelse($a['baris'] as $b)
                    @php
                        $batas = \Carbon\Carbon::parse($b->request_deadline);
                        $sisa = now()->diffInHours($batas, false);
                        $kelas = match ($b->state) { 'deal_tunggu_approve' => 'b-deal', 'nego_menunggu_kreator' => 'b-kreator', default => 'b-tim' };
                    @endphp
                    <tr>
                        <td class="num" style="text-align:left;">{{ $b->id }}</td>
                        <td>{{ $daftarToko[$b->store_slug] ?? $b->store_slug }}</td>
                        <td>
                            @if($b->username)
                                <a href="{{ 'https://www.tiktok.com/@' . ltrim($b->username, '@') }}" target="_blank" rel="noopener">{{ '@' . ltrim($b->username, '@') }}</a>
                            @else – @endif
                        </td>
                        <td>
                            <div class="produk-judul" title="{{ implode(' · ', $b->produk) }}">{{ $b->produk[0] ?? '–' }}</div>
                            @if(count($b->produk) > 1)<span class="kora-note">+{{ count($b->produk) - 1 }} produk lain</span>@endif
                        </td>
                        <td><span class="kora-badge {{ $kelas }}">{{ \App\Services\KoraStats::ANTRIAN[$b->state] ?? $b->state }}</span></td>
                        <td class="num" style="{{ $sisa < 24 ? 'color:#B3261E;font-weight:600;' : '' }}">
                            {{ $sisa < 24 ? $sisa . ' jam' : intdiv($sisa, 24) . ' hari' . ($sisa % 24 ? ' ' . ($sisa % 24) . ' jam' : '') }}
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="kora-note">Tidak ada pengajuan yang sedang menunggu.</td></tr>
                @endforelse
                </tbody>
            </table>
            </div>
            @if($halAkhir > 1)
                <div class="d-flex justify-content-between align-items-center mt-3 kora-note">
                    <span>Halaman {{ $hal }} dari {{ $halAkhir }} · {{ $a['total'] }} pengajuan</span>
                    <span class="d-flex gap-2">
                        @if($hal > 1)<a class="kora-chip" href="{{ $tautan(['state' => $state, 'page' => $hal - 1]) }}">‹ Sebelumnya</a>@endif
                        @if($hal < $halAkhir)<a class="kora-chip" href="{{ $tautan(['state' => $state, 'page' => $hal + 1]) }}">Berikutnya ›</a>@endif
                    </span>
                </div>
            @endif
        </div>

    @elseif($tab === 'penjualan')
        @php $j = $penjualan; @endphp
        <div class="surf-card p-4 mb-3">
            <div class="sec-title mb-1">GMV affiliate video per minggu</div>
            <div class="sec-sub mb-3">{{ $toko ? $daftarToko[$toko] : '6 toko' }} · ±3 bulan terakhir, per minggu yang datanya sudah lengkap · biru = KORA aktif (mulai 5 Okt)</div>
            <div style="position:relative;height:280px;width:100%;">
                <canvas id="koraGmvChart" aria-label="GMV affiliate video per minggu" role="img"></canvas>
            </div>
            <p class="kora-note mt-2 mb-0">Kenaikan atau penurunan penjualan juga dipengaruhi event dan iklan, jadi tidak bisa dianggap hasil KORA saja.</p>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-12 col-xl-6">
                <div class="surf-card p-4 h-100">
                    <div class="sec-title mb-1">Top 10 kreator · 28 hari terakhir</div>
                    <div class="sec-sub mb-3">GMV video affiliate. <span class="kora-badge b-sampel">sampel</span> = pernah dapat sampel produk yang dijualnya.</div>
                    <table class="kora-table">
                        <thead><tr><th>Kreator</th><th>Toko</th><th class="num">Video</th><th class="num">Order</th><th class="num">GMV</th></tr></thead>
                        <tbody>
                        @forelse($j['kreator'] as $k)
                            <tr>
                                <td><a href="{{ 'https://www.tiktok.com/@' . ltrim($k->username, '@') }}" target="_blank" rel="noopener">{{ '@' . ltrim($k->username, '@') }}</a>
                                    @if($k->sampel)<span class="kora-badge b-sampel ms-1">sampel</span>@endif</td>
                                <td>{{ $daftarToko[$k->store_slug] ?? $k->store_slug }}</td>
                                <td class="num">{{ $angka($k->video) }}</td>
                                <td class="num">{{ $angka($k->orders) }}</td>
                                <td class="num">{{ $rp($k->gmv) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="kora-note">Belum ada data penjualan.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-12 col-xl-6">
                <div class="surf-card p-4 h-100">
                    <div class="sec-title mb-1">Top 10 produk · 28 hari terakhir</div>
                    <div class="sec-sub mb-3">GMV video multi-produk dibagi rata ke produknya.</div>
                    <div class="table-responsive">
                    <table class="kora-table">
                        <thead><tr><th>Produk</th><th class="num">Kreator</th><th class="num">Order</th><th class="num">GMV</th></tr></thead>
                        <tbody>
                        @forelse($j['produk'] as $p)
                            <tr>
                                <td><div class="produk-judul" style="max-width:230px;" title="{{ $p->judul ?? $p->product_id }}">{{ $p->judul ?? 'Produk ' . $p->product_id }}</div>
                                    <span class="kora-note">{{ $daftarToko[$p->store_slug] ?? $p->store_slug }}</span></td>
                                <td class="num">{{ $angka($p->kreator) }}</td>
                                <td class="num">{{ $angka($p->orders) }}</td>
                                <td class="num">{{ $rp($p->gmv) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="kora-note">Belum ada data penjualan.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <p class="kora-note mb-0"><i class="bi bi-info-circle me-1"></i>Data dari KORA, diperbarui tiap putaran KORA (±60 menit); halaman ini menyimpan cache 5–30 menit.</p>
</div>
@endsection

@if(!$galat && $tab === 'penjualan')
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
            (function () {
                const data = @json(collect($penjualan['mingguan'])->map(fn ($m) => ['minggu' => $m->minggu_awal, 'gmv' => (float) $m->gmv])->values());
                const css = getComputedStyle(document.documentElement);
                const biru = css.getPropertyValue('--md-primary').trim() || '#1565C0';
                const abu = '#C4C6D0';
                const label = data.map(d => new Date(d.minggu + 'T00:00:00').toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }));
                new Chart(document.getElementById('koraGmvChart'), {
                    type: 'bar',
                    data: {
                        labels: label,
                        datasets: [{
                            label: 'GMV',
                            data: data.map(d => d.gmv / 1e6),
                            backgroundColor: data.map(d => d.minggu >= '2026-10-05' ? biru : abu),
                            borderRadius: { topLeft: 4, topRight: 4 },
                            borderSkipped: 'bottom',
                            maxBarThickness: 42,
                        }],
                    },
                    options: {
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { callbacks: {
                                title: items => 'Minggu mulai ' + items[0].label,
                                label: c => 'GMV Rp' + c.parsed.y.toLocaleString('id-ID', { maximumFractionDigits: 1 }) + ' jt',
                            } },
                        },
                        scales: {
                            y: { beginAtZero: true, grid: { color: '#EEF0F3' }, ticks: { callback: v => v.toLocaleString('id-ID') + ' jt' } },
                            x: { grid: { display: false } },
                        },
                    },
                });
            })();
        </script>
    @endpush
@endif

@extends('layouts.admin')

@section('title', 'KORA · Boost')

@php
    $rp = function ($n) {
        $n = (float) $n;
        if ($n >= 1e9) return 'Rp' . number_format($n / 1e9, 2, ',', '.') . ' M';
        if ($n >= 1e6) return 'Rp' . number_format($n / 1e6, 1, ',', '.') . ' jt';
        if ($n >= 1e3) return 'Rp' . number_format($n / 1e3, 0, ',', '.') . ' rb';
        return 'Rp' . number_format($n, 0, ',', '.');
    };
    $angka = fn ($n) => number_format((float) $n, 0, ',', '.');
    $tgl   = fn ($t, $f = 'D MMM') => $t ? \Carbon\Carbon::parse($t)->locale('id')->isoFormat($f) : '–';
    $label = \App\Services\KoraBoost::STATUS;
    $tautan = fn (array $ubah) => route('admin.kora.boost', array_filter(array_merge(
        ['kampanye' => $k->kode ?? null], $filter, $ubah), fn ($v) => $v !== null && $v !== ''));
@endphp

@push('styles')
    @include('admin.kora._gaya')
    <style>
        .target-bar { height: 10px; background: var(--md-primary-container); border-radius: 6px; overflow: hidden; margin-top: .6rem; }
        .target-bar i { display: block; height: 100%; background: var(--md-primary); border-radius: 6px; }
        .kora-filter input[type=search] { border: 1.5px solid var(--md-outline-var); border-radius: 8px; padding: .3rem .6rem; font-size: .8125rem; background: #fff; }
    </style>
@endpush

@section('content')
<div class="py-4">

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end mb-3 gap-3">
        <div>
            <p class="ts-label-sm mb-1" style="color:var(--md-on-surface-var);text-transform:uppercase;">KORA</p>
            <h1 class="ts-headline-sm mb-1" style="color:var(--md-on-surface);">Boost</h1>
            <p class="ts-body-sm mb-0" style="color:var(--md-on-surface-var);">
                Kreator yang diundang lewat program boost: produk, status, tanggal, video, dan hasilnya. Hanya baca, data dari KORA.
            </p>
        </div>
        @if($kampanye)
        <form method="GET" action="{{ route('admin.kora.boost') }}" class="kora-filter d-flex align-items-center gap-2">
            <label for="boost-kampanye" class="ts-body-sm" style="color:var(--md-on-surface-var);">Kampanye</label>
            <select id="boost-kampanye" name="kampanye" onchange="this.form.submit()">
                @foreach($kampanye as $kp)
                    <option value="{{ $kp->kode }}" @selected($k && $k->kode === $kp->kode)>{{ $kp->nama }} ({{ $tgl($kp->mulai) }})</option>
                @endforeach
            </select>
        </form>
        @endif
    </div>

@if($galat)
    <div class="kora-alert">{{ $galat }}</div>
@elseif(!$k)
    <div class="surf-card p-4 kora-note">Belum ada kampanye boost tercatat.</div>
@else
    @php
        $t = $ringkas['total'];
        $tg = $ringkas['target'];
        $corong = $ringkas['corong'];
        $diundang = $corong[0][1];
        $videoTarget = $tg ? (int) $tg->video : (int) $t->video;
        $sisaHari = $k->sampai ? now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($k->sampai), false) : null;
    @endphp

    <div class="ts-body-sm mb-3" style="color:var(--md-on-surface-var);">
        {{ $tgl($k->mulai, 'D MMM YYYY') }}@if($k->sampai) – {{ $tgl($k->sampai, 'D MMM YYYY') }}@endif
        @if($k->komisi) · komisi {{ $k->komisi }}@endif
        @if($sisaHari !== null && $sisaHari >= 0) · sisa {{ $sisaHari }} hari @endif
    </div>

    {{-- KPI --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3"><div class="kora-kpi">
            <div class="i"><i class="bi bi-camera-video"></i></div>
            <div class="v">{{ $angka($videoTarget) }}@if($k->target_video)<span style="font-size:1rem;font-weight:500;color:var(--md-on-surface-var);"> / {{ $angka($k->target_video) }}</span>@endif</div>
            <div class="l">
                @if($tg) Video dari semua sampel produk boost sejak {{ $tgl($k->mulai) }} ({{ $angka($tg->sampel) }} sampel)
                @else Video dari kreator yang diundang @endif
            </div>
            @if($k->target_video)
                <div class="target-bar" role="progressbar" aria-valuenow="{{ $videoTarget }}" aria-valuemin="0" aria-valuemax="{{ $k->target_video }}">
                    <i style="width:{{ min(100, round($videoTarget * 100 / max(1, $k->target_video))) }}%"></i></div>
            @endif
        </div></div>
        <div class="col-6 col-lg-3"><div class="kora-kpi">
            <div class="i"><i class="bi bi-send"></i></div>
            <div class="v">{{ $angka($diundang) }}</div>
            <div class="l">Kreator diundang ({{ $angka($ringkas['status']['belum_dikirim'] ?? 0) }} masih antre)</div>
        </div></div>
        <div class="col-6 col-lg-3"><div class="kora-kpi">
            <div class="i"><i class="bi bi-hand-index"></i></div>
            <div class="v">{{ $angka($corong[1][1]) }}</div>
            <div class="l">Mengajukan sampel ({{ $diundang ? round($corong[1][1] * 100 / $diundang) : 0 }}% dari yang diundang)</div>
        </div></div>
        <div class="col-6 col-lg-3"><div class="kora-kpi">
            <div class="i"><i class="bi bi-cash-coin"></i></div>
            <div class="v">{{ $rp($t->gmv) }}</div>
            <div class="l">Hasil penjualan dari kreator boost ({{ $angka($t->video) }} video, {{ $angka($t->orders) }} order)</div>
        </div></div>
    </div>

    <div class="row g-3 mb-3">
        {{-- Corong --}}
        <div class="col-lg-5"><div class="surf-card p-4 h-100">
            <div class="sec-title mb-1">Corong undangan</div>
            <div class="sec-sub mb-3">Dari kreator yang sudah diundang.</div>
            <div class="funnel">
                @foreach($corong as [$nama, $n])
                    <div>{{ $nama }}</div>
                    <div class="bar"><i style="width:{{ $diundang ? max(1, round($n * 100 / $diundang)) : 0 }}%"></i></div>
                    <div class="n">{{ $angka($n) }}</div>
                @endforeach
            </div>
            <p class="kora-note mt-3 mb-0">
                Pengajuan dihitung kalau tercatat KORA setelah undangan, di toko yang sama. Video = terbesar dari isian tim,
                video laku, dan status selesai TikTok.
            </p>
        </div></div>

        {{-- Per produk --}}
        <div class="col-lg-7"><div class="surf-card p-4 h-100">
            <div class="sec-title mb-1">Per produk</div>
            <div class="sec-sub mb-3">"Semua sampel" = semua sampel produk ini sejak {{ $tgl($k->mulai) }}, termasuk yang bukan dari undangan.</div>
            <div class="table-responsive">
            <table class="kora-table th-lipat">
                <thead><tr><th>Produk</th><th class="num">Diundang</th><th class="num">Antre</th><th class="num">Mengajukan</th>
                    <th class="num">ACC</th><th class="num">Video (undangan)</th><th class="num">Semua sampel</th><th class="num">Semua video</th></tr></thead>
                <tbody>
                @forelse($produk as $p)
                    <tr>
                        <td><strong>{{ $p->sku }}</strong>@if($p->judul)<div class="produk-judul ts-body-sm" title="{{ $p->judul }}">{{ $p->judul }}</div>@endif
                            @if(in_array($p->sku, $belumPeta))<div class="ts-body-sm" style="color:#8C1D18;">belum terhubung ke produk TikTok</div>@endif</td>
                        <td class="num"><a href="{{ $tautan(['sku' => $p->sku, 'status' => null, 'page' => null]) }}">{{ $angka($p->diundang) }}</a></td>
                        <td class="num">{{ $angka($p->antre) }}</td>
                        <td class="num">{{ $angka($p->mengajukan) }}</td>
                        <td class="num">{{ $angka($p->acc) }}</td>
                        <td class="num">{{ $angka($p->video) }}</td>
                        <td class="num">{{ $p->semua_sampel === null ? '–' : $angka($p->semua_sampel) }}</td>
                        <td class="num">{{ $p->semua_video === null ? '–' : $angka($p->semua_video) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="kora-note">Belum ada data produk.</td></tr>
                @endforelse
                </tbody>
            </table>
            </div>
            @if($belumPeta)
                <p class="kora-note mt-3 mb-0" style="color:#8C1D18;">
                    {{ implode(', ', $belumPeta) }}: belum terhubung ke produk TikTok, jadi sampel & videonya belum terhitung.
                    Tim perlu menghubungkan SKU ini ke listing TikTok di dashboard produk.
                </p>
            @endif
        </div></div>
    </div>

    {{-- Daftar kreator --}}
    <div class="surf-card p-4 mb-3">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
            <div>
                <div class="sec-title mb-1">Kreator</div>
                <div class="sec-sub">{{ $angka($daftar['total']) }} kreator{{ $filter['status'] ? '' : ' (tanpa yang masih antre)' }}.</div>
            </div>
            <form method="GET" action="{{ route('admin.kora.boost') }}" class="kora-filter d-flex flex-wrap align-items-center gap-2">
                <input type="hidden" name="kampanye" value="{{ $k->kode }}">
                <select name="toko" aria-label="Toko" onchange="this.form.submit()">
                    <option value="">Semua toko</option>
                    @foreach($daftarToko as $slug => $nama)
                        <option value="{{ $slug }}" @selected($filter['toko'] === $slug)>{{ $nama }}</option>
                    @endforeach
                </select>
                <select name="sku" aria-label="Produk" onchange="this.form.submit()">
                    <option value="">Semua produk</option>
                    @foreach($produk as $p)
                        <option value="{{ $p->sku }}" @selected($filter['sku'] === $p->sku)>{{ $p->sku }}</option>
                    @endforeach
                </select>
                <select name="status" aria-label="Status" onchange="this.form.submit()">
                    <option value="">Semua status (sudah dikirim)</option>
                    @foreach($label as $kode => [$nama, $cls])
                        <option value="{{ $kode }}" @selected($filter['status'] === $kode)>{{ $nama }} ({{ $angka($ringkas['status'][$kode] ?? 0) }})</option>
                    @endforeach
                </select>
                <input type="search" name="cari" value="{{ $filter['cari'] }}" placeholder="Cari @kreator" aria-label="Cari kreator">
            </form>
        </div>
        <div class="table-responsive">
        <table class="kora-table">
            <thead><tr><th>Kreator</th><th>Toko</th><th>Produk di-boost</th><th>Status</th><th>Diundang</th>
                <th>Mengajukan</th><th>ACC</th><th class="num">Video</th><th class="num">Hasil (GMV)</th><th class="num">Order</th></tr></thead>
            <tbody>
            @forelse($daftar['baris'] as $r)
                @php [$lbl, $cls] = $label[$r->status] ?? [$r->status, 'b-kreator']; @endphp
                <tr>
                    <td><a href="{{ 'https://www.tiktok.com/@' . $r->username }}" target="_blank" rel="noopener">{{ '@' . $r->username }}</a>
                        @if($r->gmv_sebelum)<div class="ts-body-sm" style="color:var(--md-on-surface-var);" title="Penjualan produk ini sebelum diundang">sebelumnya {{ $rp($r->gmv_sebelum) }}</div>@endif</td>
                    <td>{{ $daftarToko[$r->store_slug] ?? $r->store_slug }}</td>
                    <td><strong>{{ $r->sku ?? '' }}</strong>@if($r->product_title)<div class="produk-judul ts-body-sm" title="{{ $r->product_title }}">{{ $r->product_title }}</div>@endif</td>
                    <td><span class="kora-badge {{ $cls }}" @if($r->request_state) title="{{ $r->request_state }}" @endif>{{ $lbl }}</span>
                        @if(!empty($r->ket) && $r->ket !== $lbl)<div class="ts-body-sm" style="color:var(--md-on-surface-var);">{{ $r->ket }}</div>@endif
                        @if(!empty($r->produk_lain))<div class="ts-body-sm produk-judul" style="color:#8A4B00;" title="{{ $r->produk_lain }}">Mengajukan produk lain: {{ $r->produk_lain }}</div>@endif</td>
                    <td class="ts-body-sm">{{ $tgl($r->diundang_at, 'D MMM, HH.mm') }}</td>
                    <td class="ts-body-sm">{{ $tgl($r->mengajukan_at) }}</td>
                    <td class="ts-body-sm">{{ $tgl($r->acc_at) }}</td>
                    <td class="num">{{ $r->video ? $angka($r->video) : '–' }}</td>
                    <td class="num">{{ $r->gmv > 0 ? $rp($r->gmv) : '–' }}</td>
                    <td class="num">{{ $r->orders ? $angka($r->orders) : '–' }}</td>
                </tr>
            @empty
                <tr><td colspan="10" class="kora-note">Tidak ada kreator untuk filter ini.</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
        @php $halaman = max(1, (int) ceil($daftar['total'] / 25)); @endphp
        @if($halaman > 1)
            <div class="d-flex justify-content-between align-items-center mt-3 ts-body-sm">
                <span style="color:var(--md-on-surface-var);">Halaman {{ $page }} dari {{ $angka($halaman) }}</span>
                <span class="d-flex gap-2">
                    @if($page > 1)<a class="kora-chip" href="{{ $tautan(['page' => $page - 1]) }}">‹ Sebelumnya</a>@endif
                    @if($page < $halaman)<a class="kora-chip" href="{{ $tautan(['page' => $page + 1]) }}">Berikutnya ›</a>@endif
                </span>
            </div>
        @endif
    </div>

    <p class="kora-note">
        Data dari KORA, diperbarui tiap 30 menit{{ $t->diperbarui ? ' (terakhir ' . $tgl($t->diperbarui, 'D MMM, HH.mm') . ')' : '' }}.
        Video & penjualan dari data TikTok, biasanya tertinggal 1–2 hari.
    </p>
@endif
</div>
@endsection

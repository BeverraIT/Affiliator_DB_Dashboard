@extends('layouts.admin')

@section('title', 'KORA · Kreator Fit')

@php
    $rp = function ($n) {
        $n = (float) $n;
        if ($n >= 1e9) return 'Rp' . number_format($n / 1e9, 2, ',', '.') . ' M';
        if ($n >= 1e6) return 'Rp' . number_format($n / 1e6, 1, ',', '.') . ' jt';
        if ($n >= 1e3) return 'Rp' . number_format($n / 1e3, 0, ',', '.') . ' rb';
        return 'Rp' . number_format($n, 0, ',', '.');
    };
    $angka  = fn ($n) => number_format((float) $n, 0, ',', '.');
    $persen = fn ($n) => $n === null ? '–' : $n . '%';
    $kali   = fn ($n) => $n === null ? '–' : number_format($n, 1, ',', '.') . 'x';
    $tautan = fn (array $ubah) => route('admin.kora.fit', array_filter(array_merge(['toko' => $toko], $ubah),
        fn ($v) => $v !== null && $v !== ''));
    $keputusan = ['ok' => ['OK', 'b-deal'], 'no' => ['NO', 'b-kreator'], 'pending' => ['Menunggu tim', 'b-tim']];
@endphp

@push('styles')
    @include('admin.kora._gaya')
@endpush

@section('content')
<div class="py-4">

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end mb-3 gap-3">
        <div>
            <p class="ts-label-sm mb-1" style="color:var(--md-on-surface-var);text-transform:uppercase;">KORA</p>
            <h1 class="ts-headline-sm mb-1" style="color:var(--md-on-surface);">Kreator Fit</h1>
            <p class="ts-body-sm mb-0" style="color:var(--md-on-surface-var);">
                Histori kolaborasi sebagai rujukan: kreator yang sudah pernah menjual produk kita. Hanya baca, data dari database KORA.
            </p>
        </div>
        <form method="GET" action="{{ route('admin.kora.fit') }}" class="kora-filter d-flex align-items-center gap-2">
            <label for="fit-toko" class="ts-body-sm" style="color:var(--md-on-surface-var);">Toko</label>
            <select id="fit-toko" name="toko" onchange="this.form.submit()">
                <option value="">Semua toko</option>
                @foreach($daftarToko as $slug => $nama)
                    <option value="{{ $slug }}" @selected($toko === $slug)>{{ $nama }}</option>
                @endforeach
            </select>
        </form>
    </div>

@if($galat)
    <div class="kora-alert">{{ $galat }}</div>
@else
    @php $f = $uji['fit']; $t = $uji['tidak']; $s = $uji['semua']; @endphp

    {{-- KPI --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3"><div class="kora-kpi">
            <div class="i"><i class="bi bi-bullseye"></i></div>
            <div class="v">{{ $persen($f['pct_laku']) }}</div>
            <div class="l">Sampel laku, kreator fit (tidak fit {{ $persen($t['pct_laku']) }})</div>
        </div></div>
        <div class="col-6 col-lg-3"><div class="kora-kpi">
            <div class="i"><i class="bi bi-cash-coin"></i></div>
            <div class="v">{{ $kali($f['kali']) }}</div>
            <div class="l">GMV ÷ nilai sampel, kreator fit (tidak fit {{ $kali($t['kali']) }})</div>
        </div></div>
        <div class="col-6 col-lg-3"><div class="kora-kpi">
            <div class="i"><i class="bi bi-pie-chart"></i></div>
            <div class="v">{{ $s['n'] ? round($f['n'] * 100 / $s['n']) : 0 }}%</div>
            <div class="l">Porsi sampel ke kreator fit ({{ $s['gmv'] > 0 ? round($f['gmv'] * 100 / $s['gmv']) : 0 }}% GMV sampel)</div>
        </div></div>
        <div class="col-6 col-lg-3"><div class="kora-kpi">
            <div class="i"><i class="bi bi-people"></i></div>
            <div class="v">{{ $angka($terbukti['total']) }}</div>
            <div class="l">Kreator terbukti menjual, belum dapat sampel</div>
        </div></div>
    </div>

    {{-- Uji mundur --}}
    <div class="surf-card p-4 mb-3">
        <div class="sec-title mb-1">Uji mundur: fit vs screening</div>
        <div class="sec-sub mb-3">
            Sampel matang {{ \App\Services\KoraFit::UMUR_MIN }}–{{ \App\Services\KoraFit::UMUR_MAKS }} hari.
            "Fit" = pernah menjual produk kita <em>sebelum</em> sampel dimulai (data video sejak Agu 2026).
            {{ $toko ? $daftarToko[$toko] : 'Semua toko' }}.
        </div>
        <div class="table-responsive">
        <table class="kora-table">
            <thead><tr><th>Kelompok</th><th class="num">Sampel</th><th class="num">% laku</th>
                <th class="num">GMV / sampel</th><th class="num">GMV ÷ nilai sampel</th><th class="num">GMV</th></tr></thead>
            <tbody>
            @foreach([['Kreator fit', $f, true], ['Belum pernah jual produk kita', $t, false], ['Semua sampel matang', $s, false]] as [$label, $x, $sorot])
                <tr class="{{ $sorot ? 'sorot' : '' }}">
                    <td><strong>{{ $label }}</strong></td>
                    <td class="num">{{ $angka($x['n']) }}</td>
                    <td class="num">{{ $persen($x['pct_laku']) }}</td>
                    <td class="num">{{ $x['per_sampel'] === null ? '–' : $rp($x['per_sampel']) }}</td>
                    <td class="num">{{ $kali($x['kali']) }}</td>
                    <td class="num">{{ $rp($x['gmv']) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        </div>

        @if($uji['silang'])
            <div class="sec-title mt-4 mb-1" style="font-size:.875rem;">Dibandingkan batas GMV 30 hari (≥ Rp10 jt)</div>
            <div class="sec-sub mb-2">Hanya sampel yang punya data screening KORA.</div>
            <div class="table-responsive">
            <table class="kora-table">
                <thead><tr><th>Batas GMV</th><th>Fit</th><th class="num">Sampel</th><th class="num">% laku</th>
                    <th class="num">GMV / sampel</th><th class="num">GMV ÷ nilai sampel</th></tr></thead>
                <tbody>
                @foreach(['1:1' => ['Lolos', 'Ya'], '1:0' => ['Lolos', 'Tidak'], '0:1' => ['Gagal', 'Ya'], '0:0' => ['Gagal', 'Tidak']] as $k => [$g, $fitLabel])
                    @php $x = $uji['silang'][$k] ?? null; @endphp
                    @if($x)
                        <tr class="{{ $k === '0:1' ? 'sorot' : '' }}">
                            <td>{{ $g }}</td><td>{{ $fitLabel }}</td>
                            <td class="num">{{ $angka($x['n']) }}</td>
                            <td class="num">{{ $persen($x['pct_laku']) }}</td>
                            <td class="num">{{ $x['per_sampel'] === null ? '–' : $rp($x['per_sampel']) }}</td>
                            <td class="num">{{ $kali($x['kali']) }}</td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
            </div>
        @endif
        <p class="kora-note mt-3 mb-0">
            Sejak 10 Nov, kreator fit yang gagal screening tidak lagi ditolak otomatis: KORA mengirim kartu "Kreator fit" ke tim.
            Jumlah sampel per kelompok masih kecil; angka akan stabil seiring data bertambah.
        </p>
    </div>

    {{-- Kartu fit ke tim --}}
    <div class="surf-card p-4 mb-3">
        <div class="sec-title mb-1">Kartu "Kreator fit" ke tim</div>
        <div class="sec-sub mb-3">Kreator yang gagal screening tapi pernah menjual produk kita (aturan mulai 10 Nov).</div>
        <div class="table-responsive">
        <table class="kora-table">
            <thead><tr><th>#</th><th>Kreator</th><th>Toko</th><th>Keputusan tim</th><th>Dikirim</th></tr></thead>
            <tbody>
            @forelse($kartu as $k)
                @php [$lbl, $cls] = $keputusan[$k->decision] ?? [$k->decision, 'b-kreator']; @endphp
                <tr>
                    <td>{{ $k->id }}</td>
                    <td><a href="{{ "https://www.tiktok.com/@" . $k->username }}" target="_blank" rel="noopener">{{ "@" . $k->username }}</a></td>
                    <td>{{ $daftarToko[$k->store_slug] ?? $k->store_slug }}</td>
                    <td><span class="kora-badge {{ $cls }}">{{ $lbl }}</span></td>
                    <td class="ts-body-sm">{{ \Carbon\Carbon::parse($k->created_at)->locale('id')->isoFormat('D MMM, HH.mm') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="kora-note">Belum ada kartu kreator fit.</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
    </div>

    {{-- Kreator terbukti --}}
    <div class="surf-card p-4 mb-3">
        <div class="sec-title mb-1">Kreator terbukti, belum pernah dapat sampel</div>
        <div class="sec-sub mb-3">
            Sudah menjual produk kita (data video 14 minggu terakhir) tanpa sampel tercatat. Kandidat undangan aktif, urut GMV terbesar.
            Sampel sebelum Okt 2026 belum tercatat, jadi sebagian mungkin pernah dapat sampel dari tim.
        </div>
        <div class="table-responsive">
        <table class="kora-table">
            <thead><tr><th>Kreator</th><th>Toko</th><th class="num">GMV</th><th class="num">Order</th>
                <th class="num">Video</th><th class="num">Produk</th><th>Terakhir jualan</th></tr></thead>
            <tbody>
            @forelse($terbukti['baris'] as $k)
                <tr>
                    <td><a href="{{ "https://www.tiktok.com/@" . $k->username }}" target="_blank" rel="noopener">{{ "@" . $k->username }}</a></td>
                    <td>{{ $daftarToko[$k->store_slug] ?? $k->store_slug }}</td>
                    <td class="num">{{ $rp($k->gmv) }}</td>
                    <td class="num">{{ $angka($k->orders) }}</td>
                    <td class="num">{{ $angka($k->video) }}</td>
                    <td class="num">{{ $angka($k->produk) }}</td>
                    <td class="ts-body-sm">{{ $k->terakhir ? \Carbon\Carbon::parse($k->terakhir)->locale('id')->isoFormat('D MMM YYYY') : '–' }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="kora-note">Tidak ada data.</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
        @php $halaman = max(1, (int) ceil($terbukti['total'] / 20)); @endphp
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

    <p class="kora-note">Data dari KORA. Uji mundur dan daftar kreator di-cache 1 jam, kartu 5 menit.</p>
@endif
</div>
@endsection

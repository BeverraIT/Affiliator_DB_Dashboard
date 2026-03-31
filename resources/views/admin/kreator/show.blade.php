@extends('layouts.admin')

@section('title', 'Detail Kreator')

@section('content')
@php
    $genderLabel = match($creator->genderpengikut) {
        'perempuan' => 'Perempuan',
        'laki-laki' => 'Laki-laki',
        'seimbang'  => 'Seimbang',
        default     => '-',
    };

    $channelLabel = match($creator->channel) {
        'video'    => 'Video',
        'live'     => 'Live',
        'kartu produk' => 'Kartu Produk',
        default    => '-',
    };

    $waRaw = preg_replace('/\D+/', '', (string) ($creator->nomerwa ?? ''));
    $waNumber = $waRaw
        ? (str_starts_with($waRaw, '0') ? '62'.substr($waRaw, 1) : $waRaw)
        : null;
    $tiktokUrl = 'https://www.tiktok.com/@' . rawurlencode(ltrim((string) $creator->username, '@'));
@endphp

<style>
    .detail-wrap { max-width: 860px; }
    .stat-card { border: 1px solid rgba(0,0,0,.06); }
    .pill {
        display:inline-flex; align-items:center;
        padding:.25rem .6rem; border-radius:999px;
        border:1px solid rgba(0,0,0,.08);
        background:#f8f9fa; color:#111;
        font-size:.875rem;
        margin:.15rem .25rem 0 0;
    }
    .pill-muted { color:#6c757d; background:#fafafa; }
</style>

<div class="d-flex justify-content-between align-items-start gap-3 mb-3">
    <div>
        <h1 class="h4 mb-1">Detail Kreator</h1>
    </div>

    <div class="d-flex gap-2">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Kembali</a>
        <a href="{{ route('admin.kreator.edit', $creator->id) }}" class="btn btn-primary">Edit</a>
    </div>
</div>

<div class="detail-wrap">
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    {{-- USERNAME --}}
                    <div class="h5 mb-1">
                        <a href="{{ $tiktokUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-decoration-none text-dark">
                            {{ $creator->username }}
                        </a>
                        @if((int)$creator->top_creator === 1)
                            <span class="text-warning ms-1" 
                                style="font-size: 1.2rem; line-height: 0;" 
                                title="Top Creator">★</span>
                        @endif
                    </div>

                    {{-- CHANNEL PENJUALAN & GENDER --}}
                    <div class="text-secondary small">
                        Channel Penjualan: <span class="fw-semibold">{{ $channelLabel }}</span>
                        <span class="mx-2">·</span>
                        Gender Pengikut: <span class="fw-semibold">{{ $genderLabel }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        {{-- GMV --}}
        <div class="col-12 col-md-4">
            <div class="card stat-card shadow-sm">
                <div class="card-body p-3">
                    <div class="text-secondary small mb-1">GMV</div>
                    <div class="h5 mb-0">Rp {{ number_format((int) $creator->gmv, 0, ',', '.') }}</div>
                    <div class="text-secondary small mt-1">Total nilai penjualan</div>
                </div>
            </div>
        </div>

        {{-- ITEM TERJUAL --}}
        <div class="col-12 col-md-4">
            <div class="card stat-card shadow-sm">
                <div class="card-body p-3">
                    <div class="text-secondary small mb-1">Item Terjual</div>
                    <div class="h5 mb-0">{{ number_format((int) $creator->itemsterjual, 0, ',', '.') }}</div>
                    <div class="text-secondary small mt-1">Jumlah produk</div>
                </div>
            </div>
        </div>

        {{-- WA --}}
        <div class="col-12 col-md-4">
            <div class="card stat-card shadow-sm">
                <div class="card-body p-3">
                    <div class="text-secondary small mb-1">Kontak</div>
                    <div class="fw-semibold">{{ $creator->namawa ?: '-' }}</div>
                    <div class="text-secondary small">
                        {{ $creator->nomerwa ?: '-' }}
                        @if($waNumber)
                            <span class="mx-1 text-secondary">·</span>
                            <button type="button"
                                class="btn btn-sm btn-outline-secondary rounded-circle d-inline-flex align-items-center justify-content-center"
                                style="width:30px;height:30px;"
                                data-copy="{{ $creator->nomerwa }}"
                                onclick="copyIdPesanan(this)"
                                aria-label="Salin Nomor WA" title="Salin Nomor WA">
                                <svg viewBox="0 0 20 20" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M8 3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1V3z"/>
                                    <path d="M6 3H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1h-2"/>
                                </svg>
                            </button>                            
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- UMUR --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-4">
            <div class="fw-semibold mb-2">Umur Pengikut</div>
            <div>
                @forelse($creator->ages as $age)
                    <span class="pill">{{ $age->name }}</span>
                @empty
                    <span class="pill pill-muted">Belum diisi</span>
                @endforelse
            </div>
        </div>
    </div>

    {{-- KATEGORI --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-4">
            <div class="fw-semibold mb-2">Kategori</div>
            <div>
                @forelse($creator->categories as $cat)
                    <span class="pill">{{ $cat->name }}</span>
                @empty
                    <span class="pill pill-muted">Belum ada kategori</span>
                @endforelse
            </div>
        </div>
    </div>

    {{-- KONTEN STYLE --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-4">
            <div class="fw-semibold mb-2">Konten Style</div>
            <div>
                @forelse($creator->contentStyles as $style)
                    <span class="pill">{{ $style->name }}</span>
                @empty
                    <span class="pill pill-muted">Belum ada konten style</span>
                @endforelse
            </div>
        </div>
    </div>

    {{-- HISTORI --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-4">
            <div class="fw-semibold mb-3">Sampel Dikirim </div>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Tanggal Deal</th>
                            <th scope="col">SKU Utama</th>
                            <th scope="col">Toko</th>
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
                                <td colspan="3" class="text-center">Data tidak ditemukan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

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
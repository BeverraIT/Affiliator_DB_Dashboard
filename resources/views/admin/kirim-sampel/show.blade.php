@extends('layouts.admin')

@section('title', 'Detail Pengiriman')

@section('content')
    <div class="container" style="max-width: 1000px;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h1 class="h4 fw-bold mb-0 text-dark">Detail Pengiriman</h1>
            <div class="d-flex gap-2">
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 rounded-pill px-4">
                    <span>Kembali</span>
                </a>
                <a href="{{ route('admin.kirim-sampel.edit', $sendSample->id) }}" class="btn btn-primary d-inline-flex align-items-center gap-2 rounded-pill px-4 shadow-sm">
                    <span>Edit Data</span>
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div style="height: 4px; background: linear-gradient(90deg, #38bdf8, #6366f1, #22c55e);"></div>
            <div class="card-body p-4">
                <div class="card bg-light border-0 rounded-4 mb-2">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary bg-gradient text-white d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 52px; height: 52px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="24" height="24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        </div>
                        <div>
                            <div class="small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 1px;">Kreator</div>
                            <h5 class="fw-bold text-dark mb-1">
                                {{ $sendSample->creator->username ?? '-' }}
                            </h5>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="badge bg-warning bg-opacity-10 text-warning-emphasis border border-warning border-opacity-25 fw-bold font-monospace rounded-pill px-3">
                                    📦 {{ $sendSample->product->sku ?? 'Produk Dihapus' }}
                                </span>
                                @if($sendSample->status_chat > 0)
                                    <span class="badge bg-success bg-opacity-10 text-success-emphasis border border-success border-opacity-25 fw-bold font-monospace rounded-pill px-3">
                                        💬 Sudah Chat
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-2">
                    <div class="col-12 col-md-4">
                        <div class="card h-100 border bg-white shadow-sm">
                            <div class="card-body p-2">
                                <div class="small text-muted mb-1">Jumlah Sampel</div>
                                <div class="h5 fw-bold mb-1">{{ number_format($sendSample->qty) }} Unit</div>
                                <div class="small text-muted">@ Rp {{ number_format($sendSample->product->harga ?? 0, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="card h-100 border bg-white shadow-sm">
                            <div class="card-body p-2">
                                <div class="small text-muted mb-1">Total Nilai</div>
                                <div class="h5 fw-bold text-dark mb-1">Rp {{ number_format($sendSample->total_nilai, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="card h-100 border bg-white shadow-sm">
                            <div class="card-body p-2">
                                <div class="small text-muted mb-1">ID Pesanan</div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="h5 fw-bold text-dark mb-1">
                                        {{ $sendSample->id_pesanan ?? ($sendSample->id_pesanan ?? '-') }}
                                    </div>
                                    @if($sendSample->id_pesanan || $sendSample->id_pesanan)
                                        <button type="button"
                                                class="btn btn-sm btn-outline-secondary rounded-circle d-inline-flex align-items-center justify-content-center"
                                                style="width:30px;height:30px;"
                                                data-copy="{{ $sendSample->id_pesanan }}"
                                                onclick="copyIdPesanan(this)"
                                                aria-label="Salin ID Pesanan" title="Salin ID Pesanan">
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

                <div class="row g-4">
                    <div class="col-12 col-lg-6">
                        <div class="card border h-100">
                            <div class="card-body">
                                <h6 class="text-uppercase text-muted fw-bold mb-2 d-flex align-items-center gap-2" style="font-size: 0.75rem; letter-spacing: 1px;">
                                    <span class="d-inline-block rounded-circle bg-primary" style="width: 8px; height: 8px;"></span>
                                    Toko & PIC
                                </h6>
                                
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center border-bottom-dashed">
                                        <span class="text-muted small">Toko</span>
                                        <span class="fw-medium text-dark">{{ $sendSample->store->name ?? '-' }}</span>
                                    </li>
                                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center border-0">
                                        <span class="text-muted small">APPROVE</span>
                                        <span class="fw-medium text-dark">{{ $sendSample->approver->name ?? '-' }}</span>
                                    </li>
                                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center border-0">
                                        <span class="text-muted small">PIC</span>
                                        <span class="fw-medium text-dark">{{ $sendSample->user->name ?? '-' }}</span>
                                    </li>
                                </ul>
                                <h6 class="text-uppercase text-muted fw-bold mb-2 d-flex align-items-center gap-2" style="font-size: 0.75rem; letter-spacing: 1px;">
                                    <span class="d-inline-block rounded-circle bg-primary" style="width: 8px; height: 8px;"></span>
                                    Perjanjian
                                </h6>
                                <div class="d-flex gap-2">
                                    <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 fw-medium">
                                        Live {{ $sendSample->live_dibuat ?? '0' }}/{{ $sendSample->perjanjian_livestream ?? '0' }}
                                    </span>
                                    <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 fw-medium">
                                        Video {{ $sendSample->video_dibuat ?? '0' }}/{{ $sendSample->perjanjian_video ?? '0' }}
                                    </span>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- Timeline & Perjanjian --}}
                    <div class="col-12 col-lg-6">
                        <div class="card border h-100">
                            <div class="card-body">
                                <h6 class="text-uppercase text-muted fw-bold mb-2 d-flex align-items-center gap-2" style="font-size: 0.75rem; letter-spacing: 1px;">
                                    <span class="d-inline-block rounded-circle bg-primary" style="width: 8px; height: 8px;"></span>
                                    Timeline
                                </h6>
                                <ul class="list-group list-group-flush mb-2">
                                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                        <span class="text-muted small">Tanggal Deal</span>
                                        <span class="fw-medium">{{ optional($sendSample->deal_date)->translatedFormat('d F Y') ?? '-' }}</span>
                                    </li>
                                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                        <span class="text-muted small">Tanggal Kirim</span>
                                        <span class="fw-medium">{{ optional($sendSample->tanggal_kirim)->translatedFormat('d F Y') ?? '-' }}</span>
                                    </li>
                                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                        <span class="text-muted small">Estimasi Sampai</span>
                                        <span class="fw-medium">{{ optional($sendSample->estimasi_sampai)->translatedFormat('d F Y') ?? '-' }}</span>
                                    </li>
                                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center border-0">
                                        <span class="text-muted small">Tanggal Aktual</span>
                                        <span class="fw-bold {{ $sendSample->tanggal_aktual ? 'text-success' : '' }}">
                                            {{ optional($sendSample->tanggal_aktual)->translatedFormat('d F Y') ?? '-' }}
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
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
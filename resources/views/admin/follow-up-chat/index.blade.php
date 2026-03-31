@extends('layouts.admin')

@section('title', 'Follow Up Konten')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h4 mb-0">Follow Up Chat</h1>
        </div>

        <div class="d-flex align-items-center">
            <span class="badge bg-white text-dark border shadow-sm py-2 px-3 rounded-pill d-flex align-items-center gap-2">
                <span class="d-inline-block rounded-circle bg-warning" style="width: 10px; height: 10px;"></span>
                <span>Perlu Follow Up:</span>
                <strong class="text-dark">{{ $items->total() }}</strong>
            </span>
        </div>
    </div>

    {{-- FILTER & SEARCH --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.followup.chat.index') }}" class="row g-2 align-items-end">
                {{-- START DATE --}}
                <div class="col-md-2 col-6">
                    <label class="form-label small text-muted mb-1">Dari Tanggal</label>
                    <input type="date" 
                           name="start_date" 
                           class="form-control form-control-sm" 
                           value="{{ request('start_date') }}">
                </div>

                {{-- END DATE --}}
                <div class="col-md-2 col-6">
                    <label class="form-label small text-muted mb-1">Sampai Tanggal</label>
                    <input type="date" 
                           name="end_date" 
                           class="form-control form-control-sm" 
                           value="{{ request('end_date') }}">
                </div>

                {{-- SKU UTAMA --}}
                <div class="col-md-3 col-12">
                    <label class="form-label small text-muted mb-1">Filter SKU Utama</label>
                    <select name="sku_utama" class="form-select form-select-sm select2-init" style="width: 100%;">
                        <option value="">Semua SKU Utama</option>
                        @foreach($skuUtamaOptions as $prod)
                            <option value="{{ $prod->sku_utama }}" {{ $sku_utama == $prod->sku_utama ? 'selected' : '' }}>
                                {{ $prod->sku_utama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- SEARCH --}}
                <div class="col-md col-12">
                    <label class="form-label small text-muted mb-1">Pencarian</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white text-muted border-end-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                            </svg>
                        </span>
                        <input type="text" name="q" value="{{ $search ?? '' }}" class="form-control border-start-0 ps-0"
                               placeholder="Cari Kreator, Toko, Produk...">
                    </div>
                </div>

                {{-- AKSI --}}
                <div class="col-auto d-flex gap-2">
                    <button class="btn btn-sm btn-primary px-3" type="submit">Filter</button>
                    
                    @if(!empty($search) || !empty($sku_utama) || request('start_date') || request('end_date'))
                        <a href="{{ route('admin.followup.chat.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success py-2 mb-3">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger py-2 mb-3">{{ session('error') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small table-sm">
                <thead class="table-light">
                    <tr>
                        <th class="py-3" style="width: 20%;">Kreator</th>
                        <th class="py-3" style="width: 20%;">Toko & PIC</th>
                        <th class="py-3" style="width: 20%;">Produk</th>
                        <th class="py-3 text-center" style="width: 15%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($items as $index => $item)
                    @php
                        $perjanjianVideo = (int) ($item->perjanjian_video ?? 0);
                        $perjanjianLive  = (int) ($item->perjanjian_livestream ?? 0);
                        $videoVal = (int) ($item->video_dibuat ?? 0);
                        $liveVal  = (int) ($item->live_dibuat ?? 0);
                        $videoComplete = $perjanjianVideo > 0 && $videoVal >= $perjanjianVideo;
                    @endphp

                    <tr>
                        <td>
                            <div class="fw-bold text-dark mb-1">
                                {{ optional($item->creator)->username ?? optional($item->creator)->name ?? '-' }}
                            </div>
                            @if(optional($item->creator)->namawa)
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-light text-secondary border fw-normal">
                                        <i class="bi bi-whatsapp me-1"></i> {{ $item->creator->namawa }}
                                    </span>
                                    <button class="btn btn-link p-0 text-secondary"
                                            onclick="copyToClipboard('{{ $item->creator->namawa }}', this)"
                                            title="Salin WA">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
                                            <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        </td>

                        <td>
                            <div class="fw-medium text-dark">
                                {{ optional($item->store)->name ?? '-' }}
                            </div>
                            @if($item->user)
                                <div class="mt-1">
                                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">
                                        PIC: {{ $item->user->name }}
                                    </span>
                                </div>
                            @else
                                <div class="mt-1">
                                    <span class="badge bg-light text-muted border">PIC: -</span>
                                </div>
                            @endif
                        </td>

                        <td>
                            <div class="fw-medium text-dark mb-1">
                                {{ optional($item->product)->sku ?? '-' }}
                            </div>
                            <div class="small text-secondary">
                                Qty: <strong>{{ $item->qty ?? 0 }}</strong>
                            </div>
                        </td>

                        <td class="text-center">
                            <form method="POST" action="{{ route('admin.followup.chat.chatStatus', $item->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success w-100 text-nowrap">
                                    <i class="bi bi-check2-all me-1"></i> Sudah Dichat
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center text-muted">
                                <span>Data tidak tersedia.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $items->links() }}
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select2-init').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Pilih SKU Utama...',
                allowClear: true
            });
        });

        function copyToClipboard(text, btnElement) {
            if (!text) return;
            navigator.clipboard.writeText(text).then(() => {
                const originalHtml = btnElement.innerHTML;
                btnElement.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="#198754" viewBox="0 0 16 16"><path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7.5 7.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6 10.793l7.146-7.147a.5.5 0 0 1 .708 0z"/></svg>`;
                setTimeout(() => { btnElement.innerHTML = originalHtml; }, 1000);
            }).catch(err => {
                alert('Gagal menyalin text.');
            });
        }
    </script>
@endpush
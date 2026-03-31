@extends('layouts.admin')

@section('title', 'Follow Up Pengiriman')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h4 mb-0">Follow Up Pengiriman</h1>
            <p class="text-secondary small mb-0">
                Daftar pengiriman yang belum sampai dan mendekati estimasi (H-3).
            </p>
        </div>
        
        <div class="d-flex align-items-center">
            <span class="badge bg-white text-dark border shadow-sm py-2 px-3 rounded-pill d-flex align-items-center gap-2">
                <span class="d-inline-block rounded-circle bg-warning" style="width: 10px; height: 10px;"></span>
                <span>Perlu Follow Up:</span>
                <strong class="text-dark">{{ $items->total() }}</strong>
            </span>
        </div>
    </div>

    {{-- SEARCH & FILTER --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.followup.pengiriman.index') }}" class="row g-2 align-items-end">
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
                            <option value="{{ $prod->sku_utama }}" {{ $selectedSkuUtama == $prod->sku_utama ? 'selected' : '' }}>
                                {{ $prod->sku_utama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- SEARCH --}}
                <div class="col-md col-12">
                    <label class="form-label small text-muted mb-1">Pencarian</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white text-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                            </svg>
                        </span>
                        <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control border-start-0 ps-0"
                               placeholder="Cari Kreator, Toko, atau ID Pesanan...">
                    </div>
                </div>

                {{-- BUTTON FILTER & RESET --}}
                <div class="col-auto d-flex gap-2">
                    <button class="btn btn-sm btn-primary px-3" type="submit">Filter</button>
                    @if(!empty($q) || !empty($selectedSkuUtama) || request('start_date') || request('end_date'))
                        <a href="{{ route('admin.followup.pengiriman.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success py-2 mb-3">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small table-sm">
                <thead class="table-light">
                <tr>
                    <th class="py-3" style="width: 20%;">Kreator/Toko/PIC</th>
                    <th class="py-3" style="width: 25%;">Produk</th>
                    <th class="py-3" style="width: 20%;">Estimasi & Status</th>
                    <th class="py-3" style="width: 15%;">ID Pesanan</th>
                    <th class="py-3 text-center" style="width: 15%;">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($items as $index => $item)
                    @php
                        $qty = (int) ($item->qty ?? 0);
                        $harga = (int) optional($item->product)->harga;
                        $total = $harga * $qty;
                        $estimasiDate = $item->estimasi_sampai ? \Carbon\Carbon::parse($item->estimasi_sampai) : null;
                        $kirimDate = $item->tanggal_kirim ? \Carbon\Carbon::parse($item->tanggal_kirim) : null;
                        $badgeClass = 'bg-secondary';
                        $badgeText = '-';

                        if ($estimasiDate) {
                            if ($today->gt($estimasiDate)) {
                                $days = $estimasiDate->diffInDays($today);
                                $badgeClass = 'bg-danger';
                                $badgeText = "Terlewat {$days} hari";
                            } elseif ($today->lt($estimasiDate)) {
                                $days = $today->diffInDays($estimasiDate);
                                $badgeClass = 'bg-warning text-dark';
                                $badgeText = "H-{$days} Menuju Estimasi";
                            } else {
                                $badgeClass = 'bg-info text-white';
                                $badgeText = "Hari H Estimasi";
                            }
                        }
                    @endphp

                    <tr>
                        <td>
                            <div class="fw-bold text-dark">
                                {{ optional($item->creator)->username ?? '-' }}
                            </div>
                            <div class="small text-muted">
                                <i class="bi bi-shop me-1"></i> {{ optional($item->store)->name ?? '-' }}
                            </div>
                            @if($item->user)
                                <div class="mt-1">
                                    <span class="badge bg-light text-secondary border fw-normal">
                                        PIC: {{ $item->user->name }}
                                    </span>
                                </div>
                            @endif
                        </td>

                        <td>
                            <div class="fw-medium text-dark mb-1">
                                {{ optional($item->product)->sku ?? 'SKU Tidak Ditemukan' }}
                            </div>
                            <div class="text-secondary" style="font-size: 0.8rem;">
                                {{ \Illuminate\Support\Str::limit(optional($item->product)->name, 30) }}
                            </div>
                            <div class="small text-muted mt-1">
                                Qty: <span class="fw-bold">{{ $qty }}</span>
                                @if($total > 0)
                                    &bull; Total: Rp {{ number_format($total, 0, ',', '.') }}
                                @endif
                            </div>
                        </td>

                        <td>
                            <div class="fw-bold text-dark">
                                {{ $estimasiDate ? $estimasiDate->format('d M Y') : '-' }}
                            </div>
                            <div class="mt-1">
                                <span class="badge {{ $badgeClass }} rounded-pill" style="font-size: 0.7rem;">
                                    {{ $badgeText }}
                                </span>
                            </div>
                            @if($kirimDate)
                                <div class="small text-muted mt-1" style="font-size: 0.75rem;">
                                    Kirim: {{ $kirimDate->format('d/m/y') }}
                                </div>
                            @endif
                        </td>

                        <td>
                            @if($item->id_pesanan)
                                <div class="d-flex align-items-center gap-2">
                                    <span class="font-monospace bg-light border px-2 py-1 rounded small text-dark">
                                        {{ $item->id_pesanan }}
                                    </span>
                                    <button type="button" 
                                            class="btn btn-link p-0 text-secondary" 
                                            onclick="copyToClipboard('{{ $item->id_pesanan }}', this)" 
                                            title="Salin ID Pesanan">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
                                            <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
                                        </svg>
                                    </button>
                                </div>
                            @else
                                <span class="badge bg-danger-subtle text-danger">Belum ada ID Pesanan</span>
                            @endif
                        </td>

                        <td class="text-center">
                            <form method="POST" action="{{ route('admin.followup.pengiriman.update', $item->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="d-flex flex-column align-items-center gap-2">
                                    <input type="date"
                                           name="tanggal_aktual"
                                           class="form-control form-control-sm text-center"
                                           style="max-width: 130px;"
                                           value="{{ old('tanggal_aktual', $today->toDateString()) }}"
                                           required>
                                    
                                    <button type="submit" class="btn btn-sm btn-primary w-100" style="max-width: 130px;">
                                        Selesai
                                    </button>
                                </div>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center text-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-filter mb-2" viewBox="0 0 16 16">
                                    <path d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/>
                                </svg>
                                <span>Data tidak tersedia.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        <div class="d-flex justify-content-center py-3">
             {{ $items->links() }}
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select2-init').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Pilih opsi...',
                allowClear: true
            });
        });

        function copyToClipboard(text, btnElement) {
            if (!text) return;

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => {
                    showToast("ID Pesanan disalin!");
                }).catch(err => {
                    console.warn('Modern copy failed, trying fallback...', err);
                    fallbackCopyTextToClipboard(text);
                });
            } else {
                fallbackCopyTextToClipboard(text);
            }
        }

        function fallbackCopyTextToClipboard(text) {
            var textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed";
            textArea.style.left = "-9999px";
            textArea.style.top = "0";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();

            try {
                var successful = document.execCommand('copy');
                if (successful) {
                    showToast("ID Pesanan disalin!");
                } else {
                    alert('Browser tidak mendukung copy otomatis. Silakan salin manual.');
                }
            } catch (err) {
                console.error('Fallback copy error', err);
                alert('Gagal menyalin ID Pesanan.');
            }

            document.body.removeChild(textArea);
        }

        function showToast(message) {
            let existingToast = document.getElementById('custom-toast');
            if(existingToast) existingToast.remove();

            let toastEl = document.createElement('div');
            toastEl.id = 'custom-toast';

            toastEl.className = 'fixed-bottom mb-4 text-center';
            toastEl.style.zIndex = '9999';
            toastEl.innerHTML = `
                <div class="d-inline-block bg-dark text-white px-3 py-2 rounded shadow" style="font-size: 0.9rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#20c997" class="bi bi-check-circle-fill me-2" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                    </svg>
                    ${message}
                </div>
            `;

            document.body.appendChild(toastEl);

            setTimeout(() => {
                toastEl.style.transition = 'opacity 0.5s';
                toastEl.style.opacity = '0';
                setTimeout(() => toastEl.remove(), 500);
            }, 2000);
        }
    </script>
@endpush
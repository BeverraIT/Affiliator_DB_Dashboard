@extends('layouts.admin')

@section('title', 'Kirim Sampel')

@section('content')
    @php
        use Illuminate\Support\Facades\Route;

        $hasShow    = Route::has('admin.kirim-sampel.show');
        $hasEdit    = Route::has('admin.kirim-sampel.edit');
        $hasDestroy = Route::has('admin.kirim-sampel.destroy');
        $q    = $q    ?? request('q');
        $sort = $sort ?? request('sort', 'deal_date');
        $dir  = $dir  ?? request('dir', 'desc');

        if (! function_exists('formatRupiahFull')) {
            function formatRupiahFull($value) {
                return 'Rp' . number_format((int) $value, 0, ',', '.');
            }
        }
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-0">Kirim Sampel</h1>
        </div>

        <div class="d-flex gap-2">
            {{-- BUTTON BULK DELETE --}}
            <button type="button" 
                    id="btnBulkDelete" 
                    class="btn btn-danger rounded-pill shadow-sm d-none align-items-center gap-2"
                    onclick="submitBulkDelete()">
                <svg viewBox="0 0 20 20" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 6h12M8 6V4h4v2M7 6v9a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V6" />
                </svg>
                <span class="small fw-bold">Hapus (<span id="countSelected">0</span>)</span>
            </button>

            {{-- BUTTON EXPORT --}}
            <a href="{{ route('admin.kirim-sampel.export', request()->all()) }}" 
                class="btn btn-success rounded-pill d-inline-flex align-items-center justify-content-center" 
                title="Export Excel" 
                data-bs-toggle="tooltip" 
                style="width: 44px; height: 44px;">

                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-file-earmark-excel-fill" viewBox="0 0 16 16">
                    <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0M9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1M5.884 6.68 8 9.219l2.116-2.54a.5.5 0 1 1 .768.641L8.651 10l2.233 2.68a.5.5 0 0 1-.768.64L8 10.781l-2.116 2.54a.5.5 0 0 1-.768-.641L7.349 10 5.116 7.32a.5.5 0 1 1 .768-.64"/>
                </svg>

            </a>

            {{-- BUTTON UPLOAD MASSAL --}}
            <button type="button" 
                class="btn btn-success shadow-sm rounded-circle d-inline-flex align-items-center justify-content-center"
                style="width:44px;height:44px;"
                data-bs-toggle="modal" 
                data-bs-target="#importModal"
                aria-label="Tambah Kirim Sampel Massal"
                title="Kirim Sampel Massal">

                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
                </svg>

            </button>

            {{-- BUTTON TAMBAH --}}
            <a href="{{ route('admin.kirim-sampel.create') }}"
            class="btn btn-primary rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm"
            style="width:44px;height:44px;"
            aria-label="Tambah kirim sampel" title="Tambah">
                <svg viewBox="0 0 20 20" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10 4v12M4 10h12" stroke-linecap="round"/>
                </svg>
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success py-2">{{ session('success') }}</div>
    @endif
    @if (session('delete_error'))
        <div class="alert alert-danger py-2">{{ session('delete_error') }}</div>
    @endif

    <form method="GET" action="{{ route('admin.kirim-sampel.index') }}" class="row g-2 align-items-center mb-3">
        <div class="col-12 col-md-5">
            <input type="text" name="q" value="{{ $q }}" class="form-control"
                placeholder="Cari Kreator, Toko, PIC, Produk...">
        </div>

        <input type="hidden" name="sort" value="{{ $sort }}">
        <input type="hidden" name="dir" value="{{ $dir }}">

        {{-- SEARCH --}}
        <div class="col-auto">
            <button type="submit" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </button>
        </div>

        {{-- FILTER --}}
        <div class="col-auto">
            <div class="dropdown">
                <button type="button" 
                        class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 dropdown-toggle" 
                        data-bs-toggle="dropdown" 
                        data-bs-auto-close="outside"
                        aria-expanded="false" 
                        title="Filter Data">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                    <span>Filter</span>
                </button>

                <div class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-0 mt-2" style="min-width: 320px;">
                    <div class="card border-0">
                        <div class="card-header bg-transparent border-bottom py-2">
                            <h6 class="mb-0 fw-bold text-secondary">Filter Data</h6>
                        </div>
                        
                        <div class="card-body p-3">
                            <div class="row g-2">
                                {{-- START DATE --}}
                                <div class="col-12 col-md-6">
                                    <label class="form-label small text-muted fw-bold text-uppercase">Dari Tanggal</label>
                                    <input type="date" name="start_date" class="form-control form-control-sm"
                                        value="{{ request('start_date') }}">
                                </div>

                                {{-- END DATE --}}
                                <div class="col-12 col-md-6">
                                    <label class="form-label small text-muted fw-bold text-uppercase">Sampai Tanggal</label>
                                    <input type="date" name="end_date" class="form-control form-control-sm"
                                        value="{{ request('end_date') }}">
                                </div>

                                {{-- STATUS ID PESANAN --}}
                                <div class="col-12 col-md-6">
                                    <label class="form-label small text-muted fw-bold text-uppercase">Status ID Pesanan</label>
                                    <select class="form-select form-select-sm" name="filter_id_pesanan">
                                        <option value="">Semua</option>
                                        <option value="filled" {{ request('filter_id_pesanan') == 'filled' ? 'selected' : '' }}>Sudah Input ID</option>
                                        <option value="empty" {{ request('filter_id_pesanan') == 'empty' ? 'selected' : '' }}>Belum Input ID</option>
                                    </select>
                                </div>

                                {{-- STATUS CHAT --}}
                                <div class="col-12 col-md-6">
                                    <label class="form-label small text-muted fw-bold text-uppercase">Status Chat</label>
                                    <select class="form-select form-select-sm" name="filter_status_chat">
                                        <option value="">Semua</option>
                                        <option value="filled" {{ request('filter_status_chat') == 'filled' ? 'selected' : '' }}>Sudah Chat</option>
                                        <option value="empty" {{ request('filter_status_chat') == 'empty' ? 'selected' : '' }}>Belum Chat</option>
                                    </select>
                                </div>

                                {{-- TOKO --}}
                                <div class="col-12 col-md-6">
                                    <label class="form-label small text-muted fw-bold text-uppercase">Toko</label>
                                    <select class="form-select form-select-sm select2-init" name="filter_stores[]" multiple="multiple" style="width: 100%">
                                        @foreach($optStores as $s)
                                            <option value="{{ $s->id }}" {{ in_array($s->id, $filter_stores) ? 'selected' : '' }}>
                                                {{ $s->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- PIC --}}
                                <div class="col-12 col-md-6">
                                    <label class="form-label small text-muted fw-bold text-uppercase">PIC</label>
                                    <select class="form-select form-select-sm select2-init" name="filter_pics[]" multiple="multiple" style="width: 100%">
                                        @foreach($optPics as $p)
                                            <option value="{{ $p->id }}" {{ in_array($p->id, $filter_pics) ? 'selected' : '' }}>
                                                {{ $p->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- SKU UTAMA --}}
                                <div class="col-12">
                                    <label class="form-label small text-muted fw-bold text-uppercase">SKU Utama</label>
                                    <select class="form-select form-select-sm select2-init" name="filter_sku[]" multiple="multiple" style="width: 100%">
                                        @foreach($optSku as $item)
                                            <option value="{{ $item->sku_utama }}" {{ in_array($item->sku_utama, $filter_sku) ? 'selected' : '' }}>
                                                {{ $item->sku_utama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- BUTTON RESET DAN TERAPKAN --}}
                        <div class="card-footer bg-light border-top p-2 d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.kirim-sampel.index', ['sort' => $sort, 'dir' => $dir]) }}" class="btn btn-sm btn-link text-decoration-none text-secondary">
                                Reset
                            </a>
                            <button type="submit" class="btn btn-sm btn-primary px-3">
                                Terapkan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(!empty($q) || !empty($filter_creators) || !empty($filter_stores) || !empty($filter_pics) || !empty($filter_sku))
            <div class="col-auto">
                <a class="btn btn-outline-secondary d-inline-flex align-items-center gap-2"
                   href="{{ route('admin.kirim-sampel.index', ['sort' => $sort, 'dir' => $dir]) }}">
                    Reset All
                </a>
            </div>
        @endif
    </form>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small table-sm">
                <thead class="table-light align-middle">
                    <tr class="text-center">
                        <th style="width: 40px;">
                            <div class="form-check d-flex justify-content-center">
                                <input class="form-check-input" type="checkbox" id="checkAll">
                            </div>
                        </th>
                        <th style="width:140px;">Tanggal</th>
                        <th style="width:200px;">Kreator/Toko/PIC</th>
                        <th style="width:128px;">Produk</th>
                        <th style="width:100px;">Perjanjian</th>
                        <th style="width:48px;">Qty</th>
                        <th style="width:150px;">ID Pesanan</th>
                        <th style="width:120px;">Aksi</th>
                    </tr>
                </thead>

                <tbody id="sampleTableBody">
                @forelse($sendSamples as $i => $row)
                    @php
                        $hargaProduk    = (int) ($row->product->harga ?? 0);
                        $qty            = (int) ($row->qty ?? 0);
                        $totalHarga     = $hargaProduk * $qty;

                        $tglDealShow    = optional($row->deal_date)->format('d M Y') ?? null;
                        $tglKirimShow   = optional($row->tanggal_kirim)->format('d M Y') ?? null;
                        $tglEstimasiShow= optional($row->estimasi_sampai)->format('d M Y') ?? null;
                        $tglAktualShow  = optional($row->tanggal_aktual)->format('d M Y') ?? null;

                        $perjLive       = (int) ($row->perjanjian_livestream ?? 0);
                        $perjVideo      = (int) ($row->perjanjian_video ?? 0);
                        $hasIdPesanan   = !empty($row->id_pesanan);
                        $idPesananVal   = (string) ($row->id_pesanan ?? '');
                        
                        $usernameVal    = $row->creator->username ?? $row->creator->name ?? '-';
                    @endphp

                    <tr>
                        {{-- CHECKBOX --}}
                        <td class="text-center">
                            <div class="form-check d-flex justify-content-center">
                                <input class="form-check-input check-item" type="checkbox" value="{{ $row->id }}">
                            </div>
                        </td>

                        {{-- TANGGAL --}}
                        <td class="text-start">
                            <div class="fw-semibold">{{ $tglDealShow ?: '-' }}</div>
                            <div class="text-muted small">
                                Kirim: {{ $tglKirimShow ?: '-' }}<br>
                                Estimasi: {{ $tglEstimasiShow ?: '-' }}<br>
                                Aktual: {{ $tglAktualShow ?: '-' }}
                            </div>
                        </td>

                        {{-- KREATOR / TOKO / PIC --}}
                        <td class="text-start">
                            <div class="d-flex align-items-center gap-2">
                                <div class="fw-semibold" style="letter-spacing:.06em;">
                                    {{ $usernameVal }}
                                </div>
                                @if($usernameVal !== '-')
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-secondary rounded-circle d-inline-flex align-items-center justify-content-center border-0"
                                            style="width:24px;height:24px; padding: 0;"
                                            data-copy="{{ $usernameVal }}"
                                            onclick="copyToClipboard(this)"
                                            aria-label="Salin Username" title="Salin Username">
                                        <svg viewBox="0 0 20 20" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M8 3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1V3z"/>
                                            <path d="M6 3H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1h-2"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                            <div class="text-muted small">{{ $row->store->name ?? '-' }}</div>

                            @if($row->user)
                                <div class="mt-2">
                                    <span class="badge rounded-pill text-bg-primary-subtle text-primary border border-primary-subtle">
                                        PIC: {{ $row->user->name  }}
                                    </span>
                                    @if($row->status_chat > 0)
                                        <span class="badge rounded-pill text-bg-primary-subtle text-primary border border-primary-subtle">
                                            Sudah Chat
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </td>

                        <td class="text-start">
                            @if($row->product)
                                <div class="fw-semibold">{{ $row->product->sku ?? '-' }}</div>
                                <div class="text-muted small">{{ formatRupiahFull($hargaProduk) }}</div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        <td class="text-start">
                            <div class="d-flex flex-column gap-1">
                                <span class="badge rounded-pill text-bg-light border text-secondary align-self-start">
                                    Live: {{ $perjLive ?: '-' }}
                                </span>
                                <span class="badge rounded-pill text-bg-warning-subtle text-warning border border-warning-subtle align-self-start">
                                    Video: {{ $perjVideo ?: '-' }}
                                </span>
                            </div>
                        </td>

                        <td class="text-center fw-semibold">{{ $qty }}</td>

                        <td class="text-center">
                            @if($hasIdPesanan)
                                <div class="d-inline-flex align-items-center gap-2">
                                    <span class="badge rounded-pill text-bg-success-subtle text-success border border-success-subtle">
                                        {{ $idPesananVal }}
                                    </span>

                                    <button type="button"
                                            class="btn btn-sm btn-outline-secondary rounded-circle d-inline-flex align-items-center justify-content-center"
                                            style="width:30px;height:30px;"
                                            data-copy="{{ $idPesananVal }}"
                                            onclick="copyToClipboard(this)"
                                            aria-label="Salin ID Pesanan" title="Salin ID Pesanan">
                                        <svg viewBox="0 0 20 20" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M8 3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1V3z"/>
                                            <path d="M6 3H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1h-2"/>
                                        </svg>
                                    </button>
                                </div>
                            @else
                                <span class="badge rounded-pill text-bg-light border text-secondary">Belum ada</span>
                            @endif
                        </td>

                        <td class="text-center align-middle">
                            <div class="d-inline-flex gap-2">
                                @if($hasShow)
                                    <a href="{{ route('admin.kirim-sampel.show', $row) }}"
                                       class="btn btn-sm btn-outline-primary rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center"
                                       style="width:28px;height:28px;"
                                       aria-label="Detail" title="Detail">
                                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </a>
                                @endif

                                @if($hasEdit)
                                    <a href="{{ route('admin.kirim-sampel.edit', $row) }}"
                                       class="btn btn-sm btn-outline-secondary rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center"
                                       style="width:28px;height:28px;"
                                       aria-label="Edit" title="Edit">
                                        <svg viewBox="0 0 20 20" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M4 13.5V16h2.5L14 8.5 11.5 6 4 13.5z"/>
                                            <path d="M12 5.5 13.5 4 16 6.5 14.5 8"/>
                                        </svg>
                                    </a>
                                @endif

                                @if($hasDestroy)
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center"
                                            style="width:28px;height:28px;"
                                            data-url="{{ route('admin.kirim-sampel.destroy', $row->id) }}"
                                            data-name="Kirim sampel #{{ $row->id }}"
                                            onclick="openDeleteModal(this)"
                                            aria-label="Hapus" title="Hapus">
                                        <svg viewBox="0 0 20 20" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M4 6h12"/>
                                            <path d="M8 6V4h4v2"/>
                                            <path d="M7 6v9a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V6"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Belum ada data kirim sampel.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION --}}
    <div class="mt-3">
        @if(method_exists($sendSamples, 'links'))
            {{ $sendSamples->links() }}
        @endif
    </div>

    {{-- MODAL DELETE --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hapus Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    Yakin ingin menghapus <strong id="modalDeleteName"></strong>?
                    <div class="text-muted small mt-1">Tindakan ini tidak dapat dibatalkan.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Ya, hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- FORM BULK DELETE --}}
    <form id="formBulkDelete" action="{{ route('admin.kirim-sampel.bulk_destroy') }}" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    {{-- MODAL UPLOAD MASSAL --}}
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="importModalLabel">Upload Kirim Sampel Massal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form action="{{ route('admin.kirim-sampel.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info small mb-3">
                            <i class="me-1">!</i> Pastikan format Excel memiliki header kolom: <br>
                            <strong>deal_date, pic, approve, toko, kreator, perjanjian_livestream, perjanjian_video, sku, qty, id_pesanan, tanggal_kirim, tanggal_aktual, video_dibuat, live_dibuat</strong>
                        </div>
                        <div class="mb-3">
                            <label for="fileImport" class="form-label fw-semibold">Pilih File Excel (.xlsx / .csv)</label>
                            <input class="form-control" type="file" id="fileImport" name="file" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light text-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success d-flex align-items-center gap-2">
                            
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

    {{-- TOAST COPY --}}
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- VARIABEL ---
            const checkAll = document.getElementById('checkAll');
            const checkItems = document.querySelectorAll('.check-item');
            const btnBulkDelete = document.getElementById('btnBulkDelete');
            const countSelected = document.getElementById('countSelected');

            function toggleBulkButton() {
                const selectedCount = document.querySelectorAll('.check-item:checked').length;
                if(countSelected) countSelected.innerText = selectedCount;
                
                if (selectedCount > 0) {
                    btnBulkDelete.classList.remove('d-none');
                    btnBulkDelete.classList.add('d-inline-flex');
                } else {
                    btnBulkDelete.classList.add('d-none');
                    btnBulkDelete.classList.remove('d-inline-flex');
                }
            }

            if(checkAll) {
                checkAll.addEventListener('change', function() {
                    checkItems.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    toggleBulkButton();
                });
            }

            checkItems.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    if (!this.checked) {
                        if(checkAll) checkAll.checked = false;
                    }

                    if (document.querySelectorAll('.check-item:checked').length === checkItems.length) {
                        if(checkAll) checkAll.checked = true;
                    }

                    toggleBulkButton();
                });
            });

            window.submitBulkDelete = function() {
                const selectedIds = Array.from(document.querySelectorAll('.check-item:checked'))
                                         .map(cb => cb.value);

                if (selectedIds.length === 0) return;

                if (confirm('Apakah Anda yakin ingin menghapus ' + selectedIds.length + ' data terpilih?')) {
                    const form = document.getElementById('formBulkDelete');
                    const oldInputs = form.querySelectorAll('input[name="ids[]"]');
                    
                    oldInputs.forEach(input => input.remove());
                    selectedIds.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids[]';
                        input.value = id;
                        form.appendChild(input);
                    });

                    form.submit();
                }
            }
        });

        // FUNGSI COPY TO CLIPBOARD + TOAST
        window.copyToClipboard = function(btn) {
            const textToCopy = btn.getAttribute('data-copy');
            
            if(navigator.clipboard && window.isSecureContext) {
                // Clipboard API jika didukung browser
                navigator.clipboard.writeText(textToCopy).then(() => {
                    showToast('Berhasil disalin: ' + textToCopy);
                });
            } else {
                // Fallback untuk browser lama
                let textArea = document.createElement("textarea");
                textArea.value = textToCopy;
                textArea.style.position = "fixed";
                textArea.style.left = "-999999px";
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    document.execCommand('copy');
                    showToast('Berhasil disalin: ' + textToCopy);
                } catch (err) {
                    console.error('Gagal menyalin text', err);
                }
                document.body.removeChild(textArea);
            }
        };

        window.showToast = function(message) {
            const toastEl = document.getElementById('copyToast');
            const toastText = document.getElementById('copyToastText');
            toastText.innerText = message;
            
            // Menggunakan fungsi Toast dari Bootstrap 5
            const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
            toast.show();
        };

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

            $('.select2-container').on('click', function (e) {
                e.stopPropagation();
            });
        });
    </script>
    <script src="{{ asset('js/admin/kirim-sampel-index.js') }}"></script>
@endpush
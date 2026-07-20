@extends('layouts.admin')

@section('title', 'Produk')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-3">
        <div>
            <h1 class="h4 mb-0">Produk</h1>
        </div>
        <div class="d-flex align-items-center gap-2">
            
            {{-- BUTTON BULK DELETE --}}
            <button type="button" 
                id="btn-bulk-delete"
                class="btn btn-danger shadow-sm d-none align-items-center gap-2"
                onclick="confirmBulkDelete()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                    <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                </svg>
                <span id="count-selected" class="fw-bold">0</span> Hapus Terpilih
            </button>

            {{-- BUTTON EXPORT --}}
            <a href="{{ route('admin.product.export', request()->all()) }}" 
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
                aria-label="Upload Produk Massal"
                title="Upload Produk Massal">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
                </svg>
            </button>

            {{-- BUTTON TAMBAH --}}
            @if(Route::has('admin.product.create'))
                <a href="{{ route('admin.product.create') }}"
                   class="btn btn-primary shadow-sm rounded-circle d-inline-flex align-items-center justify-content-center"
                   style="width:44px;height:44px;"
                   aria-label="Tambah Produk"
                   title="Tambah">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 2a.75.75 0 0 1 .75.75v4.5h4.5a.75.75 0 0 1 0 1.5h-4.5v4.5a.75.75 0 0 1-1.5 0v-4.5h-4.5a.75.75 0 0 1 0-1.5h4.5v-4.5A.75.75 0 0 1 8 2z"/>
                    </svg>
                </a>
            @endif
        </div>
    </div>

    {{-- SEARCH & FILTER--}}
    <form method="GET" action="{{ route('admin.product.index') }}" class="row g-2 align-items-center mb-3">
        <div class="col-md-5">
            <input type="text" name="q" value="{{ $q }}" class="form-control"
                   placeholder="Cari SKU Utama atau SKU Variasi...">
        </div>

        <input type="hidden" name="sort" value="{{ $sort }}">
        <input type="hidden" name="dir" value="{{ $dir }}">

        <div class="col-auto">
            <button type="submit" class="btn btn-outline-secondary">Cari</button>
        </div>

        @if(!empty($q))
            <div class="col-auto">
                <a class="btn btn-outline-secondary" href="{{ route('admin.product.index') }}">Reset</a>
            </div>
        @endif
    </form>

    @if (session('success'))
        <div class="alert alert-success py-2">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger py-2">{{ session('error') }}</div>
    @endif

    @php
        $dirFor = fn($col) => $sort !== $col ? 'asc' : ($dir === 'asc' ? 'desc' : 'asc');
        $indicator = fn($col) => $sort !== $col ? '' : ($dir === 'asc' ? '▲' : '▼');
    @endphp

    <form action="{{ route('admin.product.bulk_destroy') }}" method="POST" id="bulk-delete-form">
        @csrf
        @method('DELETE')

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        {{-- CHECKBOX --}}
                        <th width="40" class="text-center">
                            <input type="checkbox" class="form-check-input" id="select-all">
                        </th>

                        {{-- SKU Utama --}}
                        <th class="py-2">
                            <a class="text-decoration-none text-dark"
                               href="{{ route('admin.product.index', ['q'=>$q, 'sort'=>'sku_utama', 'dir'=>$dirFor('sku_utama')]) }}">
                                SKU Utama<small class="text-secondary ms-1">{{ $indicator('sku_utama') }}</small>
                            </a>
                        </th>
                        
                        {{-- SKU --}}
                        <th class="py-2">
                            <a class="text-decoration-none text-dark"
                               href="{{ route('admin.product.index', ['q'=>$q, 'sort'=>'sku', 'dir'=>$dirFor('sku')]) }}">
                                SKU <small class="text-secondary ms-1">{{ $indicator('sku') }}</small>
                            </a>
                        </th>

                        {{-- Harga --}}
                        <th class="py-2">
                            <a class="text-decoration-none text-dark"
                               href="{{ route('admin.product.index', ['q'=>$q, 'sort'=>'harga', 'dir'=>$dirFor('harga')]) }}">
                                Harga <small class="text-secondary ms-1">{{ $indicator('harga') }}</small>
                            </a>
                        </th>

                        <th class="py-2 text-center" style="width:120px;">Aksi</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($products as $product)
                        <tr>
                            {{-- CHECKBOX --}}
                            <td class="text-center">
                                <input type="checkbox" name="ids[]" value="{{ $product->id }}" class="form-check-input product-checkbox">
                            </td>

                            <td class="small fw-mono">{{ $product->sku_utama }}</td>
                            <td>
                                <div class="small fw-mono">{{ $product->sku }}</div>
                            </td>
                            <td class="small fw-mono">
                                Rp {{ number_format($product->harga, 0, ',', '.') }}
                            </td>

                            <td class="py-1 text-center">
                                <div class="d-inline-flex align-items-center gap-2">
                                    {{-- EDIT --}}
                                    @if(Route::has('admin.product.edit'))
                                        <a href="{{ route('admin.product.edit', $product->id) }}"
                                           class="btn btn-sm btn-outline-secondary rounded-circle d-inline-flex align-items-center justify-content-center"
                                           style="width:30px;height:30px;"
                                           aria-label="Edit" title="Edit">
                                            <svg viewBox="0 0 20 20" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <path d="M4 13.5V16h2.5L14 8.5 11.5 6 4 13.5z"/>
                                                <path d="M12 5.5 13.5 4 16 6.5 14.5 8"/>
                                            </svg>
                                        </a>
                                    @endif

                                    {{-- DELETE --}}
                                    @if(Route::has('admin.product.destroy'))
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger rounded-circle d-inline-flex align-items-center justify-content-center"
                                                style="width:30px;height:30px;"
                                                data-url="{{ route('admin.product.destroy', $product->id) }}"
                                                data-name="{{ $product->sku }} ({{ $product->sku_utama }})"
                                                onclick="openDeleteModal(this)"
                                                aria-label="Hapus" title="Hapus">
                                            <svg viewBox="0 0 20 20" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.5">
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
                            <td colspan="5" class="text-center text-secondary py-5">
                                Belum ada data produk.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </form>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $products->withQueryString()->links() }}
    </div>

    {{-- MODAL DELETE --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hapus Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    Yakin ingin menghapus produk: <strong id="modalItemName"></strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL IMPORT --}}
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="importModalLabel">Upload Produk Massal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form action="{{ route('admin.product.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info small mb-2">
                            <i class="me-1">!</i> Pastikan format Excel memiliki header kolom: <br>
                            <strong>sku_utama, sku, harga</strong>
                        </div>
                        <div class="mb-3">
                            <a href="{{ route('admin.product.template') }}"
                               class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-2"
                               style="text-decoration:none;">
                                <svg viewBox="0 0 16 16" width="13" height="13" fill="currentColor">
                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                                    <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 1-.708.708l3 3z"/>
                                </svg>
                                Download Template Excel
                            </a>
                        </div>
                        <div class="mb-3">
                            <label for="fileImport" class="form-label fw-semibold">Pilih File Excel (.xlsx / .csv)</label>
                            <input class="form-control" type="file" id="fileImport" name="file" accept=".xlsx,.xls,.csv" required>
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @push('scripts')
    <script>
        function openDeleteModal(el) {
            const url = el.getAttribute('data-url');
            const name = el.getAttribute('data-name');
            document.getElementById('deleteForm').action = url;
            document.getElementById('modalItemName').textContent = name;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.product-checkbox');
        const btnBulkDelete = document.getElementById('btn-bulk-delete');
        const countSelectedSpan = document.getElementById('count-selected');

        function updateBulkDeleteButton() {
            const checkedCount = document.querySelectorAll('.product-checkbox:checked').length;
            countSelectedSpan.textContent = checkedCount;

            if (checkedCount > 0) {
                btnBulkDelete.classList.remove('d-none');
                btnBulkDelete.classList.add('d-inline-flex');
            } else {
                btnBulkDelete.classList.add('d-none');
                btnBulkDelete.classList.remove('d-inline-flex');
            }
        }

        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateBulkDeleteButton();
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                if (!this.checked) {
                    selectAll.checked = false;
                }

                const allChecked = document.querySelectorAll('.product-checkbox:checked').length === checkboxes.length;
                
                if(allChecked) {
                    selectAll.checked = true;
                }
                
                updateBulkDeleteButton();
            });
        });

        function confirmBulkDelete() {
            const checkedCount = document.querySelectorAll('.product-checkbox:checked').length;
            
            Swal.fire({
                title: 'Hapus ' + checkedCount + ' Produk?',
                text: "Data yang dipilih akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('bulk-delete-form').submit();
                }
            })
        }
    </script>
    @endpush
@endsection
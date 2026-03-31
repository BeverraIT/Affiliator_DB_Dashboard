@extends('layouts.admin')

@section('title', 'Kategori')

@section('content')
    {{-- Header: Judul & Tombol --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-3">
        <div>
            <h1 class="h4 mb-0">Kategori</h1>
        </div>
        <div class="d-flex align-items-center gap-2">
            
            {{-- [BARU] Tombol Bulk Delete (Default Hidden) --}}
            <button type="button" 
                    id="btnBulkDelete" 
                    class="btn btn-danger shadow-sm rounded-pill d-none align-items-center gap-2"
                    onclick="submitBulkDelete()">
                <svg viewBox="0 0 20 20" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 6h12M8 6V4h4v2M7 6v9a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V6" />
                </svg>
                <span class="small fw-bold">Hapus (<span id="countSelected">0</span>)</span>
            </button>

            @if(Route::has('admin.category.create'))
                <a href="{{ route('admin.category.create') }}"
                   class="btn btn-primary shadow-sm rounded-circle d-inline-flex align-items-center justify-content-center"
                   style="width:44px;height:44px;"
                   aria-label="Tambah Kategori"
                   title="Tambah">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 2a.75.75 0 0 1 .75.75v4.5h4.5a.75.75 0 0 1 0 1.5h-4.5v4.5a.75.75 0 0 1-1.5 0v-4.5h-4.5a.75.75 0 0 1 0-1.5h4.5v-4.5A.75.75 0 0 1 8 2z"/>
                    </svg>
                </a>
            @endif
        </div>
    </div>

    {{-- Filter & Search Bar --}}
    <form method="GET" action="{{ route('admin.category.index') }}" class="row g-2 align-items-center mb-3">
        <div class="col-md-5">
            <input type="text" name="q" value="{{ $q ?? request('q') }}" class="form-control"
                   placeholder="Cari Kategori...">
        </div>

        <input type="hidden" name="sort" value="{{ $sort ?? 'name' }}">
        <input type="hidden" name="dir" value="{{ $dir ?? 'asc' }}">

        <div class="col-auto">
            <button type="submit" class="btn btn-outline-secondary">Cari</button>
        </div>

        @if(!empty($q) || !empty(request('q')))
            <div class="col-auto">
                <a class="btn btn-outline-secondary" href="{{ route('admin.category.index') }}">Reset</a>
            </div>
        @endif
    </form>

    {{-- Flash Message --}}
    @if (session('success'))
        <div class="alert alert-success py-2">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger py-2">{{ session('error') }}</div>
    @endif

    {{-- Logic Sorting (Helper Variables) --}}
    @php
        $currentSort = $sort ?? 'name';
        $currentDir  = $dir ?? 'asc';
        
        $dirFor = fn($col) => $currentSort !== $col ? 'asc' : ($currentDir === 'asc' ? 'desc' : 'asc');
        $indicator = fn($col) => $currentSort !== $col ? '' : ($currentDir === 'asc' ? '▲' : '▼');
    @endphp

    {{-- Table Content --}}
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small table-sm">
                <thead class="table-light">
                <tr>
                    {{-- [BARU] Kolom Check All --}}
                    <th style="width: 40px;" class="text-center">
                        <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="checkAll">
                        </div>
                    </th>

                    {{-- Kolom Nama (Sortable) --}}
                    <th class="py-2">
                        <a class="text-decoration-none text-dark"
                           href="{{ route('admin.category.index', ['q' => $q ?? request('q'), 'sort'=>'name', 'dir'=>$dirFor('name')]) }}">
                            Nama Kategori
                            <small class="text-secondary ms-1">{{ $indicator('name') }}</small>
                        </a>
                    </th>
                    
                    <th class="text-end py-2 text-center" style="width:120px;">Aksi</th>
                </tr>
                </thead>

                <tbody>
                @forelse($categories as $index => $category)
                    <tr>
                        {{-- [BARU] Kolom Check Item --}}
                        <td class="text-center">
                            <div class="form-check d-flex justify-content-center">
                                <input class="form-check-input check-item" type="checkbox" value="{{ $category->id }}">
                            </div>
                        </td>

                        <td class="py-2">
                            <span>{{ $category->name }}</span>
                        </td>

                        <td class="text-end py-1 text-center">
                            <div class="d-inline-flex align-items-center gap-2">
                                @if(Route::has('admin.category.edit'))
                                    <a href="{{ route('admin.category.edit', $category->id) }}"
                                       class="btn btn-sm btn-outline-secondary rounded-circle d-inline-flex align-items-center justify-content-center"
                                       style="width:30px;height:30px;"
                                       aria-label="Edit" title="Edit">
                                        <svg viewBox="0 0 20 20" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path d="M4 13.5V16h2.5L14 8.5 11.5 6 4 13.5z"/>
                                            <path d="M12 5.5 13.5 4 16 6.5 14.5 8"/>
                                        </svg>
                                    </a>
                                @endif

                                @if(Route::has('admin.category.destroy'))
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger rounded-circle d-inline-flex align-items-center justify-content-center"
                                            style="width:30px;height:30px;"
                                            data-url="{{ route('admin.category.destroy', $category->id) }}"
                                            data-name="{{ $category->name }}"
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
                        <td colspan="3" class="text-center text-secondary py-5">
                            <div class="d-flex flex-column align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-inbox text-muted mb-2" viewBox="0 0 16 16">
                                    <path d="M4.98 4a.5.5 0 0 0-.39.188L1.54 8H6a.5.5 0 0 1 .5.5 1.5 1.5 0 1 0 3 0A.5.5 0 0 1 10 8h4.46l-3.05-3.812A.5.5 0 0 0 11.02 4H4.98zm9.954 5H10.45a2.5 2.5 0 0 1-4.9 0H1.066l.32 2.562a1 1 0 0 0 .997.878h11.234a1 1 0 0 0 .997-.878L14.934 9z"/>
                                    <path d="M14 3a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h12zM2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H2z"/>
                                </svg>
                                <span>Belum ada kategori yang ditemukan.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $categories->withQueryString()->links() }}
    </div>

    {{-- [BARU] Hidden Form untuk Bulk Delete --}}
    <form id="formBulkDelete" action="{{ route('admin.category.bulk_destroy') }}" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    {{-- Modal Delete Reusable --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hapus Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus kategori: <strong id="modalItemName"></strong>?<br>
                    <small class="text-muted">Tindakan ini tidak dapat dibatalkan.</small>
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

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- VARIABEL ---
            const checkAll = document.getElementById('checkAll');
            const checkItems = document.querySelectorAll('.check-item');
            const btnBulkDelete = document.getElementById('btnBulkDelete');
            const countSelected = document.getElementById('countSelected');
            
            // --- 1. LOGIKA BULK DELETE ---

            // A. Fungsi Toggle Tampilan Tombol Delete
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

            // B. Event Listener: Check All (Header)
            if(checkAll) {
                checkAll.addEventListener('change', function() {
                    checkItems.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    toggleBulkButton();
                });
            }

            // C. Event Listener: Check Item (Per baris)
            checkItems.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    // Uncheck 'Check All' jika ada satu yang tidak dicentang
                    if (!this.checked) {
                        if(checkAll) checkAll.checked = false;
                    }
                    // Check 'Check All' jika semua dicentang
                    if (document.querySelectorAll('.check-item:checked').length === checkItems.length) {
                        if(checkAll) checkAll.checked = true;
                    }
                    toggleBulkButton();
                });
            });

            // D. Fungsi Submit Bulk Delete (Global)
            window.submitBulkDelete = function() {
                const selectedIds = Array.from(document.querySelectorAll('.check-item:checked'))
                                         .map(cb => cb.value);

                if (selectedIds.length === 0) return;

                if (confirm('Apakah Anda yakin ingin menghapus ' + selectedIds.length + ' kategori terpilih?')) {
                    const form = document.getElementById('formBulkDelete');
                    
                    // Bersihkan input hidden lama (jika ada sisa submit sebelumnya)
                    const oldInputs = form.querySelectorAll('input[name="ids[]"]');
                    oldInputs.forEach(input => input.remove());

                    // Buat input hidden baru untuk setiap ID
                    selectedIds.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids[]'; // Penting: name pakai kurung siku []
                        input.value = id;
                        form.appendChild(input);
                    });

                    form.submit();
                }
            }
        });

        // --- 2. LOGIKA SINGLE DELETE (Modal) ---
        function openDeleteModal(element) {
            const url = element.getAttribute('data-url');
            const name = element.getAttribute('data-name');
            
            document.getElementById('deleteForm').action = url;
            document.getElementById('modalItemName').innerText = name;
            
            const myModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            myModal.show();
        }
    </script>
    @endpush
@endsection
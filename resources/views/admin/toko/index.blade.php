@extends('layouts.admin')

@section('title', 'Toko')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-3">
        <div>
            <h1 class="h4 mb-0">Toko</h1>
        </div>
        <div class="d-flex align-items-center gap-2">
            {{-- BUTTON BULK DELETE --}}
            <button type="button" 
                    id="btnBulkDelete" 
                    class="btn btn-danger shadow-sm rounded-pill d-none align-items-center gap-2"
                    onclick="submitBulkDelete()">
                <svg viewBox="0 0 20 20" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 6h12M8 6V4h4v2M7 6v9a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V6" />
                </svg>
                <span class="small fw-bold">Hapus (<span id="countSelected">0</span>)</span>
            </button>

            {{-- BUTTON TAMBAH --}}
            @if(Route::has('admin.toko.create'))
                <a href="{{ route('admin.toko.create') }}"
                   class="btn btn-primary shadow-sm rounded-circle d-inline-flex align-items-center justify-content-center"
                   style="width:44px;height:44px;"
                   aria-label="Tambah Toko"
                   title="Tambah">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 2a.75.75 0 0 1 .75.75v4.5h4.5a.75.75 0 0 1 0 1.5h-4.5v4.5a.75.75 0 0 1-1.5 0v-4.5h-4.5a.75.75 0 0 1 0-1.5h4.5v-4.5A.75.75 0 0 1 8 2z"/>
                    </svg>
                </a>
            @endif
        </div>
    </div>

    {{-- SEARCH & FILTER --}}
    <form method="GET" action="{{ route('admin.toko.index') }}" class="row g-2 align-items-center mb-3">
        <div class="col-md-5">
            <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control"
                   placeholder="Cari Toko...">
        </div>

        <input type="hidden" name="sort" value="{{ $sort ?? 'name' }}">
        <input type="hidden" name="dir" value="{{ $dir ?? 'asc' }}">

        <div class="col-auto">
            <button type="submit" class="btn btn-outline-secondary">Cari</button>
        </div>

        @if(!empty($q))
            <div class="col-auto">
                <a class="btn btn-outline-secondary" href="{{ route('admin.toko.index') }}">Reset</a>
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
        $currentSort = $sort ?? 'name';
        $currentDir  = $dir ?? 'asc';
        $dirFor = fn($col) => $currentSort !== $col ? 'asc' : ($currentDir === 'asc' ? 'desc' : 'asc');
        $indicator = fn($col) => $currentSort !== $col ? '' : ($currentDir === 'asc' ? '▲' : '▼');
    @endphp

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small table-sm">
                <thead class="table-light">
                    <tr>
                        {{-- CHECKBOX --}}
                        <th style="width: 40px;" class="text-center">
                            <div class="form-check d-flex justify-content-center">
                                <input class="form-check-input" type="checkbox" id="checkAll">
                            </div>
                        </th>

                        <th class="py-2">
                            <a class="text-decoration-none text-dark"
                            href="{{ route('admin.toko.index', ['q'=>$q, 'sort'=>'name', 'dir'=>$dirFor('name')]) }}">
                                Nama Toko
                                <small class="text-secondary ms-1">{{ $indicator('name') }}</small>
                            </a>
                        </th>

                        <th class="text-end py-2 text-center" style="width:120px;">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody>
                @forelse($stores as $index => $store)
                    <tr>
                        {{-- CHECKBOX --}}
                        <td class="text-center">
                            <div class="form-check d-flex justify-content-center">
                                <input class="form-check-input check-item" type="checkbox" value="{{ $store->id }}">
                            </div>
                        </td>

                        {{-- NAMA --}}
                        <td class="py-2">
                            <span>{{ $store->name }}</span>
                        </td>

                        {{-- BUTTON --}}
                        <td class="text-end py-1 text-center">
                            <div class="d-inline-flex align-items-center gap-2">
                                @if(Route::has('admin.toko.edit'))
                                    <a href="{{ route('admin.toko.edit', $store->id) }}"
                                       class="btn btn-sm btn-outline-secondary rounded-circle d-inline-flex align-items-center justify-content-center"
                                       style="width:30px;height:30px;"
                                       aria-label="Edit" title="Edit">
                                        <svg viewBox="0 0 20 20" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path d="M4 13.5V16h2.5L14 8.5 11.5 6 4 13.5z"/>
                                            <path d="M12 5.5 13.5 4 16 6.5 14.5 8"/>
                                        </svg>
                                    </a>
                                @endif

                                @if(Route::has('admin.toko.destroy'))
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger rounded-circle d-inline-flex align-items-center justify-content-center"
                                            style="width:30px;height:30px;"
                                            data-url="{{ route('admin.toko.destroy', $store->id) }}"
                                            data-name="{{ $store->name }}"
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
                            Belum ada data toko.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $stores->withQueryString()->links() }}
    </div>

    {{-- FORM BULK DELETE --}}
    <form id="formBulkDelete" action="{{ route('admin.toko.bulk_destroy') }}" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hapus Toko</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    Yakin ingin menghapus toko: <strong id="modalItemName"></strong>?
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

                if (confirm('Apakah Anda yakin ingin menghapus ' + selectedIds.length + ' toko terpilih?')) {
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
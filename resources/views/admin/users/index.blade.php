@extends('layouts.admin')

@section('title', 'User')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-3">
        <div>
            <h1 class="h4 mb-0">User</h1>
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
            <a href="{{ route('admin.users.create') }}"
            class="btn btn-primary shadow-sm rounded-circle d-inline-flex align-items-center justify-content-center"
            style="width:44px;height:44px;"
            aria-label="Tambah User"
            title="Tambah">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 2a.75.75 0 0 1 .75.75v4.5h4.5a.75.75 0 0 1 0 1.5h-4.5v4.5a.75.75 0 0 1-1.5 0v-4.5h-4.5a.75.75 0 0 1 0-1.5h4.5v-4.5A.75.75 0 0 1 8 2z"/>
                </svg>
            </a>
        </div>
    </div>

    {{-- SEARCHING --}}
    <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 align-items-center mb-3">
        <div class="col-md-5">
            <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control"
                   placeholder="Cari Nama atau Email...">
        </div>

        <input type="hidden" name="sort" value="{{ $sort ?? 'name' }}">
        <input type="hidden" name="dir" value="{{ $dir ?? 'asc' }}">

        <div class="col-auto">
            <button class="btn btn-outline-secondary">Cari</button>
        </div>

        @if(!empty($q))
            <div class="col-auto">
                <a class="btn btn-outline-secondary" href="{{ route('admin.users.index') }}">Reset</a>
            </div>
        @endif
    </form>

    {{-- MESSAGE --}}
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

                    <th class="py-1">
                        <a class="text-decoration-none text-dark"
                        href="{{ route('admin.users.index', ['q'=>$q, 'sort'=>'name', 'dir'=>$dirFor('name')]) }}">
                            Nama
                            <small class="text-secondary ms-1">{{ $indicator('name') }}</small>
                        </a>
                    </th>
                    <th class="py-1">
                        <a class="text-decoration-none text-dark"
                        href="{{ route('admin.users.index', ['q'=>$q, 'sort'=>'email', 'dir'=>$dirFor('email')]) }}">
                            Email
                            <small class="text-secondary ms-1">{{ $indicator('email') }}</small>
                        </a>
                    </th>
                    <th class="text-end py-1 text-center" style="width:120px;">Aksi</th>
                </tr>
                </thead>

                <tbody>
                @forelse($users as $row)
                    @php
                        $isSelf = auth()->id() === $row->id;
                    @endphp
                    <tr>
                        {{-- CHECKBOX --}}
                        <td class="text-center">
                            <div class="form-check d-flex justify-content-center">
                                <input class="form-check-input check-item" 
                                       type="checkbox" 
                                       value="{{ $row->id }}"
                                       {{ $isSelf ? 'disabled' : '' }}
                                       title="{{ $isSelf ? 'Anda tidak bisa menghapus akun sendiri' : '' }}">
                            </div>
                        </td>

                        {{-- NAMA --}}
                        <td class="py-1">
                            <div class="d-flex align-items-center gap-2">
                                <span>{{ $row->name }}</span>
                                @if($isSelf)
                                    <span class="badge bg-success-subtle text-success" style="font-size: 0.7rem">Saya</span>
                                @endif
                            </div>
                        </td>

                        {{-- EMAIL --}}
                        <td class="py-1">
                            <div class="d-flex align-items-center gap-2">
                                <span>{{ $row->email }}</span>
                            </div>
                        </td>

                        {{-- BUTTON --}}
                        <td class="text-end py-1 text-center">
                            <div class="d-inline-flex align-items-center gap-2" aria-label="Aksi">
                                {{-- EDIT --}}
                                <a href="{{ route('admin.users.edit', $row->id) }}"
                                class="btn btn-sm btn-outline-secondary rounded-circle d-inline-flex align-items-center justify-content-center"
                                style="width:30px;height:30px;"
                                aria-label="Edit" title="Edit">
                                    <svg viewBox="0 0 20 20" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path d="M4 13.5V16h2.5L14 8.5 11.5 6 4 13.5z"/>
                                        <path d="M12 5.5 13.5 4 16 6.5 14.5 8"/>
                                    </svg>
                                </a>

                                {{-- DELETE --}}
                                @if(!$isSelf)
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger rounded-circle d-inline-flex align-items-center justify-content-center"
                                            style="width:30px;height:30px;"
                                            data-url="{{ route('admin.users.destroy', $row->id) }}"
                                            data-name="{{ $row->name }}"
                                            onclick="openDeleteModal(this)"
                                            aria-label="Hapus" title="Hapus">
                                        <svg viewBox="0 0 20 20" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5">
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
                        <td colspan="4" class="text-center text-secondary py-5">
                            Belum ada user.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $users->links() }}
    </div>

    {{-- FORM BULK DELETE --}}
    <form id="formBulkDelete" action="{{ route('admin.users.bulk_destroy') }}" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hapus User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    Yakin mau hapus: <strong id="modalItemName"></strong>?
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

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkAll = document.getElementById('checkAll');
            const checkItems = document.querySelectorAll('.check-item');
            const btnBulkDelete = document.getElementById('btnBulkDelete');
            const countSelected = document.getElementById('countSelected');

            function toggleBulkButton() {
                const selectedCount = document.querySelectorAll('.check-item:checked:not(:disabled)').length;
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
                        if (!checkbox.disabled) {
                            checkbox.checked = this.checked;
                        }
                    });
                    toggleBulkButton();
                });
            }

            checkItems.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    if (!this.checked) {
                        if(checkAll) checkAll.checked = false;
                    }

                    const enabledItems = document.querySelectorAll('.check-item:not(:disabled)');
                    const checkedEnabledItems = document.querySelectorAll('.check-item:checked:not(:disabled)');
                    
                    if (enabledItems.length > 0 && enabledItems.length === checkedEnabledItems.length) {
                        if(checkAll) checkAll.checked = true;
                    }
                    
                    toggleBulkButton();
                });
            });

            window.submitBulkDelete = function() {
                const selectedIds = Array.from(document.querySelectorAll('.check-item:checked'))
                                         .map(cb => cb.value);

                if (selectedIds.length === 0) return;

                if (confirm('Apakah Anda yakin ingin menghapus ' + selectedIds.length + ' user terpilih?')) {
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
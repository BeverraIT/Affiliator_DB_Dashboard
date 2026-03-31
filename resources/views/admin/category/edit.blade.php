@extends('layouts.admin')

@section('title', 'Edit Kategori')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <h1 class="h4 mb-0">Edit Kategori</h1>
        </div>
    </div>

    {{-- Tombol Kembali (Bulat) --}}
    <a href="{{ route('admin.category.index') }}"
       class="btn btn-outline-secondary rounded-circle d-inline-flex align-items-center justify-content-center"
       style="width:44px;height:44px;"
       aria-label="Kembali"
       title="Kembali">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
            <path d="M15 8a.5.5 0 0 1-.5.5H3.707l3.147 3.146a.5.5 0 0 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L3.707 7.5H14.5A.5.5 0 0 1 15 8z"/>
        </svg>
    </a>
</div>

{{-- Flash Message (Opsional jika validasi gagal selain error field) --}}
@if (session('error'))
    <div class="alert alert-danger py-2">{{ session('error') }}</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        {{-- Form Update --}}
        <form method="POST" action="{{ route('admin.category.update', $category->id) }}" novalidate>
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label fw-semibold">Nama Kategori</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control @error('name') is-invalid @enderror"
                    {{-- Menggunakan old() dengan default value dari database --}}
                    value="{{ old('name', $category->name) }}"
                    placeholder="Contoh: Fashion, Elektronik"
                    maxlength="120"
                    required
                >

                {{-- Validasi Error --}}
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end gap-2">
                {{-- Tombol Batal --}}
                <a href="{{ route('admin.category.index') }}" class="btn btn-outline-secondary">
                    Batal
                </a>

                {{-- Tombol Simpan --}}
                <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white bg-opacity-25"
                          style="width:22px;height:22px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                        </svg>
                    </span>
                    Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
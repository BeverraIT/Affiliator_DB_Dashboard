@extends('layouts.admin')

@section('title', 'Tambah Kategori')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <h1 class="h4 mb-0">Tambah Kategori</h1>
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

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        {{-- Form Store --}}
        <form method="POST" action="{{ route('admin.category.store') }}" novalidate>
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label fw-semibold">Nama Kategori</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name') }}"
                    placeholder="Contoh: Fashion, Elektronik, Skincare"
                    maxlength="100"
                    required
                    autofocus
                >

                {{-- Validasi Error / Helper Text --}}
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
                            <path d="M7.5 1.5a.5.5 0 0 1 1 0v6h6a.5.5 0 0 1 0 1h-6v6a.5.5 0 0 1-1 0v-6h-6a.5.5 0 0 1 0-1h6v-6z"/>
                        </svg>
                    </span>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
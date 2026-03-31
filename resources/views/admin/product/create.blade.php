@extends('layouts.admin')

@section('title', 'Tambah Produk')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <h1 class="h4 mb-0">Tambah Produk</h1>
        </div>
    </div>

    {{-- BUTTON KEMBALI --}}
    <a href="{{ route('admin.product.index') }}"
       class="btn btn-outline-secondary rounded-circle d-inline-flex align-items-center justify-content-center"
       style="width:44px;height:44px;"
       aria-label="Kembali"
       title="Kembali">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
            <path d="M15 8a.5.5 0 0 1-.5.5H3.707l3.147 3.146a.5.5 0 0 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L3.707 7.5H14.5A.5.5 0 0 1 15 8z"/>
        </svg>
    </a>
</div>

{{-- FORM --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.product.store') }}" novalidate>
            @csrf
            <div class="row g-3">
                <div class="col-md-12">
                    {{-- SKU UTAMA --}}
                    <div class="mb-3">
                        <label for="sku_utama" class="form-label fw-semibold">SKU Utama</label>
                        <input
                            type="text"
                            id="sku_utama"
                            name="sku_utama"
                            class="form-control @error('sku_utama') is-invalid @enderror"
                            value="{{ old('sku_utama') }}"
                            placeholder="Contoh: PARENT-TOPI-01"
                            required
                            autofocus
                        >
                        @error('sku_utama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- SKU VARIASI --}}
                    <div class="mb-3">
                        <label for="sku" class="form-label fw-semibold">SKU Variasi</label>
                        <input
                            type="text"
                            id="sku"
                            name="sku"
                            class="form-control @error('sku') is-invalid @enderror"
                            value="{{ old('sku') }}"
                            placeholder="Contoh: TOPI-BLK-L"
                            required
                        >
                        @error('sku')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- HARGA --}}
                    <div class="mb-3">
                        <label for="harga" class="form-label fw-semibold">Harga (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Rp</span>
                            <input
                                type="number"
                                id="harga"
                                name="harga"
                                class="form-control @error('harga') is-invalid @enderror"
                                value="{{ old('harga') }}"
                                placeholder="0"
                                min="0"
                            >
                            @error('harga')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4 border-secondary-subtle opacity-25">

            {{-- BUTTON BATAL & SIMPAN --}}
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.product.index') }}" class="btn btn-outline-secondary">
                    Batal
                </a>

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
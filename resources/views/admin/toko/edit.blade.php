@extends('layouts.admin')

@section('title', 'Edit Toko')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <h1 class="h4 mb-0">Edit Toko</h1>
        </div>
    </div>

    {{-- BUTTON KEMBALI --}}
    <a href="{{ route('admin.toko.index') }}"
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
        <form method="POST" action="{{ route('admin.toko.update', $store->id) }}" novalidate>
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-8 col-lg-12">
                    {{-- TOKO --}}
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Nama Toko</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $store->name) }}"
                            placeholder="Contoh: YARRA STORE"
                            required
                            autofocus
                        >

                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <div class="form-text">Pastikan nama toko unik.</div>
                        @enderror
                    </div>

                    {{-- BUTTON BATAL & UPDATE --}}
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.toko.index') }}" class="btn btn-outline-secondary">
                            Batal
                        </a>

                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white bg-opacity-25"
                                  style="width:22px;height:22px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7.5 7.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6 10.793l7.146-7.147a.5.5 0 0 1 .708 0z"/>
                                </svg>
                            </span>
                            Update
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
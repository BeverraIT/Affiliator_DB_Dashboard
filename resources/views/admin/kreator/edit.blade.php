@extends('layouts.admin')

@section('title', 'Edit Kreator')

@section('content')
@php
    $currentCategories = old('categories', $selectedCategoryIds ?? []);
    $currentStyles     = old('contentstyles', $selectedStyleIds ?? []);
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Edit Kreator</h1>
    <a href="{{ old('redirect_to', url()->previous()) }}" class="btn btn-outline-secondary rounded-circle d-inline-flex align-items-center justify-content-center" style="width:44px;height:44px;" title="Kembali">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
            <path d="M15 8a.5.5 0 0 1-.5.5H3.707l3.147 3.146a.5.5 0 0 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L3.707 7.5H14.5A.5.5 0 0 1 15 8z"/>
        </svg>
    </a>
</div>

<div style="max-width: 1000px;">
    @if ($errors->any())
        <div class="alert alert-danger py-2 mb-3">
            <div class="fw-semibold">Terjadi kesalahan input:</div>
            <ul class="mb-0 ps-3 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.kreator.update', $creator->id) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="redirect_to" value="{{ old('redirect_to', url()->previous()) }}">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-4">
                <div class="row g-3">

                    {{-- Username --}}
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">Username</label>
                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                               value="{{ old('username', $creator->username) }}" required>
                        @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Channel --}}
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">Channel Penjualan</label>
                        <select name="channel" class="form-select @error('channel') is-invalid @enderror" required>
                            <option value="">Pilih Channel Penjualan</option>
                            @foreach(['video', 'live', 'kartu produk'] as $opt)
                                <option value="{{ $opt }}" @selected(old('channel', $creator->channel) === $opt)>
                                    {{ ucfirst($opt) }}
                                </option>
                            @endforeach
                        </select>
                        @error('channel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Gender --}}
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">Gender Pengikut</label>
                        <select name="genderpengikut" class="form-select @error('genderpengikut') is-invalid @enderror" required>
                            <option value="">Pilih Gender</option>
                            @foreach(['perempuan', 'laki-laki', 'seimbang'] as $opt)
                                <option value="{{ $opt }}" @selected(old('genderpengikut', $creator->genderpengikut) === $opt)>
                                    {{ ucfirst($opt) }}
                                </option>
                            @endforeach
                        </select>
                        @error('genderpengikut') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- GMV --}}
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">GMV</label>
                        <input type="number" name="gmv" class="form-control @error('gmv') is-invalid @enderror"
                               value="{{ old('gmv', (int)$creator->gmv) }}" required>
                        @error('gmv') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Item Terjual --}}
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">Item Terjual</label>
                        <input type="number" name="itemsterjual" class="form-control @error('itemsterjual') is-invalid @enderror"
                               value="{{ old('itemsterjual', (int)$creator->itemsterjual) }}" required>
                        @error('itemsterjual') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- TOP KREATOR --}}
                    <div class="col-12 col-md-2">
                        <label class="form-label fw-semibold d-block">TOP KREATOR</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input @error('top_creator') is-invalid @enderror" 
                                type="checkbox" 
                                name="top_creator" 
                                id="top_creator" 
                                role="switch"
                                value="1"
                                {{ old('top_creator', $creator->top_creator) ? 'checked' : '' }}>
                            <label class="form-check-label" for="top_creator">Top Kreator</label>
                            
                            @error('top_creator') 
                                <div class="invalid-feedback d-block">{{ $message }}</div> 
                            @enderror
                        </div>
                    </div>

                    {{-- BLOKIR --}}
                    <div class="col-12 col-md-2">
                        <label class="form-label fw-semibold d-block">BLOKIR</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input @error('is_blocked') is-invalid @enderror" 
                                type="checkbox" 
                                name="is_blocked" 
                                id="is_blocked" 
                                role="switch"
                                value="1"
                                {{ old('is_blocked', $creator->is_blocked) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_blocked">Blokir</label>

                            @error('is_blocked') 
                                <div class="invalid-feedback d-block">{{ $message }}</div> 
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- UMUR --}}
        <x-form.checkbox-group 
            label="Umur Pengikut" 
            name="ages" 
            :options="$ages" 
            :selected="$selectedAgeIds"
            maxInfo="Boleh pilih lebih dari satu"
        />

        {{-- KONTAK WA --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-4">
                <div class="fw-semibold mb-2">Kontak WhatsApp</div>
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">Nama WA</label>
                        <input type="text" name="namawa" class="form-control @error('namawa') is-invalid @enderror"
                               value="{{ old('namawa', $creator->namawa) }}">
                        @error('namawa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">Nomor WA</label>
                        <input type="text" name="nomerwa" class="form-control @error('nomerwa') is-invalid @enderror"
                               value="{{ old('nomerwa', $creator->nomerwa) }}">
                        @error('nomerwa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- KATEGORI --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-4">
                <div class="mb-2">
                    <label class="fw-semibold form-label">Kategori</label>
                    <span class="text-secondary small ms-1">(Maksimal 5)</span>
                </div>
                <select name="categories[]" class="form-select select2" multiple="multiple" 
                        style="width: 100%;" data-placeholder="Pilih Kategori..." data-limit="5">
                    @foreach ($categories as $id => $name)
                        <option value="{{ $id }}" 
                            {{ in_array($id, $currentCategories) ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
                @error('categories') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- KONTEN STYLE --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-4">
                <div class="mb-2">
                    <label class="fw-semibold form-label">Konten Style</label>
                    <span class="text-secondary small ms-1">(Maksimal 9)</span>
                </div>
                <select name="contentstyles[]" class="form-select select2" multiple="multiple" 
                        style="width: 100%;" data-placeholder="Pilih Content Style..." data-limit="9">
                    @foreach ($contentStyles as $id => $name)
                        <option value="{{ $id }}" 
                            {{ in_array($id, $currentStyles) ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
                @error('contentstyles') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-5">
            <a href="{{ old('redirect_to', url()->previous()) }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary px-4">Update Kreator</button>
        </div>
    </form>
</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').each(function() {
                let limit = $(this).data('limit') ? $(this).data('limit') : 0;

                $(this).select2({
                    theme: "bootstrap-5",
                    width: '100%',
                    placeholder: $(this).data('placeholder'),
                    allowClear: true,
                    maximumSelectionLength: limit
                });
            });
        });
    </script>
@endpush
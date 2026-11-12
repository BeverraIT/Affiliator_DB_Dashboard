@extends('layouts.admin')

@section('title', 'Edit Kreator')

@push('styles')
<style>
/* ─── MD3 Design Tokens ─────────────────────────────────────── */

/* ─── Type Scale ─────────────────────────────────────────────── */
.ts-headline-sm { font-size: 1.5rem; font-weight: 600; line-height: 2rem; }
.ts-body-sm     { font-size: .75rem; font-weight: 400; line-height: 1rem; letter-spacing: .025em; }

/* ─── Surface Card ───────────────────────────────────────────── */
.surf-card { background: #fff; border-radius: var(--shape-lg); box-shadow: var(--md-elev-1); border: none; }

/* ─── MD3 Buttons ────────────────────────────────────────────── */
.btn-md {
    display: inline-flex; align-items: center; justify-content: center; gap: .375rem;
    padding: .5625rem 1.25rem; border-radius: 20px;
    font-size: .875rem; font-weight: 500;
    border: none; cursor: pointer; text-decoration: none; transition: all .15s;
}
.btn-md:focus-visible           { outline: 2px solid var(--md-primary); outline-offset: 2px; }
.btn-md.filled-primary          { background: var(--md-primary); color: var(--md-on-primary); }
.btn-md.filled-primary:hover    { background: #1255A6; box-shadow: 0 2px 8px rgba(21,101,192,.35); color: var(--md-on-primary); }
.btn-md.outlined                { background: transparent; color: var(--md-on-surface-var); border: 1.5px solid var(--md-outline-var); }
.btn-md.outlined:hover          { background: rgba(26,27,31,.06); border-color: var(--md-outline); color: var(--md-on-surface); }
.btn-md.text-neutral            { background: transparent; color: var(--md-on-surface-var); }
.btn-md.text-neutral:hover      { background: rgba(26,27,31,.06); }

/* ─── Error Banner ───────────────────────────────────────────── */
.md-banner-error {
    display: flex; align-items: flex-start; gap: .75rem;
    padding: .875rem 1.125rem; border-radius: var(--shape-md);
    margin-bottom: 1.25rem; font-size: .875rem; line-height: 1.5;
    background: var(--md-error-cont); color: var(--md-on-error-cont);
    border-left: 4px solid var(--md-error);
}

/* ─── Field Label ────────────────────────────────────────────── */
.ks-field-label {
    display: block; font-size: .75rem; font-weight: 500;
    letter-spacing: .04em; color: var(--md-on-surface-var); margin-bottom: .3rem;
}

/* ─── Section Title ──────────────────────────────────────────── */
.ks-section-title {
    display: flex; align-items: center; gap: .5rem;
    font-size: .75rem; font-weight: 700; letter-spacing: .08em;
    text-transform: uppercase; color: var(--md-primary);
    margin-bottom: 1.125rem; padding-bottom: .625rem;
    border-bottom: 1px solid var(--md-outline-var);
}
.ks-section-title::before {
    content: ''; display: inline-block; width: 8px; height: 8px;
    border-radius: 50%; background: var(--md-primary); flex-shrink: 0;
}

/* ─── Choice Chip (selectable) ───────────────────────────────── */
.choice-chip {
    display: inline-flex; align-items: center; gap: .375rem;
    padding: .4375rem .875rem;
    border-radius: 8px;
    border: 1.5px solid var(--md-outline-var);
    cursor: pointer; font-size: .8125rem; font-weight: 500;
    color: var(--md-on-surface-var);
    transition: all .15s; user-select: none;
}
.choice-chip:hover { border-color: var(--md-outline); background: rgba(26,27,31,.04); }
.choice-chip-input { display: none; }
.choice-chip:has(.choice-chip-input:checked) {
    background: var(--md-primary-cont);
    color: var(--md-on-primary-cont);
    border-color: transparent;
}
</style>
@endpush

@section('content')
@php
    $currentAges       = old('ages',        $selectedAgeIds      ?? []);
    $currentCategories = old('categories',  $selectedCategoryIds ?? []);
    $currentStyles     = old('contentstyles', $selectedStyleIds  ?? []);
@endphp

<div style="max-width: 1000px;">
    {{-- ── Page Header ─────────────────────────────────────────── --}}
    <header class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="ts-headline-sm mb-0" style="color: var(--md-on-surface);">Edit Kreator</h1>
        <a href="{{ old('redirect_to', url()->previous()) }}" class="btn-md outlined">
            <svg viewBox="0 0 20 20" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M12 16l-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Kembali
        </a>
    </header>

    {{-- ── Validation Errors ───────────────────────────────────── --}}
    @if ($errors->any())
        <div class="md-banner-error">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:.1rem;">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <div>
                <div class="fw-semibold mb-1">Ada data yang belum valid</div>
                <ul class="mb-0 ps-4" style="font-size:.8125rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.kreator.update', $creator->id) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="redirect_to" value="{{ old('redirect_to', url()->previous()) }}">

        {{-- ── Data Utama ──────────────────────────────────────── --}}
        <div class="surf-card p-4 mb-3">
            <div class="ks-section-title">Data Utama</div>
            <div class="row g-3">

                {{-- Username --}}
                <div class="col-12 col-md-4">
                    <label class="ks-field-label">Username</label>
                    <input type="text" name="username"
                        class="form-control @error('username') is-invalid @enderror"
                        value="{{ old('username', $creator->username) }}" required>
                    @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Channel --}}
                <div class="col-12 col-md-4">
                    <label class="ks-field-label">Channel Penjualan</label>
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
                    <label class="ks-field-label">Gender Pengikut</label>
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
                    <label class="ks-field-label">GMV</label>
                    <input type="number" name="gmv"
                        class="form-control @error('gmv') is-invalid @enderror"
                        value="{{ old('gmv', (int)$creator->gmv) }}" required>
                    @error('gmv') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Item Terjual --}}
                <div class="col-12 col-md-4">
                    <label class="ks-field-label">Item Terjual</label>
                    <input type="number" name="itemsterjual"
                        class="form-control @error('itemsterjual') is-invalid @enderror"
                        value="{{ old('itemsterjual', (int)$creator->itemsterjual) }}" required>
                    @error('itemsterjual') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Status Toggles --}}
                <div class="col-12 col-md-4">
                    <label class="ks-field-label d-block">Status</label>
                    <div class="d-flex flex-column gap-2 mt-1">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input @error('top_creator') is-invalid @enderror"
                                type="checkbox" name="top_creator" id="top_creator"
                                role="switch" value="1"
                                {{ old('top_creator', $creator->top_creator) ? 'checked' : '' }}>
                            <label class="form-check-label" for="top_creator">Top Kreator</label>
                            @error('top_creator') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input @error('is_blocked') is-invalid @enderror"
                                type="checkbox" name="is_blocked" id="is_blocked"
                                role="switch" value="1"
                                {{ old('is_blocked', $creator->is_blocked) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_blocked">Blokir Kreator</label>
                            @error('is_blocked') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Umur Pengikut ────────────────────────────────────── --}}
        <div class="surf-card p-4 mb-3">
            <div class="ks-section-title">Umur Pengikut</div>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <p class="ts-body-sm mb-0" style="color: var(--md-on-surface-var);">Boleh pilih lebih dari satu</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @foreach ($ages as $id => $name)
                    <label class="choice-chip">
                        <input class="choice-chip-input" type="checkbox" name="ages[]" value="{{ $id }}"
                            {{ in_array($id, $currentAges) ? 'checked' : '' }}>
                        <span>{{ $name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- ── Kontak WhatsApp ──────────────────────────────────── --}}
        <div class="surf-card p-4 mb-3">
            <div class="ks-section-title">Kontak WhatsApp</div>
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <label class="ks-field-label">Nama WA</label>
                    <input type="text" name="namawa"
                        class="form-control @error('namawa') is-invalid @enderror"
                        value="{{ old('namawa', $creator->namawa) }}">
                    @error('namawa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12 col-md-4">
                    <label class="ks-field-label">Nomor WA</label>
                    <input type="text" name="nomerwa"
                        class="form-control @error('nomerwa') is-invalid @enderror"
                        value="{{ old('nomerwa', $creator->nomerwa) }}">
                    @error('nomerwa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        {{-- ── Kategori ─────────────────────────────────────────── --}}
        <div class="surf-card p-4 mb-3">
            <div class="ks-section-title">Kategori</div>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <label class="ks-field-label mb-0">Pilih Kategori <span style="color: var(--md-error);">*</span></label>
                <span class="ts-body-sm" style="color: var(--md-on-surface-var);">Maksimal 5</span>
            </div>
            <select name="categories[]" class="form-select select2" multiple="multiple"
                    style="width: 100%;" data-placeholder="Pilih Kategori..." data-limit="5">
                @foreach ($categories as $id => $name)
                    <option value="{{ $id }}" {{ in_array($id, $currentCategories) ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
            @error('categories') <div class="ts-body-sm mt-2" style="color: var(--md-error);">{{ $message }}</div> @enderror
        </div>

        {{-- ── Konten Style ─────────────────────────────────────── --}}
        <div class="surf-card p-4 mb-4">
            <div class="ks-section-title">Konten Style</div>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <label class="ks-field-label mb-0">Pilih Konten Style</label>
                <span class="ts-body-sm" style="color: var(--md-on-surface-var);">Maksimal 9</span>
            </div>
            <select name="contentstyles[]" class="form-select select2" multiple="multiple"
                    style="width: 100%;" data-placeholder="Pilih Content Style..." data-limit="9">
                @foreach ($contentStyles as $id => $name)
                    <option value="{{ $id }}" {{ in_array($id, $currentStyles) ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
            @error('contentstyles') <div class="ts-body-sm mt-2" style="color: var(--md-error);">{{ $message }}</div> @enderror
        </div>

        {{-- ── Actions ──────────────────────────────────────────── --}}
        <div class="d-flex justify-content-end gap-2 mb-5">
            <a href="{{ old('redirect_to', url()->previous()) }}" class="btn-md text-neutral">Batal</a>
            <button type="submit" class="btn-md filled-primary">Update Kreator</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
    {{-- jQuery & select2 sudah dimuat global di layouts.admin --}}
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

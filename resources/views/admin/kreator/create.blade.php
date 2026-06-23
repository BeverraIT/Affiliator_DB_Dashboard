@extends('layouts.admin')

@section('title', 'Tambah Kreator')

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

.btn-icon-md {
    display: inline-flex; align-items: center; justify-content: center;
    width: 40px; height: 40px; border-radius: 50%;
    border: none; cursor: pointer; text-decoration: none; transition: all .15s;
}
.btn-icon-md.t-warning       { background: #FFF3E0; color: #BF360C; }
.btn-icon-md.t-warning:hover { background: #FFE0B2; }

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

/* ─── MD3 Dialog ─────────────────────────────────────────────── */
.md-dialog .modal-content {
    border-radius: var(--shape-xl); border: none;
    box-shadow: 0 4px 24px rgba(0,0,0,.12);
}
.md-dialog .modal-header { padding: 1.25rem 1.5rem .75rem; border-bottom: none; }
.md-dialog .modal-title  { font-size: 1.125rem; font-weight: 600; color: var(--md-on-surface); }
.md-dialog .modal-body   { padding: .375rem 1.5rem 1rem; color: var(--md-on-surface-var); font-size: .9375rem; line-height: 1.5; }
.md-dialog .modal-footer { padding: .5rem 1.5rem 1.25rem; border-top: none; }
</style>
@endpush

@section('content')
@php
    $oldAges       = old('ages', []);
    $oldCategories = old('categories', []);
    $oldStyles     = old('contentstyles', []);
@endphp

<div style="max-width: 1000px;">
    {{-- ── Page Header ─────────────────────────────────────────── --}}
    <header class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="ts-headline-sm mb-0" style="color: var(--md-on-surface);">Tambah Kreator</h1>
        <a href="{{ url()->previous() }}" class="btn-md outlined">
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

    <form action="{{ route('admin.kreator.store') }}" method="POST">
        @csrf

        {{-- ── Data Utama ──────────────────────────────────────── --}}
        <div class="surf-card p-4 mb-3">
            <div class="ks-section-title">Data Utama</div>
            <div class="row g-3">

                {{-- USERNAME --}}
                <div class="col-12 col-md-4">
                    <label for="username" class="ks-field-label">Username <span style="color: var(--md-error);">*</span></label>
                    <div class="input-group">
                        <input type="text" name="username" id="username"
                            class="form-control @error('username') is-invalid @enderror"
                            value="{{ old('username') }}" placeholder="contoh: tiktok_fashion"
                            autocomplete="off" required>
                        <button type="button"
                            class="btn-icon-md t-warning"
                            id="btn-autofill"
                            style="border-radius: 0 var(--shape-sm) var(--shape-sm) 0; width:auto; padding: 0 .875rem;"
                            title="Auto Isi" aria-label="Auto Isi">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                            </svg>
                        </button>
                    </div>
                    @error('username')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @else
                        <div class="ts-body-sm mt-1" style="color: var(--md-on-surface-var);" id="username-help">Huruf kecil. Tanpa spasi.</div>
                    @enderror
                    <div class="invalid-feedback" id="ajax-username-error" style="display: none;"></div>
                </div>

                {{-- CHANNEL --}}
                <div class="col-12 col-md-4">
                    <label for="channel" class="ks-field-label">Channel Penjualan <span style="color: var(--md-error);">*</span></label>
                    <select name="channel" id="channel" class="form-select @error('channel') is-invalid @enderror" required>
                        <option value="">Pilih Channel Penjualan</option>
                        <option value="video"       {{ old('channel') === 'video'       ? 'selected' : '' }}>Video</option>
                        <option value="live"        {{ old('channel') === 'live'        ? 'selected' : '' }}>Live</option>
                        <option value="kartu produk"{{ old('channel') === 'kartu produk'? 'selected' : '' }}>Kartu Produk</option>
                    </select>
                    @error('channel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- GENDER --}}
                <div class="col-12 col-md-4">
                    <label for="genderpengikut" class="ks-field-label">Gender Pengikut <span style="color: var(--md-error);">*</span></label>
                    <select name="genderpengikut" id="genderpengikut" class="form-select @error('genderpengikut') is-invalid @enderror" required>
                        <option value="">Pilih Gender</option>
                        <option value="perempuan" {{ old('genderpengikut') === 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                        <option value="laki-laki" {{ old('genderpengikut') === 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="seimbang"  {{ old('genderpengikut') === 'seimbang'  ? 'selected' : '' }}>Seimbang</option>
                    </select>
                    @error('genderpengikut') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- GMV --}}
                <div class="col-12 col-md-4">
                    <label for="gmv" class="ks-field-label">GMV <span style="color: var(--md-error);">*</span></label>
                    <input type="number" name="gmv" id="gmv"
                        class="form-control @error('gmv') is-invalid @enderror"
                        value="{{ old('gmv', 0) }}" min="0" required>
                    @error('gmv') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- ITEM TERJUAL --}}
                <div class="col-12 col-md-4">
                    <label for="itemsterjual" class="ks-field-label">Item Terjual <span style="color: var(--md-error);">*</span></label>
                    <input type="number" name="itemsterjual" id="itemsterjual"
                        class="form-control @error('itemsterjual') is-invalid @enderror"
                        value="{{ old('itemsterjual', 0) }}" min="0" required>
                    @error('itemsterjual') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- TOP KREATOR --}}
                <div class="col-12 col-md-4">
                    <label class="ks-field-label d-block">Status</label>
                    <div class="form-check form-switch mt-1">
                        <input class="form-check-input @error('top_creator') is-invalid @enderror"
                            type="checkbox" name="top_creator" id="top_creator"
                            role="switch" value="1"
                            {{ old('top_creator') ? 'checked' : '' }}>
                        <label class="form-check-label" for="top_creator">Top Kreator</label>
                        @error('top_creator') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
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
                @foreach ($ages as $age)
                    <label class="choice-chip">
                        <input class="choice-chip-input" type="checkbox" name="ages[]" value="{{ $age->id }}"
                            {{ in_array($age->id, $oldAges) ? 'checked' : '' }}>
                        <span>{{ $age->name }}</span>
                    </label>
                @endforeach
            </div>
            @error('ages') <div class="ts-body-sm mt-2" style="color: var(--md-error);">{{ $message }}</div> @enderror
        </div>

        {{-- ── Kontak WhatsApp ──────────────────────────────────── --}}
        <div class="surf-card p-4 mb-3">
            <div class="ks-section-title">Kontak WhatsApp</div>
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <label for="namawa" class="ks-field-label">Nama WA</label>
                    <input type="text" name="namawa" id="namawa"
                        class="form-control @error('namawa') is-invalid @enderror"
                        value="{{ old('namawa') }}">
                    @error('namawa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12 col-md-4">
                    <label for="nomerwa" class="ks-field-label">Nomor WA</label>
                    <input type="text" name="nomerwa" id="nomerwa"
                        class="form-control @error('nomerwa') is-invalid @enderror"
                        value="{{ old('nomerwa') }}" placeholder="081234567890">
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
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ in_array($category->id, $oldCategories) ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('categories')   <div class="ts-body-sm mt-2" style="color: var(--md-error);">{{ $message }}</div> @enderror
            @error('categories.*') <div class="ts-body-sm mt-2" style="color: var(--md-error);">{{ $message }}</div> @enderror
        </div>

        {{-- ── Konten Style ─────────────────────────────────────── --}}
        <div class="surf-card p-4 mb-4">
            <div class="ks-section-title">Konten Style</div>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <label class="ks-field-label mb-0">Pilih Konten Style <span style="color: var(--md-error);">*</span></label>
                <span class="ts-body-sm" style="color: var(--md-on-surface-var);">Maksimal 9</span>
            </div>
            <select name="contentstyles[]" class="form-select select2" multiple="multiple"
                    style="width: 100%;" data-placeholder="Pilih Content Style..." data-limit="9">
                @foreach ($contentStyles as $style)
                    <option value="{{ $style->id }}" {{ in_array($style->id, $oldStyles) ? 'selected' : '' }}>
                        {{ $style->name }}
                    </option>
                @endforeach
            </select>
            @error('contentstyles') <div class="ts-body-sm mt-2" style="color: var(--md-error);">{{ $message }}</div> @enderror
        </div>

        {{-- ── Actions ──────────────────────────────────────────── --}}
        <div class="d-flex justify-content-end gap-2 mb-5">
            <a href="{{ url()->previous() }}" class="btn-md text-neutral">Batal</a>
            <button type="submit" class="btn-md filled-primary">Simpan</button>
        </div>
    </form>

    {{-- ── Autofill Toast ───────────────────────────────────────── --}}
    <div class="position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 1050">
        <div id="autofill-toast" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body fw-semibold">Data tidak ditemukan, silakan isi manual.</div>
            </div>
        </div>
    </div>

    {{-- ── Modal: Username sudah ada ───────────────────────────── --}}
    @if (session('username_exists'))
        <div class="modal fade md-dialog show" style="display:block;" tabindex="-1" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Kreator sudah ada</h5>
                        <a href="{{ route('admin.kreator.create') }}" class="btn-close" aria-label="Tutup"></a>
                    </div>
                    <div class="modal-body">
                        Username <strong>{{ session('username_value') }}</strong> sudah tersimpan di database.
                    </div>
                    <div class="modal-footer d-flex gap-2 justify-content-end">
                        <a href="{{ route('admin.kreator.index') }}" class="btn-md outlined">Lihat daftar</a>
                        <a href="{{ route('admin.kreator.create') }}" class="btn-md filled-primary">Input ulang</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif
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
                    maximumSelectionLength: limit,
                });
            });

            $('#username').on('blur', function() {
                let username = $(this).val();
                let inputField = $(this);
                let ajaxErrorDiv = $('#ajax-username-error');
                let helpText = $('#username-help');

                username = username.replace(/\s+/g, '');
                inputField.val(username);

                if (username.length > 0) {
                    $.ajax({
                        url: '{{ route("admin.kreator.check-username") }}',
                        type: 'GET',
                        data: { username: username },
                        success: function(response) {
                            if (response.exists) {
                                inputField.addClass('is-invalid');
                                ajaxErrorDiv.text('Username ini sudah terdaftar di database!').show();
                                helpText.hide();
                                $('button[type="submit"]').prop('disabled', true);
                            } else {
                                inputField.removeClass('is-invalid').addClass('is-valid');
                                ajaxErrorDiv.hide();
                                helpText.show();
                                $('button[type="submit"]').prop('disabled', false);
                            }
                        },
                        error: function() {
                            console.log("Terjadi kesalahan saat mengecek username.");
                        }
                    });
                } else {
                    inputField.removeClass('is-invalid is-valid');
                    ajaxErrorDiv.hide();
                    helpText.show();
                    $('button[type="submit"]').prop('disabled', false);
                }
            });

            $('#btn-autofill').click(function() {
                let username = $('#username').val().trim();
                if (!username) {
                    alert("Mohon isi username terlebih dahulu!");
                    $('#username').focus();
                    return;
                }
                let btn = $(this);
                let originalHtml = btn.html();
                btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>').prop('disabled', true);

                $.ajax({
                    url: '{{ route("admin.kreator.auto-fill") }}',
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}', username: username },
                    success: function(response) {
                        btn.html(originalHtml).prop('disabled', false);
                        if (response.success) {
                            $('#channel').val(response.channel).trigger('change');
                            $('#genderpengikut').val(response.gender).trigger('change');
                            $('#gmv').val(response.gmv);
                            $('#itemsterjual').val(response.item_terjual);
                            $('input[name="ages[]"]').prop('checked', false);
                            if (response.umur && response.umur.length > 0) {
                                response.umur.forEach(function(ageString) {
                                    $('input[name="ages[]"]').each(function() {
                                        let labelText = $(this).siblings('span').text().trim();
                                        if (labelText === ageString) { $(this).prop('checked', true); }
                                    });
                                });
                            }
                            $('select[name="categories[]"]').val(null).trigger('change');
                            let matchedCategories = [];
                            if (response.kategori && response.kategori.length > 0) {
                                let apiCategories = response.kategori.slice(0, 5);
                                $('select[name="categories[]"] option').each(function() {
                                    let rawOptionText = $(this).text().toLowerCase();
                                    let cleanOptionText = rawOptionText.replace(/&amp;/g, '').replace(/dan/g, '').replace(/[^a-z0-9]/g, '');
                                    let isMatch = apiCategories.some(function(apiCat) {
                                        let cleanApiCat = apiCat.toLowerCase().replace(/&amp;/g, '').replace(/dan/g, '').replace(/[^a-z0-9]/g, '');
                                        return cleanApiCat === cleanOptionText;
                                    });
                                    if (isMatch) { matchedCategories.push($(this).val()); }
                                });
                            }
                            $('select[name="categories[]"]').val(matchedCategories).trigger('change');
                        } else {
                            new bootstrap.Toast(document.getElementById('autofill-toast'), { delay: 3000 }).show();
                        }
                    },
                    error: function(xhr, status, error) {
                        btn.html(originalHtml).prop('disabled', false);
                        alert("Terjadi kesalahan. Cek inspect element -> console untuk detailnya.");
                        console.error("Detail Error:", xhr.responseText);
                    }
                });
            });
        });
    </script>
@endpush

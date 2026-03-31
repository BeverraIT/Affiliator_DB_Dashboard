@extends('layouts.admin')

@section('title', 'Tambah Kreator')

@section('content')
@php
    $oldAges       = old('ages', []);
    $oldCategories = old('categories', []);
    $oldStyles     = old('contentstyles', []);
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Tambah Kreator</h1>

    {{-- BUTTON KEMBALI --}}
    <a href="{{ url()->previous() }}"
        class="btn btn-outline-secondary rounded-circle d-inline-flex align-items-center justify-content-center"
        style="width:44px;height:44px;" aria-label="Kembali" title="Kembali">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
            <path d="M15 8a.5.5 0 0 1-.5.5H3.707l3.147 3.146a.5.5 0 0 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L3.707 7.5H14.5A.5.5 0 0 1 15 8z"/>
        </svg>
    </a>
</div>

<div style="max-width: 1000px;">
    @if ($errors->any())
        <div class="alert alert-danger py-2">
            <div class="fw-semibold">Terjadi kesalahan:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li class="small">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.kreator.store') }}" method="POST">
        @csrf
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-4">
                <div class="row g-3">
                    {{-- USERNAME --}}
                    <div class="col-12 col-md-4">
                        <label for="username" class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="username" id="username"
                                class="form-control @error('username') is-invalid @enderror"
                                value="{{ old('username') }}" placeholder="contoh: tiktok_fashion" autocomplete="off" required>

                            <button type="button" class="btn btn-warning fw-semibold d-flex align-items-center justify-content-center" id="btn-autofill" title="Auto Isi" aria-label="Auto Isi">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                                </svg>
                            </button>
                        </div>
                        
                        @error('username') 
                            <div class="invalid-feedback d-block">{{ $message }}</div> 
                        @else 
                            <div class="form-text" id="username-help">Huruf kecil. Tanpa spasi.</div> 
                        @enderror
                        <div class="invalid-feedback" id="ajax-username-error" style="display: none;"></div>
                    </div>

                    {{-- CHANNEL --}}
                    <div class="col-12 col-md-4">
                        <label for="channel" class="form-label fw-semibold">Channel Penjualan <span class="text-danger">*</span></label>
                        <select name="channel" id="channel" class="form-select @error('channel') is-invalid @enderror" required>
                            <option value="">Pilih Channel Penjualan</option>
                            <option value="video"    {{ old('channel') === 'video' ? 'selected' : '' }}>Video</option>
                            <option value="live"     {{ old('channel') === 'live' ? 'selected' : '' }}>Live</option>
                            <option value="kartu produk" {{ old('channel') === 'kartu produk' ? 'selected' : '' }}>Kartu Produk</option>
                        </select>
                        @error('channel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- GENDER --}}
                    <div class="col-12 col-md-4">
                        <label for="genderpengikut" class="form-label fw-semibold">Gender Pengikut <span class="text-danger">*</span></label>
                        <select name="genderpengikut" id="genderpengikut" class="form-select @error('genderpengikut') is-invalid @enderror" required>
                            <option value="">Pilih Gender</option>
                            <option value="perempuan" {{ old('genderpengikut') === 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                            <option value="laki-laki" {{ old('genderpengikut') === 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="seimbang"  {{ old('genderpengikut') === 'seimbang' ? 'selected' : '' }}>Seimbang</option>
                        </select>
                        @error('genderpengikut') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- GMV --}}
                    <div class="col-12 col-md-4">
                        <label for="gmv" class="form-label fw-semibold">GMV <span class="text-danger">*</span></label>
                        <input type="number" name="gmv" id="gmv" class="form-control @error('gmv') is-invalid @enderror"
                            value="{{ old('gmv', 0) }}" min="0" required>
                        @error('gmv') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- ITEM TERJUAL --}}
                    <div class="col-12 col-md-4">
                        <label for="itemsterjual" class="form-label fw-semibold">Item Terjual <span class="text-danger">*</span></label>
                        <input type="number" name="itemsterjual" id="itemsterjual" class="form-control @error('itemsterjual') is-invalid @enderror"
                            value="{{ old('itemsterjual', 0) }}" min="0" required>
                        @error('itemsterjual') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- TOP KREATOR --}}
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold d-block">STATUS</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input @error('top_creator') is-invalid @enderror" 
                                type="checkbox" 
                                name="top_creator" 
                                id="top_creator" 
                                role="switch"
                                value="1"
                                {{ old('top_creator') ? 'checked' : '' }}>
                            <label class="form-check-label" for="top_creator">Top Kreator</label>
                            
                            @error('top_creator') 
                                <div class="invalid-feedback d-block">{{ $message }}</div> 
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Umur --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="fw-semibold">Umur Pengikut <span class="text-danger">*</span></div>
                    <div class="text-secondary small">Boleh pilih lebih dari satu</div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach ($ages as $age)
                        <label class="badge rounded-pill text-bg-light border text-secondary d-inline-flex align-items-center gap-2 px-3 py-2 user-select-none" style="cursor:pointer;">
                            <input class="form-check-input m-0" type="checkbox" name="ages[]" value="{{ $age->id }}"
                                style="transform:scale(1.05);" {{ in_array($age->id, $oldAges) ? 'checked' : '' }}>
                            <span class="fw-medium">{{ $age->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('ages') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- Kontak --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-4">
                <div class="fw-semibold mb-2">Kontak WhatsApp</div>
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label for="namawa" class="form-label fw-semibold">Nama WA</label>
                        <input type="text" name="namawa" id="namawa" class="form-control @error('namawa') is-invalid @enderror"
                            value="{{ old('namawa') }}">
                        @error('namawa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="nomerwa" class="form-label fw-semibold">Nomor WA</label>
                        <input type="text" name="nomerwa" id="nomerwa" class="form-control @error('nomerwa') is-invalid @enderror"
                            value="{{ old('nomerwa') }}" placeholder="081234567890">
                        @error('nomerwa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Kategori --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-4">
                <div class="mb-2">
                    <label class="fw-semibold form-label">Kategori <span class="text-danger">*</span></label>
                    <span class="text-secondary small ms-1">(Maksimal 5)</span>
                </div>
                <select name="categories[]" class="form-select select2" multiple="multiple" 
                        style="width: 100%;" data-placeholder="Pilih Kategori..." data-limit="5">
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ in_array($category->id, $oldCategories) ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                
                @error('categories') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                @error('categories.*') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- Konten Style --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-4">
                <div class="mb-2">
                    <label class="fw-semibold form-label">Konten Style <span class="text-danger">*</span></label>
                    <span class="text-secondary small ms-1">(Maksimal 9)</span>
                </div>
                <select name="contentstyles[]" class="form-select select2" multiple="multiple" 
                        style="width: 100%;" data-placeholder="Pilih Content Style..." data-limit="9">
                    @foreach ($contentStyles as $style)
                        <option value="{{ $style->id }}" {{ in_array($style->id, $oldStyles) ? 'selected' : '' }}>
                            {{ $style->name }}
                        </option>
                    @endforeach
                </select>

                @error('contentstyles') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- BUTTON SIMPAN --}}
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>

    <div class="position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 1050">
        <div id="autofill-toast" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body fw-semibold">
                    Data tidak ditemukan, silakan isi manual.
                </div>
            </div>
        </div>
    </div>

    @if (session('username_exists'))
        <div class="modal fade show" style="display:block;" tabindex="-1" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Kreator sudah ada</h5>
                        <a href="{{ route('admin.kreator.create') }}" class="btn-close" aria-label="Tutup"></a>
                    </div>
                    <div class="modal-body">
                        Username <strong>{{ session('username_value') }}</strong> sudah tersimpan di database.
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('admin.kreator.index') }}" class="btn btn-outline-secondary">Lihat daftar</a>
                        <a href="{{ route('admin.kreator.create') }}" class="btn btn-primary">Input ulang</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif
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

            $('#username').on('blur', function() {
                let username = $(this).val();
                let inputField = $(this);
                let ajaxErrorDiv = $('#ajax-username-error');
                let helpText = $('#username-help');

                // Hapus spasi jika user tidak sengaja mengetik spasi
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
                                inputField.removeClass('is-invalid');
                                inputField.addClass('is-valid');
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
                    data: {
                        _token: '{{ csrf_token() }}',
                        username: username
                    },
                    success: function(response) {
                        btn.html(originalHtml).prop('disabled', false);

                        if (response.success) {
                            // Update 1: Channel
                            $('#channel').val(response.channel).trigger('change');

                            // Update 2: Gender
                            $('#genderpengikut').val(response.gender).trigger('change');

                            $('#gmv').val(response.gmv);
                            $('#itemsterjual').val(response.item_terjual);

                            // Update 3: Umur (Checkbox)
                            $('input[name="ages[]"]').prop('checked', false);
                            if (response.umur && response.umur.length > 0) {
                                response.umur.forEach(function(ageString) {
                                    $('input[name="ages[]"]').each(function() {
                                        let labelText = $(this).siblings('span').text().trim();
                                        if (labelText === ageString) {
                                            $(this).prop('checked', true);
                                        }
                                    });
                                });
                            }

                            // Update 4: Kategori (Select2)
                            $('select[name="categories[]"]').val(null).trigger('change');
                            let matchedCategories = [];
                            
                            if (response.kategori && response.kategori.length > 0) {
                                // MENGAMBIL HANYA 5 KATEGORI PERTAMA DARI API
                                let apiCategories = response.kategori.slice(0, 5);
                                
                                console.log("TOP 5 Kategori dari API:", apiCategories);

                                $('select[name="categories[]"] option').each(function() {
                                    let optionVal = $(this).val();
                                    let rawOptionText = $(this).text().toLowerCase();
                                    
                                    // PEMBERSIHAN EKSTREM: Hapus semua karakter yang bukan huruf a-z dan angka
                                    // Contoh: "Koper & Tas" -> "kopertas"
                                    let cleanOptionText = rawOptionText.replace(/&amp;/g, '').replace(/dan/g, '').replace(/[^a-z0-9]/g, '');
                                    
                                    let isMatch = apiCategories.some(function(apiCat) {
                                        // PEMBERSIHAN EKSTREM PADA DATA API
                                        let cleanApiCat = apiCat.toLowerCase().replace(/&amp;/g, '').replace(/dan/g, '').replace(/[^a-z0-9]/g, '');
                                        return cleanApiCat === cleanOptionText;
                                    });
                                    
                                    if (isMatch) {
                                        matchedCategories.push(optionVal); 
                                    }
                                });
                                
                                console.log("Kategori yang COCOK dengan database:", matchedCategories);
                            }
                            
                            // Masukkan ke dalam Select2
                            $('select[name="categories[]"]').val(matchedCategories).trigger('change');

                        } else {
                            let toastEl = new bootstrap.Toast(document.getElementById('autofill-toast'), {
                                delay: 3000
                            });
                            toastEl.show();
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
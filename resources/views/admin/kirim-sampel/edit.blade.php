@extends('layouts.admin')

@section('title', 'Edit Kirim Sampel')

@push('styles')
    {{-- select2 (CSS + tema bootstrap-5) sudah dimuat global di layouts.admin --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
    /* ── MD3 Design Tokens ───────────────────────────────────── */

    /* ── Type Scale ──────────────────────────────────────────── */
    .ts-headline-sm { font-size: 1.5rem; font-weight: 600; line-height: 2rem; }
    .ts-body-sm     { font-size: .75rem; font-weight: 400; line-height: 1rem; letter-spacing: .025em; }

    /* ── Surface Card ────────────────────────────────────────── */
    .surf-card { background: #fff; border-radius: var(--shape-lg); box-shadow: var(--md-elev-1); border: none; }

    /* ── MD3 Buttons ─────────────────────────────────────────── */
    .btn-md {
        display: inline-flex; align-items: center; justify-content: center; gap: .375rem;
        padding: .5625rem 1.25rem; border-radius: 20px;
        font-size: .875rem; font-weight: 500;
        border: none; cursor: pointer; text-decoration: none; transition: all .15s;
    }
    .btn-md:focus-visible { outline: 2px solid var(--md-primary); outline-offset: 2px; }
    .btn-md.filled-primary        { background: var(--md-primary); color: var(--md-on-primary); }
    .btn-md.filled-primary:hover  { background: #1255A6; box-shadow: 0 2px 8px rgba(21,101,192,.35); color: var(--md-on-primary); }
    .btn-md.tonal-secondary       { background: var(--md-secondary-cont); color: var(--md-secondary); }
    .btn-md.tonal-secondary:hover { background: #B5D9B7; }
    .btn-md.outlined              { background: transparent; color: var(--md-on-surface-var); border: 1.5px solid var(--md-outline-var); }
    .btn-md.outlined:hover        { background: rgba(26,27,31,.06); border-color: var(--md-outline); color: var(--md-on-surface); }
    .btn-md.text-neutral          { background: transparent; color: var(--md-on-surface-var); }
    .btn-md.text-neutral:hover    { background: rgba(26,27,31,.06); }

    /* ── Error Banner ────────────────────────────────────────── */
    .md-banner-error {
        display: flex; align-items: flex-start; gap: .75rem;
        padding: .875rem 1.125rem; border-radius: var(--shape-md); margin-bottom: 1.25rem;
        font-size: .875rem; line-height: 1.5;
        background: var(--md-error-cont); color: var(--md-on-error-cont);
        border-left: 4px solid var(--md-error);
    }

    /* ── Form field labels ───────────────────────────────────── */
    .ks-field-label {
        display: block; font-size: .75rem; font-weight: 500;
        letter-spacing: .04em; color: var(--md-on-surface-var);
        margin-bottom: .3rem;
    }

    /* ── Section heading inside card ─────────────────────────── */
    .ks-section-title {
        display: flex; align-items: center; gap: .5rem;
        font-size: .75rem; font-weight: 700; letter-spacing: .08em;
        text-transform: uppercase; color: var(--md-primary);
        margin-bottom: 1rem; padding-bottom: .625rem;
        border-bottom: 1px solid var(--md-outline-var);
    }
    .ks-section-title::before {
        content: ''; display: inline-block; width: 8px; height: 8px;
        border-radius: 50%; background: var(--md-primary); flex-shrink: 0;
    }

    /* ── Toko: chip radio (selalu tampil, pilih satu) ────────── */
    .ks-chip-group { display: flex; flex-wrap: wrap; gap: .5rem; }
    .ks-chip { position: relative; }
    .ks-chip input { position: absolute; opacity: 0; width: 0; height: 0; }
    .ks-chip label {
        display: inline-flex; align-items: center; gap: .375rem;
        padding: .4375rem .875rem; border-radius: 20px; cursor: pointer;
        font-size: .8125rem; font-weight: 500; line-height: 1.2;
        color: var(--md-on-surface-var);
        background: var(--md-surface-var);
        border: 1.5px solid var(--md-outline-var);
        transition: all .12s;
    }
    .ks-chip label:hover { background: rgba(26,27,31,.06); }
    .ks-chip input:checked + label {
        background: var(--md-secondary-cont); color: var(--md-secondary);
        border-color: var(--md-secondary);
    }
    .ks-chip input:focus-visible + label { outline: 2px solid var(--md-primary); outline-offset: 2px; }
    .ks-chip .ks-chip-check { display: none; }
    .ks-chip input:checked + label .ks-chip-check { display: inline-flex; }

    /* ── Inline field error ──────────────────────────────────── */
    .invalid-feedback.d-block { color: var(--md-error); margin-top: .25rem; }
    .is-invalid + .select2-container .select2-selection { border-color: var(--md-error) !important; }
    </style>
@endpush

@section('content')
    <div style="max-width: 1000px;">
        {{-- ── Page Header ─────────────────────────────────────── --}}
        <header class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="ts-headline-sm mb-0" style="color: var(--md-on-surface);">Edit Kirim Sampel</h1>
            </div>
            <a href="{{ old('redirect_to', url()->previous()) }}" class="btn-md outlined">
                <svg viewBox="0 0 20 20" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M12 16l-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Kembali
            </a>
        </header>

        {{-- ── Validation Errors ───────────────────────────────── --}}
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

        {{-- ── Form ────────────────────────────────────────────── --}}
        <form id="form-kirim-sampel" action="{{ route('admin.kirim-sampel.update', $sendSample->id) }}" method="POST">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="redirect_to" value="{{ old('redirect_to', url()->previous()) }}">

            <div class="row g-4">
                {{-- Left: Informasi Utama --}}
                <div class="col-12 col-lg-7">
                    <div class="surf-card h-100">
                        <div class="p-4">
                            <div class="ks-section-title">Informasi Utama</div>
                            <div class="row g-3">

                                {{-- KREATOR --}}
                                <div class="col-12 col-md-6">
                                    <label class="ks-field-label" for="creator_id">
                                        Kreator <span style="color:var(--md-error);">*</span>
                                    </label>
                                    <select name="creator_id" id="creator_id" class="form-select select2-ajax @error('creator_id') is-invalid @enderror" required
                                        data-url="{{ route('admin.kirim-sampel.search_creators') }}"
                                        data-placeholder="Cari kreator...">
                                        <option value=""></option>
                                        @if($selectedCreator)
                                            <option value="{{ $selectedCreator->id }}" selected>{{ $selectedCreator->username }}</option>
                                        @endif
                                    </select>
                                    @error('creator_id')<div class="invalid-feedback d-block ts-body-sm">{{ $message }}</div>@enderror
                                </div>

                                {{-- APPROVE --}}
                                <div class="col-12 col-md-6">
                                    <label class="ks-field-label" for="approve">
                                        Approve <span style="color:var(--md-error);">*</span>
                                    </label>
                                    <select name="approve" id="approve" class="form-select select2 @error('approve') is-invalid @enderror">
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" @selected(old('approve', $sendSample->approve) == $user->id)>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('approve')<div class="invalid-feedback d-block ts-body-sm">{{ $message }}</div>@enderror
                                </div>

                                {{-- PIC --}}
                                <div class="col-12 col-md-6">
                                    <label class="ks-field-label" for="pic">
                                        PIC <span style="color:var(--md-error);">*</span>
                                    </label>
                                    <select name="pic" id="pic" class="form-select select2 @error('pic') is-invalid @enderror" required>
                                        <option value="{{ auth()->user()->id }}" selected>{{ auth()->user()->name }} (Anda)</option>
                                        @if(auth()->user()->role === 'super admin')
                                            @foreach($users as $user)
                                                @if($user->id !== auth()->user()->id)
                                                    <option value="{{ $user->id }}" @selected(old('pic', $sendSample->pic) == $user->id)>{{ $user->name }}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('pic')<div class="invalid-feedback d-block ts-body-sm">{{ $message }}</div>@enderror
                                </div>

                                {{-- TOKO (chip radio: langsung tampil, pilih satu) --}}
                                <div class="col-12">
                                    <label class="ks-field-label">
                                        Toko <span style="color:var(--md-error);">*</span>
                                    </label>
                                    <div class="ks-chip-group" role="radiogroup" aria-label="Toko">
                                        @foreach($stores as $store)
                                            <div class="ks-chip">
                                                <input type="radio" name="store_id" id="store_{{ $store->id }}"
                                                    value="{{ $store->id }}" required
                                                    @checked(old('store_id', $sendSample->store_id) == $store->id)>
                                                <label for="store_{{ $store->id }}">
                                                    <span class="ks-chip-check">
                                                        <svg viewBox="0 0 20 20" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5">
                                                            <path d="M4 10l4 4 8-8" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                    </span>
                                                    {{ $store->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('store_id')<div class="invalid-feedback d-block ts-body-sm">{{ $message }}</div>@enderror
                                </div>

                                {{-- SKU --}}
                                <div class="col-12 col-md-6">
                                    <label class="ks-field-label" for="product_id">
                                        SKU <span style="color:var(--md-error);">*</span>
                                    </label>
                                    <select name="product_id" id="product_id" class="form-select select2-ajax @error('product_id') is-invalid @enderror" required
                                        data-url="{{ route('admin.kirim-sampel.search_products') }}"
                                        data-placeholder="Cari SKU...">
                                        <option value=""></option>
                                        @if($selectedProduct)
                                            <option value="{{ $selectedProduct->id }}" selected>{{ $selectedProduct->sku }}</option>
                                        @endif
                                    </select>
                                    @error('product_id')<div class="invalid-feedback d-block ts-body-sm">{{ $message }}</div>@enderror
                                </div>

                                {{-- SELLER PRODUCT ID --}}
                                <div class="col-12 col-md-6">
                                    <label class="ks-field-label" for="seller_product_id">Seller Product ID</label>
                                    <input type="text" name="seller_product_id" id="seller_product_id"
                                           value="{{ old('seller_product_id', $sendSample->seller_product_id) }}"
                                           class="form-control" placeholder="Masukkan ID Produk...">
                                </div>

                                {{-- QTY --}}
                                <div class="col-12 col-md-4">
                                    <label class="ks-field-label" for="qty">
                                        Qty <span style="color:var(--md-error);">*</span>
                                    </label>
                                    <input type="number" min="1" name="qty" id="qty"
                                           value="{{ old('qty', $sendSample->qty) }}"
                                           class="form-control @error('qty') is-invalid @enderror" required>
                                    @error('qty')<div class="invalid-feedback d-block ts-body-sm">{{ $message }}</div>@enderror
                                </div>

                                {{-- PERJANJIAN LIVE --}}
                                <div class="col-12 col-md-4">
                                    <label class="ks-field-label" for="perjanjian_livestream">Perjanjian Live</label>
                                    <input type="number" name="perjanjian_livestream" id="perjanjian_livestream"
                                           value="{{ old('perjanjian_livestream', $sendSample->perjanjian_livestream) }}"
                                           class="form-control" placeholder="0">
                                </div>

                                {{-- PERJANJIAN VIDEO --}}
                                <div class="col-12 col-md-4">
                                    <label class="ks-field-label" for="perjanjian_video">Perjanjian Video</label>
                                    <input type="number" name="perjanjian_video" id="perjanjian_video"
                                           value="{{ old('perjanjian_video', $sendSample->perjanjian_video) }}"
                                           class="form-control" placeholder="0">
                                </div>

                                {{-- LIVE DIBUAT --}}
                                <div class="col-12 col-md-6">
                                    <label class="ks-field-label" for="live_dibuat">Live Dibuat</label>
                                    <input type="number" name="live_dibuat" id="live_dibuat"
                                           value="{{ old('live_dibuat', $sendSample->live_dibuat) }}"
                                           class="form-control" placeholder="0">
                                </div>

                                {{-- VIDEO DIBUAT --}}
                                <div class="col-12 col-md-6">
                                    <label class="ks-field-label" for="video_dibuat">Video Dibuat</label>
                                    <input type="number" name="video_dibuat" id="video_dibuat"
                                           value="{{ old('video_dibuat', $sendSample->video_dibuat) }}"
                                           class="form-control" placeholder="0">
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: Timeline --}}
                <div class="col-12 col-lg-5">
                    <div class="surf-card h-100">
                        <div class="p-4 d-flex flex-column h-100">
                            <div class="ks-section-title">Timeline Pengiriman</div>
                            <div class="row g-3">

                                {{-- TANGGAL DEAL --}}
                                <div class="col-6">
                                    <label class="ks-field-label" for="deal_date">Tanggal Deal</label>
                                    <input type="date" name="deal_date" id="deal_date"
                                           value="{{ old('deal_date', optional($sendSample->deal_date)->format('Y-m-d')) }}"
                                           class="form-control @error('deal_date') is-invalid @enderror">
                                    @error('deal_date')<div class="invalid-feedback d-block ts-body-sm">{{ $message }}</div>@enderror
                                </div>

                                {{-- TANGGAL KIRIM --}}
                                <div class="col-6">
                                    <label class="ks-field-label" for="tanggal_kirim">
                                        Tanggal Kirim <span style="color:var(--md-error);">*</span>
                                    </label>
                                    <input type="date" name="tanggal_kirim" id="tanggal_kirim" required
                                           value="{{ old('tanggal_kirim', optional($sendSample->tanggal_kirim)->format('Y-m-d')) }}"
                                           class="form-control @error('tanggal_kirim') is-invalid @enderror">
                                    @error('tanggal_kirim')<div class="invalid-feedback d-block ts-body-sm">{{ $message }}</div>@enderror
                                </div>

                                {{-- ESTIMASI SAMPAI --}}
                                <div class="col-6">
                                    <label class="ks-field-label" for="estimasi_sampai">Estimasi Sampai</label>
                                    <input type="date" name="estimasi_sampai" id="estimasi_sampai"
                                           value="{{ old('estimasi_sampai', optional($sendSample->estimasi_sampai)->format('Y-m-d')) }}"
                                           class="form-control" style="background:var(--md-surface-var); color:var(--md-on-surface-var);" readonly>
                                </div>

                                {{-- TANGGAL AKTUAL --}}
                                <div class="col-6">
                                    <label class="ks-field-label" for="tanggal_aktual">Tanggal Aktual</label>
                                    <input type="date" name="tanggal_aktual" id="tanggal_aktual"
                                           value="{{ old('tanggal_aktual', optional($sendSample->tanggal_aktual)->format('Y-m-d')) }}"
                                           class="form-control">
                                </div>

                                {{-- ID Pesanan --}}
                                <div class="col-12">
                                    <label class="ks-field-label" for="id_pesanan">ID Pesanan</label>
                                    <input type="text" name="id_pesanan" id="id_pesanan"
                                           value="{{ old('id_pesanan', $sendSample->id_pesanan) }}"
                                           class="form-control" placeholder="Masukkan nomor id pesanan..." autocomplete="off">
                                </div>

                            </div>

                            {{-- Action Buttons --}}
                            <div class="mt-auto pt-4 d-flex justify-content-end gap-2 flex-wrap">
                                <a href="{{ old('redirect_to', url()->previous()) }}" class="btn-md text-neutral">Batal</a>

                                <button type="button" id="btn-duplicate" class="btn-md tonal-secondary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M13 0H6a2 2 0 0 0-2 2 2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2 2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm0 13V4a2 2 0 0 0-2-2H5a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1zM3 4a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4z"/>
                                    </svg>
                                    Duplikat
                                </button>

                                <button type="submit" id="btn-update" class="btn-md filled-primary">
                                    <svg viewBox="0 0 20 20" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M4 13l4 4L16 7" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <span class="btn-label">Update</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    {{-- jQuery & select2 sudah dimuat global di layouts.admin --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: $(this).data('placeholder'),
                allowClear: true
            });

            // Dropdown besar (kreator & produk): pencarian server-side via AJAX
            $('.select2-ajax').each(function () {
                $(this).select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: $(this).data('placeholder'),
                    allowClear: true,
                    minimumInputLength: 0,
                    ajax: {
                        url: $(this).data('url'),
                        dataType: 'json',
                        delay: 250,
                        data: params => ({ q: params.term }),
                        processResults: data => ({ results: data.results }),
                        cache: true
                    }
                });
            });

            // Fokus otomatis ke kotak cari saat dropdown Select2 dibuka (cukup 1 klik)
            $(document).on('select2:open', () => {
                document.querySelector('.select2-container--open .select2-search__field')?.focus();
            });

            // Cegah double-submit (tetap kompatibel dgn tombol Duplikat)
            $('#form-kirim-sampel').on('submit', function () {
                $('#btn-update').prop('disabled', true).css('opacity', .7)
                    .find('.btn-label').text('Menyimpan...');
            });

            $('#tanggal_kirim').on('change', function() {
                var inputDate = $(this).val();
                if (inputDate) {
                    var date = new Date(inputDate);
                    date.setDate(date.getDate() + 7);
                    var day   = ('0' + date.getDate()).slice(-2);
                    var month = ('0' + (date.getMonth() + 1)).slice(-2);
                    var year  = date.getFullYear();
                    $('#estimasi_sampai').val(year + '-' + month + '-' + day);
                } else {
                    $('#estimasi_sampai').val('');
                }
            });

            $('#btn-duplicate').on('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Duplikasi Data?',
                    text: 'Data ini akan disimpan sebagai entri baru. Anda yakin?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#1B5E20',
                    cancelButtonColor: '#74777F',
                    confirmButtonText: 'Ya, Duplikasi!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        var $form = $('#form-kirim-sampel');
                        $form.attr('action', "{{ route('admin.kirim-sampel.store') }}");
                        $('input[name="_method"]').val('POST');
                        $form.submit();
                    }
                });
            });
        });
    </script>
@endpush

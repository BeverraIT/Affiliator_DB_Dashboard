@extends('layouts.admin')

@section('title', 'Edit Kirim Sampel')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')
    <div class="container" style="max-width: 1000px;">
        <header class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <h1 class="h3 fw-bold mb-1 text-dark">Edit Kirim Sampel</h1>
            </div>

            {{-- BUTTON KEMBALI --}}
            <a href="{{ old('redirect_to', url()->previous()) }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                Kembali
            </a>
        </header>

        @if ($errors->any())
            <div class="alert alert-danger py-2 mb-4 shadow-sm border-0">
                <div class="fw-bold mb-1 d-flex align-items-center gap-2">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    Ada data yang belum valid
                </div>
                <ul class="mb-0 small ps-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FORM --}}
        <form id="form-kirim-sampel" action="{{ route('admin.kirim-sampel.update', $sendSample->id) }}" method="POST">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="redirect_to" value="{{ old('redirect_to', url()->previous()) }}">
            <div class="row g-4">
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm border-top border-3 border-primary h-100">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-2 text-primary">Informasi Utama</h5>
                            <div class="row g-3">
                                {{-- KREATOR --}}
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-secondary" for="creator_id">Kreator<span class="text-danger">*</span></label>
                                    <select name="creator_id" id="creator_id" class="form-select select2" required>
                                        <option value=""></option>
                                        @foreach($creators as $creator)
                                            <option value="{{ $creator->id }}" @selected(old('creator_id', $sendSample->creator_id) == $creator->id)>
                                                {{ $creator->username ?? $creator->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- APPROVE --}}
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-bold text-secondary" for="pic">APPROVE<span class="text-danger">*</span></label>
                                    <select name="approve" id="approve" class="form-select select2">
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" @selected(old('approve', $sendSample->approve) == $user->id)>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- PIC --}}
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-bold text-secondary" for="pic">PIC <span class="text-danger">*</span></label>
                                    <select name="pic" id="pic" class="form-select select2" required>
                                        <option value="{{ auth()->user()->id }}" selected>
                                            {{ auth()->user()->name }} (Anda)
                                        </option>
                                        @if(auth()->user()->role === 'super admin') 
                                            @foreach($users as $user)
                                                @if($user->id !== auth()->user()->id)
                                                    <option value="{{ $user->id }}" @selected(old('pic', $sendSample->pic) == $user->id)>
                                                        {{ $user->name }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                {{-- TOKO --}}
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-bold text-secondary" for="store_id">Toko<span class="text-danger">*</span></label>
                                    <select name="store_id" id="store_id" class="form-select select2" required>
                                        <option value=""></option>
                                        @foreach($stores as $store)
                                            <option value="{{ $store->id }}" @selected(old('store_id', $sendSample->store_id) == $store->id)>
                                                {{ $store->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- SKU --}}
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-bold text-secondary" for="product_id">SKU<span class="text-danger">*</span></label>
                                    <select name="product_id" id="product_id" class="form-select select2" required>
                                        <option value=""></option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" @selected(old('product_id', $sendSample->product_id) == $product->id)>
                                                {{ $product->sku }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- QTY --}}
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-bold text-secondary" for="qty">Qty<span class="text-danger">*</span></label>
                                    <input type="number" min="1" name="qty" id="qty"
                                           value="{{ old('qty', $sendSample->qty) }}"
                                           class="form-control" required>
                                </div>

                                {{-- PERJANJIAN LIVE --}}
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-secondary" for="perjanjian_livestream">Perjanjian Live</label>
                                    <input type="number" name="perjanjian_livestream" id="perjanjian_livestream"
                                           value="{{ old('perjanjian_livestream', $sendSample->perjanjian_livestream) }}"
                                           class="form-control" placeholder="0">
                                </div>

                                {{-- PERJANJIAN VIDEO --}}
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-secondary" for="perjanjian_video">Perjanjian Video</label>
                                    <input type="number" name="perjanjian_video" id="perjanjian_video"
                                           value="{{ old('perjanjian_video', $sendSample->perjanjian_video) }}"
                                           class="form-control" placeholder="0">
                                </div>

                                {{-- LIVE DIBUAT --}}
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-secondary" for="live_dibuat">Live Dibuat</label>
                                    <input type="number" name="live_dibuat" id="live_dibuat"
                                           value="{{ old('live_dibuat', $sendSample->live_dibuat) }}"
                                           class="form-control" placeholder="0"
                                           readonly>
                                </div>

                                {{-- VIDEO DIBUAT --}}
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-secondary" for="video_dibuat">Video Dibuat</label>
                                    <input type="number" name="video_dibuat" id="video_dibuat"
                                           value="{{ old('video_dibuat', $sendSample->video_dibuat) }}" 
                                           class="form-control" placeholder="0"
                                           readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4 d-flex flex-column">
                            <h5 class="card-title mb-2 text-primary">Timeline Pengiriman</h5> 
                            <div class="row g-3">
                                {{-- TANGGAL DEAL --}}
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-secondary" for="deal_date">Tanggal Deal</label>
                                    <input type="date" name="deal_date" id="deal_date" 
                                           value="{{ old('deal_date', optional($sendSample->deal_date)->format('Y-m-d')) }}" 
                                           class="form-control">
                                </div>

                                {{-- TANGGAL KIRIM --}}
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-secondary" for="tanggal_kirim">Tanggal Kirim</label>
                                    <input type="date" name="tanggal_kirim" id="tanggal_kirim" 
                                           value="{{ old('tanggal_kirim', optional($sendSample->tanggal_kirim)->format('Y-m-d')) }}" 
                                           class="form-control">
                                </div>

                                {{-- ESTIMASI SAMPAI --}}
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-secondary" for="estimasi_sampai">Estimasi Sampai</label>
                                    <input type="date" name="estimasi_sampai" id="estimasi_sampai" 
                                           value="{{ old('estimasi_sampai', optional($sendSample->estimasi_sampai)->format('Y-m-d')) }}" 
                                           class="form-control bg-light text-muted" readonly>
                                </div>

                                {{-- TANGGAL AKTUAL --}}
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-secondary" for="tanggal_aktual">Tanggal Aktual</label>
                                    <input type="date" name="tanggal_aktual" id="tanggal_aktual" 
                                           value="{{ old('tanggal_aktual', optional($sendSample->tanggal_aktual)->format('Y-m-d')) }}" 
                                           class="form-control">
                                </div>

                                {{-- ID Pesanan --}}
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-secondary" for="id_pesanan">ID Pesanan</label>
                                    <input type="text" name="id_pesanan" id="id_pesanan" 
                                           value="{{ old('id_pesanan', $sendSample->id_pesanan) }}" 
                                           class="form-control" placeholder="Masukkan nomor id pesanan..." autocomplete="off">
                                </div>
                            </div>

                            {{-- BUTTON BATAL, DUPLICATE, UPDATE --}}
                            <div class="mt-auto pt-4 d-flex justify-content-end gap-2 flex-wrap">
                                <a href="{{ old('redirect_to', url()->previous()) }}" class="btn btn-outline-secondary">
                                    Batal
                                </a>
                                
                                <button type="button" id="btn-duplicate" class="btn btn-success">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-files me-1" viewBox="0 0 16 16">
                                        <path d="M13 0H6a2 2 0 0 0-2 2 2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2 2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm0 13V4a2 2 0 0 0-2-2H5a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1zM3 4a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4z"/>
                                    </svg>
                                    Duplicate
                                </button>

                                <button type="submit" class="btn btn-primary px-4">
                                    Update
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
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: "bootstrap-5",
                width: '100%',
                placeholder: $(this).data('placeholder'),
                allowClear: true
            });

            $('#tanggal_kirim').on('change', function() {
                var inputDate = $(this).val();
                if(inputDate) {
                    var date = new Date(inputDate);
                    date.setDate(date.getDate() + 7);
                    var day = ("0" + date.getDate()).slice(-2);
                    var month = ("0" + (date.getMonth() + 1)).slice(-2);
                    var year = date.getFullYear();
                    $('#estimasi_sampai').val(year + "-" + month + "-" + day);
                } else {
                    $('#estimasi_sampai').val('');
                }
            });

            $('#btn-duplicate').on('click', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Duplikasi Data?',
                    text: "Data ini akan disimpan sebagai entri baru. Anda yakin?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d',
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
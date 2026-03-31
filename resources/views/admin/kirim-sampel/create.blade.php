@extends('layouts.admin')

@section('title', 'Tambah Kirim Sampel')
@section('page', 'kirim-sampel-create')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endpush

@section('content')
    <div class="container" style="max-width: 1000px;">
        <header class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 fw-bold mb-1 text-dark">Tambah Kirim Sampel</h1>
            </div>
            {{-- BUTTON KEMBALI --}}
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                Kembali
            </a>
        </header>

        @if ($errors->any())
            <div class="alert alert-danger py-2 mb-4 shadow-sm border-0">
                <div class="fw-bold mb-1 d-flex align-items-center gap-2">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    Ada data yang belum valid
                </div>
                <ul class="mb-0 small ps-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.kirim-sampel.store') }}" method="POST" novalidate>
            @csrf
            <input type="hidden" name="redirect_to" value="{{ old('redirect_to', url()->previous()) }}">
            <div class="row g-4">
                <div class="col-12 col-lg-7">
                    <div class="card border-0 shadow-sm border-top border-3 border-primary h-100">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-2 text-primary">Informasi Utama</h5>
                            <div class="row g-3">
                                {{-- Kreator --}}
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-bold text-secondary" for="creator_id">Kreator <span class="text-danger">*</span></label>
                                    <select name="creator_id" id="creator_id" class="form-select select2" required>
                                        <option value=""></option>
                                        @foreach($creators as $creator)
                                            <option value="{{ $creator->id }}" @selected(old('creator_id') == $creator->id)>
                                                {{ $creator->username ?? $creator->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- APPROVE --}}
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-bold text-secondary" for="approve">APPROVE <span class="text-danger">*</span></label>
                                    <select name="approve" id="approve" class="form-select select2" required>
                                        <option value="{{ auth()->user()->id }}" selected>
                                            {{ auth()->user()->name }} (Anda)
                                        </option>
                                        @foreach($users as $user)
                                            @if($user->id !== auth()->user()->id)
                                                <option value="{{ $user->id }}" @selected(old('approve') == $user->id)>
                                                    {{ $user->name }}
                                                </option>
                                            @endif
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
                                                    <option value="{{ $user->id }}" @selected(old('pic') == $user->id)>
                                                        {{ $user->name }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                {{-- Toko --}}
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-bold text-secondary" for="store_id">Toko <span class="text-danger">*</span></label>
                                    <select name="store_id" id="store_id" class="form-select select2" required>
                                        <option value=""></option>
                                        @foreach($stores as $store)
                                            <option value="{{ $store->id }}" @selected(old('store_id') == $store->id)>
                                                {{ $store->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- SKU --}}
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-bold text-secondary" for="product_id">SKU <span class="text-danger">*</span></label>
                                    <select name="product_id" id="product_id" class="form-select select2" required>
                                        <option value=""></option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>
                                                {{ $product->sku ?? $product->sku_utama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Qty --}}
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-bold text-secondary" for="qty">Qty <span class="text-danger">*</span></label>
                                    <input type="number" min="1" name="qty" id="qty" 
                                        value="{{ old('qty', 1) }}" 
                                        class="form-control" required>
                                </div>

                                {{-- Perjanjian LIVE --}}
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-secondary" for="perjanjian_livestream">Perjanjian Live</label>
                                    <input type="number" name="perjanjian_livestream" id="perjanjian_livestream" 
                                        value="{{ old('perjanjian_livestream') }}" 
                                        class="form-control" placeholder="0">
                                </div>

                                {{-- Perjanjian Video --}}
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-secondary" for="perjanjian_video">Perjanjian Video</label>
                                    <input type="number" name="perjanjian_video" id="perjanjian_video" 
                                        value="{{ old('perjanjian_video') }}" 
                                        class="form-control" placeholder="0">
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
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-secondary" for="deal_date">Tanggal Deal</label>
                                    <input type="date" name="deal_date" id="deal_date" 
                                        value="{{ old('deal_date') }}" 
                                        class="form-control">
                                </div>

                                {{-- TANGGAL KIRIM --}}
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-secondary" for="tanggal_kirim">Tanggal Kirim</label>
                                    <input type="date" name="tanggal_kirim" id="tanggal_kirim" 
                                        value="{{ old('tanggal_kirim') }}" 
                                        class="form-control">
                                </div>

                                {{-- ID PESANAN --}}
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-secondary" for="id_pesanan">ID Pesanan</label>
                                    <input type="text" name="id_pesanan" id="id_pesanan" 
                                        value="{{ old('id_pesanan') }}" 
                                        class="form-control" placeholder="Masukkan nomor ID Pesanan..." autocomplete="off">
                                </div>
                            </div>

                            {{-- BUTTON BATAL & SIMPAN --}}
                            <div class="mt-auto pt-4 d-flex justify-content-end gap-2">
                                <a href="{{ old('previous_url', url()->previous()) }}" class="btn btn-outline-secondary">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    Simpan
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
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: "bootstrap-5",
                width: '100%',
                placeholder: $(this).data('placeholder'),
                allowClear: true
            });
        });
    </script>
@endpush
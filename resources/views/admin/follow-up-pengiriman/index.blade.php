@extends('layouts.admin')

@section('title', 'Follow Up Pengiriman')

@section('content')

    {{-- ── Page Header ──────────────────────────────────────────────── --}}
    <div class="md-page-hdr">
        <div class="md-page-hdr__text">
            <h1 class="ts-headline-sm">Follow Up Pengiriman</h1>
            <p class="ts-body-md ts-color-secondary mt-1 mb-0">
                Daftar pengiriman yang belum sampai dan mendekati estimasi (H-3).
            </p>
        </div>
        <div class="md-page-hdr__actions">
            <span class="md-counter-pill">
                <span class="md-counter-pill__dot md-counter-pill__dot--warning"></span>
                Perlu Follow Up:
                <strong>{{ $items->total() }}</strong>
            </span>
        </div>
    </div>

    {{-- ── Filter Panel ─────────────────────────────────────────────── --}}
    <div class="filter-panel mb-4">
        <form method="GET" action="{{ route('admin.followup.pengiriman.index') }}">
            <div class="row g-2 align-items-end">

                {{-- Dari Tanggal --}}
                <div class="col-6 col-md-2">
                    <label class="filter-label">Dari Tanggal</label>
                    <input type="date"
                           name="start_date"
                           class="form-control form-control-sm"
                           value="{{ request('start_date') }}">
                </div>

                {{-- Sampai Tanggal --}}
                <div class="col-6 col-md-2">
                    <label class="filter-label">Sampai Tanggal</label>
                    <input type="date"
                           name="end_date"
                           class="form-control form-control-sm"
                           value="{{ request('end_date') }}">
                </div>

                {{-- SKU Utama --}}
                <div class="col-12 col-md-3">
                    <label class="filter-label">Filter SKU Utama</label>
                    <select name="sku_utama" class="form-select form-select-sm select2-init" style="width:100%">
                        <option value="">Semua SKU Utama</option>
                        @foreach($skuUtamaOptions as $prod)
                            <option value="{{ $prod->sku_utama }}"
                                    {{ $selectedSkuUtama == $prod->sku_utama ? 'selected' : '' }}>
                                {{ $prod->sku_utama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Pencarian --}}
                <div class="col-12 col-md">
                    <label class="filter-label">Pencarian</label>
                    <div class="search-field">
                        <span class="search-field__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                            </svg>
                        </span>
                        <input type="text"
                               name="q"
                               value="{{ $q ?? '' }}"
                               class="form-control form-control-sm"
                               placeholder="Cari Kreator, Toko, atau ID Pesanan...">
                    </div>
                </div>

                {{-- Aksi Filter --}}
                <div class="col-auto d-flex gap-2 align-items-end">
                    <button class="btn-md filled-primary sm" type="submit">Filter</button>
                    @if(!empty($q) || !empty($selectedSkuUtama) || request('start_date') || request('end_date'))
                        <a href="{{ route('admin.followup.pengiriman.index') }}"
                           class="btn-md text-neutral sm">Reset</a>
                    @endif
                </div>

            </div>
        </form>
    </div>

    {{-- ── Flash Message ────────────────────────────────────────────── --}}
    @if (session('success'))
        <div class="md-banner md-banner-success">
            <svg viewBox="0 0 20 20" width="18" height="18" fill="none" stroke="currentColor"
                 stroke-width="2" style="flex-shrink:0;margin-top:.1rem;">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                      stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- ── Data Table ───────────────────────────────────────────────── --}}
    <div class="surf-card">
        <div class="md-table-scroll" id="tableScroll">
            <table class="md-table" style="min-width:860px;">
                <thead>
                    <tr>
                        <th style="width:22%;">Kreator / Toko / PIC</th>
                        <th style="width:25%;">Produk</th>
                        <th style="width:20%;">Estimasi &amp; Status</th>
                        <th style="width:18%;">ID Pesanan</th>
                        <th style="width:15%; text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($items as $item)
                    @php
                        $qty  = (int) ($item->qty ?? 0);
                        $harga = (int) optional($item->product)->harga;
                        $total = $harga * $qty;

                        $estimasiDate = $item->estimasi_sampai
                            ? \Carbon\Carbon::parse($item->estimasi_sampai) : null;
                        $kirimDate = $item->tanggal_kirim
                            ? \Carbon\Carbon::parse($item->tanggal_kirim) : null;

                        $statusChip = 'chip-surface';
                        $statusText = '—';

                        if ($estimasiDate) {
                            if ($today->gt($estimasiDate)) {
                                $days = $estimasiDate->diffInDays($today);
                                $statusChip = 'chip-error';
                                $statusText  = "Terlewat {$days} hari";
                            } elseif ($today->lt($estimasiDate)) {
                                $days = $today->diffInDays($estimasiDate);
                                $statusChip = 'chip-warning';
                                $statusText  = "H-{$days} Menuju Estimasi";
                            } else {
                                $statusChip = 'chip-info';
                                $statusText  = 'Hari H Estimasi';
                            }
                        }
                    @endphp

                    <tr>
                        {{-- Kreator / Toko / PIC --}}
                        <td>
                            <div class="ts-title-sm mb-1">
                                {{ optional($item->creator)->username ?? '—' }}
                            </div>
                            <div class="ts-body-sm ts-color-secondary mb-1">
                                <i class="bi bi-shop me-1"></i>{{ optional($item->store)->name ?? '—' }}
                            </div>
                            @if($item->user)
                                <span class="chip chip-info">
                                    PIC: {{ $item->user->name }}
                                </span>
                            @endif
                        </td>

                        {{-- Produk --}}
                        <td>
                            <div class="ts-title-sm mb-1">
                                {{ optional($item->product)->sku ?? 'SKU Tidak Ditemukan' }}
                            </div>
                            <div class="ts-body-sm ts-color-secondary mb-1">
                                {{ \Illuminate\Support\Str::limit(optional($item->product)->name, 34) }}
                            </div>
                            <div class="d-flex align-items-center gap-2 ts-label-md ts-color-secondary">
                                <span>Qty: <strong>{{ $qty }}</strong></span>
                                @if($total > 0)
                                    <span>·</span>
                                    <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                                @endif
                            </div>
                        </td>

                        {{-- Estimasi & Status --}}
                        <td>
                            <div class="ts-title-sm mb-1">
                                {{ $estimasiDate ? $estimasiDate->format('d M Y') : '—' }}
                            </div>
                            <div class="mb-1">
                                <span class="chip {{ $statusChip }}">{{ $statusText }}</span>
                            </div>
                            @if($kirimDate)
                                <div class="ts-body-sm ts-color-secondary">
                                    Kirim: {{ $kirimDate->format('d/m/y') }}
                                </div>
                            @endif
                        </td>

                        {{-- ID Pesanan --}}
                        <td>
                            @if($item->id_pesanan)
                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                    <span class="chip chip-outlined">{{ $item->id_pesanan }}</span>
                                    <button type="button"
                                            class="btn-icon-md sz-sm flat"
                                            onclick="copyToClipboard('{{ $item->id_pesanan }}', this)"
                                            title="Salin ID Pesanan"
                                            aria-label="Salin ID Pesanan">
                                        <svg viewBox="0 0 20 20" width="13" height="13" fill="none"
                                             stroke="currentColor" stroke-width="1.8">
                                            <path d="M8 3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1V3z"/>
                                            <path d="M6 3H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1h-2"/>
                                        </svg>
                                    </button>
                                </div>
                            @else
                                <span class="chip chip-error">Belum ada ID Pesanan</span>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td style="text-align:center;">
                            <form method="POST"
                                  action="{{ route('admin.followup.pengiriman.update', $item->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="d-flex flex-column align-items-center gap-2">
                                    <input type="date"
                                           name="tanggal_aktual"
                                           class="form-control form-control-sm text-center"
                                           style="max-width:140px;"
                                           value="{{ old('tanggal_aktual', $today->toDateString()) }}"
                                           required>
                                    <button type="submit"
                                            class="btn-md tonal-success sm"
                                            style="min-width:120px;">
                                        <svg viewBox="0 0 20 20" width="13" height="13" fill="none"
                                             stroke="currentColor" stroke-width="2.2">
                                            <path d="M5 10l4 4 6-6"
                                                  stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        Selesai
                                    </button>
                                </div>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="md-empty">
                                <div class="md-empty__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28"
                                         fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zM8 1.918l-.797.161A4.002 4.002 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4.002 4.002 0 0 0-3.203-3.92L8 1.917zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5.002 5.002 0 0 1 13 6c0 .88.32 4.2 1.22 6z"/>
                                    </svg>
                                </div>
                                <p class="md-empty__title">Tidak Ada Data</p>
                                <p class="md-empty__body">
                                    Semua pengiriman sudah diproses atau belum ada yang mendekati estimasi.
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="md-pagination">
            {{ $items->links() }}
        </div>
    </div>

    {{-- ── MD3 Snackbar ─────────────────────────────────────────────── --}}
    <div class="md-snack" id="md-snack" role="status" aria-live="polite">
        <svg viewBox="0 0 20 20" width="15" height="15" fill="none"
             stroke="#A9F4C2" stroke-width="2.2">
            <path d="M5 10l4 4 6-6" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span class="md-snack__text"></span>
    </div>

@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('.select2-init').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Pilih opsi...',
            allowClear: true
        });

        // Table scroll-shadow hint
        const wrap = document.getElementById('tableScroll');
        if (wrap) {
            const check = () => {
                const atEnd = wrap.scrollLeft + wrap.clientWidth >= wrap.scrollWidth - 2;
                wrap.classList.toggle('is-scrollable', !atEnd && wrap.scrollWidth > wrap.clientWidth);
            };
            wrap.addEventListener('scroll', check, { passive: true });
            window.addEventListener('resize', check);
            check();
        }
    });

    function copyToClipboard(text, btnElement) {
        if (!text) return;
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text)
                .then(() => showToast('ID Pesanan disalin!'))
                .catch(() => fallbackCopyTextToClipboard(text));
        } else {
            fallbackCopyTextToClipboard(text);
        }
    }

    function fallbackCopyTextToClipboard(text) {
        const ta = document.createElement('textarea');
        ta.value = text;
        ta.style.cssText = 'position:fixed;left:-9999px;top:0';
        document.body.appendChild(ta);
        ta.focus();
        ta.select();
        try {
            document.execCommand('copy')
                ? showToast('ID Pesanan disalin!')
                : alert('Browser tidak mendukung copy otomatis.');
        } catch (e) {
            alert('Gagal menyalin ID Pesanan.');
        }
        document.body.removeChild(ta);
    }

    function showToast(message) {
        const snack = document.getElementById('md-snack');
        if (!snack) return;
        snack.querySelector('.md-snack__text').textContent = message;
        snack.classList.add('md-snack--visible');
        clearTimeout(snack._timer);
        snack._timer = setTimeout(() => snack.classList.remove('md-snack--visible'), 2200);
    }
</script>
@endpush

@extends('layouts.admin')

@section('title', 'Follow Up Konten')

@section('content')
    <div @class(['d-flex', 'flex-column', 'flex-md-row', 'justify-content-between', 'align-items-start', 'align-items-md-center', 'gap-3', 'mb-4'])>
        <div>
            <h1 @class(['h4', 'mb-0'])>Follow Up Konten</h1>
            <p @class(['text-secondary', 'small', 'mb-0'])>
                Pantau progres pembuatan konten (Video/Live) dari kreator.
            </p>
        </div>

        <div @class(['d-flex', 'align-items-center'])>
            <span @class(['badge', 'bg-white', 'text-dark', 'border', 'shadow-sm', 'py-2', 'px-3', 'rounded-pill', 'd-flex', 'align-items-center', 'gap-2'])>
                <span @class(['d-inline-block', 'rounded-circle', 'bg-warning']) style="width: 10px; height: 10px;"></span>
                <span>Perlu Follow Up:</span>
                <strong @class(['text-dark'])>{{ $items->total() }}</strong>
            </span>
        </div>
    </div>

    <div @class(['card', 'border-0', 'shadow-sm', 'mb-4'])>
        <div @class(['card-body', 'p-3'])>
            <form method="GET" action="{{ route('admin.followup_konten.index') }}" @class(['row', 'g-2', 'align-items-end'])>
                {{-- START DATE --}}
                <div @class(['col-md-2', 'col-6'])>
                    <label @class(['form-label', 'small', 'text-muted', 'mb-1'])>Dari Tanggal</label>
                    <input type="date" 
                           name="start_date" 
                           @class(['form-control', 'form-control-sm']) 
                           value="{{ request('start_date') }}">
                </div>

                {{-- END DATE --}}
                <div @class(['col-md-2', 'col-6'])>
                    <label @class(['form-label', 'small', 'text-muted', 'mb-1'])>Sampai Tanggal</label>
                    <input type="date" 
                           name="end_date" 
                           @class(['form-control', 'form-control-sm']) 
                           value="{{ request('end_date') }}">
                </div>

                {{-- SKU UTAMA --}}
                <div @class(['col-md-3', 'col-12'])>
                    <label @class(['form-label', 'small', 'text-muted', 'mb-1'])>Filter SKU Utama</label>
                    <select name="sku_utama" @class(['form-select', 'form-select-sm', 'select2-init']) style="width: 100%;">
                        <option value="">Semua SKU Utama</option>
                        @foreach($skuUtamaOptions as $prod)
                            <option value="{{ $prod->sku_utama }}" {{ $sku_utama == $prod->sku_utama ? 'selected' : '' }}>
                                {{ $prod->sku_utama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- SEARCH --}}
                <div @class(['col-md', 'col-12'])>
                    <label @class(['form-label', 'small', 'text-muted', 'mb-1'])>Pencarian</label>
                    <div @class(['input-group', 'input-group-sm'])>
                        <span @class(['input-group-text', 'bg-white', 'text-muted', 'border-end-0'])>
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                            </svg>
                        </span>
                        <input type="text" name="q" value="{{ $search ?? '' }}" @class(['form-control', 'border-start-0', 'ps-0'])
                               placeholder="Cari Kreator, Toko, Produk...">
                    </div>
                </div>

                {{-- AKSI --}}
                <div @class(['col-auto', 'd-flex', 'gap-2'])>
                    <button @class(['btn', 'btn-sm', 'btn-primary', 'px-3']) type="submit">Filter</button> 
                    @if(!empty($search) || !empty($sku_utama) || request('start_date') || request('end_date'))
                        <a href="{{ route('admin.followup_konten.index') }}" @class(['btn', 'btn-sm', 'btn-outline-secondary'])>Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div @class(['alert', 'alert-success', 'py-2', 'mb-3'])>{{ session('success') }}</div>
    @endif
 
    @if (session('error'))
        <div @class(['alert', 'alert-danger', 'py-2', 'mb-3'])>{{ session('error') }}</div>
    @endif

    <div @class(['card', 'border-0', 'shadow-sm'])>
        <div @class(['table-responsive'])>
            <table @class(['table', 'table-hover', 'align-middle', 'mb-0', 'small', 'table-sm'])>
                <thead @class(['table-light'])>
                    <tr>
                        <th @class(['py-3']) style="width: 20%;">Kreator</th>
                        <th @class(['py-3']) style="width: 20%;">Toko & PIC</th>
                        <th @class(['py-3']) style="width: 20%;">Produk</th>
                        <th @class(['py-3']) style="width: 25%;">Progres Konten</th>
                        <th @class(['py-3', 'text-center']) style="width: 15%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($items as $index => $item)
                    @php
                        $perjanjianVideo = (int) ($item->perjanjian_video ?? 0);
                        $perjanjianLive  = (int) ($item->perjanjian_livestream ?? 0);
                        $videoVal = (int) ($item->video_dibuat ?? 0);
                        $liveVal  = (int) ($item->live_dibuat ?? 0);
                        $videoComplete = $perjanjianVideo > 0 && $videoVal >= $perjanjianVideo;
                    @endphp

                    <tr>
                        <td>
                            @php
                                $kreatorName = optional($item->creator)->username ?? optional($item->creator)->name ?? '-';
                            @endphp
                            <div @class(['d-flex', 'align-items-center', 'flex-wrap', 'gap-1', 'mb-1'])>
                                <div @class(['fw-bold', 'text-dark'])>
                                    {{ $kreatorName }}
                                </div>
                                
                                @if($kreatorName !== '-')
                                    {{-- BUTTON COPY USERNAME --}}
                                    <button type="button"
                                            @class(['btn', 'btn-sm', 'btn-outline-secondary', 'rounded-circle', 'd-inline-flex', 'align-items-center', 'justify-content-center'])
                                            style="width:28px;height:28px;"
                                            data-copy="{{ $item->creator->username }}"
                                            onclick="copyIdPesanan(this)"
                                            aria-label="Salin Username" title="Salin Username">
                                        <svg viewBox="0 0 20 20" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M8 3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1V3z"/>
                                            <path d="M6 3H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1h-2"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                            @if(optional($item->creator)->namawa)
                                <div @class(['d-flex', 'align-items-center', 'gap-2'])>
                                    <span @class(['badge', 'bg-light', 'text-secondary', 'border', 'fw-normal'])>
                                        <i @class(['bi', 'bi-whatsapp', 'me-1'])></i> {{ $item->creator->namawa }}
                                    </span>
                                    <button type="button"
                                            @class(['btn', 'btn-sm', 'btn-outline-secondary', 'rounded-circle', 'd-inline-flex', 'align-items-center', 'justify-content-center'])
                                            style="width:28px;height:28px;"
                                            data-copy="{{ $item->creator->namawa }}"
                                            onclick="copyIdPesanan(this)"
                                            aria-label="Salin Nama WA" title="Salin Nama WA">
                                        <svg viewBox="0 0 20 20" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M8 3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1V3z"/>
                                            <path d="M6 3H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1h-2"/>
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        </td>

                        <td>
                            <div @class(['fw-medium', 'text-dark'])>
                                {{ optional($item->store)->name ?? '-' }}
                            </div>
                            @if($item->user)
                                <div @class(['mt-1'])>
                                    <span @class(['badge', 'bg-info-subtle', 'text-info-emphasis', 'border', 'border-info-subtle'])>
                                        PIC: {{ $item->user->name }}
                                    </span>
                                </div>
                            @else
                                <div @class(['mt-1'])>
                                    <span @class(['badge', 'bg-light', 'text-muted', 'border'])>PIC: -</span>
                                </div>
                            @endif
                        </td>

                        <td>
                            <div @class(['fw-medium', 'text-dark', 'mb-1'])>
                                {{ optional($item->product)->sku ?? '-' }}
                            </div>
                            <div @class(['small', 'text-secondary'])>
                                Qty: <strong>{{ $item->qty ?? 0 }}</strong>
                            </div>
                        </td>

                        <td>
                            <div @class(['d-flex', 'flex-column', 'gap-1'])>
                                @if ($perjanjianVideo > 0)
                                    <form method="POST" action="{{ route('admin.followup_konten.video_dibuat', $item->id) }}">
                                        @csrf
                                        <div @class(['d-flex', 'align-items-center'])>
                                            <div @class(['small', 'text-muted', 'd-flex', 'align-items-center', 'gap-1', 'lh-1', 'mx-1'])>
                                                <span>Video <span @class(['fw-semibold', 'text-dark'])>{{ $videoVal }}/{{ $perjanjianVideo }}</span></span>
                                                @if($videoComplete)
                                                    <i @class(['bi', 'bi-check-circle-fill', 'text-success']) style="font-size: 0.8rem;"></i>
                                                @endif
                                            </div>
                                            <div @class(['input-group', 'input-group-sm', 'mx-1']) style="width: 100px;">
                                                <input type="number" name="video_dibuat" @class(['form-control', 'px-2', 'text-center'])
                                                    value="{{ $videoVal }}" min="0" placeholder="0">
                                                <button @class(['btn', 'btn-outline-primary', 'd-flex', 'align-items-center', 'justify-content-center', 'px-2']) type="submit" title="Simpan Video">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16"><path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                @else
                                    <div @class(['d-flex', 'align-items-center', 'justify-content-between', 'text-muted', 'small', 'opacity-50', 'py-1'])>
                                        <span><i @class(['bi', 'bi-dash-circle', 'me-1'])></i> Video</span>
                                        <span>-</span>
                                    </div>
                                @endif
                            </div>
                        </td>

                        <td @class(['text-center'])>
                            <div @class(['d-flex', 'justify-content-center', 'gap-2'])>

                                <form method="POST" action="{{ route('admin.followup_konten.mark_mingguan', $item->id) }}" @class(['flex-fill'])>
                                    @csrf
                                    <button type="submit" @class(['btn', 'btn-sm', 'btn-success', 'w-100', 'text-nowrap'])>
                                        <i @class(['bi', 'bi-check2-all', 'me-1'])></i> Selesai
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.kreator.blokir', $item->creator) }}" @class(['flex-fill'])>
                                    @csrf
                                    <button type="submit" @class(['btn', 'btn-sm', 'btn-danger', 'w-100', 'text-nowrap']) onclick="return confirm('Apakah Anda yakin ingin memblokir kreator ini?')">
                                        <i @class(['bi', 'bi-person-x', 'me-1'])></i> Blokir
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" @class(['text-center', 'py-5'])>
                            <div @class(['d-flex', 'flex-column', 'align-items-center', 'text-muted'])>
                                <span>Data tidak tersedia.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div @class(['mt-4'])>
        {{ $items->links() }}
    </div>

    <div @class(['position-fixed', 'bottom-0', 'end-0', 'p-3']) style="z-index: 1080;">
        <div id="copyToast" @class(['toast', 'align-items-center', 'text-bg-dark', 'border-0']) role="alert" aria-live="polite" aria-atomic="true">
            <div @class(['d-flex'])>
                <div @class(['toast-body']) id="copyToastText">Tersalin.</div>
                <button type="button" @class(['btn-close', 'btn-close-white', 'me-2', 'm-auto']) data-bs-dismiss="toast" aria-label="Tutup"></button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select2-init').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Pilih SKU Utama...',
                allowClear: true
            });
        });
    </script>
    <script src="{{ asset('js/admin/kirim-sampel-index.js') }}"></script>
@endpush
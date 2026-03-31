@extends('layouts.admin')

@section('title', 'Kreator')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h1 class="h4">Kreator</h1>
        </div>

        <div class="d-flex gap-2">
            {{-- BUTTON BULK DELETE --}}
            <button type="button"
                    id="btnBulkDelete"
                    class="btn btn-danger rounded-pill shadow-sm d-none align-items-center gap-2"
                    onclick="submitBulkDelete()">
                <svg viewBox="0 0 20 20" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 6h12M8 6V4h4v2M7 6v9a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V6"/>
                </svg>
                <span class="small fw-bold">Hapus (<span id="countSelected">0</span>)</span>
            </button>

            {{-- BUTTON EXPORT --}}
            <a href="{{ route('admin.kreator.export', request()->all()) }}" 
                class="btn btn-success rounded-pill d-inline-flex align-items-center justify-content-center" 
                title="Export Excel" 
                data-bs-toggle="tooltip" 
                style="width: 44px; height: 44px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-file-earmark-excel-fill" viewBox="0 0 16 16">
                    <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0M9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1M5.884 6.68 8 9.219l2.116-2.54a.5.5 0 1 1 .768.641L8.651 10l2.233 2.68a.5.5 0 0 1-.768.64L8 10.781l-2.116 2.54a.5.5 0 0 1-.768-.641L7.349 10 5.116 7.32a.5.5 0 1 1 .768-.64"/>
                </svg>
            </a>

            {{-- BUTTON UPLOAD MASSAL --}}
            <button type="button" 
                class="btn btn-success shadow-sm rounded-circle d-inline-flex align-items-center justify-content-center"
                style="width:44px;height:44px;"
                data-bs-toggle="modal" 
                data-bs-target="#importModal"
                aria-label="Tambah Kreator Massal"
                title="Kreator Massal">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
                </svg>
            </button>

            {{-- BUTTON TAMBAH --}}
            <a href="{{ route('admin.kreator.create') }}"
                class="btn btn-primary rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm"
                style="width:44px;height:44px;"
                aria-label="Tambah kreator" title="Tambah">
                <svg viewBox="0 0 20 20" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10 4v12M4 10h12" stroke-linecap="round"/>
                </svg>
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success py-2">{{ session('success') }}</div>
    @endif

    {{-- SEARCH & FILTER --}}
    <form method="GET" action="{{ route('admin.kreator.index') }}" class="row g-2 align-items-center mb-3">
        <div class="col-12 col-md-5">
            <input
                type="text"
                name="q"
                value="{{ $q ?? '' }}"
                class="form-control"
                placeholder="Cari Kreator..."
                autocomplete="off">
        </div>

        <input type="hidden" name="sort" value="{{ $sort ?? 'id' }}">
        <input type="hidden" name="dir"  value="{{ $dir ?? 'desc' }}">

        <div class="col-auto">
            <button type="submit" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center" title="Cari">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </button>
        </div>

        <div class="col-auto">
            <div class="dropdown">
                <button type="button" 
                    class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 dropdown-toggle" 
                    data-bs-toggle="dropdown" 
                    data-bs-auto-close="outside"
                    aria-expanded="false" 
                    title="Filter Pencarian">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                </button>

                <div class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-0 mt-2" style="min-width: 350px;">
                    <div class="card border-0">
                        <div class="card-header bg-transparent border-bottom py-2">
                            <h6 class="mb-0 fw-bold text-secondary">Opsi Filter</h6>
                        </div>
                        
                        <div class="card-body p-3">
                            <div class="row g-2">
                                {{-- FILTER GMV --}}
                                <div class="col-12">
                                    <label class="form-label small text-muted fw-bold text-uppercase mb-1">Rentang GMV</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light text-muted">Rp</span>
                                        <input type="number" name="min_gmv" class="form-control" placeholder="Min" value="{{ request('min_gmv') }}">
                                        <span class="input-group-text bg-white border-start-0 border-end-0">-</span>
                                        <input type="number" name="max_gmv" class="form-control" placeholder="Max" value="{{ request('max_gmv') }}">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <hr class="my-2 border-secondary-subtle">
                                </div>

                                {{-- FILTER UMUR --}}
                                <div class="col-12 col-md-6">
                                    <label for="filterUmur" class="form-label small text-muted fw-bold text-uppercase mb-1">Umur</label>
                                    <select class="form-select form-select-sm select2-init" id="filterUmur" name="filter_umur[]" multiple="multiple" style="width: 100%">
                                        @foreach($ageOptions as $age)
                                            <option value="{{ $age->id }}" {{ in_array($age->id, $filter_umur ?? []) ? 'selected' : '' }}>{{ $age->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- FILTER KATEGORI --}}
                                <div class="col-12 col-md-6">
                                    <label for="filterKategori" class="form-label small text-muted fw-bold text-uppercase mb-1">Kategori</label>
                                    <select class="form-select form-select-sm select2-init" id="filterKategori" name="filter_kategori[]" multiple="multiple" style="width: 100%">
                                        @foreach($categoryOptions as $cat)
                                            <option value="{{ $cat->id }}" {{ in_array($cat->id, $filter_kategori ?? []) ? 'selected' : '' }}>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- FILTER KONTEN STYLE --}}
                                <div class="col-12 col-md-6">
                                    <label for="filterStyle" class="form-label small text-muted fw-bold text-uppercase mb-1">Konten Style</label>
                                    <select class="form-select form-select-sm select2-init" id="filterStyle" name="filter_style[]" multiple="multiple" style="width: 100%">
                                        @foreach($styleOptions as $style)
                                            <option value="{{ $style->id }}" {{ in_array($style->id, $filter_style ?? []) ? 'selected' : '' }}>{{ $style->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- FILTER TOP KREATOR --}}
                                <div class="col-12 col-md-6">
                                    <label class="form-label small text-muted fw-bold text-uppercase mb-1">Kreator</label>
                                    <div class="form-control form-control-sm d-flex align-items-center justify-content-between" style="height: 31px;">
                                        <label class="form-check-label small text-secondary" for="filterTopCreator">Top Kreator ★</label>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" role="switch" id="filterTopCreator" name="filter_top_creator" value="1" {{ request('filter_top_creator') ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>

                                {{-- FILTER TOP KREATOR --}}
                                <div class="col-12 col-md-6">
                                    <label class="form-label small text-muted fw-bold text-uppercase mb-1">Kreator</label>
                                    <div class="form-control form-control-sm d-flex align-items-center justify-content-between" style="height: 31px;">
                                        <label class="form-check-label small text-secondary" for="filterBlokir">Diblokir</label>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" role="switch" id="filterBlokir" name="filter_is_blocked" value="1" {{ request('filter_is_blocked') ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>

                                {{-- FILTER TANPA WHATSAPP --}}
                                <div class="col-12 col-md-6">
                                    <label class="form-label small text-muted fw-bold text-uppercase mb-1">WhatsApp</label>
                                    <div class="form-control form-control-sm d-flex align-items-center justify-content-between" style="height: 31px;">
                                        <label class="form-check-label small text-secondary" for="filterNoWa">Belum Ada WA</label>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" role="switch" id="filterNoWa" name="filter_no_wa" value="1" {{ request('filter_no_wa') ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- BUTTON RESET & TERAPKAN --}}
                        <div class="card-footer bg-light border-top p-2 d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.kreator.index', ['q' => $q, 'sort' => $sort, 'dir' => $dir]) }}" 
                               class="btn btn-sm btn-link text-decoration-none text-secondary">
                               Reset
                            </a>
                            <button type="submit" class="btn btn-sm btn-primary px-3">
                                Terapkan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(!empty($q))
            <div class="col-auto">
                <a class="btn btn-outline-secondary d-inline-flex align-items-center gap-2" 
                    href="{{ route('admin.kreator.index', ['sort' => $sort ?? 'id', 'dir' => $dir ?? 'desc']) }}">
                    <span>Reset</span>
                </a>
            </div>
        @endif
    </form>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small table-sm">
                <thead class="table-light align-middle">
                    <tr class="text-center">
                        <th style="width: 40px;">
                            <div class="form-check d-flex justify-content-center">
                                <input class="form-check-input" type="checkbox" id="checkAll">
                            </div>
                        </th>
                        <th style="width:150px;">Kreator</th>
                        <th style="width:128px;">GMV</th>
                        <th style="width:64px;">Item</th>
                        <th style="width:130px;">Umur</th>
                        <th style="width:200px;">Kategori</th>
                        <th style="width:180px;">Konten Style</th>
                        <th style="width:110px;">Aksi</th>
                    </tr>
                </thead>

                <tbody id="creatorTableBody">
                @foreach($creators as $creator)
                    @php
                        // GENDER
                        $gender = $creator->genderpengikut;
                        $genderLabel = $gender ? \Illuminate\Support\Str::title(str_replace('-', ' ', $gender)) : null;
                        $genderClass = match ($gender) {
                            'perempuan' => 'text-bg-danger-subtle text-danger border-danger-subtle',
                            'laki-laki' => 'text-bg-primary-subtle text-primary border-primary-subtle',
                            'seimbang'  => 'text-bg-success-subtle text-success border-success-subtle',
                            default     => 'text-bg-light text-secondary border',
                        };

                        // CHANNEL
                        $channel = $creator->channel;
                        $channelLabel = $channel ? \Illuminate\Support\Str::title(str_replace('-', ' ', $channel)) : null;
                        $channelClass = match ($channel) {
                            'video'     => 'text-bg-info-subtle text-info border-info-subtle',
                            'live'      => 'text-bg-warning-subtle text-warning border-warning-subtle',
                            'kartu produk'  => 'text-bg-secondary-subtle text-secondary border-secondary-subtle',
                            default     => 'text-bg-light text-secondary border',
                        };
                    @endphp

                    <tr class="row-data"
                        data-gmv="{{ $creator->gmv }}"
                        data-items="{{ $creator->itemsterjual }}"
                        data-gender="{{ $creator->genderpengikut }}"
                        data-channel="{{ $creator->channel }}"
                    >
                        <td class="text-center">
                            <div class="form-check d-flex justify-content-center">
                                <input class="form-check-input check-item" type="checkbox" value="{{ $creator->id }}">
                            </div>
                        </td>

                        {{-- KREATOR --}}
                        <td class="align-middle">
                            <div class="d-flex flex-column">
                                @php
                                    $tiktokUrl = 'https://www.tiktok.com/@' . rawurlencode(ltrim((string) $creator->username, '@'));
                                @endphp

                                <div>
                                    <a href="{{ $tiktokUrl }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-decoration-none text-dark">
                                        {{ $creator->username }}
                                    </a>

                                    <button type="button" 
                                        class="btn btn-sm btn-link text-secondary p-0 border-0 position-relative d-inline-flex align-items-center" 
                                        onclick="copyUsername('{{ $creator->username }}', this)" 
                                        title="Copy Username">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1z"/>
                                        </svg>
                                    </button>
                                    @if((int)$creator->top_creator === 1)
                                        <span class="text-warning ms-1" 
                                            style="font-size: 1.2rem; line-height: 0;" 
                                            title="Top Creator">★</span>
                                    @endif

                                    @if((int)$creator->is_blocked === 1)
                                        <span class="badge bg-danger ms-1" title="Kreator ini telah diblokir">Diblokir</span>
                                    @endif

                                    <div class="text-muted" style="font-size: 0.75rem;">
                                        Input oleh: {{ $creator->user->name ?? '-' }}
                                    </div>
                                </div>



                                <div class="d-flex flex-column gap-1 mt-1">
                                    @if($channelLabel)
                                        <span class="badge rounded-pill border {{ $channelClass }} small py-0 px-2 align-self-start">
                                            {{ $channelLabel }}
                                        </span>
                                    @endif

                                    @if($genderLabel)
                                        <span class="badge rounded-pill border {{ $genderClass }} small py-0 px-2 align-self-start">
                                            {{ $genderLabel }}
                                        </span>
                                    @endif

                                    @if(!$channelLabel && !$genderLabel)
                                        <span class="text-muted small">-</span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- GMV --}}
                        <td class="text-center fw-semibold">
                            {{ number_format((int) $creator->gmv, 0, ',', '.') }}
                        </td>

                        {{-- ITEM TERJUAL --}}
                        <td class="text-center">
                            {{ number_format((int) $creator->itemsterjual, 0, ',', '.') }}
                        </td>

                        {{-- UMUR --}}
                        <td>
                            <div class="d-flex flex-column gap-1">
                                @forelse($creator->ages as $age)
                                    <span class="badge rounded-pill text-bg-light border text-secondary align-self-center small py-0 px-2">
                                        {{ $age->name }}
                                    </span>
                                @empty
                                    <span class="text-muted">-</span>
                                @endforelse
                            </div>
                        </td>

                        {{-- KATEGORI --}}
                        <td>
                            <div class="d-flex flex-column gap-1">
                                @forelse($creator->categories as $category)
                                    <span class="badge rounded-pill text-bg-light border text-secondary align-self-start small py-0 px-2">
                                        {{ $category->name }}
                                    </span>
                                @empty
                                    <span class="text-muted">-</span>
                                @endforelse
                            </div>
                        </td>

                        {{-- KONTEN STYLE --}}
                        <td>
                            <div class="d-flex flex-column gap-1">
                                @forelse($creator->contentStyles as $cs)
                                    <span class="badge rounded-pill text-bg-light border text-secondary align-self-start small py-0 px-2">
                                        {{ $cs->name }}
                                    </span>
                                @empty
                                    <span class="text-muted">-</span>
                                @endforelse
                            </div>
                        </td>

                        {{-- AKSI --}}
                        <td class="text-center align-middle">
                            <div class="d-inline-flex gap-2">
                                <a href="{{ route('admin.kreator.show', $creator->id) }}"
                                    class="btn btn-sm btn-outline-primary rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center"
                                    style="width:28px;height:28px;"
                                    aria-label="Detail" title="Detail">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </a>

                                <a href="{{ route('admin.kreator.edit', $creator->id) }}"
                                    class="btn btn-sm btn-outline-secondary rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center"
                                    style="width:28px;height:28px;"
                                    aria-label="Edit" title="Edit">
                                    <svg viewBox="0 0 20 20" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M4 13.5V16h2.5L14 8.5 11.5 6 4 13.5z"/>
                                        <path d="M12 5.5 13.5 4 16 6.5 14.5 8"/>
                                    </svg>
                                </a>

                                @if(auth()->user()->role === 'super admin') 
                                    <button type="button"
                                        class="btn btn-sm btn-outline-danger rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center"
                                        style="width:28px;height:28px;"
                                        data-url="{{ route('admin.kreator.destroy', $creator->id) }}"
                                        data-name="{{ $creator->username }}"
                                        onclick="openDeleteModal(this)"
                                        aria-label="Hapus" title="Hapus">
                                        
                                        <svg viewBox="0 0 20 20" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M4 6h12"/>
                                            <path d="M8 6V4h4v2"/>
                                            <path d="M7 6v9a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V6"/>
                                        </svg>

                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $creators->links() }}
    </div>

    {{-- MODAL DELETE --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hapus Kreator</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    Yakin ingin menghapus <strong id="modalCreatorName"></strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Ya, hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- FORM BULK DELETE --}}
    <form id="formBulkDelete" action="{{ route('admin.kreator.bulk_destroy') }}" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="importModalLabel">Upload Kreator Massal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form action="{{ route('admin.kreator.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info small mb-3">
                            <i class="me-1">!</i> Pastikan format Excel memiliki header kolom: <br>
                            <strong>username, channel, gmv, itemsterjual, genderpengikut, namawa, nomerwa, kategori, umur, konten_style</strong>
                        </div>
                        <div class="mb-3">
                            <label for="fileImport" class="form-label fw-semibold">Pilih File Excel (.xlsx / .csv)</label>
                            <input class="form-control" type="file" id="fileImport" name="file" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light text-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success d-flex align-items-center gap-2">
                            
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                                <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
                            </svg>
                            Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkAll = document.getElementById('checkAll');
            const checkItems = document.querySelectorAll('.check-item');
            const btnBulkDelete = document.getElementById('btnBulkDelete');
            const countSelected = document.getElementById('countSelected');

            function toggleBulkButton() {
                const selectedCount = document.querySelectorAll('.check-item:checked').length;
                if(countSelected) countSelected.innerText = selectedCount;
                
                if (selectedCount > 0) {
                    btnBulkDelete.classList.remove('d-none');
                    btnBulkDelete.classList.add('d-inline-flex');
                } else {
                    btnBulkDelete.classList.add('d-none');
                    btnBulkDelete.classList.remove('d-inline-flex');
                }
            }

            if(checkAll) {
                checkAll.addEventListener('change', function() {
                    checkItems.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });

                    toggleBulkButton();
                });
            }

            checkItems.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    if (!this.checked) {
                        if(checkAll) checkAll.checked = false;
                    }

                    if (document.querySelectorAll('.check-item:checked').length === checkItems.length) {
                        if(checkAll) checkAll.checked = true;
                    }

                    toggleBulkButton();
                });
            });

            window.submitBulkDelete = function() {
                const selectedIds = Array.from(document.querySelectorAll('.check-item:checked')).map(cb => cb.value);

                if (selectedIds.length === 0) return;

                if (confirm('Apakah Anda yakin ingin menghapus ' + selectedIds.length + ' data terpilih?')) {
                    const form = document.getElementById('formBulkDelete');
                    const oldInputs = form.querySelectorAll('input[name="ids[]"]');
                    
                    oldInputs.forEach(input => input.remove());
                    selectedIds.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids[]';
                        input.value = id;
                        form.appendChild(input);
                    });

                    form.submit();
                }
            }
        });

        function openDeleteModal(button) {
            var url = button.getAttribute('data-url');
            var name = button.getAttribute('data-name');
            var form = document.getElementById('deleteForm');
            var modalName = document.getElementById('modalCreatorName');

            if(form) form.action = url;
            if(modalName) modalName.innerText = name;
            
            var deleteModalEl = document.getElementById('deleteModal');
            var modal = new bootstrap.Modal(deleteModalEl);
            modal.show();
        }

        $(document).ready(function() {
            $('.select2-init').each(function() {
                $(this).select2({
                    theme: "bootstrap-5",
                    width: '100%',
                    placeholder: 'Pilih Opsi...',
                    allowClear: true,
                    closeOnSelect: false,
                    dropdownParent: $(this).closest('.dropdown-menu') 
                });
            });

            $('.select2-container').on('click', function (e) {
                e.stopPropagation();
            });
        });

        window.copyUsername = function(text, buttonElement) {
            // API bawaan browser untuk copy ke clipboard
            navigator.clipboard.writeText(text).then(function() {
                
                // Cek apakah sudah ada popup sebelumnya agar tidak menumpuk
                let existingPopup = buttonElement.querySelector('.copy-popup');
                if (existingPopup) existingPopup.remove();

                // Buat elemen popup
                let popup = document.createElement('span');
                popup.innerText = 'Copied!';
                popup.className = 'badge bg-dark copy-popup position-absolute shadow-sm';
                
                // Styling posisi agar melayang di atas tombol
                popup.style.top = '-25px';
                popup.style.left = '50%';
                popup.style.transform = 'translateX(-50%)';
                popup.style.zIndex = '1050';
                popup.style.fontSize = '10px';
                popup.style.transition = 'opacity 0.3s ease';

                // Masukkan popup ke dalam tombol
                buttonElement.appendChild(popup);

                // Hilangkan popup setelah 1.5 detik
                setTimeout(() => {
                    popup.style.opacity = '0';
                    setTimeout(() => popup.remove(), 300); // Tunggu animasi fade out selesai
                }, 1500);

            }).catch(function(err) {
                console.error('Gagal menyalin teks: ', err);
                alert('Gagal menyalin teks.');
            });
        }
        
    </script>
@endpush
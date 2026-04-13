@extends('layouts.admin')

@section('title', 'Dashboard')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }

        .card-stat {
            transition: all 0.3s ease;
            border: 1px solid #eef2f6;
            border-radius: 12px;
            background: #fff;
            overflow: hidden;
            position: relative;
        }

        .card-stat:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px #0000000d !important;
            border-color: #fff;
        }

        .border-left-primary { border-left: 4px solid #0d6efd; }
        .border-left-success { border-left: 4px solid #198754; }
        .border-left-danger  { border-left: 4px solid #dc3545; }
        .border-left-purple  { border-left: 4px solid #6f42c1; }

        .icon-box {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }

        .card-chart {
            border: 1px solid #eef2f6;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 2px 10px #00000005;
        }

        .date-picker-wrapper {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 4px;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 5px #00000005;
        }

        .flatpickr-input {
            border: none !important;
            background: transparent !important;
            padding-left: 10px;
        }
        .flatpickr-input:focus { outline: none; }

        .btn-white {
            background: #fff;
            color: #6c757d;
            font-weight: 500;
        }
        .btn-white:hover {
            background: #f8f9fa;
            color: #0d6efd;
        }
        .ls-1 { letter-spacing: 0.5px; }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-4 py-4">
        {{-- FILTER --}}
        <form action="{{ url()->current() }}" method="GET" id="filterForm">
            <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center mb-4 gap-3">
                <div>
                    <h1 class="h4 text-dark fw-bold mb-1">Dashboard Overview</h1>
                    <p class="text-muted small mb-0">Pantau performa kreator dan distribusi sampel Anda.</p>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-2">
                    {{-- YESTERDAY // 7 DAYS // 30 DAYS  --}}
                    <div class="btn-group shadow-sm" role="group">
                        <button type="button" class="btn btn-white btn-sm border" onclick="setQuickDate('yesterday')">Kemarin</button>
                        <button type="button" class="btn btn-white btn-sm border" onclick="setQuickDate('7days')">7 Hari</button>
                        <button type="button" class="btn btn-white btn-sm border" onclick="setQuickDate('30days')">30 Hari</button>
                    </div>

                    {{-- RANGE PICKER --}}
                    <div class="d-flex align-items-center bg-white p-1 rounded-3 shadow-sm border">
                        <div class="date-picker-wrapper" style="border: none; box-shadow: none;">
                            <span class="ps-2 text-muted"><i class="bi bi-calendar4-week"></i></span>
                            <input type="text" class="form-control form-control-sm flatpickr-input" 
                                style="width: 200px;"
                                id="date_range" name="date_range" 
                                placeholder="Pilih Periode..."
                                value="{{ request('date_range') }}" readonly>
                        </div>
                        <div class="d-flex gap-1 pe-1">
                            <button type="submit" class="btn btn-primary btn-sm px-3">Terapkan</button>
                            @if(request('date_range'))
                                <a href="{{ url()->current() }}" class="btn btn-light btn-sm border text-muted"><i class="bi bi-x-lg"></i></a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </form>

        {{-- STATS CARDS --}}
        <div class="row g-4 mb-4">
            {{-- TOTAL KREATOR --}}
            <div class="col-12 col-md-4">
                <div class="card card-stat border-left-primary h-100 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-uppercase text-muted fw-bold small mb-1 ls-1">Total Kreator</p>
                                <h3 class="fw-bold text-dark mb-0">{{ number_format($totalCreator ?? 0) }}</h3>
                            </div>
                            <div class="icon-box bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-person-video2 fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SAMPEL DIKIRIM  --}}
            <div class="col-12 col-md-4">
                <div class="card card-stat border-left-success h-100 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-uppercase text-muted fw-bold small mb-1 ls-1">Sampel Dikirim</p>
                                <h3 class="fw-bold text-dark mb-0">
                                    {{ number_format($stats->totalSampel ?? 0) }}
                                </h3>
                            </div>
                            <div class="icon-box bg-success bg-opacity-10 text-success">
                                <i class="bi bi-box-seam fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PENGELUARAN --}}
            <div class="col-12 col-md-4">
                <div class="card card-stat border-left-danger h-100 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-uppercase text-muted fw-bold small mb-1 ls-1">Pengeluaran</p>
                                <h3 class="fw-bold text-dark mb-0">{{ number_format($stats->totalPengeluaran ?? 0, 0, ',', '.') }}</h3>
                            </div>
                            <div class="icon-box bg-danger bg-opacity-10 text-danger">
                                <i class="bi bi-wallet2 fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ROW 1: CHART CREATOR PER DAY --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card card-chart p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="fw-bold text-dark mb-0">Jumlah Kreator</h5>
                            {{-- Logic teks subtitle dinamis berdasarkan filter --}}
                            <p class="text-muted small mb-0">
                                {{ request('date_range') ? 'Periode: ' . request('date_range') : '7 Hari Terakhir' }}
                            </p>
                        </div>
                        <span class="icon-box bg-purple bg-opacity-10 text-purple" style="width: 35px; height: 35px; color: #6f42c1;">
                            <i class="bi bi-graph-up"></i>
                        </span>
                    </div>
                    <div style="position: relative; height: 350px; width: 100%;">
                        <canvas id="creatorTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- ROW 2: TOP 10 SKU UTAMA (TABLE) --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card card-chart p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="fw-bold text-dark mb-1">TOP 10 SKU UTAMA</h5>
                            <p class="text-muted small mb-0">Produk dengan volume pengiriman tertinggi.</p>
                        </div>
                        
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary bg-opacity-10 text-primary">By Volume</span>
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalAllSKU">
                                <i class="bi bi-arrows-fullscreen me-1"></i> Lihat Semua
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="60" class="text-center">No.</th>
                                    <th>Nama SKU</th>
                                    <th class="text-end">Total Qty Dikirim</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($chartLabels as $index => $label)
                                    <tr>
                                        <td class="text-center">
                                            @if($index == 0)
                                                <span class="badge bg-warning text-dark" style="width: 25px;">1</span>
                                            @elseif($index == 1)
                                                <span class="badge bg-secondary text-white" style="width: 25px;">2</span>
                                            @elseif($index == 2)
                                                <span class="badge bg-secondary text-white" style="width: 25px;">3</span>
                                            @else
                                                <span class="fw-bold text-muted small">{{ $index + 1 }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="icon-box bg-light text-muted me-3" style="width: 30px; height: 30px; border-radius: 6px;">
                                                    <i class="bi bi-box-seam fs-6"></i>
                                                </div>
                                                <span class="fw-semibold text-dark">{{ $label }}</span>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-bold text-primary">{{ number_format($chartValues[$index] ?? 0) }}</span>
                                            <span class="text-muted small ms-1">Pcs</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">Belum ada data produk untuk ditampilkan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalAllSKU" tabindex="-1" aria-labelledby="modalAllSKULabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="modalAllSKULabel">Daftar Lengkap SKU Sampel</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="60" class="text-center">No.</th>
                                        <th>Nama SKU</th>
                                        <th class="text-end">Total Qty Dikirim</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($allChartLabels as $index => $label)
                                        <tr>
                                            <td class="text-center fw-bold text-muted small">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="icon-box bg-light text-muted me-3" style="width: 30px; height: 30px; border-radius: 6px;">
                                                        <i class="bi bi-box-seam fs-6"></i>
                                                    </div>
                                                    <span class="fw-semibold text-dark">{{ $label }}</span>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <span class="fw-bold text-primary">{{ number_format($allChartValues[$index] ?? 0) }}</span>
                                                <span class="text-muted small ms-1">Pcs</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted">Belum ada data produk.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ROW 3: TOP PIC TABLE --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card card-chart p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Sampel Dikirim Per PIC</h5>
                            <p class="text-muted small mb-0">Daftar PIC yang paling aktif mengirimkan sampel.</p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="80" class="text-center">No.</th>
                                    <th>Nama PIC</th>
                                    <th class="text-center">Total Sampel Dikirim</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topUser as $index => $data)
                                    <tr>
                                        <td class="text-center">
                                            @if($index == 0)
                                                <span class="badge bg-warning text-dark"><i class="bi bi-trophy-fill"></i> 1</span>
                                            @else
                                                <span class="fw-bold text-muted">{{ $index + 1 }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="icon-box bg-primary bg-opacity-10 text-primary me-3" style="width: 35px; height: 35px;">
                                                    <i class="bi bi-person-fill fs-6"></i>
                                                </div>
                                                <span class="fw-semibold text-dark">{{ $data->user->name ?? 'User Tidak Ditemukan' }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info bg-opacity-10 text-info px-3 py-2">
                                                {{ number_format($data->total_dikirim) }} Sampel
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">Tidak ada data pengiriman dalam periode ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ROW 4: TOP CREATOR TABLE --}}
        <div class="row">
            <div class="col-12">
                <div class="card card-chart p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Top Kreator Performa</h5>
                            <p class="text-muted small mb-0">Kreator dengan volume penerimaan sampel terbanyak.</p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="80" class="text-center">Rank</th>
                                    <th>Username Kreator</th>
                                    <th class="text-center">Total Sampel Diterima</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topCreator as $index => $creatorData)
                                    <tr>
                                        <td class="text-center">
                                            @if($index == 0)
                                                <span class="badge bg-warning text-dark"><i class="bi bi-star-fill"></i> Best</span>
                                            @else
                                                <span class="fw-bold text-muted">{{ $index + 1 }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="icon-box bg-purple bg-opacity-10 text-purple me-3" style="width: 35px; height: 35px; color: #6f42c1; background-color: rgba(111, 66, 193, 0.1);">
                                                    <i class="bi bi-camera-reels-fill fs-6"></i>
                                                </div>
                                                <div>
                                                    <span class="fw-semibold text-dark">{{ $creatorData->creator->username ?? 'Unknown Creator' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="fw-bold text-primary">
                                                {{ number_format($creatorData->total_dikirim) }}
                                            </span>
                                            <span class="text-muted small ms-1">Barang</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">Belum ada data kreator.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- 1. SETUP DATEPICKER ---
            const fp = flatpickr("#date_range", {
                mode: "range",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "j F Y",
                locale: "id",
                defaultDate: "{{ request('date_range') }}"
            });

            window.setQuickDate = function(type) {
                let start = new Date();
                let end = new Date();

                if (type === 'yesterday') {
                    start.setDate(start.getDate() - 1);
                    end.setDate(end.getDate() - 1);
                } else if (type === '7days') {
                    start.setDate(start.getDate() - 7);
                } else if (type === '30days') {
                    start.setDate(start.getDate() - 30);
                }

                const formatDate = (date) => {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                };

                fp.setDate([formatDate(start), formatDate(end)]);
                document.getElementById('filterForm').submit();
            }

            // --- 2. SETUP CHART: CREATOR TREND (LINE) ---

            // Mengambil data dari variabel PHP creatorPerDay
            const creatorRawData = @json($creatorPerDay);

            // Mapping data untuk Chart JS
            const creatorLabels = creatorRawData.map(item => {
                const dateObj = new Date(item.date);
                return dateObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
            });
            const creatorValues = creatorRawData.map(item => item.total_creator);

            const ctxCreator = document.getElementById('creatorTrendChart').getContext('2d');
            new Chart(ctxCreator, {
                type: 'line',
                data: {
                    labels: creatorLabels,
                    datasets: [{
                        label: 'Kreator',
                        data: creatorValues,
                        backgroundColor: 'rgba(111, 66, 193, 0.1)', 
                        borderColor: '#6f42c1', 
                        borderWidth: 2,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#6f42c1',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4 
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            intersect: false,
                            mode: 'index',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 },
                            grid: { borderDash: [2, 2] },
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        });
    </script>
@endpush
@extends('layouts.app')

@section('content')
<div class="container mt-4 mb-5">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-people-fill text-primary me-2"></i>Data Pengguna</h4>
            <p class="text-muted mb-0 small">Statistik, daftar pengguna aktif, dan seluruh pengguna terdaftar di Qanaah</p>
        </div>
        <div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Dashboard
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row justify-content-center">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mb-3" style="width: 48px; height: 48px;">
                        <i class="bi bi-people fs-4"></i>
                    </div>
                    <h5 class="card-title mb-2 text-secondary">Pengguna Terdaftar</h5>
                    <p class="display-5 fw-bold text-primary mb-1">{{ $totalUsers }}</p>
                    <small class="text-muted">Total akun terdaftar</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
             <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-circle mb-3" style="width: 48px; height: 48px;">
                        <i class="bi bi-person-check fs-4"></i>
                    </div>
                    <h5 class="card-title mb-2 text-secondary">Pengguna Aktif (30 Hari)</h5>
                    <p class="display-5 fw-bold text-success mb-1">{{ $activeUsers }}</p>
                     <small class="text-muted">User yang melakukan input data dalam 30 hari terakhir</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
               <div class="card-body text-center p-4">
                   <div class="d-inline-flex align-items-center justify-content-center bg-info bg-opacity-10 text-info rounded-circle mb-3" style="width: 48px; height: 48px;">
                       <i class="bi bi-download fs-4"></i>
                   </div>
                   <h5 class="card-title mb-2 text-secondary">Total Download</h5>
                   <p class="display-5 fw-bold text-info mb-1">{{ $totalDownloads }}</p>
                    <small class="text-muted">Jumlah instalasi aplikasi (PWA)</small>
               </div>
           </div>
       </div>
    </div>

    <!-- User Lists: Pengguna Aktif & Semua Pengguna -->
    <div class="row mt-2">
        <!-- Pengguna Aktif (30 Hari) -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0 fw-bold">
                            <i class="bi bi-person-check-fill text-success me-2"></i>Pengguna Aktif (30 Hari)
                        </h6>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">
                            {{ $activeUsersList->count() }} User
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($activeUsersList->isEmpty())
                        <div class="text-center py-5 px-3">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-person-dash text-secondary fs-3"></i>
                            </div>
                            <h6 class="fw-semibold text-secondary mb-1">Belum Ada Pengguna Aktif</h6>
                            <p class="text-muted small mb-0">Tidak ada pengguna yang melakukan input transaksi dalam 30 hari terakhir.</p>
                        </div>
                    @else
                        <div class="list-group list-group-flush border-top border-light" style="max-height: 520px; overflow-y: auto;">
                            @foreach($activeUsersList as $activeUser)
                                <div class="list-group-item px-3 py-3 d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center text-truncate me-2">
                                        @if($activeUser->avatar)
                                            <img src="{{ $activeUser->avatar }}" class="rounded-circle me-3 flex-shrink-0" width="38" height="38" alt="{{ $activeUser->name }}">
                                        @else
                                            <div class="rounded-circle bg-success bg-opacity-10 text-success fw-bold d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 38px; height: 38px; font-size: 14px;">
                                                {{ strtoupper(substr($activeUser->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div class="text-truncate">
                                            <div class="fw-bold text-dark text-truncate">{{ $activeUser->name }}</div>
                                            <div class="text-muted small text-truncate">Bergabung {{ $activeUser->created_at ? $activeUser->created_at->format('d M Y') : '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="text-end flex-shrink-0">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle mb-1">
                                            <i class="bi bi-circle-fill me-1" style="font-size: 6px; vertical-align: middle;"></i>Aktif
                                        </span>
                                        @if($activeUser->last_activity_at)
                                            <div class="text-muted" style="font-size: 11px;">
                                                {{ $activeUser->last_activity_at->diffForHumans() }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Semua Pengguna Terdaftar -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <h6 class="card-title mb-0 fw-bold">
                                <i class="bi bi-people-fill text-primary me-2"></i>Semua Pengguna Terdaftar
                            </h6>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1">
                                {{ $allUsers->count() }} Pengguna
                            </span>
                            <div class="input-group input-group-sm" style="width: 200px;">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" id="userSearchInput" class="form-control border-start-0 ps-0" placeholder="Cari nama user...">
                            </div>
                        </div>
                    </div>
                    <!-- Quick Filter Buttons -->
                    <div class="mt-3 d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary active filter-btn" data-filter="all">
                            Semua ({{ $allUsers->count() }})
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success filter-btn" data-filter="active">
                            Aktif ({{ $activeUsersList->count() }})
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary filter-btn" data-filter="inactive">
                            Tidak Aktif ({{ $allUsers->count() - $activeUsersList->count() }})
                        </button>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 520px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0" id="usersTable">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th class="ps-3 py-2 text-secondary" style="font-size: 12px; width: 40px;">#</th>
                                    <th class="py-2 text-secondary" style="font-size: 12px;">NAMA USER</th>
                                    <th class="py-2 text-secondary" style="font-size: 12px;">TERDAFTAR</th>
                                    <th class="py-2 text-secondary" style="font-size: 12px;">AKTIVITAS TERAKHIR</th>
                                    <th class="pe-3 py-2 text-secondary text-center" style="font-size: 12px; width: 100px;">STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allUsers as $index => $u)
                                    <tr class="user-row" data-status="{{ $u->is_active ? 'active' : 'inactive' }}" data-name="{{ strtolower($u->name) }}">
                                        <td class="ps-3 text-muted small">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($u->avatar)
                                                    <img src="{{ $u->avatar }}" class="rounded-circle me-2 flex-shrink-0" width="32" height="32" alt="{{ $u->name }}">
                                                @else
                                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center me-2 flex-shrink-0" style="width: 32px; height: 32px; font-size: 12px;">
                                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <span class="fw-semibold text-dark">{{ $u->name }}</span>
                                            </div>
                                        </td>
                                        <td class="text-muted small">
                                            {{ $u->created_at ? $u->created_at->format('d M Y') : '-' }}
                                        </td>
                                        <td class="small">
                                            @if($u->last_activity_at)
                                                <span class="text-dark" title="{{ $u->last_activity_at->format('d M Y H:i') }}">
                                                    {{ $u->last_activity_at->diffForHumans() }}
                                                </span>
                                            @else
                                                <span class="text-muted fst-italic">Belum ada</span>
                                            @endif
                                        </td>
                                        <td class="pe-3 text-center">
                                            @if($u->is_active)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                                    <i class="bi bi-check-circle-fill me-1"></i>Aktif
                                                </span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">
                                                    <i class="bi bi-dash-circle me-1"></i>Tidak Aktif
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Tidak ada pengguna ditemukan.</td>
                                    </tr>
                                @endforelse
                                <tr id="noResultsRow" style="display: none;">
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="bi bi-search me-1"></i>Tidak ada pengguna yang cocok dengan pencarian.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section: Growth Chart -->
    <div class="row justify-content-center mt-2">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title mb-4">Pertumbuhan Pengguna (1 Tahun Terakhir)</h5>
                    <div style="position: relative; height: 400px; width: 100%;">
                        <canvas id="userGrowthChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Chart Initialization
        const ctx = document.getElementById('userGrowthChart').getContext('2d');
        
        const labels = @json($months);
        const data = @json($growthData);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Pengguna',
                    data: data,
                    borderColor: '#4e73df', // Primary blue
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#4e73df',
                    pointHoverBackgroundColor: '#4e73df',
                    pointHoverBorderColor: '#fff',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    tension: 0.3, // Smooth curve
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            font: {
                                family: "'Nunito', sans-serif",
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                        titleColor: '#6e707e',
                        bodyColor: '#858796',
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        padding: 10,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'Total: ' + context.parsed.y + ' Pengguna';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [2, 2],
                            drawBorder: false,
                            color: '#e3e6f0'
                        },
                        ticks: {
                            padding: 10,
                            font: {
                                family: "'Nunito', sans-serif"
                            },
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            padding: 10,
                            font: {
                                family: "'Nunito', sans-serif"
                            }
                        }
                    }
                }
            }
        });

        // User Search and Filter Logic
        const searchInput = document.getElementById('userSearchInput');
        const filterButtons = document.querySelectorAll('.filter-btn');
        const rows = document.querySelectorAll('.user-row');
        const noResultsRow = document.getElementById('noResultsRow');

        let currentFilter = 'all';
        let currentSearch = '';

        function applyUserFilters() {
            let visibleCount = 0;
            rows.forEach(row => {
                const status = row.getAttribute('data-status');
                const name = row.getAttribute('data-name');

                const matchesStatus = (currentFilter === 'all') || (status === currentFilter);
                const matchesSearch = !currentSearch || name.includes(currentSearch);

                if (matchesStatus && matchesSearch) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (noResultsRow) {
                noResultsRow.style.display = visibleCount === 0 ? '' : 'none';
            }
        }

        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                currentSearch = e.target.value.toLowerCase().trim();
                applyUserFilters();
            });
        }

        filterButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                filterButtons.forEach(b => {
                    b.classList.remove('active');
                });
                this.classList.add('active');
                currentFilter = this.getAttribute('data-filter');
                applyUserFilters();
            });
        });
    });
</script>
@endsection

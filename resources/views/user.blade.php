@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center p-4">
                    <h5 class="card-title mb-3 text-secondary">Pengguna Terdaftar</h5>
                    <p class="display-4 fw-bold text-primary">{{ $totalUsers }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
             <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center p-4">
                    <h5 class="card-title mb-3 text-secondary">Pengguna Aktif (30 Hari)</h5>
                    <p class="display-4 fw-bold text-success">{{ $activeUsers }}</p>
                     <small class="text-muted">User yang melakukan input data dalam 30 hari terakhir</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
               <div class="card-body text-center p-4">
                   <h5 class="card-title mb-3 text-secondary">Total Download</h5>
                   <p class="display-4 fw-bold text-info">{{ $totalDownloads }}</p>
                    <small class="text-muted">Jumlah instalasi aplikasi (PWA)</small>
               </div>
           </div>
       </div>
    </div>

    <div class="row justify-content-center mt-4">
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
    });
</script>
@endsection

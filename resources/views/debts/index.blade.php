@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h4 class="fw-bold mb-3">Catatan Hutang & Piutang</h4>
    </div>

    <!-- Summary Cards -->
    <div class="col-6 mb-3">
        <div class="card border-0 shadow-sm text-white" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border-radius: 1rem;">
            <div class="card-body p-3">
                <small class="text-white-50 d-block mb-1">Total Hutang (Saya)</small>
                <h5 class="fw-bold mb-0">Rp {{ number_format($totalPayable, 0, ',', '.') }}</h5>
            </div>
        </div>
    </div>
    <div class="col-6 mb-3">
        <div class="card border-0 shadow-sm text-white" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 1rem;">
            <div class="card-body p-3">
                <small class="text-white-50 d-block mb-1">Total Piutang (Orang)</small>
                <h5 class="fw-bold mb-0">Rp {{ number_format($totalReceivable, 0, ',', '.') }}</h5>
            </div>
        </div>
    </div>
</div>

<!-- Tabs -->
<ul class="nav nav-pills mb-3 nav-fill gap-2 p-1 bg-white rounded-pill shadow-sm" id="debtTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active rounded-pill" id="payable-tab" data-bs-toggle="tab" data-bs-target="#payable" type="button" role="tab" aria-selected="true">
            <i class="bi bi-arrow-down-circle me-1"></i> Hutang
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link rounded-pill" id="receivable-tab" data-bs-toggle="tab" data-bs-target="#receivable" type="button" role="tab" aria-selected="false">
            <i class="bi bi-arrow-up-circle me-1"></i> Piutang
        </button>
    </li>
</ul>

<div class="tab-content" id="debtTabsContent">
    <!-- Hutang Tab -->
    <div class="tab-pane fade show active" id="payable" role="tabpanel">
        @if($payables->isEmpty())
            <div class="text-center py-5 d-flex flex-column align-items-center">
                <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" width="80" class="mb-3 opacity-50">
                <p class="text-muted">Alhamdulillah, tidak ada hutang.</p>
            </div>
        @else
            <div class="d-flex flex-column gap-3">
                @foreach($payables as $debt)
                    @include('debts.card', ['debt' => $debt])
                @endforeach
            </div>
        @endif
    </div>

    <!-- Piutang Tab -->
    <div class="tab-pane fade" id="receivable" role="tabpanel">
        @if($receivables->isEmpty())
            <div class="text-center py-5 d-flex flex-column align-items-center">
                <img src="https://cdn-icons-png.flaticon.com/512/2953/2953363.png" width="80" class="mb-3 opacity-50">
                <p class="text-muted">Belum ada catatan piutang.</p>
            </div>
        @else
            <div class="d-flex flex-column gap-3">
                @foreach($receivables as $debt)
                    @include('debts.card', ['debt' => $debt])
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- FAB Add Button -->
<button class="btn btn-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center position-fixed p-0" 
    style="width: 60px; height: 60px; bottom: 25px; left: 50%; transform: translateX(-50%); z-index: 1050; border: 4px solid #fff;"
    data-bs-toggle="modal" data-bs-target="#createDebtModal">
    <i class="bi bi-plus-lg fs-2"></i>
</button>

<style>
    /* Bottom Navigation Bar */
    .bottom-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        background: #fff;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-around;
        align-items: center;
        padding: 10px 0 25px 0; /* Extra padding for safe area */
        z-index: 1000;
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
    }

    .nav-item {
        text-align: center;
        color: #9ca3af;
        text-decoration: none;
        font-size: 0.75rem;
        flex: 1;
        background: none;
        border: none;
        padding: 5px;
    }

    .nav-item i {
        display: block;
        font-size: 1.4rem;
        margin-bottom: 2px;
        transition: transform 0.2s;
    }

    .nav-item.active {
        color: #0d6efd;
        font-weight: 600;
    }

    .nav-item.active i {
        transform: translateY(-2px);
    }
</style>

<!-- Spacer -->
<div style="height: 100px;"></div>

<!-- Bottom Navigation -->
<nav class="bottom-nav">
    <a href="{{ route('dashboard') }}" class="nav-item">
        <i class="bi bi-house-door-fill"></i>
        <span>Home</span>
    </a>
    <a href="{{ route('dashboard', ['tab' => 'history']) }}" class="nav-item">
        <i class="bi bi-receipt"></i>
        <span>Transaksi</span>
    </a>
    <div style="width: 50px;"></div> <!-- Spacer for FAB -->
    <a href="{{ route('dashboard', ['tab' => 'stats']) }}" class="nav-item">
        <i class="bi bi-graph-up"></i>
        <span>Statistik</span>
    </a>
    <a href="{{ route('dashboard', ['tab' => 'profile']) }}" class="nav-item active">
        <i class="bi bi-person-fill"></i>
        <span>Profil</span>
    </a>
</nav>

@include('debts.create_modal')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hasSeenTour = localStorage.getItem('debts_tour_completed');
        
        if (!hasSeenTour) {
            setTimeout(() => {
                introJs().setOptions({
                    steps: [
                        {
                            element: document.querySelector('.card.border-0.shadow-sm.text-white'),
                            intro: 'Ini adalah ringkasan total Hutang (yang harus kamu bayar) dan Piutang (uangmu di orang lain).',
                            position: 'bottom'
                        },
                        {
                            element: document.querySelector('#debtTabs'),
                            intro: 'Gunakan tab ini untuk berpindah antara daftar Hutang dan Piutang.',
                            position: 'bottom'
                        },
                        {
                            element: document.querySelector('.btn-primary.rounded-circle.position-fixed'),
                            intro: 'Ketuk tombol ini untuk mencatat hutang atau piutang baru.',
                            position: 'top'
                        }
                    ],
                    showProgress: true,
                    showBullets: false,
                    nextLabel: 'Lanjut',
                    prevLabel: 'Kembali',
                    doneLabel: 'Selesai',
                    exitOnOverlayClick: false
                }).oncomplete(function() {
                    localStorage.setItem('debts_tour_completed', 'true');
                }).onexit(function() {
                    localStorage.setItem('debts_tour_completed', 'true');
                }).start();
            }, 1000);
        }
    });
</script>

@endsection

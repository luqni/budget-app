@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h4 class="fw-bold mb-3">Target Tabungan (Impian)</h4>
    </div>

    <!-- Summary Card -->
    <div class="col-12 mb-3">
        <div class="card border-0 shadow-sm text-white" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); border-radius: 1rem;">
            <div class="card-body p-4 text-center">
                <small class="text-white-50 d-block mb-1">Total Tabungan Terkumpul</small>
                <h2 class="fw-bold mb-0">Rp {{ number_format($totalSavings, 0, ',', '.') }}</h2>
            </div>
        </div>
    </div>
</div>

<!-- Savings List -->
<div class="row g-3 mb-5">
    @if($savings->isEmpty())
        <div class="col-12 text-center py-5">
            <div class="mb-3 display-1 opacity-25">🎯</div>
            <p class="text-muted">Belum ada target impian.<br>Yuk, mulai buat target pertamamu!</p>
        </div>
    @else
        @foreach($savings as $saving)
            <div class="col-12 col-md-6">
                <div class="card border-0 shadow-sm p-3 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm" 
                                style="width: 50px; height: 50px; background-color: {{ $saving->color }}20; color: {{ $saving->color }}; font-size: 1.5rem;">
                                {{ $saving->icon ?? '💰' }}
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">{{ $saving->name }}</h6>
                                <small class="text-muted">Target: Rp {{ number_format($saving->target_amount, 0, ',', '.') }}</small>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                                <li>
                                    <button class="dropdown-item" onclick="editSaving({{ $saving->id }}, '{{ $saving->name }}', {{ $saving->target_amount }})">
                                        <i class="bi bi-pencil me-2"></i> Edit
                                    </button>
                                </li>
                                <li>
                                    <form action="{{ route('savings.destroy', $saving->id) }}" method="POST" onsubmit="return confirm('Yakin hapus target ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-trash me-2"></i> Hapus
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    @php
                        $percentage = $saving->target_amount > 0 ? min(100, ($saving->current_amount / $saving->target_amount) * 100) : 0;
                    @endphp
                    <div class="progress rounded-pill mb-2" style="height: 10px; background-color: #f1f5f9;">
                        <div class="progress-bar rounded-pill progress-bar-striped progress-bar-animated" role="progressbar" 
                             style="width: {{ $percentage }}%; background-color: {{ $saving->color }};" 
                             aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold text-dark small">Terkumpul: Rp {{ number_format($saving->current_amount, 0, ',', '.') }}</span>
                        <span class="fw-bold small" style="color: {{ $saving->color }}">{{ number_format($percentage, 0) }}%</span>
                    </div>

                    <button class="btn btn-outline-primary w-100 rounded-pill btn-sm fw-bold" 
                        onclick="showDepositModal({{ $saving->id }}, '{{ $saving->name }}')">
                        <i class="bi bi-plus-circle me-1"></i> Nabung
                    </button>
                </div>
            </div>
        @endforeach
    @endif
</div>

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

<!-- FAB Add Button -->
<button class="btn btn-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center position-fixed p-0" 
    style="width: 60px; height: 60px; bottom: 25px; left: 50%; transform: translateX(-50%); z-index: 1050; border: 4px solid #fff;"
    data-bs-toggle="modal" data-bs-target="#createSavingModal">
    <i class="bi bi-plus-lg fs-2"></i>
</button>

@include('savings.create_modal')
@include('savings.deposit_modal')

<script>
    function showDepositModal(id, name) {
        document.getElementById('depositSavingId').value = id;
        document.getElementById('depositTitle').innerText = 'Nabung ke ' + name;
        new bootstrap.Modal(document.getElementById('depositModal')).show();
    }

    function editSaving(id, name, target) {
        // Simple edit logic: populate create modal and change action
        // For simplicity now, we might just focus on add/deposit as per MVP,
        // or quickly implement edit modal if needed.
        // Let's stick to Create/Delete/Deposit for "Simple"
        // But if user wants edit:
        const modal = new bootstrap.Modal(document.getElementById('createSavingModal'));
        const form = document.querySelector('#createSavingModal form');
        
        form.action = '/savings/' + id;
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PUT';
        form.appendChild(methodInput);

        form.querySelector('[name="name"]').value = name;
        form.querySelector('[name="target_amount"]').value = target;
        
        document.querySelector('#createSavingModal .modal-title').innerText = 'Edit Target';
        
        modal.show();

        // Reset on close
        document.getElementById('createSavingModal').addEventListener('hidden.bs.modal', function () {
            form.action = "{{ route('savings.store') }}";
            if(methodInput.parentNode) methodInput.parentNode.removeChild(methodInput);
            form.reset();
            document.querySelector('#createSavingModal .modal-title').innerText = 'Buat Target Baru';
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const hasSeenTour = localStorage.getItem('savings_tour_completed');
        
        if (!hasSeenTour) {
            setTimeout(() => {
                introJs().setOptions({
                    steps: [
                        {
                            element: document.querySelector('.card.border-0.shadow-sm.text-white'),
                            intro: 'Total tabungan yang sudah terkumpul dari semua target impianmu muncul di sini.',
                            position: 'bottom'
                        },
                        {
                            element: document.querySelector('.btn-primary.rounded-circle.position-fixed'),
                            intro: 'Mulai buat Target Impian baru (misal: Umroh, Laptop, Hewan Qurban) di sini.',
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
                    localStorage.setItem('savings_tour_completed', 'true');
                }).onexit(function() {
                    localStorage.setItem('savings_tour_completed', 'true');
                }).start();
            }, 1000);
        }
    });
</script>

@endsection

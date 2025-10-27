@extends('layouts.app')
@section('content')
<style>
    /* Mobile-first styles */
    .summary-card {
        padding: 1rem;
        border-radius: 12px;
        color: #fff;
        font-weight: 600;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
    }

    .income { background-color: #22c55e; }    /* green */
    .expense { background-color: #ef4444; }   /* red */
    .balance { background-color: #3b82f6; }   /* blue */

    /* Responsive layout */
    @media (max-width: 768px) {
        .summary-card h5 { font-size: 0.9rem; margin-bottom: 0.25rem; }
        .summary-card p { font-size: 1rem; }
        .col-4 { width: 100%; }
        .row.g-3 { display: flex; flex-direction: column; gap: 0.75rem; }
    }

    /* Card spacing and layout */
    .card {
        border-radius: 12px;
        border: none;
    }

    .list-group-item {
        border: none;
        border-bottom: 1px solid #eee;
        font-size: 0.95rem;
        padding: 0.75rem 0.5rem;
    }

    .list-group-item:last-child {
        border-bottom: none;
    }

    .btn-sm {
        font-size: 0.8rem;
    }

    @media (max-width: 576px) {
        .list-group-item {
            flex-direction: column;
            align-items: flex-start;
        }
        .list-group-item .text-section {
            margin-bottom: 0.5rem;
        }
    }

    /* Center dashboard on mobile */
    .container {
        padding: 0 1rem;
    }

    /* Chart title */
    .chart-title {
        font-size: 1rem;
        font-weight: 600;
        text-align: center;
        margin-bottom: 0.75rem;
    }
</style>

<div class="container py-3">
    {{-- Ringkasan --}}
    <div class="row g-3 mb-3 text-center">
        <div class="col-4 col-md-4 mb-2 mb-md-0">
            <div class="summary-card income">
                <h5>Pemasukan</h5>
                <p id="totalPemasukanCard">Rp {{ number_format($income ?? 5000000) }}</p>
            </div>
        </div>
        <div class="col-4 col-md-4 mb-2 mb-md-0">
            <div class="summary-card expense">
                <h5>Pengeluaran</h5>
                <p id="totalExpenseCard">Rp {{ number_format($expense ?? 2500000) }}</p>
            </div>
        </div>
        <div class="col-4 col-md-4 mb-2 mb-md-0">
            <div class="summary-card balance">
                <h5>Saldo</h5>
                <p id="totalSaldoCard">Rp {{ number_format(($income ?? 5000000) - ($expense ?? 2500000)) }}</p>
            </div>
        </div>
    </div>

    {{-- Grafik --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="chart-title">Pengeluaran Bulanan (12 Bulan Terakhir)</div>
            <canvas id="expenseChart" height="150"></canvas>
        </div>
    </div>

    {{-- Input Dinamis --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="mb-3 text-center">Tambah Catatan</h5>
            <form id="noteForm" class="mb-3">
                @csrf
                <div class="row g-2">
                    <div class="col-12 col-md-8 mb-2 mb-md-0">
                        <input type="text" id="noteText" class="form-control" placeholder="Bayar kos 500000" required>
                    </div>
                    <div class="col-12 col-md-4">
                        <button class="btn btn-primary w-100" type="submit">Tambah</button>
                    </div>
                </div>
            </form>

            <ul id="notesList" class="list-group mt-3">
                @foreach ($expenses as $exp)
                    <li class="list-group-item d-flex justify-content-between align-items-center" data-id="{{ $exp->id }}">
                        <div class="text-section">
                            <span class="note-text">{{ $exp->note }}</span>
                            <span class="fw-bold text-danger ms-2">Rp {{ number_format($exp->amount) }}</span>
                        </div>
                        <div class="mt-2 mt-md-0">
                            <button class="btn btn-sm btn-outline-secondary edit-btn me-2">Edit</button>
                            <button class="btn btn-sm btn-outline-danger delete-btn">Hapus</button>
                        </div>
                    </li>
                @endforeach
            </ul>

            <div class="mt-3 text-end fw-bold">
                Total: Rp <span id="totalExpense">{{ number_format($totalExpense) }}</span>
            </div>
        </div>
    </div>
</div>

@include('partials.chart-script')
@endsection

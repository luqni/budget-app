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

    /* Styling untuk daftar catatan */
    #notesList {
        list-style: none;
        padding: 0;
        margin-top: 1rem;
    }

    #notesList li {
        border: 1px solid #e5e7eb; /* garis pembatas */
        border-radius: 10px;
        padding: 12px 14px;
        margin-bottom: 10px;
        background: #fff;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        transition: background 0.2s ease-in-out, transform 0.1s ease-in-out;
    }

    #notesList li:hover {
        background: #f9fafb;
        transform: scale(1.01);
    }

    .text-section {
        flex: 1;
        font-size: 15px;
        line-height: 1.5;
        word-break: break-word;
    }

    .text-section .note-text {
        font-weight: 500;
    }

    .text-section .fw-bold {
        display: inline-block;
        margin-top: 4px;
        color: #dc2626;
        font-size: 14px;
    }

    .btn {
        font-size: 13px;
    }

    @media (max-width: 576px) {
        #notesList li {
            flex-direction: column;
            align-items: flex-start;
        }

        #notesList li div:last-child {
            margin-top: 8px;
        }
    }

    * Tambahan style untuk grouping per bulan */
    .month-divider {
        border-bottom: 2px solid #3b82f6;
        margin: 1.5rem 0 1rem;
        font-weight: 600;
        color: #1e3a8a;
        font-size: 1rem;
    }

    .note-date {
        font-size: 0.85rem;
        color: #6b7280;
        display: block;
    }

    #notesList li {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 12px 14px;
        margin-bottom: 10px;
        background: #fff;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        transition: background 0.2s ease-in-out, transform 0.1s ease-in-out;
    }

    #notesList li:hover {
        background: #f9fafb;
        transform: scale(1.01);
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
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-md-3 mb-2 mb-md-0">
                        <label for="noteMonth" class="form-label">Bulan</label>
                        <input type="month" id="noteMonth" class="form-control" value="{{ now()->format('Y-m') }}" required>
                    </div>
                    <div class="col-12 col-md-7 mb-2 mb-md-0">
                        <label for="noteText" class="form-label">Catatan</label>
                        <input type="text" id="noteText" class="form-control" placeholder="Bayar kos 500000" required>
                    </div>
                    <div class="col-12 col-md-2">
                        <button class="btn btn-primary w-100" type="submit">Tambah</button>
                    </div>
                </div>
            </form>

            {{-- Filter Bulan Arsip --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form id="monthFilterForm" class="d-flex align-items-center justify-content-between flex-wrap">
                        <label for="monthSelect" class="fw-semibold me-2 mb-2 mb-md-0">Lihat Arsip Bulan:</label>
                        <select id="monthSelect" class="form-select w-auto" name="month">
                            <option value="">Semua Bulan</option>
                            @foreach ($months as $month)
                                <option value="{{ $month }}" {{ $selectedMonth == $month ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y') }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>

            <div id="notesList">
                @foreach ($groupedExpenses as $month => $expenses)
                    <div class="month-divider">
                        {{ \Carbon\Carbon::parse($month . '-01')->translatedFormat('F Y') }}
                    </div>

                    <ul class="list-unstyled">
                        @foreach ($expenses as $exp)
                            <li data-id="{{ $exp->id }}">
                                <div class="text-section">
                                    <span class="note-text">{{ $exp->note }}</span><br>
                                    <span class="fw-bold">Rp {{ number_format($exp->amount) }}</span>
                                    <span class="note-date">
                                        {{ \Carbon\Carbon::parse($exp->created_at)->translatedFormat('d M Y') }}
                                    </span>
                                </div>
                                <div>
                                    <button class="btn btn-sm btn-outline-secondary edit-btn me-2">Edit</button>
                                    <button class="btn btn-sm btn-outline-danger delete-btn">Hapus</button>
                                    <button class="btn btn-sm btn-outline-primary detail-btn">Detail</button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            </div>

            <div class="mt-3 text-end fw-bold">
                Total: Rp <span id="totalExpense">{{ number_format($totalExpense) }}</span>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Belanja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <h6 id="detailTitle" class="mb-3"></h6>

                    <table class="table table-sm" id="detailTable">
                        <thead>
                            <tr>
                                <th>Nama Item</th>
                                <th>Qty</th>
                                <th>Harga (Rp)</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                    <hr>

                    <form id="detailForm">
                        @csrf
                        <input type="hidden" id="parentNoteId">

                        <div class="row g-2">
                            <div class="col-md-6">
                                <input type="text" id="detailName" class="form-control" placeholder="Nama item" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" id="detailQty" class="form-control" placeholder="Qty" required min="1">
                            </div>
                            <div class="col-md-3">
                                <input type="number" id="detailPrice" class="form-control" placeholder="Harga" required min="0">
                            </div>
                            <div class="col-md-1 d-grid">
                                <button class="btn btn-primary btn-sm">+</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

@include('partials.chart-script')
@endsection

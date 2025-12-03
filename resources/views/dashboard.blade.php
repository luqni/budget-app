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
    .realization { background-color: #d47f00ff; } 

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

    .text-section .realization-amount {
        display: inline-block;
        margin-top: 4px;
        color: #d47f00ff;
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

    /* Efek hover supaya terasa hidup */
    .summary-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.18);
    }

    .sensitive.hidden {
        filter: blur(6px);
        transition: 0.2s;
    }

    /* Warna tombol */
    #chatIcon button {
        background: linear-gradient(135deg, #007bff, #00b4d8);
        color: white;
        border: none;
        font-size: 24px;
    }

    #chatBoxContainer {
        animation: fadeInUp 0.3s ease;
        max-height: 80vh;
    }

    @keyframes fadeInUp {
        from {
            transform: translateY(30px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* 🌐 RESPONSIVE MOBILE */
    @media (max-width: 768px) {
        #chatBoxContainer {
            width: 90%;
            right: 5%;
            left: 5%;
            bottom: 80px;
            margin: 0;
            max-height: 75vh;
            border-radius: 1rem;
        }

        #chatContent {
            height: 55vh !important;
        }

        #chatIcon {
            bottom: 20px !important;
            right: 20px !important;
        }

        #chatIcon button {
            width: 55px;
            height: 55px;
            font-size: 22px;
        }
    }


</style>

<div class="container py-3">
    {{-- Ringkasan --}}
    <div class="row g-3 mb-3 text-center">

        <!-- PEMASUKAN -->
        <div class="col-4 col-md-3 mb-2 mb-md-0">
            <div class="summary-card income d-flex align-items-center justify-content-between shadow-sm p-2">

                <div>
                    <h5>Pemasukan</h5>
                    <p id="totalPemasukanCard" class="sensitive">Rp {{ number_format($income) }}</p>
                </div>

                <div class="d-flex align-items-center gap-3">

                    <!-- Edit -->
                    <i class="bi bi-pencil-square"
                    style="font-size: 1.3rem; cursor:pointer;"
                    data-bs-toggle="modal"
                    data-bs-target="#editIncomeModal"></i>

                    <!-- Hide/Unhide -->
                    <i class="bi bi-eye toggle-visibility"
                    data-target="#totalPemasukanCard"
                    style="font-size: 1.3rem; cursor:pointer;"></i>

                </div>
            </div>
        </div>

        <!-- ALOKASI -->
        <div class="col-4 col-md-3 mb-2 mb-md-0 shadow-sm">
            <div class="summary-card expense">
                <h5>Alokasi</h5>
                <p id="totalExpenseCard">Rp {{ number_format($expense ?? 0) }}</p>
            </div>
        </div>

        <!-- REALISASI -->
        <div class="col-4 col-md-3 mb-2 mb-md-0 shadow-sm">
            <div class="summary-card realization">
                <h5>Realisasi</h5>
                <p id="totalRealizationCard">Rp {{ number_format($realization ?? 0) }}</p>
            </div>
        </div>

        <!-- SALDO -->
        <div class="col-4 col-md-3 mb-2 mb-md-0 shadow-sm">
            <div class="summary-card balance d-flex align-items-center justify-content-between p-2">

                <div>
                    <h5>Saldo</h5>
                    <p id="totalSaldoCard" class="sensitive">Rp {{ number_format($balance ?? 0) }}</p>
                </div>

                <!-- Hide/Unhide -->
                <i class="bi bi-eye toggle-visibility"
                data-target="#totalSaldoCard"
                style="font-size: 1.3rem; cursor:pointer;"></i>

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
                        <input type="month" id="noteMonth" class="form-control shadow-sm" value="{{ now()->format('Y-m') }}" required>
                    </div>
                    <div class="col-12 col-md-7 mb-2 mb-md-0">
                        <label for="noteText" class="form-label">Catatan</label>
                        <input type="text" id="noteText" class="form-control shadow-sm" placeholder="Belanja Bulanan 500000" required>
                    </div>
                    <div class="col-12 col-md-2">
                        <button class="btn btn-primary w-100 shadow-sm" type="submit">Tambah</button>
                    </div>
                </div>
            </form>

            {{-- Filter Bulan Arsip --}}
            <div class="card shadow-sm mb-4 ">
                <div class="card-body">
                    <form id="monthFilterForm" class="d-flex align-items-center justify-content-between flex-wrap">
                        <label for="monthSelect" class="fw-semibold me-2 mb-2 mb-md-0">Lihat Arsip Bulan:</label>
                        <select id="monthSelect" class="form-select w-auto shadow-sm" name="month">
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

            <div class="mb-3 d-flex justify-content-end">
                <div class="input-group" style="max-width: 260px;">
                    <span class="input-group-text bg-white shadow-sm">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" id="searchNotes" class="form-control shadow-sm" placeholder="Cari catatan...">
                </div>
            </div>


            <div id="notesList">
                @foreach ($groupedExpenses as $month => $expenses)
                    <div class="month-divider">
                        {{ \Carbon\Carbon::parse($month . '-01')->translatedFormat('F Y') }}
                    </div>

                    <ul class="list-unstyled shadow-sm">
                        @foreach ($expenses as $exp)
                            <li data-id="{{ $exp->id }}">
                                <div class="text-section">
                                    <span class="note-text">{{ $exp->note }}</span><br>
                                    Alokasi : <span class="fw-bold">Rp {{ number_format($exp->amount) }}</span> <br/>
                                    Realisasi : <span class="realization-amount">Rp {{ number_format($exp->total_realisasi) }}</span>
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

                    <!-- Scrollable Table -->
                    <div style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-sm" id="detailTable">
                            <thead class="table-light">
                                <tr>
                                    <th></th>
                                    <th>Nama Item</th>
                                    <th>Qty</th>
                                    <th>Harga (Rp)</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <hr>

                    <form id="detailForm">
                        @csrf
                        <input type="hidden" id="parentNoteId">

                        <div class="row g-2">
                            <div class="col-md-6">
                                <input type="text" id="detailName" class="form-control shadow-sm" placeholder="Nama item" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" id="detailQty" class="form-control shadow-sm" placeholder="Qty" required min="1">
                            </div>
                            <div class="col-md-3">
                                <input type="number" id="detailPrice" class="form-control shadow-sm" placeholder="Harga" required min="0">
                            </div>
                            <div class="col-md-1 d-grid">
                                <button class="btn btn-primary btn-sm shadow-sm"> 
                                    <i class="bi bi-bag-plus-fill" style="font-size: 1.3rem;"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                </div>


            </div>
        </div>
    </div>

    @if(session('need_income') || auth()->user()->income == 0)
    <div class="modal fade show" id="incomeModal" style="display:block; background:rgba(0,0,0,0.6)">
        <div class="modal-dialog">
            <div class="modal-content p-3">
                <h4 class="mb-3">Masukkan Pemasukan Awal</h4>
                <form action="{{ route('income.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="monthSelect" class="form-label fw-semibold">Pilih Bulan</label>
                        <select id="monthSelect" class="form-select shadow-sm" name="monthIncome">
                            <option value="" disabled> --Pilih Bulan-- </option>
                            @foreach ($months as $month)
                                <option value="{{ $month }}" {{ $selectedMonth == $month ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="incomeInput" class="form-label fw-semibold">Total Pemasukan</label>
                        <input 
                            type="number" 
                            name="income" 
                            id="incomeInput"
                            class="form-control shadow-sm" 
                            value="{{ $income }}" 
                            required
                        >
                    </div>
                    <button class="btn btn-primary mt-3 w-100">Simpan</button>
                </form>
            </div>
        </div>
    </div>
    @endif

    <div class="modal fade" id="editIncomeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content p-3 position-relative">

                <!-- Tombol Close -->
                <button type="button" class="btn-close position-absolute" 
                    style="right: 10px; top: 10px;" 
                    data-bs-dismiss="modal" aria-label="Close"></button>

                <h4 class="mb-3">Edit Pemasukan</h4>
                <form action="{{ route('income.update') }}" method="POST" class="p-3">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="monthSelect" class="form-label fw-semibold">Pilih Bulan</label>
                        <select id="monthSelect" class="form-select shadow-sm" name="monthIncome">
                            <option value="" disabled> --Pilih Bulan-- </option>
                            @foreach ($months as $month)
                                <option value="{{ $month }}" {{ $selectedMonth == $month ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="incomeInput" class="form-label fw-semibold">Total Pemasukan</label>
                        <input 
                            type="number" 
                            name="income" 
                            id="incomeInput"
                            class="form-control shadow-sm" 
                            value="{{ $income }}" 
                            required
                        >
                    </div>

                    <button class="btn btn-success w-100">Update</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Floating Chat Icon -->
    <div id="chatIcon" class="position-fixed bottom-0 end-0 m-4" style="margin-bottom:3.5rem !important;">
        <button class="btn btn-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center"
            style="width: 60px; height: 60px; font-size: 24px;">
            💬
        </button>
    </div>

    <!-- Chatbox -->
    <div id="chatBoxContainer"
        class="position-fixed bottom-0 end-0 m-4 shadow-lg rounded-4 bg-white border"
        style="width: 350px; display: none; z-index: 1050; margin-bottom: 2.5rem;">
        <div class="d-flex justify-content-between align-items-center p-2 border-bottom bg-primary text-white rounded-top-4">
            <strong>Asisten Keuangan AI</strong>
            <button id="closeChat" class="btn btn-sm btn-light">✕</button>
        </div>

        <div id="chatContent" class="p-3" style="height: 300px; overflow-y: auto; font-size: 14px;">
            <div class="text-muted small text-center">Klik tombol di bawah untuk ringkasan keuanganmu 📊</div>
        </div>

        <div class="p-3 border-top bg-light text-center rounded-bottom-4">
            <button id="summaryButton" class="btn btn-success w-100 fw-bold">
                💰 Ringkasan Keuangan Bulan Ini
            </button>
        </div>
    </div>


</div>

@include('partials.chart-script')
@endsection

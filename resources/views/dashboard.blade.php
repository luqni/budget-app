@extends('layouts.app')

@section('content')
<style>
    /* Mobile-first styles specific to Dashboard */
    .summary-card {
        padding: 1rem;
        border-radius: 16px;
        color: #fff;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        border: none;
        position: relative;
        overflow: hidden;
    }
    
    .summary-card::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, rgba(0,0,0,0) 70%);
        transform: rotate(30deg);
        pointer-events: none;
    }

    .income { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .expense { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
    .balance { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
    
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

    /* Floating Action Button (FAB) */
    .fab-container {
        position: fixed;
        bottom: 85px; /* Above nav bar */
        right: 20px;
        z-index: 1040;
    }

    .fab {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #0d6efd;
        color: white;
        border: none;
        box-shadow: 0 4px 15px rgba(13, 110, 253, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        transition: transform 0.2s;
    }

    .fab:active {
        transform: scale(0.95);
    }

    /* Tabs Content */
    .tab-content {
        display: none;
        animation: fadeIn 0.3s ease;
        padding-bottom: 80px; /* Space for FAB/Nav */
    }

    .tab-content.active {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* List Item Styling */
    .list-group-item {
        border: none;
        margin-bottom: 12px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.02);
        border-radius: 12px !important;
        background: #fff;
        padding: 12px 16px;
    }

    .sensitive {
        transition: all 0.3s ease;
        /* Pastikan elemen terlihat secara default */
        display: inline-block; 
    }

    .sensitive.hidden {
        /* Override display: none bawaan framework jika ada */
        display: inline-block !important; 
        
        /* Efek Blur & Obfuscation */
        filter: blur(8px);
        opacity: 0.5;
        user-select: none; /* Agar tidak bisa diblok/copas */
        pointer-events: none; /* Agar tidak bisa diklik */
        
    }

    .cursor-pointer {
        cursor: pointer;
    }
</style>

<!-- TABS CONTENT -->

<!-- 1. HOME TAB -->
<div id="tab-home" class="tab-content active">
    <!-- Mobile Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 pt-2">
        <div>
            <span class="text-muted small">Halo,</span>
            <h5 class="fw-bold m-0">{{ Auth::user()->name }}</h5>
        </div>
        <div class="d-flex align-items-center gap-3">
           <!-- Global Month Selector -->
           <form action="{{ route('dashboard') }}" method="GET" id="monthFilterForm">
               <select id="monthSelect" class="form-select form-select-sm shadow-sm border-0 bg-white text-primary fw-bold" style="width:auto;" name="month" onchange="this.form.submit()">
                   @foreach ($months as $month)
                       <option value="{{ $month }}" {{ $selectedMonth == $month ? 'selected' : '' }}>
                           {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y') }}
                       </option>
                   @endforeach
               </select>
           </form>
        </div>
    </div>

    <!-- Cards Row 1 -->
    <div class="row g-3 mb-3">
        <div class="col-12">
            <div class="summary-card balance d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="opacity-75">Sisa Saldo</h5>
                    <p class="m-0 fs-2 sensitive" id="saldoAmount" data-value="0">Rp 0</p>
                </div>
                <i class="bi bi-wallet2 fs-1 opacity-25"></i>
            </div>
            <div class="d-flex justify-content-end mt-2">
                 <small class="text-muted cursor-pointer toggle-visibility" data-target="#saldoAmount">
                     <i class="bi bi-eye" id="iconSaldo"></i> <span id="textSaldo">Sembunyikan</span>
                 </small>
            </div>
        </div>
    </div>

    <!-- Cards Row 2 -->
    <div class="row g-3 mb-4">
        <div class="col-6">
            <div class="summary-card income">
                <div class="d-flex justify-content-between">
                    <h5 class="opacity-75">Pemasukan</h5>
                    <i class="bi bi-arrow-down-circle opacity-50"></i>
                </div>
                <p class="m-0 mt-2 fs-5 sensitive" id="totalPemasukanCard">Rp {{ number_format($income ?? 0, 0, ',', '.') }}</p>
                 <div class="d-flex justify-content-between align-items-center mt-1">
                    <small class="text-white cursor-pointer toggle-visibility" data-target="#totalPemasukanCard" style="opacity: 0.75;">
                        <i class="bi bi-eye" id="iconPemasukan"></i> <span id="textPemasukan">Sembunyikan</span>
                    </small>
                    <i class="bi bi-pencil-square opacity-50 small cursor-pointer" data-bs-toggle="modal" data-bs-target="#editIncomeModal"></i>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="summary-card expense">
                <div class="d-flex justify-content-between">
                    <h5 class="opacity-75">Pengeluaran</h5>
                    <i class="bi bi-arrow-up-circle opacity-50"></i>
                </div>
                <p class="m-0 mt-2 fs-5 sensitive" id="totalRealizationCard">Rp {{ number_format($totalRealization ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Bar Chart Expense per Month -->
    <div class="bg-white p-3 rounded-3 shadow-sm mb-4 border">
        <canvas id="expenseChart" height="250"></canvas>
    </div>

    <!-- Doughnut Chart Expense per Category -->
    <h6 class="fw-bold mb-3 px-2">Pengeluaran per Kategori (3 Bulan Terakhir)</h6>
    <div class="bg-white p-3 rounded-3 shadow-sm mb-3 border">
        <div style="position: relative; height: 250px;">
            <canvas id="categoryChart"></canvas>
        </div>
    </div>

    <!-- Quick Actions / Promo or Insight -->
    <div class="bg-white p-3 rounded-4 shadow-sm mb-3 border">
        <div class="d-flex align-items-center mb-2">
             <i class="bi bi-robot text-primary me-2 fs-4"></i>
             <h6 class="m-0 fw-bold">Asisten Keuangan</h6>
        </div>
        <p class="text-muted small m-0">
            Pengeluaranmu bulan ini cukup terkendali. Cek statistik untuk detailnya!
        </p>
        <button id="summaryButton" class="btn btn-sm btn-light text-primary mt-2 w-100 fw-bold">Tanya Selengkapnya</button>
    </div>

    <!-- Recent Transactions Header -->
    <div class="d-flex justify-content-between align-items-center mb-3 px-2">
        <h6 class="fw-bold m-0">Transaksi Terakhir</h6>
        <button onclick="switchTab('history')" class="btn btn-link btn-sm text-decoration-none p-0">Lihat Semua</button>
    </div>
    
    <!-- Recent List Preview -->
    <!-- Recent List Preview -->
    <ul class="list-unstyled" id="recentNotesList">
        @foreach ($expenses->take(5) as $exp)
            <li class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                <div class="d-flex align-items-center">
                    <div class="me-3 shadow-sm" style="width: 40px; height: 40px; background: {{ $exp->category->color ?? '#eee' }}20; color: {{ $exp->category->color ?? '#333' }}; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <span style="font-size: 1.2rem;">{{ $exp->category->icon ?? '📝' }}</span>
                    </div>
                    <div>
                        <h6 class="m-0 fw-bold text-dark" style="font-size: 0.9rem;">{{ Str::limit($exp->note, 20) }}</h6>
                        <small class="text-muted" style="font-size: 0.75rem;">
                            {{ \Carbon\Carbon::parse($exp->date)->translatedFormat('d M') }} {{ $exp->created_at ? $exp->created_at->format('H:i') : '' }} &bull; {{ $exp->category->name ?? 'Umum' }}
                        </small>
                    </div>
                </div>
                <span class="fw-bold text-danger" style="font-size: 0.9rem;">- Rp {{ number_format($exp->amount, 0, ',', '.') }}</span>
            </li>
        @endforeach
        
        @if(count($expenses) == 0)
             <li class="text-center text-muted py-3 small">Belum ada transaksi bulan ini.</li>
        @endif
    </ul>
</div>

<!-- 2. HISTORY TAB -->
<div id="tab-history" class="tab-content">
    <h5 class="fw-bold mb-3 px-2">Riwayat Transaksi</h5>
    
    <!-- Search Bar -->
    <div class="bg-white p-2 rounded-3 shadow-sm mb-3 d-flex align-items-center border">
        <i class="bi bi-search text-muted ms-2"></i>
        <input type="text" id="searchNotes" class="form-control border-0 shadow-none bg-transparent" placeholder="Cari catatan...">
    </div>

    <!-- Full List -->
    <ul class="list-unstyled" id="notesList">
         @if(count($expenses) == 0)
            <div id="emptyState" class="text-center py-5">
                <i class="bi bi-journal-x fs-1 text-muted opacity-50"></i>
                <p class="text-muted mt-2">Belum ada transaksi bulan ini.</p>
            </div>
         @endif

         @foreach ($expenses as $exp)
            <li class="list-group-item d-flex justify-content-between align-items-start mb-2 cursor-pointer list-item-hover"
                data-id="{{ $exp->id }}"
                data-category-id="{{ $exp->category_id }}"
                data-note="{{ $exp->note }}"
                data-amount="{{ $exp->amount }}"
                data-date="{{ $exp->date }}"
                onclick="if(!event.target.closest('button')) openEditExpense(this)">
                <div class="text-section flex-grow-1">
                    <div class="d-flex align-items-center mb-1">
                        @if($exp->category)
                             <span class="badge bg-light text-dark border me-2 rounded-pill fw-normal">
                                 {{ $exp->category->icon }} {{ $exp->category->name }}
                             </span>
                        @endif
                        <span class="note-date text-muted small" style="font-size:0.75rem;">
                             {{ \Carbon\Carbon::parse($exp->date)->translatedFormat('d M') }} {{ $exp->created_at ? $exp->created_at->format('H:i') : '' }}
                        </span>
                    </div>
                    <span class="note-text fw-semibold text-dark">{{ $exp->note }}</span> 
                </div>
                <div class="text-end ms-2">
                    <span class="fw-bold text-danger d-block mb-1">Rp {{ number_format($exp->amount, 0, ',', '.') }}</span>
                    <div>
                         <!-- <button class="btn btn-sm btn-outline-primary p-0 px-2 me-1 rounded-pill small" style="font-size: 0.7rem;" onclick="openDetailModal({{ $exp->id }}, '{{ addslashes($exp->note) }}')">
                            <i class="bi bi-list-check"></i> Isi Item
                         </button> -->
                         <button class="btn btn-sm btn-link text-muted p-0 edit-btn"><i class="bi bi-pencil-square"></i></button>
                         <button class="btn btn-sm btn-link text-danger p-0 ms-2 delete-btn"><i class="bi bi-trash"></i></button>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
</div>

<!-- 3. STATS TAB -->
<div id="tab-stats" class="tab-content">
    <div class="d-flex justify-content-between align-items-center mb-3 px-2">
        <h5 class="fw-bold mb-0">Statistik</h5>
        <input type="month" id="statsMonthFilter" class="form-control form-control-sm w-auto" value="{{ $selectedMonth ?? date('Y-m') }}">
    </div>
    <div class="card shadow-sm mb-3 border-0">
        <div class="card-body">
            <h6 class="card-title text-center text-muted small mb-3">Pengeluaran per Kategori</h6>
            <div style="height: 250px; position: relative;">
                 <canvas id="rincianKategoriChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Breakdown per Category -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <h6 class="fw-bold px-3 pt-3 mb-2">Rincian Kategori</h6>
            <ul class="list-group list-group-flush" id="categoryList">
                <!-- Javascript will populate this -->
                <li class="list-group-item text-center text-muted py-4 small">
                    Memuat data...
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- 4. PROFILE TAB -->
<div id="tab-profile" class="tab-content">
    <h5 class="fw-bold mb-4 px-2">Profil Saya</h5>
    
    <div class="bg-white p-4 rounded-4 shadow-sm text-center mb-4 border">
        <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=0d6efd&color=fff&size=128" 
             class="rounded-circle mb-3 shadow-sm mx-auto d-block" width="80" height="80">
        <h5 class="fw-bold mb-1">{{ Auth::user()->name }}</h5>
        <p class="text-muted small">{{ Auth::user()->email }}</p>
        
        <div class="row mt-4 text-start">
             <div class="col-6 text-center border-end">
                 <small class="text-muted d-block uppercase" style="font-size:0.7rem; letter-spacing:1px;">PEMASUKAN</small>
                 <span class="fw-bold text-success">{{ number_format($income ?? 0, 0, ',', '.') }}</span>
             </div>
             <div class="col-6 text-center">
                 <small class="text-muted d-block uppercase" style="font-size:0.7rem; letter-spacing:1px;">PENGELUARAN</small>
                 <span class="fw-bold text-danger">{{ number_format($totalRealization ?? 0, 0, ',', '.') }}</span>
             </div>
        </div>
    </div>

    <div class="list-group shadow-sm rounded-4 overflow-hidden border-0">
        <!-- Install PWA Button (Hidden by default, shown via JS) -->
        <button id="installPwaBtn" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3" style="display:none;">
            <div><i class="bi bi-download me-3 text-success"></i> Install Aplikasi</div>
            <i class="bi bi-chevron-right small text-muted"></i>
        </button>

        <button class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3" data-bs-toggle="modal" data-bs-target="#editIncomeModal">
            <div><i class="bi bi-wallet2 me-3 text-primary"></i> Edit Pemasukan Alokasi</div>
            <i class="bi bi-chevron-right small text-muted"></i>
        </button>
        <form action="{{ route('logout') }}" method="POST" class="d-block m-0">
            @csrf
            <button type="submit" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3 text-danger">
                <div><i class="bi bi-box-arrow-right me-3"></i> Keluar</div>
            </button>
        </form>
    </div>
    
    <div class="text-center mt-4 text-muted small opacity-50">
        Versi 1.0 (Qonaah)
    </div>
</div>

<!-- Floating Action Button -->
<div class="fab-container">
    <button class="fab" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
        <i class="bi bi-plus-lg"></i>
    </button>
</div>

<!-- MODALS -->

<!-- Add Expense Modal (Simplified for Mobile) -->
<div class="modal fade" id="addExpenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah Pengeluaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2">
                <form id="noteForm">
                    @csrf
                    <!-- Hidden input to supply month for JS logic -->
                    <input type="hidden" id="noteMonth" value="{{ $selectedMonth }}">
                    
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">NOMINAL (RP)</label>
                        <input type="text" id="amountInput" class="form-control form-control-lg fs-2 fw-bold text-primary border-0 bg-light" placeholder="0" inputmode="numeric">
                        <!-- Note: logic in JS must take this value and put it into hidden 'noteText' structure or separate amount field -->
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">TANGGAL</label>
                        <input type="date" id="dateInput" class="form-control bg-light border-0" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">KATEGORI</label>
                        <select id="noteCategory" class="form-select form-select-lg bg-light border-0" required>
                            <option value="" selected disabled>Pilih Kategori...</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">CATATAN</label>
                        <textarea id="noteText" class="form-control bg-light border-0" rows="2" placeholder="Beli Nasi Goreng..." required></textarea>
                    </div>

                    <!-- Shopping List Section -->
                    <div class="mb-3 border rounded-3 p-2 bg-aliceblue">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small fw-bold text-primary"><i class="bi bi-cart-plus me-1"></i> Input Rincian Belanja</span>
                        </div>
                        
                        <div id="shoppingListSection">
                            <ul class="list-group list-group-flush mb-2" id="newExpenseDetailsList">
                                <!-- Dynamic Rows Here -->
                            </ul>
                            <button type="button" class="btn btn-sm btn-outline-primary w-100 border-dashed" id="addDetailRowBtn">
                                <i class="bi bi-plus-circle"></i> Tambah Item
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-lg rounded-3 fw-bold mt-2">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Edit Expense Modal -->
<div class="modal fade" id="editExpenseModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Edit Catatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-0">
                <form id="editExpenseForm">
                    @csrf
                    <input type="hidden" id="editExpenseId">
                    <div class="mb-3">
                        <label class="form-label small text-muted">Nominal (Rp)</label>
                        <input type="number" id="editAmountInput" class="form-control" placeholder="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted">Tanggal</label>
                        <input type="date" id="editDateInput" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted">Catatan</label>
                        <input type="text" id="editNoteText" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted">Kategori</label>
                        <select id="editNoteCategory" class="form-select">
                            <option value="">-- Pilih --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Shopping List Section (Edit) -->
                    <div class="mb-3 border rounded-3 p-2 bg-aliceblue">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small fw-bold text-primary"><i class="bi bi-cart-check me-1"></i> Edit Rincian Belanja</span>
                        </div>
                        
                        <div id="shoppingListSectionEdit">
                            <ul class="list-group list-group-flush mb-2" id="editExpenseDetailsList">
                                <!-- Dynamic Rows Here -->
                            </ul>
                            <button type="button" class="btn btn-sm btn-outline-primary w-100 border-dashed" id="addDetailRowBtnEdit">
                                <i class="bi bi-plus-circle"></i> Tambah Item
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-3">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Income Modal (Initial Setup) -->
@if(!isset($income) || $income == 0)
<div class="modal fade show" id="incomeModal" style="display:block; background:rgba(0,0,0,0.8); z-index: 9999;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 p-3 border-0">
            <h4 class="mb-3 fw-bold text-center">Mulai Budgeting! 💰</h4>
            <p class="text-center text-muted small mb-4">Masukkan pemasukan bulan ini untuk mulai mencatat.</p>
            <form action="{{ route('income.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold small">BULAN</label>
                    <input type="month" id="monthIncomeInput" class="form-control bg-light border-0" name="monthIncome" value="{{ now()->format('Y-m') }}" required>
                </div>
                <div class="mb-3">
                     <label class="form-label fw-bold small">TOTAL PEMASUKAN</label>
                    <input type="number" name="income" class="form-control form-control-lg fw-bold text-success bg-light border-0" placeholder="Rp 0" required>
                </div>
                <button class="btn btn-primary w-100 rounded-3 py-2 fw-bold">Mulai!</button>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Edit Income Modal -->
<div class="modal fade" id="editIncomeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Kelola Pemasukan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- 1. Main Allocated Income -->
                <div class="card bg-light border-0 mb-3 rounded-3">
                    <div class="card-body p-3">
                        <h6 class="fw-bold small text-muted mb-2">PEMASUKAN UTAMA (GAJI UTAMA)</h6>
                        <form action="{{ route('income.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="monthIncome" value="{{ $selectedMonth }}">
                            
                            <div class="input-group">
                                <span class="input-group-text border-0 bg-white">Rp</span>
                                <input type="number" name="income" class="form-control fw-bold border-0" value="{{ $mainIncome ?? 0 }}" placeholder="0">
                                <button class="btn btn-primary" type="submit"><i class="bi bi-check-lg"></i></button>
                            </div>
                            <small class="text-muted" style="font-size: 0.75rem;">Ubah nolimain gaji/jatah bulanan di sini.</small>
                        </form>
                    </div>
                </div>

                <hr class="my-3 opacity-25">

                <!-- 2. Additional Income -->
                <h6 class="fw-bold small text-muted mb-3 d-flex justify-content-between align-items-center">
                    PEMASUKAN TAMBAHAN
                    <button class="btn btn-sm btn-outline-primary py-0" type="button" onclick="toggleAddIncomeForm()" id="btnToggleAddIncome">
                        <i class="bi bi-plus-lg" id="iconToggleAddIncome"></i> <span id="textToggleAddIncome">Tambah</span>
                    </button>
                </h6>

                <!-- Add Form (Custom Toggle) -->
                <div class="mb-3" id="addIncomeForm" style="display: none;">
                    <div class="card border border-primary border-opacity-25 shadow-sm rounded-3">
                        <div class="card-body p-3 bg-aliceblue">
                            <form action="{{ route('income.transaction.store') }}" method="POST">
                                @csrf
                                <div class="mb-2">
                                    <input type="text" name="title" class="form-control form-control-sm" placeholder="Nama Sumber (mis: Proyek A)" required>
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-7">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-white">Rp</span>
                                            <input type="number" name="amount" class="form-control" placeholder="Jumlah" required>
                                        </div>
                                    </div>
                                    <div class="col-5">
                                        <input type="date" name="date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold">Simpan Tambahan</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- List -->
                <ul class="list-group list-group-flush border rounded-3 overflow-hidden">
                    @forelse ($additionalIncomes as $addIncome)
                        <li class="list-group-item d-flex justify-content-between align-items-center p-2">
                            <div>
                                <div class="fw-semibold text-dark small">{{ $addIncome->title }}</div>
                                <div class="text-muted" style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($addIncome->date)->translatedFormat('d M Y') }}</div>
                            </div>
                            <div class="text-end">
                                <span class="d-block fw-bold text-success small">+ {{ number_format($addIncome->amount, 0, ',', '.') }}</span>
                                <form action="{{ route('income.transaction.destroy', $addIncome->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pemasukan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-link text-danger p-0 small text-decoration-none" style="font-size: 0.7rem;">Hapus</button>
                                </form>
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted small py-3">Belum ada pemasukan tambahan.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- AI Chat Modal (Bottom Sheet style) -->
<div class="modal fade" id="chatModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-fullscreen-md-down">
        <div class="modal-content rounded-top-4 border-0">
            <div class="modal-header border-0 bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-robot me-2"></i> Asisten Keuangan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body bg-light" id="chatContent">
                 <div class="text-center text-muted my-5">
                     <i class="bi bi-chat-dots fs-1 mb-2 d-block"></i>
                     <small>Tanyakan apa saja tentang keuanganmu!</small>
                 </div>
            </div>
            <div class="modal-footer p-2 bg-white">
                <div class="input-group">
                    <input type="text" class="form-control border-0 bg-light" placeholder="Tulis pesan..." id="aiChatInput">
                    <button class="btn btn-primary rounded-end" id="aiChatSend"><i class="bi bi-send-fill"></i></button>
                </div>

            </div>
        </div>
    </div>
</div>





<!-- Trakteer dynamic island -->
<div id="trakteerContainer" style="position: fixed; bottom: 130px; left: 16px; z-index: 1040;">
    <button onclick="document.getElementById('trakteerContainer').style.display='none'" 
            class="btn btn-sm btn-light text-danger shadow-sm rounded-circle p-0 d-flex align-items-center justify-content-center"
            style="position: absolute; top: -8px; right: -8px; width: 24px; height: 24px; z-index: 1045; border: 1px solid #eee;">
        <i class="bi bi-x mb-0" style="font-size: 16px;"></i>
    </button>
    <script type='text/javascript' src='https://edge-cdn.trakteer.id/js/trbtn-overlay.min.js?v=14-05-2025'></script>
    <script type='text/javascript' class='troverlay'>
        (function() {
            var trbtnId = trbtnOverlay.init('Dukung Saya di Trakteer','#be1e2d','https://trakteer.id/luqni/tip/embed/modal','https://cdn.trakteer.id/images/mix/coffee.png','40','inline');
            trbtnOverlay.draw(trbtnId);
        })();
    </script>
</div>
    </div>
</div>


<!-- Bottom Navigation -->
<nav class="bottom-nav">
    <button class="nav-item active" onclick="switchTab('home')">
        <i class="bi bi-house-door-fill"></i>
        <span>Home</span>
    </button>
    <button class="nav-item" onclick="switchTab('history')">
        <i class="bi bi-receipt"></i>
        <span>Transaksi</span>
    </button>
    <div style="width: 50px;"></div> <!-- Spacer for FAB -->
    <button class="nav-item" onclick="switchTab('stats')">
        <i class="bi bi-graph-up"></i>
        <span>Statistik</span>
    </button>
    <button class="nav-item" onclick="switchTab('profile')">
        <i class="bi bi-person-fill"></i>
        <span>Profil</span>
    </button>
</nav>

<!-- Hidden Holders for JS Logic compatibility with old script -->
<div class="d-none">
    <span id="totalExpenseCardHidden">{{ number_format($totalRealization ?? 0, 0, ',', '.') }}</span>
</div>

@include('partials.chart-script')
@include('partials.expense-detail-script')

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex flex-column">
                    <small class="text-muted uppercase small" style="letter-spacing: 1px;">RINCIAN</small>
                    <h5 class="modal-title fw-bold" id="detailModalTitle">Belanja Bulanan</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="currentExpenseId">
                
                <!-- Add New Item Form -->


                <!-- List Items -->
                <h6 class="fw-bold mb-3 small text-muted">DAFTAR BELANJA</h6>
                <ul class="list-group list-group-flush" id="detailItemsList">
                    <!-- JS Populated -->
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    // Simple Tab Switcher
    function switchTab(tabId) {
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        // Deactivate nav items
        document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
        
        // Show selected
        document.getElementById('tab-' + tabId).classList.add('active');
        
        // Activate nav item
        const navItems = document.querySelectorAll('.nav-item');
        if(tabId === 'home') navItems[0].classList.add('active');
        if(tabId === 'history') navItems[1].classList.add('active');
        if(tabId === 'stats') navItems[2].classList.add('active');
        if(tabId === 'profile') navItems[3].classList.add('active');

        // Special case for Stats to re-render chart if needed
        // Special case for Stats to re-render chart if needed
        if(tabId === 'stats') {
            setTimeout(() => {
                if(window.expenseChart) {
                    window.expenseChart.resize();
                    window.expenseChart.update();
                }
                if(window.rincianKategoriChart) {
                    window.rincianKategoriChart.resize();
                    window.rincianKategoriChart.update();
                }
                if(window.categoryChart) {
                    window.categoryChart.resize();
                    window.categoryChart.update();
                }
            }, 100);
        }
        
        // Save state
        localStorage.setItem('activeTab', tabId);
    }
    
    // Restore Tab
    document.addEventListener("DOMContentLoaded", function () {
        const lastTab = localStorage.getItem('activeTab') || 'home';
        switchTab(lastTab);
        
        // Handle PWA Install Button Visibility
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            const installBtn = document.getElementById('installPwaBtn');
            if(installBtn) {
                installBtn.style.display = 'flex';
                installBtn.addEventListener('click', () => {
                    deferredPrompt.prompt();
                    deferredPrompt.userChoice.then((choiceResult) => {
                        if (choiceResult.outcome === 'accepted') {
                            console.log('User accepted the install prompt');
                        }
                        deferredPrompt = null;
                        installBtn.style.display = 'none';
                    });
                });
            }
        });
        
        // Copy "Recent" to "Home" tab on load
        const mainList = document.getElementById('notesList');
        const recentList = document.getElementById('recentNotesList');
        if(mainList && recentList) {
            recentList.innerHTML = '';
            const items = mainList.querySelectorAll('li');
            if(items.length === 0) {
                 recentList.innerHTML = '<li class="text-center text-muted py-3 small">Belum ada transaksi.</li>';
            } else {
                for(let i=0; i<Math.min(items.length, 3); i++) {
                    const clone = items[i].cloneNode(true);
                    // Remove buttons for the preview if needed
                    const buttons = clone.querySelector('.text-end div');
                    if(buttons) buttons.style.display = 'none';
                    recentList.appendChild(clone);
                }
            }
        }
        
        // Calculate and Show "Sisa Saldo" logic
        // We use safe parsing
        const incomeEl = document.getElementById('totalPemasukanCard');
        const expenseEl = document.getElementById('totalRealizationCard');
        if(incomeEl && expenseEl) {
             const income = parseInt(incomeEl.innerText.replace(/\D/g, '')) || 0;
             const expense = parseInt(expenseEl.innerText.replace(/\D/g, '')) || 0;
             const saldo = income - expense;
             const saldoEl = document.getElementById('saldoAmount');
             if(saldoEl) {
                 saldoEl.innerText = 'Rp ' + saldo.toLocaleString('id-ID');
             }
        }
        
        // Enhanced Visibility Toggle with icon and text update
        document.querySelectorAll('.toggle-visibility').forEach(toggleBtn => {
            toggleBtn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const target = document.querySelector(targetId);
                
                if (target) {
                    // Toggle class 'hidden' (yang sekarang fungsinya membuat efek blur)
                    target.classList.toggle('hidden');
                    
                    // Cek kondisi SETELAH toggle:
                    // Jika punya class 'hidden', berarti sedang Blur.
                    const isNowBlurred = target.classList.contains('hidden');
                    
                    // Update icon and text based on target
                    let icon, text;
                    if (targetId === '#saldoAmount') {
                        icon = document.getElementById('iconSaldo');
                        text = document.getElementById('textSaldo');
                    } else if (targetId === '#totalPemasukanCard') {
                        icon = document.getElementById('iconPemasukan');
                        text = document.getElementById('textPemasukan');
                    }
                    
                    if (icon && text) {
                        if (isNowBlurred) {
                            // KONDISI: Sedang Blur (Hidden) -> Tombol berubah jadi "Tampilkan"
                            // Reset class icon agar bersih, lalu tambah icon mata (Show)
                            icon.classList.remove('bi-eye-slash');
                            icon.classList.add('bi-eye');
                            text.textContent = 'Tampilkan';
                        } else {
                            // KONDISI: Sedang Jelas (Visible) -> Tombol berubah jadi "Sembunyikan"
                            // Tambah icon mata dicoret (Hide)
                            icon.classList.remove('bi-eye');
                            icon.classList.add('bi-eye-slash');
                            text.textContent = 'Sembunyikan';
                        }
                    }
                }
            });
        });
    });
</script>

<script>
    // Custom toggle function to avoid Bootstrap collapse conflicts with modal-dialog-scrollable
    function toggleAddIncomeForm() {
        const form = document.getElementById('addIncomeForm');
        const icon = document.getElementById('iconToggleAddIncome');
        const text = document.getElementById('textToggleAddIncome');
        
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block';
            icon.classList.remove('bi-plus-lg');
            icon.classList.add('bi-dash-lg');
            text.textContent = 'Tutup';
        } else {
            form.style.display = 'none';
            icon.classList.remove('bi-dash-lg');
            icon.classList.add('bi-plus-lg');
            text.textContent = 'Tambah';
        }
    }
</script>
@endsection

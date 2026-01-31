@extends('layouts.app')

@section('content')
<style>
    .category-card {
        transition: transform 0.2s;
    }
    .category-card:hover {
        transform: translateY(-2px);
    }
    .emoji-picker {
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0.5rem;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        display: inline-block;
    }
</style>

<div class="container py-4" style="padding-bottom: 100px;">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">🏷️ Kelola Kategori</h4>
            <p class="text-muted small mb-0">Atur kategori dan budget limit</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-primary btn-sm rounded-pill">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Add Category Button -->
    <div class="mb-4">
        <button class="btn btn-primary w-100 rounded-3" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="bi bi-plus-lg"></i> Tambah Kategori Baru
        </button>
    </div>

    <!-- Categories List -->
    <div class="row g-3">
        @forelse($categories as $category)
            <div class="col-12">
                <div class="card category-card border-0 shadow-sm" style="border-left: 4px solid {{ $category->color }} !important;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <span style="font-size: 1.5rem;" class="me-2">{{ $category->icon }}</span>
                                    <h6 class="mb-0 fw-bold">{{ $category->name }}</h6>
                                </div>
                                
                                @if($category->budget_limit)
                                    <div class="mt-2">
                                        <small class="text-muted d-block mb-1">Budget Limit</small>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-light text-dark border">
                                                Rp {{ number_format($category->budget_limit, 0, ',', '.') }}
                                            </span>
                                            @php
                                                $used = $category->expenses()
                                                    ->where('user_id', Auth::id())
                                                    ->where('month', request()->get('month', now()->format('Y-m')))
                                                    ->sum('amount');
                                                $percentage = $category->budget_limit > 0 ? ($used / $category->budget_limit) * 100 : 0;
                                            @endphp
                                            <small class="text-muted">
                                                Terpakai: Rp {{ number_format($used, 0, ',', '.') }} ({{ number_format($percentage, 1) }}%)
                                            </small>
                                        </div>
                                    </div>
                                @else
                                    <small class="text-muted">Tidak ada budget limit</small>
                                @endif
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-primary" onclick="editCategory({{ $category->id }}, '{{ $category->name }}', '{{ $category->icon }}', '{{ $category->color }}', {{ $category->budget_limit ?? 'null' }})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted opacity-50"></i>
                    <p class="text-muted mt-2">Belum ada kategori.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Tambah Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Kategori</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Makanan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Icon (Emoji)</label>
                        <input type="text" name="icon" id="iconInput" class="form-control" placeholder="🍔" maxlength="10" required>
                        <small class="text-muted">Pilih emoji dari keyboard atau copy-paste</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Warna</label>
                        <input type="color" name="color" class="form-control form-control-color w-100" value="#0d6efd" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Budget Limit (Opsional)</label>
                        <input type="number" name="budget_limit" class="form-control" placeholder="0" min="0" step="1000">
                        <small class="text-muted">Kosongkan jika tidak ada limit</small>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-3">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Edit Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editCategoryForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Kategori</label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Icon (Emoji)</label>
                        <input type="text" name="icon" id="editIcon" class="form-control" maxlength="10" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Warna</label>
                        <input type="color" name="color" id="editColor" class="form-control form-control-color w-100" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Budget Limit (Opsional)</label>
                        <input type="number" name="budget_limit" id="editBudgetLimit" class="form-control" min="0" step="1000">
                        <small class="text-muted">Kosongkan jika tidak ada limit</small>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-3">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function editCategory(id, name, icon, color, budgetLimit) {
    document.getElementById('editName').value = name;
    document.getElementById('editIcon').value = icon;
    document.getElementById('editColor').value = color;
    document.getElementById('editBudgetLimit').value = budgetLimit || '';
    document.getElementById('editCategoryForm').action = `/categories/${id}`;
    
    const modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
    modal.show();
}
</script>
@endsection

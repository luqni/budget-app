<div class="card border-0 shadow-sm p-3">
    <div class="d-flex justify-content-between align-items-start">
        <div class="d-flex gap-3 align-items-center">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" 
                style="width: 45px; height: 45px; background-color: {{ $debt->type == 'payable' ? '#fee2e2' : '#d1fae5' }}">
                <i class="bi {{ $debt->type == 'payable' ? 'bi-arrow-down-left text-danger' : 'bi-arrow-up-right text-success' }} fs-5"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-0 {{ $debt->status == 'paid' ? 'text-decoration-line-through text-muted' : '' }}">
                    {{ $debt->name }}
                </h6>
                <small class="text-muted">
                    {{ $debt->due_date ? 'Jatuh Tempo: ' . $debt->due_date->format('d M Y') : 'Tidak ada tenggat' }}
                </small>
            </div>
        </div>
        <div class="text-end">
            <div class="fw-bold {{ $debt->status == 'paid' ? 'text-muted' : ($debt->type == 'payable' ? 'text-danger' : 'text-success') }}">
                Rp {{ number_format($debt->amount, 0, ',', '.') }}
            </div>
            @if($debt->status == 'paid')
                <span class="badge bg-success-subtle text-success rounded-pill" style="font-size: 0.7rem;">Lunas</span>
            @else
                <span class="badge bg-warning-subtle text-warning rounded-pill" style="font-size: 0.7rem;">Belum Lunas</span>
            @endif
        </div>
    </div>
    
    <!-- Action Footer -->
    <div class="d-flex justify-content-end gap-2 mt-3 pt-3 border-top">
        <form action="{{ route('debts.destroy', $debt->id) }}" method="POST" onsubmit="return confirm('Yakin hapus data ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-light text-danger rounded-pill px-3">
                <i class="bi bi-trash"></i>
            </button>
        </form>

        <form action="{{ route('debts.toggle-paid', $debt->id) }}" method="POST">
            @csrf
            @if($debt->status == 'unpaid')
                <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3">
                    <i class="bi bi-check-lg me-1"></i> Tandai Lunas
                </button>
            @else
                <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="bi bi-x-lg me-1"></i> Batal Lunas
                </button>
            @endif
        </form>
    </div>
</div>

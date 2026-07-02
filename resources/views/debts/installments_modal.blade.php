<!-- Installment Modal -->
<div class="modal fade" id="installmentModal{{ $debt->id }}" tabindex="-1" aria-labelledby="installmentModalLabel{{ $debt->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow" style="border-radius: 1.5rem;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="installmentModalLabel{{ $debt->id }}">
                    Riwayat Cicilan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <div class="mb-3 px-3 py-2 bg-light rounded-3 text-center">
                    <small class="text-muted d-block">Total Hutang</small>
                    <div class="fw-bold fs-5">Rp {{ number_format($debt->amount, 0, ',', '.') }}</div>
                    
                    <div class="d-flex justify-content-between mt-2 pt-2 border-top">
                        <div>
                            <small class="text-muted d-block" style="font-size: 0.7rem;">Telah Dibayar</small>
                            <span class="text-success fw-bold">Rp {{ number_format($debt->paid_amount, 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <small class="text-muted d-block" style="font-size: 0.7rem;">Sisa Hutang</small>
                            <span class="text-danger fw-bold">Rp {{ number_format($debt->remaining_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold mb-3 fs-6">Riwayat Pembayaran</h6>
                
                @if($debt->installments->isEmpty())
                    <div class="text-center py-3">
                        <small class="text-muted">Belum ada riwayat cicilan.</small>
                    </div>
                @else
                    <div class="list-group list-group-flush mb-4">
                        @foreach($debt->installments as $installment)
                            <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold">Rp {{ number_format($installment->amount, 0, ',', '.') }}</div>
                                    <small class="text-muted">{{ $installment->payment_date->format('d M Y') }}</small>
                                </div>
                                <form action="{{ route('debts.installments.destroy', $installment->id) }}" method="POST" onsubmit="return confirm('Hapus riwayat cicilan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($debt->remaining_amount > 0 && $debt->status == 'unpaid')
                    <hr>
                    <h6 class="fw-bold mb-3 fs-6">Tambah Cicilan Baru</h6>
                    <form action="{{ route('debts.installments.store', $debt->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Jumlah Pembayaran</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">Rp</span>
                                <input type="text" name="amount" class="form-control border-start-0 ps-0 currency-input" required placeholder="0"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Tanggal Pembayaran</label>
                            <input type="date" name="payment_date" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2">
                            Simpan Cicilan
                        </button>
                    </form>
                @elseif($debt->status == 'paid')
                    <div class="alert alert-success rounded-3 mt-3 py-2 px-3 text-center">
                        <i class="bi bi-check-circle-fill me-1"></i> Hutang ini telah lunas.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

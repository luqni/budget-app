<div class="modal fade" id="createDebtModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah Catatan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('debts.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Type Selection -->
                    <div class="btn-group w-100 mb-3" role="group">
                        <input type="radio" class="btn-check" name="type" id="type_payable" value="payable" checked>
                        <label class="btn btn-outline-danger" for="type_payable">Hutang (Saya)</label>

                        <input type="radio" class="btn-check" name="type" id="type_receivable" value="receivable">
                        <label class="btn btn-outline-success" for="type_receivable">Piutang (Orang)</label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small">Nama</label>
                        <input type="text" name="name" class="form-control form-control-lg bg-light border-0" placeholder="Contoh: Budi, Warung Sebelah" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small">Jumlah (Rp)</label>
                        <input type="number" name="amount" class="form-control form-control-lg bg-light border-0" placeholder="0" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small">Jatuh Tempo (Opsional)</label>
                        <input type="date" name="due_date" class="form-control bg-light border-0">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small">Catatan (Opsional)</label>
                        <textarea name="description" class="form-control bg-light border-0" rows="2" placeholder="Keterangan tambahan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

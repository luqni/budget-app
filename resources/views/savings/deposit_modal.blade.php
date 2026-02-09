<div class="modal fade" id="depositModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="depositTitle">Nabung</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST" id="depositForm">
                @csrf
                <input type="hidden" id="depositSavingId" name="saving_id">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-center w-100 text-muted small">Mau nabung berapa?</label>
                        <input type="text" id="deposit_amount_display" class="form-control form-control-lg fs-1 fw-bold text-center text-success border-0 bg-light" placeholder="Rp 0" required autofocus>
                        <input type="hidden" name="amount" id="deposit_amount_raw">
                    </div>
                    <p class="text-center text-muted small">Semangat! Sedikit demi sedikit lama-lama menjadi bukit.</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-success w-100 rounded-3 py-2 fw-bold">Masuk Celengan 📥</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('depositModal').addEventListener('show.bs.modal', function (event) {
        const id = document.getElementById('depositSavingId').value;
        const form = document.getElementById('depositForm');
        form.action = '/savings/' + id + '/deposit';
        
        // Reset inputs on modal show
        document.getElementById('deposit_amount_display').value = '';
        document.getElementById('deposit_amount_raw').value = '';
    });

    const depositDisplay = document.getElementById('deposit_amount_display');
    const depositRaw = document.getElementById('deposit_amount_raw');

    depositDisplay.addEventListener('input', function(e) {
        let value = this.value.replace(/\D/g, '');
        if (value === '') {
            depositRaw.value = '';
            this.value = '';
            return;
        }
        
        depositRaw.value = value;
        this.value = new Intl.NumberFormat('id-ID').format(value);
    });
</script>


<!-- Copy Previous Month Modal -->
<div class="modal fade" id="copyPreviousMonthModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">🔄 Copy Data Bulan Lalu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">
                    <i class="bi bi-info-circle"></i> Hemat waktu dengan copy data dari bulan sebelumnya
                </p>
                
                <div class="alert alert-light border-0 bg-light mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small text-muted">Dari:</span>
                        <strong id="copyFromMonth"></strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <span class="small text-muted">Ke:</span>
                        <strong id="copyToMonth"></strong>
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="fw-semibold mb-2">Pilih data yang mau dicopy:</h6>
                    
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="copyIncome" checked>
                        <label class="form-check-label" for="copyIncome">
                            <i class="bi bi-wallet2 text-success"></i> <strong>Gaji Bulanan</strong>
                            <br><small class="text-muted">Copy pemasukan utama bulan lalu</small>
                        </label>
                    </div>
                    
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="copyRecurringExpenses" checked>
                        <label class="form-check-label" for="copyRecurringExpenses">
                            <i class="bi bi-arrow-repeat text-primary"></i> <strong>Pengeluaran Rutin</strong>
                            <br><small class="text-muted">Hanya pengeluaran yang ditandai sebagai rutin</small>
                        </label>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="copyAllExpenses">
                        <label class="form-check-label" for="copyAllExpenses">
                            <i class="bi bi-list-ul text-warning"></i> <strong>Semua Pengeluaran</strong>
                            <br><small class="text-muted">Copy semua pengeluaran (termasuk yang tidak rutin)</small>
                        </label>
                    </div>
                </div>

                <div class="alert alert-warning border-0 small">
                    <i class="bi bi-exclamation-triangle"></i> Data yang sudah ada tidak akan ditimpa
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="executeCopyBtn">
                    <i class="bi bi-copy"></i> Copy Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Copy Previous Month Logic
document.addEventListener('DOMContentLoaded', function() {
    const copyModal = document.getElementById('copyPreviousMonthModal');
    const executeCopyBtn = document.getElementById('executeCopyBtn');
    const currentMonth = '{{ $selectedMonth }}';
    
    if (copyModal) {
        copyModal.addEventListener('show.bs.modal', function() {
            // Calculate previous month
            const current = new Date(currentMonth + '-01');
            const previous = new Date(current);
            previous.setMonth(previous.getMonth() - 1);
            
            const previousMonthStr = previous.toLocaleDateString('id-ID', { year: 'numeric', month: 'long' });
            const currentMonthStr = current.toLocaleDateString('id-ID', { year: 'numeric', month: 'long' });
            
            document.getElementById('copyFromMonth').textContent = previousMonthStr;
            document.getElementById('copyToMonth').textContent = currentMonthStr;
        });
    }
    
    if (executeCopyBtn) {
        executeCopyBtn.addEventListener('click', async function() {
            const copyIncome = document.getElementById('copyIncome').checked;
            const copyRecurringExpenses = document.getElementById('copyRecurringExpenses').checked;
            const copyAllExpenses = document.getElementById('copyAllExpenses').checked;
            
            if (!copyIncome && !copyRecurringExpenses && !copyAllExpenses) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih minimal satu',
                    text: 'Pilih minimal satu data yang mau dicopy',
                    confirmButtonColor: '#0d6efd'
                });
                return;
            }
            
            // Show loading
            executeCopyBtn.disabled = true;
            executeCopyBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Copying...';
            
            try {
                const response = await fetch('{{ route("copy.previous.month") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        target_month: currentMonth,
                        copy_income: copyIncome,
                        copy_recurring_expenses: copyRecurringExpenses,
                        copy_all_expenses: copyAllExpenses
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Close modal
                    bootstrap.Modal.getInstance(copyModal).hide();
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        html: `
                            <p>${data.message}</p>
                            <div class="text-start mt-3">
                                <small class="text-muted">Data yang dicopy:</small>
                                <ul class="small">
                                    ${data.copied.income > 0 ? '<li>✅ Gaji Bulanan</li>' : ''}
                                    ${data.copied.expenses > 0 ? `<li>✅ ${data.copied.expenses} Pengeluaran</li>` : ''}
                                </ul>
                            </div>
                        `,
                        confirmButtonColor: '#0d6efd'
                    }).then(() => {
                        // Reload page
                        window.location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Gagal copy data');
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: error.message,
                    confirmButtonColor: '#dc3545'
                });
            } finally {
                executeCopyBtn.disabled = false;
                executeCopyBtn.innerHTML = '<i class="bi bi-copy"></i> Copy Sekarang';
            }
        });
    }
});
</script>

<script>
    // --- EXPENSE DETAIL LOGIC ---

    // Open Modal & Fetch Data
    function openDetailModal(expenseId, noteText) {
        document.getElementById('detailModalTitle').innerText = noteText || 'Rincian Belanja';
        document.getElementById('currentExpenseId').value = expenseId;
        
        // Show Modal
        const modal = new bootstrap.Modal(document.getElementById('detailModal'));
        modal.show();

        loadDetails(expenseId);
    }

    function loadDetails(expenseId) {
        const list = document.getElementById('detailItemsList');
        list.innerHTML = '<li class="text-center text-muted py-3 small">Memuat...</li>';

        fetch(`/expense/${expenseId}/details`)
            .then(res => res.json())
            .then(data => {
                list.innerHTML = '';
                if(data.length === 0) {
                    list.innerHTML = '<li class="text-center text-muted py-3 small">Belum ada rincian item. Tambahkan sekarang!</li>';
                    return;
                }

                data.forEach(item => {
                    const li = document.createElement('li');
                    li.className = 'list-group-item d-flex justify-content-between align-items-center px-0 border-0 border-bottom';
                    
                    const isChecked = item.is_checked ? 'checked' : '';
                    const textDecoration = item.is_checked ? 'text-decoration-line-through text-muted' : '';
                    const totalItem = item.qty * item.price;

                    li.innerHTML = `
                        <div class="d-flex align-items-center" style="gap: 10px;">
                            <input class="form-check-input md-checkbox" type="checkbox" ${isChecked} 
                                onchange="toggleCheckDetail(${item.id}, this.checked)">
                            
                            <div class="${textDecoration} detail-text-${item.id}">
                                <div class="fw-semibold small">${item.name}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">
                                    ${item.qty} x ${new Intl.NumberFormat('id-ID').format(item.price)}
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="fw-bold me-3 small">Rp ${new Intl.NumberFormat('id-ID').format(totalItem)}</span>
                            <button onclick="deleteDetail(${item.id})" class="btn btn-sm text-danger p-0"><i class="bi bi-x-circle"></i></button>
                        </div>
                    `;
                    list.appendChild(li);
                });
            });
    }

    // Add New Detail
    document.addEventListener("DOMContentLoaded", function () {
        const addDetailForm = document.getElementById('addDetailForm');
        if(addDetailForm) {
            addDetailForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const expenseId = document.getElementById('currentExpenseId').value;
                const name = document.getElementById('detailName').value;
                const qty = document.getElementById('detailQty').value;
                const price = document.getElementById('detailPrice').value;

                fetch('/expense/detail', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        note_id: expenseId,
                        name: name,
                        qty: qty,
                        price: price
                    })
                })
                .then(res => res.json())
                .then(res => {
                    // Reset input
                    document.getElementById('detailName').value = '';
                    document.getElementById('detailQty').value = '1';
                    document.getElementById('detailPrice').value = '';
                    document.getElementById('detailName').focus();

                    // Reload list
                    loadDetails(expenseId);
                });
            });
        }
    });

    // Delete Detail
    function deleteDetail(id) {
        if(!confirm('Hapus item ini?')) return;

        fetch(`/expense/detail/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(res => res.json())
        .then(res => {
            loadDetails(document.getElementById('currentExpenseId').value);
        });
    }

    // Toggle Check
    function toggleCheckDetail(id, isChecked) {
        // Optimistic UI Update
        const textDiv = document.querySelector(`.detail-text-${id}`);
        if(isChecked) {
            textDiv.classList.add('text-decoration-line-through', 'text-muted');
        } else {
            textDiv.classList.remove('text-decoration-line-through', 'text-muted');
        }

        fetch(`/expense/detail/${id}/check`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ is_checked: isChecked ? 1 : 0 })
        })
        .then(res => res.json())
        .then(data => {
            console.log('Total Checked Updated:', data.total);
            // Optionally update some total UI here
        });
    }
</script>

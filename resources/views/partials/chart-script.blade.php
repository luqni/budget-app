<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let ctx = document.getElementById('expenseChart').getContext('2d');
    let expenseChart;

    function loadChartData() {
        fetch('{{ route('chart.data') }}')
            .then(res => res.json())
            .then(data => {
                const labels = data.map(d => d.month);
                const values = data.map(d => d.total);

                if (expenseChart) expenseChart.destroy();

                expenseChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total Pengeluaran per Bulan (Rp)',
                            data: values,
                            borderWidth: 2,
                            borderColor: '#f87171',
                            backgroundColor: 'rgba(248,113,113,0.3)',
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: true },
                            title: {
                                display: true,
                                text: 'Pengeluaran Bulanan (12 Bulan Terakhir)',
                                font: { size: 16 }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: value => 'Rp ' + value.toLocaleString()
                                }
                            }
                        }
                    }
                });
            });
    }

    loadChartData();

    const noteForm = document.getElementById('noteForm');
    const notesList = document.getElementById('notesList');

    // Tambah data baru
    noteForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const noteText = document.getElementById('noteText').value;
        const amount = noteText.match(/\d+/)?.[0] ?? 0;

        fetch('{{ route('expenses.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ note: noteText, amount: amount })
        })
        .then(res => res.json())
        .then(data => {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            li.dataset.id = data.id;
            li.innerHTML = `
                <div class="text-section">
                    <span class="note-text">${data.note}</span>
                    <span class="fw-bold text-danger ms-2">Rp ${data.amount.toLocaleString()}</span>
                </div>
                <div>
                    <button class="btn btn-sm btn-outline-secondary edit-btn me-2">Edit</button>
                    <button class="btn btn-sm btn-outline-danger delete-btn">Hapus</button>
                </div>`;
            notesList.prepend(li);

            updateTotal(data.amount);
            document.getElementById('noteText').value = '';
            loadChartData();
        });
    });

    // Edit dan Hapus
    notesList.addEventListener('click', function(e) {
        const li = e.target.closest('li');
        const id = li?.dataset.id;

        if (e.target.classList.contains('delete-btn')) {
            if (confirm('Yakin ingin menghapus catatan ini?')) {
                fetch(`/expenses/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                }).then(() => {
                    const amount = parseInt(li.querySelector('.text-section span.fw-bold')
                        .innerText.replace(/\D/g, ''));
                    li.remove();
                    updateTotal(-amount);
                    loadChartData();
                });
            }
        }

        if (e.target.classList.contains('edit-btn')) {
            const noteTextEl = li.querySelector('.note-text');
            const oldNote = noteTextEl.innerText;
            const oldAmount = li.querySelector('.fw-bold').innerText.replace(/\D/g, '');

            const newNote = prompt('Ubah catatan:', oldNote);
            if (!newNote) return;
            const newAmount = newNote.match(/\d+/)?.[0] ?? oldAmount;

            fetch(`/expenses/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ note: newNote, amount: newAmount })
            })
            .then(res => res.json())
            .then(data => {
                noteTextEl.innerText = data.note;
                li.querySelector('.fw-bold').innerText = `Rp ${data.amount.toLocaleString()}`;
                refreshTotal();
                loadChartData();
            });
        }
    });

    // Update total otomatis
    function updateTotal(change) {
        const totalEl = document.getElementById('totalExpense');
        let total = parseInt(totalEl.innerText.replace(/\D/g, ''));
        total += change;
        totalEl.innerText = total.toLocaleString();

        const totalElCard = document.getElementById('totalExpenseCard');
        totalElCard.innerText = total.toLocaleString();

        // Ambil nilai total pemasukan (dari card)
        const totalPemasukan = document.getElementById('totalPemasukanCard');
        let totalPem = parseInt(totalPemasukan.innerText.replace(/\D/g, ''));

        // Hitung saldo = pemasukan - pengeluaran
        const saldo = totalPem - total;

        // Update kartu saldo
        const totalSaldo = document.getElementById('totalSaldoCard');
        totalSaldo.innerText = saldo.toLocaleString();

    }

    function refreshTotal() {
        let sum = 0;
        document.querySelectorAll('#notesList .fw-bold').forEach(el => {
            sum += parseInt(el.innerText.replace(/\D/g, ''));
        });
        document.getElementById('totalExpense').innerText = sum.toLocaleString();
    }

</script>

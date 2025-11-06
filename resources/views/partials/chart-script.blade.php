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
    document.getElementById('noteForm').addEventListener('submit', function(e) {
        e.preventDefault();

        showLoader();


        const noteText = document.getElementById('noteText').value.trim();
        const month = document.getElementById('noteMonth').value; // <--- ambil bulan dari input

        if (!month || !noteText) {
            alert('Lengkapi catatan dan bulan terlebih dahulu!');
            return;
        }
        
        try {

            fetch('{{ route('expenses.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    note: noteText,
                    month: month  // <--- kirim bulan ke server
                })
            })
            .then(res => res.json())
            .then(data => {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.dataset.id = data.id;
                li.innerHTML = `
                    <div class="text-section">
                        <span class="note-text">${data.note}</span>
                        <span class="fw-bold text-danger ms-2">Rp 0</span>
                        <span class="note-date d-block">${data.month}</span>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary edit-btn me-2">Edit</button>
                        <button class="btn btn-sm btn-outline-danger delete-btn">Hapus</button>
                        <button class="btn btn-sm btn-outline-primary detail-btn">Detail</button>
                    </div>
                `;
                document.getElementById('notesList').prepend(li);

                // updateTotal(parseInt(data.amount));
                document.getElementById('noteText').value = '';
                loadChartData();
                refreshTotal();
            })
            .catch(err => console.error(err));

        } catch (error) {
                console.error("Gagal save:", error);
        } finally {
            hideLoader();
        }
    });

    // Edit dan Hapus
    notesList.addEventListener('click', function(e) {
        const li = e.target.closest('li');
        const id = li?.dataset.id;

        if (e.target.classList.contains('delete-btn')) {
            if (confirm('Yakin ingin menghapus catatan ini?')) {
                fetch(`/expenses/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ _method: 'DELETE' }) // spoofing
                }).then(() => {
                    const amount = parseInt(
                        li.querySelector('.text-section span.fw-bold').innerText.replace(/\D/g, '')
                    );
                    li.remove();
                    updateTotal(-amount);
                    loadChartData();
                });
            }
        }

        if (e.target.classList.contains('edit-btn')) {

            showLoader();

            const noteTextEl = li.querySelector('.note-text');
            const oldNote = noteTextEl.innerText;
            const oldAmount = li.querySelector('.fw-bold').innerText.replace(/\D/g, '');

            const newNote = prompt('Ubah catatan:', oldNote);
            if (!newNote) return;
            const newAmount = newNote.match(/\d+/)?.[0] ?? oldAmount;

            try {

                fetch(`/expenses/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ _method: 'PUT', note: newNote, amount: newAmount })
                })
                .then(res => res.json())
                .then(data => {
                    noteTextEl.innerText = data.note;
                    li.querySelector('.fw-bold').innerText = `Rp ${data.amount.toLocaleString()}`;
                    refreshTotal();
                    loadChartData();
                });

            } catch (error) {
                console.error("Gagal update:", error);
            } finally {
                hideLoader();
            }
        }
    });

    // Update total otomatis
    function updateTotal(change) {
        const totalEl = document.getElementById('totalExpense');
        let total = parseInt(totalEl.innerText.replace(/\D/g, ''));
        total += change;
        totalEl.innerText = total.toLocaleString();

    }

    function refreshTotal() {
        let sum = 0;
        document.querySelectorAll('#notesList .fw-bold').forEach(el => {
            sum += parseInt(el.innerText.replace(/\D/g, ''));
        });
        document.getElementById('totalExpense').innerText = sum.toLocaleString();
        
        const totalElCard = document.getElementById('totalExpenseCard');
        totalElCard.innerText = sum.toLocaleString();

        // Ambil nilai total pemasukan (dari card)
        const totalPemasukan = document.getElementById('totalPemasukanCard');
        let totalPem = parseInt(totalPemasukan.innerText.replace(/\D/g, ''));

        // Hitung saldo = pemasukan - pengeluaran
        const saldo = totalPem - sum;

        // Update kartu saldo
        const totalSaldo = document.getElementById('totalSaldoCard');
        totalSaldo.innerText = saldo.toLocaleString();
    }


    document.getElementById('monthSelect').addEventListener('change', function() {
        const selectedMonth = this.value;
        const url = new URL(window.location.href);
        
        if (selectedMonth) {
            url.searchParams.set('month', selectedMonth);
        } else {
            url.searchParams.delete('month');
        }

        window.location.href = url.toString(); // Reload halaman dengan parameter baru

    });

    // document.getElementById('noteText').addEventListener('blur', function (e) {
    //     let value = e.target.value;

    //     // Ganti semua deretan angka menjadi format dengan pemisah ribuan
    //     e.target.value = value.replace(/\d+/g, match => {
    //         return new Intl.NumberFormat('id-ID').format(Number(match));
    //     });
    // });

    document.addEventListener('click', async function(e) {
        if (e.target.classList.contains('detail-btn')) {

            showLoader();

            try {

                const li = e.target.closest('li');
                const noteId = li.dataset.id;
                const noteText = li.querySelector('.note-text').innerText;

                document.getElementById('parentNoteId').value = noteId;
                document.getElementById('detailTitle').innerText = "Rincian: " + noteText;

                // Load data detail via AJAX (controller menyusul)
                const res = await fetch(`/notes/${noteId}/details`);
                const data = await res.json();

                updateDetailTable(data);

                new bootstrap.Modal(document.getElementById('detailModal')).show();

            } catch (error) {
                console.error("Gagal open:", error);
            } finally {
                hideLoader();
            }
        }
    });

    function updateDetailTable(details) {
        const tbody = document.querySelector('#detailTable tbody');
        tbody.innerHTML = "";

        details.forEach(det => {
            tbody.insertAdjacentHTML('beforeend', `
                <tr data-id="${det.id}" data-price="${det.price}">
                    <td>
                        <input type="checkbox" class="form-check-input detail-check" ${det.is_checked ? 'checked' : ''}>
                    </td>
                    <td>${det.name}</td>
                    <td>${det.qty}</td>
                    <td>${parseInt(det.price).toLocaleString()}</td>
                    <td>
                        <button class="btn btn-sm btn-danger delete-detail-btn">Hapus</button>
                    </td>
                </tr>
            `);
        });
    }

    document.getElementById('detailForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        showLoader();

        const payload = {
            note_id: document.getElementById('parentNoteId').value,
            name: document.getElementById('detailName').value,
            qty: document.getElementById('detailQty').value,
            price: document.getElementById('detailPrice').value,
        };

        try {

            const res = await fetch('/details', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();
            updateDetailTable(data.details); // response harus mengembalikan detail list terbaru
            
            refreshTotal();
            loadChartData();
            updateTotal(parseInt(payload.price));
            updateParentAmount(payload.note_id, data.total);
            refreshCardSummary();
            this.reset();

        } catch (error) {
                console.error("Gagal save:", error);
        } finally {
            hideLoader();
        }
    });

    document.addEventListener('click', async function(e) {
        if (e.target.classList.contains('delete-detail-btn')) {

            showLoader();

            const tr = e.target.closest('tr');
            const id = tr.dataset.id;
            const price = tr.dataset.price;

            if(!confirm("Hapus item ini?")) return;

            try {
                const res = await fetch(`/details/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await res.json();
                updateDetailTable(data.details);
                refreshTotal();
                loadChartData();
                updateTotal(-parseInt(price));
                updateParentAmount(document.getElementById('parentNoteId').value, data.total);
                refreshCardSummary();

            } catch (error) {
                console.error("Gagal delete:", error);
            } finally {
                hideLoader();
            }
        }
    });

    document.addEventListener('change', async function(e) {
        if (e.target.classList.contains('detail-check')) {

            showLoader();

            const tr = e.target.closest('tr');
            const detailId = tr.dataset.id;
            const noteId = document.getElementById('parentNoteId').value;
            const isChecked = e.target.checked ? 1 : 0;

            try {
                const res = await fetch(`/details/${detailId}/check`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ is_checked: isChecked })
                });

                const data = await res.json();

                const li = document.querySelector(`li[data-id="${noteId}"]`);
                li.querySelector('.realization-amount').textContent = `Rp ${parseInt(data.total).toLocaleString()}`;

                refreshTotal();
                loadChartData();
                refreshCardSummary();

            } catch (error) {
                console.error("Gagal update:", error);
            } finally {
                hideLoader();
            }
        }
    });


    function updateParentAmount(noteId, newAmount) {
        const li = document.querySelector(`li[data-id="${noteId}"]`);
        if (!li) return;
        li.querySelector('.fw-bold').textContent = `Rp ${parseInt(newAmount).toLocaleString()}`;
    }

    document.getElementById('searchNotes').addEventListener('input', function() {
        const search = this.value.toLowerCase();
        const items = document.querySelectorAll('#notesList li');

        items.forEach(li => {
            const text = li.querySelector('.note-text').innerText.toLowerCase();
            if (text.includes(search)) {
                li.style.display = '';
            } else {
                li.style.display = 'none';
            }
        });
    });

    async function refreshCardSummary() {
        const month = document.getElementById('monthSelect').value; // ambil bulan dipilih
        
        // Ambil total alokasi
        const alokasiRes = await fetch(`/summary/alokasi?month=${month}`);
        const alokasiTotal = await alokasiRes.json();

        // Ambil total realisasi
        const realisasiRes = await fetch(`/summary/realisasi?month=${month}`);
        const realisasiTotal = await realisasiRes.json();

        const incomeRes = await fetch(`/summary/income?month=${month}`);
        const incomeTotal = await incomeRes.json();

        let saldo = incomeTotal - realisasiTotal;

        // Update card di halaman
        document.querySelector('#totalExpenseCard').textContent = `Rp ${parseInt(alokasiTotal).toLocaleString()}`;
        document.querySelector('#totalRealizationCard').textContent = `Rp ${parseInt(realisasiTotal).toLocaleString()}`;
        document.querySelector('#totalSaldoCard').textContent = `Rp ${parseInt(saldo).toLocaleString()}`;

        // Jika ada grafik, panggil reload grafik juga
        if (typeof loadChartData === 'function') {
            loadChartData();
        }
    }
</script>

<script>
    // --- OFFLINE MANAGER ---
    const OfflineManager = {
        QUEUE_KEY: 'offline_expense_queue',
        
        getQueue: function() {
            return JSON.parse(localStorage.getItem(this.QUEUE_KEY) || '[]');
        },
        
        addToQueue: function(action, url, method, data) {
            const queue = this.getQueue();
            queue.push({
                id: Date.now(), // Temp ID
                action: action,
                url: url,
                method: method,
                data: data,
                timestamp: new Date().toISOString()
            });
            localStorage.setItem(this.QUEUE_KEY, JSON.stringify(queue));
            this.renderOfflineItems(); // Render immediately
        },
        
        renderOfflineItems: function() {
            const queue = this.getQueue();
            const recentList = document.getElementById('recentNotesList');
            const historyList = document.getElementById('notesList');
            const additionalIncomeList = document.getElementById('additionalIncomeList');
            
            // Remove existing offline items (identified by class)
            document.querySelectorAll('.offline-item-entry').forEach(el => el.remove());
            // Restore hidden items (for deletes/edits that were optimistic)
            document.querySelectorAll('.d-none-offline').forEach(el => el.classList.remove('d-none-offline', 'd-none'));

            if (queue.length === 0) return;

            // Categories map
            const cats = window.CATEGORY_DATA || [];

            queue.forEach(item => {
                // EXPENSE: STORE
                if(item.action === 'store') {
                     const cat = cats.find(c => c.id == item.data.category_id) || {name: 'Umum', icon: '📝', color: '#eee'};
                     const amount = new Intl.NumberFormat('id-ID').format(item.data.amount);
                     
                     // Template for Recent List
                     if(recentList) {
                         const li = document.createElement('li');
                         li.className = 'd-flex justify-content-between align-items-center mb-3 border-bottom pb-2 offline-item-entry';
                         li.innerHTML = `
                             <div class="d-flex align-items-center opacity-75">
                                 <div class="me-3 shadow-sm" style="width: 40px; height: 40px; background: #fff3cd; color: #856404; border-radius: 12px; display: flex; align-items: center; justify-content: center; border: 2px dashed #ffc107;">
                                     <span style="font-size: 1.2rem;"><i class="bi bi-hourglass-split"></i></span>
                                 </div>
                                 <div class="offline-item rounded p-1 px-2">
                                     <h6 class="m-0 fw-bold text-dark" style="font-size: 0.9rem;">${item.data.note} <small class="text-warning">(Offline)</small></h6>
                                     <small class="text-muted" style="font-size: 0.75rem;">
                                         Pending Sync &bull; ${cat.name}
                                     </small>
                                 </div>
                             </div>
                             <span class="fw-bold text-warning" style="font-size: 0.9rem;">- Rp ${amount}</span>
                         `;
                         recentList.prepend(li);
                     }
                     
                     // Template for History List
                     if(historyList) {
                         const li = document.createElement('li');
                         li.className = 'list-group-item d-flex justify-content-between align-items-start mb-2 offline-item-entry offline-item';
                         li.innerHTML = `
                            <div class="text-section flex-grow-1">
                                <div class="d-flex align-items-center mb-1">
                                     <span class="badge bg-warning text-dark border me-2 rounded-pill fw-normal">
                                         <i class="bi bi-hourglass-split"></i> Pending
                                     </span>
                                    <span class="note-date text-muted small" style="font-size:0.75rem;">
                                         Menunggu koneksi...
                                    </span>
                                </div>
                                <span class="note-text fw-semibold text-dark">${item.data.note}</span> 
                            </div>
                            <div class="text-end ms-2">
                                <span class="fw-bold text-danger d-block mb-1">Rp ${amount}</span>
                                <small class="text-muted fst-italic">Offline</small>
                            </div>
                         `;
                         historyList.prepend(li);
                     }
                }
                
                // EXPENSE: DELETE
                if (item.action === 'delete_expense') {
                    // Hide the original item
                    // Extract ID from URL: /expenses/123
                    const id = item.url.split('/').pop();
                    const el = document.querySelector(`li[data-id="${id}"]`);
                    if (el) {
                        el.classList.add('d-none', 'd-none-offline');
                    }
                }

                // EXPENSE: UPDATE
                if (item.action === 'update_expense') {
                     const id = item.url.split('/')[2]; // /expenses/123
                     const el = document.querySelector(`li[data-id="${id}"]`);
                     if(el) {
                         const amount = new Intl.NumberFormat('id-ID').format(item.data.amount);
                         el.querySelector('.note-text').innerHTML = `${item.data.note} <span class="badge bg-warning text-dark ms-1" style="font-size: 0.6em">EDITED OFF</span>`;
                         el.querySelector('.fw-bold.text-danger').innerText = `Rp ${amount}`;
                     }
                }

                // INCOME: UPDATE MAIN
                if (item.action === 'update_main_income') {
                     const amount = new Intl.NumberFormat('id-ID').format(item.data.income);
                     const card = document.getElementById('totalPemasukanCard');
                     if(card) {
                         card.innerHTML = `Rp ${amount} <small class="text-warning" style="font-size:0.5em">(Syncing)</small>`;
                     }
                }

                // INCOME: ADDITIONAL STORE
                if (item.action === 'store_additional_income' && additionalIncomeList) {
                     const amount = new Intl.NumberFormat('id-ID').format(item.data.amount);
                     const li = document.createElement('li');
                     li.className = 'list-group-item d-flex justify-content-between align-items-center p-2 offline-item-entry';
                     li.innerHTML = `
                        <div>
                            <div class="fw-semibold text-dark small">${item.data.title} <i class="bi bi-hourglass-split text-warning"></i></div>
                            <div class="text-muted" style="font-size: 0.7rem;">Offline</div>
                        </div>
                        <div class="text-end">
                            <span class="d-block fw-bold text-success small">+ ${amount}</span>
                            <span class="text-muted small" style="font-size: 0.7rem;">Pending</span>
                        </div>
                     `;
                     additionalIncomeList.appendChild(li);
                }

                // INCOME: ADDITIONAL DELETE
                if (item.action === 'delete_additional_income') {
                    const id = item.url.split('/').pop();
                    // We can't easily find this without an ID on the LI. 
                    // Assuming user doesn't delete just-added offline items for now? 
                    // Actually, we should add IDs to the list items in dashboard.blade.php but for now we might skip visual hiding strictly or rely on reload.
                    // Let's leave visual hiding for DB items that have IDs.
                    // For now, no visual hide for additional income delete to avoid complexity without IDs.
                }
            });
        },
        
        processQueue: async function() {
            const queue = this.getQueue();
            if (queue.length === 0) return;

            // Show Toast or Indicator
            const toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
            
            toast.fire({
                icon: 'info',
                title: 'Menyinkronkan data offline (' + queue.length + ' item)...'
            });

            // Process sequentially
            const newQueue = [];
            for (const item of queue) {
                try {
                    await fetch(item.url, {
                        method: item.method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(item.data)
                    });
                } catch (error) {
                    console.error('Failed to sync item', item, error);
                    // If error is network related, keep in queue? 
                    // For now, if fetch fails (e.g. still offline), we keep it.
                    // fetch only rejects on network error.
                    newQueue.push(item); 
                }
            }
            
            localStorage.setItem(this.QUEUE_KEY, JSON.stringify(newQueue));
            
            if(newQueue.length === 0) {
                 toast.fire({
                    icon: 'success',
                    title: 'Sinkronisasi Selesai!'
                });
                setTimeout(() => window.location.reload(), 1500);
            } else {
                 toast.fire({
                    icon: 'warning',
                    title: 'Beberapa data gagal disinkronkan.'
                });
            }
            this.updateIndicator();
        },
        
        updateIndicator: function() {
            // Optional: Add visual indicator if items are pending
            const queue = this.getQueue();
            // Could add a badge somewhere
        }
    };

    window.addEventListener('online', () => {
        console.log('Back online! Processing queue...');
        OfflineManager.processQueue();
    });
    
    // Check on load
    if(navigator.onLine) {
        OfflineManager.processQueue();
    } else {
        OfflineManager.renderOfflineItems();
    }
    
    // Check on load
    if(navigator.onLine) {
        OfflineManager.processQueue();
    } else {
        OfflineManager.renderOfflineItems();
    }
    
    // --- QUERY FORMATTING HELPERS ---
    window.formatCurrency = function(input) {
        // Strip non-digits
        let value = input.value.replace(/\D/g, '');
        if (value === '') {
            input.value = '';
            return;
        }
        // Format
        input.value = new Intl.NumberFormat('id-ID').format(value);
    };

    window.parseCurrency = function(valueStr) {
        if(!valueStr) return 0;
        return parseInt(valueStr.replace(/\./g, '')) || 0;
    };

    document.addEventListener("DOMContentLoaded", function () {
        // Search Filter
        const searchInput = document.getElementById('searchNotes');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const term = e.target.value.toLowerCase();
                const items = document.querySelectorAll('#notesList li'); // Select specific list items
                
                items.forEach(item => {
                    const note = item.getAttribute('data-note').toLowerCase();
                    const category = item.querySelector('.badge') ? item.querySelector('.badge').innerText.toLowerCase() : '';
                    
                    if (note.includes(term) || category.includes(term)) {
                        item.classList.remove('d-none');
                        item.classList.add('d-flex'); // Restore flex
                    } else {
                        item.classList.add('d-none');
                        item.classList.remove('d-flex');
                    }
                });
            });
        }

        // ... existing code ...
        // ... existing code ...

        
        // --- CHART & STATS LOGIC ---
        
        const monthFilterInput = document.getElementById('statsMonthFilter');
        let currentMonth = monthFilterInput ? monthFilterInput.value : new Date().toISOString().slice(0, 7);

        // Chart Instances
        let ctxElement = document.getElementById('expenseChart');
        let ctxCatElement = document.getElementById('categoryChart');
        
        window.expenseChart = null; 
        window.rincianKategoriChart = null;
        window.categoryChart = null;

        // Initial Load
        if(ctxElement) loadChartData(ctxElement.getContext('2d'), currentMonth);
        
        // New Chart Load
        let ctxRincian = document.getElementById('rincianKategoriChart');
        if(ctxRincian) loadRincianKategoriChart(ctxRincian.getContext('2d'), currentMonth);

        if(ctxCatElement) loadCategoryChart(ctxCatElement.getContext('2d'), currentMonth);

        // Listener: Month Filter Change
        if (monthFilterInput) {
            monthFilterInput.addEventListener('change', function(e) {
                currentMonth = e.target.value;
                if(ctxElement) loadChartData(ctxElement.getContext('2d'), currentMonth);
                // Check if new chart exists
                let ctxRincian = document.getElementById('rincianKategoriChart');
                if(ctxRincian) loadRincianKategoriChart(ctxRincian.getContext('2d'), currentMonth);
                
                if(ctxCatElement) loadCategoryChart(ctxCatElement.getContext('2d'), currentMonth);
            });
        }

        // 1. Bar Chart (Category Breakdown)
        function loadChartData(context, month) {
            if(!context) return;
            console.log('Loading chart data for:', month);
            
            // Use URL param for filtering
            fetch(`{{ route('chart.data') }}?month=${month}`)
                .then(res => {
                    if (!res.ok) throw new Error('Network response was not ok');
                    return res.json();
                })
                .then(data => {
                    console.log('Chart data received:', data);
                    
                    // Use category names with icons as labels
                    const labels = data.map(d => `${d.icon} ${d.category}`);
                    const values = data.map(d => d.total);
                    const colors = data.map(d => d.color);

                    if (window.expenseChart) {
                        window.expenseChart.destroy();
                    }
                
                    window.expenseChart = new Chart(context, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Total (Rp)',
                                data: values,
                                borderWidth: 0,
                                borderRadius: 4,
                                backgroundColor: colors,
                                barThickness: 20
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { borderDash: [2, 2] },
                                    ticks: { font: { size: 9 }, callback: val => (val/1000) + 'k' }
                                },
                                x: { 
                                    grid: { display: false }, 
                                    ticks: { 
                                        font: { size: 10 },
                                        autoSkip: false,
                                        maxRotation: 45,
                                        minRotation: 45
                                    } 
                                }
                            }
                        }
                    });
                    
                    console.log('Chart initialized:', window.expenseChart);
                })
                .catch(error => console.error('Error loading chart:', error));
        }


        // 1.5 New Logic for Rincian Kategori Chart (Percentage)
        function loadRincianKategoriChart(context, month) {
            if(!context) return;
            fetch(`{{ route('chart.category.data') }}?month=${month}`)
                .then(res => res.json())
                .then(data => {
                    const labels = data.map(d => d.name);
                    const values = data.map(d => d.total);
                    const colors = data.map(d => d.color);

                    if (window.rincianKategoriChart) {
                        window.rincianKategoriChart.destroy();
                    }
                
                    window.rincianKategoriChart = new Chart(context, {
                        type: 'doughnut',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: values,
                                backgroundColor: colors,
                                borderWidth: 2,
                                borderColor: '#ffffff',
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        boxWidth: 8,
                                        padding: 15,
                                        font: { size: 11 }
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.label || '';
                                            if (label) {
                                                label += ': ';
                                            }
                                            let value = context.raw || 0;
                                            let total = context.chart._metasets[context.datasetIndex].total;
                                            let percentage = Math.round((value / total) * 100) + '%';
                                            return label + 'Rp ' + new Intl.NumberFormat('id-ID').format(value) + ' (' + percentage + ')';
                                        }
                                    }
                                }
                            },
                            layout: {
                                padding: 20
                            }
                        }
                    });
                });
        }
        // 2. Category Doughnut & List Breakdown
        function loadCategoryChart(context, month) {
            if(!context) return;
            fetch(`{{ route('chart.category.data') }}?month=${month}`)
                .then(res => res.json())
                .then(data => {
                    renderCategoryList(data); // Update the list below chart

                    const labels = data.map(d => d.name);
                    const values = data.map(d => d.total);
                    const backgroundColors = [
                        '#0d6efd', '#6610f2', '#6f42c1', '#d63384', '#dc3545', 
                        '#fd7e14', '#ffc107', '#198754', '#20c997', '#0dcaf0'
                    ];

                    if (window.categoryChart) window.categoryChart.destroy();

                    window.categoryChart = new Chart(context, {
                        type: 'doughnut',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: values,
                                backgroundColor: backgroundColors,
                                borderWidth: 2,
                                borderColor: '#ffffff',
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'right', labels: { boxWidth: 10, font: { size: 10 } } }
                            },
                            layout: { padding: 5 }
                        }
                    });
                });
        }

        // Helper: Render Category List
        function renderCategoryList(data) {
            const listContainer = document.getElementById('categoryList');
            if (!listContainer) return;
            
            listContainer.innerHTML = ''; // Clear current

            if (data.length === 0) {
                listContainer.innerHTML = '<li class="list-group-item text-center text-muted py-3 small">Belum ada pengeluaran bulan ini.</li>';
                return;
            }

            data.forEach(item => {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center px-3 py-2 border-0 border-bottom';
                li.innerHTML = `
                    <div class="d-flex align-items-center">
                        <span class="me-3 fs-5">${item.icon}</span>
                        <div>
                            <div class="fw-semibold text-dark" style="font-size: 0.9rem;">${item.name}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">${item.percentage}%</div>
                        </div>
                    </div>
                    <span class="fw-bold text-dark" style="font-size: 0.9rem;">Rp ${new Intl.NumberFormat('id-ID').format(item.total)}</span>
                `;
                listContainer.appendChild(li);
            });
        }

        // --- BUDGET REMINDER (Simple Local Notification) ---
        checkBudgetReminder();

        function checkBudgetReminder() {
            // Check if Notification is supported
            if (!("Notification" in window)) return;

            // Logic: If date is 1-5, remind user
            const date = new Date().getDate();
            const lastReminded = localStorage.getItem('last_budget_reminder');
            const todayStr = new Date().toISOString().split('T')[0];

            if (date <= 5 && lastReminded !== todayStr) {
                // Request permission
                Notification.requestPermission().then(permission => {
                    if (permission === "granted") {
                        new Notification("Waktunya Budgeting! 📝", {
                            body: "Jangan lupa catat pemasukan dan rencana pengeluaran bulan ini di Qanaah.",
                            icon: "https://cdn-icons-png.flaticon.com/512/2344/2344132.png" // App icon
                        });
                        localStorage.setItem('last_budget_reminder', todayStr);
                    }
                });
            }
        }

        // --- ADD EXPENSE LOGIC ---
        const noteForm = document.getElementById('noteForm');
        
        // Dynamic Rows Logic
        if(document.getElementById('addDetailRowBtn')) {
            document.getElementById('addDetailRowBtn').addEventListener('click', function() {
                const list = document.getElementById('newExpenseDetailsList');
                const li = document.createElement('li');
                li.className = 'list-group-item p-2 border rounded mb-2 bg-white';
                li.innerHTML = `
                    <div class="d-flex gap-2 mb-1">
                        <input type="text" class="form-control form-control-sm detail-name" placeholder="Nama Item" required>
                        <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="this.closest('li').remove(); calculateTotal();"><i class="bi bi-x-lg"></i></button>
                    </div>
                    <div class="d-flex gap-2">
                        <input type="number" class="form-control form-control-sm detail-qty" placeholder="Qty" style="width: 70px;" value="1" min="1" required oninput="calculateTotal()">
                        <div class="input-group input-group-sm flex-grow-1">
                            <span class="input-group-text border-0 bg-light">Rp</span>
                            <input type="text" inputmode="numeric" class="form-control detail-price text-end" placeholder="Harga" required oninput="formatCurrency(this); calculateTotal()">
                        </div>
                    </div>
                `;
                list.appendChild(li);
            });
        }

        window.calculateTotal = function() {
            let total = 0;
            const rows = document.querySelectorAll('#newExpenseDetailsList li');
            
            if (rows.length > 0) {
                 rows.forEach(row => {
                    const qty = parseFloat(row.querySelector('.detail-qty').value) || 0;
                    const price = parseFloat(row.querySelector('.detail-price').value) || 0;
                    total += (qty * price);
                });
                
                const amountInput = document.getElementById('amountInput');
                amountInput.value = total;
                amountInput.readOnly = true;
                amountInput.classList.add('bg-light');
            } else {
                const amountInput = document.getElementById('amountInput');
                amountInput.readOnly = false;
                amountInput.classList.remove('bg-light');
            }
        };

        if(noteForm) {
            noteForm.addEventListener('submit', function(e) {
                e.preventDefault();
                showLoader();

                // Get values
                const noteTextEl = document.getElementById('noteText');
                const amountEl = document.getElementById('amountInput'); 
                const dateEl = document.getElementById('dateInput');
                const categoryEl = document.getElementById('noteCategory');
                const monthEl = document.getElementById('noteMonth');

                // Basic validation
                if (!noteTextEl.value || !categoryEl.value || !amountEl.value) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Mohon Lengkapi Data',
                        text: 'Pastikan catatan, jumlah, dan kategori sudah terisi!',
                        confirmButtonColor: '#0d6efd'
                    });
                    hideLoader();
                    return;
                }
                
                const noteText = noteTextEl.value;
                const rawAmount = amountEl.value.replace(/\D/g, ''); 
                
                // Collect Details
                let details = [];
                document.querySelectorAll('#newExpenseDetailsList li').forEach(row => {
                    details.push({
                        name: row.querySelector('.detail-name').value,
                        qty: row.querySelector('.detail-qty').value,
                        price: parseCurrency(row.querySelector('.detail-price').value)
                    });
                });

                // Check Offline
                if (!navigator.onLine) {
                     OfflineManager.addToQueue(
                        'store', 
                        '{{ route('expenses.store') }}', 
                        'POST', 
                        {
                            note: noteText,
                            amount: rawAmount,
                            date: dateEl.value,
                            month: monthEl.value,
                            category_id: categoryEl.value,
                            is_recurring: document.getElementById('isRecurring')?.checked ? 1 : 0,
                            details: details
                        }
                    );
                    
                    Swal.fire({
                        icon: 'info',
                        title: 'Offline Mode',
                        text: 'Kamu sedang offline. Data disimpan sementara dan akan diupload otomatis saat online.',
                        confirmButtonColor: '#0d6efd'
                    });
                    
                    document.getElementById('noteForm').reset();
                    document.getElementById('newExpenseDetailsList').innerHTML = ''; // Clear details
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addExpenseModal'));
                    if(modal) modal.hide();
                    
                    hideLoader();
                    return;
                }

                fetch('{{ route('expenses.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        note: noteText,
                        amount: rawAmount,
                        date: dateEl.value,
                        month: monthEl.value,
                        category_id: categoryEl.value,
                        is_recurring: document.getElementById('isRecurring')?.checked ? 1 : 0,
                        details: details
                    })
                })
                .then(async res => {
                    if (!res.ok) {
                        const errData = await res.json();
                        throw new Error(errData.message || 'Terjadi kesalahan saat menyimpan data.');
                    }
                    return res.json();
                })
                .then(data => {
                    window.location.reload();
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({
                        icon: 'warning',
                        title: 'Gagal Menyimpan',
                        text: err.message,
                        confirmButtonColor: '#0d6efd'
                    });
                })
                .finally(() => hideLoader());
            });
        }



        // --- EDIT / DELETE LOGIC ---
        // Delegate events from document or main wrapper to handle dynamic items
        document.addEventListener('click', function(e) {
            const target = e.target;
            
            // DELETE
            if(target.closest('.delete-btn')) {
                const li = target.closest('li');
                const id = li.dataset.id;
                if(confirm('Hapus transaksi ini?')) {
                    // Check Offline
                    if (!navigator.onLine) {
                        OfflineManager.addToQueue(
                            'delete_expense', 
                            `/expenses/${id}`, 
                            'POST', 
                            { _method: 'DELETE' }
                        );
                        Swal.fire({ icon: 'info', title: 'Offline', text: 'Penghapusan akan diproses saat online.', timer: 2000 });
                        return;
                    }

                    showLoader();
                    fetch(`/expenses/${id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ _method: 'DELETE' })
                    }).then(() => {
                        window.location.reload();
                    }).finally(() => hideLoader());
                }
            }
            
            // EDIT
            // EDIT
            if(target.closest('.edit-btn')) {
                openEditExpense(target.closest('li'));
            }
        });

        // Global Edit Function
        window.openEditExpense = function(li) {
            const id = li.dataset.id;
            const note = li.dataset.note;
            const catId = li.dataset.categoryId;
            const amount = li.dataset.amount;
            const date = li.dataset.date;
            
            document.getElementById('editExpenseId').value = id;
            document.getElementById('editNoteText').value = note; 
            document.getElementById('editAmountInput').value = new Intl.NumberFormat('id-ID').format(amount);
            document.getElementById('editDateInput').value = date.substring(0, 10);
            document.getElementById('editNoteCategory').value = catId;
            
            // Fetch Details Logic
            const list = document.getElementById('editExpenseDetailsList');
            list.innerHTML = '<li class="text-center text-muted py-2 small">Memuat rincian...</li>';
            
            fetch(`/expense/${id}/details`)
                .then(res => res.json())
                .then(details => {
                    list.innerHTML = ''; // Clear loading
                    if(details && details.length > 0) {
                        details.forEach(d => {
                            const li = document.createElement('li');
                            li.className = 'list-group-item p-2 border rounded mb-2 bg-white';
                            li.innerHTML = `
                                <div class="d-flex gap-2 mb-1">
                                    <input type="text" class="form-control form-control-sm detail-name" placeholder="Nama Item" value="${d.name}" required>
                                    <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="this.closest('li').remove(); calculateTotalEdit();"><i class="bi bi-x-lg"></i></button>
                                </div>
                                <div class="d-flex gap-2">
                                    <input type="number" class="form-control form-control-sm detail-qty" placeholder="Qty" style="width: 70px;" value="${d.qty}" min="1" required oninput="calculateTotalEdit()">
                                    <div class="input-group input-group-sm flex-grow-1">
                                        <span class="input-group-text border-0 bg-light">Rp</span>
                                        <input type="text" inputmode="numeric" class="form-control detail-price text-end" placeholder="Harga" value="${new Intl.NumberFormat('id-ID').format(d.price)}" required oninput="formatCurrency(this); calculateTotalEdit()">
                                    </div>
                                </div>
                            `;
                            list.appendChild(li);
                        });
                        calculateTotalEdit(); // Ensure readonly and sum check
                    } else {
                        // No details, manual amount allowed
                        document.getElementById('editAmountInput').readOnly = false;
                        document.getElementById('editAmountInput').classList.remove('bg-light');
                    }
                });

            
            const editModal = new bootstrap.Modal(document.getElementById('editExpenseModal'));
            editModal.show();
        };
        
        // Dynamic Rows Logic (Edit)
        if(document.getElementById('addDetailRowBtnEdit')) {
            document.getElementById('addDetailRowBtnEdit').addEventListener('click', function() {
                const list = document.getElementById('editExpenseDetailsList');
                const li = document.createElement('li');
                li.className = 'list-group-item p-2 border rounded mb-2 bg-white';
                li.innerHTML = `
                    <div class="d-flex gap-2 mb-1">
                        <input type="text" class="form-control form-control-sm detail-name" placeholder="Nama Item" required>
                        <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="this.closest('li').remove(); calculateTotalEdit();"><i class="bi bi-x-lg"></i></button>
                    </div>
                    <div class="d-flex gap-2">
                        <input type="number" class="form-control form-control-sm detail-qty" placeholder="Qty" style="width: 70px;" value="1" min="1" required oninput="calculateTotalEdit()">
                        <div class="input-group input-group-sm flex-grow-1">
                            <span class="input-group-text border-0 bg-light">Rp</span>
                            <input type="text" inputmode="numeric" class="form-control detail-price text-end" placeholder="Harga" required oninput="formatCurrency(this); calculateTotalEdit()">
                        </div>
                    </div>
                `;
                list.appendChild(li);
            });
        }
        
        window.calculateTotalEdit = function() {
            let total = 0;
            const rows = document.querySelectorAll('#editExpenseDetailsList li');
            
            if (rows.length > 0) {
                rows.forEach(row => {
                    const qty = parseFloat(row.querySelector('.detail-qty').value) || 0;
                    const price = parseCurrency(row.querySelector('.detail-price').value);
                    total += (qty * price);
                });
                
                const amountInput = document.getElementById('editAmountInput');
                amountInput.value = new Intl.NumberFormat('id-ID').format(total);
                amountInput.readOnly = true;
                amountInput.classList.add('bg-light');
            } else {
                const amountInput = document.getElementById('editAmountInput');
                amountInput.readOnly = false;
                amountInput.classList.remove('bg-light');
            }
        };

        // Handle Edit Submit
        const editForm = document.getElementById('editExpenseForm');
        if(editForm) {
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                showLoader();
                const id = document.getElementById('editExpenseId').value;
                const note = document.getElementById('editNoteText').value;
                const amount = document.getElementById('editAmountInput').value;
                const date = document.getElementById('editDateInput').value;
                const catId = document.getElementById('editNoteCategory').value;
                
                // Collect Details
                let details = [];
                document.querySelectorAll('#editExpenseDetailsList li').forEach(row => {
                    details.push({
                        name: row.querySelector('.detail-name').value,
                        qty: row.querySelector('.detail-qty').value,
                        price: parseCurrency(row.querySelector('.detail-price').value)
                    });
                });

                // Check Offline
                if (!navigator.onLine) {
                     OfflineManager.addToQueue(
                        'update', 
                        `/expenses/${id}`, 
                        'POST', 
                        { 
                            _method: 'PUT', 
                            note: note, 
                            amount: parseCurrency(amount), 
                            date: date, 
                            category_id: catId, 
                            is_recurring: document.getElementById('editIsRecurring')?.checked ? 1 : 0,
                            details: details 
                        }
                    );
                    
                    Swal.fire({
                        icon: 'info',
                        title: 'Offline Mode',
                        text: 'Perubahan disimpan sementara dan akan diupdate otomatis saat online.',
                        confirmButtonColor: '#0d6efd'
                    });
                    
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editExpenseModal'));
                    if(modal) modal.hide();
                    
                    hideLoader();
                    return;
                }

                fetch(`/expenses/${id}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ _method: 'PUT', note: note, amount: parseCurrency(amount), date: date, category_id: catId, is_recurring: document.getElementById('editIsRecurring')?.checked ? 1 : 0, details: details })
                })
                .then(res => res.json())
                .then(data => {
                    location.reload(); 
                })
                .finally(() => hideLoader());
            });
        }

        // --- GLOBAL SUMMARY REFRESH ---
        async function refreshCardSummary() {
            // Re-fetch dashboard partials or just amounts?
            // Easier to just fetch amounts.
            // But we need endpoints. `refreshCardSummary` in old script used `/summary/alokasi` etc.
            // Let's reuse that logic if endpoints exist. Assuming they do from old code.
            
            const monthSelect = document.getElementById('monthSelect'); // Global selector
            const month = monthSelect ? monthSelect.value : '{{ now()->format("Y-m") }}';

            try {
                const [alokasiRes, realisasiRes, incomeRes] = await Promise.all([
                    fetch(`/summary/alokasi?month=${month}`),
                    fetch(`/summary/realisasi?month=${month}`),
                    fetch(`/summary/income?month=${month}`)
                ]);
                
                const alokasi = await alokasiRes.json();
                const realisasi = await realisasiRes.json();
                const income = await incomeRes.json();
                const saldo = income - realisasi;
    
                // Update elements
                const els = {
                    expense: document.getElementById('totalRealizationCard'),
                    income: document.getElementById('totalPemasukanCard'),
                    saldo: document.getElementById('saldoAmount'),
                };
                
                if(els.expense) els.expense.textContent = `Rp ${parseInt(realisasi).toLocaleString('id-ID')}`;
                if(els.income) els.income.textContent = `Rp ${parseInt(income).toLocaleString('id-ID')}`;
                if(els.saldo) els.saldo.textContent = `Rp ${parseInt(saldo).toLocaleString('id-ID')}`;
                
            } catch(e) { console.error("Summary update failed", e); }
        }

        // --- EXPOSE GLOBAL FUNCTIONS ---
        window.reloadCharts = function() {
            const month = document.getElementById('statsMonthFilter')?.value || new Date().toISOString().slice(0, 7);
            const ctx = document.getElementById('expenseChart')?.getContext('2d');
            if(ctx) loadChartData(ctx, month);
            
            const ctxCat = document.getElementById('categoryChart')?.getContext('2d');
            if(ctxCat) loadCategoryChart(ctxCat, month);
        };
        
        window.refreshCardSummary = refreshCardSummary;

    });
</script>

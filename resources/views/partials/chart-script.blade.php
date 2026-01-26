<script>
    document.addEventListener("DOMContentLoaded", function () {
        
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
            fetch(`{{ route('chart.category.data') }}?month=${month}`)
                .then(res => {
                    if (!res.ok) throw new Error('Network response was not ok');
                    return res.json();
                })
                .then(data => {
                    console.log('Chart data received:', data);
                    
                    const labels = data.map(d => d.name); // Check if icon needed? d.icon + ' ' + d.name
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
                                backgroundColor: colors, // Use category colors
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
                        <div class="input-group input-group-sm">
                            <span class="input-group-text border-0 bg-light">Rp</span>
                            <input type="number" class="form-control detail-price" placeholder="Harga" required oninput="calculateTotal()">
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
                const categoryEl = document.getElementById('noteCategory');
                const monthEl = document.getElementById('noteMonth');

                // Basic validation
                if (!noteTextEl.value || !categoryEl.value || !amountEl.value) {
                    alert('Mohon lengkapi catatan, jumlah, dan kategori!');
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
                        price: row.querySelector('.detail-price').value
                    });
                });

                fetch('{{ route('expenses.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        note: noteText,
                        amount: rawAmount,
                        month: monthEl.value,
                        category_id: categoryEl.value,
                        details: details
                    })
                })
                .then(res => res.json())
                .then(data => {
                    window.location.reload();
                })
                .catch(err => {
                    console.error(err);
                    alert("Gagal menyimpan data.");
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
            
            document.getElementById('editExpenseId').value = id;
            document.getElementById('editNoteText').value = note; 
            document.getElementById('editAmountInput').value = amount;
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
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text border-0 bg-light">Rp</span>
                                        <input type="number" class="form-control detail-price" placeholder="Harga" value="${parseInt(d.price)}" required oninput="calculateTotalEdit()">
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
                        <div class="input-group input-group-sm">
                            <span class="input-group-text border-0 bg-light">Rp</span>
                            <input type="number" class="form-control detail-price" placeholder="Harga" required oninput="calculateTotalEdit()">
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
                    const price = parseFloat(row.querySelector('.detail-price').value) || 0;
                    total += (qty * price);
                });
                
                const amountInput = document.getElementById('editAmountInput');
                amountInput.value = total;
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
                const catId = document.getElementById('editNoteCategory').value;
                
                // Collect Details
                let details = [];
                document.querySelectorAll('#editExpenseDetailsList li').forEach(row => {
                    details.push({
                        name: row.querySelector('.detail-name').value,
                        qty: row.querySelector('.detail-qty').value,
                        price: row.querySelector('.detail-price').value
                    });
                });

                fetch(`/expenses/${id}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ _method: 'PUT', note: note, amount: amount, category_id: catId, details: details })
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

        // --- AI CHAT LOGIC ---
        // Attached to new #aiChatSend and #aiChatInput in dashboard layout
        const chatInput = document.getElementById('aiChatInput');
        const chatSend = document.getElementById('aiChatSend');
        const chatContent = document.getElementById('chatContent');
        
        if(chatSend && chatInput) {
            chatSend.addEventListener('click', sendMessage);
            chatInput.addEventListener('keypress', (e) => { if(e.key === 'Enter') sendMessage(); });
            
            async function sendMessage() {
                const text = chatInput.value.trim();
                if(!text) return;
                
                addMessage(text, 'user');
                chatInput.value = '';
                addMessage('...', 'bot-loading'); // loading state
                
                // Call AI Endpoint (Assuming /api/ai/chat exists or mock it)
                // Using the specific context endpoint from old script: `/api/ai/finance/context/{id}` is for context/summary
                // If this is a general chat, we might need a different endpoint. 
                // For now, let's just trigger summary if they ask "summary", or use existing flow.
                // The old script only had a "Summary Button".
                // I will hook into that Summary Button logic for now.
                
                // Placeholder response
                setTimeout(() => {
                    document.querySelector('.bot-loading')?.remove();
                    addMessage("Maaf, fitur chat bebas sedang dikembangkan. Coba klik 'AI Insight' di Home!", 'bot');
                }, 1000);
            }
        }
        
        // AI Summary Button (Home Tab)
        const summaryBtn = document.getElementById('summaryButton');
        if(summaryBtn) {
            summaryBtn.addEventListener('click', async () => {
                // Open Chat Modal
                const chatModal = new bootstrap.Modal(document.getElementById('chatModal'));
                chatModal.show();
                
                addMessage("Analisa keuanganku dong!", "user");
                addMessage("Sebentar, sedang menganalisa data...", "bot");
                
                try {
                    const userId = "{{ auth()->id() }}";
                    const res = await fetch(`/api/ai/finance/context/${userId}`);
                    const data = await res.json();
                    
                    if (data.error) {
                         addMessage("⚠️ " + data.error, "bot");
                    } else {
                         addMessage(data.context, "bot");
                    }
                } catch (err) {
                    addMessage("❌ Gagal mengambil data.", "bot");
                }
            });
        }
        
        function addMessage(text, sender) {
            if (!chatContent) return;
            const align = sender === 'user' ? 'text-end' : 'text-start';
            const bg = sender === 'user' ? 'bg-primary text-white' : 'bg-white border text-dark';
            
            const html = `
                <div class="mb-3 ${align} ${sender === 'bot-loading' ? 'bot-loading' : ''}">
                    <div class="d-inline-block p-3 rounded-4 ${bg}" style="max-width: 85%;">
                        ${text}
                    </div>
                </div>
            `;
            chatContent.insertAdjacentHTML('beforeend', html);
            chatContent.scrollTop = chatContent.scrollHeight;
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

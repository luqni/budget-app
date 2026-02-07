<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0d6efd">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="https://cdn-icons-png.flaticon.com/512/2344/2344132.png">
    
    <title>{{ $title ?? 'Dashboard' }}</title>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <style>
        body {
            background: #f8fafc;
            /* Prevent pull-to-refresh on mobile */
            overscroll-behavior-y: none;
        }
        .summary-card {
            border-radius: 1rem;
            padding: 1rem;
            color: white;
        }
        .summary-card h5 {
            font-size: 1rem;
        }
        .summary-card p {
            font-size: 1.3rem;
            font-weight: bold;
        }
        .income { background: #10b981; }
        .expense { background: #ef4444; }
        .balance { background: #3b82f6; }
        
        /* Hide scrollbar for cleaner mobile look */
        ::-webkit-scrollbar {
            width: 0px;
            background: transparent;
        }

        /* Override style tombol Lewati/Skip Intro.js */
        .introjs-skipbutton {
            font-size: 12px !important;       /* Perkecil ukuran huruf */
            font-weight: normal !important;   /* Pastikan tidak bold */
            color: #6c757d !important;        /* Warna abu-abu (muted) agar tidak mencolok */
            text-decoration: none !important; /* Hilangkan garis bawah */
            line-height: 1.5 !important;      /* Rapikan jarak baris */
            top: 10px !important;             /* Sesuaikan posisi vertikal (opsional) */
            right: 10px !important;           /* Sesuaikan posisi horizontal (opsional) */
        }

        /* Efek saat mouse diarahkan (Hover) */
        .introjs-skipbutton:hover {
            color: #000 !important;           /* Warna jadi hitam saat di-hover */
            text-decoration: underline !important;
        }
    </style>
    <style>
        /* Custom Qanaah Loader */
        .qanaah-loader {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        
        .qanaah-logo {
            width: 80px;
            height: 80px;
            margin-bottom: 15px;
            animation: pulse-custom 2s infinite ease-in-out;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
        }

        .qanaah-text {
            font-family: 'Figtree', sans-serif;
            color: #0d6efd;
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
        }

        .qanaah-tagline {
            font-family: 'Figtree', sans-serif;
            color: #64748b;
            font-size: 0.9rem;
            font-style: italic;
            animation: user-fade 3s infinite ease-in-out;
        }

        @keyframes pulse-custom {
            0% { transform: scale(0.95); opacity: 0.9; }
            50% { transform: scale(1.05); opacity: 1; }
            100% { transform: scale(0.95); opacity: 0.9; }
        }

        @keyframes user-fade {
            0%, 100% { opacity: 0.6; }
            50% { opacity: 1; }
        }
    </style>
    
    <!-- Intro.js for Dashboard Tour -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intro.js@7.2.0/minified/introjs.min.css">
    <script src="https://cdn.jsdelivr.net/npm/intro.js@7.2.0/intro.min.js"></script>
</head>
<body>
    <div id="ajaxLoader" 
        style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
        background:rgba(255,255,255,0.85); backdrop-filter:blur(4px);
        z-index:9999; align-items:center; justify-content:center;">
        
        <div class="qanaah-loader">
            <img src="https://cdn-icons-png.flaticon.com/512/2344/2344132.png" alt="Qanaah Logo" class="qanaah-logo">
            <div class="qanaah-text">Qanaah</div>
            <div class="qanaah-tagline">"Cukup itu Kaya..."</div>
            
            <div class="mt-3">
                <div class="spinner-border text-primary spinner-border-sm" role="status" style="width: 1rem; height: 1rem; opacity: 0.5;"></div>
            </div>
        </div>
    </div>
    
    <!-- Main Container -->
    <div class="container-fluid p-0" style="padding-bottom: 80px !important;"> <!-- padding bottom for nav -->
        {{-- Navbar Removed for Mobile First look, content should handle its own header if needed --}}
        {{-- But keeping it simple for now, we might want a sticky top header for title --}}
        
        @if(!Request::is('login') && !Request::is('register'))
            <nav class="navbar navbar-light bg-white border-bottom px-3 py-2 sticky-top shadow-sm d-md-none">
                 <span class="navbar-brand mb-0 h1 fw-bold">{{ $title ?? 'Qanaah' }}</span>
                 <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-light text-warning shadow-sm rounded-circle position-relative d-flex align-items-center justify-content-center p-0" 
                        style="width: 40px; height: 40px;"
                        data-bs-toggle="modal" data-bs-target="#quoteModal" id="quoteBtn">
                        <i class="bi bi-bell-fill fs-4"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="quoteBadge" style="font-size: 0.6rem; display: none;">
                            1
                        </span>
                    </button>
                    <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'User' }}&background=random" class="rounded-circle" style="width: 32px; height: 32px;">
                 </div>
            </nav>
        @endif

        <div class="container py-3">
             @yield('content')
        </div>
    </div>

    <!-- PWA Installation Prompt (Simple Toast) -->
    <div class="toast-container position-fixed bottom-0 start-50 translate-middle-x p-3" style="z-index: 1050; bottom: 80px !important;">
        <div id="pwaInstallToast" class="toast align-items-center text-white bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    Install aplikasi ini untuk akses lebih cepat!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>


    <!-- Daily Quote Modal (Enhanced) -->
    <div class="modal fade" id="quoteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-body text-center p-4">
                    @if(isset($todaysQuote))
                        <!-- Category Badge -->
                        <div class="mb-3">
                            @php
                                $categoryConfig = [
                                    'quran' => ['icon' => 'book-fill', 'color' => '#10b981', 'label' => 'Al-Quran'],
                                    'hadits' => ['icon' => 'chat-quote-fill', 'color' => '#3b82f6', 'label' => 'Hadits'],
                                    'ulama' => ['icon' => 'person-fill', 'color' => '#8b5cf6', 'label' => 'Ulama'],
                                    'tips' => ['icon' => 'lightbulb-fill', 'color' => '#f59e0b', 'label' => 'Tips']
                                ];
                                $config = $categoryConfig[$todaysQuote->category] ?? $categoryConfig['tips'];
                            @endphp
                            <span class="badge rounded-pill px-3 py-2 mb-2" style="background-color: {{ $config['color'] }}; font-size: 0.85rem;">
                                <i class="bi bi-{{ $config['icon'] }} me-1"></i> {{ $config['label'] }}
                            </span>
                        </div>
                        
                        <div class="mb-3">
                            <i class="bi bi-{{ $config['icon'] }} fs-1" style="color: {{ $config['color'] }};"></i>
                        </div>
                        
                        <h5 class="fw-bold mb-3">Renungan Hari Ini</h5>
                        
                        <blockquote class="blockquote mb-3">
                            <p class="fs-6 fst-italic text-dark" style="line-height: 1.8;">"{{ $todaysQuote->content }}"</p>
                        </blockquote>
                        
                        <figcaption class="blockquote-footer mt-2">
                            <cite title="Source">{{ $todaysQuote->source ?? 'Unknown' }}</cite>
                        </figcaption>
                        
                        <div class="d-flex gap-2 mt-4">
                            <a href="{{ route('quotes.index') }}" class="btn btn-outline-primary rounded-3 flex-fill">
                                <i class="bi bi-collection"></i> Lihat Semua
                            </a>
                            <button type="button" class="btn btn-primary rounded-3 flex-fill" data-bs-dismiss="modal">
                                <i class="bi bi-check-lg"></i> Mengerti
                            </button>
                        </div>
                    @else
                        <div class="mb-3">
                            <i class="bi bi-chat-quote-fill text-muted fs-1"></i>
                        </div>
                        <p class="text-muted">Belum ada kutipan hari ini.</p>
                        <button type="button" class="btn btn-primary w-100 rounded-3 mt-3" data-bs-dismiss="modal">Tutup</button>
                    @endif
                </div>
            </div>
        </div>
    </div>


    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false,
                confirmButtonColor: '#0d6efd'
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: "{{ $errors->first() }}",
                confirmButtonColor: '#0d6efd'
            });
        @endif
    </script>
</body>
<script>
let loaderStartTime = 0;
const MIN_LOADER_TIME = 500; // ms

function showLoader() {
    loaderStartTime = Date.now();
    const loader = document.getElementById('ajaxLoader');
    if(loader) loader.style.display = 'flex';
}

function hideLoader() {
    const loader = document.getElementById('ajaxLoader');
    if(loader) {
        const elapsed = Date.now() - loaderStartTime;
        const remaining = MIN_LOADER_TIME - elapsed;
        
        if (remaining > 0) {
            setTimeout(() => {
                loader.style.display = 'none';
            }, remaining);
        } else {
            loader.style.display = 'none';
        }
    }
}

// Global Loader Triggers for SPA-like feel
document.addEventListener('DOMContentLoaded', function() {
    // 1. Form Submits
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            // Don't show if it's an AJAX form handled manually elsewhere usually, 
            // but for standard submits we want it.
            // If the form has target="_blank" or specifically excluded, skip.
            if(!this.target && !this.hasAttribute('target')) {
                showLoader();
            }
        });
    });

    // 2. Link Clicks
    document.body.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        if (link && link.href) {
            // Check if internal link
            const isInternal = link.hostname === window.location.hostname;
            const isAnchor = link.getAttribute('href').startsWith('#');
            const isNewTab = link.target === '_blank';
            const isVoid = link.getAttribute('href') === 'javascript:void(0)' || link.getAttribute('href') === '#';
            const isControl = e.ctrlKey || e.metaKey || e.shiftKey;
            
            if (isInternal && !isAnchor && !isNewTab && !isVoid && !isControl) {
                // Exclude Bootstrap toggles (Modals, Tabs, etc)
                if(!link.hasAttribute('data-bs-toggle') && !link.hasAttribute('data-bs-dismiss')) {
                     showLoader();
                }
            }
        }
    });
});

// Handle Back/Forward Cache (Hide loader if user navigates back)
window.addEventListener('pageshow', function(event) {
    hideLoader();
});

// Service Worker Registration with Auto-Update
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js?v=5')
            .then((reg) => {
                console.log('SW registered!', reg);
                // Check if there's an update
                reg.onupdatefound = () => {
                    const installingWorker = reg.installing;
                    installingWorker.onstatechange = () => {
                        if (installingWorker.state === 'installed') {
                            if (navigator.serviceWorker.controller) {
                                console.log('New content is available; please refresh.');
                            } else {
                                console.log('Content is cached for offline use.');
                            }
                        }
                    };
                };
            })
            .catch((err) => console.log('SW failed', err));
    });
}

// Install Prompt Logic
let deferredPrompt;
window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    const toast = new bootstrap.Toast(document.getElementById('pwaInstallToast'));
    toast.show();
});

document.getElementById('pwaInstallToast').addEventListener('click', () => {
    if (deferredPrompt) {
        deferredPrompt.prompt();
        deferredPrompt.userChoice.then((choiceResult) => {
            if (choiceResult.outcome === 'accepted') {
                console.log('User accepted the install prompt');
            }
            deferredPrompt = null;
        });
    }
});

// Quote Badge and Auto-Show Logic
document.addEventListener("DOMContentLoaded", function () {
    const lastSeenDate = localStorage.getItem('lastQuoteDate');
    const todayDate = new Date().toISOString().split('T')[0];
    const quoteBadge = document.getElementById('quoteBadge');
    const quoteModal = document.getElementById('quoteModal');
    
    if (lastSeenDate !== todayDate && quoteModal) {
        // Show badge
        if(quoteBadge) quoteBadge.style.display = 'block';
        
        // Auto-show modal after 1 second (smooth entrance)
        setTimeout(() => {
            const modal = new bootstrap.Modal(quoteModal, {
                backdrop: 'static',
                keyboard: true
            });
            modal.show();
            
            // Mark as seen when modal is shown
            localStorage.setItem('lastQuoteDate', todayDate);
            if(quoteBadge) quoteBadge.style.display = 'none';
        }, 1000);
    }
    
    // Manual click on bell icon
    const btn = document.getElementById('quoteBtn');
    if(btn) {
        btn.addEventListener('click', function() {
            localStorage.setItem('lastQuoteDate', todayDate);
            if(quoteBadge) quoteBadge.style.display = 'none';
        });
    }
    
    // === BUDGET WARNING NOTIFICATIONS ===
    checkBudgetNotifications();
});

function checkBudgetNotifications() {
    // Check if Notification is supported
    if (!("Notification" in window)) {
        console.log('Browser does not support notifications');
        return;
    }
    
    const today = new Date();
    const dayOfMonth = today.getDate();
    const lastNotificationDate = localStorage.getItem('lastBudgetNotification');
    const todayStr = today.toISOString().split('T')[0];
    
    // Don't spam - only once per day
    if (lastNotificationDate === todayStr) {
        return;
    }
    
    // Request permission if not granted
    if (Notification.permission === "default") {
        Notification.requestPermission().then(permission => {
            if (permission === "granted") {
                scheduleBudgetNotifications(dayOfMonth, todayStr);
            }
        });
    } else if (Notification.permission === "granted") {
        scheduleBudgetNotifications(dayOfMonth, todayStr);
    }
}

function scheduleBudgetNotifications(dayOfMonth, todayStr) {
    // 1. Beginning of Month Reminder (Day 1-3)
    if (dayOfMonth >= 1 && dayOfMonth <= 3) {
        showNotification(
            "🎯 Waktunya Budgeting!",
            "Awal bulan adalah waktu terbaik untuk merencanakan keuangan. Jangan lupa set budget limit untuk setiap kategori!",
            todayStr
        );
    }
    
    // 2. Mid-Month Check (Day 15)
    else if (dayOfMonth === 15) {
        showNotification(
            "📊 Cek Keuangan Tengah Bulan",
            "Sudah setengah bulan berjalan. Yuk cek apakah pengeluaran masih sesuai budget!",
            todayStr
        );
    }
    
    // 3. End of Month Warning (Day 25-28)
    else if (dayOfMonth >= 25 && dayOfMonth <= 28) {
        showNotification(
            "⚠️ Akhir Bulan Mendekat",
            "Bulan ini hampir berakhir. Pastikan pengeluaran tidak melebihi budget ya!",
            todayStr
        );
    }
}

function showNotification(title, body, dateStr) {
    // Check if we have budget warnings from server
    const hasWarnings = document.querySelector('.alert-warning, .alert-danger');
    
    if (hasWarnings) {
        // Customize message if there are active warnings
        body = "⚠️ Ada kategori yang sudah mendekati limit! " + body;
    }
    
    new Notification(title, {
        body: body,
        icon: '/android-chrome-192x192.png',
        badge: '/android-chrome-192x192.png',
        tag: 'budget-reminder',
        requireInteraction: false,
        vibrate: [200, 100, 200]
    });
    
    localStorage.setItem('lastBudgetNotification', dateStr);
}
</script>
<script>
    window.addEventListener('appinstalled', (evt) => {
        fetch('/api/app-stat/download', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
    });
</script>
</html>

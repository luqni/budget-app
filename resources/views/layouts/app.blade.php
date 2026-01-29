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

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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
    </style>
</head>
<body>
    <div id="ajaxLoader" 
        style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
        background:rgba(255,255,255,0.6); backdrop-filter:blur(2px);
        z-index:9999; align-items:center; justify-content:center;">
        <div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;"></div>
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

    <!-- Daily Quote Modal (Moved to Layout) -->
    <div class="modal fade" id="quoteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-body text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-chat-quote-fill text-warning fs-1"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Renungan Hari Ini</h5>
                    @if(isset($todaysQuote))
                        <blockquote class="blockquote mb-3">
                            <p class="fs-6 fst-italic">"{{ $todaysQuote->content }}"</p>
                        </blockquote>
                        <figcaption class="blockquote-footer mt-2">
                            <cite title="Source">{{ $todaysQuote->source ?? 'Unknown' }}</cite>
                        </figcaption>
                    @else
                        <p class="text-muted">Belum ada kutipan hari ini.</p>
                    @endif
                    <button type="button" class="btn btn-primary w-100 rounded-3 mt-3" data-bs-dismiss="modal">Mengerti</button>
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
function showLoader() {
    document.getElementById('ajaxLoader').style.display = 'flex';
}

function hideLoader() {
    document.getElementById('ajaxLoader').style.display = 'none';
}

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

// Quote Badge Logic
document.addEventListener("DOMContentLoaded", function () {
    const lastSeenDate = localStorage.getItem('lastQuoteDate');
    const todayDate = new Date().toISOString().split('T')[0];
    const quoteBadge = document.getElementById('quoteBadge');
    
    if (lastSeenDate !== todayDate) {
        if(quoteBadge) quoteBadge.style.display = 'block';
        // Auto show if wanted:
        // const quoteModal = new bootstrap.Modal(document.getElementById('quoteModal'));
        // quoteModal.show();
        
        // Mark seen logic moved to modal interaction or explicit close? 
        // For now, let's keep badge until clicked.
        
        const btn = document.getElementById('quoteBtn');
        if(btn) {
            btn.addEventListener('click', function() {
                localStorage.setItem('lastQuoteDate', todayDate);
                if(quoteBadge) quoteBadge.style.display = 'none';
            });
        }
    }
});
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#0d6efd">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="https://cdn-icons-png.flaticon.com/512/2344/2344132.png">
    
    <title>{{ $title ?? 'Dashboard' }}</title>

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
                 <div class="d-flex align-items-center">
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
        navigator.serviceWorker.register('/sw.js')
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
</script>
</html>

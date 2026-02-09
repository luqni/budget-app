<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Dokumentasi Qanaah App</title>
    <!-- Fonts & CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            height: 100vh; /* Use fixed height to prevent scrolling on body if iframe scrolls */
            margin: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden; /* Hide body scrollbar */
        }
        .header {
            background: white;
            padding: 0 20px;
            height: 70px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            position: relative;
            z-index: 100;
            flex-shrink: 0;
        }
        .app-brand {
            font-weight: 700;
            font-size: 1.2rem;
            color: #0d6efd;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            white-space: nowrap;
        }
        .back-btn {
            text-decoration: none;
            color: #555;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 8px 15px;
            border-radius: 50px;
            transition: all 0.2s;
            background: #f0f2f5;
            width: fit-content;
        }
        .back-btn:hover {
            background: #e9ecef;
            color: #0d6efd;
        }
        .iframe-container {
            flex: 1;
            width: 100%;
            position: relative;
            overflow: hidden;
        }
        iframe {
            width: 100%;
            height: 100%;
            border: none;
            display: block;
        }
        
        /* Mobile adjustment */
        @media (max-width: 576px) {
            .header {
                padding: 0 15px;
            }
            .back-text {
                display: none;
            }
            .back-btn {
                padding: 8px;
                border-radius: 50%;
            }
        }
    </style>
</head>
<body>

    <div class="header">
        <!-- Left: Back Button -->
        <div>
            <a href="{{ Auth::check() ? route('dashboard') : url('/') }}" class="back-btn" title="Kembali">
                <i class="bi bi-arrow-left"></i> 
                <span class="back-text">{{ Auth::check() ? 'Dashboard' : 'Kembali ke Beranda' }}</span>
            </a>
        </div>

        <!-- Center: Brand -->
        <div class="app-brand">
            <i class="bi bi-book"></i> Dokumentasi
        </div>

        <!-- Right: Spacer for balance -->
        <div></div> 
    </div>

    <div class="iframe-container">
        <iframe src="https://docs.google.com/document/d/e/2PACX-1vTvncKgKGWA-z10VrvziBHbpXvA5NOZPXgxTvTal6WBfRQ-7sMzti9gscDrmxFmUL_whaj-9Gors3sI/pub?embedded=true"></iframe>
    </div>

</body>
</html>

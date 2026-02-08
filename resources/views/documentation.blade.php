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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .header {
            background: white;
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .app-brand {
            font-weight: 700;
            font-size: 1.2rem;
            color: #0d6efd;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .back-btn {
            text-decoration: none;
            color: #555;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 8px 15px;
            border-radius: 50px;
            transition: all 0.2s;
            background: #f0f2f5;
        }
        .back-btn:hover {
            background: #e9ecef;
            color: #0d6efd;
        }
        .iframe-container {
            flex: 1;
            width: 100%;
            height: calc(100vh - 70px); /* Adjust based on header height */
            overflow: hidden;
        }
        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>
</head>
<body>

    <div class="header">
        <a href="{{ Auth::check() ? route('dashboard') : url('/') }}" class="back-btn">
            <i class="bi bi-arrow-left"></i> 
            {{ Auth::check() ? 'Kembali ke Dashboard' : 'Kembali ke Beranda' }}
        </a>
        <div class="app-brand">
            <i class="bi bi-book"></i> Dokumentasi
        </div>
        <div style="width: 100px;"></div> <!-- Spacer -->
    </div>

    <div class="iframe-container">
        <iframe src="https://docs.google.com/document/d/e/2PACX-1vTvncKgKGWA-z10VrvziBHbpXvA5NOZPXgxTvTal6WBfRQ-7sMzti9gscDrmxFmUL_whaj-9Gors3sI/pub?embedded=true"></iframe>
    </div>

</body>
</html>

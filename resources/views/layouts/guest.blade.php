<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MyBudget') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        
        <!-- Bootstrap 5 -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --primary-color: #0d6efd;
                --bg-glass: rgba(255, 255, 255, 0.9);
            }
            body {
                font-family: 'Outfit', sans-serif;
                background-color: #f8f9fa;
                background-image: 
                    radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
                    radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%), 
                    radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%);
                background-size: cover;
                background-attachment: fixed;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }
            .auth-card {
                background: var(--bg-glass);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                border-radius: 24px;
                border: 1px solid rgba(255, 255, 255, 0.3);
                box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
                padding: 2.5rem;
                width: 100%;
                max-width: 420px;
                transition: transform 0.3s ease;
            }
            .auth-card:hover {
                transform: translateY(-5px);
            }
            .form-control {
                border-radius: 12px;
                padding: 12px;
                border: 1px solid #e0e0e0;
                background: rgba(255,255,255,0.8);
            }
            .form-control:focus {
                box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
                border-color: var(--primary-color);
            }
            .btn-primary {
                border-radius: 12px;
                padding: 12px;
                font-weight: 600;
                box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
                transition: all 0.3s ease;
            }
            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(13, 110, 253, 0.4);
            }
            .logo-container {
                width: 60px;
                height: 60px;
                background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
                border-radius: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 30px;
                margin-bottom: 20px;
                box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
            }
        </style>
    </head>
    <body>
        <div class="auth-card">
            <div class="d-flex justify-content-center">
                 <div class="logo-container">
                     <i class="bi bi-wallet2"></i>
                 </div>
            </div>
            
            {{ $slot }}
        </div>

        <div class="text-white-50 mt-4 small text-center px-4">
            &copy; {{ date('Y') }} {{ config('app.name') }}. Manage your finances with ease.
        </div>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
    </body>
</html>

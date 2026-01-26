<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="theme-color" content="#0d6efd">
    <title>Qanaah - Cukup itu Kaya</title>
    <!-- Icons -->
    <link rel="apple-touch-icon" href="https://cdn-icons-png.flaticon.com/512/2344/2344132.png">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    
    <!-- Fonts & CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #0a58ca;
            --bg-gradient: linear-gradient(135deg, #f0f2f5 0%, #ffffff 100%);
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: #333;
        }
        .hero-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px 20px;
            background: linear-gradient(180deg, rgba(13, 110, 253, 0.05) 0%, rgba(255,255,255,0) 100%);
        }
        .app-logo-container {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #0d6efd 0%, #0043a8 100%);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 20px rgba(13, 110, 253, 0.2);
            color: white;
            font-size: 3rem;
        }
        .app-title {
            font-weight: 800;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #333 0%, #0d6efd 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -1px;
        }
        .app-tagline {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 2rem;
            font-weight: 500;
        }
        .btn-primary-custom {
            background: #0d6efd;
            border: none;
            padding: 14px 40px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
            transition: all 0.3s ease;
            width: 100%;
            max-width: 300px;
        }
        .btn-primary-custom:hover {
            background: #0b5ed7;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(13, 110, 253, 0.4);
        }
        .btn-outline-custom {
            background: transparent;
            border: 2px solid #0d6efd;
            color: #0d6efd;
            padding: 12px 40px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            max-width: 300px;
            margin-top: 10px;
            transition: all 0.3s;
        }
        .btn-outline-custom:hover {
            background: rgba(13, 110, 253, 0.05);
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 15px;
            padding: 20px;
            max-width: 800px;
            margin: 0 auto 40px;
            width: 100%;
        }
        .feature-item {
            background: white;
            padding: 20px 15px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.03);
            border: 1px solid rgba(0,0,0,0.05);
        }
        .feature-icon {
            font-size: 1.5rem;
            color: #0d6efd;
            margin-bottom: 10px;
            background: rgba(13, 110, 253, 0.1);
            width: 50px;
            height: 50px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
        }
        .feature-text {
            font-size: 0.9rem;
            font-weight: 600;
            color: #444;
        }
        
        .footer {
            padding: 20px;
            text-align: center;
            font-size: 0.8rem;
            color: #999;
        }

        /* Desktop Optimization */
        @media (min-width: 768px) {
            .hero-section {
                padding: 60px 20px;
            }
            .app-title { font-size: 3.5rem; }
            .features-grid { padding: 40px 20px; gap: 30px; }
            .feature-item { padding: 30px 20px; }
            .feature-icon { width: 60px; height: 60px; font-size: 2rem; }
            .feature-text { font-size: 1rem; }
        }
    </style>
</head>
<body>

    <div class="hero-section">
        <div class="container">
            <div class="app-logo-container">
                <i class="bi bi-wallet2"></i>
            </div>
            
            <h1 class="app-title">Qanaah</h1>
            <p class="app-tagline">"Cukup itu Kaya"</p>
            <p class="text-muted mb-5 mx-auto" style="max-width: 500px;">
                Aplikasi pencatat keuangan simpel dengan filosofi Qanaah. 
                Fokus pada apa yang penting, kelola keuangan dengan tenang.
            </p>

            <div class="d-flex flex-column align-items-center">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-primary-custom text-white text-decoration-none">
                            <i class="bi bi-speedometer2 me-2"></i> Buka Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary-custom text-white text-decoration-none mb-2">
                            Mulai Sekarang
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-outline-custom text-decoration-none">
                                Daftar Akun Baru
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </div>

    <div class="container">
        <div class="features-grid">
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="bi bi-lightning-charge-fill"></i>
                </div>
                <div class="feature-text">Catat Cepat</div>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="bi bi-wifi-off"></i>
                </div>
                <div class="feature-text">Mode Offline</div>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="bi bi-robot"></i>
                </div>
                <div class="feature-text">Chatbox</div>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="bi bi-chat-quote-fill"></i>
                </div>
                <div class="feature-text">Renungan Harian</div>
            </div>
        </div>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} Qanaah App. Crafted for Peace of Mind.
    </div>

</body>
</html>

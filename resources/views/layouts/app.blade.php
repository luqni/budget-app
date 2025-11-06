<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background: #f8fafc;
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
    </style>
</head>
<body>
    <div class="container py-3">
        <nav class="navbar navbar-light bg-white border-bottom px-3 py-2 d-flex justify-content-between sticky-top shadow-sm mb-3">
            <a href="{{ route('dashboard') }}" class="navbar-brand fw-semibold">Dashboard</a>

            <div>
                @auth
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-outline-danger">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-sm btn-primary ms-1">Register</a>
                @endauth
            </div>
        </nav>

        <h3 class="text-center mb-4">{{ $title ?? 'Catatan Keuangan Keluargaku' }}</h3>
        @yield('content')
    </div>

    <!-- FOOTER -->
    <footer class="text-center py-2 bg-white border-top shadow-sm fixed-bottom small">
        dibuat dengan ❤️ oleh <strong>Muhammad Luqni Baehaqi</strong>
    </footer>
</body>
</html>

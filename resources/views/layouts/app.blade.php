<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
        <h3 class="text-center mb-4">{{ $title ?? 'Dashboard' }}</h3>
         @yield('content')
    </div>
</body>
</html>

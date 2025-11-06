<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Rumah Keuangan Keluarga</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: #FFF5EA;
            font-family: 'Poppins', sans-serif;
        }
        .card-cute {
            border-radius: 20px;
            border: 1px solid #F8DCCB;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            background: white;
        }
        .cute-input {
            border-radius: 12px !important;
            background: #FFF9F4;
            border-color: #E3C8B5;
        }
        .cute-input:focus {
            border-color: #FF9EC3;
            box-shadow: 0 0 0 .2rem rgba(255,158,195,0.3);
        }
        .cute-btn {
            background: #FF9EC3;
            border-radius: 12px;
            border: none;
        }
        .cute-btn:hover {
            background: #FF85B4;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">

    <div class="card card-cute p-4" style="max-width: 420px; width: 100%;">

        <div class="text-center mb-3">
            <img src="https://cdn-icons-png.flaticon.com/512/4772/4772417.png" alt="Family" class="img-fluid" style="width:120px;">
        </div>

        <h2 class="text-center fw-bold text-brown mb-2" style="color:#5A4632;">
            Buat Akun Keluarga 👨‍👩‍👧‍👦
        </h2>

        <p class="text-center small text-muted mb-4">
            Catat keuangan penuh cinta, biar keluarga makin bahagia 💗
        </p>

        @if ($errors->any())
            <div class="mb-4 text-center text-sm text-red-600 bg-red-100 border border-red-300 px-3 py-2 rounded-lg">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label" style="color:#6C584C;">Nama Kamu</label>
                <input type="text" id="name" name="name" class="form-control cute-input" required value="{{ old('name') }}">
                <x-input-error :messages="$errors->get('name')" class="text-danger small mt-1" />
            </div>

            <div class="mb-3">
                <label class="form-label" style="color:#6C584C;">Email</label>
                <input type="email" id="email" name="email" class="form-control cute-input" required value="{{ old('email') }}">
                <x-input-error :messages="$errors->get('email')" class="text-danger small mt-1" />
            </div>

            <div class="mb-3">
                <label class="form-label" style="color:#6C584C;">Password</label>
                <input type="password" id="password" name="password" class="form-control cute-input" required>
            </div>

            <div class="mb-4">
                <label class="form-label" style="color:#6C584C;">Konfirmasi Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control cute-input" required>
            </div>

            <button class="btn cute-btn w-100 py-2 fw-semibold text-white">
                Buat Akun 🎉
            </button>

            <p class="text-center small text-muted mt-3">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-decoration-none" style="color:#FF79A8;">Masuk yuk</a>
            </p>
        </form>
    </div>

</body>
</html>

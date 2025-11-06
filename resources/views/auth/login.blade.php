<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pencatatan Keuangan Keluarga</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#FFF5E9] min-h-screen flex items-center justify-center">

    <div class="bg-white w-full max-w-md shadow-xl rounded-2xl p-8 border border-[#f6dcb9] relative">

        <!-- Ilustrasi Keluarga Kecil di Atas -->
        <div class="flex justify-center mb-4">
            <div class="text-center">
                <img src="https://cdn-icons-png.flaticon.com/512/4772/4772417.png" alt="family" class="w-20 mx-auto drop-shadow-md">
                <h2 class="text-center text-2xl font-bold text-[#6B4226] mt-2">
                    Selamat Datang 😊
                </h2>
                <p class="text-sm text-[#8c6b4a] -mt-1">Ayo jaga keuangan keluarga dengan bahagia</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="mb-4 text-center text-sm text-red-600 bg-red-100 border border-red-300 px-3 py-2 rounded-lg">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <label class="block mb-3">
                <span class="text-[#6B4226] font-medium">Email</span>
                <input type="email" name="email" required autofocus 
                    class="mt-1 block w-full rounded-xl border-[#e4c9a8] bg-[#FFF9F3] focus:ring-[#d39e63] focus:border-[#d39e63] 
               text-base px-4 py-3">
            </label>

            <!-- Password -->
            <label class="block mb-3">
                <span class="text-[#6B4226] font-medium">Password</span>
                <input type="password" name="password" required
                    class="mt-1 block w-full rounded-xl border-[#e4c9a8] bg-[#FFF9F3] focus:ring-[#d39e63] focus:border-[#d39e63] 
               text-base px-4 py-3">
            </label>

            <!-- Remember -->
            <label class="inline-flex items-center mb-4">
                <input type="checkbox" name="remember" 
                    class="rounded border-[#d39e63] text-[#d39e63] focus:ring-[#d39e63]">
                <span class="ml-2 text-[#6B4226] text-sm">Ingat saya</span>
            </label>

            <!-- Button -->
            <button class="w-full py-2 px-4 rounded-lg font-semibold text-white bg-[#d39e63] hover:bg-[#b87f46] transition shadow-lg">
                Masuk
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-[#6B4226]">
            Belum punya akun? 
            <a href="{{ route('register') }}" class="text-[#b5703b] font-semibold hover:underline">Daftar</a>
        </div>

        <div class="mt-1 text-center text-sm">
            <a href="{{ route('password.request') }}" class="text-[#8c6b4a] hover:underline">Lupa password?</a>
        </div>

        <!-- Dekorasi kecil -->
        <img src="https://cdn-icons-png.flaticon.com/512/4772/4772483.png" class="w-14 absolute -bottom-4 -left-4 opacity-80">
        <img src="https://cdn-icons-png.flaticon.com/512/4772/4772544.png" class="w-12 absolute -top-4 -right-4 opacity-80">

    </div>

</body>
</html>

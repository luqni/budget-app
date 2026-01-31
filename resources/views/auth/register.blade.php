<x-guest-layout>
    <div class="text-center mb-4">
        <h4 class="fw-bold text-dark">Buat Akun Baru 🚀</h4>
        <p class="text-muted small">Mulai kelola keuanganmu dengan lebih baik.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show small py-2" role="alert">
            {{ $errors->first() }}
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label small fw-bold text-muted">NAMA LENGKAP</label>
            <input type="text" id="name" name="name" class="form-control" placeholder="Nama Anda" value="{{ old('name') }}" required autofocus>
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label small fw-bold text-muted">EMAIL</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="nama@email.com" value="{{ old('email') }}" required>
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label small fw-bold text-muted">PASSWORD</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="********" required>
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <label for="password_confirmation" class="form-label small fw-bold text-muted">ULANGI PASSWORD</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="********" required>
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-3">
            Daftar Sekarang
        </button>

        <!-- Divider -->
        <div class="d-flex align-items-center mb-3">
            <hr class="flex-grow-1">
            <span class="px-3 text-muted small">ATAU</span>
            <hr class="flex-grow-1">
        </div>

        <!-- Google Sign Up Button -->
        <a href="{{ route('auth.google') }}" class="btn btn-outline-dark w-100 mb-3 d-flex align-items-center justify-content-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 48 48">
                <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/>
                <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/>
                <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/>
                <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"/>
            </svg>
            Daftar dengan Google
        </a>

        <div class="text-center small text-muted">
            Sudah punya akun? 
            <a href="{{ route('login') }}" class="text-decoration-none fw-bold">Masuk di sini</a>
        </div>
    </form>
</x-guest-layout>

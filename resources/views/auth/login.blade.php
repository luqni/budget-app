<x-guest-layout>
    <div class="text-center mb-4">
        <h4 class="fw-bold text-dark">Selamat Datang Kembali! 👋</h4>
        <p class="text-muted small">Silakan masuk untuk mengelola keuanganmu.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show small py-2" role="alert">
            {{ $errors->first() }}
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label small fw-bold text-muted">EMAIL</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="nama@email.com" value="{{ old('email') }}" required autofocus>
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label small fw-bold text-muted">PASSWORD</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="********" required>
        </div>

        <!-- Remember Me -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                <label class="form-check-label small text-muted" for="remember_me">
                    Ingat Saya
                </label>
            </div>
            @if (Route::has('password.request'))
                <a class="text-decoration-none small fw-bold" href="{{ route('password.request') }}">
                    Lupa Password?
                </a>
            @endif
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-3">
            Masuk Sekarang
        </button>

        <div class="text-center small text-muted">
            Belum punya akun? 
            <a href="{{ route('register') }}" class="text-decoration-none fw-bold">Daftar Gratis</a>
        </div>
    </form>
</x-guest-layout>

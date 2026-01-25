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

        <div class="text-center small text-muted">
            Sudah punya akun? 
            <a href="{{ route('login') }}" class="text-decoration-none fw-bold">Masuk di sini</a>
        </div>
    </form>
</x-guest-layout>

@extends('layouts.auth')

@section('title', 'Masuk')

@section('content')
    <form action="{{ route('login') }}" method="POST">
        @csrf

        @if ($errors->any())
            <div class="alert alert-danger border-0 rgba-red-bg text-light py-2 px-3 rounded-3 mb-4" style="background-color: rgba(239, 68, 68, 0.2); font-size: 0.85rem;">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mb-3">
            <label for="email" class="form-label">Alamat Email</label>
            <div class="input-group">
                <span class="input-group-text" id="email-addon"><i class="fa-regular fa-envelope"></i></span>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="nama@perusahaan.com" value="{{ old('email') }}" required aria-describedby="email-addon">
            </div>
        </div>

        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="password" class="form-label mb-0">Password</label>
            </div>
            <div class="input-group">
                <span class="input-group-text" id="password-addon"><i class="fa-solid fa-lock"></i></span>
                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••" required aria-describedby="password-addon">
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember">
                    Ingat saya di perangkat ini
                </label>
            </div>
        </div>

        <button type="submit" class="btn btn-accent w-100 mb-2">
            <i class="fa-solid fa-right-to-bracket me-2"></i>Masuk Ke Dashboard
        </button>
    </form>

    <div class="auth-footer">
        Belum memiliki akun? <a href="{{ route('register') }}">Daftar Sekarang</a>
    </div>
@endsection

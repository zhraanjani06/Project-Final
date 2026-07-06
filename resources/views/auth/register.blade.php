@extends('layouts.auth')

@section('title', 'Daftar')

@section('content')
    <form action="{{ route('register') }}" method="POST">
        @csrf

        @if ($errors->any())
            <div class="alert alert-danger border-0 text-light py-2 px-3 rounded-3 mb-4" style="background-color: rgba(239, 68, 68, 0.2); font-size: 0.85rem;">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mb-3">
            <label for="name" class="form-label">Nama Lengkap</label>
            <div class="input-group">
                <span class="input-group-text" id="name-addon"><i class="fa-regular fa-user"></i></span>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="John Doe" value="{{ old('name') }}" required aria-describedby="name-addon">
            </div>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Alamat Email</label>
            <div class="input-group">
                <span class="input-group-text" id="email-addon"><i class="fa-regular fa-envelope"></i></span>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="nama@perusahaan.com" value="{{ old('email') }}" required aria-describedby="email-addon">
            </div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text" id="password-addon"><i class="fa-solid fa-lock"></i></span>
                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter" required aria-describedby="password-addon">
            </div>
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
            <div class="input-group">
                <span class="input-group-text" id="password-confirm-addon"><i class="fa-solid fa-shield-halved"></i></span>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ulangi password" required aria-describedby="password-confirm-addon">
            </div>
        </div>

        <button type="submit" class="btn btn-accent w-100 mb-2">
            <i class="fa-solid fa-user-plus me-2"></i>Daftar Akun Baru
        </button>
    </form>

    <div class="auth-footer">
        Sudah memiliki akun? <a href="{{ route('login') }}">Masuk</a>
    </div>
@endsection

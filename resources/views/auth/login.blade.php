@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="main-card">
                <div class="text-center mb-4">
                    <h3 class="fw-black m-0" style="letter-spacing: -1px;">Acesse sua Conta</h3>
                    <p class="text-muted fs-7">Entre para gerenciar suas entregas no RotaJá</p>
                </div>

                @if (session('status'))
                    <div class="alert alert-success rounded-3 fs-7 mb-3">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold small text-muted">E-MAIL</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autofocus autocomplete="username">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold small text-muted">SENHA</label>
                        <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check mb-4">
                        <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                        <label for="remember_me" class="form-check-label fs-7 text-muted fw-medium">Lembrar de mim</label>
                    </div>

                    <button type="submit" class="btn btn-custom btn-purple w-100 py-3 mb-3">
                        Entrar no Sistema
                    </button>

                    <div class="text-center">
                        <a href="{{ route('empresas.create') }}" class="text-decoration-none fs-7 fw-bold" style="color: var(--primary-purple);">
                            Ainda não tem conta? Cadastre-se
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
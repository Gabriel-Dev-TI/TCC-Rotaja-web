@extends('layouts.guest')

@section('content')

<div class="row justify-content-center">

    <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5">

        <div class="text-center mb-4">

            <h1 class="h2">
                Entrar
            </h1>

            <p class="lead text-muted">
                Entre com sua conta para continuar
            </p>

        </div>


        <div class="card shadow-sm">

            <div class="card-body p-4">

                <form method="POST" action="{{ route('login') }}">

                    @csrf

                    <div class="mb-3">

                        <label class="form-label">
                            Email
                        </label>

                        <input
                            class="form-control form-control-lg"
                            type="email"
                            name="email"
                            placeholder="Insira seu email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                        >

                    </div>


                    {{-- SENHA --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Senha
                        </label>

                        <input
                            class="form-control form-control-lg"
                            type="password"
                            name="password"
                            placeholder="Insira sua senha"
                            required
                        >

                    </div>

                    @if ($errors->any())

                        <div class="alert alert-danger">

                            @foreach ($errors->all() as $error)

                                <div class="text-danger m-1">
                                    {{ $error }}
                                </div>

                            @endforeach

                        </div>

                    @endif


                    <div class="mb-3">

                        <div class="form-check">

                            <input
                                id="remember"
                                type="checkbox"
                                class="form-check-input"
                                name="remember"
                            >

                            <label
                                class="form-check-label"
                                for="remember"
                            >
                                Lembrar-me
                            </label>

                        </div>

                    </div>

                    <div class="d-grid">

                        <button
                            type="submit"
                            class="btn btn-primary btn-lg"
                        >
                            Entrar
                        </button>

                    </div>

                </form>

            </div>

        </div>


        <div class="text-center mt-3 mb-4">

            <span class="text-muted">
                Ainda não tem uma conta?
            </span>

            <a href="{{ route('registro') }}">
                Cadastre-se
            </a>

        </div>

    </div>

</div>

@endsection
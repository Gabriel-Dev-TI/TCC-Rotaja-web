@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h1 class="h3 mb-1">
            Editar perfil
        </h1>

        <p class="text-muted mb-0 small">
            Atualize as informações da sua conta.
        </p>
    </div>

    <a
        href="{{ route('perfil.show') }}"
        class="btn btn-outline-secondary btn-sm"
    >
        <i data-feather="arrow-left" class="me-1"></i>
        Voltar
    </a>

</div>


{{-- =====================================================
    MENSAGEM DE SUCESSO
===================================================== --}}

@if(session('status') === 'profile-updated')

    <div class="alert alert-success">
        <i data-feather="check-circle" class="me-1"></i>
        Perfil atualizado com sucesso.
    </div>

@endif


{{-- =====================================================
    ERROS
===================================================== --}}

@if($errors->any())

    <div class="alert alert-danger">

        <strong>Não foi possível atualizar o perfil.</strong>

        <ul class="mb-0 mt-2">

            @foreach($errors->all() as $error)

                <li>{{ $error }}</li>

            @endforeach

        </ul>

    </div>

@endif


<div class="row g-3">

    {{-- =====================================================
        INFORMAÇÕES PESSOAIS
    ====================================================== --}}

    <div class="col-12">

        <div class="card border-0 shadow-sm">

            <div class="card-header bg-transparent py-3">

                <h5 class="card-title mb-1">
                    Informações pessoais
                </h5>

                <p class="text-muted small mb-0">
                    Altere seus dados pessoais.
                </p>

            </div>


            <div class="card-body">

                <form
                    method="POST"
                    action="{{ route('perfil.update') }}"
                >

                    @csrf

                    @method('PATCH')


                    <div class="row g-3">


                        {{-- NOME --}}

                        <div class="col-md-6">

                            <label
                                for="nome"
                                class="form-label"
                            >
                                Nome
                            </label>

                            <input
                                type="text"
                                id="nome"
                                name="nome"
                                class="form-control @error('nome') is-invalid @enderror"
                                value="{{ old('nome', $user->nome) }}"
                                required
                                autofocus
                            >

                            @error('nome')

                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>


                        {{-- TELEFONE --}}

                        <div class="col-md-6">

                            <label
                                for="telefone"
                                class="form-label"
                            >
                                Telefone
                            </label>

                            <input
                                type="text"
                                id="telefone"
                                name="telefone"
                                class="form-control @error('telefone') is-invalid @enderror"
                                value="{{ old('telefone', $user->telefone) }}"
                            >

                            @error('telefone')

                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>


                        {{-- EMAIL --}}

                        <div class="col-md-6">

                            <label
                                for="email"
                                class="form-label"
                            >
                                E-mail
                            </label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $user->email) }}"
                                required
                            >

                            @error('email')

                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>


                        {{-- CARGO --}}

                        <div class="col-md-6">

                            <label class="form-label">
                                Tipo de conta
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="{{ ucfirst($user->cargo) }}"
                                disabled
                            >

                            <div class="form-text">
                                O tipo de conta não pode ser alterado.
                            </div>

                        </div>


                        {{-- BOTÃO --}}

                        <div class="col-12">

                            <hr class="my-2">

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >

                                <i
                                    data-feather="save"
                                    class="me-1"
                                ></i>

                                Salvar alterações

                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>


    {{-- =====================================================
        ALTERAR SENHA
    ====================================================== --}}

    <div class="col-12">

        <div class="card border-0 shadow-sm">

            <div class="card-header bg-transparent py-3">

                <h5 class="card-title mb-1">
                    Alterar senha
                </h5>

                <p class="text-muted small mb-0">
                    Altere a senha utilizada para acessar sua conta.
                </p>

            </div>


            <div class="card-body">

                <form
                    method="POST"
                    action="{{ route('password.update') }}"
                >

                    @csrf

                    @method('PUT')


                    <div class="row g-3">


                        <div class="col-md-4">

                            <label
                                for="current_password"
                                class="form-label"
                            >
                                Senha atual
                            </label>

                            <input
                                type="password"
                                id="current_password"
                                name="current_password"
                                class="form-control"
                                required
                            >

                        </div>


                        <div class="col-md-4">

                            <label
                                for="password"
                                class="form-label"
                            >
                                Nova senha
                            </label>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control"
                                required
                            >

                        </div>


                        <div class="col-md-4">

                            <label
                                for="password_confirmation"
                                class="form-label"
                            >
                                Confirmar senha
                            </label>

                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="form-control"
                                required
                            >

                        </div>


                        <div class="col-12">

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >

                                <i
                                    data-feather="lock"
                                    class="me-1"
                                ></i>

                                Alterar senha

                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>


    {{-- =====================================================
        EXCLUIR CONTA
    ====================================================== --}}

    <div class="col-12">

        <div class="card border-0 shadow-sm">

            <div class="card-header bg-transparent py-3">

                <h5 class="card-title text-danger mb-1">
                    Excluir conta
                </h5>

                <p class="text-muted small mb-0">
                    A exclusão da conta não poderá ser desfeita.
                </p>

            </div>


            <div class="card-body">

                <form
                    method="POST"
                    action="{{ route('perfil.destroy') }}"
                >

                    @csrf

                    @method('DELETE')


                    <div class="row align-items-end g-3">

                        <div class="col-md-6">

                            <label
                                for="delete_password"
                                class="form-label"
                            >
                                Confirme sua senha
                            </label>

                            <input
                                type="password"
                                id="delete_password"
                                name="password"
                                class="form-control"
                                required
                            >

                        </div>


                        <div class="col-md-auto">

                            <button
                                type="submit"
                                class="btn btn-outline-danger"
                                onclick="return confirm('Tem certeza que deseja excluir sua conta?')"
                            >

                                <i
                                    data-feather="trash-2"
                                    class="me-1"
                                ></i>

                                Excluir conta

                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection


@push('scripts')

<script>

    if (typeof feather !== 'undefined') {
        feather.replace();
    }

</script>

@endpush
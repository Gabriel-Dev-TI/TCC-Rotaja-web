@extends('layouts.app')

@section('content')

@php
    $usuario = auth()->user();

    $cargo = $usuario->cargo;

    $nomeCargo = match ($cargo) {
        'admin' => 'Administrador',
        'empresa' => 'Empresa',
        'entregador' => 'Entregador',
        default => ucfirst($cargo),
    };

    $inicial = strtoupper(substr($usuario->nome ?? 'U', 0, 1));

    $empresa = $cargo === 'empresa'
        ? $usuario->empresa
        : null;

    $entregador = $cargo === 'entregador'
        ? $usuario->entregador
        : null;

    $endereco = $empresa?->endereco;

    $enderecoFormatado = $endereco
        ? trim(
            ($endereco->logradouro ?? '') .
            (isset($endereco->numero) ? ', ' . $endereco->numero : '') .
            (!empty($endereco->bairro) ? ' - ' . $endereco->bairro : '') .
            (!empty($endereco->cidade) ? ' - ' . $endereco->cidade : '') .
            (!empty($endereco->estado) ? '/' . $endereco->estado : '')
        )
        : null;

    $entregasRealizadas = $entregador?->entregas
        ? $entregador->entregas->where('status', 'concluido')->count()
        : 0;

    $entregasAndamento = $entregador?->entregas
        ? $entregador->entregas->where('status', 'em_transito')->count()
        : 0;

    $entregasEmpresa = $empresa?->entregas?->count() ?? 0;
@endphp


<div class="d-flex justify-content-between align-items-center mb-3">

    <div>
        <h1 class="h3 mb-1">Meu perfil</h1>

        <p class="text-muted mb-0 small">
            Visualize e gerencie seus dados no Rota Já.
        </p>
    </div>

    <a
        href="{{ route('perfil.edit') }}"
        class="btn btn-primary btn-sm"
    >
        <i data-feather="edit-2" class="me-1"></i>
        Editar perfil
    </a>

</div>


<div class="row g-3">


    <div class="col-md-4 col-xl-3">

        <div class="card border-0 shadow-sm">

            <div class="card-body text-center p-3">

                <div
                    class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2"
                    style="
                        width:80px;
                        height:80px;
                        background:linear-gradient(135deg,#6d4aff,#8b5cf6);
                        color:white;
                        font-size:30px;
                        font-weight:600;
                    "
                >
                    {{ $inicial }}
                </div>

                <h5 class="mb-1">
                    {{ $usuario->nome }}
                </h5>

                <div class="text-muted small mb-2">
                    {{ $nomeCargo }}
                </div>

                <span class="badge bg-primary-subtle text-primary px-3 py-2">
                    {{ ucfirst($cargo) }}
                </span>

            </div>

            <hr class="my-0">

            <div class="card-body p-3">

                <h6 class="card-title mb-3">
                    Informações
                </h6>

                <div class="mb-2">

                    <div class="small text-muted mb-1">
                        <i data-feather="mail" class="feather-sm me-1"></i>
                        E-mail
                    </div>

                    <div class="small text-break">
                        {{ $usuario->email }}
                    </div>

                </div>


                <div class="mb-2">

                    <div class="small text-muted mb-1">
                        <i data-feather="phone" class="feather-sm me-1"></i>
                        Telefone
                    </div>

                    <div class="small">
                        {{ $usuario->telefone ?: 'Não informado' }}
                    </div>

                </div>


                <div>

                    <div class="small text-muted mb-1">
                        <i data-feather="calendar" class="feather-sm me-1"></i>
                        Cadastro
                    </div>

                    <div class="small">
                        {{ $usuario->created_at?->format('d/m/Y') }}
                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="col-md-8 col-xl-9">

        @if($cargo === 'entregador')

            <div class="card border-0 shadow-sm mb-3">

                <div class="card-header bg-transparent py-2 px-3">

                    <h6 class="card-title mb-1">
                        Dados do entregador
                    </h6>

                    <p class="text-muted small mb-0">
                        Informações utilizadas para realizar suas entregas.
                    </p>

                </div>

                <div class="card-body py-3">

                    <div class="row g-3">

                        <div class="col-md-6">

                            <div class="small text-muted mb-1">
                                <i data-feather="user" class="feather-sm me-1"></i>
                                Nome
                            </div>

                            <strong class="small">
                                {{ $usuario->nome }}
                            </strong>

                        </div>


                        <div class="col-md-6">

                            <div class="small text-muted mb-1">
                                <i data-feather="credit-card" class="feather-sm me-1"></i>
                                CPF
                            </div>

                            <strong class="small">
                                {{ $entregador?->cpf ?? 'Não informado' }}
                            </strong>

                        </div>


                        <div class="col-md-6">

                            <div class="small text-muted mb-1">
                                <i data-feather="truck" class="feather-sm me-1"></i>
                                Tipo de veículo
                            </div>

                            <strong class="small">
                                {{ $entregador?->tipo_veiculo ?? 'Não informado' }}
                            </strong>

                        </div>

                    </div>

                </div>

            </div>

        @elseif($cargo === 'empresa')

            <div class="card border-0 shadow-sm mb-3">

                <div class="card-header bg-transparent py-2 px-3">

                    <h6 class="card-title mb-1">
                        Dados da empresa
                    </h6>

                    <p class="text-muted small mb-0">
                        Informações cadastradas no Rota Já.
                    </p>

                </div>

                <div class="card-body py-3">

                    <div class="row g-3">

                        <div class="col-md-6">

                            <div class="small text-muted mb-1">
                                <i data-feather="briefcase" class="feather-sm me-1"></i>
                                Nome
                            </div>

                            <strong class="small">
                                {{ $usuario->nome }}
                            </strong>

                        </div>


                        <div class="col-md-6">

                            <div class="small text-muted mb-1">
                                <i data-feather="credit-card" class="feather-sm me-1"></i>
                                CNPJ
                            </div>

                            <strong class="small">
                                {{ $empresa?->cnpj ?? 'Não informado' }}
                            </strong>

                        </div>


                        <div class="col-12">

                            <div class="small text-muted mb-1">
                                <i data-feather="map-pin" class="feather-sm me-1"></i>
                                Endereço
                            </div>

                            @if($enderecoFormatado)

                                <strong class="small">
                                    {{ $enderecoFormatado }}
                                </strong>

                                @if($endereco?->cep)

                                    <div class="text-muted small">
                                        CEP: {{ $endereco->cep }}
                                    </div>

                                @endif

                            @else

                                <span class="text-muted small">
                                    Endereço não informado.
                                </span>

                            @endif

                        </div>

                    </div>

                </div>

            </div>

        @elseif($cargo === 'admin')

            <div class="card border-0 shadow-sm mb-3">

                <div class="card-header bg-transparent py-2 px-3">

                    <h6 class="card-title mb-1">
                        Informações administrativas
                    </h6>

                    <p class="text-muted small mb-0">
                        Dados da sua conta de administrador.
                    </p>

                </div>

                <div class="card-body py-3">

                    <div class="row g-3">

                        <div class="col-md-6">

                            <div class="small text-muted mb-1">
                                <i data-feather="user" class="feather-sm me-1"></i>
                                Nome
                            </div>

                            <strong class="small">
                                {{ $usuario->nome }}
                            </strong>

                        </div>


                        <div class="col-md-6">

                            <div class="small text-muted mb-1">
                                <i data-feather="shield" class="feather-sm me-1"></i>
                                Função
                            </div>

                            <strong class="small">
                                Administrador
                            </strong>

                        </div>


                        <div class="col-12">

                            <div class="alert alert-primary py-2 mb-0 small">

                                <i data-feather="info" class="me-2"></i>

                                Esta conta possui acesso administrativo ao Rota Já.

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        @endif

        <div class="card border-0 shadow-sm">

            <div class="card-header bg-transparent py-2 px-3">

                <h6 class="card-title mb-1">
                    Conta
                </h6>

                <p class="text-muted small mb-0">
                    Informações gerais da sua conta.
                </p>

            </div>


            <div class="card-body py-3">

                <div class="row g-3">

                    <div class="col-md-6">

                        <div class="small text-muted mb-1">
                            E-mail
                        </div>

                        <div class="d-flex align-items-center gap-2">

                            <span class="small">
                                {{ $usuario->email }}
                            </span>

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="small text-muted mb-1">
                            Tipo de conta
                        </div>

                        <span class="badge bg-primary-subtle text-primary px-3 py-2">
                            {{ $nomeCargo }}
                        </span>

                    </div>


                </div>

            </div>

        </div>

    </div>

</div>

@endsection
